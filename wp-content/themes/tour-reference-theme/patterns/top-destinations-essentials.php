<?php
/**
 * Title: Top Destinations & Essentials
 * Slug: tour-reference-theme/top-destinations-essentials
 * Categories: featured, gallery
 * Description: Tabbed showcase for highland destinations, routes, and travel essentials.
 */
?>
<!-- wp:group {"align":"full","className":"destinations-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull destinations-section" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"40px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:40px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Highland Highlights', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Top Destinations & Essentials', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"destinations-grid","layout":{"type":"grid","columnCount":4,"minimumColumnWidth":"260px"}} -->
	<div class="wp-block-group destinations-grid">
		<!-- Destination Card 1 -->
		<div class="destination-card">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23ccc5b9' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Ma Pi Leng Pass</text></svg>" alt="Ma Pi Leng Pass" />
				<span class="destination-card__tag"><?php esc_html_e( 'Iconic Pass', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Mã Pí Lèng Pass', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'One of the four great mountain passes of Vietnam, overlooking the emerald Nho Que River.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 2 -->
		<div class="destination-card">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23b8b0a2' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Dong Van Geopark</text></svg>" alt="Dong Van Geopark" />
				<span class="destination-card__tag"><?php esc_html_e( 'UNESCO Heritage', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Đồng Văn Karst Plateau', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Spectacular limestone peaks and historic ancient towns steeped in ethnic H’mong culture.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 3 -->
		<div class="destination-card">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23c5beb2' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Ban Gioc Waterfall</text></svg>" alt="Ban Gioc Waterfall" />
				<span class="destination-card__tag"><?php esc_html_e( 'Waterfalls', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Bản Giốc Waterfalls', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Majestic multi-tiered waterfall nestled on the northern frontier surrounded by lush karst hills.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 4 -->
		<div class="destination-card">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23ded8ce' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Nho Que River</text></svg>" alt="Nho Que River" />
				<span class="destination-card__tag"><?php esc_html_e( 'Boat Tour', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Nho Quế Canyon Boat Ride', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Glide through Tu San, Southeast Asia’s deepest canyon, on crystal turquoise mountain waters.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
