<?php
/**
 * Minimal "Tour Booking" admin page with a one-click demo-content import
 * button. The remove button is added by Task 8.
 */

defined( 'ABSPATH' ) || exit;

class Tbc_Admin_Page {

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_post_tbc_import_demo', array( __CLASS__, 'handle_import' ) );
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
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Tour Booking Core', 'tour-booking-core' ); ?></h1>
			<?php if ( isset( $_GET['tbc_notice'] ) && 'imported' === $_GET['tbc_notice'] ) : ?>
				<div class="notice notice-success"><p><?php esc_html_e( 'Demo content imported.', 'tour-booking-core' ); ?></p></div>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'tbc_import_demo' ); ?>
				<input type="hidden" name="action" value="tbc_import_demo" />
				<?php submit_button( __( 'Import Demo Content', 'tour-booking-core' ) ); ?>
			</form>
		</div>
		<?php
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
}
