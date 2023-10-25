<?php
// Define a class named "plugin_Admin"
class plugin_Admin
{
    // Define a public static method named "contributors_plugin_Admin_scripts"
    // This function is responsible for enqueueing the AJAX , CSS, JS, file for the plugin on the admin side of the website
    public static function contributors_plugin_Admin_scripts()
    {
        // Enqueue the jQuery script
        wp_enqueue_script('jquery');

        // Enqueue the "my-style" stylesheet located in the "css" directory of the plugin
        wp_enqueue_style('my-style', plugin_dir_url(__FILE__) . '/css/admin-contributors-style.css');

        // Enqueue the "my-script" script located in the "js" directory of the plugin,
        // which depends on jQuery and is version 1.0
        wp_enqueue_script('my-script', plugin_dir_url(__FILE__) . '/js/my-script.js', array('jquery'), '1.0', true);

        // Localize the "my-script" script with an object named "my_script_vars",
        // which contains the "ajax_url" property set to the URL of the "admin-ajax.php" file
        wp_localize_script('my-script', 'my_script_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('my-nonce'),
            'jsDeleteConfirmation'    => __('Are you sure you want to delete this item?', 'web-Contributors'),
        ));

        // Register the "font-awesome" stylesheet from a CDN, version 5.15.3, with no dependencies,
        // and enqueue it
        wp_register_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css', array(), '5.15.3', 'all');
        wp_enqueue_style('font-awesome');

        // Enqueue the "contributors-style.css" stylesheet located in the "css" directory of the plugin
        wp_enqueue_style('contributors-style.css', plugins_url('/css/contributors-style.css', __FILE__));
    }
}
