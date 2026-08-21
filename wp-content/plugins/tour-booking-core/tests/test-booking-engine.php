<?php
/**
 * Test Milestone 7 Pricing Engine & Booking Handler.
 */

class Test_Booking_Engine extends WP_UnitTestCase {

	public function test_pricing_engine_calculates_base_and_party_size() {
		$args = array(
			'tour_id'    => 0,
			'party_size' => 3,
		);

		$quote = Tbc_Pricing_Engine::calculate_quote( $args );

		$this->assertNotEmpty( $quote );
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
			'tour_id'      => 0,
			'party_size'   => 2,
			'voucher_code' => 'WELCOME10',
		);

		$quote = Tbc_Pricing_Engine::calculate_quote( $args );

		$this->assertTrue( $quote['discount_applied'] );
		$this->assertEquals( 28.0, $quote['discount_usd'] ); // 10% of 280
		$this->assertEquals( 252.0, $quote['total_usd'] ); // 280 - 28
	}

	public function test_booking_handler_rest_route_quote() {
		$request = new WP_REST_Request( 'POST', '/tour-booking/v1/quote' );
		$request->set_body_params( array( 'party_size' => 2 ) );

		$response = Tbc_Booking_Handler::handle_quote( $request );
		$data     = $response->get_data();

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
		$this->assertEquals( 'confirmed', $saved_status );
	}
}
