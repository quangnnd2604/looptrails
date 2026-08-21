<?php
/**
 * Registers post meta for every custom post type from a single schema array.
 * Fields marked is_price require the edit_tbc_prices capability to write,
 * on top of the post type's normal edit capability.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Meta_Fields {

	public static function get_schema() {
		return array(
			'tour'              => array(
				'tbc_duration_days'     => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_duration_nights'   => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_badge'             => array( 'type' => 'string', 'is_price' => false ),
				'tbc_rating_value'      => array( 'type' => 'number', 'is_price' => false ),
				'tbc_rating_count'      => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_price_from_vnd'    => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_destination_id'    => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_lang'              => array( 'type' => 'string', 'is_price' => false ),
				'tbc_translation_group' => array( 'type' => 'string', 'is_price' => false ),
				'tbc_is_demo'           => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'destination'       => array(
				'tbc_region'  => array( 'type' => 'string', 'is_price' => false ),
				'tbc_is_demo' => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'itinerary_day'     => array(
				'tbc_tour_id'    => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_day_number' => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_included'   => array( 'type' => 'string', 'is_price' => false ),
				'tbc_excluded'   => array( 'type' => 'string', 'is_price' => false ),
				'tbc_is_demo'    => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'vehicle_option'    => array(
				'tbc_tour_id'      => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_vehicle_type' => array( 'type' => 'string', 'is_price' => false ),
				'tbc_price_vnd'    => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_capacity'     => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_is_demo'      => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'accommodation'     => array(
				'tbc_tour_id'   => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_price_vnd' => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_upgrade'   => array( 'type' => 'boolean', 'is_price' => false ),
				'tbc_is_demo'   => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'transfer_option'   => array(
				'tbc_tour_id'   => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_direction' => array( 'type' => 'string', 'is_price' => false ),
				'tbc_price_vnd' => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_is_demo'   => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'addon'             => array(
				'tbc_tour_id'   => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_price_vnd' => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_is_demo'   => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'testimonial'       => array(
				'tbc_rating_value' => array( 'type' => 'number', 'is_price' => false ),
				'tbc_author_name'  => array( 'type' => 'string', 'is_price' => false ),
				'tbc_tour_id'      => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_is_demo'      => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'faq'               => array(
				'tbc_tour_id' => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_is_demo' => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'booking'           => array(
				'tbc_booking_code'   => array( 'type' => 'string', 'is_price' => false ),
				'tbc_status'         => array( 'type' => 'string', 'is_price' => false ),
				'tbc_tour_id'        => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_total_vnd'      => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_currency'       => array( 'type' => 'string', 'is_price' => false ),
				'tbc_exchange_rate'  => array( 'type' => 'number', 'is_price' => true ),
				'tbc_customer_email' => array( 'type' => 'string', 'is_price' => false ),
				'tbc_customer_name'  => array( 'type' => 'string', 'is_price' => false ),
				'tbc_is_demo'        => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'voucher'           => array(
				'tbc_code'          => array( 'type' => 'string', 'is_price' => false ),
				'tbc_voucher_type'  => array( 'type' => 'string', 'is_price' => false ),
				'tbc_amount'        => array( 'type' => 'number', 'is_price' => true ),
				'tbc_valid_from'    => array( 'type' => 'string', 'is_price' => false ),
				'tbc_valid_to'      => array( 'type' => 'string', 'is_price' => false ),
				'tbc_usage_limit'   => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_used_count'    => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_min_spend_vnd' => array( 'type' => 'integer', 'is_price' => true ),
				'tbc_is_demo'       => array( 'type' => 'boolean', 'is_price' => false ),
			),
			'availability_rule' => array(
				'tbc_tour_id'  => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_date'     => array( 'type' => 'string', 'is_price' => false ),
				'tbc_state'    => array( 'type' => 'string', 'is_price' => false ),
				'tbc_capacity' => array( 'type' => 'integer', 'is_price' => false ),
				'tbc_is_demo'  => array( 'type' => 'boolean', 'is_price' => false ),
			),
		);
	}

	public static function register() {
		/*
		 * Meta is registered with show_in_rest => true in preparation for future
		 * REST / block-editor exposure, but NO post type in this plugin currently
		 * declares 'custom-fields' in its supports array — and WordPress only
		 * actually exposes registered post meta over REST for post types that do.
		 * So as of this milestone the flag is inert and the price auth_callback
		 * gating below has no live REST-path coverage yet.
		 *
		 * This is intentional and deferred, not an oversight: deciding which post
		 * types need 'custom-fields' support is a UI/consumer decision that belongs
		 * to whichever later milestone builds the actual meta-editing UI (Gutenberg
		 * custom-fields panel or a REST-based admin screen), not to this data-model
		 * milestone.
		 */
		foreach ( self::get_schema() as $post_type => $fields ) {
			foreach ( $fields as $meta_key => $config ) {
				register_post_meta(
					$post_type,
					$meta_key,
					array(
						'type'          => $config['type'],
						'single'        => true,
						'show_in_rest'  => true,
						'auth_callback' => self::build_auth_callback( $post_type, $config['is_price'] ),
					)
				);
			}
		}
	}

	/**
	 * Builds the auth_callback closure for one post type / field kind.
	 *
	 * WordPress invokes this as the `auth_{$object_type}_meta_{$meta_key}[_for_{$subtype}]`
	 * filter with six arguments — ( $allowed, $meta_key, $object_id, $user_id, $cap, $caps )
	 * (see map_meta_cap() in wp-includes/capabilities.php). The 4th argument is the ID of
	 * the user being asked about, which is NOT necessarily the logged-in user: user_can()
	 * routes through the same filter for an arbitrary user. So the check must use
	 * user_can( $user_id, ... ) rather than current_user_can( ... ), or a permission
	 * question about another user would be silently answered for the current one.
	 */
	private static function build_auth_callback( $post_type, $is_price ) {
		return function ( $allowed, $meta_key, $post_id, $user_id = 0 ) use ( $post_type, $is_price ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object || ! user_can( $user_id, $post_type_object->cap->edit_post, $post_id ) ) {
				return false;
			}

			if ( $is_price && ! user_can( $user_id, 'edit_tbc_prices' ) ) {
				return false;
			}

			return true;
		};
	}
}
