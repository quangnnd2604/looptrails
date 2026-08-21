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

	public function test_footer_part_renders_non_empty_content() {
		$html = do_blocks( $this->footer_markup() );

		$this->assertStringContainsString( 'wp-block-social-links', $html );
		$this->assertStringContainsString( 'All rights reserved', $html );
	}

	public function test_footer_renders_every_declared_social_network() {
		$html = do_blocks( $this->footer_markup() );

		foreach ( array( 'facebook', 'instagram', 'whatsapp', 'tiktok' ) as $service ) {
			$this->assertStringContainsString( 'wp-social-link-' . $service, $html, "Social link not rendered: {$service}" );
		}
	}

	public function test_footer_social_links_do_not_force_a_single_icon_color() {
		// Regression guard for the iconColor bug: an iconColor attribute on
		// wp:social-links paints every network the same color, defeating the
		// per-network brand tokens in assets/css/theme.css.
		$this->assertStringNotContainsString( 'iconColor', $this->footer_markup() );
	}

	public function test_footer_declared_styles_match_emitted_markup() {
		$markup = $this->footer_markup();

		// A declared attribute with no matching emitted style/class makes the
		// Site Editor flag the block as invalid on load.
		$this->assertStringContainsString( '"fontSize":"body"', $markup );
		$this->assertStringContainsString( 'has-body-font-size', $markup );
		$this->assertStringContainsString( 'padding-bottom:0', $markup );
	}
}
