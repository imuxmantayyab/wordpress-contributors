<?php
/**
 * Plugin Name: OOP AJAX Wordpress Contributors Plugin
 * Plugin URI:  https://google.com/
 * Description: This plugin is designed to streamline the management of WordPress contributors. It provides a user-friendly interface and tools to efficiently handle and organize contributors on your WordPress website. Whether you need to credit authors, collaborators, or anyone contributing to your content, this plugin simplifies the process, ensuring that proper recognition is given to those who play a role in your WordPress projects. It offers a seamless and intuitive contributor management solution to enhance your WordPress experience.
 * Version:     0.3
 * Author:      M Usman Tayyab
 * Author URI:  https://google.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: web-Contributors
 * Domain Path: /languages
 * Requires at least: 4.9
 * Requires PHP: 5.6 or later
 */

// Prevents the plugin file from being accessed directly
if ( ! defined( 'WPINC' ) ) {
    die;
}

// include public files
require_once plugin_dir_path(__FILE__) . 'public/public.php';

// include admin files
require_once plugin_dir_path(__FILE__) . 'admin/admin.php';
require_once plugin_dir_path(__FILE__) . 'oawp-ajax.php';

// Define a class named "OopAjaxWordpressContributorsPlugin"
class OopAjaxWordpressContributorsPlugin
{
    // Constructor function
    public function __construct()
    {
        $this->enqueue_admin_scripts();
        $this->enqueue_public_scripts();
        $this->adding_meta_box();
        $this->ajax_functions();
        // $this->load_textdomain();
    }

    // Function to add the metabox to the post editor
    public function add_contributors_metabox()
    {
        add_meta_box(
            'contributors_metabox',
            'Contributors',
            array($this, 'display_contributors_metabox'),
            'post'
        );
    }

    // Function to display the contributors metabox
    public function display_contributors_metabox()
    {

        // Get the stored contributor names for the current post
        $contributor_names = get_post_meta(get_the_ID(), 'contributors', true);

        // HTML output for the metabox
?>
        <div class="pt-main-box">
            <label for="contributor-input"><?php _e( 'Contributors:', 'web-Contributors' ); ?></label>
            <input type="text" id="contributor-input" name="contributor-input">
            <span class="loader" id="loading"></span>
        </div>
        <div id="suggested-names" class="cursorstyle"></div>
        <div id="selected-names">
            <ul class="ulstyle">
                <?php if (!empty($contributor_names)) { ?>
                    <?php foreach ($contributor_names as $contributor_name) {
                        if (!empty($contributor_name)) {
                    ?>
                            <li class="liStyle"><?php echo !empty($contributor_name) ? $contributor_name : ''; ?> <i class="fas fa-times show-before"></i></li>
                    <?php }
                    } ?>
                <?php } ?>
            </ul>
        </div>
        <input type="hidden" id="contributor-names" name="contributor-names" value="<?php echo !empty($contributor_names) ? implode(',', $contributor_names) : ''; ?>">
<?php
    }


    // Save the contributor names when the post is saved
    public function save_contributor_names($post_id)
    {
        if ( isset($_POST['contributor-names']) ) {
            $valueContributorNames = $_POST['contributor-names'];
            if (!empty($valueContributorNames)) {
                $contributor_names = array_map('sanitize_text_field', explode(',', $_POST['contributor-names']));
                update_post_meta($post_id, 'contributors', $contributor_names);
            } else {
                delete_post_meta($post_id, 'contributors');
            }
        }
    }

    public function enqueue_public_scripts()
    {

        // define the public class names for enqueueing scripts
        $plugin_Public  = 'plugin_Public';

        // enqueue public
        add_action('wp_enqueue_scripts', [$plugin_Public, 'contributors_plugin_public_scripts']);
    }

    public function enqueue_admin_scripts()
    {

        // define the admin class names for enqueueing scripts
        $plugin_Admin  = 'plugin_Admin';

        // enqueue admin
        add_action('admin_enqueue_scripts', [$plugin_Admin, 'contributors_plugin_Admin_scripts']);
    }

    public function adding_meta_box()
    {

        // add meta box for contributors to the post editor
        add_action('add_meta_boxes', array($this, 'add_contributors_metabox'));

        // save the list of contributors when the post is saved
        add_action('save_post', array($this, 'save_contributor_names'));

        // add a list of contributors to the post content
        add_filter('the_content', array($this, 'display_contributors_in_content'));
    }

    public function ajax_functions()
    {

        $plugin_Oawp_Ajax = "PluginOawpAjax";

        // AJAX function for getting suggested contributor names
        add_action('wp_ajax_ajax_function_get_contributor_names', array($plugin_Oawp_Ajax, 'ajax_function_get_contributor_names'));
        add_action('wp_ajax_nopriv_ajax_function_get_contributor_names', array($plugin_Oawp_Ajax, 'ajax_function_get_contributor_names'));
    }

    // public function load_textdomain() {
    //     // Load plugin text domain
    //     add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

    //     // Load translations for the plugin
    //     load_plugin_textdomain( 'web-Contributors', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    // }

    // Adds a list of contributors to the bottom of the post content.
    function display_contributors_in_content($content)
    {
        global $post;

        // Get the list of contributors for the current post.
        $contributor_names = get_post_meta($post->ID, 'contributors', true);

        // If the contributors meta key exists for the post, add the contributors list to the post content.
        if (key_exists('contributors', get_post_meta(get_the_ID()))) {
            $content .= '<div class="title-avatar-container">';
            $content .= '<h1 class="title">Contributors</h1>';
            $content .= '<div class="contributors-main">';

            if ($contributor_names) {
                // Loop through each contributor name and add their avatar and name to the contributors list.
                foreach ($contributor_names as $contributor_name) {
                    $avatar = get_avatar($contributor_name, 90);
                    if ($avatar && $contributor_name) {
                        $content .= '<div class="sub-contributors">';
                        $content .= '<div class="second">';
                        $content .= '<span>' . $avatar . '</span>';
                        $content .= '<p class="name">' .  $contributor_name . '</p>';
                        $content .= '</div>';
                        $content .= '</div>';
                    }
                }
            }

            $content .= '</div>';
            $content .= '</div>';
        }

        // Return the modified post content.
        return $content;
    }
}
$OopAjaxWordpressContributorsPlugin = new OopAjaxWordpressContributorsPlugin();