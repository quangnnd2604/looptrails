<?php
/**
 * Test Milestone 6 Secondary Templates.
 */

class Test_Secondary_Templates extends WP_UnitTestCase {

	public function test_all_secondary_templates_exist_and_have_main_landmarks() {
		$templates = array(
			'single-tour.html'           => array( 'tour-detail-hero', 'itinerary-timeline', 'tour-booking-sidebar-card' ),
			'archive-tour.html'          => array( 'archive-hero-banner', 'tour_featured_grid' ),
			'page-motorbike-rental.html' => array( 'rental-hero-banner', 'rental-bikes' ),
			'single.html'                => array( 'wp:post-title', 'wp:post-content' ),
			'archive.html'               => array( 'wp:query-title', 'wp:query' ),
			'search.html'                => array( 'wp:query-title', 'wp:search' ),
			'page-contact.html'          => array( 'contact-hero-banner', 'contact-form' ),
			'page-about.html'            => array( 'about-hero-banner', 'brand-narrative' ),
			'page.html'                  => array( 'wp:post-title', 'wp:post-content' ),
			'404.html'                   => array( '404', 'wp:search' ),
		);

		foreach ( $templates as $file => $expected_strings ) {
			$path = TOUR_THEME_DIR . '/templates/' . $file;
			$this->assertFileExists( $path, "Template {$file} must exist." );

			$content = file_get_contents( $path );

			$this->assertStringContainsString( 'wp:template-part {"slug":"header"', $content, "Template {$file} must wire header." );
			$this->assertStringContainsString( 'wp:template-part {"slug":"footer"', $content, "Template {$file} must wire footer." );
			$this->assertStringContainsString( 'tagName":"main"', $content, "Template {$file} must have tagName:main attribute." );
			$this->assertStringContainsString( '<main', $content, "Template {$file} must render <main> tag." );
			$this->assertStringContainsString( '</main>', $content, "Template {$file} must close </main> tag." );

			foreach ( $expected_strings as $str ) {
				$this->assertStringContainsString( $str, $content, "Template {$file} must contain '{$str}'." );
			}
		}
	}

	public function test_rental_bikes_pattern_renders_via_do_blocks() {
		$file = TOUR_THEME_DIR . '/patterns/rental-bikes.php';
		$this->assertFileExists( $file );

		ob_start();
		include $file;
		$markup = ob_get_clean();

		$rendered = do_blocks( $markup );
		$this->assertNotEmpty( $rendered );
		$this->assertStringContainsString( 'rental-bikes-grid', $rendered );
		$this->assertStringContainsString( 'Honda Wave', $rendered );
	}
}
