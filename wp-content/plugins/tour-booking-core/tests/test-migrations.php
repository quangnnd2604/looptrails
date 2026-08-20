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

	public function test_migrations_are_declared_in_ascending_version_order() {
		$versions = array_keys( Tbc_Migrations::get_migrations() );
		$sorted   = $versions;
		usort( $sorted, 'version_compare' );

		// run() sorts defensively, but a mis-ordered declaration is still a smell.
		$this->assertSame( $sorted, $versions );
	}

	public function test_maybe_run_executes_when_stale() {
		update_option( 'tbc_db_version', '0.0.0' );
		delete_option( 'tbc_installed_at' );

		Tbc_Migrations::maybe_run();

		$this->assertSame( TBC_DB_VERSION, get_option( 'tbc_db_version' ) );
		$this->assertNotFalse( get_option( 'tbc_installed_at' ) );
	}
}
