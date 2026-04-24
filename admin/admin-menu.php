<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MH_Admin_Menu Class
 * Final Hybrid Version: Injects menu styles and enqueues page styles.
 */

class MH_Admin_Menu {

    // Use an associative array for better management.
    // Key => Internal Name, Value => Display Name
    private $widgets = [
        'mh_heading'        => 'Advanced Heading',
        'mh_site_logo'      => 'Site Logo', 
        'mh_site_title'     => 'Site Title',
        
        // Global Settings
        'enable_menu_icons' => 'Menu Icons',
        
        'mh_brush_text'     => 'Brush Text',
        'mh_brush_slider'   => 'Brush Slider',
        'mh_image_circle'   => 'Image Circle',
        'mh_image_circle_slider' => 'Image Circle Slider',
        'mh_feature_card'   => 'Feature Card',
        'mh_post_carousel'  => 'Post',
        'mh_synced_slider'  => 'Synced Slider',
        'mh_button'         => 'Button',
        'mh_stacked_carousel' => 'Stacked Carousel',
        'mh_breadcrumb'         => 'Breadcrumb',

        // Elementor WooCommerce Wishlist Widgets
        'mh_wishlist_button' => 'MH Wishlist Button',
        'mh_wishlist_table'  => 'MH Wishlist Table',

        // WooCommerce Global Feature
        'enable_wc_wishlist' => 'Enable WooCommerce Wishlist',
        'mh_woo_add_to_cart' => 'MH Custom Add to Cart',
        'mh_woo_attributes'  => 'MH Attributes',
        'mh_product_search'  => 'Product Search',

        'mh_product_title'              => 'Product Title',
        'mh_product_price'              => 'Product Price',
        'mh_product_short_description'  => 'Product Short Description',
        'mh_product_category'           => 'Product Category',
        'mh_product_tags'               => 'Product Tags',
        'mh_product_brands'             => 'Product Brands',
        'mh_product_rating'             => 'Product Rating',
        'mh_product_gallery'            => 'Product Gallery',
        'mh_product_share'              => 'Product Share',
        'mh_product_data_accordion'     => 'Product Data (Tabs & Accordion)',
        'mh_nav_menu'                   => 'Nav Menu',
        'mh_copyright'                  => 'Copyright',
        'mh_header_wishlist'            => 'Header Wishlist Icon',
        'mh_header_cart'                => 'Header Cart Icon',
        'mh_product_grid'               => 'Product Grid',
        'mh_taxonomy_card'              => 'Taxonomy Card',
        
        // 🚀 NEW: Compare Widgets added to Settings Dashboard
        'mh_header_compare'             => 'Header Compare Icon',
        'mh_product_compare_btn'        => 'Product Compare Button',
        'mh_compare_table'              => 'Compare Table', // 🚀 Add this line!
    ];

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_head', [$this, 'add_menu_inline_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_page_assets']);
    }

    public function register_menu() {
        add_menu_page(
            esc_html__('MH Plug Settings', 'mh-plug'),
            'MH Plug',
            'manage_options',
            'mh-plug-settings',
            [$this, 'render_settings_page'],
            'dashicons-admin-generic', 
            58
        );

        add_submenu_page(
            'mh-plug-settings',
            esc_html__('Theme Builder', 'mh-plug'),
            esc_html__('Theme Builder', 'mh-plug'),
            'manage_options',
            'mh-plug-theme-builder',
            [$this, 'render_theme_builder_page']
        );
    }

    public function add_menu_inline_styles() {
        ?>
        <style id="mh-plug-menu-styles">
        #adminmenu #toplevel_page_mh-plug-settings .wp-menu-image {
            background-image: url('<?php echo esc_url(MH_PLUG_URL . 'admin/assets/images/MH-icon.png'); ?>') !important;
            background-repeat: no-repeat !important;
            background-position: center center !important;
            background-size: 20px auto !important;
        }
        #adminmenu #toplevel_page_mh-plug-settings .wp-menu-image::before {
            content: '' !important;
        }
        #adminmenu li#toplevel_page_mh-plug-settings:hover a,
        #adminmenu li.current#toplevel_page_mh-plug-settings a,
        #adminmenu li.wp-has-current-submenu#toplevel_page_mh-plug-settings a {
            background: #004265 !important;
            color: #fff !important;
        }
        </style>
        <?php
    }

    public function enqueue_page_assets($hook) {
        if ( in_array( $hook, [ 'toplevel_page_mh-plug-settings', 'mh-plug_page_mh-plug-theme-builder' ], true ) ) {
            $css_path = MH_PLUG_PATH . 'admin/assets/css/admin-styles.css';
            if (file_exists($css_path)) {
                $css_version = filemtime($css_path);
                wp_enqueue_style('mh-plug-admin-styles', MH_PLUG_URL . 'admin/assets/css/admin-styles.css', [], $css_version);
            }

            $js_path = MH_PLUG_PATH . 'admin/assets/js/admin-scripts.js';
             if (file_exists($js_path)) {
                $js_version = filemtime($js_path);
                wp_enqueue_script('mh-plug-admin-scripts', MH_PLUG_URL . 'admin/assets/js/admin-scripts.js', ['jquery'], $js_version, true);

                wp_localize_script( 'mh-plug-admin-scripts', 'mhTbDeleteNonce', wp_create_nonce( 'mh_tb_delete_template' ) );
                wp_localize_script( 'mh-plug-admin-scripts', 'mhTbAjaxUrl', admin_url( 'admin-ajax.php' ) );
             }
        }
        elseif ( 'nav-menus.php' === $hook ) { 
            $picker_css_path = MH_PLUG_PATH . 'admin/assets/css/menu-icon-picker.css';
             if (file_exists($picker_css_path)) {
                $picker_css_version = filemtime($picker_css_path);
                wp_enqueue_style('mh-plug-menu-icon-picker-styles', MH_PLUG_URL . 'admin/assets/css/menu-icon-picker.css', [], $picker_css_version);
             }

            $picker_js_path = MH_PLUG_PATH . 'admin/assets/js/menu-icon-picker.js';
             if (file_exists($picker_js_path)) {
                 $picker_js_version = filemtime($picker_js_path);
                wp_enqueue_script('mh-plug-menu-icon-picker-script', MH_PLUG_URL . 'admin/assets/js/menu-icon-picker.js', ['jquery'], $picker_js_version, true);
             }

            $icon_font_css_path = MH_PLUG_PATH . 'elementor/assets/css/style.css'; 
            if (file_exists($icon_font_css_path)) {
                $icon_font_css_version = filemtime($icon_font_css_path);
                wp_enqueue_style('mh-icons-for-picker', MH_PLUG_URL . 'elementor/assets/css/style.css', [], $icon_font_css_version);
            }

            $fa_css_path = MH_PLUG_PATH . 'assets/fontawesome-7/css/all.min.css';
            if (file_exists($fa_css_path)) {
                wp_enqueue_style('mh-fontawesome-7', MH_PLUG_URL . 'assets/fontawesome-7/css/all.min.css', [], '7.1.0'); 
            }
        }
    }
    
    public function render_settings_page() { require_once MH_PLUG_PATH . 'admin/settings-page.php'; }

    public function render_theme_builder_page() { require_once MH_PLUG_PATH . 'admin/theme-builder-page.php'; }

    public function register_settings() {
        register_setting(
            'mh_plug_settings_group',
            'mh_plug_widgets_settings',
            [$this, 'sanitize_widgets_settings']
        );
        
        add_settings_section('mh_plug_widgets_section', null, null, 'mh-plug-settings-page');
        add_settings_section('mh_plug_global_settings_section', null, null, 'mh-plug-settings-page');
        add_settings_section('mh_plug_woocommerce_section', null, null, 'mh-plug-settings-page');

        foreach ($this->widgets as $key => $label) {
            
            $section_id = 'mh_plug_widgets_section'; 
            if ( $key === 'enable_menu_icons' ) {
                $section_id = 'mh_plug_global_settings_section';
            } elseif ( in_array( $key, [
                'enable_wc_wishlist',
                'mh_wishlist_button',
                'mh_wishlist_table',
                'mh_woo_add_to_cart',
                'mh_woo_attributes',
                'mh_product_search',
                'mh_product_title',
                'mh_product_price',
                'mh_product_short_description',
                'mh_product_category',
                'mh_product_tags',
                'mh_product_brands',
                //'mh_breadcrumb',
                'mh_product_rating',
                'mh_product_gallery',
                'mh_product_share',
                'mh_product_data_accordion', 
                'mh_nav_menu' ,
                'mh_copyright',
                'mh_header_wishlist', 
                'mh_header_cart', 
                'mh_product_grid',
                'mh_taxonomy_card',
                // 🚀 NEW: Route Compare widgets to WooCommerce section
                'mh_header_compare',
                'mh_product_compare_btn',
                'mh_compare_table',
            ], true ) ) {
                $section_id = 'mh_plug_woocommerce_section';
            }

            add_settings_field(
                $key, 
                $label, 
                [$this, 'render_widget_toggle_field'],
                'mh-plug-settings-page', 
                $section_id, 
                ['id' => $key, 'label' => $label]
            );
        }
    }
   
    public function render_widget_toggle_field($args) {
        $options = get_option('mh_plug_widgets_settings');
        $id = esc_attr($args['id']);
        $is_checked = isset($options[$id]) ? (bool)$options[$id] : true;
        $checked_attr = $is_checked ? ' checked' : '';
        $current_value = $is_checked ? '1' : '0';
        $disabled_string = isset($args['disabled']) ? $args['disabled'] : '';
        
        echo "<div class='mh-widget-card'>";
        echo "  <div class='mh-widget-card-header'>";
        echo "      <div class='mh-widget-title'>" . esc_html($args['label']) . "</div>";
        echo "      <label class='switch'>";
        echo "          <input class='cb' type='checkbox' name='mh_plug_widgets_settings[{$id}]' value='1' {$checked_attr} {$disabled_string}/>";
        echo "          <span class='toggle'>";
        echo "              <span class='left'>off</span>";
        echo "              <span class='right'>on</span>";
        echo "          </span>";
        echo "      </label>";
        echo "  </div>";
        
        if ( $disabled_string !== '' ) {
            echo "<input type='hidden' name='mh_plug_widgets_settings[{$id}]' value='{$current_value}' />";
        }
        echo "</div>";
    }

    public function sanitize_widgets_settings($input) {
        $sanitized_data = [];
        $widget_keys = array_keys($this->widgets);

        foreach ($widget_keys as $widget_key) {
            if (isset($input[$widget_key]) && $input[$widget_key] == '1') {
                $sanitized_data[$widget_key] = 1;
            } else {
                $sanitized_data[$widget_key] = 0;
            }
        }
        return $sanitized_data;
    }
}

new MH_Admin_Menu();