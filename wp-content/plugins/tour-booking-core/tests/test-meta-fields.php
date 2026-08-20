<?php
class Test_Meta_Fields extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		// Ensure meta fields are registered for each test
		if ( ! get_registered_meta_keys( 'post', 'vehicle_option' ) ) {
			Tbc_Meta_Fields::register();
		}
	}

	public function test_all_schema_meta_keys_are_registered() {
		foreach ( Tbc_Meta_Fields::get_schema() as $post_type => $fields ) {
			$registered = get_registered_meta_keys( 'post', $post_type );
			foreach ( array_keys( $fields ) as $meta_key ) {
				$this->assertArrayHasKey( $meta_key, $registered, "Missing meta {$meta_key} on {$post_type}" );
			}
		}
	}

	public function test_vehicle_option_price_meta_is_registered() {
		$this->assertArrayHasKey( 'tbc_price_vnd', get_registered_meta_keys( 'post', 'vehicle_option' ) );
	}

	public function test_price_meta_denies_translator() {
		$translator_id = self::factory()->user->create( array( 'role' => 'translator' ) );
		$vehicle_id     = self::factory()->post->create( array( 'post_type' => 'vehicle_option' ) );

		wp_set_current_user( $translator_id );

		$this->assertFalse( current_user_can( 'edit_post_meta', $vehicle_id, 'tbc_price_vnd' ) );
	}

	public function test_price_meta_allows_administrator() {
		$admin_id   = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$vehicle_id = self::factory()->post->create( array( 'post_type' => 'vehicle_option' ) );

		// Grant edit_tbc_prices capability to the admin user
		$user = get_userdata( $admin_id );
		$user->add_cap( 'edit_tbc_prices' );

		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'edit_post_meta', $vehicle_id, 'tbc_price_vnd' ) );
	}

	public function test_non_price_meta_only_requires_edit_post_capability() {
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$tour_id  = self::factory()->post->create( array( 'post_type' => 'tour' ) );

		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'edit_post_meta', $tour_id, 'tbc_badge' ) );
	}
}
