<?php
/**
 * Fires only when the plugin is deleted from wp-admin (not on deactivate).
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/includes/class-roles.php';
Tbc_Roles::uninstall();

delete_option( 'tbc_db_version' );
delete_option( 'tbc_installed_at' );
