<?php
/**
 * Registers the plugin's custom post types from a single schema array.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Post_Types {

	public static function get_schema() {
		return array(
			'tour'               => array(
				'public'       => true,
				'has_archive'  => true,
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'menu_icon'    => 'dashicons-palmtree',
				'rewrite_slug' => 'tours',
			),
			'destination'        => array(
				'public'       => true,
				'has_archive'  => true,
				'supports'     => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon'    => 'dashicons-location-alt',
				'rewrite_slug' => 'destinations',
			),
			'testimonial'        => array(
				'public'       => true,
				'has_archive'  => false,
				'supports'     => array( 'title', 'editor' ),
				'menu_icon'    => 'dashicons-format-quote',
				'rewrite_slug' => 'testimonials',
			),
			'faq'                => array(
				'public'       => true,
				'has_archive'  => false,
				'supports'     => array( 'title', 'editor' ),
				'menu_icon'    => 'dashicons-editor-help',
				'rewrite_slug' => 'faqs',
			),
			'itinerary_day'      => array(
				'public'    => false,
				'supports'  => array( 'title', 'editor' ),
				'menu_icon' => 'dashicons-calendar-alt',
			),
			'vehicle_option'     => array(
				'public'    => false,
				'supports'  => array( 'title' ),
				'menu_icon' => 'dashicons-car',
			),
			'accommodation'      => array(
				'public'    => false,
				'supports'  => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon' => 'dashicons-admin-home',
			),
			'transfer_option'    => array(
				'public'    => false,
				'supports'  => array( 'title' ),
				'menu_icon' => 'dashicons-migrate',
			),
			'addon'              => array(
				'public'    => false,
				'supports'  => array( 'title', 'editor' ),
				'menu_icon' => 'dashicons-plus-alt',
			),
			'availability_rule'  => array(
				'public'          => false,
				'supports'        => array( 'title' ),
				'menu_icon'       => 'dashicons-clock',
				'capability_type' => array( 'tbc_availability_rule', 'tbc_availability_rules' ),
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'edit_post'              => 'edit_tbc_availability_rule',
					'read_post'              => 'read_tbc_availability_rule',
					'delete_post'            => 'delete_tbc_availability_rule',
					'edit_posts'             => 'manage_tbc_availability',
					'edit_others_posts'      => 'manage_tbc_availability',
					'publish_posts'          => 'manage_tbc_availability',
					'read_private_posts'     => 'manage_tbc_availability',
					'edit_published_posts'   => 'manage_tbc_availability',
					'edit_private_posts'     => 'manage_tbc_availability',
					'delete_posts'           => 'manage_tbc_availability',
					'delete_published_posts' => 'manage_tbc_availability',
					'delete_private_posts'   => 'manage_tbc_availability',
					'delete_others_posts'    => 'manage_tbc_availability',
				),
			),
			'booking'            => array(
				'public'          => false,
				'show_in_rest'    => false,
				'supports'        => array( 'title' ),
				'menu_icon'       => 'dashicons-tickets-alt',
				'capability_type' => array( 'tbc_booking', 'tbc_bookings' ),
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'edit_post'              => 'edit_tbc_booking',
					'read_post'              => 'read_tbc_booking',
					'delete_post'            => 'delete_tbc_booking',
					'edit_posts'             => 'manage_tbc_bookings',
					'edit_others_posts'      => 'manage_tbc_bookings',
					'edit_published_posts'   => 'manage_tbc_bookings',
					'edit_private_posts'     => 'manage_tbc_bookings',
					'publish_posts'          => 'manage_tbc_bookings',
					'read_private_posts'     => 'manage_tbc_bookings',
					'delete_posts'           => 'manage_tbc_bookings',
					'delete_others_posts'    => 'manage_tbc_bookings',
					'delete_published_posts' => 'manage_tbc_bookings',
					'delete_private_posts'   => 'manage_tbc_bookings',
				),
			),
			'voucher'            => array(
				'public'          => false,
				'show_in_rest'    => false,
				'supports'        => array( 'title' ),
				'menu_icon'       => 'dashicons-tag',
				'capability_type' => array( 'tbc_voucher', 'tbc_vouchers' ),
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'edit_post'              => 'edit_tbc_voucher',
					'read_post'              => 'read_tbc_voucher',
					'delete_post'            => 'delete_tbc_voucher',
					'edit_posts'             => 'manage_tbc_vouchers',
					'edit_others_posts'      => 'manage_tbc_vouchers',
					'edit_published_posts'   => 'manage_tbc_vouchers',
					'edit_private_posts'     => 'manage_tbc_vouchers',
					'publish_posts'          => 'manage_tbc_vouchers',
					'read_private_posts'     => 'manage_tbc_vouchers',
					'delete_posts'           => 'manage_tbc_vouchers',
					'delete_others_posts'    => 'manage_tbc_vouchers',
					'delete_published_posts' => 'manage_tbc_vouchers',
					'delete_private_posts'   => 'manage_tbc_vouchers',
				),
			),
		);
	}

	public static function register() {
		foreach ( self::get_schema() as $post_type => $args ) {
			register_post_type( $post_type, self::build_args( $post_type, $args ) );
		}
	}

	private static function build_args( $post_type, $args ) {
		$defaults = array(
			'label'        => ucwords( str_replace( '_', ' ', $post_type ) ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'supports'     => array( 'title' ),
			'menu_icon'    => 'dashicons-admin-generic',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['rewrite_slug'] ) ) {
			$args['rewrite'] = array( 'slug' => $args['rewrite_slug'] );
			unset( $args['rewrite_slug'] );
		}

		return $args;
	}
}
