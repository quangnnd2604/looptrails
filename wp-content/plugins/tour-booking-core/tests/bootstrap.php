<?php
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

define( 'WP_TESTS_CONFIG_FILE_PATH', __DIR__ . '/wp-tests-config.php' );

$_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';

require $_tests_dir . '/includes/functions.php';

function tbc_manually_load_plugin() {
	require dirname( __DIR__ ) . '/tour-booking-core.php';
}
tests_add_filter( 'muplugins_loaded', 'tbc_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
