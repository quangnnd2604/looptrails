<?php
/**
 * Title: Editorial CTA Banner
 * Slug: tour-reference-theme/editorial-cta
 * Categories: featured, banner
 * Description: Standalone editorial call-to-action banner.
 */
?>
<!-- wp:group {"align":"full","className":"editorial-cta-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1000px"}} -->
<div class="wp-block-group alignfull editorial-cta-section" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<div class="editorial-cta-card">
		<!-- wp:group {"style":{"spacing":{"blockGap":"20px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","alignItems":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}},"textColor":"surface"} -->
			<h2 class="wp-block-heading has-text-align-center has-surface-color has-text-color" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Ready to Conquer the Northern Loop?', 'tour-reference-theme' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"17px","lineHeight":"1.6"}},"textColor":"surface"} -->
			<p class="has-text-align-center has-surface-color has-text-color" style="font-size:17px;line-height:1.6;max-width:600px"><?php esc_html_e( 'Lock in your preferred departure date and vehicle tier with our flexible booking and instant confirmation.', 'tour-reference-theme' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-book-now"} -->
				<div class="wp-block-button is-style-book-now">
					<a class="wp-block-button__link wp-element-button" href="#book"><?php esc_html_e( 'Book Your Adventure Now', 'tour-reference-theme' ); ?></a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:group -->
