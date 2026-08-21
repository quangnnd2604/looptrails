<?php
/**
 * Title: Hero Home Full-Bleed
 * Slug: tour-reference-theme/hero-home
 * Categories: featured, banner
 * Description: Full-bleed hero banner with high-contrast headline, eyebrow badge, subtitle, and Book Now CTA.
 */
?>
<!-- wp:group {"align":"full","className":"hero-home-section","style":{"spacing":{"padding":{"top":"130px","bottom":"120px","left":"20px","right":"20px"}}},"textColor":"surface","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull hero-home-section has-surface-color has-text-color" style="padding-top:130px;padding-right:20px;padding-bottom:120px;padding-left:20px">
	<!-- Eyebrow Pill -->
	<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"20px"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:20px">
		<span class="hero-eyebrow-pill"><?php esc_html_e( 'HA GIANG & NORTHERN VIETNAM MOTORBIKE TOURS', 'tour-reference-theme' ); ?></span>
	</div>
	<!-- /wp:group -->

	<!-- Main Headline H1 -->
	<!-- wp:heading {"textAlign":"center","level":1,"className":"hero-headline","style":{"typography":{"fontSize":"clamp(36px, 5.5vw, 65px)","fontWeight":"900","lineHeight":"1.1","letterSpacing":"-0.5px"}}} -->
	<h1 class="wp-block-heading has-text-align-center hero-headline" style="font-size:clamp(36px, 5.5vw, 65px);font-weight:900;line-height:1.1;letter-spacing:-0.5px"><?php esc_html_e( 'Experience The Real Vietnam On Two Wheels', 'tour-reference-theme' ); ?></h1>
	<!-- /wp:heading -->

	<!-- Supporting Subtitle -->
	<!-- wp:paragraph {"align":"center","className":"hero-subhead","style":{"typography":{"fontSize":"clamp(16px, 2vw, 20px)","lineHeight":"1.6"},"spacing":{"margin":{"top":"20px","bottom":"36px"}}}} -->
	<p class="has-text-align-center hero-subhead" style="font-size:clamp(16px, 2vw, 20px);line-height:1.6;margin-top:20px;margin-bottom:36px;max-width:760px;margin-left:auto;margin-right:auto;"><?php esc_html_e( 'Daily departures from Ha Giang & Hanoi with licensed local easy-riders, certified safety gear, and intimate small-group adventures.', 'tour-reference-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- Dual Action Buttons -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","gap":"16px"}} -->
	<div class="wp-block-buttons" style="display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
		<!-- wp:button {"className":"is-style-book-now"} -->
		<div class="wp-block-button is-style-book-now">
			<a class="wp-block-button__link wp-element-button" href="#book" style="padding:14px 32px;font-size:16px;"><?php esc_html_e( 'Book Your Tour', 'tour-reference-theme' ); ?></a>
		</div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline-pill"} -->
		<div class="wp-block-button is-style-outline-pill">
			<a class="wp-block-button__link wp-element-button" href="#destinations" style="padding:14px 30px;font-size:16px;border:2px solid #ffffff;color:#ffffff;border-radius:25px;background:transparent;text-decoration:none;font-weight:700;"><?php esc_html_e( 'Explore Routes', 'tour-reference-theme' ); ?></a>
		</div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
