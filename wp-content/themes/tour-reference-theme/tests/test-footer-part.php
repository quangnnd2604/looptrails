<?php
class Test_Footer_Part extends WP_UnitTestCase {

	private function footer_markup() {
		return file_get_contents( TOUR_THEME_DIR . '/parts/footer.html' );
	}

	public function test_footer_part_file_exists() {
		$this->assertFileExists( TOUR_THEME_DIR . '/parts/footer.html' );
	}

	public function test_footer_contains_social_links() {
		foreach ( array( 'facebook', 'instagram', 'whatsapp', 'tiktok' ) as $service ) {
			$this->assertStringContainsString( '"service":"' . $service . '"', $this->footer_markup() );
		}
	}

	public function test_footer_contains_copyright_line() {
		$this->assertStringContainsString( 'All rights reserved', $this->footer_markup() );
	}

	public function test_footer_uses_footer_legal_font() {
		$this->assertStringContainsString( 'fontFamily":"footer-legal"', $this->footer_markup() );
	}
}
