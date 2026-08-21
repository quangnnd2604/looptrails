<?php
/**
 * Authoritative Server-Side Pricing Engine
 *
 * Implements Spec §7, §8.1.
 * Canonical prices stored in integer minor units (USD cents or VND integers).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Pricing_Engine {

	const DEFAULT_DEPOSIT_PERCENT = 20;
	const DEFAULT_USD_VND_RATE = 25400;

	const RENTAL_BIKES = array(
		'wave_alpha' => array( 'label' => 'Honda Wave Alpha 110cc', 'rate_usd' => 10.0 ),
		'blade_fi'   => array( 'label' => 'Honda Blade FI 110cc', 'rate_usd' => 12.0 ),
		'xr150l'     => array( 'label' => 'Honda XR 150L', 'rate_usd' => 22.0 ),
		'cb500x'     => array( 'label' => 'Adventure Honda CB500X', 'rate_usd' => 48.0 ),
	);

	/**
	 * Calculate full itemized price quote.
	 *
	 * @param array $args Booking parameters.
	 * @return array Itemized calculation breakdown.
	 */
	public static function calculate_quote( $args ) {
		$tour_id      = isset( $args['tour_id'] ) ? absint( $args['tour_id'] ) : 0;
		$party_size   = isset( $args['party_size'] ) ? max( 1, absint( $args['party_size'] ) ) : 1;
		$vehicle_id   = isset( $args['vehicle_id'] ) ? absint( $args['vehicle_id'] ) : 0;
		$transfer_in  = isset( $args['transfer_in'] ) ? absint( $args['transfer_in'] ) : 0;
		$transfer_out = isset( $args['transfer_out'] ) ? absint( $args['transfer_out'] ) : 0;
		$rental_days  = isset( $args['rental_days'] ) ? absint( $args['rental_days'] ) : 0;
		$rental_bike  = isset( $args['rental_bike'] ) ? sanitize_key( $args['rental_bike'] ) : '';
		$voucher_code = isset( $args['voucher_code'] ) ? sanitize_text_field( $args['voucher_code'] ) : '';

		// --- Validate tour_id refers to a real, published tour ---
		if ( $tour_id ) {
			if ( 'tour' !== get_post_type( $tour_id ) || 'publish' !== get_post_status( $tour_id ) ) {
				return array( 'error' => 'invalid_tour_id' );
			}
		}

		// --- Validate vehicle_id belongs to this tour ---
		$tour_unit_price = 0.0;
		$vehicle_name    = '';
		if ( $vehicle_id ) {
			if ( 'vehicle_option' !== get_post_type( $vehicle_id ) ) {
				return array( 'error' => 'invalid_vehicle_id' );
			}
			$linked_tour = absint( get_post_meta( $vehicle_id, 'tbc_tour_id', true ) );
			if ( $tour_id && $linked_tour !== $tour_id ) {
				return array( 'error' => 'vehicle_does_not_belong_to_tour' );
			}
			$price_vnd = intval( get_post_meta( $vehicle_id, 'tbc_price_vnd', true ) );
			if ( $price_vnd <= 0 ) {
				return array( 'error' => 'vehicle_has_no_price' );
			}
			$tour_unit_price = Tbc_Currency::vnd_to_usd( $price_vnd );
			$vehicle_name    = get_the_title( $vehicle_id );
		} elseif ( $tour_id ) {
			// No specific vehicle chosen — use the cheapest published vehicle_option for this tour.
			$cheapest = self::get_cheapest_vehicle_for_tour( $tour_id );
			if ( ! $cheapest ) {
				return array( 'error' => 'no_pricing_available_for_tour' );
			}
			$tour_unit_price = Tbc_Currency::vnd_to_usd( $cheapest['price_vnd'] );
			$vehicle_name    = $cheapest['label'];
		} elseif ( $rental_days > 0 && isset( self::RENTAL_BIKES[ $rental_bike ] ) ) {
			// Pure motorbike rental (no tour required)
			$tour_unit_price = 0.0;
			$vehicle_name    = isset( self::RENTAL_BIKES[ $rental_bike ]['label'] ) ? self::RENTAL_BIKES[ $rental_bike ]['label'] : $rental_bike;
		} else {
			return array( 'error' => 'tour_id_required' );
		}

		$tour_subtotal = $tour_unit_price * $party_size;

		// --- Transfers: real price looked up server-side, tbc_price_vnd is the real field ---
		$transfer_subtotal = 0.0;
		foreach ( array( $transfer_in, $transfer_out ) as $transfer_post_id ) {
			if ( ! $transfer_post_id ) {
				continue;
			}
			if ( 'transfer_option' !== get_post_type( $transfer_post_id ) ) {
				return array( 'error' => 'invalid_transfer_id' );
			}
			$t_price_vnd = intval( get_post_meta( $transfer_post_id, 'tbc_price_vnd', true ) );
			if ( $t_price_vnd > 0 ) {
				$transfer_subtotal += Tbc_Currency::vnd_to_usd( $t_price_vnd ) * $party_size;
			}
		}

		// --- Motorbike rental add-on: rate comes from a fixed server-side catalog, never from the client ---
		$rental_subtotal = 0.0;
		if ( $rental_days > 0 && isset( self::RENTAL_BIKES[ $rental_bike ] ) ) {
			$rental_subtotal = $rental_days * self::RENTAL_BIKES[ $rental_bike ]['rate_usd'];
		}

		$subtotal_usd = $tour_subtotal + $transfer_subtotal + $rental_subtotal;

		// --- Voucher / Discount ---
		$discount_usd      = 0.0;
		$discount_applied  = false;
		$voucher_post_id   = 0;
		if ( ! empty( $voucher_code ) ) {
			$voucher_data = self::validate_voucher( $voucher_code, $tour_id, $subtotal_usd );
			if ( $voucher_data['valid'] ) {
				$discount_usd     = $voucher_data['discount_amount'];
				$discount_applied = true;
				$voucher_post_id  = isset( $voucher_data['voucher_id'] ) ? $voucher_data['voucher_id'] : 0;
			}
		}

		$total_usd = max( 0.0, $subtotal_usd - $discount_usd );

		// A subtotal > 0 must never result in total_usd of 0 unless the discount legitimately covers it.
		if ( ( $tour_subtotal > 0 || $rental_subtotal > 0 ) && $total_usd <= 0 && ! $discount_applied ) {
			return array( 'error' => 'price_calculation_error' );
		}

		$deposit_percent = self::get_deposit_percent();
		$deposit_usd     = round( ( $total_usd * $deposit_percent ) / 100, 2 );
		$balance_due_usd = max( 0.0, $total_usd - $deposit_usd );

		$rate_vnd    = self::get_exchange_rate();
		$total_vnd   = intval( round( $total_usd * $rate_vnd ) );
		$deposit_vnd = intval( round( $deposit_usd * $rate_vnd ) );

		$quote_payload = array(
			'tour_id'           => $tour_id,
			'party_size'        => $party_size,
			'vehicle_name'      => $vehicle_name,
			'tour_unit_price'   => $tour_unit_price,
			'tour_subtotal'     => $tour_subtotal,
			'transfer_subtotal' => $transfer_subtotal,
			'rental_subtotal'   => $rental_subtotal,
			'subtotal_usd'      => $subtotal_usd,
			'discount_usd'      => $discount_usd,
			'discount_applied'  => $discount_applied,
			'voucher_id'        => $voucher_post_id,
			'total_usd'         => $total_usd,
			'total_vnd'         => $total_vnd,
			'deposit_percent'   => $deposit_percent,
			'deposit_usd'       => $deposit_usd,
			'deposit_vnd'       => $deposit_vnd,
			'balance_due_usd'   => $balance_due_usd,
			'exchange_rate'     => $rate_vnd,
			'timestamp'         => time(),
		);

		$quote_payload['signature'] = self::sign_quote( $quote_payload );

		return $quote_payload;
	}

	/**
	 * Find the cheapest published vehicle_option linked to a tour (or its translation siblings).
	 */
	public static function get_cheapest_vehicle_for_tour( $tour_id ) {
		$cheapest = self::query_cheapest_vehicle_direct( $tour_id );
		if ( $cheapest ) {
			return $cheapest;
		}

		// Resolve via tbc_translation_group if this tour (e.g. VI version) has no vehicle_option of its own
		$group = get_post_meta( $tour_id, 'tbc_translation_group', true );
		if ( ! $group ) {
			return null;
		}
		$siblings = get_posts(
			array(
				'post_type'      => 'tour',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__not_in'   => array( $tour_id ),
				'meta_key'       => 'tbc_translation_group',
				'meta_value'     => $group,
			)
		);
		foreach ( $siblings as $sibling ) {
			$sibling_cheapest = self::query_cheapest_vehicle_direct( $sibling->ID );
			if ( $sibling_cheapest ) {
				return $sibling_cheapest;
			}
		}
		return null;
	}

	/**
	 * Query cheapest vehicle directly linked to a tour_id.
	 */
	private static function query_cheapest_vehicle_direct( $tour_id ) {
		$vehicles = get_posts(
			array(
				'post_type'      => 'vehicle_option',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => 'tbc_tour_id',
				'meta_value'     => $tour_id,
				'orderby'        => 'meta_value_num',
				'meta_query'     => array(
					array(
						'key'     => 'tbc_price_vnd',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
			)
		);
		if ( empty( $vehicles ) ) {
			return null;
		}
		$cheapest = null;
		foreach ( $vehicles as $v ) {
			$price = intval( get_post_meta( $v->ID, 'tbc_price_vnd', true ) );
			if ( null === $cheapest || $price < $cheapest['price_vnd'] ) {
				$cheapest = array(
					'id'        => $v->ID,
					'price_vnd' => $price,
					'label'     => get_the_title( $v->ID ),
				);
			}
		}
		return $cheapest;
	}

	/**
	 * Validate a voucher against database CPT rules or standard fallback.
	 */
	public static function validate_voucher( $code, $tour_id, $subtotal ) {
		$code = strtoupper( trim( $code ) );

		// 1. Search database Voucher CPT
		$voucher_posts = get_posts(
			array(
				'post_type'      => 'voucher',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'meta_key'       => 'tbc_code',
				'meta_value'     => $code,
			)
		);

		if ( ! empty( $voucher_posts ) ) {
			$v_post     = $voucher_posts[0];
			$v_type     = get_post_meta( $v_post->ID, 'tbc_voucher_type', true );
			$v_amount   = floatval( get_post_meta( $v_post->ID, 'tbc_amount', true ) );
			$valid_from = get_post_meta( $v_post->ID, 'tbc_valid_from', true );
			$valid_to   = get_post_meta( $v_post->ID, 'tbc_valid_to', true );
			$limit      = intval( get_post_meta( $v_post->ID, 'tbc_usage_limit', true ) );
			$used       = intval( get_post_meta( $v_post->ID, 'tbc_used_count', true ) );
			$min_vnd    = intval( get_post_meta( $v_post->ID, 'tbc_min_spend_vnd', true ) );

			$now = current_time( 'Y-m-d' );
			if ( $valid_from && $now < $valid_from ) {
				return array( 'valid' => false, 'message' => 'Voucher is not yet active.', 'discount_amount' => 0.0, 'code' => $code );
			}
			if ( $valid_to && $now > $valid_to ) {
				return array( 'valid' => false, 'message' => 'Voucher has expired.', 'discount_amount' => 0.0, 'code' => $code );
			}
			if ( $limit > 0 && $used >= $limit ) {
				return array( 'valid' => false, 'message' => 'Voucher usage limit reached.', 'discount_amount' => 0.0, 'code' => $code );
			}
			if ( $min_vnd > 0 && ( $subtotal * self::get_exchange_rate() ) < $min_vnd ) {
				return array( 'valid' => false, 'message' => 'Minimum spend requirement not met.', 'discount_amount' => 0.0, 'code' => $code );
			}

			$discount = 0.0;
			if ( 'percentage' === $v_type || empty( $v_type ) ) {
				$pct      = $v_amount > 0 ? ( $v_amount / 100 ) : 0.10;
				$discount = round( $subtotal * $pct, 2 );
			} else {
				$discount = min( $subtotal, $v_amount );
			}

			return array(
				'valid'           => true,
				'voucher_id'      => $v_post->ID,
				'discount_amount' => $discount,
				'code'            => $code,
				'message'         => 'Voucher discount applied!',
			);
		}

		// 2. Standard fallback vouchers
		if ( 'WELCOME10' === $code || 'LOOP10' === $code ) {
			$discount = round( $subtotal * 0.10, 2 );
			return array(
				'valid'           => true,
				'discount_amount' => $discount,
				'code'            => $code,
				'message'         => '10% discount applied!',
			);
		}

		if ( 'EARLYBIRD' === $code ) {
			$discount = 20.0;
			return array(
				'valid'           => true,
				'discount_amount' => min( $subtotal, $discount ),
				'code'            => $code,
				'message'         => '$20 Early Bird discount applied!',
			);
		}

		return array(
			'valid'           => false,
			'discount_amount' => 0.0,
			'code'            => $code,
			'message'         => 'Invalid or expired voucher code.',
		);
	}

	/**
	 * Configured USD to VND exchange rate.
	 */
	public static function get_exchange_rate() {
		$rate = get_option( 'tbc_exchange_rate', self::DEFAULT_USD_VND_RATE );
		return apply_filters( 'tbc_usd_vnd_exchange_rate', absint( $rate ) );
	}

	/**
	 * Configured deposit percent.
	 */
	public static function get_deposit_percent() {
		$pct = get_option( 'tbc_deposit_percent', self::DEFAULT_DEPOSIT_PERCENT );
		return min( 100, max( 1, absint( $pct ) ) );
	}

	/**
	 * Sign quote payload with HMAC.
	 */
	public static function sign_quote( $data ) {
		$secret = defined( 'NONCE_KEY' ) ? NONCE_KEY : 'tbc_secret_key_salt';
		$signable = sprintf(
			'%d|%d|%f|%f|%d',
			$data['tour_id'],
			$data['party_size'],
			$data['subtotal_usd'],
			$data['total_usd'],
			$data['timestamp']
		);
		return hash_hmac( 'sha256', $signable, $secret );
	}

	/**
	 * Verify quote signature.
	 */
	public static function verify_quote( $data ) {
		if ( empty( $data['signature'] ) ) {
			return false;
		}
		$expected = self::sign_quote( $data );
		return hash_equals( $expected, $data['signature'] );
	}

	/**
	 * Recompute and cache the tour's "starting from" price whenever one of
	 * its vehicle_option children is saved. Hooked to save_post_vehicle_option.
	 */
	public static function sync_tour_starting_price( $vehicle_post_id ) {
		$tour_id = absint( get_post_meta( $vehicle_post_id, 'tbc_tour_id', true ) );
		if ( ! $tour_id ) {
			return;
		}
		$cheapest = self::get_cheapest_vehicle_for_tour( $tour_id );
		if ( $cheapest ) {
			update_post_meta( $tour_id, 'tbc_price_from_vnd', $cheapest['price_vnd'] );

			// Also sync to sibling tours in the same translation group
			$group = get_post_meta( $tour_id, 'tbc_translation_group', true );
			if ( $group ) {
				$siblings = get_posts(
					array(
						'post_type'      => 'tour',
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'post__not_in'   => array( $tour_id ),
						'meta_key'       => 'tbc_translation_group',
						'meta_value'     => $group,
					)
				);
				foreach ( $siblings as $sibling ) {
					update_post_meta( $sibling->ID, 'tbc_price_from_vnd', $cheapest['price_vnd'] );
				}
			}
		}
	}
}
