<?php
/**
 * Test Milestone 7 Pricing Engine & Booking Handler.
 */

class Test_Booking_Engine extends WP_UnitTestCase {

	private $tour_id;
	private $vehicle_id;

	public function set_up() {
		parent::set_up();

		$this->tour_id = wp_insert_post(
			array(
				'post_title'  => 'Test Ha Giang Loop Tour',
				'post_type'   => 'tour',
				'post_status' => 'publish',
			)
		);

		$this->vehicle_id = wp_insert_post(
			array(
				'post_title'  => 'Motorbike Self-Ride',
				'post_type'   => 'vehicle_option',
				'post_status' => 'publish',
			)
		);
		update_post_meta( $this->vehicle_id, 'tbc_tour_id', $this->tour_id );
		update_post_meta( $this->vehicle_id, 'tbc_price_vnd', 3556000 ); // $140 USD
		update_post_meta( $this->tour_id, 'tbc_price_from_vnd', 3556000 );
	}

	public function test_pricing_engine_calculates_base_and_party_size() {
		$args = array(
			'tour_id'    => $this->tour_id,
			'party_size' => 3,
		);

		$quote = Tbc_Pricing_Engine::calculate_quote( $args );

		$this->assertIsArray( $quote );
		$this->assertFalse( isset( $quote['error'] ) );
		$this->assertEquals( 3, $quote['party_size'] );
		$this->assertEquals( 420.0, $quote['tour_subtotal'] ); // 140 * 3
		$this->assertEquals( 420.0, $quote['total_usd'] );
		$this->assertEquals( 84.0, $quote['deposit_usd'] ); // 20% of 420
		$this->assertEquals( 336.0, $quote['balance_due_usd'] );
		$this->assertTrue( $quote['total_vnd'] > 0 );
		$this->assertTrue( Tbc_Pricing_Engine::verify_quote( $quote ) );
	}

	public function test_voucher_discount_application() {
		$args = array(
			'tour_id'      => $this->tour_id,
			'party_size'   => 2,
			'voucher_code' => 'WELCOME10',
		);

		$quote = Tbc_Pricing_Engine::calculate_quote( $args );

		$this->assertIsArray( $quote );
		$this->assertTrue( $quote['discount_applied'] );
		$this->assertEquals( 28.0, $quote['discount_usd'] ); // 10% of 280
		$this->assertEquals( 252.0, $quote['total_usd'] ); // 280 - 28
	}

	public function test_booking_handler_rest_route_quote() {
		$request = new WP_REST_Request( 'POST', '/tour-booking/v1/quote' );
		$request->set_body_params(
			array(
				'tour_id'    => $this->tour_id,
				'party_size' => 2,
			)
		);

		$response = Tbc_Booking_Handler::handle_quote( $request );
		$this->assertNotWPError( $response );
		$data = $response->get_data();

		$this->assertTrue( $data['success'] );
		$this->assertEquals( 280.0, $data['quote']['subtotal_usd'] );
	}

	public function test_booking_handler_honeypot_rejection() {
		$request = new WP_REST_Request( 'POST', '/tour-booking/v1/book' );
		$request->set_body_params(
			array(
				'customer_name'  => 'Spam Bot',
				'customer_email' => 'spam@bot.com',
				'honeypot_field' => 'i am a bot',
			)
		);

		$response = Tbc_Booking_Handler::handle_book( $request );
		$this->assertWPError( $response );
		$this->assertEquals( 'spam_detected', $response->get_error_code() );
	}

	public function test_booking_creation_and_meta_recording() {
		$request = new WP_REST_Request( 'POST', '/tour-booking/v1/book' );
		$request->set_body_params(
			array(
				'tour_id'        => $this->tour_id,
				'customer_name'  => 'John Rider',
				'customer_email' => 'john.rider@example.com',
				'customer_phone' => '+84987654321',
				'party_size'     => 2,
				'start_date'     => '2026-09-01',
			)
		);

		$response = Tbc_Booking_Handler::handle_book( $request );
		$this->assertNotWPError( $response );
		$data = $response->get_data();

		$this->assertTrue( $data['success'] );
		$this->assertNotEmpty( $data['booking_id'] );
		$this->assertStringStartsWith( 'LT-', $data['booking_ref'] );

		// Verify meta saved in DB
		$saved_email = get_post_meta( $data['booking_id'], 'tbc_customer_email', true );
		$this->assertEquals( 'john.rider@example.com', $saved_email );
		$saved_status = get_post_meta( $data['booking_id'], 'tbc_booking_status', true );
		$this->assertEquals( 'pending_payment', $saved_status );
	}

	public function test_pure_motorbike_rental_quote_without_tour_id() {
		$args = array(
			'rental_bike' => 'wave_alpha',
			'rental_days' => 3,
			'party_size'  => 1,
		);

		$quote = Tbc_Pricing_Engine::calculate_quote( $args );

		$this->assertIsArray( $quote );
		$this->assertFalse( isset( $quote['error'] ) );
		$this->assertEquals( 0, $quote['tour_id'] );
		$this->assertEquals( 0.0, $quote['tour_subtotal'] );
		$this->assertEquals( 30.0, $quote['rental_subtotal'] ); // 3 days * $10 USD
		$this->assertEquals( 30.0, $quote['total_usd'] );
		$this->assertEquals( 6.0, $quote['deposit_usd'] ); // 20% of 30
		$this->assertTrue( $quote['total_vnd'] > 0 );
		$this->assertTrue( Tbc_Pricing_Engine::verify_quote( $quote ) );
	}

	public function test_pure_motorbike_rental_booking() {
		$request = new WP_REST_Request( 'POST', '/tour-booking/v1/book' );
		$request->set_body_params(
			array(
				'rental_bike'    => 'wave_alpha',
				'rental_days'    => 3,
				'customer_name'  => 'Rental Rider',
				'customer_email' => 'rental@rider.com',
				'customer_phone' => '+84900000000',
				'start_date'     => '2026-09-10',
			)
		);

		$response = Tbc_Booking_Handler::handle_book( $request );
		$this->assertNotWPError( $response );
		$data = $response->get_data();

		$this->assertTrue( $data['success'] );
		$this->assertNotEmpty( $data['booking_id'] );

		$booking_post = get_post( $data['booking_id'] );
		$this->assertStringContainsString( 'Motorbike Rental', $booking_post->post_title );
		$this->assertSame( 0, (int) get_post_meta( $data['booking_id'], 'tbc_tour_id', true ) );
	}
}
