<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MH_Admin_Menu Class
 * Final Hybrid Version: Injects menu styles and enqueues page styles.
 */

class MH_Admin_Menu
{

    // Use an associative array for better management.
    // Key => Internal Name, Value => Display Name
    private $widgets = [
        'mh_heading' => 'Advanced Heading',
        'mh_site_logo' => 'Site Logo',
        'mh_site_title' => 'Site Title',

        // Global Settings
        'enable_menu_icons' => 'Menu Icons',

        'mh_brush_text' => 'Brush Text',
        'mh_brush_slider' => 'Brush Slider',
        'mh_image_circle' => 'Image Circle',
        'mh_image_circle_slider' => 'Image Circle Slider',
        'mh_feature_card' => 'Feature Card',
        'mh_post_carousel' => 'Post',
        'mh_synced_slider' => 'Synced Slider',
        'mh_button' => 'Button',
        'mh_stacked_carousel' => 'Stacked Carousel',
        'mh_breadcrumb' => 'Breadcrumb',
        // 🚀 NEW: Added FAQ Widget
        'mh_faq' => 'Animated FAQ',

        // Elementor WooCommerce Wishlist Widgets
        'mh_wishlist_button' => 'MH Wishlist Button',
        'mh_wishlist_table' => 'MH Wishlist Table',

        // WooCommerce Global Feature
        'enable_wc_wishlist' => 'Enable WooCommerce Wishlist',
        'mh_woo_add_to_cart' => 'MH Custom Add to Cart',
        'mh_woo_attributes' => 'MH Attributes',
        'mh_product_search' => 'Product Search',

        'mh_product_title' => 'Product Title',
        'mh_product_price' => 'Product Price',
        'mh_product_short_description' => 'Product Short Description',
        'mh_product_category' => 'Product Category',
        'mh_product_tags' => 'Product Tags',
        'mh_product_brands' => 'Product Brands',
        'mh_product_rating' => 'Product Rating',
        'mh_product_gallery' => 'Product Gallery',
        'mh_product_share' => 'Product Share',
        'mh_product_data_accordion' => 'Product Data (Tabs & Accordion)',
        'mh_nav_menu' => 'Nav Menu',
        'mh_copyright' => 'Copyright',
        'mh_header_wishlist' => 'Header Wishlist Icon',
        'mh_header_cart' => 'Header Cart Icon',
        'mh_product_grid' => 'Product Grid',
        'mh_taxonomy_card' => 'Taxonomy Card',

        // Compare Widgets added to Settings Dashboard
        'mh_header_compare' => 'Header Compare Icon',
        'mh_product_compare_btn' => 'Product Compare Button',
        'mh_compare_table' => 'Compare Table',
        'mh_taxonomy_slider' => 'Taxonomy Card Slider',
        'mh_product_slider' => 'Product Slider',
        'mh_product_filter' => 'Product Filter',
        'mh_product_attribute_filter' => 'Product Attribute Filter',
        'mh_blog_post' => 'Blog Post',
        'mh_single_post' => 'Single Post',
        'mh_testimonial' => 'Testimonial',
    ];

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_head', [$this, 'add_menu_inline_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_page_assets']);
    }

    public function register_menu()
    {
        add_menu_page(
            esc_html__('MH Plug Settings', 'mh-plug-ecommerce-builder-widgets'),
            esc_html__('MH Plug', 'mh-plug-ecommerce-builder-widgets'),
            'manage_options',
            'mh-plug-settings',
            [$this, 'render_settings_page'],
            'dashicons-admin-generic',
            58
        );

        add_submenu_page(
            'mh-plug-settings',
            esc_html__('Theme Builder', 'mh-plug-ecommerce-builder-widgets'),
            esc_html__('Theme Builder', 'mh-plug-ecommerce-builder-widgets'),
            'manage_options',
            'mh-plug-theme-builder',
            [$this, 'render_theme_builder_page']
        );

        // 🚀 NEW: Preloader Submenu Page
        add_submenu_page(
            'mh-plug-settings',
            esc_html__('Preloader', 'mh-plug-ecommerce-builder-widgets'),
            esc_html__('Preloader', 'mh-plug-ecommerce-builder-widgets'),
            'manage_options',
            'mh-plug-preloader',
            [$this, 'render_preloader_page']
        );

        // 🚀 Mini Cart Submenu Page
        add_submenu_page(
            'mh-plug-settings',
            esc_html__('Mini Cart', 'mh-plug-ecommerce-builder-widgets'),
            esc_html__('Mini Cart', 'mh-plug-ecommerce-builder-widgets'),
            'manage_options',
            'mh-plug-mini-cart',
            [$this, 'render_mini_cart_page']
        );

        // 🚀 WooCommerce Pages Customizer
        add_submenu_page(
            'mh-plug-settings',
            esc_html__('WooCommerce Pages', 'mh-plug-ecommerce-builder-widgets'),
            esc_html__('WooCommerce Pages', 'mh-plug-ecommerce-builder-widgets'),
            'manage_options',
            'mh-plug-woo-pages',
            [$this, 'render_woo_pages_settings']
        );
    }

    public function add_menu_inline_styles()
    {
        // Moved to admin/assets/css/admin-styles.css
    }

    public function enqueue_page_assets($hook)
    {
        $plugin_pages = [
            'toplevel_page_mh-plug-settings',
            'mh-plug_page_mh-plug-theme-builder',
            'mh-plug_page_mh-plug-preloader',
            'mh-plug_page_mh-plug-mini-cart',
            'mh-plug_page_mh-plug-woo-pages',
        ];
        if (in_array($hook, $plugin_pages, true)) {
            $css_path = MH_PLUG_PATH . 'admin/assets/css/admin-styles.css';
            if (file_exists($css_path)) {
                $css_version = filemtime($css_path);
                wp_enqueue_style('mh-plug-admin-styles', MH_PLUG_URL . 'admin/assets/css/admin-styles.css', [], $css_version);
            }

            $js_path = MH_PLUG_PATH . 'admin/assets/js/admin-scripts.js';
            if (file_exists($js_path)) {
                $js_version = filemtime($js_path);
                wp_enqueue_script('mh-plug-admin-scripts', MH_PLUG_URL . 'admin/assets/js/admin-scripts.js', ['jquery'], $js_version, true);

                wp_localize_script('mh-plug-admin-scripts', 'mhTbDeleteNonce', wp_create_nonce('mh_tb_delete_template'));
                wp_localize_script('mh-plug-admin-scripts', 'mhTbToggleNonce', wp_create_nonce('mh_tb_toggle_status'));
                wp_localize_script('mh-plug-admin-scripts', 'mhTbAjaxUrl', admin_url('admin-ajax.php'));
            }

            // 🚀 ENQUEUE WP MEDIA UPLOADER ONLY FOR THE PRELOADER PAGE
            if ($hook === 'mh-plug_page_mh-plug-preloader') {
                wp_enqueue_media();
            }

            // 🚀 ENQUEUE COLOR PICKER FOR SETTINGS PAGES
            if (in_array($hook, ['mh-plug_page_mh-plug-mini-cart', 'mh-plug_page_mh-plug-woo-pages'], true)) {
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
            }
        } elseif ('nav-menus.php' === $hook) {
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

            $fa_css_path = MH_PLUG_PATH . 'assets/fontawesome-6/css/all.min.css';
            if (file_exists($fa_css_path)) {
                wp_enqueue_style('mh-fontawesome', MH_PLUG_URL . 'assets/fontawesome-6/css/all.min.css', [], '6.7.2');
            }
        }
    }

    public function render_settings_page()
    {
        require_once MH_PLUG_PATH . 'admin/settings-page.php';
    }

    public function render_theme_builder_page()
    {
        require_once MH_PLUG_PATH . 'admin/theme-builder-page.php';
    }

    // 🚀 Renders the Preloader Dashboard Page
    public function render_preloader_page()
    {
        require_once MH_PLUG_PATH . 'admin/preloader-page.php';
    }

    public function render_mini_cart_page()
    {
        require_once MH_PLUG_PATH . 'admin/mini-cart-page.php';
    }

    public function render_woo_pages_settings()
    {
        require_once MH_PLUG_PATH . 'admin/woo-pages-settings.php';
    }

    public function register_settings()
    {
        register_setting(
            'mh_plug_settings_group',
            'mh_plug_widgets_settings',
            [$this, 'sanitize_widgets_settings']
        );

        // 🚀 NEW: Register the Preloader settings group
        register_setting(
            'mh_plug_preloader_group',
            'mh_plug_preloader_settings',
            [$this, 'sanitize_generic_array']
        );

        // 🚀 Register Mini Cart settings group
        register_setting(
            'mh_plug_mini_cart_group',
            'mh_plug_mini_cart_settings',
            [$this, 'sanitize_generic_array']
        );

        // 🚀 Register WooCommerce Pages Customizer settings
        register_setting(
            'mh_plug_woo_pages_group',
            'mh_plug_woo_pages_settings',
            [$this, 'sanitize_generic_array']
        );

        add_settings_section('mh_plug_widgets_section', null, null, 'mh-plug-settings-page');
        add_settings_section('mh_plug_global_settings_section', null, null, 'mh-plug-settings-page');
        add_settings_section('mh_plug_woocommerce_section', null, null, 'mh-plug-settings-page');

        foreach ($this->widgets as $key => $label) {

            $section_id = 'mh_plug_widgets_section';
            if ($key === 'enable_menu_icons') {
                $section_id = 'mh_plug_global_settings_section';
            } elseif (
                in_array($key, [
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
                    'mh_nav_menu',
                    'mh_copyright',
                    'mh_header_wishlist',
                    'mh_header_cart',
                    'mh_product_grid',
                    'mh_taxonomy_card',
                    'mh_header_compare',
                    'mh_product_compare_btn',
                    'mh_compare_table',
                    'mh_product_slider',
                    'mh_product_filter',
                    'mh_product_attribute_filter',


                ], true)
            ) {
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

    public function render_widget_toggle_field($args)
    {
        $options = get_option('mh_plug_widgets_settings');
        $id = esc_attr($args['id']);
        $is_checked = isset($options[$id]) ? (bool) $options[$id] : true;
        $checked_attr = $is_checked ? ' checked' : '';
        $current_value = $is_checked ? '1' : '0';
        $disabled_string = isset($args['disabled']) ? $args['disabled'] : '';

        echo "<div class='mh-widget-card'>";
        echo "  <div class='mh-widget-card-header'>";
        echo "      <div class='mh-widget-title'>" . esc_html($args['label']) . "</div>";
        echo "      <label class='switch'>";
        echo "<input class='cb' type='checkbox' name='mh_plug_widgets_settings[{$id}]' value='1' " . esc_attr($checked_attr) . " " . esc_attr($disabled_string) . "/>";
        echo "          <span class='toggle'>";
        echo "              <span class='left'>off</span>";
        echo "              <span class='right'>on</span>";
        echo "          </span>";
        echo "      </label>";
        echo "  </div>";

        if ($disabled_string !== '') {
            echo "<input type='hidden' name='mh_plug_widgets_settings[{$id}]' value='" . esc_attr($current_value) . "' />";
        }
        echo "</div>";
    }

    public function sanitize_widgets_settings($input)
    {
        $sanitized = [];
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                // All our values are boolean strings 'yes' or 'no'
                $sanitized[$key] = ($value === 'yes') ? 'yes' : 'no';
            }
        }
        return $sanitized;
    }

    /**
     * Smart array sanitization for WordPress.org compliance.
     * Explicitly sanitizes each key based on its expected data type:
     * - Colors  → sanitize_hex_color()
     * - URLs    → esc_url_raw()
     * - Toggles → 1 or 0
     * - Text    → sanitize_text_field()
     */
    public function sanitize_generic_array( $input ) {
        if ( ! is_array( $input ) ) {
            return [];
        }

        // Keys that hold a hex color value.
        $color_keys = [
            'loader_c1', 'loader_c2',
            'bg_c1', 'bg_c2',
            'text_c1', 'text_c2',
            'panel_bg', 'panel_text_color',
            'icon_color', 'icon_bg',
            'counter_bg', 'counter_color',
            'btn_view_cart_bg', 'btn_view_cart_text',
            'btn_checkout_bg', 'btn_checkout_text',
        ];

        // Keys that hold a URL.
        $url_keys = [ 'cart_url', 'checkout_url', 'image' ];

        // Keys that are boolean toggles (checkbox / radio "yes"/"no" or "1"/"0").
        $toggle_keys = [ 'enable', 'enable_mini_cart' ];

        $sanitized = [];

        foreach ( $input as $key => $value ) {
            $safe_key = sanitize_key( $key );

            if ( is_array( $value ) ) {
                $sanitized[ $safe_key ] = $this->sanitize_generic_array( $value );
                continue;
            }

            if ( in_array( $safe_key, $color_keys, true ) ) {
                $sanitized[ $safe_key ] = sanitize_hex_color( $value ) ?: '';
            } elseif ( in_array( $safe_key, $url_keys, true ) ) {
                $sanitized[ $safe_key ] = esc_url_raw( $value );
            } elseif ( in_array( $safe_key, $toggle_keys, true ) ) {
                $sanitized[ $safe_key ] = ( $value === 'yes' || $value === '1' || $value === true ) ? '1' : '0';
            } else {
                $sanitized[ $safe_key ] = sanitize_text_field( $value );
            }
        }

        return $sanitized;
    }
}

new MH_Admin_Menu();