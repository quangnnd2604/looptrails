<?php
/**
 * Plugin Name: Tour Booking Core
 * Description: Companion plugin for tour data, booking, pricing, availability, payments and email — business logic for the tour-reference-theme.
 * Version: 0.1.0
 * Requires PHP: 8.2
 * Text Domain: tour-booking-core
 */

defined( 'ABSPATH' ) || exit;

define( 'TBC_PLUGIN_FILE', __FILE__ );
define( 'TBC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TBC_DB_VERSION', '0.1.0' );

require_once TBC_PLUGIN_DIR . 'includes/class-migrations.php';

register_activation_hook( TBC_PLUGIN_FILE, array( 'Tbc_Migrations', 'run' ) );
add_action( 'admin_init', array( 'Tbc_Migrations', 'maybe_run' ) );

require_once TBC_PLUGIN_DIR . 'includes/class-post-types.php';

add_action( 'init', array( 'Tbc_Post_Types', 'register' ) );

require_once TBC_PLUGIN_DIR . 'includes/class-taxonomies.php';

add_action( 'init', array( 'Tbc_Taxonomies', 'register' ), 10 );
