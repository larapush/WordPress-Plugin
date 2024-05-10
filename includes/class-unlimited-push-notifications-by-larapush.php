<?php
/**
 * The core plugin class.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/includes
 * @author     LaraPush <support@larapush.com>
 */
class Unlimited_Push_Notifications_By_Larapush
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Unlimited_Push_Notifications_By_Larapush_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('UNLIMITED_PUSH_NOTIFICATIONS_BY_LARAPUSH_VERSION')) {
            $this->version = UNLIMITED_PUSH_NOTIFICATIONS_BY_LARAPUSH_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'unlimited-push-notifications-by-larapush';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Unlimited_Push_Notifications_By_Larapush_Loader. Orchestrates the hooks of the plugin.
     * - Unlimited_Push_Notifications_By_Larapush_i18n. Defines internationalization functionality.
     * - Unlimited_Push_Notifications_By_Larapush_Admin. Defines all hooks for the admin area.
     * - Unlimited_Push_Notifications_By_Larapush_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) .
            'includes/class-unlimited-push-notifications-by-larapush-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) .
            'includes/class-unlimited-push-notifications-by-larapush-i18n.php';

        /**
         * The class responsible for helper functions required for admin and side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) .
            'admin/class-unlimited-push-notifications-by-larapush-admin-helper.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) .
            'admin/class-unlimited-push-notifications-by-larapush-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) .
            'public/class-unlimited-push-notifications-by-larapush-public.php';

        $this->loader = new Unlimited_Push_Notifications_By_Larapush_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Unlimited_Push_Notifications_By_Larapush_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Unlimited_Push_Notifications_By_Larapush_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Unlimited_Push_Notifications_By_Larapush_Admin(
            $this->get_plugin_name(),
            $this->get_version()
        );

        // Add Menu Page and Submenu Page
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_menu_pages');
        $this->loader->add_action('admin_post_larapush_connect', $plugin_admin, 'larapush_connect');
        $this->loader->add_action('admin_post_larapush_code_integration', $plugin_admin, 'code_integration');
        $this->loader->add_action('transition_post_status', $plugin_admin, 'post_page_status_changed', 20, 3);
        $this->loader->add_action('admin_notices', $plugin_admin, 'admin_notices');
        $this->loader->add_filter('post_row_actions', $plugin_admin, 'add_post_row_actions', 20, 2);
        $this->loader->add_filter('admin_bar_menu', $plugin_admin, 'add_admin_bar_menu', 81, 2);
        $this->loader->add_action('wp_ajax_larapush_send_notification', $plugin_admin, 'larapush_send_notification');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.2
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Unlimited_Push_Notifications_By_Larapush_Public(
            $this->get_plugin_name(),
            $this->get_version()
        );

        # Web Codes Locations
        $this->loader->add_action('wp_head', $plugin_public, 'wp_head');

        # AMP Codes Locations
        $this->loader->add_action('amp_post_template_head', $plugin_public, 'amp_post_template_head');
        $this->loader->add_action('ampforwp_body_beginning', $plugin_public, 'ampforwp_body_beginning');
        $this->loader->add_action('amp_post_template_body_open', $plugin_public, 'ampforwp_body_beginning');
        $this->loader->add_action('amp_post_template_css', $plugin_public, 'amp_post_template_css');
        $this->loader->add_action('amp_post_template_footer', $plugin_public, 'amp_post_template_footer');
        $this->loader->add_filter('the_content', $plugin_public, 'the_content', 20, 1);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Unlimited_Push_Notifications_By_Larapush_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
