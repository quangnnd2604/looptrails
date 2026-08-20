<?php
class Test_Demo_Importer extends WP_UnitTestCase {

	public function test_import_creates_six_tours_per_language() {
		$counts = Tbc_Demo_Importer::import();

		$this->assertSame( 12, $counts['tour'] );
	}

	public function test_import_marks_every_tour_as_demo() {
		Tbc_Demo_Importer::import();

		$tours = get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1 ) );
		$this->assertNotEmpty( $tours );

		foreach ( $tours as $tour ) {
			$this->assertTrue( (bool) get_post_meta( $tour->ID, 'tbc_is_demo', true ) );
		}
	}

	public function test_import_creates_both_languages_for_each_tour() {
		Tbc_Demo_Importer::import();

		$en_count = count(
			get_posts(
				array(
					'post_type'      => 'tour',
					'meta_key'       => 'tbc_lang',
					'meta_value'     => 'en',
					'posts_per_page' => -1,
				)
			)
		);
		$vi_count = count(
			get_posts(
				array(
					'post_type'      => 'tour',
					'meta_key'       => 'tbc_lang',
					'meta_value'     => 'vi',
					'posts_per_page' => -1,
				)
			)
		);

		$this->assertSame( 6, $en_count );
		$this->assertSame( 6, $vi_count );
	}

	public function test_import_creates_related_records_for_every_tour() {
		$counts = Tbc_Demo_Importer::import();

		// Operational records are created once per EN/VI tour pair (6 pairs),
		// translatable content once per language tour (12 tours) — spec §6
		// forbids duplicating operational values between translations.
		$this->assertSame( 6, $counts['destination'] );
		$this->assertSame( 12, $counts['vehicle_option'] );
		$this->assertSame( 6, $counts['accommodation'] );
		$this->assertSame( 12, $counts['transfer_option'] );
		$this->assertSame( 6, $counts['addon'] );
		$this->assertSame( 30, $counts['availability_rule'] );

		$this->assertSame( 24, $counts['itinerary_day'] );
		$this->assertSame( 12, $counts['testimonial'] );
		$this->assertSame( 24, $counts['faq'] );
		$this->assertSame( 1, $counts['voucher'] );
	}

	public function test_translation_pairs_share_group_and_operational_records() {
		Tbc_Demo_Importer::import();

		$tours = get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1 ) );
		$this->assertCount( 12, $tours );

		$groups = array();
		foreach ( $tours as $tour ) {
			$group = get_post_meta( $tour->ID, 'tbc_translation_group', true );
			$this->assertNotEmpty( $group );
			$groups[ $group ][ get_post_meta( $tour->ID, 'tbc_lang', true ) ] = $tour->ID;
		}

		// Six pairs, each holding exactly one EN and one VI tour.
		$this->assertCount( 6, $groups );

		foreach ( $groups as $group => $pair ) {
			$this->assertArrayHasKey( 'en', $pair, "Group {$group} has no EN tour" );
			$this->assertArrayHasKey( 'vi', $pair, "Group {$group} has no VI tour" );

			$en_destination = get_post_meta( $pair['en'], 'tbc_destination_id', true );
			$vi_destination = get_post_meta( $pair['vi'], 'tbc_destination_id', true );

			$this->assertNotEmpty( $en_destination );
			// Same destination row, not a duplicate with identical values.
			$this->assertSame( $en_destination, $vi_destination );
		}
	}

	public function test_itinerary_days_are_numbered_from_one() {
		Tbc_Demo_Importer::import();

		$days = get_posts( array( 'post_type' => 'itinerary_day', 'posts_per_page' => -1 ) );
		$this->assertNotEmpty( $days );

		foreach ( $days as $day ) {
			$number = (int) get_post_meta( $day->ID, 'tbc_day_number', true );
			$this->assertGreaterThanOrEqual( 1, $number );
			$this->assertStringContainsString( (string) $number, $day->post_title );
		}
	}

	public function test_import_is_idempotent() {
		$first  = Tbc_Demo_Importer::import();
		$second = Tbc_Demo_Importer::import();

		$this->assertSame( 12, $first['tour'] );
		$this->assertSame( array_fill_keys( array_keys( $first ), 0 ), $second );

		$tours = get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1 ) );
		$this->assertCount( 12, $tours );
	}

	public function test_removal_allows_a_fresh_import() {
		Tbc_Demo_Importer::import();
		Tbc_Demo_Remover::remove();

		$counts = Tbc_Demo_Importer::import();

		$this->assertSame( 12, $counts['tour'] );
	}

	public function test_import_covers_all_availability_states() {
		Tbc_Demo_Importer::import();

		$states = wp_list_pluck(
			array_map(
				function ( $post ) {
					return array( 'tbc_state' => get_post_meta( $post->ID, 'tbc_state', true ) );
				},
				get_posts( array( 'post_type' => 'availability_rule', 'posts_per_page' => -1 ) )
			),
			'tbc_state'
		);

		foreach ( array( 'available', 'limited', 'full', 'blocked' ) as $state ) {
			$this->assertContains( $state, $states, "Missing availability state: {$state}" );
		}
	}
}
