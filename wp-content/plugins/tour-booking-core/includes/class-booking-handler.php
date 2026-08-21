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

	public static function handle_quote( WP_REST_Request $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		$quote = Tbc_Pricing_Engine::calculate_quote( $params );
		return rest_ensure_response(
			array(
				'success' => true,
				'quote'   => $quote,
			)
		);
	}

	public static function handle_book( WP_REST_Request $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		// 1. Honeypot check (Spec §8 item 17)
		if ( ! empty( $params['honeypot_field'] ) ) {
			return new WP_Error( 'spam_detected', 'Spam detection triggered.', array( 'status' => 400 ) );
		}

		// 2. Validate required fields
		$customer_name  = isset( $params['customer_name'] ) ? sanitize_text_field( $params['customer_name'] ) : '';
		$customer_email = isset( $params['customer_email'] ) ? sanitize_email( $params['customer_email'] ) : '';
		$customer_phone = isset( $params['customer_phone'] ) ? sanitize_text_field( $params['customer_phone'] ) : '';
		$tour_id        = isset( $params['tour_id'] ) ? absint( $params['tour_id'] ) : 0;
		$start_date     = isset( $params['start_date'] ) ? sanitize_text_field( $params['start_date'] ) : '';
		$party_size     = isset( $params['party_size'] ) ? max( 1, absint( $params['party_size'] ) ) : 1;

		if ( empty( $customer_name ) || empty( $customer_email ) ) {
			return new WP_Error( 'missing_fields', 'Name and email are required.', array( 'status' => 400 ) );
		}

		// 3. Authoritative server recalculation
		$quote = Tbc_Pricing_Engine::calculate_quote( $params );

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
		update_post_meta( $booking_id, 'tbc_booking_status', 'confirmed' );

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
		Tbc_Mailer::send_booking_emails( $booking_id, $booking_data );

		return rest_ensure_response(
			array(
				'success'     => true,
				'booking_id'  => $booking_id,
				'booking_ref' => $booking_ref,
				'quote'       => $quote,
				'message'     => 'Booking successfully submitted! Confirmation email has been sent.',
			)
		);
	}
}
