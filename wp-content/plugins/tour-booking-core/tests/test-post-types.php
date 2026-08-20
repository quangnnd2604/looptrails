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

		// The meta-cap keys must map to DEDICATED singular capabilities. Pointing
		// any of them at the blanket primitive (manage_tbc_bookings) registers that
		// primitive in WordPress's global $post_type_meta_caps table, which makes
		// current_user_can( 'manage_tbc_bookings' ) recurse into map_meta_cap()
		// and return do_not_allow for everyone, administrators included.
		$this->assertSame( 'edit_tbc_booking', $booking->cap->edit_post );
		$this->assertSame( 'read_tbc_booking', $booking->cap->read_post );
		$this->assertSame( 'delete_tbc_booking', $booking->cap->delete_post );
	}

	public function test_voucher_uses_custom_capabilities() {
		$voucher = get_post_type_object( 'voucher' );

		$this->assertSame( 'manage_tbc_vouchers', $voucher->cap->edit_posts );

		$this->assertSame( 'edit_tbc_voucher', $voucher->cap->edit_post );
		$this->assertSame( 'read_tbc_voucher', $voucher->cap->read_post );
		$this->assertSame( 'delete_tbc_voucher', $voucher->cap->delete_post );
	}

	public function test_availability_rule_uses_custom_capabilities() {
		$rule = get_post_type_object( 'availability_rule' );

		$this->assertSame( 'manage_tbc_availability', $rule->cap->edit_posts );
		$this->assertSame( 'manage_tbc_availability', $rule->cap->edit_others_posts );
		$this->assertSame( 'edit_tbc_availability_rule', $rule->cap->edit_post );
		$this->assertSame( 'read_tbc_availability_rule', $rule->cap->read_post );
		$this->assertSame( 'delete_tbc_availability_rule', $rule->cap->delete_post );
	}

	/**
	 * Regression test for the meta-cap poisoning bug: a WP_Role::has_cap() check
	 * passes even when the bug is present, so this must go through the real
	 * current_user_can() -> map_meta_cap() chain.
	 */
	public function test_blanket_capabilities_resolve_for_an_administrator() {
		Tbc_Roles::install();

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->assertTrue( current_user_can( 'manage_tbc_bookings' ) );
		$this->assertTrue( current_user_can( 'manage_tbc_vouchers' ) );
		$this->assertTrue( current_user_can( 'manage_tbc_availability' ) );
		$this->assertTrue( current_user_can( 'edit_tbc_prices' ) );
	}

	public function test_public_post_types_are_rest_enabled() {
		$rest_enabled = array_diff( $this->expected, array( 'booking', 'voucher' ) );

		foreach ( $rest_enabled as $post_type ) {
			$object = get_post_type_object( $post_type );
			$this->assertTrue( $object->show_in_rest, "Not REST-enabled: {$post_type}" );
		}
	}

	/**
	 * public => false does NOT stop REST reads of published posts:
	 * WP_REST_Posts_Controller::check_read_permission() allows any published post.
	 * Operational records must opt out of REST explicitly.
	 */
	public function test_booking_and_voucher_are_not_rest_enabled() {
		$this->assertFalse( get_post_type_object( 'booking' )->show_in_rest );
		$this->assertFalse( get_post_type_object( 'voucher' )->show_in_rest );
	}
}
