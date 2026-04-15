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
 */
define('MH_PLUG_VERSION', '1.0.0');
define('MH_PLUG_PATH', plugin_dir_path(__FILE__));
define('MH_PLUG_URL', plugin_dir_url(__FILE__));

/**
 * Activation Hook: Create the Wishlist custom DB table.
 */
function mh_plug_create_wishlist_table() {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'mh_woocommerce_wishlist';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id            BIGINT(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id       BIGINT(20)   UNSIGNED NOT NULL DEFAULT 0,
        session_id    VARCHAR(255) NOT NULL DEFAULT '',
        product_id    BIGINT(20)   UNSIGNED NOT NULL,
        variation_id  BIGINT(20)   UNSIGNED NOT NULL DEFAULT 0,
        date_added    DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
        PRIMARY KEY  (id),
        KEY user_id      (user_id),
        KEY session_id   (session_id(191)),
        KEY product_id   (product_id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'mh_plug_create_wishlist_table' );

/**
 * Load Admin Functionality.
 */
require_once MH_PLUG_PATH . 'admin/admin-menu.php';
require_once MH_PLUG_PATH . 'includes/theme-builder-cpt.php';
require_once MH_PLUG_PATH . 'includes/theme-builder-display.php';

/**
 * Load Elementor Integration.
 */
require_once MH_PLUG_PATH . 'elementor/elementor-loader.php';

/**
 * Load global plugin features based on dashboard settings.
 */
function mh_plug_load_global_features() {
    $settings = get_option('mh_plug_widgets_settings', []);

    // Menu Icons Feature
    $enable_menu_icons = isset($settings['enable_menu_icons']) ? (bool) $settings['enable_menu_icons'] : false;
    if ( $enable_menu_icons ) {
        require_once MH_PLUG_PATH . 'includes/menu-icon-fields.php';
    }

    // Age Gate Feature
    require_once MH_PLUG_PATH . 'includes/age-gate/class-mh-plug-age-gate-admin.php';
    require_once MH_PLUG_PATH . 'includes/age-gate/class-mh-plug-age-gate-meta.php';
    require_once MH_PLUG_PATH . 'includes/age-gate/class-mh-plug-age-gate-front.php';

    // WooCommerce Wishlist Feature — load only if WooCommerce is active and toggle is on
    $enable_wc_wishlist = isset($settings['enable_wc_wishlist']) ? (bool) $settings['enable_wc_wishlist'] : false;
    if ( $enable_wc_wishlist && class_exists('WooCommerce') ) {
        require_once MH_PLUG_PATH . 'includes/mh-wishlist-functions.php';
    }
}
add_action('plugins_loaded', 'mh_plug_load_global_features');

/**
 * Enqueue Frontend Scripts (Slick, icons, wishlist assets).
 */
function mh_plug_enqueue_frontend_scripts() {
    // Slick Slider
    wp_enqueue_style('slick-css',       MH_PLUG_URL . 'assets/slick/slick.css',       [], MH_PLUG_VERSION);
    wp_enqueue_style('slick-theme-css', MH_PLUG_URL . 'assets/slick/slick-theme.css', [], MH_PLUG_VERSION);
    wp_enqueue_script('slick-js',       MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);

    // MH Icons + Font Awesome
    wp_enqueue_style('mhi-icons',        MH_PLUG_URL . './elementor/assets/css/style.css',         [], MH_PLUG_VERSION);
    wp_enqueue_style('fontawesome-icons',MH_PLUG_URL . './assets/fontawesome-7/css/all.min.css',   [], MH_PLUG_VERSION);

    // WooCommerce Wishlist Assets — only when WooCommerce is active, feature is on, and we're on a WC page
    $settings           = get_option('mh_plug_widgets_settings', []);
    $enable_wc_wishlist = isset($settings['enable_wc_wishlist']) ? (bool) $settings['enable_wc_wishlist'] : false;

    if ( $enable_wc_wishlist && class_exists('WooCommerce') ) {
        $css_path = MH_PLUG_PATH . 'assets/css/mh-wishlist.css';
        $js_path  = MH_PLUG_PATH . 'assets/js/mh-wishlist.js';

        if ( file_exists($css_path) ) {
            wp_enqueue_style(
                'mh-wishlist-css',
                MH_PLUG_URL . 'assets/css/mh-wishlist.css',
                [],
                filemtime($css_path)
            );
        }

        if ( file_exists($js_path) ) {
            wp_enqueue_script(
                'mh-wishlist-js',
                MH_PLUG_URL . 'assets/js/mh-wishlist.js',
                ['jquery'],
                filemtime($js_path),
                true
            );

            wp_localize_script('mh-wishlist-js', 'mhWishlist', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('mh_wishlist_nonce'),
                'i18n'    => [
                    'addLabel'     => __('Add to Wishlist',          'mh-plug'),
                    'removeLabel'  => __('Remove from Wishlist',     'mh-plug'),
                    'emptyMessage' => __('Your wishlist is empty.',  'mh-plug'),
                ],
            ]);
        }
    }
}
add_action('wp_enqueue_scripts', 'mh_plug_enqueue_frontend_scripts');
/**
 * COMMAND: Database Diagnostic
 * This will reveal exactly why the database query is failing to find your header.
 */
add_action( 'wp_footer', function() {
    echo '<div style="position:fixed; bottom:0; left:0; width:100%; background:#111; color:#0f0; padding:20px; z-index:9999999; font-family:monospace; font-size:16px; border-top: 4px solid #0f0;">';
    echo '<strong style="color:#fff;">MH-PLUG DATABASE DIAGNOSTIC:</strong><br><br>';
    
    // Fetch ALL templates, even drafts
    $all_templates = get_posts([
        'post_type' => 'mh_templates',
        'post_status' => 'any',
        'posts_per_page' => -1
    ]);
    
    if ( empty( $all_templates ) ) {
        echo '<span style="color:red;">CRITICAL: Zero templates found in the database. Did you create one?</span><br>';
    } else {
        foreach( $all_templates as $t ) {
            $status = $t->post_status;
            
            // Check both new and legacy meta keys
            $type = get_post_meta( $t->ID, '_mh_template_type', true );
            if ( ! $type ) $type = get_post_meta( $t->ID, 'mh_template_type', true );
            
            $active = get_post_meta( $t->ID, '_mh_template_active', true );
            if ( ! $active ) $active = get_post_meta( $t->ID, 'mh_template_active', true );
            
            // Color code the output to easily spot the error
            $status_color = ( $status === 'publish' ) ? '#0f0' : 'red';
            $type_color   = ( $type === 'header' ) ? '#0f0' : 'orange';
            $active_color = ( $active === 'yes' ) ? '#0f0' : 'red';

            echo "Template ID: <strong>{$t->ID}</strong> | ";
            echo "Title: <strong>{$t->post_title}</strong> | ";
            echo "Status: <strong style='color:{$status_color};'>{$status}</strong> | ";
            echo "Type: <strong style='color:{$type_color};'>" . ( $type ? $type : 'NOT SET' ) . "</strong> | ";
            echo "Active: <strong style='color:{$active_color};'>" . ( $active ? $active : 'NOT SET' ) . "</strong><br>";
        }
    }
    echo '</div>';
}, 99999 );