<?php

/**
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/public
 * @author     LaraPush <support@larapush.com>
 */
class Unlimited_Push_Notifications_By_Larapush_Public
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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Adds the code to header
     *
     * @since 1.0.0
     */
    public function wp_head()
    {
        # When AMP is not Enabled, add website code else add AMP code
        if (!(function_exists('is_amp_endpoint') && is_amp_endpoint())) {
            # Check if token collection is enabled
            if (get_option('unlimited_push_notifications_by_larapush_enable_push_notifications', false)) {
                # Add the code to header
                $code = get_option('unlimited_push_notifications_by_larapush_codes', []);
                if (array_key_exists('code_to_be_added_in_header', $code)) {
                    echo $code['code_to_be_added_in_header'];
                }
            }
        } else {
            $this->amp_post_template_head();
        }
    }

    /**
     * Adds the amp code to header
     *
     * @since 1.0.0
     */
    public function amp_post_template_head()
    {
        $amp_enabled = get_option('unlimited_push_notifications_by_larapush_add_code_for_amp', false);
        $locations = get_option('unlimited_push_notifications_by_larapush_amp_code_location', []);
        $code = get_option('unlimited_push_notifications_by_larapush_codes', []);

        # Check if amp is enabled
        if ($amp_enabled) {
            if (array_key_exists('amp_code_to_be_added_in_header', $code)) {
                echo $code['amp_code_to_be_added_in_header'];
            }

            # Check if user has selected header location
            if (in_array('header', $locations)) {
                # Add the code to header
                echo $this->get_widget_code();
            }
        }
    }

    /**
     * Adds the amp code to footer
     *
     * @since 1.0.0
     */
    public function amp_post_template_footer()
    {
        $amp_enabled = get_option('unlimited_push_notifications_by_larapush_add_code_for_amp', false);
        $locations = get_option('unlimited_push_notifications_by_larapush_amp_code_location', []);
        $code = get_option('unlimited_push_notifications_by_larapush_codes', []);

        # Check if amp is enabled
        if ($amp_enabled) {
            # Check if user has selected footer location
            if (in_array('footer', $locations)) {
                # Add the code to footer
                echo $this->get_widget_code();
            }
        }
    }

    public function the_content($content)
    {
        # Ignore if amp is not enabled
        if (!(function_exists('is_amp_endpoint') && is_amp_endpoint())) {
            return $content;
        }

        # Ignoring if it is not the page
        if (!is_single()) {
            return $content;
        }

        $amp_enabled = get_option('unlimited_push_notifications_by_larapush_add_code_for_amp', false);
        $locations = get_option('unlimited_push_notifications_by_larapush_amp_code_location', []);
        $code = get_option('unlimited_push_notifications_by_larapush_codes', []);

        # Check if amp is enabled
        if ($amp_enabled) {
            # Check if user has selected before_post location
            if (in_array('before_post', $locations)) {
                # Add the code before post
                $content = $this->get_widget_code() . $content;
            }

            # Check if user has selected after_post location
            if (in_array('after_post', $locations)) {
                # Add the code to after post
                $content = $content . $this->get_widget_code();
            }
        }

        return $content;
    }

    /**
     * Returns the widget code to be added
     *
     * @since 1.0.0
     */
    private function get_widget_code()
    {
        $code = get_option('unlimited_push_notifications_by_larapush_codes', []);
        if (array_key_exists('amp_code_widget', $code)) {
            return $code['amp_code_widget'];
        }
        return '';
    }
}
