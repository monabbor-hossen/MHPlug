<?php
if (!defined('ABSPATH')) exit;

final class MH_Elementor_Loader {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) { self::$_instance = new self(); }
        return self::$_instance;
    }

    private function __construct() {
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'print_inline_editor_styles']);

        add_action('wp_enqueue_scripts', [$this, 'mh_plug_register_widget_assets']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'mh_plug_register_widget_assets']);
        add_action('wp_enqueue_scripts', [$this, 'mh_plug_enqueue_woo_scripts']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'mh_plug_enqueue_woo_scripts']);

        // AJAX Hooks for Compare Table
        add_action('wp_ajax_mh_get_compare_table', [$this, 'get_compare_table_ajax']);
        add_action('wp_ajax_nopriv_mh_get_compare_table', [$this, 'get_compare_table_ajax']);

        // AJAX Hooks for QUICK VIEW
        add_action('wp_ajax_mh_quick_view', [$this, 'quick_view_ajax']);
        add_action('wp_ajax_nopriv_mh_quick_view', [$this, 'quick_view_ajax']);

        // 🚀 NEW: Render the Preloader globally on the frontend!
        add_action('wp_head', [$this, 'render_preloader_css']);
        add_action('wp_footer', [$this, 'render_preloader_html_js']);
    }

    // 🚀 NEW: Function to render Preloader CSS globally
    public function render_preloader_css() {
        if (is_admin()) return;
        // Smart bypass: Do not show preloader inside Elementor editor!
        if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) return; 

        $settings = get_option('mh_plug_preloader_settings', []);
        if (empty($settings['enable']) || $settings['enable'] !== 'yes') return;

        $bg_color   = !empty($settings['bg_color']) ? $settings['bg_color'] : '#ffffff';
        $img_width  = !empty($settings['img_width']) ? $settings['img_width'] : '150';
        $transition = !empty($settings['transition']) ? intval($settings['transition']) : 500;

        echo '<style>
            #mh-global-preloader { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: ' . esc_attr($bg_color) . '; z-index: 99999999; display: flex; align-items: center; justify-content: center; transition: opacity ' . esc_attr($transition) . 'ms ease, visibility ' . esc_attr($transition) . 'ms ease; }
            #mh-global-preloader.mh-preloader-hidden { opacity: 0; visibility: hidden; }
            #mh-global-preloader img { width: ' . esc_attr($img_width) . 'px; height: auto; }
        </style>';
    }

    // 🚀 NEW: Function to render Preloader HTML and JavaScript
    public function render_preloader_html_js() {
        if (is_admin()) return;
        if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) return;

        $settings = get_option('mh_plug_preloader_settings', []);
        if (empty($settings['enable']) || $settings['enable'] !== 'yes') return;

        $image = !empty($settings['image']) ? $settings['image'] : MH_PLUG_URL . 'admin/assets/images/MH-icon.png';
        $delay = !empty($settings['delay']) ? intval($settings['delay']) : 500;

        echo '<div id="mh-global-preloader"><img src="' . esc_url($image) . '" alt="Loading..." /></div>';
        echo '<script>
            window.addEventListener("load", function() {
                setTimeout(function() {
                    var preloader = document.getElementById("mh-global-preloader");
                    if (preloader) { preloader.classList.add("mh-preloader-hidden"); }
                }, ' . esc_js($delay) . ');
            });
        </script>';
    }

    // 🚀 The AJAX Function that builds the Quick View Popup
    public function quick_view_ajax() {
        if (!isset($_POST['product_id'])) {
            wp_send_json_error(['message' => 'No product ID provided.']);
        }

        $product_id = intval($_POST['product_id']);
        $template_id = !empty($_POST['template_id']) ? intval($_POST['template_id']) : 0;

        // Force WordPress to recognize this exact product context
        global $post, $product;
        $post = get_post($product_id);
        $product = wc_get_product($product_id);
        setup_postdata($post);

        ob_start();

        if ($template_id && class_exists('\Elementor\Plugin')) {
            // Force Elementor to print the CSS for the custom template
            if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
                $css_file->enqueue();
            }
            // Print the template layout
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id, true);
        } else {
            // Fallback Design if no template is selected
            echo '<div style="padding:30px; text-align:center; font-family:sans-serif;">';
            echo $product->get_image('woocommerce_single', ['style' => 'max-width:300px; border-radius:10px; margin-bottom:20px;']);
            echo '<h2 style="margin:0 0 10px; color:#111;">' . $product->get_title() . '</h2>';
            echo '<div style="font-size:20px; color:#d63638; font-weight:bold; margin-bottom:20px;">' . $product->get_price_html() . '</div>';
            echo '<div>';
            woocommerce_template_single_add_to_cart();
            echo '</div></div>';
        }

        $html = ob_get_clean();
        wp_reset_postdata();

        wp_send_json_success(['html' => $html]);
    }

    // 🚀 The AJAX Function that builds the Compare Table
    public function get_compare_table_ajax() {
        if (!isset($_POST['product_ids']) || !is_array($_POST['product_ids'])) {
            wp_send_json_error(['html' => '<div class="mh-compare-empty"><h3>No products to compare</h3><p>Return to the shop to add products.</p></div>']);
        }

        $product_ids = array_map('intval', $_POST['product_ids']);
        $products = [];
        $all_attributes = [];

        foreach ($product_ids as $id) {
            $prod = wc_get_product($id);
            if ($prod) {
                $products[] = $prod;
                foreach ($prod->get_attributes() as $attr_name => $attr) {
                    $label = $attr->is_taxonomy() ? wc_attribute_label($attr_name) : $attr->get_name();
                    $all_attributes[$attr_name] = $label;
                }
            }
        }

        if (empty($products)) wp_send_json_error(['html' => '<p>Products not found.</p>']);

        ob_start();
        ?>
        <table class="mh-compare-table">
            <tbody>
                <tr>
                    <th>Product Details</th>
                    <?php foreach($products as $prod_obj): 
                        global $product, $post;
                        $product = $prod_obj;
                        $post = get_post($prod_obj->get_id());
                        setup_postdata($post);
                    ?>
                        <td class="mh-compare-item">
                            <div class="mh-compare-image">
                                <a href="#" class="mh-remove-compare" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="Remove"><i class="fas fa-times"></i></a>
                                <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                            </div>
                            <h3 class="mh-compare-title"><a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a></h3>
                            <div class="mh-compare-price"><?php echo $product->get_price_html(); ?></div>
                            <div class="mh-compare-add-to-cart">
                                <?php woocommerce_template_loop_add_to_cart(); ?>
                            </div>
                        </td>
                    <?php endforeach; wp_reset_postdata(); ?>
                </tr>
                <tr>
                    <th>Description</th>
                    <?php foreach($products as $product): ?>
                        <td><?php echo wp_trim_words($product->get_short_description(), 15, '...'); ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th>Rating</th>
                    <?php foreach($products as $product): ?>
                        <td><?php echo wc_get_rating_html($product->get_average_rating()); ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th>Availability</th>
                    <?php foreach($products as $product): ?>
                        <td><?php echo wc_get_stock_html($product); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php foreach($all_attributes as $attr_key => $attr_label): ?>
                    <tr>
                        <th><?php echo esc_html($attr_label); ?></th>
                        <?php foreach($products as $product): ?>
                            <td>
                                <?php 
                                $attr_val = $product->get_attribute($attr_key);
                                echo !empty($attr_val) ? wp_kses_post($attr_val) : '-';
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        wp_send_json_success(['html' => ob_get_clean()]);
    }

    public function register_widget_category($elements_manager) {
        $elements_manager->add_category('mh-plug-widgets', ['title' => esc_html__('MH Plug', 'mh-plug'), 'icon' => 'eicon-plug']);
    }

    public function print_inline_editor_styles() {
        echo '<style id="mh-plug-editor-badge-styles"> .elementor-element-wrapper [class^="mhi-"] { position: relative !important; } .elementor-element-wrapper [class^="mhi-"]::after { content: "MH"; position: absolute; top: -10px; right: -45px; z-index: 10; background-color: #2293e9ff; color: #ffffff; padding: 2px 6px; font-size: 10px; line-height: 1; font-weight: 600; border-radius: 4px; text-transform: uppercase; box-shadow: 0 1px 2px rgba(0,0,0,0.2); } </style>';
    }

    public function register_widgets($widgets_manager) {
        $widget_options = get_option('mh_plug_widgets_settings', []);
        $widget_map = [
            'mh_heading' => ['file' => 'mh-heading-widget.php', 'class' => 'MH_Heading_Widget'],
            'mh_site_logo' => ['file' => 'mh-site-logo-widget.php', 'class' => 'MH_Site_Logo_Widget'],
            'mh_site_title' => ['file' => 'mh-site-title-widget.php', 'class' => 'MH_Site_Title_Widget'],
            'mh_brush_text' => ['file' => 'mh-brush-text-widget.php', 'class' => 'MH_Brush_Text_Widget'],
            'mh_brush_slider' => ['file' => 'mh-brush-slider-widget.php', 'class' => 'MH_Brush_Slider_Widget'],
            'mh_image_circle' => ['file' => 'mh-image-circle-widget.php', 'class' => 'MH_Image_Circle_Widget'],
            'mh_image_circle_slider' => ['file' => 'mh-image-circle-slider-widget.php', 'class' => 'MH_Image_Circle_Slider_Widget'],
            'mh_feature_card' => ['file' => 'mh-feature-card-widget.php', 'class' => 'MH_Feature_Card_Widget'],
            'mh_post_carousel' => ['file' => 'mh-post-carousel-widget.php', 'class' => 'MH_Post_Carousel_Widget'],
            'mh_synced_slider' => ['file' => 'mh-synced-slider-widget.php', 'class' => 'MH_Synced_Slider_Widget'],
            'mh_button' => ['file' => 'mh-button-widget.php', 'class' => 'MH_Button_Widget'],
            'mh_stacked_carousel' => ['file' => 'mh-stacked-carousel-widget.php', 'class' => 'MH_Stacked_Carousel_Widget'],
            'mh_wishlist_button' => ['file' => 'mh-wishlist-button-widget.php', 'class' => 'MH_Wishlist_Button_Widget'],
            'mh_wishlist_table' => ['file' => 'mh-wishlist-table-widget.php', 'class' => 'MH_Wishlist_Table_Widget'],
            'mh_nav_menu' => ['file' => 'mh-nav-menu-widget.php', 'class' => 'MH_Nav_Menu_Widget'],
            'mh_copyright' => ['file' => 'mh-copyright-widget.php', 'class' => 'MH_Copyright_Widget'],
            'mh_taxonomy_card' => ['file' => 'mh-taxonomy-card-widget.php', 'class' => 'MH_Plug_Taxonomy_Card_Widget'],
            'mh_breadcrumb' => [ 'file' => 'mh-breadcrumb-widget.php', 'class' => 'MH_Breadcrumb_Widget' ],
        ];

        if ( class_exists( 'WooCommerce' ) ) {
            $wc_widget_map = [
                'mh_woo_add_to_cart' => [ 'file' => 'mh-woo-add-to-cart-widget.php', 'class' => 'MH_Woo_Add_To_Cart_Widget' ],
                'mh_woo_attributes' => [ 'file' => 'mh-woo-attributes-widget.php', 'class' => 'MH_Woo_Attributes_Widget' ],
                'mh_product_search' => [ 'file' => 'mh-product-search-widget.php', 'class' => 'MH_Plug_Product_Search_Widget' ],
                'mh_product_title' => [ 'file' => 'mh-product-title-widget.php', 'class' => 'MH_Product_Title_Widget' ],
                'mh_product_price' => [ 'file' => 'mh-product-price-widget.php', 'class' => 'MH_Product_Price_Widget' ],
                'mh_product_short_description' => [ 'file' => 'mh-product-short-description-widget.php', 'class' => 'MH_Product_Short_Description_Widget' ],
                'mh_product_category' => [ 'file' => 'mh-product-category-widget.php', 'class' => 'MH_Product_Category_Widget' ],
                'mh_product_tags' => [ 'file' => 'mh-product-tags-widget.php', 'class' => 'MH_Product_Tags_Widget' ],
                'mh_product_brands' => [ 'file' => 'mh-product-brands-widget.php', 'class' => 'MH_Product_Brands_Widget' ],
                'mh_product_rating' => [ 'file' => 'mh-product-rating-widget.php', 'class' => 'MH_Product_Rating_Widget' ],
                'mh_product_gallery' => [ 'file' => 'mh-product-gallery-widget.php', 'class' => 'MH_Product_Gallery_Widget' ],
                'mh_product_share' => [ 'file' => 'mh-product-share-widget.php', 'class' => 'MH_Product_Share_Widget' ],
                'mh_product_data_accordion' => [ 'file' => 'mh-product-data-accordion-widget.php', 'class' => 'MH_Product_Data_Accordion_Widget' ],
                'mh_header_wishlist' => [ 'file' => 'mh-header-wishlist-widget.php', 'class' => 'MH_Header_Wishlist_Widget' ],
                'mh_header_cart' => [ 'file' => 'mh-header-cart-widget.php', 'class' => 'MH_Header_Cart_Widget' ],
                'mh_product_grid' => [ 'file' => 'mh-product-grid-widget.php', 'class' => 'MH_Product_Grid_Widget' ],
                'mh_header_compare' => [ 'file' => 'mh-header-compare-widget.php', 'class' => 'MH_Header_Compare_Widget' ],
                'mh_product_compare_btn' => [ 'file' => 'mh-product-compare-btn-widget.php', 'class' => 'MH_Product_Compare_Btn_Widget' ],
                'mh_compare_table' => [ 'file' => 'mh-compare-table-widget.php', 'class' => 'MH_Compare_Table_Widget' ],
            ];
            $widget_map = array_merge( $widget_map, $wc_widget_map );
        }

        foreach ($widget_map as $option_key => $widget_data) {
            $is_enabled = isset($widget_options[$option_key]) ? (bool)$widget_options[$option_key] : true;
            if ($is_enabled) {
                $file_path = MH_PLUG_PATH . 'elementor/widgets/' . $widget_data['file'];
                if (file_exists($file_path)) {
                    require_once $file_path;
                    $class_name = '\\' . ltrim($widget_data['class'], '\\');
                    if (class_exists($class_name)) { $widgets_manager->register(new $class_name()); }
                }
            }
        }
    }

    public function mh_plug_register_widget_assets() {
        wp_register_style('mh-widgets-css', MH_PLUG_URL . 'elementor/assets/css/mh-widgets.css', [], MH_PLUG_VERSION);
        wp_register_script('mh-widgets-js', MH_PLUG_URL . 'elementor/assets/js/mh-widgets.js', ['jquery'], MH_PLUG_VERSION, true);
        wp_register_style('mh-nav-menu-css', MH_PLUG_URL . 'elementor/assets/css/mh-nav-menu.css', [], MH_PLUG_VERSION);
        wp_register_script('mh-nav-menu-js', MH_PLUG_URL . 'elementor/assets/js/mh-nav-menu.js', ['jquery'], MH_PLUG_VERSION, true);
    }

    public function mh_plug_enqueue_woo_scripts() {
        if (!class_exists('WooCommerce')) return;
        wp_register_script('mh-woo-scripts', MH_PLUG_URL . 'elementor/assets/js/mh-woo-scripts.js', ['jquery'], MH_PLUG_VERSION, true);
        wp_register_script('mh-product-gallery-script', MH_PLUG_URL . 'elementor/assets/js/mh-product-gallery.js', ['jquery', 'mh-slick-js'], MH_PLUG_VERSION, true);
        wp_script_add_data('mh-widgets-js', 'group', 1);
        wp_enqueue_script('mh-woo-scripts');
        if (is_product()) wp_enqueue_script('mh-product-gallery-script');

        $ajax_data = [
            'ajax_url'       => admin_url('admin-ajax.php'),
            'login_url'      => wc_get_page_permalink('myaccount'),
            'wishlist_nonce' => wp_create_nonce('mh_wishlist_nonce'),
        ];
        wp_add_inline_script('mh-woo-scripts', 'var mh_plug_ajax = ' . wp_json_encode($ajax_data) . ';', 'before');
    }
}
MH_Elementor_Loader::instance();

function mh_plug_enqueue_editor_icons() {
    wp_enqueue_style('mhi-icons', MH_PLUG_URL . 'elementor/assets/css/style.css', [], MH_PLUG_VERSION);
    wp_enqueue_style('style', MH_PLUG_URL . 'elementor/assets/css/widget-style.css', [], MH_PLUG_VERSION);
    wp_enqueue_script('mh-brush-color-filter-script', MH_PLUG_URL . 'elementor/assets/js/brush-color-filter.js', ['jquery'], MH_PLUG_VERSION, true);
    wp_enqueue_script('slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
}
add_action('elementor/editor/before_enqueue_scripts', 'mh_plug_enqueue_editor_icons');
add_action('elementor/frontend/after_register_scripts', 'mh_plug_enqueue_editor_icons');