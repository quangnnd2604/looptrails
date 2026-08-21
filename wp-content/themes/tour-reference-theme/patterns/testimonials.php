<?php
/**
 * Title: Guest Testimonials & Reviews
 * Slug: tour-reference-theme/testimonials
 * Categories: featured, text
 * Description: 3-column review card presentation with platform ratings and customer quotes.
 */
?>
<!-- wp:group {"align":"full","className":"testimonials-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"backgroundColor":"surface-header-footer","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull testimonials-section has-surface-header-footer-background-color has-background" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"40px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:40px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Verified Rider Stories', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Loved by Adventurers Worldwide', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"reviews-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"300px"}} -->
	<div class="wp-block-group reviews-grid">
		<!-- Review 1 -->
		<div class="review-card">
			<div class="review-card__stars">★★★★★</div>
			<p class="review-card__text"><?php esc_html_e( '“The trip of a lifetime! Our easy-rider guide was incredibly safe and showed us jaw-dropping viewpoints we never would have found alone.”', 'tour-reference-theme' ); ?></p>
			<div class="review-card__author">
				<span class="review-card__name">Alex M.</span>
				<span class="review-card__origin">United Kingdom</span>
			</div>
		</div>

		<!-- Review 2 -->
		<div class="review-card">
			<div class="review-card__stars">★★★★★</div>
			<p class="review-card__text"><?php esc_html_e( '“Impeccable organization from hotel pickup to the mountain homestays. Food was delicious and bikes were in pristine condition.”', 'tour-reference-theme' ); ?></p>
			<div class="review-card__author">
				<span class="review-card__name">Sophie L.</span>
				<span class="review-card__origin">Germany</span>
			</div>
		</div>

		<!-- Review 3 -->
		<div class="review-card">
			<div class="review-card__stars">★★★★★</div>
			<p class="review-card__text"><?php esc_html_e( '“Best motorbike loop in Southeast Asia. The views along Ma Pi Leng Pass are surreal. 10/10 recommend this tour team!”', 'tour-reference-theme' ); ?></p>
			<div class="review-card__author">
				<span class="review-card__name">Marcus K.</span>
				<span class="review-card__origin">Australia</span>
			</div>
		</div>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
