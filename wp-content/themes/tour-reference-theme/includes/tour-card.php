<?php
/**
 * Server-side rendering helper for Tour Cards.
 * Consumes tour post meta from tour-booking-core schema.
 */

defined( 'ABSPATH' ) || exit;

function tour_theme_render_tour_card( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return '';
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	$title     = get_the_title( $post_id );
	$permalink = get_permalink( $post_id );
	$thumbnail = get_the_post_thumbnail_url( $post_id, 'large' );
	if ( ! $thumbnail ) {
		$thumbnail = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect fill="%23e4e0da" width="600" height="400"/><text fill="%236e6b7b" font-family="sans-serif" font-size="20" font-weight="bold" x="50%" y="50%" text-anchor="middle" dominant-baseline="middle">Tour Photo</text></svg>';
	}

	$days   = (int) get_post_meta( $post_id, 'tbc_duration_days', true );
	$nights = (int) get_post_meta( $post_id, 'tbc_duration_nights', true );
	$badge  = get_post_meta( $post_id, 'tbc_badge', true );
	$rating = get_post_meta( $post_id, 'tbc_rating_value', true );
	$count  = (int) get_post_meta( $post_id, 'tbc_rating_count', true );

	$duration_text = '';
	if ( $days > 0 ) {
		$duration_text = sprintf( __( '%1$d Days %2$d Nights', 'tour-reference-theme' ), $days, $nights );
	}

	// Fetch vehicle pricing options associated with this tour
	$vehicle_query = new WP_Query(
		array(
			'post_type'      => 'vehicle_option',
			'posts_per_page' => 4,
			'meta_query'     => array(
				array(
					'key'   => 'tbc_tour_id',
					'value' => $post_id,
				),
			),
		)
	);

	$prices = array();
	if ( $vehicle_query->have_posts() ) {
		while ( $vehicle_query->have_posts() ) {
			$vehicle_query->the_post();
			$v_id   = get_the_ID();
			$v_type = get_post_meta( $v_id, 'tbc_vehicle_type', true );
			$v_vnd  = (int) get_post_meta( $v_id, 'tbc_price_vnd', true );
			if ( $v_type && $v_vnd > 0 ) {
				$v_label  = ucfirst( str_replace( '_', ' ', $v_type ) );
				$prices[] = array(
					'label'     => $v_label,
					'price_vnd' => $v_vnd,
					'price_usd' => round( $v_vnd / 25000 ),
				);
			}
		}
		wp_reset_postdata();
	}

	// Fallback prices if no vehicle options exist
	if ( empty( $prices ) ) {
		$prices[] = array(
			'label'     => __( 'Self-Ride', 'tour-reference-theme' ),
			'price_vnd' => 3500000,
			'price_usd' => 140,
		);
		$prices[] = array(
			'label'     => __( 'Easy Rider (Pillion)', 'tour-reference-theme' ),
			'price_vnd' => 5200000,
			'price_usd' => 208,
		);
	}

	ob_start();
	?>
	<article class="tour-card" data-tour-id="<?php echo esc_attr( $post_id ); ?>">
		<div class="tour-card__media">
			<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="tour-card__image" loading="lazy" />
			<?php if ( ! empty( $badge ) ) : ?>
				<span class="tour-card__badge"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $duration_text ) ) : ?>
				<span class="tour-card__duration"><?php echo esc_html( $duration_text ); ?></span>
			<?php endif; ?>
		</div>
		<div class="tour-card__body">
			<?php if ( $rating > 0 ) : ?>
				<div class="tour-card__rating">
					<span class="tour-card__stars">★</span>
					<span class="tour-card__score"><?php echo esc_html( number_format( (float) $rating, 1 ) ); ?></span>
					<?php if ( $count > 0 ) : ?>
						<span class="tour-card__count">(<?php echo esc_html( $count ); ?>)</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<h3 class="tour-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>

			<div class="tour-card__prices">
				<?php foreach ( $prices as $p ) : ?>
					<div class="tour-card__price-row">
						<span class="tour-card__price-label"><?php echo esc_html( $p['label'] ); ?></span>
						<span class="tour-card__price-value">$<?php echo esc_html( $p['price_usd'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="tour-card__actions">
				<div class="wp-block-button is-style-book-now">
					<a href="<?php echo esc_url( $permalink . '#book' ); ?>" class="wp-block-button__link wp-element-button">
						<?php esc_html_e( 'Book Now', 'tour-reference-theme' ); ?>
					</a>
				</div>
				<a href="<?php echo esc_url( $permalink ); ?>" class="tour-card__details-link">
					<?php esc_html_e( 'Details →', 'tour-reference-theme' ); ?>
				</a>
			</div>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Dynamic block rendering callback for the tour grid block / shortcode.
 */
function tour_theme_render_featured_tours( $attributes = array() ) {
	$count = isset( $attributes['postsPerPage'] ) ? (int) $attributes['postsPerPage'] : 6;

	$args = array(
		'post_type'      => 'tour',
		'posts_per_page' => $count,
		'post_status'    => 'publish',
	);

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '<p class="tour-grid__empty">' . esc_html__( 'No tours found.', 'tour-reference-theme' ) . '</p>';
	}

	$output = '<div class="tour-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		$output .= tour_theme_render_tour_card( get_the_ID() );
	}
	wp_reset_postdata();
	$output .= '</div>';

	return $output;
}
add_shortcode( 'tour_featured_grid', 'tour_theme_render_featured_tours' );
