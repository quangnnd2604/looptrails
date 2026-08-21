<?php
/**
 * Title: Top Destinations & Essentials
 * Slug: tour-reference-theme/top-destinations-essentials
 * Categories: featured, gallery
 * Description: Tabbed showcase for highland destinations, routes, and travel essentials with 8 destination cards.
 */

$dest_svg_1 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#ccc5b9' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Ma Pi Leng Pass</text></svg>";
$dest_svg_2 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#b8b0a2' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Dong Van Geopark</text></svg>";
$dest_svg_3 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#c5beb2' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Tu San Canyon</text></svg>";
$dest_svg_4 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#ded8ce' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Tham Ma Pass</text></svg>";
$dest_svg_5 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#d0cac0' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Du Gia Village</text></svg>";
$dest_svg_6 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#bfb8ab' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Lung Cu Flag Tower</text></svg>";
$dest_svg_7 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#e4e0da' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Vuong Palace</text></svg>";
$dest_svg_8 = "<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect fill='#d8d2c6' width='400' height='300'/><text fill='#333' font-family='sans-serif' font-size='16' font-weight='bold' x='50%' y='50%' text-anchor='middle'>Quan Ba Twin Mountains</text></svg>";
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

	<!-- Panel 1: Destinations (8-Card Destination Grid: 4 cols x 2 rows) -->
	<div class="destinations-tab-panel destinations-grid lt-destinations__grid is-active" data-panel="destinations">
		<!-- Destination Card 1 -->
		<div class="destination-card lt-grid__item">
			<div class="destination-card__media">
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_1 ) ); ?>" alt="<?php esc_attr_e( 'Ma Pi Leng Pass', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_2 ) ); ?>" alt="<?php esc_attr_e( 'Dong Van Geopark', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_3 ) ); ?>" alt="<?php esc_attr_e( 'Tu San Canyon', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_4 ) ); ?>" alt="<?php esc_attr_e( 'Tham Ma Pass', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_5 ) ); ?>" alt="<?php esc_attr_e( 'Du Gia Village', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_6 ) ); ?>" alt="<?php esc_attr_e( 'Lung Cu Flag Tower', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_7 ) ); ?>" alt="<?php esc_attr_e( 'Vuong Palace', 'tour-reference-theme' ); ?>" />
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
				<img src="<?php echo esc_attr( 'data:image/svg+xml,' . rawurlencode( $dest_svg_8 ) ); ?>" alt="<?php esc_attr_e( 'Quan Ba Twin Mountains', 'tour-reference-theme' ); ?>" />
				<span class="destination-card__tag"><?php esc_html_e( 'Heaven Gate', 'tour-reference-theme' ); ?></span>
			</div>
			<div class="destination-card__body">
				<h3 class="destination-card__title"><?php esc_html_e( 'Quản Bạ Fairy Mountains', 'tour-reference-theme' ); ?></h3>
				<p class="destination-card__desc"><?php esc_html_e( 'Two perfectly symmetrical limestone cones rising majestically above the Tam Son valley.', 'tour-reference-theme' ); ?></p>
			</div>
		</div>
	</div>

	<!-- Panel 2: Itinerary & Route -->
	<div class="destinations-tab-panel destinations-grid lt-destinations__grid" data-panel="itinerary" style="display:none;">
		<div class="destination-card lt-grid__item">
			<div class="destination-card__body">
				<span class="destination-card__tag">3-Day Classic</span>
				<h3 class="destination-card__title">Ha Giang – Dong Van – Du Gia Loop</h3>
				<p class="destination-card__desc">350 km through Bac Sum, Quan Ba, Tham Ma, Ma Pi Leng, and back via Du Gia valley.</p>
			</div>
		</div>
		<div class="destination-card lt-grid__item">
			<div class="destination-card__body">
				<span class="destination-card__tag">4-Day Extended</span>
				<h3 class="destination-card__title">Ha Giang – Lung Cu – Cao Bang Connection</h3>
				<p class="destination-card__desc">Includes frontier border marker, Ban Gioc waterfall transit, and Pac Bo cave history.</p>
			</div>
		</div>
	</div>

	<!-- Panel 3: Transport Options -->
	<div class="destinations-tab-panel destinations-grid lt-destinations__grid" data-panel="transport" style="display:none;">
		<div class="destination-card lt-grid__item">
			<div class="destination-card__body">
				<span class="destination-card__tag">Self-Drive</span>
				<h3 class="destination-card__title">Honda Semi-Auto &amp; Manual Fleet</h3>
				<p class="destination-card__desc">Honda Wave 110cc, Blade 110cc, or XR150L with helmets and repair toolkits.</p>
			</div>
		</div>
		<div class="destination-card lt-grid__item">
			<div class="destination-card__body">
				<span class="destination-card__tag">Easy Rider</span>
				<h3 class="destination-card__title">Local Experienced Motorbike Guides</h3>
				<p class="destination-card__desc">Sit back and enjoy the views with our English-speaking mountain navigation experts.</p>
			</div>
		</div>
	</div>

	<!-- Panel 4: Accommodation -->
	<div class="destinations-tab-panel destinations-grid lt-destinations__grid" data-panel="accommodation" style="display:none;">
		<div class="destination-card lt-grid__item">
			<div class="destination-card__body">
				<span class="destination-card__tag">Authentic</span>
				<h3 class="destination-card__title">Ethnic Homestays &amp; Family Dinners</h3>
				<p class="destination-card__desc">Stay in traditional stilt houses in Yen Minh and Du Gia with hot showers and warm hospitality.</p>
			</div>
		</div>
		<div class="destination-card lt-grid__item">
			<div class="destination-card__body">
				<span class="destination-card__tag">Eco-Lodge</span>
				<h3 class="destination-card__title">Mountain View Lodges &amp; Boutique Hotels</h3>
				<p class="destination-card__desc">Upgraded private rooms overlooking karst valleys in Dong Van and Meo Vac.</p>
			</div>
		</div>
	</div>
</div>
<!-- /wp:group -->
