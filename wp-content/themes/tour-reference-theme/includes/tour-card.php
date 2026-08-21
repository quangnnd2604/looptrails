<?php
/**
 * Reusable Tour Card Component
 *
 * Implements Spec §5.3 and reference design measurements (01-home.md, desktop.json).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render single tour card markup.
 *
 * @param int $post_id Tour post ID.
 * @return string HTML markup.
 */
function tour_theme_render_tour_card( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	$title         = get_the_title( $post_id );
	$permalink     = get_permalink( $post_id );
	$duration_days = get_post_meta( $post_id, 'tbc_duration_days', true );
	$duration_nts  = get_post_meta( $post_id, 'tbc_duration_nights', true );
	$badge         = get_post_meta( $post_id, 'tbc_badge', true );
	$rating_val    = get_post_meta( $post_id, 'tbc_rating_value', true );
	$rating_cnt    = get_post_meta( $post_id, 'tbc_rating_count', true );

	if ( ! $rating_val ) {
		$rating_val = '4.9';
	}
	if ( ! $rating_cnt ) {
		$rating_cnt = '120';
	}
	if ( ! $badge ) {
		$badge = 'POPULAR';
	}

	$duration_label = '';
	if ( $duration_days ) {
		$duration_label = $duration_days . 'D';
		if ( $duration_nts ) {
			$duration_label .= $duration_nts . 'N';
		}
	} else {
		$duration_label = '4D3N';
	}

	$thumb_html = '';
	if ( has_post_thumbnail( $post_id ) ) {
		$thumb_html = get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'tour-card__img' ) );
	} else {
		$card_svg   = sprintf(
			"<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#e4e0da' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='18' font-weight='bold' x='50%%' y='50%%' text-anchor='middle'>%s</text></svg>",
			esc_html( $title )
		);
		$thumb_html = sprintf(
			'<img src="%s" alt="%s" class="tour-card__img" />',
			esc_attr( 'data:image/svg+xml,' . rawurlencode( $card_svg ) ),
			esc_attr( $title )
		);
	}

	// Read real vehicle_option children
	$vehicles = get_posts( array(
		'post_type'      => 'vehicle_option',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_key'       => 'tbc_tour_id',
		'meta_value'     => $post_id,
	) );
	if ( empty( $vehicles ) ) {
		$group = get_post_meta( $post_id, 'tbc_translation_group', true );
		if ( $group ) {
			$siblings = get_posts( array(
				'post_type'      => 'tour',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__not_in'   => array( $post_id ),
				'meta_key'       => 'tbc_translation_group',
				'meta_value'     => $group,
			) );
			foreach ( $siblings as $sibling ) {
				$sibling_vehicles = get_posts( array(
					'post_type'      => 'vehicle_option',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_key'       => 'tbc_tour_id',
					'meta_value'     => $sibling->ID,
				) );
				if ( ! empty( $sibling_vehicles ) ) {
					$vehicles = $sibling_vehicles;
					break;
				}
			}
		}
	}

	$price_rows = array();
	foreach ( $vehicles as $v ) {
		$vnd = intval( get_post_meta( $v->ID, 'tbc_price_vnd', true ) );
		if ( $vnd <= 0 ) {
			continue;
		}
		$price_rows[] = array(
			'label' => get_the_title( $v->ID ),
			'vnd'   => $vnd,
			'usd'   => class_exists( 'Tbc_Currency' ) ? Tbc_Currency::vnd_to_usd( $vnd ) : intval( round( $vnd / 25400 ) ),
		);
	}
	usort( $price_rows, function( $a, $b ) { return $a['vnd'] <=> $b['vnd']; } );

	ob_start();
	?>
	<div class="tour-card lt-tour-card" data-tour-id="<?php echo esc_attr( $post_id ); ?>">
		<div class="tour-card__media">
			<a href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo $thumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php if ( $badge ) : ?>
				<span class="tour-card__badge lt-badge"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<?php if ( $duration_label ) : ?>
				<span class="tour-card__duration lt-duration-pill"><?php echo esc_html( $duration_label ); ?></span>
			<?php endif; ?>
		</div>

		<div class="tour-card__body">
			<!-- Taxonomy / Spec chips -->
			<div class="tour-card__chips">
				<span class="lt-pill"><?php esc_html_e( 'Motorbike', 'tour-reference-theme' ); ?></span>
				<span class="lt-pill"><?php esc_html_e( 'Jeep 4x4', 'tour-reference-theme' ); ?></span>
				<span class="lt-pill"><?php esc_html_e( 'Daily Departure', 'tour-reference-theme' ); ?></span>
			</div>

			<!-- Tour Title -->
			<h3 class="tour-card__title lt-tour-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>

			<!-- Rating -->
			<div class="tour-card__rating">
				<span class="tour-card__stars">★★★★★</span>
				<span class="tour-card__rating-text"><?php echo esc_html( $rating_val ); ?> (<?php echo esc_html( $rating_cnt ); ?>)</span>
			</div>

			<!-- Multi-tier vehicle pricing rows from real vehicle_options -->
			<div class="tour-card__prices lt-price-rows">
				<?php if ( empty( $price_rows ) ) : ?>
					<div class="lt-price-row"><span class="lt-price-row__label"><?php esc_html_e( 'Contact for pricing', 'tour-reference-theme' ); ?></span></div>
				<?php else : ?>
					<?php foreach ( $price_rows as $i => $row ) : ?>
						<div class="lt-price-row<?php echo 1 === $i ? ' is-featured-tier' : ''; ?>">
							<span class="lt-price-row__label"><?php echo esc_html( $row['label'] ); ?></span>
							<div class="lt-price-row__amount">
								<span class="lt-price-row__value"><?php echo esc_html( number_format( $row['vnd'], 0, ',', '.' ) ); ?> ₫</span>
								<span class="lt-price-row__usd">· $<?php echo esc_html( $row['usd'] ); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- Actions -->
			<div class="tour-card__actions">
				<div class="wp-block-button is-style-book-now">
					<a class="wp-block-button__link wp-element-button" href="#book" data-tour-select="<?php echo esc_attr( $post_id ); ?>">
						<?php esc_html_e( 'Book Now', 'tour-reference-theme' ); ?>
					</a>
				</div>
				<a class="tour-card__details-link" href="<?php echo esc_url( $permalink ); ?>">
					<?php esc_html_e( 'Details →', 'tour-reference-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render grid of featured tours.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML markup.
 */
function tour_theme_render_featured_tours( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'postsPerPage' => 6,
			'columns'      => 3,
		),
		$atts,
		'tour_featured_grid'
	);

	$args = array(
		'post_type'      => 'tour',
		'post_status'    => 'publish',
		'posts_per_page' => intval( $atts['postsPerPage'] ),
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	);

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		// Fallback seeded render if DB is empty
		ob_start();
		?>
		<div class="tour-grid lt-tours__grid cols-3">
			<?php
			$fallback_tours = array(
				array( 'title' => 'Ha Giang Loop 4 Days 3 Nights (Classic Route)', 'price' => 140, 'badge' => 'POPULAR', 'days' => 4, 'nights' => 3 ),
				array( 'title' => 'Ha Giang Loop 3 Days 2 Nights (Express Loop)', 'price' => 110, 'badge' => 'BEST SELLER', 'days' => 3, 'nights' => 2 ),
				array( 'title' => 'Ha Giang & Cao Bang 6 Days 5 Nights (Ultimate Frontier)', 'price' => 220, 'badge' => 'EPIC', 'days' => 6, 'nights' => 5 ),
				array( 'title' => 'Ha Giang Loop 2 Days 1 Night (Quick Taste)', 'price' => 85, 'badge' => 'SHORT BREAK', 'days' => 2, 'nights' => 1 ),
				array( 'title' => 'Ba Be Lake & Ban Gioc Waterfall 3 Days', 'price' => 135, 'badge' => 'NATURE', 'days' => 3, 'nights' => 2 ),
				array( 'title' => 'Sapa & Y Ty Cloud Hunting Motorbike Tour 4 Days', 'price' => 165, 'badge' => 'ADVENTURE', 'days' => 4, 'nights' => 3 ),
			);
			foreach ( $fallback_tours as $idx => $t ) {
				$mock_id = -( $idx + 1 );
				?>
				<div class="tour-card lt-tour-card">
					<div class="tour-card__media">
						<?php
						$fb_svg = sprintf(
							"<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#e4e0da' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%%' y='50%%' text-anchor='middle'>%s</text></svg>",
							esc_html( $t['title'] )
						);
						?>
						<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $fb_svg ) ); ?>" alt="<?php echo esc_attr( $t['title'] ); ?>" class="tour-card__img" />
						<span class="tour-card__badge lt-badge"><?php echo esc_html( $t['badge'] ); ?></span>
						<span class="tour-card__duration lt-duration-pill"><?php echo esc_html( $t['days'] . 'D' . $t['nights'] . 'N' ); ?></span>
					</div>
					<div class="tour-card__body">
						<div class="tour-card__chips">
							<span class="lt-pill"><?php esc_html_e( 'Motorbike', 'tour-reference-theme' ); ?></span>
							<span class="lt-pill"><?php esc_html_e( 'Jeep', 'tour-reference-theme' ); ?></span>
							<span class="lt-pill"><?php esc_html_e( 'Daily', 'tour-reference-theme' ); ?></span>
						</div>
						<h3 class="tour-card__title lt-tour-card__title"><a href="#book"><?php echo esc_html( $t['title'] ); ?></a></h3>
						<div class="tour-card__rating">
							<span class="tour-card__stars">★★★★★</span>
							<span class="tour-card__rating-text">4.9 (120+)</span>
						</div>
						<div class="tour-card__prices lt-price-rows">
							<div class="lt-price-row">
								<span class="lt-price-row__label">Self-drive</span>
								<div class="lt-price-row__amount">
									<span class="lt-price-row__value"><?php echo esc_html( number_format( $t['price'] * 25400, 0, ',', '.' ) ); ?> ₫</span>
									<span class="lt-price-row__usd">· $<?php echo esc_html( $t['price'] ); ?></span>
								</div>
							</div>
							<div class="lt-price-row is-featured-tier">
								<span class="lt-price-row__label">Easy rider</span>
								<div class="lt-price-row__amount">
									<span class="lt-price-row__value"><?php echo esc_html( number_format( round( $t['price'] * 1.48 ) * 25400, 0, ',', '.' ) ); ?> ₫</span>
									<span class="lt-price-row__usd">· $<?php echo esc_html( round( $t['price'] * 1.48 ) ); ?></span>
								</div>
							</div>
						</div>
						<div class="tour-card__actions">
							<div class="wp-block-button is-style-book-now">
								<a class="wp-block-button__link wp-element-button" href="#book"><?php esc_html_e( 'Book Now', 'tour-reference-theme' ); ?></a>
							</div>
							<a class="tour-card__details-link" href="#book"><?php esc_html_e( 'Details →', 'tour-reference-theme' ); ?></a>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	ob_start();
	?>
	<div class="tour-grid lt-tours__grid cols-<?php echo esc_attr( $atts['columns'] ); ?>">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			echo tour_theme_render_tour_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tour_featured_grid', 'tour_theme_render_featured_tours' );

