<?php

/**
 * The admin-specific helper functionality of the plugin.
 *
 * @link       https://larapush.com
 * @since      1.0.0
 *
 * @package    Unlimited_Push_Notifications_By_Larapush
 * @subpackage Unlimited_Push_Notifications_By_Larapush/admin/helper
 * @author     Satyam Gupta <satyam@larapush.com>
 */
class Unlimited_Push_Notifications_By_Larapush_Admin_Helper
{
    /**
     * Saves the error and redirects to the settings page.
     *
     * @since 1.0.0
     */
    public static function responseErrorAndRedirect($message)
    {
        set_transient('larapush_error', $message, 30);
        wp_redirect(admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings'));
        exit();
    }

    /**
     * Saves the success and redirects to the settings page.
     *
     * @since 1.0.0
     */
    public static function responseSuccessAndRedirect($message)
    {
        set_transient('larapush_success', $message, 30);
        wp_redirect(admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings'));
        exit();
    }

    /**
     * Assembles the url for the api calls.
     *
     * @since 1.0.0
     */
    public static function assambleUrl($url, $url_path)
    {
        $panel_url = parse_url(trim($url));

        $panel_url =
            ($panel_url['scheme'] ?? 'https') .
            '://' .
            ($panel_url['host'] ?? 'localhost') .
            (isset($panel_url['port']) ? ':' . $panel_url['port'] : '');

        return $panel_url . $url_path;
    }

    /**
     * Encodes the Important Information to be saved in the database.
     *
     * @since 1.0.0
     */
    public static function encode($msg)
    {
        // encode to base64
        $msg = base64_encode($msg);

        // split into chunks
        $msg = str_split($msg, 1);

        // convert to ascii
        $msg = array_map(function ($char) {
            return ord($char) + 415;
        }, $msg);

        // join with - saparator
        $msg = implode('-', $msg);

        return $msg;
    }

    /**
     * Decodes the Important Information to be saved in the database.
     *
     * @since 1.0.0
     */
    public static function decode($msg)
    {
        // if no - found, return the same string
        if (strpos($msg, '-') === false) {
            return $msg;
        }

        // splits with - saparator
        $msg = explode('-', $msg);

        // convert from ascii
        $msg = array_map(function ($char) {
            return chr($char - 415);
        }, $msg);

        // join all
        $msg = implode('', $msg);

        // decode from base64
        $msg = base64_decode($msg);

        return $msg;
    }

    /**
     * Checks if the connection is valid.
     *
     * @since 1.0.0
     */
    public static function checkConnection()
    {
        // get the url and path
        $url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_url', '')
        );
        $url_path = '/api/checkAuth';

        // get the email and password
        $email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_email', '')
        );
        $password = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_password', '')
        );

        // check if all 3 fields exist
        if (empty($url) || empty($email) || empty($password)) {
            return false;
        }

