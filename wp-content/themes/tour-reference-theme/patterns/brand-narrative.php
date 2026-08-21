<?php
/**
 * Title: Brand Narrative & Stats
 * Slug: tour-reference-theme/brand-narrative
 * Categories: featured, text
 * Description: 2-column narrative section with feature callouts, image showcase, and statistics.
 */
?>
<!-- wp:group {"align":"full","className":"brand-narrative-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"backgroundColor":"surface-header-footer","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull brand-narrative-section has-surface-header-footer-background-color has-background" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"40px","left":"60px"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
			<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
			<p class="has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Why Ride With Us', 'tour-reference-theme' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
			<h2 class="wp-block-heading" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Pioneering Northern Vietnam Loops Since 2018', 'tour-reference-theme' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","lineHeight":"1.7"}}} -->
			<p style="font-size:16px;line-height:1.7"><?php esc_html_e( 'We believe travel in the highlands should be immersive, respectful to indigenous communities, and conducted with top-tier safety equipment and small group sizes.', 'tour-reference-theme' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"narrative-stats-grid","style":{"spacing":{"blockGap":"20px","margin":{"top":"24px","bottom":"32px"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
			<div class="wp-block-group narrative-stats-grid" style="margin-top:24px;margin-bottom:32px">
				<!-- wp:group {"className":"stat-item","layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group stat-item">
					<span class="stat-number">10k+</span>
					<span class="stat-label"><?php esc_html_e( 'Happy Riders', 'tour-reference-theme' ); ?></span>
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"stat-item","layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group stat-item">
					<span class="stat-number">100%</span>
					<span class="stat-label"><?php esc_html_e( 'Local Guides', 'tour-reference-theme' ); ?></span>
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"stat-item","layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group stat-item">
					<span class="stat-number">4.9/5</span>
					<span class="stat-label"><?php esc_html_e( 'Guest Rating', 'tour-reference-theme' ); ?></span>
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-book-now"} -->
				<div class="wp-block-button is-style-book-now">
					<a class="wp-block-button__link wp-element-button" href="#book"><?php esc_html_e( 'Explore Tours', 'tour-reference-theme' ); ?></a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
			<!-- wp:group {"className":"narrative-media-wrapper"} -->
			<div class="wp-block-group narrative-media-wrapper">
				<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"narrative-image"} -->
				<figure class="wp-block-image size-large narrative-image">
					<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='600' height='450' viewBox='0 0 600 450'><rect fill='%23d0cac0' width='600' height='450'/><text fill='%23444' font-family='sans-serif' font-size='22' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Highland Trail Landscape</text></svg>" alt="Highland Trail" />
				</figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