/**
 * Render dynamic day-by-day itinerary for single tour.
 */
function tour_theme_render_single_itinerary( $tour_id = 0 ) {
	if ( ! $tour_id ) {
		$tour_id = get_the_ID();
	}
	if ( ! $tour_id ) {
		return '';
	}

	$days = get_posts( array(
		'post_type'      => 'itinerary_day',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_key'       => 'tbc_day_number',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => 'tbc_tour_id',
				'value'   => $tour_id,
				'compare' => '=',
			),
		),
	) );

	// Fallback to translation group siblings if empty
	if ( empty( $days ) ) {
		$group = get_post_meta( $tour_id, 'tbc_translation_group', true );
		if ( $group ) {
			$siblings = get_posts( array(
				'post_type'      => 'tour',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__not_in'   => array( $tour_id ),
				'meta_key'       => 'tbc_translation_group',
				'meta_value'     => $group,
			) );
			foreach ( $siblings as $sibling ) {
				$sibling_days = get_posts( array(
					'post_type'      => 'itinerary_day',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_key'       => 'tbc_day_number',
					'orderby'        => 'meta_value_num',
					'order'          => 'ASC',
					'meta_query'     => array(
						array(
							'key'     => 'tbc_tour_id',
							'value'   => $sibling->ID,
							'compare' => '=',
						),
					),
				) );
				if ( ! empty( $sibling_days ) ) {
					$days = $sibling_days;
					break;
				}
			}
		}
	}

	if ( empty( $days ) ) {
		return '<p class="itinerary-empty">' . esc_html__( 'Itinerary details coming soon.', 'tour-reference-theme' ) . '</p>';
	}

	$tour_title = get_the_title( $tour_id );
	$output = '<div class="itinerary-timeline">';
	$colors = array(
		1 => array( '#1b3b22', '#2d5a37', '#407a4e' ),
		2 => array( '#1e3048', '#2b476b', '#3d6190' ),
		3 => array( '#4a2c16', '#6b4223', '#8f5b33' ),
		4 => array( '#382245', '#523466', '#714b8a' ),
	);

	foreach ( $days as $day ) {
		$day_num   = get_post_meta( $day->ID, 'tbc_day_number', true );
		$day_title = trim( get_the_title( $day->ID ) );
		$day_desc  = ! empty( $day->post_content ) ? $day->post_content : ( ! empty( $day->post_excerpt ) ? $day->post_excerpt : sprintf( esc_html__( 'Explore scenic mountain passes, cultural heritage sites, and river valleys along the %s route.', 'tour-reference-theme' ), esc_html( $tour_title ) ) );
		$included  = get_post_meta( $day->ID, 'tbc_included', true );

		$output .= '<div class="itinerary-day">';
		$output .= '<div class="itinerary-day__header">';
		$output .= '<div class="itinerary-day__number">' . sprintf( esc_html__( 'Day %s', 'tour-reference-theme' ), esc_html( $day_num ) ) . '</div>';
		if ( ! empty( $day_title ) && ! preg_match( '/^(day|ngày)\s*\d+$/iu', $day_title ) ) {
			$output .= '<h3 class="itinerary-day__title">' . esc_html( $day_title ) . '</h3>';
		}
		$output .= '</div>';
		$output .= '<div class="itinerary-day__desc">' . esc_html( $day_desc ) . '</div>';

		// 3 Placeholder images per day (encoded with rawurlencode)
		$output .= '<div class="itinerary-day__photos">';
		$day_idx = ( (int) $day_num ) % 4;
		if ( 0 === $day_idx ) {
			$day_idx = 4;
		}
		$bg_palette = isset( $colors[ $day_idx ] ) ? $colors[ $day_idx ] : $colors[1];
		for ( $p = 1; $p <= 3; $p++ ) {
			$bg = $bg_palette[ $p - 1 ];
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="213" height="160" viewBox="0 0 213 160"><rect width="213" height="160" fill="' . $bg . '"/><text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="Montserrat, sans-serif" font-size="13" font-weight="bold">Day ' . esc_attr( $day_num ) . ' Highlight ' . $p . '</text><text x="50%" y="62%" dominant-baseline="middle" text-anchor="middle" fill="#dddddd" font-family="Montserrat, sans-serif" font-size="10">Scenic Mountain Route</text></svg>';
			$src = 'data:image/svg+xml;charset=utf-8,' . rawurlencode( $svg );
			$output .= '<img class="itinerary-photo" src="' . esc_url( $src ) . '" alt="' . esc_attr( sprintf( 'Day %s Photo %d', $day_num, $p ) ) . '" width="213" height="160" />';
		}
		$output .= '</div>';

		if ( ! empty( $included ) ) {
			$output .= '<div class="itinerary-day__included"><strong>' . esc_html__( 'Included: ', 'tour-reference-theme' ) . '</strong>' . esc_html( $included ) . '</div>';
		}
		$output .= '</div>';
	}
	$output .= '</div>';

	// Strip any wpautop artifacts
	return str_replace( array( '<p></p>', '<p> </p>' ), '', $output );
}
add_shortcode( 'tour_single_itinerary', 'tour_theme_render_single_itinerary' );

