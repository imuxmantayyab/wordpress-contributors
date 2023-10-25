<?php
// Define a class named "plugin_Public"
class plugin_Public
{
    // Define a public static method named "contributors_plugin_public_scripts"
    // This function is responsible for enqueueing the CSS file for the plugin on the public side of the website
    public static function contributors_plugin_public_scripts()
    {
        // Enqueue the 'contributors-style.css' file using the URL of the plugin directory and the '__FILE__' constant
        wp_enqueue_style( 'contributors-style.css', plugins_url('/css/contributors-style.css', __FILE__));
    }
}
