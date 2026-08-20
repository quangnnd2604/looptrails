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

		$this->assertSame( 12, $counts['destination'] );
		$this->assertSame( 24, $counts['itinerary_day'] );
		$this->assertSame( 24, $counts['vehicle_option'] );
		$this->assertSame( 12, $counts['accommodation'] );
		$this->assertSame( 24, $counts['transfer_option'] );
		$this->assertSame( 12, $counts['addon'] );
		$this->assertSame( 12, $counts['testimonial'] );
		$this->assertSame( 24, $counts['faq'] );
		$this->assertSame( 60, $counts['availability_rule'] );
		$this->assertSame( 1, $counts['voucher'] );
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
