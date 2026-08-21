<?php
/**
 * Dynamic Tour Filtering & Search Engine
 *
 * Implements Spec §6, §13.8.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Search_Filter {

	/**
	 * Query tours based on dynamic filter arguments.
	 *
	 * @param array $args Filter arguments (duration_days, max_price, region).
	 * @return WP_Query
	 */
	public static function filter_tours( $args = array() ) {
		$query_args = array(
			'post_type'      => 'tour',
			'post_status'    => 'publish',
			'posts_per_page' => isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 12,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		);

		$meta_query = array();

		if ( ! empty( $args['duration_days'] ) ) {
			$meta_query[] = array(
				'key'     => 'tbc_duration_days',
				'value'   => absint( $args['duration_days'] ),
				'compare' => '=',
				'type'    => 'NUMERIC',
			);
		}

		if ( ! empty( $args['max_price_usd'] ) ) {
			$meta_query[] = array(
				'key'     => 'tbc_price_from_usd',
				'value'   => floatval( $args['max_price_usd'] ),
				'compare' => '<=',
				'type'    => 'DECIMAL(10,2)',
			);
		}

		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		return new WP_Query( $query_args );
	}
}
