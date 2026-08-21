<?php
/**
 * Title: Hero Home
 * Slug: tour-reference-theme/hero-home
 * Categories: featured, banner
 * Description: Full-bleed hero banner with dramatic headline, subtitle, and primary booking CTA.
 */
?>
<!-- wp:group {"align":"full","className":"hero-home-section","style":{"spacing":{"padding":{"top":"140px","bottom":"120px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull hero-home-section" style="padding-top:140px;padding-right:20px;padding-bottom:120px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"24px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","alignItems":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"align":"center","className":"hero-tagline","style":{"typography":{"textTransform":"uppercase","letterSpacing":"2px","fontSize":"14px","fontWeight":"700"}},"textColor":"surface"} -->
		<p class="has-text-align-center hero-tagline has-surface-color has-text-color" style="font-size:14px;font-weight:700;letter-spacing:2px;text-transform:uppercase"><?php esc_html_e( 'The Ultimate Loop Experience', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":1,"className":"hero-headline","style":{"typography":{"fontSize":"clamp(32px, 5vw, 64px)","fontWeight":"800","lineHeight":"1.15"}},"textColor":"surface"} -->
		<h1 class="wp-block-heading has-text-align-center hero-headline has-surface-color has-text-color" style="font-size:clamp(32px, 5vw, 64px);font-weight:800;line-height:1.15"><?php esc_html_e( 'Discover Vietnam’s Most Majestic Mountain Trails', 'tour-reference-theme' ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center","className":"hero-subhead","style":{"typography":{"fontSize":"18px","lineHeight":"1.6"},"spacing":{"margin":{"bottom":"16px"}}},"textColor":"surface"} -->
		<p class="has-text-align-center hero-subhead has-surface-color has-text-color" style="font-size:18px;line-height:1.6;margin-bottom:16px"><?php esc_html_e( 'Authentic guided motorbike & 4x4 expeditions through Ha Giang and Cao Bang. Safe, unforgettable, and led by licensed local guides.', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-book-now"} -->
			<div class="wp-block-button is-style-book-now">
				<a class="wp-block-button__link wp-element-button" href="#book"><?php esc_html_e( 'Book Your Tour', 'tour-reference-theme' ); ?></a>
			</div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
