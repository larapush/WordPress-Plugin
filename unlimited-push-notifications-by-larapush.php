<?php

/**
 * @link              https://larapush.com
 * @since             1.0.5
 * @package           Push_Notifications_By_Larapush
 *
 * @wordpress-plugin
 * Plugin Name:       Push Notifications by LaraPush
 * Plugin URI:        https://wordpress.org/plugins/push-notifications-by-larapush/
 * Description:       Push Notifications by LaraPush simplifies push notifications on WordPress with unlimited capabilities and AMP support.
 * Version:           1.0.5
 * Author:            LaraPush
 * Author URI:        https://larapush.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       push-notifications-by-larapush
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die();
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('UNLIMITED_PUSH_NOTIFICATIONS_BY_LARAPUSH_VERSION', '1.0.5');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-unlimited-push-notifications-by-larapush-activator.php
 */
function activate_unlimited_push_notifications_by_larapush()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-unlimited-push-notifications-by-larapush-activator.php';
    Unlimited_Push_Notifications_By_Larapush_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-unlimited-push-notifications-by-larapush-deactivator.php
 */
function deactivate_unlimited_push_notifications_by_larapush()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-unlimited-push-notifications-by-larapush-deactivator.php';
    Unlimited_Push_Notifications_By_Larapush_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_unlimited_push_notifications_by_larapush');
register_deactivation_hook(__FILE__, 'deactivate_unlimited_push_notifications_by_larapush');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-unlimited-push-notifications-by-larapush.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_unlimited_push_notifications_by_larapush()
{
    $plugin = new Unlimited_Push_Notifications_By_Larapush();
    $plugin->run();
}
run_unlimited_push_notifications_by_larapush();
