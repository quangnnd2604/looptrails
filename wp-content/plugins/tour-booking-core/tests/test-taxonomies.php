<?php
class Test_Taxonomies extends WP_UnitTestCase {

	public function test_all_taxonomies_registered() {
		foreach ( array( 'tour_type', 'destination_region', 'tour_duration', 'tour_feature' ) as $taxonomy ) {
			$this->assertTrue( taxonomy_exists( $taxonomy ), "Missing taxonomy: {$taxonomy}" );
		}
	}

	public function test_tour_type_attached_to_tour() {
		$this->assertTrue( is_object_in_taxonomy( 'tour', 'tour_type' ) );
	}

	public function test_destination_region_is_hierarchical_and_shared() {
		$taxonomy = get_taxonomy( 'destination_region' );

		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertTrue( is_object_in_taxonomy( 'tour', 'destination_region' ) );
		$this->assertTrue( is_object_in_taxonomy( 'destination', 'destination_region' ) );
	}

	public function test_taxonomies_are_rest_enabled() {
		foreach ( array( 'tour_type', 'destination_region', 'tour_duration', 'tour_feature' ) as $taxonomy ) {
			$this->assertTrue( get_taxonomy( $taxonomy )->show_in_rest, "Not REST-enabled: {$taxonomy}" );
		}
	}
}
