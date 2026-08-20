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

	private static function build_auth_callback( $post_type, $is_price ) {
		return function ( $allowed, $meta_key, $post_id ) use ( $post_type, $is_price ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) {
				return false;
			}

			if ( $is_price && ! current_user_can( 'edit_tbc_prices' ) ) {
				return false;
			}

			return true;
		};
	}
}
