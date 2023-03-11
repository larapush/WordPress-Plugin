<?php
if (!defined('ABSPATH')) {
    die();
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin/partials
 */

$url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
    get_option('unlimited_push_notifications_by_larapush_panel_url')
);
$email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
    get_option('unlimited_push_notifications_by_larapush_panel_email')
);
?>
<style>
    .notice{
        display: none;
    }
</style>
<!-- Center the div and add a buton to open panel in new tab -->
<div class="wrap" style="text-align: center; margin-top: 50px;">
    <img src="<?php echo plugin_dir_url(__FILE__) .
        '../images/larapush-logo.svg'; ?>" alt="Larapush Logo" style="width: 200px; height: auto;">
    <h1>Unlimited Push Notifications by Larapush</h1>
    <p>Click the button below to open the Larapush panel in a new tab.</p>
    <a href="<?php echo esc_url($url); ?>" target="_blank" class="button button-primary">Open Larapush Panel</a>
</div>