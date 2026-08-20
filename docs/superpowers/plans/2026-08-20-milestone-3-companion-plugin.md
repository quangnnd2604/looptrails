# Milestone 3 — Companion Plugin Schema, Roles, Migrations, Demo Importer

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stand up the `tour-booking-core` companion plugin's data foundation — custom post types, taxonomies, post-meta field schema, custom roles/capabilities, a version-gated migration runner, and a demo-content importer/remover — all covered by real PHPUnit tests against a WP test database, per spec §13 milestone 3.

**Architecture:** A single WordPress plugin at `wp-content/plugins/tour-booking-core/` with one PHP class per concern (`Tbc_Post_Types`, `Tbc_Taxonomies`, `Tbc_Meta_Fields`, `Tbc_Roles`, `Tbc_Migrations`, `Tbc_Demo_Importer`, `Tbc_Demo_Remover`, `Tbc_Admin_Page`), wired together from a thin bootstrap file (`tour-booking-core.php`). No business logic (pricing, availability calculation, payments) lives here yet — that arrives in later milestones (spec §13 items 8–9). This milestone only registers the data shapes those milestones will fill in, plus enough demo data to prove the shapes work end-to-end. Business/operational CPTs (`booking`, `voucher`) get custom capabilities instead of the default `post` capability type, so role separation (`booking_manager` vs `translator`) is enforced by WordPress itself, not by ad-hoc checks scattered through later code.

