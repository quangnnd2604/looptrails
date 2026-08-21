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
	$price_from    = get_post_meta( $post_id, 'tbc_price_from_usd', true );
	$rating_val    = get_post_meta( $post_id, 'tbc_rating_value', true );
	$rating_cnt    = get_post_meta( $post_id, 'tbc_rating_count', true );

	if ( ! $price_from ) {
		$price_from = 140;
	}
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
		$thumb_html = sprintf(
			'<img src="data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\' viewBox=\'0 0 400 300\'><rect fill=\'%%23e4e0da\' width=\'400\' height=\'300\'/><text fill=\'%%23333\' font-family=\'sans-serif\' font-size=\'18\' font-weight=\'bold\' x=\'50%%\' y=\'50%%\' text-anchor=\'middle\'>%s</text></svg>" alt="%s" class="tour-card__img" />',
			esc_attr( $title ),
			esc_attr( $title )
		);
	}

	// Calculated prices for 3 vehicle options matching reference data
	$price_self_usd = intval( $price_from );
	$price_self_vnd = number_format( $price_self_usd * 25400, 0, ',', '.' );

	$price_easy_usd = intval( round( $price_self_usd * 1.48 ) );
	$price_easy_vnd = number_format( $price_easy_usd * 25400, 0, ',', '.' );

	$price_jeep_usd = intval( round( $price_self_usd * 2.07 ) );
	$price_jeep_vnd = number_format( $price_jeep_usd * 25400, 0, ',', '.' );

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

			<!-- Multi-tier vehicle pricing rows matching reference measurement -->
			<div class="tour-card__prices lt-price-rows">
				<div class="lt-price-row">
					<span class="lt-price-row__label"><?php esc_html_e( 'Self-drive', 'tour-reference-theme' ); ?></span>
					<div class="lt-price-row__amount">
						<span class="lt-price-row__value"><?php echo esc_html( $price_self_vnd ); ?> ₫</span>
						<span class="lt-price-row__usd">· $<?php echo esc_html( $price_self_usd ); ?></span>
					</div>
				</div>
				<div class="lt-price-row is-featured-tier">
					<span class="lt-price-row__label"><?php esc_html_e( 'Easy rider', 'tour-reference-theme' ); ?></span>
					<div class="lt-price-row__amount">
						<span class="lt-price-row__value"><?php echo esc_html( $price_easy_vnd ); ?> ₫</span>
						<span class="lt-price-row__usd">· $<?php echo esc_html( $price_easy_usd ); ?></span>
					</div>
				</div>
				<div class="lt-price-row">
					<span class="lt-price-row__label"><?php esc_html_e( 'Jeep', 'tour-reference-theme' ); ?></span>
					<div class="lt-price-row__amount">
						<span class="lt-price-row__value"><?php echo esc_html( $price_jeep_vnd ); ?> ₫</span>
						<span class="lt-price-row__usd">· $<?php echo esc_html( $price_jeep_usd ); ?></span>
					</div>
				</div>
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
						<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23e4e0da' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'><?php echo esc_attr( $t['title'] ); ?></text></svg>" alt="<?php echo esc_attr( $t['title'] ); ?>" class="tour-card__img" />
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
