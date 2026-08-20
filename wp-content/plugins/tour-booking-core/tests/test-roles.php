<?php
class Test_Roles extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		Tbc_Roles::install();
	}

	public function test_administrator_gets_custom_capabilities() {
		$admin = get_role( 'administrator' );

		foreach ( Tbc_Roles::ADMIN_CAPS as $cap ) {
			$this->assertTrue( $admin->has_cap( $cap ), "Administrator missing {$cap}" );
		}
	}

	public function test_booking_manager_role_exists_with_scoped_caps() {
		$role = get_role( 'booking_manager' );

		$this->assertNotNull( $role );
		$this->assertTrue( $role->has_cap( 'manage_tbc_bookings' ) );
		$this->assertTrue( $role->has_cap( 'manage_tbc_vouchers' ) );
		$this->assertTrue( $role->has_cap( 'manage_tbc_availability' ) );
		$this->assertFalse( $role->has_cap( 'edit_plugins' ) );
		$this->assertFalse( $role->has_cap( 'edit_theme_options' ) );
		$this->assertFalse( $role->has_cap( 'install_plugins' ) );
	}

	public function test_translator_role_cannot_manage_prices_or_bookings() {
		$role = get_role( 'translator' );

		$this->assertNotNull( $role );
		$this->assertTrue( $role->has_cap( 'edit_posts' ) );
		$this->assertFalse( $role->has_cap( 'edit_tbc_prices' ) );
		$this->assertFalse( $role->has_cap( 'manage_tbc_bookings' ) );
		$this->assertFalse( $role->has_cap( 'manage_tbc_vouchers' ) );
		$this->assertFalse( $role->has_cap( 'edit_plugins' ) );
	}

	public function test_booking_manager_can_edit_booking_price_meta() {
		$user_id    = self::factory()->user->create( array( 'role' => 'booking_manager' ) );
		$booking_id = self::factory()->post->create( array( 'post_type' => 'booking' ) );

		wp_set_current_user( $user_id );

		$this->assertTrue( current_user_can( 'edit_post_meta', $booking_id, 'tbc_total_vnd' ) );
	}

	public function test_translator_cannot_edit_booking_at_all() {
		$user_id    = self::factory()->user->create( array( 'role' => 'translator' ) );
		$booking_id = self::factory()->post->create( array( 'post_type' => 'booking' ) );

		wp_set_current_user( $user_id );

		$this->assertFalse( current_user_can( 'edit_post', $booking_id ) );
	}
}
