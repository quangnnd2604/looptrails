<?php
class Test_Header_Part extends WP_UnitTestCase {

	private function header_markup() {
		return file_get_contents( TOUR_THEME_DIR . '/parts/header.html' );
	}

	public function test_header_part_file_exists() {
		$this->assertFileExists( TOUR_THEME_DIR . '/parts/header.html' );
	}

	public function test_header_contains_navigation_block_with_always_overlay() {
		$this->assertStringContainsString( 'wp:navigation', $this->header_markup() );
		$this->assertStringContainsString( '"overlayMenu":"always"', $this->header_markup() );
	}

	public function test_header_contains_site_logo_block() {
		$this->assertStringContainsString( 'wp:site-logo', $this->header_markup() );
	}

	public function test_header_contains_book_now_button() {
		$this->assertStringContainsString( 'is-style-book-now', $this->header_markup() );
		$this->assertStringContainsString( 'Book Now', $this->header_markup() );
	}

	public function test_book_now_style_is_registered() {
		$styles = WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( 'core/button' );
		$this->assertArrayHasKey( 'book-now', $styles );
	}

	public function test_header_declares_site_title_fallback() {
		// Spec §5.1 requires an accessible site-name fallback beside the logo,
		// which also keeps the space-between group at two children when no
		// custom_logo theme mod is set (core/site-logo then renders nothing).
		$this->assertStringContainsString( 'wp:site-title', $this->header_markup() );
	}

	public function test_header_navigation_overlay_uses_theme_tokens() {
		// Guards against falling back to core's #fff/#000 overlay defaults.
		$this->assertStringContainsString( '"overlayBackgroundColor":"surface-header-footer"', $this->header_markup() );
		$this->assertStringContainsString( '"overlayTextColor":"text-body"', $this->header_markup() );
	}

	/*
	 * Rendering assertions (do_blocks) -- the string checks above pass
	 * identically whether the markup renders correctly, renders empty, or
	 * renders with the wrong colors. Three real bugs in this milestone (the
	 * self-referential template-part recursion, the iconColor attribute, and
	 * the missing <main> landmark) were all invisible to string checks.
	 */

	public function test_header_part_renders_non_empty_content() {
		$html = do_blocks( $this->header_markup() );

		$this->assertStringContainsString( '<nav', $html );
		$this->assertStringContainsString( 'Book Now', $html );
	}

	public function test_header_part_renders_site_title_element() {
		$html = do_blocks( $this->header_markup() );

		$this->assertStringContainsString( 'wp-block-site-title', $html );
		$this->assertStringContainsString( get_bloginfo( 'name' ), $html );
	}

	public function test_header_part_is_registered_in_the_header_area() {
		// Guards theme.json's templateParts declaration: without it core
		// classifies every part as WP_TEMPLATE_PART_AREA_UNCATEGORIZED.
		$parts = get_block_templates( array( 'area' => 'header' ), 'wp_template_part' );
		$slugs = wp_list_pluck( $parts, 'slug' );

		$this->assertContains( 'header', $slugs, 'Header part should be classified in the "header" template-part area' );
	}
}
