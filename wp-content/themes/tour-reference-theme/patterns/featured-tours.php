<?php
/**
 * Title: Featured Tours Grid
 * Slug: tour-reference-theme/featured-tours
 * Categories: featured, query
 * Description: Section displaying a 3-column responsive grid of featured adventure tours.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"60px","bottom":"60px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull" style="padding-top:60px;padding-right:20px;padding-bottom:60px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"32px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:32px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Handcrafted Adventures', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"36px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:36px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Popular Loop Tours', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<?php echo tour_theme_render_featured_tours(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
<!-- /wp:group -->
