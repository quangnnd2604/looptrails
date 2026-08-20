<?php
class Test_Post_Types extends WP_UnitTestCase {

	private $expected = array(
		'tour',
		'destination',
		'itinerary_day',
		'vehicle_option',
		'accommodation',
		'transfer_option',
		'addon',
		'testimonial',
		'faq',
		'booking',
		'voucher',
		'availability_rule',
	);

	public function test_all_post_types_registered() {
		foreach ( $this->expected as $post_type ) {
			$this->assertTrue( post_type_exists( $post_type ), "Missing post type: {$post_type}" );
		}
	}

	public function test_tour_is_public_with_archive() {
		$tour = get_post_type_object( 'tour' );

		$this->assertTrue( $tour->public );
		$this->assertTrue( $tour->has_archive );
		$this->assertSame( 'tours', $tour->rewrite['slug'] );
	}

	public function test_booking_is_not_public() {
		$booking = get_post_type_object( 'booking' );

		$this->assertFalse( $booking->public );
	}

	public function test_booking_uses_custom_capabilities() {
		$booking = get_post_type_object( 'booking' );

		$this->assertSame( 'manage_tbc_bookings', $booking->cap->edit_posts );
		$this->assertNotSame( 'edit_posts', $booking->cap->edit_posts );
	}

	public function test_voucher_uses_custom_capabilities() {
		$voucher = get_post_type_object( 'voucher' );

		$this->assertSame( 'manage_tbc_vouchers', $voucher->cap->edit_posts );
	}

	public function test_all_post_types_are_rest_enabled() {
		foreach ( $this->expected as $post_type ) {
			$object = get_post_type_object( $post_type );
			$this->assertTrue( $object->show_in_rest, "Not REST-enabled: {$post_type}" );
		}
	}
}
