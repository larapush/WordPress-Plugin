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
     * Convert to camel case.
     * 
     * @since 1.0.0
     */
    public static function convertToCamelCase($string)
    {
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return lcfirst($string);
    }

    /**
     * Returns error response.
     * 
     * @since 1.0.0
     */
    public static function responseError($message)
    {
        set_transient('larapush_error', $message, 30);
        wp_redirect( admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings') );
        exit;
    }

    /**
     * Returns success response.
     * 
     * @since 1.0.0
     */
    public static function responseSuccess($message)
    {
        set_transient('larapush_success', $message, 30);
        wp_redirect( admin_url('admin.php?page=unlimited-push-notifications-by-larapush-settings') );
        exit;
    }

    /**
     * Assembles the url for the api calls.
     * 
     * @since 1.0.0
     */
    public static function assambleUrl($url, $url_path)
    {
        $panel_url = parse_url(trim($url));
        
        $panel_url = ($panel_url['scheme'] ?? 'https') . '://' . ($panel_url['host'] ?? 'localhost') . (isset($panel_url['port']) ? ':' . $panel_url['port'] : '');

        return $panel_url . $url_path;
    }

    public static function encode($msg)
    {
        // encode to base64
        $msg = base64_encode($msg);
        
        // split into chunks
        $msg = str_split($msg, 1);

        // convert to ascii
        $msg = array_map(function($char) {
            return ord($char)+415;
        }, $msg);

        return implode('-', $msg);
    }

    public static function decode($msg)
    {
        if (strpos($msg, '-') === false) {
            return $msg;
        }
        
        // split into chunks
        $msg = explode('-', $msg);

        // convert to ascii
        $msg = array_map(function($char) {
            return chr($char-415);
        }, $msg);


        // join
        $msg = implode('', $msg);

        // decode from base64
        $msg = base64_decode($msg);

        return $msg;
    }

    public static function checkConnection(){
        $url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_url', ''));
        $url_path = '/api/checkAuth';
        
        $email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_email', ''));
        $password = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_password', ''));

        // check if all 3 fields are filled
        if (empty($url) || empty($email) || empty($password)) {
            return false;
        }

        // Authenticate to LaraPush Panel
		$panel_url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::assambleUrl($url, $url_path);
        $response = wp_remote_post($panel_url, [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json',
            ],
            'body' => json_encode([
                'email' => $email,
                'password' => $password,
            ]),
        ]);

		try{
			if($response['response']['code'] != 200 or is_wp_error($response)){
				$body = json_decode($response['body']);
				$error = $body->message;
                add_settings_error( 'unlimited-push-notifications-by-larapush-settings', 'my_connection_error', 'Error: ' . $error, 'error' );
                return false;
			}else{
				$body = json_decode($response['body']);
				if(!$body->success){
                    add_settings_error( 'unlimited-push-notifications-by-larapush-settings', 'my_connection_error', 'Error: LaraPush v3 Pro Panel not found, Make sure you are using LaraPush v3 Pro Panel.', 'error' );
                    return false;
				}
                return true;
			}
		}catch(\Throwable $e){
			add_settings_error( 'unlimited-push-notifications-by-larapush-settings', 'my_connection_error', 'Error: LaraPush v3 Pro Panel not found, Make sure you are using LaraPush v3 Pro Panel.', 'error' );
            return false;
		}

        return false;
    }

    public static function codeIntegration(){
        $url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_url', ''));
        $url_path = '/api/codeIntegration';
        
        $email = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_email', ''));
        $password = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::decode(get_option('unlimited_push_notifications_by_larapush_panel_password', ''));

        // check if all 3 fields are filled
        if (empty($url) || empty($email) || empty($password)) {
            return false;
        }

        // Authenticate to LaraPush Panel
		$panel_url = Unlimited_Push_Notifications_By_Larapush_Admin_Helper::assambleUrl($url, $url_path);
        $response = wp_remote_post($panel_url, [
            'headers' => [
                'Accept' => 'application/json',
                'content-type' => 'application/json',
            ],
            'body' => json_encode([
                // TODO: Change this to your domain
                'domain' => 'test.larapush.com',
                'email' => $email,
                'password' => $password,
            ]),
        ]);

		try{
			if($response['response']['code'] != 200 or is_wp_error($response)){
                $body = json_decode($response['body']);
				$error = $body->message;
                Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Error: ' . $error);
                return false;
			}else{
                $body = json_decode($response['body']);
				if(!$body->success){
                    Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Error: LaraPush v3 Pro Panel not found, Make sure you are using LaraPush v3 Pro Panel.');
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

                    // Save new file names
                    $file_names = [];
                    $file_names[] = $body->data->integration->js_filename_for_site;
                    $file_names[] = $body->data->integration->sw_firebase_filename;
                    update_option('unlimited_push_notifications_by_larapush_js_filenames_for_site', $file_names);

                    $js_filename = $body->data->integration->js_filename_for_site;
                    $js_code = $body->data->integration->js_code;
                    $js_file = ABSPATH . $js_filename;
                    file_put_contents($js_file, $js_code);

                    // Writing service worker file
                    $sw_filename = $body->data->integration->sw_firebase_filename;
                    $sw_code = $body->data->integration->sw_firebase_code;
                    $sw_file = ABSPATH . $sw_filename;
                    file_put_contents($sw_file, $sw_code);
                    
                    $code_to_be_added_in_header = $body->data->integration->code_to_be_added_in_header;
                    update_option('unlimited_push_notifications_by_larapush_code_to_be_added_in_header', $code_to_be_added_in_header);
                }

                return true;
			}
		}catch(\Throwable $e){
            Unlimited_Push_Notifications_By_Larapush_Admin_Helper::responseError('Error: '.$e->getMessage());
            return false;
		}

        return false;
    }
}