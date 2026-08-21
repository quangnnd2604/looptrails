<?php
/**
 * Setup Admin-Editable Pages (Home, About, Contact, Motorbike Rental)
 * Populates real block markup into post_content for each page.
 */

require_once __DIR__ . '/../wp-load.php';

// Helper to get pattern content by slug or file
function get_theme_pattern_markup( $filename ) {
	$file = get_template_directory() . '/patterns/' . $filename;
	if ( ! file_exists( $file ) ) {
		return '';
	}
	ob_start();
	include $file;
	return trim( ob_get_clean() );
}

echo "=== 1. Setup Home Page ===\n";
$home_page = get_page_by_path( 'home' );
if ( ! $home_page ) {
	$home_page = get_page_by_title( 'Trang chủ' );
}

$hero_content       = get_theme_pattern_markup( 'hero-home.php' );
$featured_content   = get_theme_pattern_markup( 'featured-tours.php' );
$narrative_content  = get_theme_pattern_markup( 'brand-narrative.php' );
$destinations_content = get_theme_pattern_markup( 'top-destinations-essentials.php' );
$why_content        = get_theme_pattern_markup( 'why-choose-us.php' );
$testimonials_content = get_theme_pattern_markup( 'testimonials.php' );
$editorial_content  = get_theme_pattern_markup( 'editorial-cta.php' );
$booking_content    = get_theme_pattern_markup( 'booking-section.php' );
$blog_content       = get_theme_pattern_markup( 'blog-teaser.php' );
$faq_content        = get_theme_pattern_markup( 'faq-accordion.php' );

$home_blocks = implode( "\n\n", array(
	$hero_content,
	$featured_content,
	$narrative_content,
	$destinations_content,
	$why_content,
	$testimonials_content,
	$editorial_content,
	$booking_content,
	$blog_content,
	$faq_content,
) );

if ( ! $home_page ) {
	$home_id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_title'   => 'Trang chủ',
		'post_name'    => 'home',
		'post_status'  => 'publish',
		'post_content' => $home_blocks,
	) );
	echo "Created new Home page (ID: {$home_id})\n";
} else {
	$home_id = $home_page->ID;
	wp_update_post( array(
		'ID'           => $home_id,
		'post_title'   => 'Trang chủ',
		'post_content' => $home_blocks,
	) );
	echo "Updated existing Home page (ID: {$home_id})\n";
}

// Update Reading settings to static home page
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
echo "Set show_on_front=page, page_on_front={$home_id}\n";


echo "\n=== 2. Setup About Page (ID 300) ===\n";
$about_hero = '<!-- wp:group {"align":"full","className":"about-hero-banner","style":{"spacing":{"padding":{"top":"100px","bottom":"60px","left":"20px","right":"20px"}}},"backgroundColor":"primary","textColor":"surface","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull about-hero-banner has-surface-color has-primary-background-color has-text-color has-background" style="padding-top:100px;padding-right:20px;padding-bottom:60px;padding-left:20px">
	<!-- wp:heading {"level":1,"textAlign":"center","style":{"typography":{"fontSize":"clamp(32px, 4.5vw, 54px)","fontWeight":"800","lineHeight":"1.15"}}} -->
	<h1 class="wp-block-heading has-text-align-center" style="font-size:clamp(32px, 4.5vw, 54px);font-weight:800;line-height:1.15">About Our Team &amp; Tours</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"18px","lineHeight":"1.6"},"spacing":{"margin":{"top":"12px"}}}} -->
	<p class="has-text-align-center" style="font-size:18px;line-height:1.6;margin-top:12px;max-width:700px;margin-left:auto;margin-right:auto;">Born in the mountains of northern Vietnam, we are a local operator dedicated to safe, respectful, and transformative motorcycle journeys.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';

$about_blocks = implode( "\n\n", array(
	$about_hero,
	$narrative_content,
	$why_content,
	$testimonials_content,
	$editorial_content,
) );

$about_page = get_page_by_path( 'about' );
$about_id = $about_page ? $about_page->ID : 300;
wp_update_post( array(
	'ID'           => $about_id,
	'post_title'   => 'About',
	'post_name'    => 'about',
	'post_status'  => 'publish',
	'post_content' => $about_blocks,
) );
echo "Updated About page (ID: {$about_id})\n";


