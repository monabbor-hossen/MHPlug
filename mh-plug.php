<?php
/**
 * Plugin Name:       MH Plug
 * Description:       A custom Elementor addon with a dedicated dashboard for managing widgets and features.
 * Plugin URI:        https://your-website.com/
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://your-website.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mh-plug
 * Elementor tested up to: 3.7.0
 * Elementor Pro tested up to: 3.7.0
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define Plugin Constants.
 * Constants make it easier to reference file paths and URLs without hardcoding them.
 */
define('MH_PLUG_VERSION', '1.0.0');
define('MH_PLUG_PATH', plugin_dir_path(__FILE__)); // The server path to the plugin directory.
define('MH_PLUG_URL', plugin_dir_url(__FILE__));   // The web URL to the plugin directory.

/**
 * Load Admin Functionality.
 * This file is responsible for creating the admin menu and settings pages.
 */
require_once MH_PLUG_PATH . 'admin/admin-menu.php';

/**
 * Load Elementor Integration.
 * This file checks if Elementor is active and then loads all your custom widgets.
 */
require_once MH_PLUG_PATH . 'elementor/elementor-loader.php';
/**
 * Load global plugin features based on dashboard settings.
 */
function mh_plug_load_global_features() {
    // Get our single array of all saved settings
    $settings = get_option('mh_plug_widgets_settings', []);

    // Check if the 'Enable Menu Icons' toggle is on (set to 1)
    $enable_menu_icons = isset($settings['enable_menu_icons']) ? (bool) $settings['enable_menu_icons'] : false;

    if ( $enable_menu_icons ) {
        require_once plugin_dir_path(__FILE__) . 'includes/menu-icon-fields.php';
    }

    // You can add other global features here later
    // Example:
    // $enable_feature_x = isset($settings['enable_feature_x']) ? (bool) $settings['enable_feature_x'] : false;
    // if ( $enable_feature_x ) {
    //    require_once plugin_dir_path(__FILE__) . 'includes/feature-x.php';
    // }

    // Load Age Gate Components
    require_once plugin_dir_path(__FILE__) . 'includes/age-gate/class-mh-plug-age-gate-admin.php';
    require_once plugin_dir_path(__FILE__) . 'includes/age-gate/class-mh-plug-age-gate-meta.php';
    require_once plugin_dir_path(__FILE__) . 'includes/age-gate/class-mh-plug-age-gate-front.php';
}
add_action('plugins_loaded', 'mh_plug_load_global_features');
// --- END OF ADDED LINE ---
/**
 * Enqueue Frontend Scripts.
 * This function loads the CSS and JavaScript needed for your widgets on the live website.
 * For example, the Slick Slider library for the post slider widget.
 */
function mh_plug_enqueue_frontend_scripts() {
    // Enqueue Slick Slider CSS files.
    wp_enqueue_style('slick-css', MH_PLUG_URL . 'assets/slick/slick.css', [], MH_PLUG_VERSION);
    wp_enqueue_style('slick-theme-css', MH_PLUG_URL . 'assets/slick/slick-theme.css', [], MH_PLUG_VERSION);

    // Enqueue Slick Slider JavaScript file.
    // The `['jquery']` part tells WordPress that this script depends on jQuery.
    // The `true` at the end tells WordPress to load this script in the footer.
    wp_enqueue_script('slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
    wp_enqueue_style(
        'mhi-icons', // Use the same handle name
        MH_PLUG_URL . './elementor/assets/css/style.css', // Correct path
        [],
        MH_PLUG_VERSION
    );
    
    wp_enqueue_style(
        'fontawesome-icons', // Use the same handle name
        MH_PLUG_URL . './assets/fontawesome-7/css/all.min.css', // Correct path
        [],
        MH_PLUG_VERSION
    );


}
// The 'wp_enqueue_scripts' action hook is the proper way to add scripts and styles to the frontend.
add_action('wp_enqueue_scripts', 'mh_plug_enqueue_frontend_scripts');

