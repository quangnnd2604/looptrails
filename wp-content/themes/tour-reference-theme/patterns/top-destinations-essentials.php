<?php
/**
 * Title: Top Destinations & Essentials
 * Slug: tour-reference-theme/top-destinations-essentials
 * Categories: featured, gallery
 * Description: Tabbed showcase for highland destinations, routes, and travel essentials with 8 destination cards.
 */
?>
<!-- wp:group {"align":"full","className":"destinations-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull destinations-section" id="destinations" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"30px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:30px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Highland Highlights', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Top Destinations & Essentials', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- Tab controls matching reference UI -->
	<div class="destinations-tabs-nav">
		<button type="button" class="tab-btn is-active" data-tab="destinations"><?php esc_html_e( 'Destinations', 'tour-reference-theme' ); ?></button>
		<button type="button" class="tab-btn" data-tab="itinerary"><?php esc_html_e( 'Itinerary & Route', 'tour-reference-theme' ); ?></button>
		<button type="button" class="tab-btn" data-tab="transport"><?php esc_html_e( 'Transport Options', 'tour-reference-theme' ); ?></button>
		<button type="button" class="tab-btn" data-tab="accommodation"><?php esc_html_e( 'Accommodation', 'tour-reference-theme' ); ?></button>
	</div>

	<!-- 8-Card Destination Grid (4 cols x 2 rows) matching reference measurements -->
	<div class="destinations-grid lt-destinations__grid">
		<!-- Destination Card 1 -->
		<div class="destination-card lt-grid__item">
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
		<div class="destination-card lt-grid__item">
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
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23c5beb2' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Tu San Canyon</text></svg>" alt="Nho Que River" />
				<span class="destination-card__tag"><?php esc_html_e( 'Deepest Canyon', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Tu Sản Canyon & River', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Glide through Southeast Asia’s deepest canyon on crystal turquoise mountain waters.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 4 -->
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23ded8ce' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Tham Ma Pass</text></svg>" alt="Tham Ma Slope" />
				<span class="destination-card__tag"><?php esc_html_e( 'Nine Turns', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Thẩm Mã Winding Pass', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Legendary serpentine road featuring nine dramatic sharp curves carved into vertical cliffs.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 5 -->
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23d0cac0' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Du Gia Village</text></svg>" alt="Du Gia Waterfall" />
				<span class="destination-card__tag"><?php esc_html_e( 'Hidden Oasis', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Du Già Waterfall Valley', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Peaceful ethnic Tay village with refreshing natural swimming holes and mountain streams.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 6 -->
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23bfb8ab' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Lung Cu Flag Tower</text></svg>" alt="Lung Cu Flag Tower" />
				<span class="destination-card__tag"><?php esc_html_e( 'Northern Point', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Lũng Cú Flag Point', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'The northernmost tip of Vietnam offering 360-degree panoramic frontier border views.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 7 -->
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23e4e0da' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Vuong Palace</text></svg>" alt="Hmong King Palace" />
				<span class="destination-card__tag"><?php esc_html_e( 'History', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'H’mong King Palace', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Century-old stone fortress blending traditional Chinese and European colonial architecture.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>

		<!-- Destination Card 8 -->
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='%23d8d2c6' width='400' height='300'/><text fill='%23333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Quan Ba Twin Mountains</text></svg>" alt="Quan Ba Fairy Bosom" />
				<span class="destination-card__tag"><?php esc_html_e( 'Heaven Gate', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Quản Bạ Fairy Mountains', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Two perfectly symmetrical limestone cones rising majestically above the Tam Son valley.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>
	</div>
</div>
<!-- /wp:group -->
