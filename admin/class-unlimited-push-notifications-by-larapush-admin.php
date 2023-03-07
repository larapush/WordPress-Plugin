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
			plugin_dir_url( __FILE__ ) . 'images/icon.svg',
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
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Unlimited_Push_Notifications_By_Larapush_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Unlimited_Push_Notifications_By_Larapush_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/unlimited-push-notifications-by-larapush-admin.js', array( 'jquery' ), $this->version, false );

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
		include('partials/unlimited-push-notifications-by-larapush-admin-settings.php');
	}

	/**
	 * Connect to LaraPush Panel.
	 * 
	 * @since 1.0.0
	 */
	public function larapush_connect() {
		// Check if nonce is valid
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'larapush_connect' ) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid nonce.');
		}

		// Check if user has permission to access this page
		if ( ! current_user_can( 'manage_options' ) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('You do not have permission to access this page.');
		}

		// Check if panel url is valid
		if ( ! filter_var($_POST['unlimited_push_notifications_by_larapush_panel_url'], FILTER_VALIDATE_URL) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid panel url.');
		}

		// Check if panel email is valid
		if ( ! filter_var($_POST['unlimited_push_notifications_by_larapush_panel_email'], FILTER_VALIDATE_EMAIL) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid panel email.');
		}

		// Check if panel password is valid
		if ( empty($_POST['unlimited_push_notifications_by_larapush_panel_password']) ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid panel password.');
		}

		// Update options
		update_option('unlimited_push_notifications_by_larapush_panel_url', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_url'])));
		update_option('unlimited_push_notifications_by_larapush_panel_email', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_email'])));
		update_option('unlimited_push_notifications_by_larapush_panel_password', Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_password'])));
		update_option('unlimited_push_notifications_by_larapush_enable_push_notifications', isset($_POST['unlimited_push_notifications_by_larapush_enable_push_notifications']) ? 1 : 0);
		update_option('unlimited_push_notifications_by_larapush_push_on_publish', isset($_POST['unlimited_push_notifications_by_larapush_push_on_publish']) ? 1 : 0);
		update_option('unlimited_push_notifications_by_larapush_panel_domains_selected', isset($_POST['unlimited_push_notifications_by_larapush_panel_domains_selected'])? $_POST['unlimited_push_notifications_by_larapush_panel_domains_selected'] : []);
		update_option('unlimited_push_notifications_by_larapush_panel_migrated_domains_selected', isset($_POST['unlimited_push_notifications_by_larapush_panel_migrated_domains_selected'])? $_POST['unlimited_push_notifications_by_larapush_panel_migrated_domains_selected'] : []);

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
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid nonce.');
		}

		$integration_done = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::codeIntegration();

		if ( $integration_done == true ) {
			Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseSuccessAndRedirect('Code integration done successfully.');
		} else {
			if(count(get_settings_errors('unlimited-push-notifications-by-larapush-settings'))){
				Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect(get_settings_errors('unlimited-push-notifications-by-larapush-settings')[0]['message']);
			} else {
				Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Code integration failed.');
			}
		}
	}

	/**
	 * Call when post or page status is changed
	 * 
	 * @since    1.0.0
	 */
	public function post_page_status_changed($new_status, $old_status, $post) {
		if ( ! empty( $_REQUEST['meta-box-loader'] ) ) { // phpcs:ignore
			return;
		}

		// Checking for page updates
		if($post->post_type == 'post' and $new_status == 'publish' and get_option('unlimited_push_notifications_by_larapush_push_on_publish', false)) {
			$notification = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::send_notification($post->ID);
		}
	}

	/**
	 * Add Post Row Actions
	 * 
	 * @since 1.0.0
	 */
	public function add_post_row_actions($actions, $post) {
		if ( $post->post_type == 'post' ) {
			$actions['send_notification'] = '<a href="#" class="larapush_send_notification" data-post-id="' . $post->ID . '">Send Notification</a>';
		}
		return $actions;
	}

	/**
	 * Admin Notices Here
	 * 
	 * @since 1.0.0
	 */
	public function admin_notices() {
		$error_msg = get_transient('larapush_error');
		if ( $error_msg ) {
			echo '<div class="notice notice-error is-dismissible"><p><strong>' . $error_msg . '</strong></p></div>';
			delete_transient('larapush_error');
		}
		$success_msg = get_transient('larapush_success');
		if ( $success_msg ) {
			echo '<div class="notice notice-success is-dismissible"><p><strong>' . $success_msg . '</strong></p></div>';
			delete_transient('larapush_success');
		}

		$setup_done = get_option('unlimited_push_notifications_by_larapush_panel_integration_tried', false);
		if ( ! $setup_done and ! isset($_GET['page']) and $_GET['page'] != 'unlimited-push-notifications-by-larapush-settings') {
			?>
			<div class="notice notice-warning is-dismissible">
				<p><strong>Larapush</strong> is not setup yet. <a href="<?php echo admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings'); ?>">Click here</a> to setup.</p>
			</div>
			<?php
		}
	}

	/**
	 * Send Notification on Click
	 * 
	 * @since 1.0.0
	 */
	public function larapush_send_notification() {
		$id = $_POST['post_id'];
		$notification = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::send_notification($id);
		if ( $notification ) {
			die(json_encode([
				'status' => 'success',
				'message' => 'Notification sent successfully.'
			]));
		} else {
			die(json_encode([
				'status' => 'error',
				'message' => 'Notification sending failed.'
			]));
		}
	}

}
