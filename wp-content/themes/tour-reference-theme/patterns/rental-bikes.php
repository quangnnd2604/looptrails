<?php
/**
 * Title: Motorbike Rental Fleet
 * Slug: tour-reference-theme/rental-bikes
 * Categories: featured, gallery
 * Description: Fleet cards for motorcycle and scooter rentals with specifications and day rates.
 */
?>
<!-- wp:group {"align":"full","className":"rental-fleet-section","style":{"spacing":{"padding":{"top":"60px","bottom":"60px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull rental-fleet-section" style="padding-top:60px;padding-right:20px;padding-bottom:60px;padding-left:20px">
	<div class="rental-bikes-grid">
		<!-- Bike 1 -->
		<div class="bike-card">
			<div class="bike-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='280' viewBox='0 0 400 280'><rect fill='%23e4e0da' width='400' height='280'/><text fill='%23333' font-family='sans-serif' font-size='18' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Honda Wave 110cc</text></svg>" alt="Honda Wave Alpha" />
				<span class="bike-card__type"><?php esc_html_e( 'Semi-Automatic', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="bike-card__body">
				<h3 class="bike-card__title">Honda Wave Alpha 110cc</h3>
				<p class="bike-card__spec"><?php esc_html_e( 'Lightweight, reliable, low fuel consumption. Ideal for single riders on paved roads.', 'tour-reference-theme' ); ?></p>
				<div class="bike-card__rate">
					<span class="bike-rate-val">$10 <small>/ day</small></span>
				</div>
				<div class="wp-block-button is-style-book-now" style="margin-top:14px;">
					<a class="wp-block-button__link wp-element-button" style="width:100%;justify-content:center" href="#book"><?php esc_html_e( 'Rent This Bike', 'tour-reference-theme' ); ?></a>
				</div>
			</div>
		</div>

		<!-- Bike 2 -->
		<div class="bike-card is-popular">
			<div class="bike-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='280' viewBox='0 0 400 280'><rect fill='%23d8d2c6' width='400' height='280'/><text fill='%23333' font-family='sans-serif' font-size='18' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Honda Blade 110cc</text></svg>" alt="Honda Blade 110" />
				<span class="bike-card__type"><?php esc_html_e( 'Semi-Automatic (Stronger)', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="bike-card__body">
				<h3 class="bike-card__title">Honda Blade 110cc FI</h3>
				<p class="bike-card__spec"><?php esc_html_e( 'Fuel injected, strong disc brakes, great suspension for hill climbing with luggage.', 'tour-reference-theme' ); ?></p>
				<div class="bike-card__rate">
					<span class="bike-rate-val">$12 <small>/ day</small></span>
				</div>
				<div class="wp-block-button is-style-book-now" style="margin-top:14px;">
					<a class="wp-block-button__link wp-element-button" style="width:100%;justify-content:center" href="#book"><?php esc_html_e( 'Rent This Bike', 'tour-reference-theme' ); ?></a>
				</div>
			</div>
		</div>

		<!-- Bike 3 -->
		<div class="bike-card">
			<div class="bike-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='280' viewBox='0 0 400 280'><rect fill='%23ccc5b9' width='400' height='280'/><text fill='%23333' font-family='sans-serif' font-size='18' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Honda XR 150cc</text></svg>" alt="Honda XR 150L" />
				<span class="bike-card__type"><?php esc_html_e( 'Dual-Sport Manual', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="bike-card__body">
				<h3 class="bike-card__title">Honda XR 150L Dual-Sport</h3>
				<p class="bike-card__spec"><?php esc_html_e( 'High clearance dual-sport motorcycle. Conquers rocky passes and off-road trails effortlessly.', 'tour-reference-theme' ); ?></p>
				<div class="bike-card__rate">
					<span class="bike-rate-val">$22 <small>/ day</small></span>
				</div>
				<div class="wp-block-button is-style-book-now" style="margin-top:14px;">
					<a class="wp-block-button__link wp-element-button" style="width:100%;justify-content:center" href="#book"><?php esc_html_e( 'Rent This Bike', 'tour-reference-theme' ); ?></a>
				</div>
			</div>
		</div>

		<!-- Bike 4 -->
		<div class="bike-card">
			<div class="bike-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='280' viewBox='0 0 400 280'><rect fill='%23bfb8ab' width='400' height='280'/><text fill='%23333' font-family='sans-serif' font-size='18' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Honda CB500X 500cc</text></svg>" alt="Honda CB500X" />
				<span class="bike-card__type"><?php esc_html_e( 'Adventure Touring', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="bike-card__body">
				<h3 class="bike-card__title">Honda CB500X Adventure</h3>
				<p class="bike-card__spec"><?php esc_html_e( 'Twin-cylinder power and plush comfort for two riders with full heavy luggage sets.', 'tour-reference-theme' ); ?></p>
				<div class="bike-card__rate">
					<span class="bike-rate-val">$48 <small>/ day</small></span>
				</div>
				<div class="wp-block-button is-style-book-now" style="margin-top:14px;">
					<a class="wp-block-button__link wp-element-button" style="width:100%;justify-content:center" href="#book"><?php esc_html_e( 'Rent This Bike', 'tour-reference-theme' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /wp:group -->
