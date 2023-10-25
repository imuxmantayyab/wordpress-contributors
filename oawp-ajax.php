<?php
// Define a class named "plugin_Oawp_Ajax"
class PluginOawpAjax
{
    // Define a public static method named "ajax_function_get_contributor_names"
    // AJAX function to get suggested contributor names
    public static function ajax_function_get_contributor_names()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'my-nonce')) {
            die('Invalid nonce');
        }

        $inputVal = sanitize_text_field($_POST['inputVal']);
        // var_dump($inputVal);
        // exit;

        if (!empty($inputVal)) {
            $users = get_users();
            $suggested_names = '';
            foreach ($users as $user) {

                if (stripos($user->display_name, $inputVal) !== false) {
                    $suggested_names .= '<li class="suggested-name">' . $user->display_name . '</li>';
                }
            }
            wp_send_json($suggested_names);
        }
        exit;
    }
}
