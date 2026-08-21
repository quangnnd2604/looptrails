<?php
/**
 * Title: Motorbike Rental Booking Form
 * Slug: tour-reference-theme/rental-booking-form
 * Categories: featured, forms
 * Description: Dedicated motorbike rental booking form with motorcycle picker, dates, and live cost calculation.
 */
?>
<!-- wp:group {"anchor":"rental-book","align":"full","className":"rental-booking-section","style":{"spacing":{"padding":{"top":"60px","bottom":"80px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div id="rental-book" class="wp-block-group alignfull rental-booking-section" style="padding-top:60px;padding-right:20px;padding-bottom:80px;padding-left:20px">
	<div class="rental-booking-container" style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:16px;padding:36px;box-shadow:0 10px 35px rgba(0,0,0,0.06);border:1px solid #e5e5e5;">
		<div style="text-align:center;margin-bottom:28px;">
			<h2 class="wp-block-heading" style="font-size:28px;font-weight:800;margin-bottom:8px;"><?php esc_html_e( 'Reserve Your Motorbike', 'tour-reference-theme' ); ?></h2>
			<p style="color:#666;font-size:15px;margin:0;"><?php esc_html_e( 'Choose your bike and rental duration. Instant confirmation with zero hidden fees.', 'tour-reference-theme' ); ?></p>
		</div>

		<form id="lt-rental-form" class="lt-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
			<div class="lt-form-group" style="grid-column:1 / -1;">
				<label style="display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;"><?php esc_html_e( 'Select Motorbike', 'tour-reference-theme' ); ?></label>
				<select name="rental_bike" id="rental_bike" style="width:100%;height:46px;padding:0 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;background:#fff;" required>
					<option value="wave_alpha"><?php esc_html_e( 'Honda Wave Alpha 110cc — $10 / day', 'tour-reference-theme' ); ?></option>
					<option value="blade_fi"><?php esc_html_e( 'Honda Blade 110cc FI — $12 / day', 'tour-reference-theme' ); ?></option>
					<option value="xr150l"><?php esc_html_e( 'Honda XR 150L Dual-Sport — $22 / day', 'tour-reference-theme' ); ?></option>
					<option value="cb500x"><?php esc_html_e( 'Honda CB500X Adventure — $48 / day', 'tour-reference-theme' ); ?></option>
				</select>
			</div>

			<div class="lt-form-group">
				<label style="display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;"><?php esc_html_e( 'Start Date', 'tour-reference-theme' ); ?></label>
				<input type="date" name="start_date" id="rental_start_date" style="width:100%;height:46px;padding:0 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;" required />
			</div>

			<div class="lt-form-group">
				<label style="display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;"><?php esc_html_e( 'Rental Days', 'tour-reference-theme' ); ?></label>
				<input type="number" name="rental_days" id="rental_days" min="1" max="30" value="3" style="width:100%;height:46px;padding:0 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;" required />
			</div>

			<div class="lt-form-group">
				<label style="display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;"><?php esc_html_e( 'Full Name', 'tour-reference-theme' ); ?></label>
				<input type="text" name="customer_name" id="rental_customer_name" placeholder="<?php esc_attr_e( 'Your Full Name', 'tour-reference-theme' ); ?>" style="width:100%;height:46px;padding:0 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;" required />
			</div>

			<div class="lt-form-group">
				<label style="display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;"><?php esc_html_e( 'Email Address', 'tour-reference-theme' ); ?></label>
				<input type="email" name="customer_email" id="rental_customer_email" placeholder="name@example.com" style="width:100%;height:46px;padding:0 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;" required />
			</div>

			<div class="lt-form-group" style="grid-column:1 / -1;">
				<label style="display:block;font-weight:600;font-size:14px;margin-bottom:6px;color:#333;"><?php esc_html_e( 'Phone / WhatsApp', 'tour-reference-theme' ); ?></label>
				<input type="tel" name="customer_phone" id="rental_customer_phone" placeholder="+84 987 654 321" style="width:100%;height:46px;padding:0 14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;" />
			</div>

			<!-- Honeypot -->
			<input type="text" name="honeypot_field" style="display:none;" tabindex="-1" autocomplete="off" />

			<!-- Cost Summary Box -->
			<div class="lt-form-group" style="grid-column:1 / -1;background:#f9fafb;border-radius:10px;padding:16px 20px;border:1px solid #e5e7eb;margin-top:6px;">
				<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
					<span style="font-size:14px;color:#4b5563;"><?php esc_html_e( 'Estimated Total:', 'tour-reference-theme' ); ?></span>
					<span id="rental-cost-display" style="font-size:22px;font-weight:800;color:#ff6602;">$30 USD</span>
				</div>
				<div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#6b7280;">
					<span><?php esc_html_e( 'Deposit Due (20%):', 'tour-reference-theme' ); ?></span>
					<span id="rental-deposit-display" style="font-weight:600;">$6 USD</span>
				</div>
			</div>

			<!-- Submit Button -->
			<div class="lt-form-group" style="grid-column:1 / -1;margin-top:10px;">
				<button type="submit" id="rental-submit-btn" style="width:100%;height:54px;background:#ff6602;color:#ffffff;border:none;border-radius:8px;font-family:Montserrat,sans-serif;font-size:16px;font-weight:700;cursor:pointer;box-shadow:2px 3px 0px 0px #36343b;transition:background 0.2s;">
					<?php esc_html_e( 'Confirm Motorbike Reservation', 'tour-reference-theme' ); ?>
				</button>
				<div id="rental-form-feedback" style="margin-top:14px;display:none;padding:12px;border-radius:8px;font-size:14px;text-align:center;"></div>
			</div>
		</form>

		<script>
		(function(){
			document.addEventListener("DOMContentLoaded", function(){
				const bikeSelect = document.getElementById("rental_bike");
				const daysInput = document.getElementById("rental_days");
				const costDisplay = document.getElementById("rental-cost-display");
				const depositDisplay = document.getElementById("rental-deposit-display");
				const form = document.getElementById("lt-rental-form");
				const feedback = document.getElementById("rental-form-feedback");
				const submitBtn = document.getElementById("rental-submit-btn");

				const rates = {
					wave_alpha: 10,
					blade_fi: 12,
					xr150l: 22,
					cb500x: 48
				};

				function updateCost() {
					const bike = bikeSelect ? bikeSelect.value : "wave_alpha";
					const days = parseInt(daysInput ? daysInput.value : "1", 10) || 1;
					const rate = rates[bike] || 10;
					const total = rate * days;
					const deposit = Math.round(total * 0.2);
					if (costDisplay) costDisplay.textContent = "$" + total + " USD";
					if (depositDisplay) depositDisplay.textContent = "$" + deposit + " USD";
				}

				if (bikeSelect) bikeSelect.addEventListener("change", updateCost);
				if (daysInput) daysInput.addEventListener("input", updateCost);

				// Connect Fleet card buttons
				document.querySelectorAll(".rental-bikes-grid a[href='#rental-book']").forEach(function(btn){
					btn.addEventListener("click", function(e){
						const cardBike = btn.getAttribute("data-bike");
						if (cardBike && bikeSelect) {
							bikeSelect.value = cardBike;
							updateCost();
						}
					});
				});

				// Submit handler
				if (form) {
					form.addEventListener("submit", function(e){
						e.preventDefault();
						submitBtn.disabled = true;
						submitBtn.textContent = "Processing...";
						feedback.style.display = "none";

						const payload = {
							rental_bike: bikeSelect.value,
							rental_days: parseInt(daysInput.value, 10) || 1,
							start_date: document.getElementById("rental_start_date").value,
							customer_name: document.getElementById("rental_customer_name").value,
							customer_email: document.getElementById("rental_customer_email").value,
							customer_phone: document.getElementById("rental_customer_phone").value,
							honeypot_field: form.querySelector("input[name=honeypot_field]").value
						};

						fetch("/looptrails/wp-json/tour-booking/v1/book", {
							method: "POST",
							headers: { "Content-Type": "application/json" },
							body: JSON.stringify(payload)
						})
						.then(r => r.json())
						.then(data => {
							submitBtn.disabled = false;
							submitBtn.textContent = "Confirm Motorbike Reservation";
							feedback.style.display = "block";
							if (data.success) {
								feedback.style.background = "#e6f4ea";
								feedback.style.color = "#137333";
								feedback.textContent = "✓ Reservation received! Reference: " + (data.booking_ref || data.booking_id) + ". We will email your rental details shortly.";
								form.reset();
								updateCost();
							} else {
								feedback.style.background = "#fce8e6";
								feedback.style.color = "#c5221f";
								feedback.textContent = "Error: " + (data.message || "Failed to submit booking.");
							}
						})
						.catch(err => {
							submitBtn.disabled = false;
							submitBtn.textContent = "Confirm Motorbike Reservation";
							feedback.style.display = "block";
							feedback.style.background = "#fce8e6";
							feedback.style.color = "#c5221f";
							feedback.textContent = "Network error. Please try again.";
						});
					});
				}
			});
		})();
		</script>
	</div>
</div>
<!-- /wp:group -->
