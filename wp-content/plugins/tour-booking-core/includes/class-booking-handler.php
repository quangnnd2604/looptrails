<?php
/**
 * REST API Booking & Quote Handler
 *
 * Implements Spec §8.1, §8.2.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Booking_Handler {

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	public static function register_routes() {
		register_rest_route(
			'tour-booking/v1',
			'/quote',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle_quote' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'tour-booking/v1',
			'/book',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle_book' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rate limiter helper.
	 */
	private static function check_rate_limit() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
		$key = 'tbc_rl_' . md5( $ip );
		$count = (int) get_transient( $key );
		if ( $count > 30 ) {
			return false;
		}
		set_transient( $key, $count + 1, 60 );
		return true;
	}

	public static function handle_quote( WP_REST_Request $request ) {
		if ( ! self::check_rate_limit() ) {
			return new WP_Error( 'rate_limit_exceeded', 'Too many requests. Please try again in a minute.', array( 'status' => 429 ) );
		}

		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		$quote = Tbc_Pricing_Engine::calculate_quote( $params );
		if ( isset( $quote['error'] ) ) {
			return new WP_Error( $quote['error'], 'Unable to calculate a valid quote for the given parameters.', array( 'status' => 400 ) );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'quote'   => $quote,
			)
		);
	}

	public static function handle_book( WP_REST_Request $request ) {
		if ( ! self::check_rate_limit() ) {
			return new WP_Error( 'rate_limit_exceeded', 'Too many requests. Please try again in a minute.', array( 'status' => 429 ) );
		}

		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		// 1. Honeypot check (Spec §8 item 17)
		if ( ! empty( $params['honeypot_field'] ) ) {
			return new WP_Error( 'spam_detected', 'Spam detection triggered.', array( 'status' => 400 ) );
		}

		// 2. Validate required fields
		$customer_name   = isset( $params['customer_name'] ) ? sanitize_text_field( $params['customer_name'] ) : '';
		$customer_email  = isset( $params['customer_email'] ) ? sanitize_email( $params['customer_email'] ) : '';
		$customer_phone  = isset( $params['customer_phone'] ) ? sanitize_text_field( $params['customer_phone'] ) : '';
		$tour_id         = isset( $params['tour_id'] ) ? absint( $params['tour_id'] ) : 0;
		$start_date      = isset( $params['start_date'] ) ? sanitize_text_field( $params['start_date'] ) : '';
		$party_size      = isset( $params['party_size'] ) ? max( 1, absint( $params['party_size'] ) ) : 1;
		$idempotency_key = isset( $params['idempotency_key'] ) ? sanitize_text_field( $params['idempotency_key'] ) : '';

		if ( empty( $customer_name ) || empty( $customer_email ) ) {
			return new WP_Error( 'missing_fields', 'Name and email are required.', array( 'status' => 400 ) );
		}

		// Check idempotency key within 24h
		if ( ! empty( $idempotency_key ) ) {
			$existing = get_posts(
				array(
					'post_type'      => 'booking',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'meta_key'       => 'tbc_idempotency_key',
					'meta_value'     => $idempotency_key,
				)
			);
			if ( ! empty( $existing ) ) {
				$existing_id  = $existing[0]->ID;
				$existing_ref = get_post_meta( $existing_id, 'tbc_booking_ref', true );
				return rest_ensure_response(
					array(
						'success'     => true,
						'booking_id'  => $existing_id,
						'booking_ref' => $existing_ref,
						'message'     => 'Booking already submitted.',
					)
				);
			}
		}

		// 3. Authoritative server recalculation
		$quote = Tbc_Pricing_Engine::calculate_quote( $params );
		if ( isset( $quote['error'] ) ) {
			return new WP_Error( $quote['error'], 'Unable to calculate a valid quote for the given parameters.', array( 'status' => 400 ) );
		}

		// 4. Generate unique Booking Reference
		$booking_ref = sprintf( 'LT-%s-%04d', gmdate( 'Ymd' ), wp_rand( 1000, 9999 ) );
		$tour_title  = $tour_id ? get_the_title( $tour_id ) : 'Custom Northern Loop Tour';

		// 5. Create Booking post in WP database
		$booking_id = wp_insert_post(
			array(
				'post_type'   => 'booking',
				'post_title'  => sprintf( '%s — %s (%s)', $booking_ref, $customer_name, $tour_title ),
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $booking_id ) ) {
			return new WP_Error( 'booking_failed', 'Could not create booking record.', array( 'status' => 500 ) );
		}

		// 6. Save metadata
		update_post_meta( $booking_id, 'tbc_booking_ref', $booking_ref );
		update_post_meta( $booking_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $booking_id, 'tbc_customer_name', $customer_name );
		update_post_meta( $booking_id, 'tbc_customer_email', $customer_email );
		update_post_meta( $booking_id, 'tbc_customer_phone', $customer_phone );
		update_post_meta( $booking_id, 'tbc_start_date', $start_date );
		update_post_meta( $booking_id, 'tbc_party_size', $party_size );
		update_post_meta( $booking_id, 'tbc_total_usd', $quote['total_usd'] );
		update_post_meta( $booking_id, 'tbc_total_vnd', $quote['total_vnd'] );
		update_post_meta( $booking_id, 'tbc_deposit_usd', $quote['deposit_usd'] );
		update_post_meta( $booking_id, 'tbc_deposit_vnd', $quote['deposit_vnd'] );
		update_post_meta( $booking_id, 'tbc_payment_status', 'pending' );
		update_post_meta( $booking_id, 'tbc_booking_status', 'pending_payment' );
		if ( ! empty( $idempotency_key ) ) {
			update_post_meta( $booking_id, 'tbc_idempotency_key', $idempotency_key );
		}

		// Increment voucher usage count if applicable
		if ( ! empty( $quote['voucher_id'] ) ) {
			$cur_used = intval( get_post_meta( $quote['voucher_id'], 'tbc_used_count', true ) );
			update_post_meta( $quote['voucher_id'], 'tbc_used_count', $cur_used + 1 );
		}

		// 7. Send notification emails
		$booking_data = array(
			'booking_ref'    => $booking_ref,
			'customer_name'  => $customer_name,
			'customer_email' => $customer_email,
			'tour_name'      => $tour_title,
			'start_date'     => $start_date,
			'total_usd'      => $quote['total_usd'],
			'deposit_usd'    => $quote['deposit_usd'],
		);
		$email_sent = Tbc_Mailer::send_booking_emails( $booking_data );

		$response_msg = $email_sent
			? 'Booking successfully submitted! Confirmation email has been sent.'
			: 'Booking submitted successfully — our operations team will contact you directly to confirm.';

		return rest_ensure_response(
			array(
				'success'     => true,
				'booking_id'  => $booking_id,
				'booking_ref' => $booking_ref,
				'quote'       => $quote,
				'message'     => $response_msg,
			)
		);
	}
}
