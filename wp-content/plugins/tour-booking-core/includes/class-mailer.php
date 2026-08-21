<?php
/**
 * Customer & Admin Email Notifications
 *
 * Implements Spec §8.3.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tbc_Mailer {

	/**
	 * Send booking confirmation email to customer and notification to admin.
	 *
	 * @param int   $booking_id Booking Post ID.
	 * @param array $booking_data Booking meta parameters.
	 * @return bool True on success.
	 */
	public static function send_booking_emails( $booking_id, $booking_data ) {
		$customer_email = isset( $booking_data['customer_email'] ) ? sanitize_email( $booking_data['customer_email'] ) : '';
		$customer_name  = isset( $booking_data['customer_name'] ) ? sanitize_text_field( $booking_data['customer_name'] ) : 'Valued Traveler';
		$booking_ref    = isset( $booking_data['booking_ref'] ) ? sanitize_text_field( $booking_data['booking_ref'] ) : 'LT-' . $booking_id;
		$tour_name      = isset( $booking_data['tour_name'] ) ? sanitize_text_field( $booking_data['tour_name'] ) : 'Vietnam Loop Adventure';
		$start_date     = isset( $booking_data['start_date'] ) ? sanitize_text_field( $booking_data['start_date'] ) : 'Pending Confirmation';
		$total_usd      = isset( $booking_data['total_usd'] ) ? floatval( $booking_data['total_usd'] ) : 0.0;
		$deposit_usd    = isset( $booking_data['deposit_usd'] ) ? floatval( $booking_data['deposit_usd'] ) : 0.0;

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		// 1. Customer Email
		if ( is_email( $customer_email ) ) {
			$customer_subject = sprintf( '[Loop Trails] Booking Confirmation — %s (%s)', $tour_name, $booking_ref );
			$customer_body    = sprintf(
				'<h2>Dear %s,</h2>
				<p>Thank you for choosing Loop Trails for your mountain journey! We have received your booking request.</p>
				<table style="border-collapse:collapse;width:100%%;max-width:500px;margin:20px 0;">
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Booking Ref:</strong></td><td>%s</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Tour:</strong></td><td>%s</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Start Date:</strong></td><td>%s</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Deposit Paid/Due:</strong></td><td>$%s USD</td></tr>
					<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px 0;"><strong>Total Amount:</strong></td><td>$%s USD</td></tr>
				</table>
				<p>Our dispatch team is preparing your gear and guide assignment. If you have any urgent requests, message us on WhatsApp: <strong>+84 987 654 321</strong>.</p>
				<p>Best regards,<br>The Loop Trails Operations Team</p>',
				esc_html( $customer_name ),
				esc_html( $booking_ref ),
				esc_html( $tour_name ),
				esc_html( $start_date ),
				number_format( $deposit_usd, 2 ),
				number_format( $total_usd, 2 )
			);

			wp_mail( $customer_email, $customer_subject, $customer_body, $headers );
		}

		// 2. Admin Notification
		$admin_email = get_option( 'admin_email' );
		if ( is_email( $admin_email ) ) {
			$admin_subject = sprintf( '[New Booking] %s — %s (%s)', $booking_ref, $customer_name, $tour_name );
			$admin_body    = sprintf(
				'<p>A new booking has been submitted online:</p>
				<ul>
					<li><strong>Ref:</strong> %s</li>
					<li><strong>Customer:</strong> %s (%s)</li>
					<li><strong>Tour:</strong> %s</li>
					<li><strong>Date:</strong> %s</li>
					<li><strong>Total:</strong> $%s USD</li>
				</ul>
				<p><a href="%s">View Booking in WP Admin</a></p>',
				esc_html( $booking_ref ),
				esc_html( $customer_name ),
				esc_html( $customer_email ),
				esc_html( $tour_name ),
				esc_html( $start_date ),
				number_format( $total_usd, 2 ),
				admin_url( 'post.php?post=' . $booking_id . '&action=edit' )
			);

			wp_mail( $admin_email, $admin_subject, $admin_body, $headers );
		}

		return true;
	}
}
