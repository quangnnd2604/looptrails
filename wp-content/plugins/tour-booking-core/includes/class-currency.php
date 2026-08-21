<?php
/**
 * Currency Formatting & Conversion Engine
 *
 * Implements Spec §7.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Currency {

	/**
	 * Format currency amount for display.
	 *
	 * @param float|int $amount Amount in base currency units.
	 * @param string    $currency 'USD' or 'VND'.
	 * @return string Formatted price string with symbol.
	 */
	public static function format( $amount, $currency = 'USD' ) {
		$currency = strtoupper( $currency );

		if ( 'VND' === $currency ) {
			return number_format( intval( $amount ), 0, ',', '.' ) . ' ₫';
		}

		// Default USD
		return '$' . number_format( floatval( $amount ), 0, '.', ',' );
	}

	/**
	 * Convert USD to VND.
	 *
	 * @param float $amount_usd Amount in USD.
	 * @return int Amount in VND.
	 */
	public static function usd_to_vnd( $amount_usd ) {
		$rate = Tbc_Pricing_Engine::get_exchange_rate();
		return intval( round( floatval( $amount_usd ) * $rate ) );
	}

	/**
	 * Convert VND to USD.
	 *
	 * @param int $amount_vnd Amount in VND.
	 * @return float Amount in USD.
	 */
	public static function vnd_to_usd( $amount_vnd ) {
		$rate = Tbc_Pricing_Engine::get_exchange_rate();
		if ( $rate <= 0 ) {
			$rate = 25400;
		}
		return round( floatval( $amount_vnd ) / $rate, 2 );
	}
}
