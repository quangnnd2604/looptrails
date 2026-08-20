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

	/**
	 * booking_manager holds manage_tbc_availability, so the availability_rule
	 * post type must actually be wired to that capability. Checked through
	 * current_user_can() against a post authored by somebody else, because
	 * WP_Role::has_cap() never exercises map_meta_cap()'s real resolution.
	 */
	public function test_booking_manager_can_edit_availability_rule_authored_by_another_user() {
		$other_user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$manager_id    = self::factory()->user->create( array( 'role' => 'booking_manager' ) );
		$rule_id       = self::factory()->post->create(
			array(
				'post_type'   => 'availability_rule',
				'post_author' => $other_user_id,
				'post_status' => 'publish',
			)
		);

		wp_set_current_user( $manager_id );

		$this->assertTrue( current_user_can( 'edit_post', $rule_id ) );
		$this->assertTrue( current_user_can( get_post_type_object( 'availability_rule' )->cap->edit_posts ) );
	}

	/**
	 * A translator's whole job is editing tours somebody else authored, which
	 * needs edit_others_posts on top of edit_published_posts.
	 */
	public function test_translator_can_edit_published_tour_authored_by_another_user() {
		$other_user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$translator_id = self::factory()->user->create( array( 'role' => 'translator' ) );
		$tour_id       = self::factory()->post->create(
			array(
				'post_type'   => 'tour',
				'post_author' => $other_user_id,
				'post_status' => 'publish',
			)
		);

		wp_set_current_user( $translator_id );

		$this->assertTrue( current_user_can( 'edit_post', $tour_id ) );
	}

	public function test_translator_still_cannot_write_price_meta() {
		$translator_id = self::factory()->user->create( array( 'role' => 'translator' ) );
		$vehicle_id    = self::factory()->post->create(
			array(
				'post_type'   => 'vehicle_option',
				'post_author' => self::factory()->user->create( array( 'role' => 'administrator' ) ),
				'post_status' => 'publish',
			)
		);

		wp_set_current_user( $translator_id );

		$this->assertFalse( current_user_can( 'edit_tbc_prices' ) );
		$this->assertFalse( current_user_can( 'edit_post_meta', $vehicle_id, 'tbc_price_vnd' ) );
	}

	public function test_translator_cannot_edit_booking_at_all() {
		$user_id    = self::factory()->user->create( array( 'role' => 'translator' ) );
		$booking_id = self::factory()->post->create( array( 'post_type' => 'booking' ) );

		wp_set_current_user( $user_id );

		$this->assertFalse( current_user_can( 'edit_post', $booking_id ) );
	}
}
