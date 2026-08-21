<?php
/**
 * Test Unified Tour Editor Meta Boxes and Synchronization Logic.
 */

class Test_Tour_Editor extends WP_UnitTestCase {

	private $admin_id;
	private $editor_id;
	private $tour_id;

	public function setUp(): void {
		parent::setUp();

		$this->admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$this->editor_id = $this->factory->user->create( array( 'role' => 'editor' ) );

		// Give admin edit_tbc_prices capability
		$admin = get_user_by( 'id', $this->admin_id );
		$admin->add_cap( 'edit_tbc_prices' );

		// Create a test tour
		$this->tour_id = $this->factory->post->create( array(
			'post_type'   => 'tour',
			'post_title'  => 'Test Highlands Loop',
			'post_status' => 'publish',
		) );
	}

	public function test_meta_boxes_registered() {
		wp_set_current_user( $this->admin_id );
		global $wp_meta_boxes;
		$wp_meta_boxes = array();

		Tbc_Tour_Editor::register_meta_boxes();

		$this->assertNotEmpty( $wp_meta_boxes['tour']['normal']['high']['tbc_itinerary_metabox'] );
		$this->assertNotEmpty( $wp_meta_boxes['tour']['normal']['high']['tbc_vehicles_metabox'] );
		$this->assertNotEmpty( $wp_meta_boxes['tour']['normal']['default']['tbc_accommodation_metabox'] );
		$this->assertNotEmpty( $wp_meta_boxes['tour']['normal']['default']['tbc_transfer_metabox'] );
		$this->assertNotEmpty( $wp_meta_boxes['tour']['normal']['default']['tbc_addons_metabox'] );
		$this->assertNotEmpty( $wp_meta_boxes['tour']['normal']['default']['tbc_availability_metabox'] );
	}

