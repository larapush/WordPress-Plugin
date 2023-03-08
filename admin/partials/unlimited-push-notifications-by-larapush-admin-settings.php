<?php
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Provide a settings area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin/partials
 */

$connection = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkConnection();
if($connection){
    $campaignFilter = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::getCampaignFilter();
}else{
    $campaignFilter = false;
}

$integration_done = false;
if($connection == true and get_option('unlimited_push_notifications_by_larapush_panel_integration_tried', false) == false){
    update_option('unlimited_push_notifications_by_larapush_panel_integration_tried', true);
    $integration_done = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::codeIntegration();
}
?>
<div class="wrap">
    <div>
        <h1>Connect LaraPush</h1>
        <p>Send unlimited push notifications to your users directly from WordPress.</p>
        <?php settings_errors( 'unlimited-push-notifications-by-larapush-settings' ); ?>
        <?php if($integration_done){ ?>
            <div class="notice notice-success is-dismissible">
                <p>Integration done successfully.</p>
            </div>
        <?php } ?>
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
                    <th scope="row">Enable Subscriber Collection</th>
                    <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_enable_push_notifications" value="1" <?php checked(1, get_option('unlimited_push_notifications_by_larapush_enable_push_notifications', 1), true); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Automatic Push On Publish</th>
                    <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_push_on_publish" value="1" <?php checked(1, get_option('unlimited_push_notifications_by_larapush_push_on_publish', 0), true); ?> /></td>
                </tr>
                <?php if($campaignFilter == true){ ?>
                <tr valign="top">
                    <th scope="row">Select Domains to Send Notifications</th>
                    <td>
                        <select name="unlimited_push_notifications_by_larapush_panel_domains_selected[]" multiple="multiple" style="width: 100%; height: 100px;">
                            <?php
                            $domains = get_option('unlimited_push_notifications_by_larapush_panel_domains', []);
                            $domains_selected = get_option('unlimited_push_notifications_by_larapush_panel_domains_selected', []);
                            foreach ( $domains as $domain ) { ?>
                                <option value="<?= $domain ?>"  <?php selected( true, in_array($domain, $domains_selected) ); ?>><?= $domain ?></option>
                            <?php } ?>
                        </select>
                        <p class="description">Use shift to select multiple domains</p>
                    </td>
                </tr>
                <?php if(count(get_option('unlimited_push_notifications_by_larapush_panel_migrated_domains', []))) { ?>
                <tr valign="top">
                    <th scope="row">Select Migrated Domains to Send Notifications</th>
                    <td>
                        <select name="unlimited_push_notifications_by_larapush_panel_migrated_domains_selected[]" multiple="multiple" style="width: 100%; height: 100px;">
                            <?php
                            $migrated_domains = get_option('unlimited_push_notifications_by_larapush_panel_migrated_domains', []);
                            $migrated_domains_selected = get_option('unlimited_push_notifications_by_larapush_panel_migrated_domains_selected', []);
                            foreach ( $migrated_domains as $domain ) { ?>
                                <option value="<?= $domain ?>"  <?php selected( true, in_array($domain, $migrated_domains_selected) ); ?>><?= $domain ?></option>
                            <?php } ?>
                        </select>
                        <p class="description">Use shift to select multiple domains</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Add Code for AMP</th>
                    <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_add_code_for_amp" value="1" <?php checked(1, get_option('unlimited_push_notifications_by_larapush_add_code_for_amp', 0), true); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Select AMP Code Location</th>
                    <td>
                    <?php
                        $amp_code_location = get_option('unlimited_push_notifications_by_larapush_amp_code_location', []);
                    ?>
                        <select name="unlimited_push_notifications_by_larapush_amp_code_location[]" multiple="multiple" style="width: 100%; height: 100px;">
                            <option value="header" <?php selected( true, in_array('header', $amp_code_location) ); ?>>Header (All Pages)</option>
                            <option value="footer" <?php selected( true, in_array('footer', $amp_code_location) ); ?>>Footer (All Pages)</option>
                            <option value="before_post" <?php selected( true, in_array('before_post', $amp_code_location) ); ?>>Before Post (Post Pages)</option>
                            <option value="after_post" <?php selected( true, in_array('after_post', $amp_code_location) ); ?>>After Post (Post Pages)</option>
                        </select>
                        <p class="description">Use shift to select multiple locations</p>
                    </td>
                </tr>
                <?php } ?>
                <?php } ?>
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
</div>