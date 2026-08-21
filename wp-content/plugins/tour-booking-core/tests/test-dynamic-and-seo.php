<?php
/**
 * Test Dynamic Features & SEO (Milestones 8 & 9).
 */

class Test_Dynamic_And_Seo extends WP_UnitTestCase {

	public function test_currency_formatting() {
		$usd_formatted = Tbc_Currency::format( 140, 'USD' );
		$this->assertEquals( '$140', $usd_formatted );

		$vnd_formatted = Tbc_Currency::format( 3556000, 'VND' );
		$this->assertStringContainsString( '3.556.000', $vnd_formatted );
		$this->assertStringContainsString( '₫', $vnd_formatted );
	}

	public function test_currency_conversion() {
		$vnd = Tbc_Currency::usd_to_vnd( 100 );
		$this->assertEquals( 2540000, $vnd );

		$usd = Tbc_Currency::vnd_to_usd( 2540000 );
		$this->assertEquals( 100.0, $usd );
	}

	public function test_tour_search_filter_query() {
		$query = Tbc_Search_Filter::filter_tours(
			array(
				'per_page'      => 4,
				'max_price_usd' => 300,
			)
		);

		$this->assertInstanceOf( 'WP_Query', $query );
		$this->assertEquals( 'tour', $query->query_vars['post_type'] );
	}

	public function test_seo_json_ld_renders() {
		ob_start();
		Tbc_Seo::output_json_ld();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'application/ld+json', $output );
		$this->assertStringContainsString( 'TravelAgency', $output );
		$this->assertStringContainsString( 'PostalAddress', $output );
	}
}
