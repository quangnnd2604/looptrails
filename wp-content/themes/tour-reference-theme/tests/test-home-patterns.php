<?php
/**
 * Test Home page block patterns.
 */

class Test_Home_Patterns extends WP_UnitTestCase {

	public function test_all_home_patterns_exist_and_render() {
		$patterns = array(
			'hero-home'                  => array( 'Experience The Real Vietnam', 'hero-headline' ),
			'featured-tours'             => array( 'Popular Loop Tours', 'tour-grid' ),
			'brand-narrative'            => array( 'Pioneering Northern Vietnam Loops', 'narrative-stats-grid' ),
			'top-destinations-essentials'=> array( 'Top Destinations', 'destinations-grid' ),
			'why-choose-us'              => array( 'Why Ride Loop Trails', 'dark-stats-bar' ),
			'testimonials'               => array( 'Loved by Adventurers Worldwide', 'reviews-grid' ),
			'editorial-cta'              => array( 'Ready to Conquer the Northern Loop?', 'editorial-cta-card' ),
			'booking-section'            => array( 'Book Your Ha Giang Adventure', 'lt-booking-form' ),
			'blog-teaser'                => array( 'Essential Travel Articles', 'blog-teaser-grid' ),
			'faq-accordion'              => array( 'Frequently Asked Questions', 'faq-accordion' ),
		);

		foreach ( $patterns as $slug => $expected_strings ) {
			$file = TOUR_THEME_DIR . '/patterns/' . $slug . '.php';
			$this->assertFileExists( $file, "Pattern file {$slug}.php must exist." );

			ob_start();
			include $file;
			$markup = ob_get_clean();

			$rendered = do_shortcode( do_blocks( $markup ) );
			$this->assertNotEmpty( $rendered, "Pattern {$slug} must render non-empty HTML." );

			foreach ( $expected_strings as $str ) {
				$this->assertStringContainsString( $str, $rendered, "Pattern {$slug} must contain '{$str}'." );
			}
		}
	}

	public function test_faq_accordion_uses_native_details_elements() {
		ob_start();
		include TOUR_THEME_DIR . '/patterns/faq-accordion.php';
		$markup = ob_get_clean();

		$this->assertStringContainsString( '<details', $markup );
		$this->assertStringContainsString( '<summary', $markup );
	}
}
