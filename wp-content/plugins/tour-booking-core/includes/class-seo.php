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

			echo "\n<!-- Loop Trails Tour SEO -->\n";
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

		// 1. Organization / TravelAgency Schema (Site-wide)
		$schema[] = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'TravelAgency',
			'name'        => 'Loop Trails Vietnam',
			'url'         => home_url( '/' ),
			'telephone'   => '+84987654321',
			'email'       => 'booking@looptrails.com',
			'priceRange'  => '$$',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'Nguyen Trai Street',
				'addressLocality' => 'Ha Giang City',
				'addressCountry'  => 'VN',
			),
			'aggregateRating' => array(
				'@type'       => 'AggregateRating',
				'ratingValue' => '4.9',
				'reviewCount' => '1200',
			),
		);

		// 2. TouristTrip / Product Schema on single tours
		if ( is_singular( 'tour' ) ) {
			global $post;
			$price_usd = floatval( get_post_meta( $post->ID, 'tbc_price_from_usd', true ) );
			if ( $price_usd <= 0 ) {
				$price_usd = 140.0;
			}

			$schema[] = array(
				'@context'    => 'https://schema.org',
				'@type'       => array( 'TouristTrip', 'Product' ),
				'name'        => get_the_title(),
				'description' => wp_strip_all_tags( get_the_excerpt() ),
				'url'         => get_permalink(),
				'offers'      => array(
					'@type'         => 'Offer',
					'price'         => number_format( $price_usd, 2, '.', '' ),
					'priceCurrency' => 'USD',
					'availability'  => 'https://schema.org/InStock',
				),
				'aggregateRating' => array(
					'@type'       => 'AggregateRating',
					'ratingValue' => '4.9',
					'reviewCount' => '120',
				),
			);
		}

		if ( ! empty( $schema ) ) {
			echo "\n<!-- Loop Trails Schema.org JSON-LD -->\n";
			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( count( $schema ) === 1 ? $schema[0] : $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
			echo "</script>\n";
		}
	}
}
