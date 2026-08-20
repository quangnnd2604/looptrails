<?php
/**
 * Theme bootstrap: supports, self-hosted fonts. No business logic — see tour-booking-core.
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
	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'tour-reference-theme' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'tour_theme_enqueue_fonts' );
function tour_theme_enqueue_fonts() {
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

	wp_enqueue_style( 'tour-theme-fonts', TOUR_THEME_URI . '/assets/css/theme.css', array(), '0.1.0' );
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
