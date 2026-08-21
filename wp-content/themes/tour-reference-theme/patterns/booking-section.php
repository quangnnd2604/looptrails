<?php
/**
 * Title: Home Booking Section
 * Slug: tour-reference-theme/booking-section
 * Categories: featured, forms
 * Description: Interactive booking interface with tour selector, vehicle options, dates, live summary, and payment selection.
 */
?>
<!-- wp:group {"align":"full","className":"home-booking-section","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"}}},"backgroundColor":"surface-header-footer","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull home-booking-section has-surface-header-footer-background-color has-background" style="padding-top:80px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"10px","margin":{"bottom":"30px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group" style="margin-bottom:30px">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1.5px","fontSize":"13px","fontWeight":"700"}},"textColor":"primary"} -->
		<p class="has-text-align-center has-primary-color has-text-color" style="font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e( 'Fast & Secure Reservation', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"34px","fontWeight":"800","lineHeight":"1.2"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="font-size:34px;font-weight:800;line-height:1.2"><?php esc_html_e( 'Book Your Ha Giang Adventure', 'tour-reference-theme' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"16px"}}} -->
		<p class="has-text-align-center" style="font-size:16px;color:#666;"><?php esc_html_e( 'Instant confirmation · No hidden fees · Free cancellation up to 48 hours before departure', 'tour-reference-theme' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- Booking Form Container -->
	<div class="lt-booking-form-container" id="book">
		<form id="lt-main-booking-form" class="lt-booking-form">
			<!-- Honeypot anti-spam (invisible) -->
			<div style="display:none !important;" aria-hidden="true">
				<input type="text" name="honeypot_field" tabindex="-1" autocomplete="off" />
			</div>

			<div class="lt-form-grid">
				<!-- Step 1: Select Tour -->
				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '1. Select Your Tour Itinerary', 'tour-reference-theme' ); ?> *</label>
					<select id="lt-tour-select" name="tour_id" class="lt-form-select" required>
						<option value="1"><?php esc_html_e( 'Ha Giang Loop 4 Days 3 Nights (Most Popular)', 'tour-reference-theme' ); ?></option>
						<option value="2"><?php esc_html_e( 'Ha Giang Loop 3 Days 2 Nights (Express Loop)', 'tour-reference-theme' ); ?></option>
						<option value="3"><?php esc_html_e( 'Ha Giang & Cao Bang 6 Days (Ultimate Frontier)', 'tour-reference-theme' ); ?></option>
						<option value="4"><?php esc_html_e( 'Ha Giang Loop 2 Days 1 Night (Quick Taste)', 'tour-reference-theme' ); ?></option>
					</select>
				</div>

				<!-- Step 2: Start Date -->
				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '2. Tour Start Date', 'tour-reference-theme' ); ?> *</label>
					<input type="date" id="lt-start-date" name="start_date" class="lt-form-input" required />
				</div>

				<!-- Step 3: Vehicle & Driving Style -->
				<div class="lt-form-group full-width">
					<label class="lt-form-label"><?php esc_html_e( '3. Riding Style & Vehicle Tier', 'tour-reference-theme' ); ?> *</label>
					<div class="lt-vehicle-options">
						<label class="lt-vehicle-radio-card is-selected">
							<input type="radio" name="vehicle_type" value="easy_rider" checked />
							<div class="lt-radio-body">
								<strong>🛵 <?php esc_html_e( 'Easy Rider (With Local Driver)', 'tour-reference-theme' ); ?></strong>
								<span><?php esc_html_e( 'Sit on the back, relax, take photos. Best for beginners.', 'tour-reference-theme' ); ?></span>
								<div class="lt-card-price">$208 / person</div>
							</div>
						</label>

						<label class="lt-vehicle-radio-card">
							<input type="radio" name="vehicle_type" value="self_ride" />
							<div class="lt-radio-body">
								<strong>🏍️ <?php esc_html_e( 'Self-Ride (Drive Yourself)', 'tour-reference-theme' ); ?></strong>
								<span><?php esc_html_e( 'Drive semi-auto or manual bike behind guide. IDP required.', 'tour-reference-theme' ); ?></span>
								<div class="lt-card-price">$140 / person</div>
							</div>
						</label>

						<label class="lt-vehicle-radio-card">
							<input type="radio" name="vehicle_type" value="jeep" />
							<div class="lt-radio-body">
								<strong>🚙 <?php esc_html_e( 'Open-Top 4x4 Jeep', 'tour-reference-theme' ); ?></strong>
								<span><?php esc_html_e( 'Panoramic open-top mountain jeep for couples/groups.', 'tour-reference-theme' ); ?></span>
								<div class="lt-card-price">$290 / person</div>
							</div>
						</label>
					</div>
				</div>

				<!-- Step 4: Number of Travelers -->
				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '4. Number of Travelers', 'tour-reference-theme' ); ?> *</label>
					<div class="lt-stepper">
						<button type="button" class="lt-stepper-btn" onclick="let el=document.getElementById('lt-party-size');el.value=Math.max(1,parseInt(el.value)-1);">−</button>
						<input type="number" id="lt-party-size" name="party_size" value="1" min="1" max="20" class="lt-stepper-input" />
						<button type="button" class="lt-stepper-btn" onclick="let el=document.getElementById('lt-party-size');el.value=parseInt(el.value)+1;">+</button>
					</div>
				</div>

				<!-- Step 5: Bus Transfers -->
				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '5. Hanoi ⇄ Ha Giang Bus Transfer', 'tour-reference-theme' ); ?></label>
					<select id="lt-bus-select" name="bus_transfer" class="lt-form-select">
						<option value="none"><?php esc_html_e( 'No Bus Needed (Meet in Ha Giang)', 'tour-reference-theme' ); ?></option>
						<option value="vip_cabin"><?php esc_html_e( 'VIP Sleeper Cabin Bus (+$15 / person)', 'tour-reference-theme' ); ?></option>
						<option value="limousine"><?php esc_html_e( 'Luxury Limousine Van (+$18 / person)', 'tour-reference-theme' ); ?></option>
					</select>
				</div>

				<!-- Step 6: Contact Information -->
				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '6. Full Name', 'tour-reference-theme' ); ?> *</label>
					<input type="text" id="lt-cust-name" name="customer_name" class="lt-form-input" required placeholder="John Doe" />
				</div>

				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '7. Email Address', 'tour-reference-theme' ); ?> *</label>
					<input type="email" id="lt-cust-email" name="customer_email" class="lt-form-input" required placeholder="john@example.com" />
				</div>

				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '8. WhatsApp / Phone', 'tour-reference-theme' ); ?> *</label>
					<input type="tel" id="lt-cust-phone" name="customer_phone" class="lt-form-input" required placeholder="+1 234 567 890" />
				</div>

				<div class="lt-form-group">
					<label class="lt-form-label"><?php esc_html_e( '9. Voucher Code', 'tour-reference-theme' ); ?></label>
					<div style="display:flex;gap:8px;">
						<input type="text" id="lt-voucher-input" name="voucher_code" class="lt-form-input" placeholder="e.g. WELCOME10" />
						<button type="button" id="lt-voucher-apply" class="tab-btn" style="padding:8px 16px;font-size:13px;"><?php esc_html_e( 'Apply', 'tour-reference-theme' ); ?></button>
					</div>
				</div>
			</div>

			<!-- Live Booking Summary Box -->
			<div class="lt-live-summary-card">
				<h3 class="lt-summary-title"><?php esc_html_e( 'Booking Summary', 'tour-reference-theme' ); ?></h3>

				<div class="lt-summary-row">
					<span><?php esc_html_e( 'Tour Base & Vehicle Subtotal', 'tour-reference-theme' ); ?>:</span>
					<strong id="lt-sum-subtotal">$208 USD</strong>
				</div>

				<div class="lt-summary-row" id="lt-sum-discount-row" style="display:none;color:#10b981;">
					<span><?php esc_html_e( 'Voucher Discount', 'tour-reference-theme' ); ?>:</span>
					<strong id="lt-sum-discount">-$0 USD</strong>
				</div>

				<div class="lt-summary-row lt-total-row">
					<span><?php esc_html_e( 'Total Amount', 'tour-reference-theme' ); ?>:</span>
					<strong id="lt-sum-total">$208 USD <small id="lt-sum-vnd">(~ 5.283.000 ₫)</small></strong>
				</div>

				<div class="lt-summary-row lt-deposit-row">
					<span><?php esc_html_e( '20% Deposit Due Now', 'tour-reference-theme' ); ?>:</span>
					<strong id="lt-sum-deposit" style="color:#ff6602;">$41.60 USD</strong>
				</div>

				<div class="lt-summary-actions">
					<div class="wp-block-button is-style-book-now" style="width:100%;">
						<button type="submit" id="lt-submit-btn" class="wp-block-button__link wp-element-button" style="width:100%;justify-content:center;cursor:pointer;border:none;font-size:16px;padding:16px 28px;">
							<?php esc_html_e( 'Confirm & Pay 20% Deposit ($41.60)', 'tour-reference-theme' ); ?>
						</button>
					</div>
					<p style="font-size:12px;color:#777;text-align:center;margin:12px 0 0;">
						🔒 <?php esc_html_e( 'Secure SSL Checkout · Instant Email Voucher · 100% Guaranteed Departures', 'tour-reference-theme' ); ?>
					</p>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- /wp:group -->
