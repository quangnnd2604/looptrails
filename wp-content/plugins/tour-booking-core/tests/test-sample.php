<?php
class Test_Sample extends WP_UnitTestCase {
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TBC_PLUGIN_FILE' ) );
		$this->assertSame( '0.2.0', TBC_DB_VERSION );
	}
}
