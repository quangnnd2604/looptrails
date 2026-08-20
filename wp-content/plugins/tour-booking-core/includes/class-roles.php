<?php
/**
 * Custom roles: Booking Manager (operational access, no code/plugin access)
 * and Translator (content access, no prices/payments access).
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Roles {

	const ADMIN_CAPS = array(
		'manage_tbc_bookings',
		'manage_tbc_vouchers',
		'manage_tbc_availability',
		'edit_tbc_prices',
	);

	public static function install() {
		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			foreach ( self::ADMIN_CAPS as $cap ) {
				$administrator->add_cap( $cap );
			}
		}

		add_role(
			'booking_manager',
			__( 'Booking Manager', 'tour-booking-core' ),
			array(
				'read'                    => true,
				'upload_files'            => true,
				'manage_tbc_bookings'     => true,
				'manage_tbc_vouchers'     => true,
				'manage_tbc_availability' => true,
				'edit_tbc_prices'         => true,
			)
		);

		add_role(
			'translator',
			__( 'Translator', 'tour-booking-core' ),
			array(
				'read'                 => true,
				'upload_files'         => true,
				'edit_posts'           => true,
				'edit_published_posts' => true,
				// Translators are almost never the original author of the tours
				// they translate, so editing another author's published tour
				// requires edit_others_posts on top of edit_published_posts.
				// Deliberately NOT granted: edit_private_posts, delete_posts,
				// and every price/booking/voucher capability.
				'edit_others_posts'    => true,
			)
		);
	}

	public static function uninstall() {
		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			foreach ( self::ADMIN_CAPS as $cap ) {
				$administrator->remove_cap( $cap );
			}
		}

		remove_role( 'booking_manager' );
		remove_role( 'translator' );
	}
}
