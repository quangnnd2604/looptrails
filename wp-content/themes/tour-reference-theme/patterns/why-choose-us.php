<?php
/**
 * Title: Why Choose Us & Statistics
 * Slug: tour-reference-theme/why-choose-us
 * Categories: featured, text
 * Description: 6-feature grid and dark statistics summary bar.
 */
?>
<!-- wp:group {"align":"full","className":"why-choose-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull why-choose-section" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"40px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:40px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'The Loop Standard', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Why Ride With Us', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"features-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"300px"}} -->
	<div class="wp-block-group features-grid">
		<!-- Feature 1 -->
		<div class="feature-card">
			<div class="feature-card__icon">🛡️</div>
			<h3 class="feature-card__title"><?php esc_html_e( 'Unmatched Safety Standards', 'tour-reference-theme' ); ?></h3>
			<p class="feature-card__desc"><?php esc_html_e( 'DOT/ECE full-face helmets, armored riding jackets, and protective knee/elbow guards provided on all tours.', 'tour-reference-theme' ); ?></p>
		</div>

		<!-- Feature 2 -->
		<div class="feature-card">
			<div class="feature-card__icon">🧭</div>
			<h3 class="feature-card__title"><?php esc_html_e( 'Licensed Local Guides', 'tour-reference-theme' ); ?></h3>
			<p class="feature-card__desc"><?php esc_html_e( 'Born and raised in the northern mountains, our bilingual tour leaders share authentic local stories and hidden viewpoints.', 'tour-reference-theme' ); ?></p>
		</div>

		<!-- Feature 3 -->
		<div class="feature-card">
			<div class="feature-card__icon">👥</div>
			<h3 class="feature-card__title"><?php esc_html_e( 'Small Group Experience', 'tour-reference-theme' ); ?></h3>
			<p class="feature-card__desc"><?php esc_html_e( 'We cap our groups at maximum 8 riders to maintain flexible pacing, intimate experiences, and high safety supervision.', 'tour-reference-theme' ); ?></p>
		</div>

		<!-- Feature 4 -->
		<div class="feature-card">
			<div class="feature-card__icon">🏡</div>
			<h3 class="feature-card__title"><?php esc_html_e( 'Authentic Mountain Homestays', 'tour-reference-theme' ); ?></h3>
			<p class="feature-card__desc"><?php esc_html_e( 'Handpicked ethnic Tay and Dao homestays with hot private showers, cozy bedding, and hearty family-style dinners.', 'tour-reference-theme' ); ?></p>
		</div>

		<!-- Feature 5 -->
		<div class="feature-card">
			<div class="feature-card__icon">🏍️</div>
			<h3 class="feature-card__title"><?php esc_html_e( 'Premium Fleet Maintenance', 'tour-reference-theme' ); ?></h3>
			<p class="feature-card__desc"><?php esc_html_e( 'Modern semi-automatic and manual adventure bikes serviced thoroughly before and after every single loop journey.', 'tour-reference-theme' ); ?></p>
		</div>

		<!-- Feature 6 -->
		<div class="feature-card">
			<div class="feature-card__icon">📞</div>
			<h3 class="feature-card__title"><?php esc_html_e( '24/7 Roadside & Trip Support', 'tour-reference-theme' ); ?></h3>
			<p class="feature-card__desc"><?php esc_html_e( 'Dedicated support mechanics and multilingual dispatch reachable anytime via WhatsApp and phone.', 'tour-reference-theme' ); ?></p>
		</div>
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"dark-stats-bar","style":{"spacing":{"padding":{"top":"30px","bottom":"30px","left":"24px","right":"24px"},"margin":{"top":"50px"}}},"backgroundColor":"primary","textColor":"surface","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-around"}} -->
	<div class="wp-block-group dark-stats-bar has-surface-color has-primary-background-color has-text-color has-background" style="margin-top:50px;padding-top:30px;padding-right:24px;padding-bottom:30px;padding-left:24px">
		<div class="dark-stat-item">
			<span class="dark-stat-val">99.8%</span>
			<span class="dark-stat-lbl"><?php esc_html_e( 'Safety Record', 'tour-reference-theme' ); ?></span>
		</div>
		<div class="dark-stat-item">
			<span class="dark-stat-val">10,000+</span>
			<span class="dark-stat-lbl"><?php esc_html_e( 'Completed Loops', 'tour-reference-theme' ); ?></span>
		</div>
		<div class="dark-stat-item">
			<span class="dark-stat-val">4.9★</span>
			<span class="dark-stat-lbl"><?php esc_html_e( 'Average Rating', 'tour-reference-theme' ); ?></span>
		</div>
		<div class="dark-stat-item">
			<span class="dark-stat-val">65+</span>
			<span class="dark-stat-lbl"><?php esc_html_e( 'Village Partners', 'tour-reference-theme' ); ?></span>
		</div>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