	public function test_save_creates_all_child_cpts_with_real_data() {
		wp_set_current_user( $this->admin_id );

		$_POST = array(
			'tbc_tour_editor_nonce' => wp_create_nonce( 'tbc_save_tour_editor' ),
			'tbc_itinerary'         => array(
				0 => array(
					'post_id'     => 0,
					'day_number'  => 1,
					'title'       => 'Day 1: Ha Giang to Yen Minh',
					'description' => 'Cross Bac Sum pass and enjoy pine hills.',
					'included'    => 'Breakfast, bike, helmet',
					'excluded'    => 'Personal drinks',
					'delete'      => 0,
				),
				1 => array(
					'post_id'     => 0,
					'day_number'  => 2,
					'title'       => 'Day 2: Yen Minh to Dong Van',
					'description' => 'Ride Tham Ma pass and visit King Palace.',
					'included'    => 'All meals, boat ticket',
					'excluded'    => 'Tips',
					'delete'      => 0,
				),
			),
			'tbc_vehicles'          => array(
				0 => array(
					'post_id'      => 0,
					'title'        => 'Honda Wave 110cc',
					'vehicle_type' => 'motorbike',
					'price_vnd'    => 350000,
					'capacity'     => 2,
					'delete'       => 0,
				),
				1 => array(
					'post_id'      => 0,
					'title'        => '4x4 Mountain Jeep',
					'vehicle_type' => 'jeep',
					'price_vnd'    => 900000,
					'capacity'     => 6,
					'delete'       => 0,
				),
			),
			'tbc_accommodation'     => array(
				0 => array(
					'post_id'     => 0,
					'title'       => 'Ethnic Stilt Homestay',
					'description' => 'Clean bedding and hot showers.',
					'price_vnd'   => 200000,
					'upgrade'     => 0,
					'delete'      => 0,
				),
			),
			'tbc_transfer'          => array(
				0 => array(
					'post_id'   => 0,
					'title'     => 'VIP Sleeper Bus Hanoi - Ha Giang',
					'direction' => 'to',
					'price_vnd' => 250000,
					'delete'    => 0,
				),
			),
			'tbc_addons'            => array(
				0 => array(
					'post_id'     => 0,
					'title'       => 'Full Coverage Travel Insurance',
					'description' => 'Covers all mountain sports.',
					'price_vnd'   => 100000,
					'delete'      => 0,
				),
			),
			'tbc_availability'      => array(
				0 => array(
					'post_id'  => 0,
					'date'     => '2026-09-01',
					'state'    => 'available',
					'capacity' => 12,
					'delete'   => 0,
				),
			),
		);

		Tbc_Tour_Editor::save( $this->tour_id );

		// Verify Itinerary Days
		$days = get_posts( array(
			'post_type'      => 'itinerary_day',
			'post_status'    => 'any',
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $this->tour_id,
			'posts_per_page' => -1,
		) );
		usort( $days, function ( $a, $b ) {
			return ( (int) get_post_meta( $a->ID, 'tbc_day_number', true ) ) <=> ( (int) get_post_meta( $b->ID, 'tbc_day_number', true ) );
		} );
		$this->assertCount( 2, $days );
		$this->assertSame( 'Day 1: Ha Giang to Yen Minh', $days[0]->post_title );
		$this->assertSame( '1', (string) get_post_meta( $days[0]->ID, 'tbc_day_number', true ) );
		$this->assertSame( 'Breakfast, bike, helmet', get_post_meta( $days[0]->ID, 'tbc_included', true ) );

		// Verify Vehicles
		$vehicles = get_posts( array(
			'post_type'      => 'vehicle_option',
			'post_status'    => 'any',
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $this->tour_id,
			'posts_per_page' => -1,
		) );
		usort( $vehicles, function ( $a, $b ) {
			return ( (int) get_post_meta( $a->ID, 'tbc_price_vnd', true ) ) <=> ( (int) get_post_meta( $b->ID, 'tbc_price_vnd', true ) );
		} );
		$this->assertCount( 2, $vehicles );
		$this->assertSame( 'Honda Wave 110cc', $vehicles[0]->post_title );
		$this->assertSame( '350000', (string) get_post_meta( $vehicles[0]->ID, 'tbc_price_vnd', true ) );

		// Verify starting price sync
		$tour_price_from = get_post_meta( $this->tour_id, 'tbc_price_from_vnd', true );
		$this->assertSame( '350000', (string) $tour_price_from );

		// Verify Accommodation
		$acc = get_posts( array(
			'post_type'      => 'accommodation',
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $this->tour_id,
			'posts_per_page' => -1,
		) );
		$this->assertCount( 1, $acc );
		$this->assertSame( 'Ethnic Stilt Homestay', $acc[0]->post_title );

		// Verify Transfer
		$transfer = get_posts( array(
			'post_type'      => 'transfer_option',
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $this->tour_id,
			'posts_per_page' => -1,
		) );
		$this->assertCount( 1, $transfer );
		$this->assertSame( 'to', get_post_meta( $transfer[0]->ID, 'tbc_direction', true ) );

		// Verify Add-on
		$addons = get_posts( array(
			'post_type'      => 'addon',
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $this->tour_id,
			'posts_per_page' => -1,
		) );
		$this->assertCount( 1, $addons );
		$this->assertSame( 'Full Coverage Travel Insurance', $addons[0]->post_title );

		// Verify Availability Rule
		$avail = get_posts( array(
			'post_type'      => 'availability_rule',
			'meta_key'       => 'tbc_tour_id',
			'meta_value'     => $this->tour_id,
			'posts_per_page' => -1,
		) );
		$this->assertCount( 1, $avail );
		$this->assertSame( '2026-09-01', get_post_meta( $avail[0]->ID, 'tbc_date', true ) );
	}

