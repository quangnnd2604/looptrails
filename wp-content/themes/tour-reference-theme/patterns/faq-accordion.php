<?php
/**
 * Title: Frequently Asked Questions
 * Slug: tour-reference-theme/faq-accordion
 * Categories: featured, text
 * Description: Accessible accordion layout for frequently asked tour questions.
 */
?>
<!-- wp:group {"align":"full","className":"faq-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"900px"}} -->
<div class="wp-block-group alignfull faq-section" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"40px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:40px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Help & Information', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Frequently Asked Questions', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<div class="faq-accordion">
		<details class="faq-item" open>
			<summary class="faq-question"><?php esc_html_e( 'What is the difference between Self-Ride and Easy Rider (Pillion)?', 'tour-reference-theme' ); ?></summary>
			<div class="faq-answer">
				<p><?php esc_html_e( 'With Self-Ride, you drive your own semi-automatic or manual motorcycle behind the tour leader. With Easy Rider (Pillion), an experienced local driver carries you on the back seat so you can sit back, relax, and take photos without driving stress.', 'tour-reference-theme' ); ?></p>
			</div>
		</details>

		<details class="faq-item">
			<summary class="faq-question"><?php esc_html_e( 'What gear and clothing should I pack for the mountain loop?', 'tour-reference-theme' ); ?></summary>
			<div class="faq-answer">
				<p><?php esc_html_e( 'We provide all certified safety gear (full-face helmets, armored jackets, knee/elbow pads, rain gear). You should bring sturdy walking shoes, warm layers (evenings get chilly), sunscreen, and a compact backpack under 10kg.', 'tour-reference-theme' ); ?></p>
			</div>
		</details>

		<details class="faq-item">
			<summary class="faq-question"><?php esc_html_e( 'Are the mountain roads safe for beginner motorbike riders?', 'tour-reference-theme' ); ?></summary>
			<div class="faq-answer">
				<p><?php esc_html_e( 'The mountain passes have sharp switchbacks and steep grades. If you do not have confident prior motorcycle experience, we strongly recommend our Easy Rider (Pillion) option for maximum safety and comfort.', 'tour-reference-theme' ); ?></p>
			</div>
		</details>

		<details class="faq-item">
			<summary class="faq-question"><?php esc_html_e( 'How do homestay accommodations and meals work on tour?', 'tour-reference-theme' ); ?></summary>
			<div class="faq-answer">
				<p><?php esc_html_e( 'We stay at traditional ethnic homestays equipped with clean private/shared rooms and modern hot showers. All authentic northern Vietnamese meals (breakfast, lunch, dinner) are included, catering to vegetarians and special dietary requests.', 'tour-reference-theme' ); ?></p>
			</div>
		</details>
	</div>
</div>
<!-- /wp:group -->
