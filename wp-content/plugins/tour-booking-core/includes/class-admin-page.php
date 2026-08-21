<?php
/**
 * Tour Booking admin page with site settings and demo-content import/remove.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Admin_Page {

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_post_tbc_import_demo', array( __CLASS__, 'handle_import' ) );
		add_action( 'admin_post_tbc_remove_demo', array( __CLASS__, 'handle_remove' ) );
		add_action( 'admin_post_tbc_save_settings', array( __CLASS__, 'handle_save_settings' ) );
	}

	public static function add_menu() {
		add_menu_page(
			__( 'Tour Booking', 'tour-booking-core' ),
			__( 'Tour Booking', 'tour-booking-core' ),
			'manage_options',
			'tour-booking-core',
			array( __CLASS__, 'render' ),
			'dashicons-palmtree'
		);
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$business_name   = get_option( 'tbc_site_business_name', get_bloginfo( 'name' ) );
		$business_email  = get_option( 'tbc_site_email', 'contact@example.com' );
		$business_phone  = get_option( 'tbc_site_phone', '+84 123 456 789' );
		$business_addr   = get_option( 'tbc_site_address', 'Ha Giang City, Vietnam' );
		$exchange_rate   = get_option( 'tbc_exchange_rate', 25400 );
		$deposit_percent = get_option( 'tbc_deposit_percent', 20 );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Tour Booking Core Settings', 'tour-booking-core' ); ?></h1>

			<?php if ( isset( $_GET['tbc_notice'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p>
					<?php
					if ( 'saved' === $_GET['tbc_notice'] ) {
						esc_html_e( 'Settings saved successfully.', 'tour-booking-core' );
					} elseif ( 'removed' === $_GET['tbc_notice'] ) {
						esc_html_e( 'Demo content removed.', 'tour-booking-core' );
					} else {
						esc_html_e( 'Demo content imported.', 'tour-booking-core' );
					}
					?>
				</p></div>
			<?php endif; ?>

			<div style="background:#fff;border:1px solid #ccd0d4;padding:20px;border-radius:4px;margin-bottom:24px;max-width:700px;">
				<h2><?php esc_html_e( 'Business & Financial Settings', 'tour-booking-core' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'tbc_save_settings' ); ?>
					<input type="hidden" name="action" value="tbc_save_settings" />

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="tbc_site_business_name"><?php esc_html_e( 'Business Name', 'tour-booking-core' ); ?></label></th>
							<td><input name="tbc_site_business_name" type="text" id="tbc_site_business_name" value="<?php echo esc_attr( $business_name ); ?>" class="regular-text" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="tbc_site_email"><?php esc_html_e( 'Public Email', 'tour-booking-core' ); ?></label></th>
							<td><input name="tbc_site_email" type="email" id="tbc_site_email" value="<?php echo esc_attr( $business_email ); ?>" class="regular-text" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="tbc_site_phone"><?php esc_html_e( 'Public Phone / WhatsApp', 'tour-booking-core' ); ?></label></th>
							<td><input name="tbc_site_phone" type="text" id="tbc_site_phone" value="<?php echo esc_attr( $business_phone ); ?>" class="regular-text" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="tbc_site_address"><?php esc_html_e( 'Basecamp Address', 'tour-booking-core' ); ?></label></th>
							<td><input name="tbc_site_address" type="text" id="tbc_site_address" value="<?php echo esc_attr( $business_addr ); ?>" class="regular-text" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="tbc_exchange_rate"><?php esc_html_e( 'USD / VND Exchange Rate', 'tour-booking-core' ); ?></label></th>
							<td><input name="tbc_exchange_rate" type="number" id="tbc_exchange_rate" value="<?php echo esc_attr( $exchange_rate ); ?>" class="small-text" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="tbc_deposit_percent"><?php esc_html_e( 'Deposit Percentage (%)', 'tour-booking-core' ); ?></label></th>
							<td><input name="tbc_deposit_percent" type="number" id="tbc_deposit_percent" value="<?php echo esc_attr( $deposit_percent ); ?>" min="1" max="100" class="small-text" /></td>
						</tr>
					</table>

					<?php submit_button( __( 'Save Settings', 'tour-booking-core' ) ); ?>
				</form>
			</div>

			<div style="background:#fff;border:1px solid #ccd0d4;padding:20px;border-radius:4px;max-width:700px;">
				<h2><?php esc_html_e( 'Demo Data Operations', 'tour-booking-core' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:12px;">
					<?php wp_nonce_field( 'tbc_import_demo' ); ?>
					<input type="hidden" name="action" value="tbc_import_demo" />
					<?php submit_button( __( 'Import Demo Content', 'tour-booking-core' ) ); ?>
				</form>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Delete all demo content? This cannot be undone.', 'tour-booking-core' ) ); ?>');">
					<?php wp_nonce_field( 'tbc_remove_demo' ); ?>
					<input type="hidden" name="action" value="tbc_remove_demo" />
					<?php submit_button( __( 'Remove Demo Content', 'tour-booking-core' ), 'delete' ); ?>
				</form>
			</div>
		</div>
		<?php
	}

	public static function handle_save_settings() {
		check_admin_referer( 'tbc_save_settings' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'tour-booking-core' ) );
		}

		if ( isset( $_POST['tbc_site_business_name'] ) ) {
			update_option( 'tbc_site_business_name', sanitize_text_field( wp_unslash( $_POST['tbc_site_business_name'] ) ) );
		}
		if ( isset( $_POST['tbc_site_email'] ) ) {
			update_option( 'tbc_site_email', sanitize_email( wp_unslash( $_POST['tbc_site_email'] ) ) );
		}
		if ( isset( $_POST['tbc_site_phone'] ) ) {
			update_option( 'tbc_site_phone', sanitize_text_field( wp_unslash( $_POST['tbc_site_phone'] ) ) );
		}
		if ( isset( $_POST['tbc_site_address'] ) ) {
			update_option( 'tbc_site_address', sanitize_text_field( wp_unslash( $_POST['tbc_site_address'] ) ) );
		}
		if ( isset( $_POST['tbc_exchange_rate'] ) ) {
			update_option( 'tbc_exchange_rate', absint( $_POST['tbc_exchange_rate'] ) );
		}
		if ( isset( $_POST['tbc_deposit_percent'] ) ) {
			update_option( 'tbc_deposit_percent', min( 100, max( 1, absint( $_POST['tbc_deposit_percent'] ) ) ) );
		}

		wp_safe_redirect( add_query_arg( 'tbc_notice', 'saved', admin_url( 'admin.php?page=tour-booking-core' ) ) );
		exit;
	}

	public static function handle_import() {
		check_admin_referer( 'tbc_import_demo' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'tour-booking-core' ) );
		}

		Tbc_Demo_Importer::import();

		wp_safe_redirect( add_query_arg( 'tbc_notice', 'imported', admin_url( 'admin.php?page=tour-booking-core' ) ) );
		exit;
	}

	public static function handle_remove() {
		check_admin_referer( 'tbc_remove_demo' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'tour-booking-core' ) );
		}

		Tbc_Demo_Remover::remove();

		wp_safe_redirect( add_query_arg( 'tbc_notice', 'removed', admin_url( 'admin.php?page=tour-booking-core' ) ) );
		exit;
	}
}
