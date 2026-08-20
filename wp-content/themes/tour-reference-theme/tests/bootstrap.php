<?php
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

define( 'WP_TESTS_CONFIG_FILE_PATH', __DIR__ . '/wp-tests-config.php' );

$_tests_dir = dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit';

require $_tests_dir . '/includes/functions.php';

function tour_theme_switch_to_theme_under_test() {
	switch_theme( 'tour-reference-theme' );
}
tests_add_filter( 'muplugins_loaded', 'tour_theme_switch_to_theme_under_test' );

require $_tests_dir . '/includes/bootstrap.php';
