<?php
/**
 * Safe remover for demo content: deletes only posts stamped tbc_is_demo,
 * across every post type the importer can create.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Demo_Remover {

	const DEMO_POST_TYPES = array(
		'tour',
		'destination',
		'itinerary_day',
		'vehicle_option',
		'accommodation',
		'transfer_option',
		'addon',
		'testimonial',
		'faq',
		'voucher',
		'availability_rule',
	);

	public static function remove() {
		$deleted = 0;

		foreach ( self::DEMO_POST_TYPES as $post_type ) {
			$posts = get_posts(
				array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'meta_key'       => 'tbc_is_demo',
					'meta_value'     => '1',
				)
			);

			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
				++$deleted;
			}
		}

		return $deleted;
	}

	public static function cli_remove() {
		$deleted = self::remove();
		WP_CLI::success( sprintf( 'Removed %d demo records.', $deleted ) );
	}
}
