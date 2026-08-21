<?php
/**
 * Title: Blog & Travel Articles Teaser
 * Slug: tour-reference-theme/blog-teaser
 * Categories: featured, posts
 * Description: 3-column article cards showcasing travel tips, motorbike guides, and route insights.
 */

$blog_svg_1 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='260' viewBox='0 0 400 260'><rect fill='#c5beb2' width='400' height='260'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Ha Giang Weather Guide</text></svg>";
$blog_svg_2 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='260' viewBox='0 0 400 260'><rect fill='#ded8ce' width='400' height='260'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Packing Checklist</text></svg>";
$blog_svg_3 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='260' viewBox='0 0 400 260'><rect fill='#b8b0a2' width='400' height='260'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Self-Ride vs Easy Rider</text></svg>";
?>
<!-- wp:group {"align":"full","className":"blog-teaser-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull blog-teaser-section" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"40px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:40px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Loop Knowledge & Guides', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Essential Travel Articles', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- 3-Column Blog Cards Grid -->
	<div class="blog-teaser-grid">
		<!-- Article 1 -->
		<article class="blog-card">
			<div class="blog-card__media">
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $blog_svg_1 ) ); ?>" alt="<?php esc_attr_e( 'Weather Guide', 'tour-reference-theme' ); ?>" />
				<span class="blog-card__badge"><?php esc_html_e( 'Travel Tips', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="blog-card__body">
				<span class="blog-card__meta">August 2026 · 5 min read</span>
				<h3 class="blog-card__title"><a href="#blog-1"><?php esc_html_e( 'Best Time to Ride the Ha Giang Loop: Month-by-Month Guide', 'tour-reference-theme' ); ?></a></h3>
				<p class="blog-card__excerpt"><?php esc_html_e( 'From golden rice terraces in September to blooming buckwheat flowers in November, discover the ideal season for your highland trip.', 'tour-reference-theme' ); ?></p>
				<a href="#blog-1" class="blog-card__link"><?php esc_html_e( 'Read Article →', 'tour-reference-theme' ); ?></a>
			</div>
		</article>

		<!-- Article 2 -->
		<article class="blog-card">
			<div class="blog-card__media">
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $blog_svg_2 ) ); ?>" alt="<?php esc_attr_e( 'Packing Guide', 'tour-reference-theme' ); ?>" />
				<span class="blog-card__badge"><?php esc_html_e( 'Checklist', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="blog-card__body">
				<span class="blog-card__meta">July 2026 · 4 min read</span>
				<h3 class="blog-card__title"><a href="#blog-2"><?php esc_html_e( 'What to Pack for Northern Vietnam Motorbike Loops', 'tour-reference-theme' ); ?></a></h3>
				<p class="blog-card__excerpt"><?php esc_html_e( 'Everything you need in a lightweight 10kg backpack: layers, footwear, medication, and essential rain gear for the passes.', 'tour-reference-theme' ); ?></p>
				<a href="#blog-2" class="blog-card__link"><?php esc_html_e( 'Read Article →', 'tour-reference-theme' ); ?></a>
			</div>
		</article>

		<!-- Article 3 -->
		<article class="blog-card">
			<div class="blog-card__media">
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $blog_svg_3 ) ); ?>" alt="<?php esc_attr_e( 'Riding Styles', 'tour-reference-theme' ); ?>" />
				<span class="blog-card__badge"><?php esc_html_e( 'Rider Guide', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="blog-card__body">
				<span class="blog-card__meta">June 2026 · 6 min read</span>
				<h3 class="blog-card__title"><a href="#blog-3"><?php esc_html_e( 'Self-Driving vs. Easy Rider: Which Option Suits You Best?', 'tour-reference-theme' ); ?></a></h3>
				<p class="blog-card__excerpt"><?php esc_html_e( 'A transparent comparison of driving requirements, mountain safety conditions, physical fatigue, and photography opportunities.', 'tour-reference-theme' ); ?></p>
				<a href="#blog-3" class="blog-card__link"><?php esc_html_e( 'Read Article →', 'tour-reference-theme' ); ?></a>
			</div>
		</article>
	</div>
</div>
<!-- /wp:group -->
