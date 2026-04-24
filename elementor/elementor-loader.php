<?php
// Command: Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class MH_Elementor_Loader {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'print_inline_editor_styles']);

        // Register general widget assets (non-WC dependent)
        add_action( 'wp_enqueue_scripts', [$this, 'mh_plug_register_widget_assets'] );
        add_action( 'elementor/frontend/after_register_scripts', [$this, 'mh_plug_register_widget_assets'] );

        // Register WooCommerce-specific scripts
        add_action( 'wp_enqueue_scripts', [$this, 'mh_plug_enqueue_woo_scripts'] );
        add_action( 'elementor/frontend/after_register_scripts', [$this, 'mh_plug_enqueue_woo_scripts'] );
    }

    public function register_widget_category($elements_manager) {
        $elements_manager->add_category(
            'mh-plug-widgets',
            [
                'title' => esc_html__('MH Plug', 'mh-plug'),
                'icon' => 'eicon-plug',
            ]
        );
    }

    public function print_inline_editor_styles() {
        ?>
        <style id="mh-plug-editor-badge-styles">
            .elementor-element-wrapper [class^="mhi-"], .elementor-element-wrapper [class*=" mhi-"] {
                position: relative !important;
            }
            .elementor-element-wrapper [class^="mhi-"]::after, .elementor-element-wrapper [class*=" mhi-"]::after {
                content: 'MH';
                position: absolute;
                top: -10px;
                right: -45px;
                z-index: 10;
                background-color: #2293e9ff;
                color: #ffffff;
                padding: 2px 6px;
                font-size: 10px;
                line-height: 1;
                font-weight: 600;
                border-radius: 4px;
                text-transform: uppercase;
                box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }
        </style>
        <?php
    }

    public function register_widgets($widgets_manager) {
        $widget_options = get_option('mh_plug_widgets_settings', []);

        $widget_map = [
            'mh_heading'             => ['file' => 'mh-heading-widget.php', 'class' => 'MH_Heading_Widget'],
            'mh_site_logo'           => ['file' => 'mh-site-logo-widget.php', 'class' => 'MH_Site_Logo_Widget'],
            'mh_site_title'          => ['file' => 'mh-site-title-widget.php', 'class' => 'MH_Site_Title_Widget'],
            'mh_brush_text'          => ['file' => 'mh-brush-text-widget.php', 'class' => 'MH_Brush_Text_Widget'],
            'mh_brush_slider'        => ['file' => 'mh-brush-slider-widget.php', 'class' => 'MH_Brush_Slider_Widget'],
            'mh_image_circle'        => ['file' => 'mh-image-circle-widget.php', 'class' => 'MH_Image_Circle_Widget'],
            'mh_image_circle_slider' => ['file' => 'mh-image-circle-slider-widget.php', 'class' => 'MH_Image_Circle_Slider_Widget'],
            'mh_feature_card'        => ['file' => 'mh-feature-card-widget.php', 'class' => 'MH_Feature_Card_Widget'],
            'mh_post_carousel'       => ['file' => 'mh-post-carousel-widget.php', 'class' => 'MH_Post_Carousel_Widget'],
            'mh_synced_slider'       => ['file' => 'mh-synced-slider-widget.php', 'class' => 'MH_Synced_Slider_Widget'],
            'mh_button'              => ['file' => 'mh-button-widget.php', 'class' => 'MH_Button_Widget'],
            'mh_stacked_carousel'    => ['file' => 'mh-stacked-carousel-widget.php', 'class' => 'MH_Stacked_Carousel_Widget' ],
            'mh_wishlist_button'     => ['file' => 'mh-wishlist-button-widget.php', 'class' => 'MH_Wishlist_Button_Widget' ],
            'mh_wishlist_table'      => ['file' => 'mh-wishlist-table-widget.php',  'class' => 'MH_Wishlist_Table_Widget'  ],
            'mh_nav_menu'            => ['file' => 'mh-nav-menu-widget.php', 'class' => 'MH_Nav_Menu_Widget' ],
            'mh_copyright'           => ['file' => 'mh-copyright-widget.php', 'class' => 'MH_Copyright_Widget'],
            'mh_taxonomy_card'       => ['file' => 'mh-taxonomy-card-widget.php', 'class' => 'MH_Plug_Taxonomy_Card_Widget'],
        ];

        if ( class_exists( 'WooCommerce' ) ) {
            $wc_widget_map = [
                'mh_woo_add_to_cart'           => [ 'file' => 'mh-woo-add-to-cart-widget.php',  'class' => 'MH_Woo_Add_To_Cart_Widget' ],
                'mh_woo_attributes'            => [ 'file' => 'mh-woo-attributes-widget.php',   'class' => 'MH_Woo_Attributes_Widget' ],
                'mh_product_search'            => [ 'file' => 'mh-product-search-widget.php',   'class' => 'MH_Plug_Product_Search_Widget' ],
                'mh_product_title'             => [ 'file' => 'mh-product-title-widget.php',    'class' => 'MH_Product_Title_Widget' ],
                'mh_product_price'             => [ 'file' => 'mh-product-price-widget.php',    'class' => 'MH_Product_Price_Widget' ],
                'mh_product_short_description' => [ 'file' => 'mh-product-short-description-widget.php',  'class' => 'MH_Product_Short_Description_Widget' ],
                'mh_product_category'          => [ 'file' => 'mh-product-category-widget.php',           'class' => 'MH_Product_Category_Widget' ],
                'mh_product_tags'              => [ 'file' => 'mh-product-tags-widget.php',               'class' => 'MH_Product_Tags_Widget' ],
                'mh_product_brands'            => [ 'file' => 'mh-product-brands-widget.php',             'class' => 'MH_Product_Brands_Widget' ],
                'mh_product_breadcrumb'        => [ 'file' => 'mh-product-breadcrumb-widget.php',         'class' => 'MH_Product_Breadcrumb_Widget' ],
                'mh_product_rating'            => [ 'file' => 'mh-product-rating-widget.php',             'class' => 'MH_Product_Rating_Widget' ],
                'mh_product_gallery'           => [ 'file' => 'mh-product-gallery-widget.php',            'class' => 'MH_Product_Gallery_Widget' ],
                'mh_product_share'             => [ 'file' => 'mh-product-share-widget.php',              'class' => 'MH_Product_Share_Widget' ],
                'mh_product_data_accordion'    => [ 'file' => 'mh-product-data-accordion-widget.php',     'class' => 'MH_Product_Data_Accordion_Widget' ],
                'mh_header_wishlist'           => [ 'file' => 'mh-header-wishlist-widget.php', 'class' => 'MH_Header_Wishlist_Widget' ],
                'mh_header_cart'               => [ 'file' => 'mh-header-cart-widget.php',     'class' => 'MH_Header_Cart_Widget' ],
                'mh_product_grid'              => [ 'file' => 'mh-product-grid-widget.php',    'class' => 'MH_Product_Grid_Widget' ],
                
                // 🚀 NEW: COMPARE WIDGETS
                'mh_header_compare'            => [ 'file' => 'mh-header-compare-widget.php',      'class' => 'MH_Header_Compare_Widget' ],
                'mh_product_compare_btn'       => [ 'file' => 'mh-product-compare-btn-widget.php', 'class' => 'MH_Product_Compare_Btn_Widget' ],
            ];
            $widget_map = array_merge( $widget_map, $wc_widget_map );
        }

        foreach ($widget_map as $option_key => $widget_data) {
            
            // Check if widget is active in settings (default true if not set)
            $is_enabled = isset($widget_options[$option_key]) ? (bool)$widget_options[$option_key] : true;
            
            if ($is_enabled) {
                $file_path = MH_PLUG_PATH . 'elementor/widgets/' . $widget_data['file'];
                
                // 🚀 SAFETY CHECK: Does the file exist and is it readable?
                if (file_exists($file_path) && is_readable($file_path)) {
                    require_once $file_path;
                    
                    // 🚀 STRICT NAMESPACE FIX: Forces PHP to look globally for the class name
                    $class_name = '\\' . ltrim($widget_data['class'], '\\');

                    // 🚀 SAFETY CHECK: Did the class actually load from the file?
                    if (class_exists($class_name)) {
                        $widgets_manager->register(new $class_name());
                    }
                }
            }
        }
    }

    /**
     * Register general (non-WC) widget assets.
     */
    public function mh_plug_register_widget_assets() {

        $css_path_widgets = MH_PLUG_PATH . 'elementor/assets/css/mh-widgets.css';
        wp_register_style(
            'mh-widgets-css',
            MH_PLUG_URL . 'elementor/assets/css/mh-widgets.css',
            [],
            file_exists( $css_path_widgets ) ? filemtime( $css_path_widgets ) : MH_PLUG_VERSION
        );

        $js_path_widgets = MH_PLUG_PATH . 'elementor/assets/js/mh-widgets.js';
        wp_register_script(
            'mh-widgets-js',
            MH_PLUG_URL . 'elementor/assets/js/mh-widgets.js',
            [ 'jquery' ],
            file_exists( $js_path_widgets ) ? filemtime( $js_path_widgets ) : MH_PLUG_VERSION,
            true
        );

        $css_path_nav = MH_PLUG_PATH . 'elementor/assets/css/mh-nav-menu.css';
        wp_register_style(
            'mh-nav-menu-css',
            MH_PLUG_URL . 'elementor/assets/css/mh-nav-menu.css',
            [],
            file_exists( $css_path_nav ) ? filemtime( $css_path_nav ) : MH_PLUG_VERSION
        );

        $js_path_nav = MH_PLUG_PATH . 'elementor/assets/js/mh-nav-menu.js';
        wp_register_script(
            'mh-nav-menu-js',
            MH_PLUG_URL . 'elementor/assets/js/mh-nav-menu.js',
            [ 'jquery' ],
            file_exists( $js_path_nav ) ? filemtime( $js_path_nav ) : MH_PLUG_VERSION,
            true
        );
    }

    /**
     * Register and enqueue WooCommerce-specific scripts.
     */
    public function mh_plug_enqueue_woo_scripts() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        $js_path_universal = MH_PLUG_PATH . 'elementor/assets/js/mh-woo-scripts.js';
        wp_register_script(
            'mh-woo-scripts',
            MH_PLUG_URL . 'elementor/assets/js/mh-woo-scripts.js',
            [ 'jquery' ],
            file_exists( $js_path_universal ) ? filemtime( $js_path_universal ) : MH_PLUG_VERSION,
            true
        );

        $js_path_gallery = MH_PLUG_PATH . 'elementor/assets/js/mh-product-gallery.js';
        wp_register_script(
            'mh-product-gallery-script',
            MH_PLUG_URL . 'elementor/assets/js/mh-product-gallery.js',
            [ 'jquery', 'mh-slick-js' ],
            file_exists( $js_path_gallery ) ? filemtime( $js_path_gallery ) : MH_PLUG_VERSION,
            true
        );

        wp_script_add_data( 'mh-widgets-js', 'group', 1 );
        wp_enqueue_script( 'mh-woo-scripts' );

        if ( is_product() ) {
            wp_enqueue_script( 'mh-product-gallery-script' );
        }

        $ajax_data = [
            'ajax_url'       => admin_url( 'admin-ajax.php' ),
            'login_url'      => wc_get_page_permalink( 'myaccount' ),
            'wishlist_nonce' => wp_create_nonce( 'mh_wishlist_nonce' ),
        ];
        wp_add_inline_script(
            'mh-woo-scripts',
            'var mh_plug_ajax = ' . wp_json_encode( $ajax_data ) . ';',
            'before'
        );
    }

}

MH_Elementor_Loader::instance();

/**
 * Enqueue Custom Icon Font for Elementor Editor.
 */
function mh_plug_enqueue_editor_icons() {
    wp_enqueue_style(
        'mhi-icons',
        MH_PLUG_URL . 'elementor/assets/css/style.css',
        [],
        MH_PLUG_VERSION
    );
    wp_enqueue_style(
        'style',
        MH_PLUG_URL . 'elementor/assets/css/widget-style.css',
        [],
        MH_PLUG_VERSION
    );

    wp_enqueue_script(
        'mh-brush-color-filter-script',
        MH_PLUG_URL . 'elementor/assets/js/brush-color-filter.js',
        ['jquery'], 
        MH_PLUG_VERSION,
        true
    );
    
    wp_enqueue_script('slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
}

add_action( 'elementor/editor/before_enqueue_scripts', 'mh_plug_enqueue_editor_icons' );
add_action( 'elementor/frontend/after_register_scripts', 'mh_plug_enqueue_editor_icons' );