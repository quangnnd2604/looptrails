<?php
class Test_Theme_Setup extends WP_UnitTestCase {

	public function test_active_theme_is_tour_reference_theme() {
		$this->assertSame( 'tour-reference-theme', get_stylesheet() );
	}

	public function test_theme_json_declares_expected_color_palette() {
		$theme_json = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
		$slugs      = wp_list_pluck( $theme_json, 'slug' );

		foreach ( array( 'primary', 'ink', 'surface-header-footer', 'social-whatsapp' ) as $expected ) {
			$this->assertContains( $expected, $slugs, "Missing color token: {$expected}" );
		}
	}

	public function test_theme_json_declares_fluid_h1() {
		$sizes = wp_get_global_settings( array( 'typography', 'fontSizes', 'theme' ) );
		$h1     = current( array_filter( $sizes, fn( $s ) => 'h1' === $s['slug'] ) );

		$this->assertNotFalse( $h1 );
		$this->assertSame( '28px', $h1['fluid']['min'] );
		$this->assertSame( '60px', $h1['fluid']['max'] );
	}

	public function test_primary_nav_menu_location_is_registered() {
		$this->assertArrayHasKey( 'primary', get_registered_nav_menus() );
	}

	public function test_font_files_exist_on_disk() {
		$fonts_dir = TOUR_THEME_DIR . '/assets/fonts/';
		foreach ( array( 'montserrat-latin-800-normal.woff2', 'inter-latin-400-normal.woff2' ) as $file ) {
			$this->assertFileExists( $fonts_dir . $file, "Missing font file: {$file}" );
		}
	}
}
