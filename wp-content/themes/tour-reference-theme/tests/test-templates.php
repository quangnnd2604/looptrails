<?php
class Test_Templates extends WP_UnitTestCase {

	public function test_index_template_exists() {
		$this->assertFileExists( TOUR_THEME_DIR . '/templates/index.html' );
	}

	public function test_index_template_references_header_and_footer_parts() {
		$markup = file_get_contents( TOUR_THEME_DIR . '/templates/index.html' );

		$this->assertStringContainsString( '"slug":"header"', $markup );
		$this->assertStringContainsString( '"slug":"footer"', $markup );
	}

	public function test_block_template_is_registered() {
		$templates = get_block_templates();
		$slugs     = wp_list_pluck( $templates, 'slug' );

		$this->assertContains( 'index', $slugs, 'Index block template should be registered with WordPress block template system' );
	}

	public function test_index_template_renders_a_main_landmark() {
		// Without a <main> element core's _block_template_add_skip_link()
		// returns the template unchanged and emits no "Skip to content" link
		// at all (WCAG 2.4.1 Level A). It fails silently -- no warning, no
		// error -- so only a rendering assertion catches it.
		$html = do_blocks( file_get_contents( TOUR_THEME_DIR . '/templates/index.html' ) );

		$this->assertStringContainsString( '<main', $html );
	}

	public function test_footer_part_is_registered_in_the_footer_area() {
		$parts = get_block_templates( array( 'area' => 'footer' ), 'wp_template_part' );
		$slugs = wp_list_pluck( $parts, 'slug' );

		$this->assertContains( 'footer', $slugs, 'Footer part should be classified in the "footer" template-part area' );
	}
}