/**
 * Render dynamic vehicle pricing tiers for single tour sticky sidebar.
 */
function tour_theme_render_single_pricing( $tour_id = 0 ) {
	if ( ! $tour_id ) {
		$tour_id = get_the_ID();
	}
	if ( ! $tour_id ) {
		return '';
	}

	$vehicles = get_posts( array(
		'post_type'      => 'vehicle_option',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_key'       => 'tbc_tour_id',
		'meta_value'     => $tour_id,
	) );
	if ( empty( $vehicles ) ) {
		$group = get_post_meta( $tour_id, 'tbc_translation_group', true );
		if ( $group ) {
			$siblings = get_posts( array(
				'post_type'      => 'tour',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__not_in'   => array( $tour_id ),
				'meta_key'       => 'tbc_translation_group',
				'meta_value'     => $group,
			) );
			foreach ( $siblings as $sibling ) {
				$sibling_vehicles = get_posts( array(
					'post_type'      => 'vehicle_option',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_key'       => 'tbc_tour_id',
					'meta_value'     => $sibling->ID,
				) );
				if ( ! empty( $sibling_vehicles ) ) {
					$vehicles = $sibling_vehicles;
					break;
				}
			}
		}
	}

	if ( empty( $vehicles ) ) {
		return '<div class="booking-price-tier"><span class="booking-price-label">' . esc_html__( 'Contact for pricing', 'tour-reference-theme' ) . '</span></div>';
	}

	$price_rows = array();
	foreach ( $vehicles as $v ) {
		$vnd = intval( get_post_meta( $v->ID, 'tbc_price_vnd', true ) );
		if ( $vnd <= 0 ) {
			continue;
		}
		$usd = class_exists( 'Tbc_Currency' ) ? Tbc_Currency::vnd_to_usd( $vnd ) : intval( round( $vnd / 25400 ) );
		$price_rows[] = array(
			'id'    => $v->ID,
			'label' => get_the_title( $v->ID ),
			'vnd'   => $vnd,
			'usd'   => $usd,
		);
	}
	usort( $price_rows, function( $a, $b ) { return $a['vnd'] <=> $b['vnd']; } );

	$output = '<div class="ltw-pricing-tiers-wrapper">';
	$output .= '<div class="ltw-option-title">' . esc_html__( 'Select Option & Price', 'tour-reference-theme' ) . '</div>';
	$output .= '<div class="ltw-pricing-tiers">';
	foreach ( $price_rows as $i => $row ) {
		$is_selected = 0 === $i;
		$output .= sprintf(
			'<label class="booking-price-tier%s" data-index="%d">
				<input type="radio" name="tour_vehicle_choice" value="%d" %s class="ltw-tier-radio">
				<div class="booking-price-tier__content">
					<span class="booking-price-label">%s</span>
					<span class="booking-price-value">$%s <small>USD / person</small></span>
				</div>
			</label>',
			$is_selected ? ' is-selected' : '',
			$i,
			esc_attr( $row['id'] ),
			$is_selected ? 'checked' : '',
			esc_html( $row['label'] ),
			esc_html( $row['usd'] )
		);
	}
	$output .= '</div>';

	// Stepper
	$output .= '
	<div class="ltw-people-picker">
		<span class="ltw-people-label">' . esc_html__( 'Travelers (Max 8)', 'tour-reference-theme' ) . '</span>
		<div class="ltw-stepper">
			<button type="button" class="ltw-people-btn ltw-dec" aria-label="Decrease travelers">−</button>
			<span class="ltw-people-count" id="ltw-travelers-display">1</span>
			<button type="button" class="ltw-people-btn ltw-inc" aria-label="Increase travelers">+</button>
			<input type="hidden" name="party_size" id="ltw-party-size-input" value="1">
		</div>
	</div>';

	// CTA Button & Notes
	$output .= '
	<div class="booking-card-cta">
		<a href="#book" class="ltw-cta-button">' . esc_html__( 'Instant Booking', 'tour-reference-theme' ) . '</a>
		<p class="booking-guarantee-note">🔒 ' . esc_html__( 'Free cancellation · Instant confirmation · No hidden fees', 'tour-reference-theme' ) . '</p>
	</div>';

	// Interactive JS
	$output .= '
	<script>
	(function(){
		document.addEventListener("DOMContentLoaded", function(){
			const labels = document.querySelectorAll(".booking-price-tier");
			labels.forEach(function(lbl){
				lbl.addEventListener("click", function(){
					labels.forEach(function(l){ l.classList.remove("is-selected"); });
					lbl.classList.add("is-selected");
					const radio = lbl.querySelector("input[type=radio]");
					if (radio) radio.checked = true;
				});
			});

			const decBtn = document.querySelector(".ltw-dec");
			const incBtn = document.querySelector(".ltw-inc");
			const display = document.getElementById("ltw-travelers-display");
			const hiddenInput = document.getElementById("ltw-party-size-input");
			if (decBtn && incBtn && display && hiddenInput) {
				let count = 1;
				decBtn.addEventListener("click", function(e){
					e.preventDefault();
					if (count > 1) {
						count--;
						display.textContent = count;
						hiddenInput.value = count;
					}
				});
				incBtn.addEventListener("click", function(e){
					e.preventDefault();
					if (count < 8) {
						count++;
						display.textContent = count;
						hiddenInput.value = count;
					}
				});
			}
		});
	})();
	</script>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'tour_single_pricing', 'tour_theme_render_single_pricing' );