	public function test_save_updates_and_deletes_child_posts() {
		wp_set_current_user( $this->admin_id );

		// 1. Create initial day
		$day_id = $this->factory->post->create( array(
			'post_type'    => 'itinerary_day',
			'post_title'   => 'Old Title Day 1',
			'post_content' => 'Old content',
		) );
		update_post_meta( $day_id, 'tbc_tour_id', $this->tour_id );
		update_post_meta( $day_id, 'tbc_day_number', 1 );

		// 2. Create another day to delete
		$day2_id = $this->factory->post->create( array(
			'post_type'    => 'itinerary_day',
			'post_title'   => 'Day to Delete',
			'post_content' => 'Will be removed',
		) );
		update_post_meta( $day2_id, 'tbc_tour_id', $this->tour_id );
		update_post_meta( $day2_id, 'tbc_day_number', 2 );

		$_POST = array(
			'tbc_tour_editor_nonce' => wp_create_nonce( 'tbc_save_tour_editor' ),
			'tbc_itinerary'         => array(
				0 => array(
					'post_id'     => $day_id,
					'day_number'  => 1,
					'title'       => 'Updated Title Day 1',
					'description' => 'Updated content',
					'included'    => 'Updated included',
					'excluded'    => 'Updated excluded',
					'delete'      => 0,
				),
				1 => array(
					'post_id'     => $day2_id,
					'day_number'  => 2,
					'title'       => 'Day to Delete',
					'description' => 'Will be removed',
					'delete'      => 1,
				),
			),
		);

		Tbc_Tour_Editor::save( $this->tour_id );

		// Day 1 must be updated
		$updated_day = get_post( $day_id );
		$this->assertSame( 'Updated Title Day 1', $updated_day->post_title );
		$this->assertSame( 'Updated content', $updated_day->post_content );
		$this->assertSame( 'Updated included', get_post_meta( $day_id, 'tbc_included', true ) );

		// Day 2 must be deleted
		$this->assertNull( get_post( $day2_id ) );
	}

	public function test_price_cannot_be_edited_without_capability() {
		// User without edit_tbc_prices
		wp_set_current_user( $this->editor_id );

		$vehicle_id = $this->factory->post->create( array(
			'post_type'   => 'vehicle_option',
			'post_title'  => 'Standard Motorbike',
			'post_status' => 'publish',
		) );
		update_post_meta( $vehicle_id, 'tbc_tour_id', $this->tour_id );
		update_post_meta( $vehicle_id, 'tbc_price_vnd', 350000 );

		$_POST = array(
			'tbc_tour_editor_nonce' => wp_create_nonce( 'tbc_save_tour_editor' ),
			'tbc_vehicles'          => array(
				0 => array(
					'post_id'      => $vehicle_id,
					'title'        => 'Standard Motorbike Renamed',
					'vehicle_type' => 'motorbike',
					'price_vnd'    => 50000, // Unauthorized price change attempt
					'capacity'     => 2,
					'delete'       => 0,
				),
			),
		);

		Tbc_Tour_Editor::save( $this->tour_id );

		$updated = get_post( $vehicle_id );
		$this->assertSame( 'Standard Motorbike Renamed', $updated->post_title );
		// Price must remain 350000, NOT 50000
		$this->assertSame( '350000', (string) get_post_meta( $vehicle_id, 'tbc_price_vnd', true ) );
	}

	public function test_save_aborts_on_invalid_nonce() {
		wp_set_current_user( $this->admin_id );

		$_POST = array(
			'tbc_tour_editor_nonce' => 'invalid_nonce',
			'tbc_itinerary'         => array(
				0 => array(
					'post_id'     => 0,
					'day_number'  => 1,
					'title'       => 'Hacker Day',
					'description' => 'Should not be inserted',
					'delete'      => 0,
				),
			),
		);

		Tbc_Tour_Editor::save( $this->tour_id );

		$days = get_posts( array(
			'post_type'  => 'itinerary_day',
			'meta_key'   => 'tbc_tour_id',
			'meta_value' => $this->tour_id,
		) );
		$this->assertEmpty( $days );
	}
}
