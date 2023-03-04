<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin
 * @author     Satyam Gupta <satyam@larapush.com>
 */
class Unlimited_Push_Notifications_By_Larapush_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Add Menu and Submenu Pages for admin area.
	 * 
	 * @since 1.0.0
	 */
	public function add_menu_pages() {
		add_menu_page(
			'Unlimited Push Notifications by Larapush',
			'LaraPush',
			'manage_options',
			'unlimited-push-notifications-by-larapush',
			array($this, 'render_menu_page'),
			'dashicons-megaphone',
			25
		);

		add_submenu_page(
			'unlimited-push-notifications-by-larapush',
			'Unlimited Push Notifications by Larapush',
			'Larapush Panel',
			'manage_options',
			'unlimited-push-notifications-by-larapush',
			array($this, 'render_menu_page')
		);

		add_submenu_page(
			'unlimited-push-notifications-by-larapush',
			'Unlimited Push Notifications by Larapush',
			'Settings',
			'manage_options',
			'unlimited-push-notifications-by-larapush-settings',
			array($this, 'render_settings_page')
		);
	}

	/**
	 * Render Menu Page for admin area.
	 * 
	 * @since 1.0.0
	 */
	public function render_menu_page() {
		$connection = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkConnection();
		if($connection == false){
			$redirect_url = admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings');
			?>
			<script>
				window.location.href = '<?php echo $redirect_url; ?>';
			</script>
			<?php
		}
		include('partials/unlimited-push-notifications-by-larapush-admin-display.php');
	}

	/**
	 * Render Settings Page for admin area.
	 * 
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		$connection = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkConnection();
		?>
		<div class="wrap">
			<h1>Connect LaraPush</h1>
			<p>Send unlimited push notifications to your users directly from WordPress.</p>
			<?php
			$error_msg = get_transient('larapush_error');
			if ( $error_msg ) {
				echo '<div class="notice notice-error is-dismissible"><p>' . $error_msg . '</p></div>';
				delete_transient('larapush_error');
			}
			settings_errors( 'unlimited-push-notifications-by-larapush-settings' );

			$success_msg = get_transient('larapush_success');
			if ( $success_msg ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . $success_msg . '</p></div>';
				delete_transient('larapush_success');
			}
			?>
			<form method="post" action="<?= esc_url( admin_url('admin-post.php') ) ?>" style="display: inline">
				<input type="hidden" name="action" value="larapush_connect">			
				<?php wp_nonce_field('larapush_connect'); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Panel URL</th>
						<td>
							<input type="text" name="unlimited_push_notifications_by_larapush_panel_url" value="<?= Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_url')) ?>" />
							<p class="description"><?= ($connection == true)? '<span class="dashicons dashicons-yes" style="color: green;"></span> Connected' : '<span class="dashicons dashicons-no" style="color: red;"></span> Not Connected'; ?></p>
						
					</tr>
					<tr valign="top">
						<th scope="row">Panel Email</th>
						<td><input type="text" name="unlimited_push_notifications_by_larapush_panel_email" value="<?= Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_email')) ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Panel Password</th>
						<td><input type="password" name="unlimited_push_notifications_by_larapush_panel_password" value="<?= Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_password')) ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">License Key</th>
						<td><input type="text" name="unlimited_push_notifications_by_larapush_license_key" value="<?= Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_license_key')) ?>" /></td>
					</tr>
				</table>
				<button type="submit" class="button button-primary" id="larapush_connect">Connect Panel</button>
			</form>
			<?php if ( $connection == true ) { ?>
				<form method="post" action="<?= esc_url( admin_url('admin-post.php') ) ?>" style="display: inline">
					<input type="hidden" name="action" value="larapush_code_integration">			
					<?php wp_nonce_field('larapush_code_integration'); ?>
				<button type="submit" class="button button-secondary" id="larapush_code_integration">Integrate Code</button>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Connect to LaraPush Panel.
	 * 
	 * @since 1.0.0
	 */
	public function larapush_connect() {
		// Check if nonce is valid
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'larapush_connect' ) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Invalid nonce.');
		}

		// Check if user has permission to access this page
		if ( ! current_user_can( 'manage_options' ) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('You do not have permission to access this page.');
		}

		// Check if panel url is valid
		if ( ! filter_var($_POST['unlimited_push_notifications_by_larapush_panel_url'], FILTER_VALIDATE_URL) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Invalid panel url.');
		}

		// Check if panel email is valid
		if ( ! filter_var($_POST['unlimited_push_notifications_by_larapush_panel_email'], FILTER_VALIDATE_EMAIL) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Invalid panel email.');
		}

		// Check if panel password is valid
		if ( empty($_POST['unlimited_push_notifications_by_larapush_panel_password']) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Invalid panel password.');
		}

		// Check if license key is valid
		if ( empty($_POST['unlimited_push_notifications_by_larapush_license_key']) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Invalid license key.');
		}

		// Update options
		update_option('unlimited_push_notifications_by_larapush_panel_url', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_url'])));
		update_option('unlimited_push_notifications_by_larapush_panel_email', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_email'])));
		update_option('unlimited_push_notifications_by_larapush_panel_password', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_password'])));
		update_option('unlimited_push_notifications_by_larapush_license_key', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_license_key'])));
		
		// Redirect to settings page
		wp_redirect( admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings') );
		exit;
	}

	/**
	 * Code Integration
	 * 
	 * @since 1.0.0
	 */
	public function code_integration() {
		// Check if nonce is valid
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'larapush_code_integration' ) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Invalid nonce.');
		}

		$integration_done = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::codeIntegration();

		if ( $integration_done == true ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseSuccess('Code integration done successfully.');
		} else {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Code integration failed.');
		}
	}

}
