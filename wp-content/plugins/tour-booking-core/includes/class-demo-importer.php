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

	const IMPORTED_OPTION = 'tbc_demo_content_imported';

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

		// Idempotency guard: a second "Import" click (or CLI run) must not
		// double the demo content. The flag is cleared by Tbc_Demo_Remover.
		if ( get_option( self::IMPORTED_OPTION ) ) {
			return $counts;
		}

		self::create_voucher();
		++$counts['voucher'];

		foreach ( self::TOURS as $tour_data ) {
			$translation_group = uniqid( 'tbc_demo_', true );
			$destination_id    = null;

			foreach ( array( 'en', 'vi' ) as $lang ) {
				$tour_id = self::create_tour( $tour_data, $lang, $translation_group );
				++$counts['tour'];

				if ( 'en' === $lang ) {
					// Operational children are created once per tour pair, keyed to the
					// canonical (English) tour, and shared by both language tours — per
					// spec §6, operational values (price, capacity, dates) must not be
					// duplicated between translations.
					$destination_id = self::create_destination( $tour_data, $lang );
					++$counts['destination'];

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

					foreach ( self::availability_states() as $offset => $state ) {
						self::create_availability_rule( $tour_id, $offset, $state );
						++$counts['availability_rule'];
					}
				}

				update_post_meta( $tour_id, 'tbc_destination_id', $destination_id );

				// Translatable-only content stays per-language.
				for ( $day = 1; $day <= 2; $day++ ) {
					self::create_itinerary_day( $tour_id, $day, $lang );
					++$counts['itinerary_day'];
				}

				self::create_testimonial( $tour_id, $lang );
				++$counts['testimonial'];

				for ( $i = 0; $i < 2; $i++ ) {
					self::create_faq( $tour_id, $lang );
					++$counts['faq'];
				}
			}
		}

		update_option( self::IMPORTED_OPTION, true );

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

		if ( 0 === array_sum( $counts ) ) {
			WP_CLI::warning( 'Demo content is already imported; nothing was created. Run `wp tbc demo remove` first.' );
			return;
		}

		WP_CLI::success( sprintf( 'Imported demo content: %s', wp_json_encode( $counts ) ) );
	}
}
