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

	public function test_theme_json_opts_out_of_core_default_presets() {
		// A native v3 theme.json does NOT get the v2->v3 migration's automatic
		// opt-outs, so core's palette/font-size/spacing/shadow presets would
		// otherwise merge in alongside the measured tokens.
		$this->assertFalse( wp_get_global_settings( array( 'color', 'defaultPalette' ) ) );
		$this->assertFalse( wp_get_global_settings( array( 'typography', 'defaultFontSizes' ) ) );
		$this->assertFalse( wp_get_global_settings( array( 'spacing', 'defaultSpacingSizes' ) ) );
		$this->assertFalse( wp_get_global_settings( array( 'shadow', 'defaultPresets' ) ) );
	}

	public function test_every_font_family_declares_font_faces_that_exist_on_disk() {
		$families = wp_get_global_settings( array( 'typography', 'fontFamilies', 'theme' ) );
		$this->assertNotEmpty( $families );

		$face_count = 0;
		foreach ( $families as $family ) {
			$this->assertArrayHasKey( 'fontFace', $family, "No fontFace for family: {$family['slug']}" );
			foreach ( $family['fontFace'] as $face ) {
				foreach ( (array) $face['src'] as $src ) {
					$relative = str_replace( 'file:./', '', $src );
					$this->assertFileExists( TOUR_THEME_DIR . '/' . $relative, "Declared font face missing on disk: {$src}" );
					++$face_count;
				}
			}
		}

		$this->assertSame( 8, $face_count, 'Expected exactly 8 self-hosted woff2 faces declared in theme.json' );
	}

	public function test_theme_css_no_longer_hand_rolls_font_face() {
		// The faces belong in theme.json so the Site Editor and Font Library
		// see them too; a stray @font-face here would silently duplicate them.
		$css = file_get_contents( TOUR_THEME_DIR . '/assets/css/theme.css' );
		$this->assertStringNotContainsString( '@font-face', $css );
	}

	public function test_theme_declares_template_part_areas() {
		$theme_json = json_decode( file_get_contents( TOUR_THEME_DIR . '/theme.json' ), true );
		$areas      = wp_list_pluck( $theme_json['templateParts'] ?? array(), 'area', 'name' );

		$this->assertSame( 'header', $areas['header'] ?? null );
		$this->assertSame( 'footer', $areas['footer'] ?? null );
	}

	public function test_editor_styles_are_registered() {
		$this->assertTrue( current_theme_supports( 'editor-styles' ) );
		$this->assertContains( 'assets/css/theme.css', (array) ( $GLOBALS['editor_styles'] ?? array() ) );
	}
}