echo "\n=== 3. Setup Contact Page (ID 301) ===\n";
$contact_hero = '<!-- wp:group {"align":"full","className":"contact-hero-banner","style":{"spacing":{"padding":{"top":"100px","bottom":"60px","left":"20px","right":"20px"}}},"backgroundColor":"primary","textColor":"surface","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull contact-hero-banner has-surface-color has-primary-background-color has-text-color has-background" style="padding-top:100px;padding-right:20px;padding-bottom:60px;padding-left:20px">
	<!-- wp:heading {"level":1,"textAlign":"center","style":{"typography":{"fontSize":"clamp(32px, 4.5vw, 54px)","fontWeight":"800","lineHeight":"1.15"}}} -->
	<h1 class="wp-block-heading has-text-align-center" style="font-size:clamp(32px, 4.5vw, 54px);font-weight:800;line-height:1.15">Contact Our Team</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"18px","lineHeight":"1.6"},"spacing":{"margin":{"top":"12px"}}}} -->
	<p class="has-text-align-center" style="font-size:18px;line-height:1.6;margin-top:12px;max-width:700px;margin-left:auto;margin-right:auto;">Have questions about routes, weather, or custom private tours? We reply promptly on WhatsApp and email.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';

$contact_grid = '<!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"60px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group" style="padding-top:60px;padding-right:20px;padding-bottom:60px;padding-left:20px">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"40px","left":"50px"}}}} -->
	<div class="wp-block-columns">
		<!-- Left Column: Info Box -->
		<!-- wp:column {"width":"42%"} -->
		<div class="wp-block-column" style="flex-basis:42%">
			<!-- wp:html -->
			<div class="contact-info-panel" style="background:#fdfbf7;border:1px solid #eee8dc;border-radius:14px;padding:32px;">
				<h3 style="font-family:var(--wp--preset--font-family--heading);font-size:22px;font-weight:800;margin-top:0;margin-bottom:20px;">Tour Basecamp</h3>

				<div class="contact-info-item" style="margin-bottom:18px;">
					<strong>📍 Location:</strong>
					<p style="margin:4px 0 0;font-size:15px;color:#555;">Ha Giang City, Vietnam</p>
				</div>

				<div class="contact-info-item" style="margin-bottom:18px;">
					<strong>💬 WhatsApp / Hotline:</strong>
					<p style="margin:4px 0 0;font-size:15px;color:#555;">+84 123 456 789</p>
				</div>

				<div class="contact-info-item" style="margin-bottom:18px;">
					<strong>✉️ Email:</strong>
					<p style="margin:4px 0 0;font-size:15px;color:#555;">contact@example.com</p>
				</div>

				<div class="contact-info-item">
					<strong>⏰ Opening Hours:</strong>
					<p style="margin:4px 0 0;font-size:15px;color:#555;">07:00 – 22:00 (Daily)</p>
				</div>
			</div>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->

		<!-- Right Column: Form -->
		<!-- wp:column {"width":"58%"} -->
		<div class="wp-block-column" style="flex-basis:58%">
			<!-- wp:html -->
			<form class="contact-form" style="background:#ffffff;border:1px solid #e5e5e5;border-radius:14px;padding:32px;box-shadow:0 4px 15px rgba(0,0,0,0.03);">
				<h3 style="font-family:var(--wp--preset--font-family--heading);font-size:22px;font-weight:800;margin-top:0;margin-bottom:20px;">Send a Message</h3>

				<div style="margin-bottom:16px;">
					<label style="display:block;font-weight:700;font-size:14px;margin-bottom:6px;">Your Name *</label>
					<input type="text" required style="width:100%;padding:10px 14px;border:1px solid #ccc;border-radius:8px;font-size:15px;" placeholder="Full Name" />
				</div>

				<div style="margin-bottom:16px;">
					<label style="display:block;font-weight:700;font-size:14px;margin-bottom:6px;">Email Address *</label>
					<input type="email" required style="width:100%;padding:10px 14px;border:1px solid #ccc;border-radius:8px;font-size:15px;" placeholder="email@example.com" />
				</div>

				<div style="margin-bottom:16px;">
					<label style="display:block;font-weight:700;font-size:14px;margin-bottom:6px;">Message *</label>
					<textarea rows="4" required style="width:100%;padding:10px 14px;border:1px solid #ccc;border-radius:8px;font-size:15px;" placeholder="Tell us about your trip plans..."></textarea>
				</div>

				<button type="submit" style="width:100%;padding:14px;background:#ff6602;color:#ffffff;border:none;border-radius:8px;font-family:Montserrat,sans-serif;font-weight:700;font-size:15px;cursor:pointer;box-shadow:2px 3px 0px 0px #36343b;">Send Message</button>
			</form>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->';

