<?php
/**
 * Version-gated setup: keeps roles, options and rewrite rules in sync with
 * TBC_DB_VERSION whether the plugin was just activated or silently updated.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Migrations {

	const OPTION_KEY = 'tbc_db_version';

	public static function get_migrations() {
		return array(
			'0.1.0' => array( __CLASS__, 'migrate_0_1_0' ),
			'0.2.0' => array( __CLASS__, 'migrate_0_2_0' ),
		);
	}

	public static function run() {
		$current = get_option( self::OPTION_KEY, '0.0.0' );

		// Run migrations in ascending version order rather than relying on the
		// array's insertion order, which nothing enforces.
		$migrations = self::get_migrations();
		uksort( $migrations, 'version_compare' );

		foreach ( $migrations as $version => $callback ) {
			if ( version_compare( $current, $version, '<' ) ) {
				call_user_func( $callback );
			}
		}

		update_option( self::OPTION_KEY, TBC_DB_VERSION );
	}

	public static function maybe_run() {
		if ( TBC_DB_VERSION !== get_option( self::OPTION_KEY ) ) {
			self::run();
		}
	}

	private static function migrate_0_1_0() {
		update_option( 'tbc_installed_at', current_time( 'mysql', true ) );
		flush_rewrite_rules();
	}

	private static function migrate_0_2_0() {
		require_once TBC_PLUGIN_DIR . 'includes/class-roles.php';
		Tbc_Roles::install();
	}
}
