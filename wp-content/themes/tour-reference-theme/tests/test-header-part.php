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
}
