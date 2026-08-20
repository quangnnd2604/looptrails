<?php
/**
 * Registers the plugin's taxonomies from a single schema array.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Taxonomies {

	public static function get_schema() {
		return array(
			'tour_type'          => array(
				'object_types' => array( 'tour' ),
				'hierarchical' => false,
				'rewrite_slug' => 'tour-type',
			),
			'destination_region' => array(
				'object_types' => array( 'tour', 'destination' ),
				'hierarchical' => true,
				'rewrite_slug' => 'destination-region',
			),
			'tour_duration'      => array(
				'object_types' => array( 'tour' ),
				'hierarchical' => false,
				'rewrite_slug' => 'duration',
			),
			'tour_feature'       => array(
				'object_types' => array( 'tour' ),
				'hierarchical' => false,
				'rewrite_slug' => 'feature',
			),
		);
	}

	public static function register() {
		foreach ( self::get_schema() as $taxonomy => $args ) {
			register_taxonomy( $taxonomy, $args['object_types'], self::build_args( $taxonomy, $args ) );
		}
	}

	private static function build_args( $taxonomy, $args ) {
		return array(
			'label'        => ucwords( str_replace( '_', ' ', $taxonomy ) ),
			'hierarchical' => $args['hierarchical'],
			'public'       => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => $args['rewrite_slug'] ),
		);
	}
}
