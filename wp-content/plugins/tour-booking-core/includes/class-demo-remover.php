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
					// 'any' silently excludes statuses flagged exclude_from_search,
					// which includes 'trash' — a trashed demo post would survive
					// removal. List the statuses explicitly instead.
					'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
					'meta_key'       => 'tbc_is_demo',
					'meta_value'     => '1',
				)
			);

			foreach ( $posts as $post ) {
				// Force-delete (bypassing trash) is a deliberate exception for
				// tbc_is_demo-marked scratch content only: the admin UI gates this
				// behind a confirm dialog, and the tbc_is_demo meta filter above is
				// what limits this method to demo records in the first place.
				// Any future code deleting real operational records (booking,
				// voucher) MUST use wp_trash_post() instead, per spec §6's
				// audit-trail requirement. This method is not a template for
				// deleting non-demo content.
				wp_delete_post( $post->ID, true );
				++$deleted;
			}
		}

		// Clear the importer's idempotency guard so a fresh import is possible.
		delete_option( Tbc_Demo_Importer::IMPORTED_OPTION );

		return $deleted;
	}

	public static function cli_remove() {
		$deleted = self::remove();
		WP_CLI::success( sprintf( 'Removed %d demo records.', $deleted ) );
	}
}
