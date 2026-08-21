<?php
/**
 * Test Tour Card rendering, shortcode, and pattern registration.
 */

class Test_Tour_Card extends WP_UnitTestCase {

	private $tour_id;

	public function set_up() {
		parent::set_up();

		$this->tour_id = wp_insert_post(
			array(
				'post_title'   => 'Epic Ha Giang 3D2N Loop',
				'post_content' => 'Tour description test',
				'post_type'    => 'tour',
				'post_status'  => 'publish',
			)
		);

		update_post_meta( $this->tour_id, 'tbc_duration_days', 3 );
		update_post_meta( $this->tour_id, 'tbc_duration_nights', 2 );
		update_post_meta( $this->tour_id, 'tbc_badge', 'Best Seller' );
		update_post_meta( $this->tour_id, 'tbc_rating_value', 4.9 );
		update_post_meta( $this->tour_id, 'tbc_rating_count', 88 );
	}

	public function test_tour_card_renders_expected_markup() {
		$html = tour_theme_render_tour_card( $this->tour_id );

		$this->assertNotEmpty( $html );
		$this->assertStringContainsString( 'tour-card', $html );
		$this->assertStringContainsString( 'Epic Ha Giang 3D2N Loop', $html );
		$this->assertStringContainsString( 'Best Seller', $html );
		$this->assertStringContainsString( '3D2N', $html );
		$this->assertStringContainsString( '4.9', $html );
		$this->assertStringContainsString( '(88)', $html );
		$this->assertStringContainsString( 'Book Now', $html );
		$this->assertStringContainsString( 'Details →', $html );
	}

	public function test_tour_featured_grid_renders_container() {
		$html = tour_theme_render_featured_tours( array( 'postsPerPage' => 3 ) );

		$this->assertNotEmpty( $html );
		$this->assertStringContainsString( 'tour-grid', $html );
		$this->assertStringContainsString( 'Epic Ha Giang 3D2N Loop', $html );
	}

	public function test_featured_tours_pattern_renders_via_do_blocks() {
		$pattern_file = TOUR_THEME_DIR . '/patterns/featured-tours.php';
		$this->assertFileExists( $pattern_file );

		ob_start();
		include $pattern_file;
		$markup = ob_get_clean();

		$rendered = do_shortcode( do_blocks( $markup ) );
		$this->assertNotEmpty( $rendered );
		$this->assertStringContainsString( 'Popular Loop Tours', $rendered );
		$this->assertStringContainsString( 'tour-grid', $rendered );
	}
}
