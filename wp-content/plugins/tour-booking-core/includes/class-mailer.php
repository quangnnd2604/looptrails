<?php
/**
 * Automated Email Dispatcher
 *
 * Implements Spec §8.4.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Mailer {

	/**
	 * Send booking confirmation emails to customer and admin.
	 *
	 * @param array $booking_data Booking parameters.
	 * @return bool True if emails sent successfully.
	 */
	public static function send_booking_emails( $booking_data ) {
		$booking_ref    = isset( $booking_data['booking_ref'] ) ? sanitize_text_field( $booking_data['booking_ref'] ) : 'LT-UNKNOWN';
		$customer_name  = isset( $booking_data['customer_name'] ) ? sanitize_text_field( $booking_data['customer_name'] ) : 'Customer';
		$customer_email = isset( $booking_data['customer_email'] ) ? sanitize_email( $booking_data['customer_email'] ) : '';
		$tour_name      = isset( $booking_data['tour_name'] ) ? sanitize_text_field( $booking_data['tour_name'] ) : 'Vietnam Loop Adventure';
		$start_date     = isset( $booking_data['start_date'] ) ? sanitize_text_field( $booking_data['start_date'] ) : 'Pending Confirmation';
		$total_usd      = isset( $booking_data['total_usd'] ) ? floatval( $booking_data['total_usd'] ) : 0.0;
		$deposit_usd    = isset( $booking_data['deposit_usd'] ) ? floatval( $booking_data['deposit_usd'] ) : 0.0;

		$business_name  = get_option( 'tbc_site_business_name', get_bloginfo( 'name' ) );
		$business_phone = get_option( 'tbc_site_phone', '+84 123 456 789' );

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$all_sent = true;

		// 1. Customer Email
		if ( is_email( $customer_email ) ) {
			$customer_subject = sprintf( '[%s] Booking Confirmation — %s (%s)', $business_name, $tour_name, $booking_ref );
			$customer_body    = sprintf(
				'<h2>Dear %s,</h2>
				<p>Thank you for choosing %s for your mountain journey! We have received your booking request.</p>
				<table style="border-collapse:collapse;width:100%%;max-width:500px;margin:20px 0;">
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Booking Ref:</strong></td><td>%s</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Tour:</strong></td><td>%s</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Start Date:</strong></td><td>%s</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Deposit Paid/Due:</strong></td><td>$%s USD</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Total Amount:</strong></td><td>$%s USD</td></tr>
				</table>
				<p>Our dispatch team is preparing your gear and guide assignment. If you have any urgent requests, message us on WhatsApp: <strong>%s</strong>.</p>
				<p>Best regards,<br>The %s Operations Team</p>',
				esc_html( $customer_name ),
				esc_html( $business_name ),
				esc_html( $booking_ref ),
				esc_html( $tour_name ),
				esc_html( $start_date ),
				number_format( $deposit_usd, 2 ),
				number_format( $total_usd, 2 ),
				esc_html( $business_phone ),
				esc_html( $business_name )
			);

			$sent = wp_mail( $customer_email, $customer_subject, $customer_body, $headers );
			if ( ! $sent ) {
				error_log( 'TBC Mailer: Failed to send customer confirmation email to ' . $customer_email );
				$all_sent = false;
			}
		}

		// 2. Admin Notification
		$admin_email = get_option( 'tbc_site_email', get_option( 'admin_email' ) );
		if ( is_email( $admin_email ) ) {
			$admin_subject = sprintf( '[NEW BOOKING] %s — %s (%s)', $booking_ref, $customer_name, $tour_name );
			$admin_body    = sprintf(
				'<h2>New Tour Booking Received</h2>
				<p>A new customer has submitted a booking request.</p>
				<ul>
					<li><strong>Reference:</strong> %s</li>
					<li><strong>Customer:</strong> %s &lt;%s&gt;</li>
					<li><strong>Tour:</strong> %s</li>
					<li><strong>Date:</strong> %s</li>
					<li><strong>Total:</strong> $%s USD</li>
					<li><strong>Deposit:</strong> $%s USD</li>
				</ul>
				<p><a href="%s">View in WordPress Admin →</a></p>',
				esc_html( $booking_ref ),
				esc_html( $customer_name ),
				esc_html( $customer_email ),
				esc_html( $tour_name ),
				esc_html( $start_date ),
				number_format( $total_usd, 2 ),
				number_format( $deposit_usd, 2 ),
				esc_url( admin_url( 'edit.php?post_type=booking' ) )
			);

			$sent_admin = wp_mail( $admin_email, $admin_subject, $admin_body, $headers );
			if ( ! $sent_admin ) {
				error_log( 'TBC Mailer: Failed to send admin notification email to ' . $admin_email );
			}
		}

		return $all_sent;
	}
}
