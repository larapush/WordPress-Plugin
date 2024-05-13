<?php
if (!defined('ABSPATH')) {
    die();
}

/**
 * Provide a settings area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://larapush.com
 * @since      1.0.4
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin/partials
 */

try {
    $connection = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::checkConnection();
    if ($connection) {
        $campaignFilter = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::getCampaignFilter();
        $integration_done = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::codeIntegration();
        update_option('unlimited_push_notifications_by_larapush_panel_integration_tried', true);
    } else {
        $campaignFilter = false;
    }
} catch (Exception $error) {
    $error = $error->getMessage();
}
$plan = get_option('unlimited_push_notifications_by_larapush_panel_plan', 'pro');
?>
<div class="wrap">
    <div>
        <h1>Connect LaraPush</h1>
        <p>Send unlimited push notifications to your users directly from WordPress.</p>
        <?php settings_errors('unlimited-push-notifications-by-larapush-settings'); ?>
        <?php if (isset($error)) { ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo esc_html($error); ?></p>
            </div>
        <?php } ?>
        <?php if (isset($integration_done) && $integration_done) { ?>
            <div class="notice notice-success is-dismissible">
                <p>Settings saved.</p>
            </div>
        <?php } ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline">
            <input type="hidden" name="action" value="larapush_connect">			
            <?php wp_nonce_field('larapush_connect'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Panel URL</th>
                    <td>
                        <input type="text" name="unlimited_push_notifications_by_larapush_panel_url" value="<?php echo esc_attr(
                            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
                                get_option('unlimited_push_notifications_by_larapush_panel_url')
                            )
                        ); ?>" />
                        <?php if (
                            get_option('unlimited_push_notifications_by_larapush_panel_integration_tried', false) ==
                            true
                        ) { ?>
                            <p class="description"><?php echo $connection == true
                                ? '<span class="dashicons dashicons-yes" style="color: green;"></span> Connected'
                                : '<span class="dashicons dashicons-no" style="color: red;"></span> Not Connected'; ?></p>
                        <?php } ?>
                    
                </tr>
                <tr valign="top">
                    <th scope="row">Panel Email</th>
                    <td><input type="text" name="unlimited_push_notifications_by_larapush_panel_email" value="<?php echo esc_attr(
                        Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
                            get_option('unlimited_push_notifications_by_larapush_panel_email')
                        )
                    ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Panel Password</th>
                    <td><input type="password" name="unlimited_push_notifications_by_larapush_panel_password" value="<?php echo esc_attr(
                        Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
                            get_option('unlimited_push_notifications_by_larapush_panel_password')
                        )
                    ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Subscriber Collection</th>
                    <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_enable_push_notifications" value="1" <?php checked(
                        1,
                        get_option('unlimited_push_notifications_by_larapush_enable_push_notifications', 1),
                        true
                    ); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Who can send notifications</th>
                    <td>
                        <?php $access = get_option('unlimited_push_notifications_by_larapush_access', []); ?>
                        <input type="checkbox" name="unlimited_push_notifications_by_larapush_access[]" value="admin" disabled checked /> Administrator &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="unlimited_push_notifications_by_larapush_access[]" value="editor" <?php checked(
                            true,
                            in_array('editor', $access)
                        ); ?>
                         /> Editor &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="unlimited_push_notifications_by_larapush_access[]" value="author" <?php checked(
                            true,
                            in_array('author', $access)
                        ); ?>
                        /> Author &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            
            <?php if ($campaignFilter == true) { ?>
                <div style="position: relative">
                    <h2 class="title">One Click Push</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Push On Publish</th>
                        <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_push_on_publish" value="1" <?php checked(
                            1,
                            get_option('unlimited_push_notifications_by_larapush_push_on_publish', 0),
                            true
                        ); ?> />
                        <p class="description">Send Notifications to all your subscribers on as soon as you publish a post.</p></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Push On Publish WebStories</th>
                        <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_push_on_publish_for_webstories" value="1" <?php checked(
                            1,
                            get_option('unlimited_push_notifications_by_larapush_push_on_publish_for_webstories', 0),
                            true
                        ); ?> />
                        <p class="description">Send Notifications to all your subscribers on as soon as you publish a web story.</p></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Select Domains to Send Notifications</th>
                        <td>
                            <select name="unlimited_push_notifications_by_larapush_panel_domains_selected[]" multiple="multiple" style="width: 100%; height: 100px;">
                                <?php
                                $domains = get_option('unlimited_push_notifications_by_larapush_panel_domains', []);
                                $domains_selected = get_option(
                                    'unlimited_push_notifications_by_larapush_panel_domains_selected',
                                    []
                                );
                                foreach ($domains as $domain) { ?>
                                    <option value="<?php echo esc_attr($domain); ?>"  <?php selected(
    true,
    in_array($domain, $domains_selected)
); ?>><?php echo esc_html($domain); ?></option>
                                <?php }
                                ?>
                            </select>
                            <p class="description">Use ctrl to select multiple domains</p>
                        </td>
                    </tr>
                </table>

            <h2 class="title">Configure AMP</h2>
            <p>Configure AMP to show subscribe button on AMP pages.</p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Enable AMP</th>
                        <td><input type="checkbox" name="unlimited_push_notifications_by_larapush_add_code_for_amp" value="1" <?php checked(
                            1,
                            get_option('unlimited_push_notifications_by_larapush_add_code_for_amp', 0),
                            true
                        ); ?> />
                        <p class="description">Check to show subscribe button on AMP.</p></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">AMP Subscribe Button Location</th>
                        <td>
                        <?php $amp_code_location = get_option(
                            'unlimited_push_notifications_by_larapush_amp_code_location',
                            []
                        ); ?>
                            <select name="unlimited_push_notifications_by_larapush_amp_code_location[]" multiple="multiple" style="width: 100%; height: 100px;">
                                <option value="header" <?php selected(
                                    true,
                                    in_array('header', $amp_code_location)
                                ); ?>>Header (All Pages)</option>
                                <option value="footer" <?php selected(
                                    true,
                                    in_array('footer', $amp_code_location)
                                ); ?>>Footer (All Pages)</option>
                                <option value="before_post" <?php selected(
                                    true,
                                    in_array('before_post', $amp_code_location)
                                ); ?>>Before Post (Post Pages)</option>
                                <option value="after_post" <?php selected(
                                    true,
                                    in_array('after_post', $amp_code_location)
                                ); ?>>After Post (Post Pages)</option>
                            </select>
                            <p class="description">Use ctrl to select multiple locations</p>
                        </td>
                    </tr>
                </table>
                <?php if ($plan != 'pro') { ?>
                <div style="position: absolute; border-radius: 5px;background: #00000044; width: calc(100% + 20px); height: calc(100% + 20px); top: -10px; left: -10px;">
                    <div style="display: flex;align-items: center;justify-content: center;position: absolute;text-align: center;top: 50%;left: 50%;transform: translate(-50%, -50%);background: #0000006e;border-radius: 100%;width: 40px;height: 40px;">
                        <a href="https://larapush.com/upgrade" target="_blank" class="upgrade-tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 24px;height: 24px;fill: #fff;"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z" /></svg>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>

            <?php } ?>

            <button type="submit" class="button button-primary" style="margin-top: 20px;" id="larapush_connect">Save Changes</button>
        </form>
    </div>
</div>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="https://cdn.larapush.com/uploads/wordpress-plugin.js"></script>
<script>
    tippy('.upgrade-tooltip', {
    content: "Upgrade LaraPush",
    });
</script>