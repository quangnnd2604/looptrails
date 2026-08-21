<?php
/**
 * SEO & Schema.org JSON-LD Structured Data
 *
 * Implements Spec §9.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Seo {

	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output_json_ld' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'output_opengraph_tags' ), 5 );
	}

	/**
	 * Output OpenGraph and Twitter Cards.
	 */
	public static function output_opengraph_tags() {
		if ( is_singular( 'tour' ) ) {
			global $post;
			$title = get_the_title();
			$url   = get_permalink();
			$image = get_the_post_thumbnail_url( $post, 'large' );
			$desc  = wp_strip_all_tags( get_the_excerpt() );

			echo "\n<!-- Tour SEO Meta Tags -->\n";
			echo '<meta property="og:type" content="product" />' . "\n";
			echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
			echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
			if ( $desc ) {
				echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
			}
			if ( $image ) {
				echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
			}
			echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		}
	}

	/**
	 * Output Schema.org JSON-LD.
	 */
	public static function output_json_ld() {
		$schema = array();

		$business_name  = get_option( 'tbc_site_business_name', get_bloginfo( 'name' ) );
		$business_email = get_option( 'tbc_site_email', get_option( 'admin_email' ) );
		$business_phone = get_option( 'tbc_site_phone', '+84 123 456 789' );
		$business_addr  = get_option( 'tbc_site_address', 'Ha Giang City, Vietnam' );

		// 1. Organization / TravelAgency Schema (Site-wide)
		$agency_schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'TravelAgency',
			'name'       => $business_name,
			'url'        => home_url( '/' ),
			'telephone'  => $business_phone,
			'email'      => $business_email,
			'priceRange' => '$$',
			'address'    => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $business_addr,
				'addressLocality' => 'Ha Giang City',
				'addressCountry'  => 'VN',
			),
		);
		$schema[] = $agency_schema;

		// 2. TouristTrip / Product Schema on single tours
		if ( is_singular( 'tour' ) ) {
			global $post;
			$price_vnd = intval( get_post_meta( $post->ID, 'tbc_price_from_vnd', true ) );
			$price_usd = $price_vnd > 0 ? ( class_exists( 'Tbc_Currency' ) ? Tbc_Currency::vnd_to_usd( $price_vnd ) : round( $price_vnd / 25400, 2 ) ) : 0;

			$tour_schema = array(
				'@context'    => 'https://schema.org',
				'@type'       => array( 'TouristTrip', 'Product' ),
				'name'        => get_the_title(),
				'description' => wp_strip_all_tags( get_the_excerpt() ),
				'url'         => get_permalink(),
			);

			if ( $price_usd > 0 ) {
				$tour_schema['offers'] = array(
					'@type'         => 'Offer',
					'price'         => number_format( $price_usd, 2, '.', '' ),
					'priceCurrency' => 'USD',
					'availability'  => 'https://schema.org/InStock',
				);
			}

			// Only output rating if verified ratings exist
			$rating_cnt = intval( get_post_meta( $post->ID, 'tbc_rating_count', true ) );
			$rating_val = floatval( get_post_meta( $post->ID, 'tbc_rating_value', true ) );
			if ( $rating_cnt > 0 && $rating_val > 0 ) {
				$tour_schema['aggregateRating'] = array(
					'@type'       => 'AggregateRating',
					'ratingValue' => strval( $rating_val ),
					'reviewCount' => strval( $rating_cnt ),
				);
			}

			$schema[] = $tour_schema;
		}

		if ( ! empty( $schema ) ) {
			echo "\n<!-- Schema.org JSON-LD -->\n";
			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( count( $schema ) === 1 ? $schema[0] : $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
			echo "</script>\n";
		}
	}
}
