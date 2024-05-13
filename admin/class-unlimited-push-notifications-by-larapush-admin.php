<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin
 * @author     LaraPush <support@larapush.com>
 */
class Unlimited_Push_Notifications_By_Larapush_Admin
{
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
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Add Menu and Submenu Pages for admin area.
     *
     * @since 1.0.0
     */
    public function add_menu_pages()
    {
        add_menu_page(
            'Unlimited Push Notifications by Larapush',
            'LaraPush',
            'read',
            'unlimited-push-notifications-by-larapush',
            [$this, 'render_menu_page'],
            plugin_dir_url(__FILE__) . 'images/icon.svg',
            25
        );

        add_submenu_page(
            'unlimited-push-notifications-by-larapush',
            'Unlimited Push Notifications by Larapush',
            'Larapush Panel',
            'manage_options',
            'unlimited-push-notifications-by-larapush',
            [$this, 'render_menu_page']
        );

        add_submenu_page(
            'unlimited-push-notifications-by-larapush',
            'Unlimited Push Notifications by Larapush',
            'Settings',
            'manage_options',
            'unlimited-push-notifications-by-larapush-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        if (!Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkIfUserHasAccess()) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/unlimited-push-notifications-by-larapush-admin.js',
            ['jquery'],
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'adminAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
    }

    /**
     * Admin Notices Here
     *
     * @since 1.0.0
     */
    public function admin_notices()
    {
        if (!Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkIfUserHasAccess()) {
            return;
        }

        $error_msg = get_transient('larapush_error');
        if ($error_msg) {
            echo '<div class="notice notice-error is-dismissible"><p><strong>' .
                esc_html($error_msg) .
                '</strong></p></div>';
            delete_transient('larapush_error');
        }
        $success_msg = get_transient('larapush_success');
        if ($success_msg) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>' .
                esc_html($success_msg) .
                '</strong></p></div>';
            delete_transient('larapush_success');
        }

        $setup_done = get_option('unlimited_push_notifications_by_larapush_panel_integration_tried', false);
        if (!$setup_done) {
            if (
                !isset($_GET['page']) ||
                (sanitize_text_field($_GET['page']) != 'unlimited-push-notifications-by-larapush-settings' &&
                    sanitize_text_field($_GET['page']) != 'unlimited-push-notifications-by-larapush')
            ) { ?>
                <div class="notice notice-warning is-dismissible">
                    <p><strong>Larapush</strong> is not setup yet. <a href="<?php echo admin_url(
                        'admin.php?page=unlimited-push-notifications-by-larapush-settings'
                    ); ?>">Click here</a> to setup.</p>
                </div>
                <?php }
        }
    }

    /**
     * Render Menu Page for admin area.
     *
     * @since 1.0.0
     */
    public function render_menu_page()
    {
        # Check if Larapush is connected, if not redirect to settings page using javascript
        $connection = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkConnection();
        if ($connection == false) {
            $redirect_url = admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings'); ?>
			<script>
				window.location.href = '<?php echo esc_url($redirect_url); ?>';
			</script>
			<?php
        }
        include 'partials/unlimited-push-notifications-by-larapush-admin-display.php';
    }

    /**
     * Render Settings Page for admin area.
     *
     * @since 1.0.0
     */
    public function render_settings_page()
    {
        include 'partials/unlimited-push-notifications-by-larapush-admin-settings.php';
    }

    /**
     * Connect to LaraPush Panel, save options and redirect back to settings page.
     *
     * @since 1.0.0
     */
    public function larapush_connect()
    {
        // Check if nonce is valid
        if (!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'larapush_connect')) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid nonce.');
        }

        // Check if user has permission to access this page
        if (!current_user_can('manage_options')) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect(
                'Only Admins can edit settings.'
            );
        }

        // Check if panel url is valid
        if (
            !filter_var(
                sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_url']),
                FILTER_VALIDATE_URL
            )
        ) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid panel url.');
        }

        // Check if panel email is valid
        if (
            !filter_var(
                sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_email']),
                FILTER_VALIDATE_EMAIL
            )
        ) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid panel email.');
        }

        // Check if panel password is valid
        if (empty(sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_password']))) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid panel password.');
        }

        // Check if access is selected, unlimited_push_notifications_by_larapush_access
        if (isset($_POST['unlimited_push_notifications_by_larapush_access'])) {
            $access = $_POST['unlimited_push_notifications_by_larapush_access'];
            if (is_array($access) && (in_array('editor', $access) || in_array('author', $access))) {
                $accessAllowed = [];
                foreach ($access as $role) {
                    if ($role == 'editor' || $role == 'author') {
                        $accessAllowed[] = $role;
                    }
                }
                update_option('unlimited_push_notifications_by_larapush_access', $accessAllowed);
            } else {
                update_option('unlimited_push_notifications_by_larapush_access', []);
            }
        } else {
            update_option('unlimited_push_notifications_by_larapush_access', []);
        }

        // Update options
        update_option(
            'unlimited_push_notifications_by_larapush_panel_url',
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(
                sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_url'])
            )
        );
        update_option(
            'unlimited_push_notifications_by_larapush_panel_email',
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(
                sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_email'])
            )
        );
        update_option(
            'unlimited_push_notifications_by_larapush_panel_password',
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::encode(
                sanitize_text_field($_POST['unlimited_push_notifications_by_larapush_panel_password'])
            )
        );
        update_option(
            'unlimited_push_notifications_by_larapush_enable_push_notifications',
            isset($_POST['unlimited_push_notifications_by_larapush_enable_push_notifications']) ? 1 : 0
        );
        update_option(
            'unlimited_push_notifications_by_larapush_push_on_publish',
            isset($_POST['unlimited_push_notifications_by_larapush_push_on_publish']) ? 1 : 0
        );
        update_option(
            'unlimited_push_notifications_by_larapush_push_on_publish_for_webstories',
            isset($_POST['unlimited_push_notifications_by_larapush_push_on_publish_for_webstories']) ? 1 : 0
        );

        if (get_option('unlimited_push_notifications_by_larapush_panel_integration_tried', false) == true) {
            // Array of Domains come from Select tag
            $domains_selected = [];
            if (isset($_POST['unlimited_push_notifications_by_larapush_panel_domains_selected'])) {
                // check if array
                if (is_array($_POST['unlimited_push_notifications_by_larapush_panel_domains_selected'])) {
                    // loop through array
                    foreach ($_POST['unlimited_push_notifications_by_larapush_panel_domains_selected'] as $domain) {
                        $domains_selected[] = sanitize_text_field($domain);
                    }
                }
            }
            update_option('unlimited_push_notifications_by_larapush_panel_domains_selected', $domains_selected);

            update_option(
                'unlimited_push_notifications_by_larapush_add_code_for_amp',
                isset($_POST['unlimited_push_notifications_by_larapush_add_code_for_amp']) ? 1 : 0
            );

            // Array of AMP Code Location come from Select tag
            $amp_code_location = [];
            if (isset($_POST['unlimited_push_notifications_by_larapush_amp_code_location'])) {
                // check if array
                if (is_array($_POST['unlimited_push_notifications_by_larapush_amp_code_location'])) {
                    // loop through array
                    foreach ($_POST['unlimited_push_notifications_by_larapush_amp_code_location'] as $location) {
                        $amp_code_location[] = sanitize_text_field($location);
                    }
                }
            }
            update_option('unlimited_push_notifications_by_larapush_amp_code_location', $amp_code_location);
        }

        // Redirect to settings page
        wp_redirect(admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings'));
        exit();
    }

    /**
     * Automatically adds the files and code to the website
     *
     * @since 1.0.0
     */
    public function code_integration()
    {
        // Check if nonce is valid
        if (!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'larapush_code_integration')) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect('Invalid nonce.');
        }

        # Integrating code here
        $integration_done = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::codeIntegration();

        # If Integration is successful, redirect to settings page else show error
        if ($integration_done == true) {
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseSuccessAndRedirect('Settings saved.');
        } else {
            if (count(get_settings_errors('unlimited-push-notifications-by-larapush-settings'))) {
                Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect(
                    get_settings_errors('unlimited-push-notifications-by-larapush-settings')[0]['message']
                );
            } else {
                Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseErrorAndRedirect(
                    'Code integration failed.'
                );
            }
        }
    }

    /**
     * Call when post or page status is changed
     *
     * @since    1.0.3
     */
    public function post_page_status_changed($new_status, $old_status, $post)
    {
        if (!empty($_REQUEST['meta-box-loader'])) {
            return;
        }

        if ($old_status == 'publish') {
            return;
        }

        if ($new_status == 'publish') {
            if ($post->post_type == 'post') {
                if (get_option('unlimited_push_notifications_by_larapush_push_on_publish', false)) {
                    $notification = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::send_notification($post->ID);
                }
            }
            if ($post->post_type == 'web-story') {
                if (get_option('unlimited_push_notifications_by_larapush_push_on_publish_for_webstories', false)) {
                    $notification = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::send_notification($post->ID);
                }
            }
        }
    }

    /**
     * Add Post Row Actions to the post list
     *
     * @since 1.0.3
     */
    public function add_post_row_actions($actions, $post)
    {
        if (
            !Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkIfUserHasAccess() ||
            $post->post_status != 'publish'
        ) {
            return $actions;
        }

        if ($post->post_type == 'post' or $post->post_type == 'web-story') {
            $plan = get_option('unlimited_push_notifications_by_larapush_panel_plan', 'pro');
            if ($plan == 'pro') {
                $actions['send_notification'] =
                    '<a href="#" class="larapush_send_notification" data-post-id="' .
                    $post->ID .
                    '">Send Notification</a>';
            } else {
                $actions['send_notification'] =
                    '<span onclick="window.open(\'https://larapush.com/upgrade\', \'_blank\')" style="cursor: pointer;">Send Notification</span>';
            }
        }
        return $actions;
    }

    /**
     * Add Admin Bar Menu
     *
     * @since 1.0.5
     */
    public function add_admin_bar_menu($wp_admin_bar)
    {
        if (!is_admin() && (is_single() || is_page())) {
            // Get the current post id
            $post_id = get_the_ID();
            $post = get_post($post_id);

            if (
                !Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkIfUserHasAccess() ||
                $post->post_status != 'publish'
            ) {
                return;
            }

            $icon_html =
                '<div style="display: flex;align-items: center;align-content: center;"><div style="display: flex;align-items: center;align-content: center;opacity: 0.8;"><img src="' .
                plugin_dir_url(__FILE__) .
                'images/icon.svg' .
                '"></div>&nbsp;&nbsp;<span id="larapush-send-notification-btn">Send Notification</span></div>';
            $title = $icon_html;

            $args = [
                'id' => 'send_notification',
                'title' => $title, // Updated title includes icon
                'href' => '#',
                'meta' => [
                    'class' => 'send-notification-button',
                    'title' => 'Send Notification',
                    'html' => '<div data-post-id="' . esc_attr($post_id) . '"></div>' // Add post ID as a data attribute
                ]
            ];

            $wp_admin_bar->add_node($args);
        }
    }

    /**
     * Send Notification on Row Action Click
     *
     * @since 1.0.0
     */
    public function larapush_send_notification()
    {
        if (!Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkIfUserHasAccess()) {
            return;
        }

        $id = sanitize_text_field($_POST['post_id']);
        $notification = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::send_notification($id);
        if ($notification) {
            die(
                json_encode([
                    'status' => 'success',
                    'message' => 'Notification sent successfully.'
                ])
            );
        } else {
            die(
                json_encode([
                    'status' => 'error',
                    'message' => 'Notification sending failed.'
                ])
            );
        }
    }
}
