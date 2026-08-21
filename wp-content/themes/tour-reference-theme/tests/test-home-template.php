<?php
/**
 * Test Home and Front Page template files.
 */

class Test_Home_Template extends WP_UnitTestCase {

	public function test_front_page_template_exists_and_wires_sections() {
		$template_file = TOUR_THEME_DIR . '/templates/front-page.html';
		$this->assertFileExists( $template_file );

		$content = file_get_contents( $template_file );
		$this->assertStringContainsString( 'wp:template-part {"slug":"header"', $content );
		$this->assertStringContainsString( 'wp:template-part {"slug":"footer"', $content );
		$this->assertStringContainsString( 'tagName":"main"', $content );
		$this->assertStringContainsString( '<main', $content );
		$this->assertStringContainsString( '</main>', $content );

		$this->assertStringContainsString( 'tour-reference-theme/hero-home', $content );
		$this->assertStringContainsString( 'tour-reference-theme/featured-tours', $content );
		$this->assertStringContainsString( 'tour-reference-theme/brand-narrative', $content );
		$this->assertStringContainsString( 'tour-reference-theme/top-destinations-essentials', $content );
		$this->assertStringContainsString( 'tour-reference-theme/why-choose-us', $content );
		$this->assertStringContainsString( 'tour-reference-theme/testimonials', $content );
		$this->assertStringContainsString( 'tour-reference-theme/editorial-cta', $content );
		$this->assertStringContainsString( 'tour-reference-theme/faq-accordion', $content );
	}

	public function test_home_template_exists_and_wires_sections() {
		$template_file = TOUR_THEME_DIR . '/templates/home.html';
		$this->assertFileExists( $template_file );

		$content = file_get_contents( $template_file );
		$this->assertStringContainsString( 'tour-reference-theme/hero-home', $content );
		$this->assertStringContainsString( 'tagName":"main"', $content );
	}
}
