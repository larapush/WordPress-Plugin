<?php

/**
 * Fired during plugin activation.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/includes
 * @author     LaraPush <support@larapush.com>
 */
class Unlimited_Push_Notifications_By_Larapush_Activator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        add_rewrite_rule(
            '^wp-content/plugins/push-notifications-by-larapush/larapush-reconfigure/?$',
            'index.php',
            'top'
        );
        flush_rewrite_rules();
    }
}
