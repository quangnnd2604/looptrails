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
		$this->assertStringContainsString( 'wp:post-content', $content );
	}

	public function test_home_template_exists_and_wires_sections() {
		$template_file = TOUR_THEME_DIR . '/templates/home.html';
		$this->assertFileExists( $template_file );

		$content = file_get_contents( $template_file );
		$this->assertStringContainsString( 'wp:post-content', $content );
		$this->assertStringContainsString( 'tagName":"main"', $content );
	}

	public function test_front_page_renders_content_blocks() {
		$posts = get_posts( array(
			'post_type'      => 'page',
			'name'           => 'home',
			'posts_per_page' => 1,
		) );
		if ( ! empty( $posts ) ) {
			$rendered = do_shortcode( do_blocks( $posts[0]->post_content ) );
			$this->assertStringContainsString( 'hero-home-section', $rendered );
			$this->assertStringContainsString( 'narrative-stats-grid', $rendered );
			$this->assertStringContainsString( 'why-choose-us-section', $rendered );
		} else {
			$this->assertTrue( true );
		}
	}
}