        // Authenticate to LaraPush Panel
        $panel_url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::assambleUrl($url, $url_path);
        $response = wp_remote_post($panel_url, [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json'
            ],
            'body' => json_encode([
                'email' => $email,
                'password' => $password
            ])
        ]);

        try {
            $body = json_decode($response['body']);
            if (!$body->success) {
                add_settings_error(
                    'unlimited-push-notifications-by-larapush-settings',
                    'my_connection_error',
                    'Error: ' . $body->message,
                    'error'
                );
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            add_settings_error(
                'unlimited-push-notifications-by-larapush-settings',
                'my_connection_error',
                'Error: LaraPush v3 Pro Panel not found, Make sure you are using LaraPush v3 Pro Panel.',
                'error'
            );
            return false;
        }

        return false;
    }

    /**
     * Gets Campaign Filters from LaraPush Panel to select domains to send push notifications to.
     *
     * @since 1.0.0
     */
    public static function getCampaignFilter()
    {
        // get the url and path
        $url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_url', '')
        );
        $url_path = '/api/getCampaignFilter';

        // get the email and password
        $email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_email', '')
        );
        $password = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_password', '')
        );

        // check if all 3 fields exists
        if (empty($url) || empty($email) || empty($password)) {
            return false;
        }

        // Authenticate to LaraPush Panel
        $panel_url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::assambleUrl($url, $url_path);
        $response = wp_remote_post($panel_url, [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json'
            ],
            'body' => json_encode([
                'email' => $email,
                'password' => $password
            ])
        ]);

        try {
            $body = json_decode($response['body']);
            if (!$body->success) {
                add_settings_error(
                    'unlimited-push-notifications-by-larapush-settings',
                    'my_connection_error',
                    'Error: ' . $body->message,
                    'error'
                );
                return false;
            }

            update_option('unlimited_push_notifications_by_larapush_panel_domains', $body->data->domains);
            update_option(
                'unlimited_push_notifications_by_larapush_panel_migrated_domains',
                $body->data->migrated_domains
            );

            return true;
        } catch (\Throwable $e) {
            add_settings_error(
                'unlimited-push-notifications-by-larapush-settings',
                'my_connection_error',
                'Error: LaraPush v3 Pro Panel not found, Make sure you are using LaraPush v3 Pro Panel.',
                'error'
            );
            return false;
        }

        return false;
    }

    public static function codeIntegration()
    {
        // get the url and path
        $url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_url', '')
        );
        $url_path = '/api/codeIntegration';

        // get the email and password
        $email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_email', '')
        );
        $password = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_password', '')
        );

        // check if all 3 fields exists
        if (empty($url) || empty($email) || empty($password)) {
            return false;
        }

        // Get site url
        $site_url = str_replace(['http://', 'https://'], '', get_site_url());

        // Authenticate to LaraPush Panel
        $panel_url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::assambleUrl($url, $url_path);
        $response = wp_remote_post($panel_url, [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json'
            ],
            'body' => json_encode([
                // TODO: Change this to your domain
                'domain' => $site_url,
                'email' => $email,
                'password' => $password
            ])
        ]);

        try {
            // check if the response is valid
            $body = json_decode($response['body']);
            if (!$body->success) {
                add_settings_error(
                    'unlimited-push-notifications-by-larapush-settings',
                    'my_connection_error',
                    'Error: ' . $body->message,
                    'error'
                );
                return false;
            }

            // check if root of the website is writable, if yes, write the js file
            if (is_writable(ABSPATH)) {
                // Writing javascript file
                $old_files_name = get_option('unlimited_push_notifications_by_larapush_js_filenames_for_site', []);
                // Delete old files
                foreach ($old_files_name as $old_file_name) {
                    $old_file = ABSPATH . $old_file_name;
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }

                #Files Setup
                $file_names = [];
                $file_names[] = $body->data->integration->integrationCode->js_code_filename;
                $file_names[] = $body->data->integration->integrationCode->sw_firebase_code_filename;
                $file_names[] = $body->data->integration->ampIntegrationCode->helper_frame_filename;
                $file_names[] = $body->data->integration->ampIntegrationCode->permission_dialog_filename;
                update_option('unlimited_push_notifications_by_larapush_js_filenames_for_site', $file_names);

                // Writing javascript files
                $js_filename = $body->data->integration->integrationCode->js_code_filename;
                $js_code = $body->data->integration->integrationCode->js_code;
                $js_file = ABSPATH . $js_filename;
                file_put_contents($js_file, $js_code);

                // Writing service worker file
                $sw_filename = $body->data->integration->integrationCode->sw_firebase_code_filename;
                $sw_code = $body->data->integration->integrationCode->sw_firebase_code;
                $sw_file = ABSPATH . $sw_filename;
                file_put_contents($sw_file, $sw_code);

                // Writing helper frame file
                $helper_frame_filename = $body->data->integration->ampIntegrationCode->helper_frame_filename;
                $helper_frame = $body->data->integration->ampIntegrationCode->helper_frame;
                $helper_frame_file = ABSPATH . $helper_frame_filename;
                file_put_contents($helper_frame_file, $helper_frame);

                // Writing permission dialog file
                $permission_dialog_filename = $body->data->integration->ampIntegrationCode->permission_dialog_filename;
                $permission_dialog = $body->data->integration->ampIntegrationCode->permission_dialog;
                $permission_dialog_file = ABSPATH . $permission_dialog_filename;
                file_put_contents($permission_dialog_file, $permission_dialog);

                // Writing codes to database
                $code_to_be_added_in_header = $body->data->integration->integrationCode->code_to_be_added_in_header;
                $amp_code_to_be_added_in_header = $body->data->integration->ampIntegrationCode->header;
                $amp_code_widget = $body->data->integration->ampIntegrationCode->widget;
                $codes = [
                    'code_to_be_added_in_header' => $code_to_be_added_in_header,
                    'amp_code_to_be_added_in_header' => $amp_code_to_be_added_in_header,
                    'amp_code_widget' => $amp_code_widget
                ];

                update_option('unlimited_push_notifications_by_larapush_codes', $codes);
            } else {
                add_settings_error(
                    'unlimited-push-notifications-by-larapush-settings',
                    'my_connection_error',
                    'Error: ' . ABSPATH . ' is not writable, please make it writable.',
                    'error'
                );
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            add_settings_error(
                'unlimited-push-notifications-by-larapush-settings',
                'my_connection_error',
                'Error: ' . $e->getMessage(),
                'error'
            );
            return false;
        }

        update_option('unlimited_push_notifications_by_larapush_panel_integration_tried', true);
        return false;
    }

    /**
     * Send notification to LaraPush Panel
     *
     * @param int $postId
     * @return bool
     *
     * @since 1.0.0
     */
    public static function send_notification($postId)
    {
        // get post meta
        $meta = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::get_meta($postId);

        // get url and path
        $url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_url', '')
        );
        $url_path = '/api/createCampaign';

        // get email and password
        $email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_email', '')
        );
        $password = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(
            get_option('unlimited_push_notifications_by_larapush_panel_password', '')
        );

        // check if all 3 fields exists
        if (empty($url) || empty($email) || empty($password)) {
            return false;
        }

        // Authenticate to LaraPush Panel
        $panel_url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::assambleUrl($url, $url_path);
        $response = wp_remote_post($panel_url, [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json'
            ],
            'body' => json_encode([
                'email' => $email,
                'password' => $password,
                'domains' => get_option('unlimited_push_notifications_by_larapush_panel_domains_selected', []),
                'migrated_domains' => get_option(
                    'unlimited_push_notifications_by_larapush_panel_migrated_domains_selected',
                    []
                ),
                'title' => $meta['title'],
                'message' => $meta['body'],
                'icon' => $meta['icon'],
                'image' => $meta['image'],
                'url' => $meta['url'],
                'schedule_now' => 1,
                'source' => 'Wordpress Plugin'
            ])
        ]);

        try {
            if ($response['response']['code'] != 200 or is_wp_error($response)) {
                $body = json_decode($response['body']);
                $error = $body->message;
                set_transient('larapush_error', 'Error: ' . $error, 30);
                return false;
            } else {
                $body = json_decode($response['body']);
                if (!$body->success) {
                    set_transient('larapush_error', 'Error: Some issue occurred while sending Push Notification.', 30);
                    return false;
                }
                set_transient('larapush_success', $body->message, 30);
                return true;
            }
        } catch (\Throwable $e) {
            set_transient('larapush_error', 'Error: ' . $error->getMessage(), 30);
            return false;
        }

        return false;
    }

    /**
     * Gets the meta data of the post
     *
     * @param int $postId
     * @return array
     */
    public static function get_meta($postId)
    {
        // Getting the post title
        $title = get_the_title($postId);
        $title = str_replace('&#8211;', '-', $title);

        // Limiting the title to 7 words
        $title = explode(' ', $title);
        $title = implode(' ', array_slice($title, 0, 7));

        // Converting Get the description of the post
        $body = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::get_description($postId);

        // Getting the post icon
        $icon = get_site_icon_url(32) == '' ? '' : get_site_icon_url(32);

        // Getting the post url
        $url = get_permalink($postId);

        // Getting the post image
        $image = get_the_post_thumbnail_url($postId);

        // If the post image is not found, get the first image of the post
        // If the first image is not found, use example.com/image so to not show the image
        if ($image == false) {
            $image = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::first_image_of_the_post($postId);
            if ($image == false) {
                $image = '';
            }
        }

        return [
            'title' => $title,
            'body' => $body,
            'icon' => $icon,
            'url' => $url,
            'image' => $image,
            'postId' => $postId
        ];
    }

    /**
     * Gets the first image url of the post
     *
     * @param int $postId
     * @return string|false
     */
    public static function first_image_of_the_post($postId)
    {
        // Getting the post from ID
        $post = get_post($postId);

        // Getting the post content
        $content = $post->post_content;

        // Use a regular expression to search for the first image in the post content
        preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);

        // If an image is found, return the image URL
        if (!empty($image)) {
            $image_url = $image['src'];
            return $image_url;
        } else {
            return false;
        }
    }

    /**
     * Get the description of the post
     *
     * @param string $postId
     * @return string
     */
    public static function get_description($postId)
    {
        // Getting the description from the post excerpt
        $description = get_post_field('post_excerpt', $postId);

        // Yoast SEO plugin.
        if (empty($description) || !is_string($description)) {
            $description = get_post_meta($postId, '_yoast_wpseo_metadesc', true);
        }

        // Rank Math SEO plugin.
        if (empty($description) || !is_string($description)) {
            $description = get_post_meta($postId, 'rank_math_description', true);
        }

        // The SEO Framework
        if (empty($description) || !is_string($description)) {
            $description = get_post_meta($postId, '_genesis_description', true);
        }

        // SEOPress
        if (empty($description) || !is_string($description)) {
            $description = get_post_meta($postId, '_seopress_titles_desc', true);
        }

        // All In One SEO
        if (empty($description) || !is_string($description)) {
            $description = get_post_meta($postId, '_aioseo_og_description', true);
        }

        if (empty($description) || !is_string($description)) {
            // Getting the post content
            $html = apply_filters('the_content', strip_tags(get_post_field('post_content', $postId)));
            if (empty($html)) {
                return $html;
            }
            $dom = new \DOMDocument();
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $dom->preserveWhiteSpace = false;
            $description = $dom->textContent;
        }

        // Limiting the description to 14 words
        $description = explode(' ', $description);
        $description = implode(' ', array_slice($description, 0, 14));

        return $description;
    }
}