$contact_blocks = implode( "\n\n", array(
	$contact_hero,
	$contact_grid,
) );

$contact_page = get_page_by_path( 'contact' );
$contact_id = $contact_page ? $contact_page->ID : 301;
wp_update_post( array(
	'ID'           => $contact_id,
	'post_title'   => 'Contact',
	'post_name'    => 'contact',
	'post_status'  => 'publish',
	'post_content' => $contact_blocks,
) );
echo "Updated Contact page (ID: {$contact_id})\n";


echo "\n=== 4. Setup Motorbike Rental Page (ID 302) ===\n";
$rental_hero = '<!-- wp:group {"align":"full","className":"rental-hero-banner","style":{"spacing":{"padding":{"top":"100px","bottom":"60px","left":"20px","right":"20px"}}},"backgroundColor":"primary","textColor":"surface","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull rental-hero-banner has-surface-color has-primary-background-color has-text-color has-background" style="padding-top:100px;padding-right:20px;padding-bottom:60px;padding-left:20px">
	<!-- wp:heading {"level":1,"textAlign":"center","style":{"typography":{"fontSize":"clamp(32px, 4.5vw, 54px)","fontWeight":"800","lineHeight":"1.15"}}} -->
	<h1 class="wp-block-heading has-text-align-center" style="font-size:clamp(32px, 4.5vw, 54px);font-weight:800;line-height:1.15">Ha Giang Motorbike Rental</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"18px","lineHeight":"1.6"},"spacing":{"margin":{"top":"12px"}}}} -->
	<p class="has-text-align-center" style="font-size:18px;line-height:1.6;margin-top:12px;max-width:700px;margin-left:auto;margin-right:auto;">Reliable, well-maintained semi-automatic, manual, and adventure touring bikes for your self-guided loop journey.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->';

$rental_fleet = get_theme_pattern_markup( 'rental-bikes.php' );

$rental_reqs = '<!-- wp:group {"style":{"spacing":{"padding":{"top":"30px","bottom":"50px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1000px"}} -->
<div class="wp-block-group" style="padding-top:30px;padding-right:20px;padding-bottom:50px;padding-left:20px">
	<div class="rental-requirements-card" style="background:#fdfbf7;border:1px solid #eee8dc;border-radius:14px;padding:32px;">
		<h2 class="wp-block-heading" style="font-size:24px;font-weight:700;margin-bottom:16px;">Rental Requirements &amp; What’s Included</h2>
		<ul style="line-height:1.8;font-size:15px;color:#444;">
			<li>Valid International Driving Permit (IDP) or valid Vietnamese driver’s license</li>
			<li>Passport or deposit required upon bike pickup in Ha Giang City</li>
			<li>Free DOT-certified full-face helmet, phone mount, bungee straps, and rain gear provided</li>
			<li>24/7 roadside emergency breakdown assistance &amp; replacement bike support</li>
		</ul>
	</div>
</div>
<!-- /wp:group -->';

$rental_booking_markup = get_theme_pattern_markup( 'rental-booking-form.php' );

$rental_blocks = implode( "\n\n", array(
	$rental_hero,
	$rental_fleet,
	$rental_reqs,
	$rental_booking_markup,
	$faq_content,
) );

$rental_page = get_page_by_path( 'motorbike-rental' );
$rental_id = $rental_page ? $rental_page->ID : 302;
wp_update_post( array(
	'ID'           => $rental_id,
	'post_title'   => 'Motorbike Rental',
	'post_name'    => 'motorbike-rental',
	'post_status'  => 'publish',
	'post_content' => $rental_blocks,
) );
echo "Updated Motorbike Rental page (ID: {$rental_id})\n";

echo "\n=== All Pages Populated Successfully ===\n";
