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
		$rental_rate  = isset( $args['rental_rate'] ) ? floatval( $args['rental_rate'] ) : 0.0;
		$voucher_code = isset( $args['voucher_code'] ) ? sanitize_text_field( $args['voucher_code'] ) : '';

		// 1. Base Tour Price
		$base_price_usd = 140.0; // standard default
		if ( $tour_id ) {
			$meta_price = floatval( get_post_meta( $tour_id, 'tbc_price_from_usd', true ) );
			if ( $meta_price > 0 ) {
				$base_price_usd = $meta_price;
			}
		}

		// 2. Vehicle Tier Adjustment
		$vehicle_surcharge_usd = 0.0;
		$vehicle_name = 'Standard Self-Ride';
		if ( $vehicle_id ) {
			$v_usd = floatval( get_post_meta( $vehicle_id, 'tbc_price_usd', true ) );
			if ( $v_usd > 0 ) {
				$vehicle_surcharge_usd = $v_usd - $base_price_usd;
				$vehicle_name = get_the_title( $vehicle_id );
			}
		}

		$tour_unit_price = max( $base_price_usd, $base_price_usd + $vehicle_surcharge_usd );
		$tour_subtotal   = $tour_unit_price * $party_size;

		// 3. Transfers
		$transfer_subtotal = 0.0;
		if ( $transfer_in ) {
			$t_in_price = floatval( get_post_meta( $transfer_in, 'tbc_price_usd', true ) );
			$transfer_subtotal += ( $t_in_price > 0 ? $t_in_price : 15.0 ) * $party_size;
		}
		if ( $transfer_out ) {
			$t_out_price = floatval( get_post_meta( $transfer_out, 'tbc_price_usd', true ) );
			$transfer_subtotal += ( $t_out_price > 0 ? $t_out_price : 15.0 ) * $party_size;
		}

		// 4. Motorbike Rental Addon
		$rental_subtotal = $rental_days * $rental_rate;

		// Subtotal before discount
		$subtotal_usd = $tour_subtotal + $transfer_subtotal + $rental_subtotal;

		// 5. Voucher / Discount
		$discount_usd = 0.0;
		$discount_applied = false;
		if ( ! empty( $voucher_code ) ) {
			$voucher_data = self::validate_voucher( $voucher_code, $tour_id, $subtotal_usd );
			if ( $voucher_data['valid'] ) {
				$discount_usd = $voucher_data['discount_amount'];
				$discount_applied = true;
			}
		}

		$total_usd = max( 0.0, $subtotal_usd - $discount_usd );

		// 6. Deposit
		$deposit_percent = self::DEFAULT_DEPOSIT_PERCENT;
		$deposit_usd     = round( ( $total_usd * $deposit_percent ) / 100, 2 );
		$balance_due_usd = max( 0.0, $total_usd - $deposit_usd );

		// 7. VND Currency Conversion
		$rate_vnd   = self::get_exchange_rate();
		$total_vnd  = intval( round( $total_usd * $rate_vnd ) );
		$deposit_vnd = intval( round( $deposit_usd * $rate_vnd ) );

		// 8. Sign quote for anti-tampering
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
	 * Validate a voucher against rules.
	 */
	public static function validate_voucher( $code, $tour_id, $subtotal ) {
		$code = strtoupper( trim( $code ) );

		// Standard seeded vouchers
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
	 * Current USD to VND exchange rate.
	 */
	public static function get_exchange_rate() {
		return apply_filters( 'tbc_usd_vnd_exchange_rate', self::DEFAULT_USD_VND_RATE );
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
}