**Tech Stack:** WordPress 7.0.4 (PHP 8.2, MariaDB 10.4, via XAMPP), PHPUnit + `wp-phpunit/wp-phpunit` + `yoast/phpunit-polyfills` (installed via Composer — no `svn` dependency, since this environment doesn't have it), WP-CLI 2.12 for manual verification.

**Spec:** `docs/AI_AGENT_WORDPRESS_TOUR_WEBSITE_SPEC.md` (v2.0) — this plan implements §2.2 (schema/migration/importer deliverables), §6 (content and admin data model), and the relevant slice of §12 (demo content) needed to prove §6 works; full booking/pricing/payment behavior from §8–§10 is out of scope here and is not simulated beyond storing the fields those milestones will read and write.

## Global Constraints

- No Elementor/Divi/WPBakery/page-builder runtime; no paid plugin or paid SaaS required to operate the core site (spec §2.1).
- Business logic (post types, fields, booking, pricing, availability, payments, emails) lives in the `tour-booking-core` plugin, never the theme (spec §2.1).
- All custom code follows WordPress Coding Standards: nonces, capabilities, escaping, sanitization, prepared queries (spec §2.1).
- No domain, email, API key, gateway secret, phone number, exchange rate or business identity may be embedded in source code (spec §2.2).
- Shared operational values (SKU, capacity, dates, stock, base VND price, payment state) must not be duplicated between EN/VI translations (spec §6).
- Role capabilities must ensure Booking Manager cannot edit code/plugins and Translator cannot edit prices/payments (spec §6).
- All delete actions use trash/soft deletion where WordPress supports it; operational records retain an audit trail (spec §6).
- Every sample operational record carries an `is_demo` marker; the remover deletes only marked demo records (spec §12).
- Version-controlled field/schema definitions and database migration/version logic are required deliverables (spec §2.2).

---

## Task 1: PHPUnit + WP test harness

**Files:**
- Create: `wp-content/plugins/tour-booking-core/composer.json`
- Create: `wp-content/plugins/tour-booking-core/phpunit.xml.dist`
- Create: `wp-content/plugins/tour-booking-core/tests/wp-tests-config.php`
- Create: `wp-content/plugins/tour-booking-core/tests/bootstrap.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-sample.php`
- Create: `wp-content/plugins/tour-booking-core/tour-booking-core.php` (stub plugin header only — filled in by Task 2)
- Modify: `.gitignore` — ignore Composer's `vendor/` inside the plugin

**Interfaces:**
- Consumes: nothing (first task).
- Produces: a working `phpunit` command runnable from `wp-content/plugins/tour-booking-core/`, loading a real WordPress test environment via `WP_UnitTestCase`. `TBC_PLUGIN_FILE`, `TBC_PLUGIN_DIR`, `TBC_DB_VERSION` constants, defined once the plugin file loads. All later tasks' tests extend `WP_UnitTestCase` and rely on this bootstrap.

- [ ] **Step 1: Write `composer.json`**

```json
{
	"name": "looptrails/tour-booking-core",
	"description": "Companion plugin for a WordPress tour-booking site: post types, taxonomies, roles, migrations, booking, pricing, availability and payments.",
	"type": "wordpress-plugin",
	"license": "GPL-2.0-or-later",
	"require-dev": {
		"phpunit/phpunit": "^9.6 || ^10.0 || ^11.0",
		"yoast/phpunit-polyfills": "^2.0 || ^3.0",
		"wp-phpunit/wp-phpunit": "*"
	},
	"minimum-stability": "stable"
}
```

- [ ] **Step 2: Install dependencies**

Run (from `wp-content/plugins/tour-booking-core/`):
```bash
composer install
```
Expected: `vendor/` created containing `phpunit/phpunit`, `wp-phpunit/wp-phpunit`, `yoast/phpunit-polyfills`, and `vendor/bin/phpunit` (or `vendor\bin\phpunit.bat` on Windows) exists.

- [ ] **Step 3: Create the test database**

Run:
```bash
"C:/xampp/mysql/bin/mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS looptrails_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"
```
Expected: no error; `SHOW DATABASES` includes `looptrails_test`.

- [ ] **Step 4: Write `tests/wp-tests-config.php`**

```php
<?php
define( 'ABSPATH', 'C:/xampp/htdocs/looptrails/' );

define( 'DB_NAME', 'looptrails_test' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_';

define( 'WP_TESTS_DOMAIN', 'looptrails.test' );
define( 'WP_TESTS_EMAIL', 'admin@looptrails.test' );
define( 'WP_TESTS_TITLE', 'Loop Trails Test' );

define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
```

- [ ] **Step 5: Write `tests/bootstrap.php`**

```php
<?php
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

putenv( 'WP_TESTS_CONFIG_FILE_PATH=' . __DIR__ . '/wp-tests-config.php' );
putenv( 'WP_PHPUNIT__DIR=' . dirname( __DIR__ ) . '/vendor/wp-phpunit/wp-phpunit' );
putenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH=' . dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );

$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );

require $_tests_dir . '/includes/functions.php';

function tbc_manually_load_plugin() {
	require dirname( __DIR__ ) . '/tour-booking-core.php';
}
tests_add_filter( 'muplugins_loaded', 'tbc_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
```

- [ ] **Step 6: Write `phpunit.xml.dist`**

```xml
<?xml version="1.0"?>
<phpunit
	bootstrap="tests/bootstrap.php"
	colors="true"
>
	<testsuites>
		<testsuite name="tour-booking-core">
			<directory suffix=".php">./tests/</directory>
			<exclude>./tests/bootstrap.php</exclude>
			<exclude>./tests/wp-tests-config.php</exclude>
		</testsuite>
	</testsuites>
</phpunit>
```

- [ ] **Step 7: Write the stub plugin file `tour-booking-core.php`**

```php
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
```

- [ ] **Step 8: Write `tests/test-sample.php`**

```php
<?php
class Test_Sample extends WP_UnitTestCase {
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TBC_PLUGIN_FILE' ) );
		$this->assertSame( '0.1.0', TBC_DB_VERSION );
	}
}
```

- [ ] **Step 9: Run PHPUnit and confirm it passes**

Run (from `wp-content/plugins/tour-booking-core/`):
```bash
vendor/bin/phpunit
```
Expected: `OK (1 test, 2 assertions)` (Windows: `vendor\bin\phpunit.bat` if the POSIX shim doesn't execute directly).

- [ ] **Step 10: Ignore Composer's vendor directory**

Add to `.gitignore`:
```
# Companion plugin PHPUnit/Composer dev dependencies (reinstall via `composer install`)
/wp-content/plugins/tour-booking-core/vendor/
```

- [ ] **Step 11: Commit**

```bash
git add wp-content/plugins/tour-booking-core/composer.json wp-content/plugins/tour-booking-core/composer.lock wp-content/plugins/tour-booking-core/phpunit.xml.dist wp-content/plugins/tour-booking-core/tests wp-content/plugins/tour-booking-core/tour-booking-core.php .gitignore
git commit -m "test: add PHPUnit + WP test harness for tour-booking-core"
```

---

## Task 2: Migration framework + plugin bootstrap

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-migrations.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-migrations.php`
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require the class, wire activation + `admin_init` hooks

**Interfaces:**
- Consumes: `TBC_PLUGIN_DIR`, `TBC_PLUGIN_FILE`, `TBC_DB_VERSION` (Task 1).
- Produces: `Tbc_Migrations::run()`, `Tbc_Migrations::maybe_run()`, `Tbc_Migrations::get_migrations()` (static, returns `array<string, callable>` keyed by version). Task 6 appends a `'0.2.0'` entry to this map — later tasks must not replace `get_migrations()`, only add to its returned array.

- [ ] **Step 1: Write the failing tests — `tests/test-migrations.php`**

```php
<?php
class Test_Migrations extends WP_UnitTestCase {

	public function test_run_sets_db_version_option() {
		delete_option( 'tbc_db_version' );

		Tbc_Migrations::run();

		$this->assertSame( TBC_DB_VERSION, get_option( 'tbc_db_version' ) );
	}

	public function test_run_is_idempotent() {
		Tbc_Migrations::run();
		$first_installed_at = get_option( 'tbc_installed_at' );

		Tbc_Migrations::run();

		$this->assertSame( $first_installed_at, get_option( 'tbc_installed_at' ) );
	}

	public function test_maybe_run_skips_when_already_current() {
		update_option( 'tbc_db_version', TBC_DB_VERSION );
		delete_option( 'tbc_installed_at' );

		Tbc_Migrations::maybe_run();

		$this->assertFalse( get_option( 'tbc_installed_at' ) );
	}

	public function test_maybe_run_executes_when_stale() {
		update_option( 'tbc_db_version', '0.0.0' );
		delete_option( 'tbc_installed_at' );

		Tbc_Migrations::maybe_run();

		$this->assertSame( TBC_DB_VERSION, get_option( 'tbc_db_version' ) );
		$this->assertNotFalse( get_option( 'tbc_installed_at' ) );
	}
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Migrations`
Expected: FAIL — `Class "Tbc_Migrations" not found`.

- [ ] **Step 3: Write `includes/class-migrations.php`**

```php
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
		);
	}

	public static function run() {
		$current = get_option( self::OPTION_KEY, '0.0.0' );

		foreach ( self::get_migrations() as $version => $callback ) {
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
}
```

- [ ] **Step 4: Wire it into the plugin bootstrap**

Append to `tour-booking-core.php` (after the constants from Task 1):

```php
require_once TBC_PLUGIN_DIR . 'includes/class-migrations.php';

register_activation_hook( TBC_PLUGIN_FILE, array( 'Tbc_Migrations', 'run' ) );
add_action( 'admin_init', array( 'Tbc_Migrations', 'maybe_run' ) );
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests from Task 1 and Task 2 pass.

- [ ] **Step 6: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-migrations.php wp-content/plugins/tour-booking-core/tests/test-migrations.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: add version-gated migration runner to tour-booking-core"
```

---

## Task 3: Custom post types

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-post-types.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-post-types.php`
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require the class, hook registration on `init`

**Interfaces:**
- Consumes: nothing beyond WP core.
- Produces: 12 registered post types (`tour`, `destination`, `itinerary_day`, `vehicle_option`, `accommodation`, `transfer_option`, `addon`, `testimonial`, `faq`, `booking`, `voucher`, `availability_rule`), all `show_in_rest`. `booking` and `voucher` expose custom primitive capabilities `manage_tbc_bookings`/`edit_tbc_booking` and `manage_tbc_vouchers`/`edit_tbc_voucher` (via `capability_type` + `map_meta_cap`) — Task 6 grants these to roles, Task 5's meta auth callbacks rely on `get_post_type_object($post_type)->cap->edit_post`.

- [ ] **Step 1: Write the failing tests — `tests/test-post-types.php`**

```php
<?php
class Test_Post_Types extends WP_UnitTestCase {

	private $expected = array(
		'tour',
		'destination',
		'itinerary_day',
		'vehicle_option',
		'accommodation',
		'transfer_option',
		'addon',
		'testimonial',
		'faq',
		'booking',
		'voucher',
		'availability_rule',
	);

	public function test_all_post_types_registered() {
		foreach ( $this->expected as $post_type ) {
			$this->assertTrue( post_type_exists( $post_type ), "Missing post type: {$post_type}" );
		}
	}

	public function test_tour_is_public_with_archive() {
		$tour = get_post_type_object( 'tour' );

		$this->assertTrue( $tour->public );
		$this->assertTrue( $tour->has_archive );
		$this->assertSame( 'tours', $tour->rewrite['slug'] );
	}

	public function test_booking_is_not_public() {
		$booking = get_post_type_object( 'booking' );

		$this->assertFalse( $booking->public );
	}

	public function test_booking_uses_custom_capabilities() {
		$booking = get_post_type_object( 'booking' );

		$this->assertSame( 'manage_tbc_bookings', $booking->cap->edit_posts );
		$this->assertNotSame( 'edit_posts', $booking->cap->edit_posts );
	}

	public function test_voucher_uses_custom_capabilities() {
		$voucher = get_post_type_object( 'voucher' );

		$this->assertSame( 'manage_tbc_vouchers', $voucher->cap->edit_posts );
	}

	public function test_all_post_types_are_rest_enabled() {
		foreach ( $this->expected as $post_type ) {
			$object = get_post_type_object( $post_type );
			$this->assertTrue( $object->show_in_rest, "Not REST-enabled: {$post_type}" );
		}
	}
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Post_Types`
Expected: FAIL — `Class "Tbc_Post_Types" not found`.

- [ ] **Step 3: Write `includes/class-post-types.php`**

```php
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
				'public'    => false,
				'supports'  => array( 'title' ),
				'menu_icon' => 'dashicons-clock',
			),
			'booking'            => array(
				'public'          => false,
				'supports'        => array( 'title' ),
				'menu_icon'       => 'dashicons-tickets-alt',
				'capability_type' => array( 'tbc_booking', 'tbc_bookings' ),
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'edit_post'          => 'edit_tbc_booking',
					'edit_posts'         => 'manage_tbc_bookings',
					'edit_others_posts'  => 'manage_tbc_bookings',
					'publish_posts'      => 'manage_tbc_bookings',
					'read_post'          => 'read_tbc_booking',
					'read_private_posts' => 'manage_tbc_bookings',
					'delete_post'        => 'manage_tbc_bookings',
				),
			),
			'voucher'            => array(
				'public'          => false,
				'supports'        => array( 'title' ),
				'menu_icon'       => 'dashicons-tag',
				'capability_type' => array( 'tbc_voucher', 'tbc_vouchers' ),
				'map_meta_cap'    => true,
				'capabilities'    => array(
					'edit_post'          => 'edit_tbc_voucher',
					'edit_posts'         => 'manage_tbc_vouchers',
					'edit_others_posts'  => 'manage_tbc_vouchers',
					'publish_posts'      => 'manage_tbc_vouchers',
					'read_post'          => 'read_tbc_voucher',
					'read_private_posts' => 'manage_tbc_vouchers',
					'delete_post'        => 'manage_tbc_vouchers',
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
```

- [ ] **Step 4: Wire it into the plugin bootstrap**

Append to `tour-booking-core.php`:

```php
require_once TBC_PLUGIN_DIR . 'includes/class-post-types.php';

add_action( 'init', array( 'Tbc_Post_Types', 'register' ) );
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests pass.

- [ ] **Step 6: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-post-types.php wp-content/plugins/tour-booking-core/tests/test-post-types.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: register tour-booking-core custom post types"
```

---

## Task 4: Taxonomies

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-taxonomies.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-taxonomies.php`
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require the class, hook registration on `init`

**Interfaces:**
- Consumes: `tour`, `destination` post types (Task 3) — taxonomies attach to them.
- Produces: `tour_type`, `destination_region`, `tour_duration`, `tour_feature` taxonomies, all `show_in_rest`, all attached to `tour`; `destination_region` additionally attached to `destination`.

- [ ] **Step 1: Write the failing tests — `tests/test-taxonomies.php`**

```php
<?php
class Test_Taxonomies extends WP_UnitTestCase {

	public function test_all_taxonomies_registered() {
		foreach ( array( 'tour_type', 'destination_region', 'tour_duration', 'tour_feature' ) as $taxonomy ) {
			$this->assertTrue( taxonomy_exists( $taxonomy ), "Missing taxonomy: {$taxonomy}" );
		}
	}

	public function test_tour_type_attached_to_tour() {
		$this->assertTrue( is_object_in_taxonomy( 'tour', 'tour_type' ) );
	}

	public function test_destination_region_is_hierarchical_and_shared() {
		$taxonomy = get_taxonomy( 'destination_region' );

		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertTrue( is_object_in_taxonomy( 'tour', 'destination_region' ) );
		$this->assertTrue( is_object_in_taxonomy( 'destination', 'destination_region' ) );
	}

	public function test_taxonomies_are_rest_enabled() {
		foreach ( array( 'tour_type', 'destination_region', 'tour_duration', 'tour_feature' ) as $taxonomy ) {
			$this->assertTrue( get_taxonomy( $taxonomy )->show_in_rest, "Not REST-enabled: {$taxonomy}" );
		}
	}
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Taxonomies`
Expected: FAIL — `Class "Tbc_Taxonomies" not found`.

- [ ] **Step 3: Write `includes/class-taxonomies.php`**

```php
<?php
/**
 * Registers the plugin's taxonomies from a single schema array.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Taxonomies {

	public static function get_schema() {
		return array(
			'tour_type'          => array(
				'object_types' => array( 'tour' ),
				'hierarchical' => false,
				'rewrite_slug' => 'tour-type',
			),
			'destination_region' => array(
				'object_types' => array( 'tour', 'destination' ),
				'hierarchical' => true,
				'rewrite_slug' => 'destination-region',
			),
			'tour_duration'      => array(
				'object_types' => array( 'tour' ),
				'hierarchical' => false,
				'rewrite_slug' => 'duration',
			),
			'tour_feature'       => array(
				'object_types' => array( 'tour' ),
				'hierarchical' => false,
				'rewrite_slug' => 'feature',
			),
		);
	}

	public static function register() {
		foreach ( self::get_schema() as $taxonomy => $args ) {
			register_taxonomy( $taxonomy, $args['object_types'], self::build_args( $taxonomy, $args ) );
		}
	}

	private static function build_args( $taxonomy, $args ) {
		return array(
			'label'        => ucwords( str_replace( '_', ' ', $taxonomy ) ),
			'hierarchical' => $args['hierarchical'],
			'public'       => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => $args['rewrite_slug'] ),
		);
	}
}
```

- [ ] **Step 4: Wire it into the plugin bootstrap**

Append to `tour-booking-core.php`:

```php
require_once TBC_PLUGIN_DIR . 'includes/class-taxonomies.php';

add_action( 'init', array( 'Tbc_Taxonomies', 'register' ), 10 );
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests pass.

- [ ] **Step 6: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-taxonomies.php wp-content/plugins/tour-booking-core/tests/test-taxonomies.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: register tour-booking-core taxonomies"
```

---

## Task 5: Post meta field schema

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-meta-fields.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-meta-fields.php`
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require the class, hook registration on `init` at a later priority than post types

**Interfaces:**
- Consumes: all 12 post types (Task 3) and their `cap->edit_post` capability names.
- Produces: `Tbc_Meta_Fields::get_schema()` — the field/type registry every later task (demo importer, and future milestones' admin UI/booking engine) reads meta key names from. Meta keys tagged `is_price => true` require the `edit_tbc_prices` capability to write, on top of the post type's own edit capability — Task 6 is what grants/withholds that capability per role.

- [ ] **Step 1: Write the failing tests — `tests/test-meta-fields.php`**

```php
<?php
class Test_Meta_Fields extends WP_UnitTestCase {

	public function test_all_schema_meta_keys_are_registered() {
		foreach ( Tbc_Meta_Fields::get_schema() as $post_type => $fields ) {
			$registered = get_registered_meta_keys( 'post', $post_type );
			foreach ( array_keys( $fields ) as $meta_key ) {
				$this->assertArrayHasKey( $meta_key, $registered, "Missing meta {$meta_key} on {$post_type}" );
			}
		}
	}

	public function test_vehicle_option_price_meta_is_registered() {
		$this->assertArrayHasKey( 'tbc_price_vnd', get_registered_meta_keys( 'post', 'vehicle_option' ) );
	}

	public function test_price_meta_denies_translator() {
		$translator_id = self::factory()->user->create( array( 'role' => 'translator' ) );
		$vehicle_id     = self::factory()->post->create( array( 'post_type' => 'vehicle_option' ) );

		wp_set_current_user( $translator_id );

		$this->assertFalse( current_user_can( 'edit_post_meta', $vehicle_id, 'tbc_price_vnd' ) );
	}

	public function test_price_meta_allows_administrator() {
		$admin_id   = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$vehicle_id = self::factory()->post->create( array( 'post_type' => 'vehicle_option' ) );

		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'edit_post_meta', $vehicle_id, 'tbc_price_vnd' ) );
	}

	public function test_non_price_meta_only_requires_edit_post_capability() {
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$tour_id  = self::factory()->post->create( array( 'post_type' => 'tour' ) );

		wp_set_current_user( $admin_id );

		$this->assertTrue( current_user_can( 'edit_post_meta', $tour_id, 'tbc_badge' ) );
	}
}
```

Note: `test_price_meta_denies_translator` requires the `translator` role to exist — it will fail with "role doesn't exist" style behavior (an empty-capability user) until Task 6 registers it. Run it now to confirm it fails for the *expected* reason (no `Tbc_Meta_Fields` class yet), not skip ahead.

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Meta_Fields`
Expected: FAIL — `Class "Tbc_Meta_Fields" not found`.

- [ ] **Step 3: Write `includes/class-meta-fields.php`**

```php
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
```

- [ ] **Step 4: Wire it into the plugin bootstrap**

Append to `tour-booking-core.php`:

```php
require_once TBC_PLUGIN_DIR . 'includes/class-meta-fields.php';

add_action( 'init', array( 'Tbc_Meta_Fields', 'register' ), 20 );
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests pass, including `test_price_meta_denies_translator` (the `translator` role doesn't exist yet at this point, so `current_user_can()` for that user correctly returns `false` for every capability including `edit_tbc_prices`, which already satisfies the assertion — Task 6 later gives the role real, narrower capabilities without changing this test's outcome).

- [ ] **Step 6: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-meta-fields.php wp-content/plugins/tour-booking-core/tests/test-meta-fields.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: register tour-booking-core post meta schema with price-field gating"
```

---

## Task 6: Roles and capabilities

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-roles.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-roles.php`
- Create: `wp-content/plugins/tour-booking-core/uninstall.php`
- Modify: `wp-content/plugins/tour-booking-core/includes/class-migrations.php` — add a `'0.2.0'` migration step
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require the class, bump `TBC_DB_VERSION`

**Interfaces:**
- Consumes: `manage_tbc_bookings`/`manage_tbc_vouchers` capability names from Task 3's CPT registration; `edit_tbc_prices` capability name from Task 5's meta auth callback; `Tbc_Migrations::get_migrations()` (Task 2) to hook the install step.
- Produces: `booking_manager` and `translator` roles; `administrator` gains `manage_tbc_bookings`, `manage_tbc_vouchers`, `manage_tbc_availability`, `edit_tbc_prices`. `Tbc_Roles::ADMIN_CAPS` (public constant, `array<string>`) and `Tbc_Roles::install()` / `Tbc_Roles::uninstall()`.

- [ ] **Step 1: Write the failing tests — `tests/test-roles.php`**

```php
<?php
class Test_Roles extends WP_UnitTestCase {

	public function test_administrator_gets_custom_capabilities() {
		$admin = get_role( 'administrator' );

		foreach ( Tbc_Roles::ADMIN_CAPS as $cap ) {
			$this->assertTrue( $admin->has_cap( $cap ), "Administrator missing {$cap}" );
		}
	}

	public function test_booking_manager_role_exists_with_scoped_caps() {
		$role = get_role( 'booking_manager' );

		$this->assertNotNull( $role );
		$this->assertTrue( $role->has_cap( 'manage_tbc_bookings' ) );
		$this->assertTrue( $role->has_cap( 'manage_tbc_vouchers' ) );
		$this->assertTrue( $role->has_cap( 'manage_tbc_availability' ) );
		$this->assertFalse( $role->has_cap( 'edit_plugins' ) );
		$this->assertFalse( $role->has_cap( 'edit_theme_options' ) );
		$this->assertFalse( $role->has_cap( 'install_plugins' ) );
	}

	public function test_translator_role_cannot_manage_prices_or_bookings() {
		$role = get_role( 'translator' );

		$this->assertNotNull( $role );
		$this->assertTrue( $role->has_cap( 'edit_posts' ) );
		$this->assertFalse( $role->has_cap( 'edit_tbc_prices' ) );
		$this->assertFalse( $role->has_cap( 'manage_tbc_bookings' ) );
		$this->assertFalse( $role->has_cap( 'manage_tbc_vouchers' ) );
		$this->assertFalse( $role->has_cap( 'edit_plugins' ) );
	}

	public function test_booking_manager_can_edit_booking_price_meta() {
		$user_id    = self::factory()->user->create( array( 'role' => 'booking_manager' ) );
		$booking_id = self::factory()->post->create( array( 'post_type' => 'booking' ) );

		wp_set_current_user( $user_id );

		$this->assertTrue( current_user_can( 'edit_post_meta', $booking_id, 'tbc_total_vnd' ) );
	}

	public function test_translator_cannot_edit_booking_at_all() {
		$user_id    = self::factory()->user->create( array( 'role' => 'translator' ) );
		$booking_id = self::factory()->post->create( array( 'post_type' => 'booking' ) );

		wp_set_current_user( $user_id );

		$this->assertFalse( current_user_can( 'edit_post', $booking_id ) );
	}
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Roles`
Expected: FAIL — `Class "Tbc_Roles" not found`.

- [ ] **Step 3: Write `includes/class-roles.php`**

```php
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
```

- [ ] **Step 4: Hook `Tbc_Roles::install()` into the migration runner**

Modify `includes/class-migrations.php`:

```php
public static function get_migrations() {
	return array(
		'0.1.0' => array( __CLASS__, 'migrate_0_1_0' ),
		'0.2.0' => array( __CLASS__, 'migrate_0_2_0' ),
	);
}
```

Add a new private method to the same class:

```php
private static function migrate_0_2_0() {
	require_once TBC_PLUGIN_DIR . 'includes/class-roles.php';
	Tbc_Roles::install();
}
```

- [ ] **Step 5: Bump the plugin version and require the roles class up front**

In `tour-booking-core.php`, change:
```php
define( 'TBC_DB_VERSION', '0.1.0' );
```
to:
```php
define( 'TBC_DB_VERSION', '0.2.0' );
```

Also update the header comment's `Version:` line from `0.1.0` to `0.2.0` for consistency.

- [ ] **Step 6: Write `uninstall.php`**

```php
<?php
/**
 * Fires only when the plugin is deleted from wp-admin (not on deactivate).
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once __DIR__ . '/includes/class-roles.php';
Tbc_Roles::uninstall();

delete_option( 'tbc_db_version' );
delete_option( 'tbc_installed_at' );
```

- [ ] **Step 7: Run tests to verify they pass**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests pass. `WP_UnitTestCase` runs each test inside a transaction rolled back afterward, but role/capability changes made via `add_role`/`add_cap` in `wp_options` persist across the suite the same way plugin activation would — this is expected and matches how the real site behaves after activation.

- [ ] **Step 8: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-roles.php wp-content/plugins/tour-booking-core/includes/class-migrations.php wp-content/plugins/tour-booking-core/uninstall.php wp-content/plugins/tour-booking-core/tests/test-roles.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: add Booking Manager and Translator roles with scoped capabilities"
```

---

## Task 7: Demo content importer

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-demo-importer.php`
- Create: `wp-content/plugins/tour-booking-core/includes/class-admin-page.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-demo-importer.php`
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require both classes, hook the admin page and the WP-CLI import command

**Interfaces:**
- Consumes: all 12 post types (Task 3), meta schema (Task 5).
- Produces: `Tbc_Demo_Importer::import()` — returns `array<string,int>` of created-post counts keyed by post type. `Tbc_Demo_Importer::TOURS` (6-entry demo tour catalog). Every created post/meta record is stamped `tbc_is_demo = true` and, for tours, `tbc_lang` (`en`|`vi`) + `tbc_translation_group` (shared id per language pair) — Task 8's remover selects on `tbc_is_demo`.

- [ ] **Step 1: Write the failing tests — `tests/test-demo-importer.php`**

```php
<?php
class Test_Demo_Importer extends WP_UnitTestCase {

	public function test_import_creates_six_tours_per_language() {
		$counts = Tbc_Demo_Importer::import();

		$this->assertSame( 12, $counts['tour'] );
	}

	public function test_import_marks_every_tour_as_demo() {
		Tbc_Demo_Importer::import();

		$tours = get_posts( array( 'post_type' => 'tour', 'posts_per_page' => -1 ) );
		$this->assertNotEmpty( $tours );

		foreach ( $tours as $tour ) {
			$this->assertTrue( (bool) get_post_meta( $tour->ID, 'tbc_is_demo', true ) );
		}
	}

	public function test_import_creates_both_languages_for_each_tour() {
		Tbc_Demo_Importer::import();

		$en_count = count(
			get_posts(
				array(
					'post_type'      => 'tour',
					'meta_key'       => 'tbc_lang',
					'meta_value'     => 'en',
					'posts_per_page' => -1,
				)
			)
		);
		$vi_count = count(
			get_posts(
				array(
					'post_type'      => 'tour',
					'meta_key'       => 'tbc_lang',
					'meta_value'     => 'vi',
					'posts_per_page' => -1,
				)
			)
		);

		$this->assertSame( 6, $en_count );
		$this->assertSame( 6, $vi_count );
	}

	public function test_import_creates_related_records_for_every_tour() {
		$counts = Tbc_Demo_Importer::import();

		$this->assertSame( 12, $counts['destination'] );
		$this->assertSame( 24, $counts['itinerary_day'] );
		$this->assertSame( 24, $counts['vehicle_option'] );
		$this->assertSame( 12, $counts['accommodation'] );
		$this->assertSame( 24, $counts['transfer_option'] );
		$this->assertSame( 12, $counts['addon'] );
		$this->assertSame( 12, $counts['testimonial'] );
		$this->assertSame( 24, $counts['faq'] );
		$this->assertSame( 60, $counts['availability_rule'] );
		$this->assertSame( 1, $counts['voucher'] );
	}

	public function test_import_covers_all_availability_states() {
		Tbc_Demo_Importer::import();

		$states = wp_list_pluck(
			array_map(
				function ( $post ) {
					return array( 'tbc_state' => get_post_meta( $post->ID, 'tbc_state', true ) );
				},
				get_posts( array( 'post_type' => 'availability_rule', 'posts_per_page' => -1 ) )
			),
			'tbc_state'
		);

		foreach ( array( 'available', 'limited', 'full', 'blocked' ) as $state ) {
			$this->assertContains( $state, $states, "Missing availability state: {$state}" );
		}
	}
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Demo_Importer`
Expected: FAIL — `Class "Tbc_Demo_Importer" not found`.

- [ ] **Step 3: Write `includes/class-demo-importer.php`**

```php
<?php
/**
 * One-click demo content importer. Creates an EN + VI pair of tours, each
 * with a destination, itinerary, vehicle/accommodation/transfer/add-on
 * options, a testimonial, FAQs and a spread of availability states, plus
 * one shared demo voucher. Every created record is stamped tbc_is_demo.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Demo_Importer {

	const TOURS = array(
		array( 'title_en' => 'Northern Highlands Loop', 'title_vi' => 'Vòng Cung Cao Nguyên Bắc', 'days' => 4, 'nights' => 3 ),
		array( 'title_en' => 'Central Coast Explorer', 'title_vi' => 'Khám Phá Duyên Hải Miền Trung', 'days' => 3, 'nights' => 2 ),
		array( 'title_en' => 'Mekong River Discovery', 'title_vi' => 'Khám Phá Sông Mê Kông', 'days' => 2, 'nights' => 1 ),
		array( 'title_en' => 'Sapa Mountain Trail', 'title_vi' => 'Cung Đường Núi Sa Pa', 'days' => 5, 'nights' => 4 ),
		array( 'title_en' => 'Hue Imperial Route', 'title_vi' => 'Hành Trình Kinh Thành Huế', 'days' => 3, 'nights' => 2 ),
		array( 'title_en' => 'Ha Giang Extreme Loop', 'title_vi' => 'Vòng Cung Mạo Hiểm Hà Giang', 'days' => 6, 'nights' => 5 ),
	);

	public static function import() {
		$counts = array(
			'tour'              => 0,
			'destination'       => 0,
			'itinerary_day'     => 0,
			'vehicle_option'    => 0,
			'accommodation'     => 0,
			'transfer_option'   => 0,
			'addon'             => 0,
			'testimonial'       => 0,
			'faq'               => 0,
			'voucher'           => 0,
			'availability_rule' => 0,
		);

		self::create_voucher();
		++$counts['voucher'];

		foreach ( self::TOURS as $tour_data ) {
			$translation_group = uniqid( 'tbc_demo_', true );

			foreach ( array( 'en', 'vi' ) as $lang ) {
				$tour_id = self::create_tour( $tour_data, $lang, $translation_group );
				++$counts['tour'];

				$destination_id = self::create_destination( $tour_data, $lang );
				++$counts['destination'];
				update_post_meta( $tour_id, 'tbc_destination_id', $destination_id );

				for ( $day = 0; $day <= 1; $day++ ) {
					self::create_itinerary_day( $tour_id, $day, $lang );
					++$counts['itinerary_day'];
				}

				foreach ( array( 'motorbike', 'jeep' ) as $vehicle_type ) {
					self::create_vehicle_option( $tour_id, $vehicle_type );
					++$counts['vehicle_option'];
				}

				self::create_accommodation( $tour_id, $lang );
				++$counts['accommodation'];

				foreach ( array( 'to', 'from' ) as $direction ) {
					self::create_transfer_option( $tour_id, $direction );
					++$counts['transfer_option'];
				}

				self::create_addon( $tour_id, $lang );
				++$counts['addon'];

				self::create_testimonial( $tour_id, $lang );
				++$counts['testimonial'];

				for ( $i = 0; $i < 2; $i++ ) {
					self::create_faq( $tour_id, $lang );
					++$counts['faq'];
				}

				foreach ( self::availability_states() as $offset => $state ) {
					self::create_availability_rule( $tour_id, $offset, $state );
					++$counts['availability_rule'];
				}
			}
		}

		return $counts;
	}

	private static function availability_states() {
		return array( 'available', 'available', 'limited', 'full', 'blocked' );
	}

	private static function create_tour( $tour_data, $lang, $translation_group ) {
		$tour_id = wp_insert_post(
			array(
				'post_type'    => 'tour',
				'post_title'   => 'vi' === $lang ? $tour_data['title_vi'] : $tour_data['title_en'],
				'post_excerpt' => 'Demo tour seeded by the Tour Booking Core importer.',
				'post_status'  => 'publish',
			)
		);

		update_post_meta( $tour_id, 'tbc_duration_days', $tour_data['days'] );
		update_post_meta( $tour_id, 'tbc_duration_nights', $tour_data['nights'] );
		update_post_meta( $tour_id, 'tbc_badge', 'featured' );
		update_post_meta( $tour_id, 'tbc_rating_value', 4.8 );
		update_post_meta( $tour_id, 'tbc_rating_count', 12 );
		update_post_meta( $tour_id, 'tbc_lang', $lang );
		update_post_meta( $tour_id, 'tbc_translation_group', $translation_group );
		update_post_meta( $tour_id, 'tbc_is_demo', true );

		return $tour_id;
	}

	private static function create_destination( $tour_data, $lang ) {
		$suffix          = 'vi' === $lang ? ' - Điểm đến' : ' Destination';
		$destination_id  = wp_insert_post(
			array(
				'post_type'   => 'destination',
				'post_title'  => ( 'vi' === $lang ? $tour_data['title_vi'] : $tour_data['title_en'] ) . $suffix,
				'post_status' => 'publish',
			)
		);

		update_post_meta( $destination_id, 'tbc_region', 'demo-region' );
		update_post_meta( $destination_id, 'tbc_is_demo', true );

		return $destination_id;
	}

	private static function create_itinerary_day( $tour_id, $day, $lang ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'itinerary_day',
				'post_title'  => sprintf( 'vi' === $lang ? 'Ngày %d' : 'Day %d', $day ),
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_day_number', $day );
		update_post_meta( $post_id, 'tbc_included', 'Breakfast, guide, fuel' );
		update_post_meta( $post_id, 'tbc_excluded', 'Personal expenses' );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_vehicle_option( $tour_id, $vehicle_type ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'vehicle_option',
				'post_title'  => ucfirst( $vehicle_type ),
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_vehicle_type', $vehicle_type );
		update_post_meta( $post_id, 'tbc_price_vnd', 'motorbike' === $vehicle_type ? 350000 : 900000 );
		update_post_meta( $post_id, 'tbc_capacity', 'motorbike' === $vehicle_type ? 2 : 6 );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_accommodation( $tour_id, $lang ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'accommodation',
				'post_title'  => 'vi' === $lang ? 'Homestay tiêu chuẩn' : 'Standard Homestay',
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_price_vnd', 250000 );
		update_post_meta( $post_id, 'tbc_upgrade', false );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_transfer_option( $tour_id, $direction ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'transfer_option',
				'post_title'  => 'to' === $direction ? 'Bus to destination' : 'Bus after tour',
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_direction', $direction );
		update_post_meta( $post_id, 'tbc_price_vnd', 150000 );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_addon( $tour_id, $lang ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'addon',
				'post_title'  => 'vi' === $lang ? 'Bảo hiểm du lịch' : 'Travel Insurance',
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_price_vnd', 80000 );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_testimonial( $tour_id, $lang ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'testimonial',
				'post_title'   => 'vi' === $lang ? 'Trải nghiệm tuyệt vời' : 'Wonderful experience',
				'post_content' => 'vi' === $lang ? 'Nội dung đánh giá mẫu.' : 'Sample demo review content.',
				'post_status'  => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_rating_value', 5 );
		update_post_meta( $post_id, 'tbc_author_name', 'Demo Traveler' );
		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_faq( $tour_id, $lang ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'faq',
				'post_title'   => 'vi' === $lang ? 'Câu hỏi thường gặp' : 'Frequently asked question',
				'post_content' => 'vi' === $lang ? 'Câu trả lời mẫu.' : 'Sample demo answer.',
				'post_status'  => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_availability_rule( $tour_id, $offset, $state ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'availability_rule',
				'post_title'  => sprintf( '%d:%s', $tour_id, $state ),
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_tour_id', $tour_id );
		update_post_meta( $post_id, 'tbc_date', gmdate( 'Y-m-d', strtotime( "+{$offset} days" ) ) );
		update_post_meta( $post_id, 'tbc_state', $state );
		update_post_meta( $post_id, 'tbc_capacity', 'blocked' === $state ? 0 : 8 );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	private static function create_voucher() {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'voucher',
				'post_title'  => 'DEMO10',
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, 'tbc_code', 'DEMO10' );
		update_post_meta( $post_id, 'tbc_voucher_type', 'percent' );
		update_post_meta( $post_id, 'tbc_amount', 10 );
		update_post_meta( $post_id, 'tbc_valid_from', gmdate( 'Y-m-d' ) );
		update_post_meta( $post_id, 'tbc_valid_to', gmdate( 'Y-m-d', strtotime( '+90 days' ) ) );
		update_post_meta( $post_id, 'tbc_usage_limit', 100 );
		update_post_meta( $post_id, 'tbc_used_count', 0 );
		update_post_meta( $post_id, 'tbc_min_spend_vnd', 500000 );
		update_post_meta( $post_id, 'tbc_is_demo', true );

		return $post_id;
	}

	public static function cli_import() {
		$counts = self::import();
		WP_CLI::success( sprintf( 'Imported demo content: %s', wp_json_encode( $counts ) ) );
	}
}
```

- [ ] **Step 4: Write `includes/class-admin-page.php`**

```php
<?php
/**
 * Minimal "Tour Booking" admin page with a one-click demo-content import
 * button. The remove button is added by Task 8.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Admin_Page {

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_post_tbc_import_demo', array( __CLASS__, 'handle_import' ) );
	}

	public static function add_menu() {
		add_menu_page(
			__( 'Tour Booking', 'tour-booking-core' ),
			__( 'Tour Booking', 'tour-booking-core' ),
			'manage_options',
			'tour-booking-core',
			array( __CLASS__, 'render' ),
			'dashicons-palmtree'
		);
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Tour Booking Core', 'tour-booking-core' ); ?></h1>
			<?php if ( isset( $_GET['tbc_notice'] ) && 'imported' === $_GET['tbc_notice'] ) : ?>
				<div class="notice notice-success"><p><?php esc_html_e( 'Demo content imported.', 'tour-booking-core' ); ?></p></div>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'tbc_import_demo' ); ?>
				<input type="hidden" name="action" value="tbc_import_demo" />
				<?php submit_button( __( 'Import Demo Content', 'tour-booking-core' ) ); ?>
			</form>
		</div>
		<?php
	}

	public static function handle_import() {
		check_admin_referer( 'tbc_import_demo' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'tour-booking-core' ) );
		}

		Tbc_Demo_Importer::import();

		wp_safe_redirect( add_query_arg( 'tbc_notice', 'imported', admin_url( 'admin.php?page=tour-booking-core' ) ) );
		exit;
	}
}
```

- [ ] **Step 5: Wire both classes into the plugin bootstrap**

Append to `tour-booking-core.php`:

```php
require_once TBC_PLUGIN_DIR . 'includes/class-demo-importer.php';
require_once TBC_PLUGIN_DIR . 'includes/class-admin-page.php';

Tbc_Admin_Page::register();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'tbc demo import', array( 'Tbc_Demo_Importer', 'cli_import' ) );
}
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `vendor/bin/phpunit`
Expected: `OK` — all tests pass.

- [ ] **Step 7: Manual WP-CLI smoke check against the real site**

Run:
```bash
C:/xampp/wp-cli.bat plugin activate tour-booking-core --path=c:/xampp/htdocs/looptrails
C:/xampp/wp-cli.bat tbc demo import --path=c:/xampp/htdocs/looptrails
C:/xampp/wp-cli.bat post list --post_type=tour --path=c:/xampp/htdocs/looptrails
```
Expected: plugin activates without fatal errors, importer reports 12 tours created, `post list` shows them.

- [ ] **Step 8: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-demo-importer.php wp-content/plugins/tour-booking-core/includes/class-admin-page.php wp-content/plugins/tour-booking-core/tests/test-demo-importer.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: add demo content importer with EN/VI tours and admin trigger"
```

---

## Task 8: Demo content remover

**Files:**
- Create: `wp-content/plugins/tour-booking-core/includes/class-demo-remover.php`
- Create: `wp-content/plugins/tour-booking-core/tests/test-demo-remover.php`
- Modify: `wp-content/plugins/tour-booking-core/includes/class-admin-page.php` — add the remove form + handler
- Modify: `wp-content/plugins/tour-booking-core/tour-booking-core.php` — require the class, register the WP-CLI remove command

**Interfaces:**
- Consumes: `Tbc_Demo_Importer` (Task 7) in tests only; `tbc_is_demo` meta convention (Task 5/7).
- Produces: `Tbc_Demo_Remover::remove()` — returns `int` deleted count. `Tbc_Demo_Remover::DEMO_POST_TYPES` (public constant, `array<string>`).

- [ ] **Step 1: Write the failing tests — `tests/test-demo-remover.php`**

```php
<?php
class Test_Demo_Remover extends WP_UnitTestCase {

	public function test_remove_deletes_only_demo_marked_posts() {
		$demo_tour_id = self::factory()->post->create( array( 'post_type' => 'tour' ) );
		update_post_meta( $demo_tour_id, 'tbc_is_demo', true );

		$real_tour_id = self::factory()->post->create( array( 'post_type' => 'tour' ) );

		Tbc_Demo_Remover::remove();

		$this->assertNull( get_post( $demo_tour_id ) );
		$this->assertNotNull( get_post( $real_tour_id ) );
	}

	public function test_remove_covers_every_demo_post_type() {
		Tbc_Demo_Importer::import();

		Tbc_Demo_Remover::remove();

		foreach ( Tbc_Demo_Remover::DEMO_POST_TYPES as $post_type ) {
			$remaining = get_posts(
				array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'meta_key'       => 'tbc_is_demo',
					'meta_value'     => '1',
				)
			);
			$this->assertCount( 0, $remaining, "Leftover demo posts in {$post_type}" );
		}
	}

	public function test_remove_returns_deleted_count() {
		self::factory()->post->create(
			array(
				'post_type' => 'tour',
				'meta_input' => array( 'tbc_is_demo' => true ),
			)
		);

		$deleted = Tbc_Demo_Remover::remove();

		$this->assertGreaterThanOrEqual( 1, $deleted );
	}
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `vendor/bin/phpunit --filter Test_Demo_Remover`
Expected: FAIL — `Class "Tbc_Demo_Remover" not found`.

- [ ] **Step 3: Write `includes/class-demo-remover.php`**

```php
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
					'post_status'    => 'any',
					'meta_key'       => 'tbc_is_demo',
					'meta_value'     => '1',
				)
			);

			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true );
				++$deleted;
			}
		}

		return $deleted;
	}

	public static function cli_remove() {
		$deleted = self::remove();
		WP_CLI::success( sprintf( 'Removed %d demo records.', $deleted ) );
	}
}
```

- [ ] **Step 4: Add the remove form and handler to the admin page**

Modify `includes/class-admin-page.php`:

In `register()`, add a second action hook:
```php
add_action( 'admin_post_tbc_remove_demo', array( __CLASS__, 'handle_remove' ) );
```

In `render()`, after the import `<form>` and before the closing `</div>`, add:
```php
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Delete all demo content? This cannot be undone.', 'tour-booking-core' ) ); ?>');">
	<?php wp_nonce_field( 'tbc_remove_demo' ); ?>
	<input type="hidden" name="action" value="tbc_remove_demo" />
	<?php submit_button( __( 'Remove Demo Content', 'tour-booking-core' ), 'delete' ); ?>
</form>
```

Also extend the notice check to cover `removed`:
```php
<?php if ( isset( $_GET['tbc_notice'] ) && in_array( $_GET['tbc_notice'], array( 'imported', 'removed' ), true ) ) : ?>
	<div class="notice notice-success"><p>
		<?php echo 'removed' === $_GET['tbc_notice'] ? esc_html__( 'Demo content removed.', 'tour-booking-core' ) : esc_html__( 'Demo content imported.', 'tour-booking-core' ); ?>
	</p></div>
<?php endif; ?>
```
(replace the existing single-condition notice block from Task 7 with this one)

Add the new handler method to the class:
```php
public static function handle_remove() {
	check_admin_referer( 'tbc_remove_demo' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'tour-booking-core' ) );
	}

	Tbc_Demo_Remover::remove();

	wp_safe_redirect( add_query_arg( 'tbc_notice', 'removed', admin_url( 'admin.php?page=tour-booking-core' ) ) );
	exit;
}
```

- [ ] **Step 5: Wire the class into the plugin bootstrap**

Append to `tour-booking-core.php`:

```php
require_once TBC_PLUGIN_DIR . 'includes/class-demo-remover.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'tbc demo remove', array( 'Tbc_Demo_Remover', 'cli_remove' ) );
}
```
(this second `WP_CLI` block sits alongside the `tbc demo import` registration from Task 7 — combine into one `if` block with both `add_command` calls rather than two separate `if` statements)

- [ ] **Step 6: Run the full suite to verify everything passes together**

Run: `vendor/bin/phpunit`
Expected: `OK` — every test from Tasks 1–8 passes in one run.

- [ ] **Step 7: Manual WP-CLI smoke check against the real site**

Run:
```bash
C:/xampp/wp-cli.bat tbc demo remove --path=c:/xampp/htdocs/looptrails
C:/xampp/wp-cli.bat post list --post_type=tour --path=c:/xampp/htdocs/looptrails
```
Expected: remover reports 12 records removed (from Task 7's manual import), `post list` returns no tours.

- [ ] **Step 8: Commit**

```bash
git add wp-content/plugins/tour-booking-core/includes/class-demo-remover.php wp-content/plugins/tour-booking-core/includes/class-admin-page.php wp-content/plugins/tour-booking-core/tests/test-demo-remover.php wp-content/plugins/tour-booking-core/tour-booking-core.php
git commit -m "feat: add safe demo content remover with admin trigger"
```

---

## Milestone completion checklist

- [ ] All 8 tasks' PHPUnit tests pass in a single `vendor/bin/phpunit` run.
- [ ] `wp-cli.bat plugin activate tour-booking-core` succeeds with no fatal errors or PHP warnings against the real `looptrails` database.
- [ ] `wp-cli.bat tbc demo import` then `wp-cli.bat tbc demo remove` round-trip cleanly (import creates records, remove deletes exactly those records, no orphaned demo data).
- [ ] Report to the user: files changed, migrations added (`0.1.0`, `0.2.0`), tests run/passed, and any known deviations (e.g., EN/VI linkage here is a placeholder `tbc_lang`/`tbc_translation_group` meta pair, not yet wired to Polylang — full i18n/currency behavior is spec §13 milestone 7's job) — per the spec's zero-undisclosed-deviation rule (§15).
- [ ] Wait for the user's explicit pass before starting Milestone 4 (theme shell: header, navigation, footer, global responsive tokens).
