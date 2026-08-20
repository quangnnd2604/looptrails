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
}
