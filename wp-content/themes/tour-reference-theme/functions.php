<?php
/**
 * Theme bootstrap: supports, editor styles, self-hosted font preloads.
 * No business logic — see tour-booking-core.
 *
 * The @font-face declarations themselves live in theme.json
 * (settings.typography.fontFamilies[].fontFace), so WordPress emits them for
 * both the front end and the Site Editor. This file only preloads the two
 * critical faces and enqueues the theme's component CSS.
 */

defined( 'ABSPATH' ) || exit;

define( 'TOUR_THEME_DIR', get_template_directory() );
define( 'TOUR_THEME_URI', get_template_directory_uri() );

add_action( 'after_setup_theme', 'tour_theme_supports' );
function tour_theme_supports() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// Make the Site Editor / block editor render with the same component CSS
	// (Book Now button, social icon colors, legal bar) as the front end.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/theme.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'tour-reference-theme' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'tour_theme_enqueue_assets' );
function tour_theme_enqueue_assets() {
	$fonts = array(
		'montserrat-700' => 'montserrat-latin-700-normal.woff2',
		'montserrat-800' => 'montserrat-latin-800-normal.woff2',
		'poppins-600'    => 'poppins-latin-600-normal.woff2',
		'inter-400'      => 'inter-latin-400-normal.woff2',
		'inter-600'      => 'inter-latin-600-normal.woff2',
		'inter-700'      => 'inter-latin-700-normal.woff2',
		'dm-sans-600'    => 'dm-sans-latin-600-normal.woff2',
		'open-sans-400'  => 'open-sans-latin-400-normal.woff2',
	);

	foreach ( array( 'montserrat-800', 'inter-400' ) as $critical ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( TOUR_THEME_URI . '/assets/fonts/' . $fonts[ $critical ] )
		);
	}

	// Handle is deliberately not "…-fonts": this stylesheet now carries the
	// Book Now button, social icon and legal-bar rules, not @font-face.
	wp_enqueue_style( 'tour-theme-styles', TOUR_THEME_URI . '/assets/css/theme.css', array(), '0.1.0' );
	wp_enqueue_script( 'tour-theme-tabs', TOUR_THEME_URI . '/assets/js/tabs.js', array(), '0.1.0', true );
}

add_action( 'init', 'tour_theme_register_block_styles' );
function tour_theme_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'book-now',
			'label' => __( 'Book Now (pill, hard shadow)', 'tour-reference-theme' ),
		)
	);
}

add_action( 'init', 'tour_theme_register_custom_patterns', 9 );
function tour_theme_register_custom_patterns() {
	$pattern_files = glob( TOUR_THEME_DIR . '/patterns/*.php' );
	if ( ! empty( $pattern_files ) ) {
		foreach ( $pattern_files as $file ) {
			$slug = 'tour-reference-theme/' . basename( $file, '.php' );
			if ( ! WP_Block_Patterns_Registry::get_instance()->is_registered( $slug ) ) {
				$headers = get_file_data(
					$file,
					array(
						'title'       => 'Title',
						'slug'        => 'Slug',
						'categories'  => 'Categories',
						'description' => 'Description',
					)
				);
				ob_start();
				include $file;
				$content = ob_get_clean();
				register_block_pattern(
					! empty( $headers['slug'] ) ? $headers['slug'] : $slug,
					array(
						'title'       => ! empty( $headers['title'] ) ? $headers['title'] : basename( $file, '.php' ),
						'content'     => $content,
						'description' => ! empty( $headers['description'] ) ? $headers['description'] : '',
						'categories'  => ! empty( $headers['categories'] ) ? array_map( 'trim', explode( ',', $headers['categories'] ) ) : array( 'featured' ),
					)
				);
			}
		}
	}
}

require_once TOUR_THEME_DIR . '/includes/tour-card.php';

