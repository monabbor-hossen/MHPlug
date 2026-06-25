<?php
if (!defined('ABSPATH')) exit;

final class MH_Plug_Elementor_Loader {

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

        add_action('wp_ajax_mh_get_compare_table', [$this, 'get_compare_table_ajax']);
        add_action('wp_ajax_nopriv_mh_get_compare_table', [$this, 'get_compare_table_ajax']);
        add_action('wp_ajax_mh_quick_view', [$this, 'quick_view_ajax']);
        add_action('wp_ajax_nopriv_mh_quick_view', [$this, 'quick_view_ajax']);

        add_action('wp_head', [$this, 'render_preloader_css']);
        add_action('wp_footer', [$this, 'render_preloader_html_js']);
    }

    public function render_preloader_css() {
        if ( is_admin() ) return;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only query string check, no data mutation.
        if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && sanitize_key( $_GET['action'] ) === 'elementor' ) ) return;

        $settings = get_option( 'mh_plug_preloader_settings', [] );
        if ( empty( $settings['enable'] ) || $settings['enable'] !== 'yes' ) return;

        // ── 1. Enqueue the STATIC preloader stylesheet ──────────────────────────
        $css_path    = MH_PLUG_PATH . 'assets/css/preloader.css';
        $css_version = file_exists( $css_path ) ? filemtime( $css_path ) : MH_PLUG_VERSION;
        wp_enqueue_style( 'mh-preloader', MH_PLUG_URL . 'assets/css/preloader.css', [], $css_version );

        // ── 2. Build ONLY the dynamic CSS custom properties ─────────────────────
        $bg_type    = isset( $settings['bg_type'] )    ? $settings['bg_type']    : 'solid';
        $bg_c1      = isset( $settings['bg_c1'] )      ? $settings['bg_c1']      : '#0f172a';
        $bg_c2      = isset( $settings['bg_c2'] )      ? $settings['bg_c2']      : '#1e293b';
        $bg_ang     = isset( $settings['bg_angle'] )   ? intval( $settings['bg_angle'] ) : 90;
        $transition = ! empty( $settings['transition'] ) ? intval( $settings['transition'] ) : 500;

        // Sanitize hex colours before embedding in CSS.
        $bg_c1 = sanitize_hex_color( $bg_c1 ) ?: '#0f172a';
        $bg_c2 = sanitize_hex_color( $bg_c2 ) ?: '#1e293b';

        $global_bg = ( $bg_type === 'gradient' )
            ? 'linear-gradient(' . $bg_ang . 'deg, ' . $bg_c1 . ', ' . $bg_c2 . ')'
            : $bg_c1;

        $dynamic_css = '#mh-global-preloader {';
        $dynamic_css .= '--mh-pl-bg: '         . $global_bg    . ';';
        $dynamic_css .= '--mh-pl-transition: ' . $transition   . 'ms;';
        $dynamic_css .= '}';

        wp_add_inline_style( 'mh-preloader', $dynamic_css );
    }

    public function render_preloader_html_js() {
        if (is_admin()) return;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only query string check, no data mutation.
        if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && sanitize_key( $_GET['action'] ) === 'elementor' ) ) return;

        $settings = get_option('mh_plug_preloader_settings', []);
        if (empty($settings['enable']) || $settings['enable'] !== 'yes') return;

        $type        = isset($settings['type']) ? $settings['type'] : 'css';
        $css_effect  = isset($settings['css_effect']) ? (string)$settings['css_effect'] : '1';
        $effect_size = !empty($settings['effect_size']) ? $settings['effect_size'] : '1.0';
        
        $eff_type    = isset($settings['loader_type']) ? $settings['loader_type'] : 'solid';
        $eff_c1      = isset($settings['loader_c1']) ? $settings['loader_c1'] : '#2293e9';
        $eff_c2      = isset($settings['loader_c2']) ? $settings['loader_c2'] : '#00ffd5';
        $eff_angle   = isset($settings['loader_angle']) ? $settings['loader_angle'] : '90';
        $eff_bg      = ($eff_type === 'gradient') ? "linear-gradient({$eff_angle}deg, {$eff_c1}, {$eff_c2})" : $eff_c1;
        $svg_stroke  = ($eff_type === 'gradient') ? 'url(#mh-svg-gradient)' : $eff_c1;

        $bg_type   = isset($settings['bg_type']) ? $settings['bg_type'] : 'solid';
        $bg_c1     = isset($settings['bg_c1']) ? $settings['bg_c1'] : '#0f172a';
        $bg_c2     = isset($settings['bg_c2']) ? $settings['bg_c2'] : '#1e293b';
        $bg_ang    = isset($settings['bg_angle']) ? $settings['bg_angle'] : '90';
        $global_bg = ($bg_type === 'gradient') ? "linear-gradient({$bg_ang}deg, {$bg_c1}, {$bg_c2})" : $bg_c1;

        $image       = !empty($settings['image']) ? $settings['image'] : '';
        $img_width   = !empty($settings['img_width']) ? $settings['img_width'] : '150';
        $delay       = !empty($settings['delay']) ? intval($settings['delay']) : 500;

        $custom_text = isset($settings['custom_text']) ? $settings['custom_text'] : '';
        $text_type   = isset($settings['text_type']) ? $settings['text_type'] : 'solid';
        $text_c1     = !empty($settings['text_c1']) ? $settings['text_c1'] : '#2293e9';
        $text_c2     = !empty($settings['text_c2']) ? $settings['text_c2'] : '#00ffd5';
        $text_angle  = !empty($settings['text_angle']) ? $settings['text_angle'] : '90';
        $text_size   = !empty($settings['text_size']) ? $settings['text_size'] : '16';
        $text_anim   = !empty($settings['text_anim']) ? $settings['text_anim'] : 'pulse';
        $text_bg     = ($text_type === 'gradient') ? "linear-gradient({$text_angle}deg, {$text_c1}, {$text_c2})" : $text_c1;
        $text_style  = ($text_type === 'gradient') ? "background: {$text_bg}; -webkit-background-clip: text; -webkit-text-fill-color: transparent;" : "color: {$text_c1};";

        // 🚀 Dynamic wrapper variables specifically so the Typing effect knows what color to cover the text with!
        $typing_vars = "--mh-bg-style: {$global_bg}; --mh-c1: {$text_c1};";

        echo '<div id="mh-global-preloader">';
        
        if ($eff_type === 'gradient') {
            echo '<svg style="width:0;height:0;position:absolute;" aria-hidden="true" focusable="false"><linearGradient id="mh-svg-gradient" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="' . esc_attr($eff_c1) . '" /><stop offset="100%" stop-color="' . esc_attr($eff_c2) . '" /></linearGradient></svg>';
        }

        echo '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 35px; z-index: 5;">';

        if ($type === 'image' && !empty($image)) {
            echo '<img src="' . esc_url($image) . '" alt="Loading..." style="width:' . esc_attr($img_width) . 'px; height:auto;" />';
        } else {
            echo '<div style="width: calc(100px * ' . esc_attr($effect_size) . '); height: calc(100px * ' . esc_attr($effect_size) . '); display: flex; align-items: center; justify-content: center;">';
            echo '<div style="transform: scale(' . esc_attr($effect_size) . '); --mh-c1: ' . esc_attr($eff_c1) . '; --mh-bg-style: ' . esc_attr($eff_bg) . '; --mh-svg-stroke: ' . esc_attr($svg_stroke) . ';">';
            switch ($css_effect) {
                case '1': echo '<div class="mh-loader-1"></div>'; break;
                case '2': echo '<div class="mh-loader-2"><div></div><div></div><div></div></div>'; break;
                case '3': echo '<div class="mh-loader-3"></div>'; break;
                case '4': echo '<div class="mh-loader-4"></div>'; break;
                case '5': echo '<div class="mh-loader-5"><div></div><div></div></div>'; break;
                case '6': echo '<div class="mh-loader-6"><div></div><div></div><div></div><div></div><div></div></div>'; break;
                case '7': echo '<div class="mh-loader-7"></div>'; break;
                case '8': echo '<div class="mh-loader-8"></div>'; break;
                case '9': echo '<div class="mh-loader-9"><div></div><div></div></div>'; break;
                case '10': echo '<div class="mh-loader-10"><div></div><div></div></div>'; break;
                case '11': echo '<div class="mh-loader-11"><div></div></div>'; break;
                case '12': echo '<div class="mh-loader-12"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'; break;
                case '13': echo '<div class="mh-loader-13"><div></div><div></div></div>'; break;
                case '14': echo '<div class="mh-loader-14"><div></div><div></div><div></div></div>'; break;
                case '15': echo '<div class="mh-loader-15"><div></div><div></div><div></div><div></div></div>'; break;
                case '16': echo '<div class="mh-loader-16"></div>'; break;
                case '17': echo '<div class="mh-loader-17"><div></div></div>'; break;
                case '18': echo '<div class="mh-loader-18"><div></div><div></div><div></div><div></div></div>'; break;
                case '19': echo '<div class="mh-loader-19"></div>'; break;
                case '20': echo '<div class="mh-loader-20"><div></div></div>'; break;
                case '21': echo '<div class="mh-loader-21 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg></div>'; break;
                case '22': echo '<div class="mh-loader-22 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></div>'; break;
                case '23': echo '<div class="mh-loader-23 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></div>'; break;
                case '24': echo '<div class="mh-loader-24 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></div>'; break;
                case '25': echo '<div class="mh-loader-25 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg></div>'; break;
                case '26': echo '<div class="mh-loader-26 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>'; break;
                case '27': echo '<div class="mh-loader-27 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 5v14M8 5v14M12 5v14M17 5v14M21 5v14"></path></svg><div class="mh-scanline"></div></div>'; break;
                case '28': echo '<div class="mh-loader-28 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8"></path><path d="M9 12h6"></path></svg></div>'; break;
                case '29': echo '<div class="mh-loader-29 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></div>'; break;
                case '30': echo '<div class="mh-loader-30 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>'; break;
                default:  echo '<div class="mh-loader-1"></div>'; break;
            }
            echo '</div></div>'; 
        }

        // 🚀 FIX: The Text animation wrapper safely wraps the span to allow independent gradients and the typing cover!
        if (!empty(trim($custom_text))) {
            echo '<div class="mh-text-anim-' . esc_attr($text_anim) . '" style="' . esc_attr($typing_vars) . ' font-size: ' . esc_attr($text_size) . 'px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; text-align: center; margin-top: 15px;"><span style="' . esc_attr($text_style) . '">' . esc_html($custom_text) . '</span></div>';
        }

        echo '</div></div>';

        echo '<script>
            window.addEventListener("load", function() {
                setTimeout(function() {
                    var preloader = document.getElementById("mh-global-preloader");
                    if (preloader) { preloader.classList.add("mh-preloader-hidden"); }
                }, ' . esc_js($delay) . ');
            });
        </script>';
    }

    // -- OMITTED UNCHANGED WIDGET REGISTRATION METHODS FOR BREVITY --
    public function quick_view_ajax() {
        check_ajax_referer( 'mh_quick_view_nonce', 'nonce' );

        if (!isset($_POST['product_id'])) { wp_send_json_error(['message' => __( 'No product ID provided.', 'mh-plug-ecommerce-builder-widgets' )]); }
        $product_id = intval($_POST['product_id']);
        $template_id = !empty($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        global $post, $product;
        $post = get_post($product_id);
        $product = wc_get_product($product_id);
        setup_postdata($post);
        ob_start();
        if ($template_id && class_exists('\Elementor\Plugin')) {
            if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
                $css_file->enqueue();
            }
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id, true);
        } else {
            echo '<div style="padding:30px; text-align:center; font-family:sans-serif;">';
            // Product image HTML comes from WooCommerce core and is safe to output directly.
            echo $product->get_image('woocommerce_single', ['style' => 'max-width:300px; border-radius:10px; margin-bottom:20px;']);
            echo '<h2 style="margin:0 0 10px; color:#111;">' . esc_html( $product->get_title() ) . '</h2>';
            echo '<div style="font-size:20px; color:#d63638; font-weight:bold; margin-bottom:20px;">' . wp_kses_post( $product->get_price_html() ) . '</div>';
            echo '<div>'; woocommerce_template_single_add_to_cart(); echo '</div></div>';
        }
        $html = ob_get_clean();
        wp_reset_postdata();
        wp_send_json_success(['html' => $html]);
    }

    public function get_compare_table_ajax() {
        check_ajax_referer( 'mh_compare_nonce', 'nonce' );

        if (!isset($_POST['product_ids']) || !is_array($_POST['product_ids'])) { wp_send_json_error(['html' => '<div class="mh-compare-empty"><h3>' . esc_html__('No products to compare', 'mh-plug-ecommerce-builder-widgets') . '</h3><p>' . esc_html__('Return to the shop to add products.', 'mh-plug-ecommerce-builder-widgets') . '</p></div>']); }
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
        if (empty($products)) wp_send_json_error(['html' => '<p>' . esc_html__( 'Products not found.', 'mh-plug-ecommerce-builder-widgets' ) . '</p>']);
        ob_start();
        ?>
        <table class="mh-compare-table">
            <tbody>
                <tr>
                    <th><?php esc_html_e( 'Product Details', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                    <?php foreach($products as $prod_obj):
                        global $product, $post;
                        $product = $prod_obj;
                        $post = get_post($prod_obj->get_id());
                        setup_postdata($post);
                    ?>
                        <td class="mh-compare-item">
                            <div class="mh-compare-image">
                                <a href="#" class="mh-remove-compare" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="<?php esc_attr_e( 'Remove', 'mh-plug-ecommerce-builder-widgets' ); ?>"><i class="fas fa-times"></i></a>
                                <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                            </div>
                            <h3 class="mh-compare-title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a></h3>
                            <div class="mh-compare-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
                            <div class="mh-compare-add-to-cart"><?php woocommerce_template_loop_add_to_cart(); ?></div>
                        </td>
                    <?php endforeach; wp_reset_postdata(); ?>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Description', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                    <?php foreach($products as $product): ?><td><?php echo wp_kses_post( wp_trim_words($product->get_short_description(), 15, '...') ); ?></td><?php endforeach; ?>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Rating', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                    <?php foreach($products as $product): ?><td><?php echo wp_kses_post( wc_get_rating_html($product->get_average_rating()) ); ?></td><?php endforeach; ?>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Availability', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                    <?php foreach($products as $product): ?><td><?php echo wp_kses_post( wc_get_stock_html($product) ); ?></td><?php endforeach; ?>
                </tr>
                <?php foreach($all_attributes as $attr_key => $attr_label): ?>
                    <tr>
                        <th><?php echo esc_html($attr_label); ?></th>
                        <?php foreach($products as $product): ?>
                            <td><?php $attr_val = $product->get_attribute($attr_key); echo !empty($attr_val) ? wp_kses_post($attr_val) : '-'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }

    public function register_widget_category($elements_manager) { $elements_manager->add_category('mh-plug-widgets', ['title' => esc_html__('MH Plug', 'mh-plug-ecommerce-builder-widgets'), 'icon' => 'eicon-plug']); }
    public function print_inline_editor_styles() { 
        wp_enqueue_style( 'mh-plug-editor-styles', MH_PLUG_URL . 'elementor/assets/css/editor.css', [], filemtime( MH_PLUG_PATH . 'elementor/assets/css/editor.css' ) );
    }
    
    // Continue below...
    public function register_widgets($widgets_manager) {
        $widget_options = get_option('mh_plug_widgets_settings', []);
        $widget_map = [
            'mh_heading' => ['file' => 'mh-heading-widget.php', 'class' => 'MH_Plug_Heading_Widget'],
            'mh_site_logo' => ['file' => 'mh-site-logo-widget.php', 'class' => 'MH_Plug_Site_Logo_Widget'],
            'mh_site_title' => ['file' => 'mh-site-title-widget.php', 'class' => 'MH_Plug_Site_Title_Widget'],
            'mh_brush_text' => ['file' => 'mh-brush-text-widget.php', 'class' => 'MH_Plug_Brush_Text_Widget'],
            'mh_brush_slider' => ['file' => 'mh-brush-slider-widget.php', 'class' => 'MH_Plug_Brush_Slider_Widget'],
            'mh_image_circle' => ['file' => 'mh-image-circle-widget.php', 'class' => 'MH_Plug_Image_Circle_Widget'],
            'mh_image_circle_slider' => ['file' => 'mh-image-circle-slider-widget.php', 'class' => 'MH_Plug_Image_Circle_Slider_Widget'],
            'mh_feature_card' => ['file' => 'mh-feature-card-widget.php', 'class' => 'MH_Plug_Feature_Card_Widget'],
            'mh_post_carousel' => ['file' => 'mh-post-carousel-widget.php', 'class' => 'MH_Plug_Post_Carousel_Widget'],
            'mh_synced_slider' => ['file' => 'mh-synced-slider-widget.php', 'class' => 'MH_Plug_Synced_Slider_Widget'],
            'mh_button' => ['file' => 'mh-button-widget.php', 'class' => 'MH_Plug_Button_Widget'],
            'mh_stacked_carousel' => ['file' => 'mh-stacked-carousel-widget.php', 'class' => 'MH_Plug_Stacked_Carousel_Widget'],
            'mh_wishlist_button' => ['file' => 'mh-wishlist-button-widget.php', 'class' => 'MH_Plug_Wishlist_Button_Widget'],
            'mh_wishlist_table' => ['file' => 'mh-wishlist-table-widget.php', 'class' => 'MH_Plug_Wishlist_Table_Widget'],
            'mh_nav_menu' => ['file' => 'mh-nav-menu-widget.php', 'class' => 'MH_Plug_Nav_Menu_Widget'],
            'mh_copyright' => ['file' => 'mh-copyright-widget.php', 'class' => 'MH_Plug_Copyright_Widget'],
            'mh_taxonomy_card' => ['file' => 'mh-taxonomy-card-widget.php', 'class' => 'MH_Plug_Taxonomy_Card_Widget'],
            'mh_breadcrumb' => [ 'file' => 'mh-breadcrumb-widget.php', 'class' => 'MH_Plug_Breadcrumb_Widget' ],
            // 🚀 NEW: Register FAQ Widget
            'mh_faq' => [ 'file' => 'mh-faq-widget.php', 'class' => 'MH_Plug_FAQ_Widget' ],
            'mh_testimonial' => [ 'file' => 'mh-testimonial-widget.php', 'class' => 'MH_Plug_Testimonial_Widget' ],
            'mh_taxonomy_slider' => ['file' => 'mh-taxonomy-slider-widget.php', 'class' => 'MH_Plug_Taxonomy_Slider_Widget'],
            'mh_blog_post' => [ 'file' => 'mh-blog-post-widget.php', 'class' => 'MH_Plug_Blog_Post_Widget' ],
            'mh_single_post' => [ 'file' => 'mh-single-post-widget.php', 'class' => 'MH_Plug_Single_Post_Widget' ],
        ];
        
        if ( class_exists( 'WooCommerce' ) ) {
            $wc_widget_map = [
                'mh_woo_add_to_cart' => [ 'file' => 'mh-woo-add-to-cart-widget.php', 'class' => 'MH_Plug_Woo_Add_To_Cart_Widget' ],
                'mh_woo_attributes' => [ 'file' => 'mh-woo-attributes-widget.php', 'class' => 'MH_Plug_Woo_Attributes_Widget' ],
                'mh_product_search' => [ 'file' => 'mh-product-search-widget.php', 'class' => 'MH_Plug_Product_Search_Widget' ],
                'mh_product_title' => [ 'file' => 'mh-product-title-widget.php', 'class' => 'MH_Plug_Product_Title_Widget' ],
                'mh_product_price' => [ 'file' => 'mh-product-price-widget.php', 'class' => 'MH_Plug_Product_Price_Widget' ],
                'mh_product_short_description' => [ 'file' => 'mh-product-short-description-widget.php', 'class' => 'MH_Plug_Product_Short_Description_Widget' ],
                'mh_product_category' => [ 'file' => 'mh-product-category-widget.php', 'class' => 'MH_Plug_Product_Category_Widget' ],
                'mh_product_tags' => [ 'file' => 'mh-product-tags-widget.php', 'class' => 'MH_Plug_Product_Tags_Widget' ],
                'mh_product_brands' => [ 'file' => 'mh-product-brands-widget.php', 'class' => 'MH_Plug_Product_Brands_Widget' ],
                'mh_product_rating' => [ 'file' => 'mh-product-rating-widget.php', 'class' => 'MH_Plug_Product_Rating_Widget' ],
                'mh_product_gallery' => [ 'file' => 'mh-product-gallery-widget.php', 'class' => 'MH_Plug_Product_Gallery_Widget' ],
                'mh_product_share' => [ 'file' => 'mh-product-share-widget.php', 'class' => 'MH_Plug_Product_Share_Widget' ],
                'mh_product_data_accordion' => [ 'file' => 'mh-product-data-accordion-widget.php', 'class' => 'MH_Plug_Product_Data_Accordion_Widget' ],
                'mh_header_wishlist' => [ 'file' => 'mh-header-wishlist-widget.php', 'class' => 'MH_Plug_Header_Wishlist_Widget' ],
                'mh_header_cart' => [ 'file' => 'mh-header-cart-widget.php', 'class' => 'MH_Plug_Header_Cart_Widget' ],
                'mh_product_grid' => [ 'file' => 'mh-product-grid-widget.php', 'class' => 'MH_Plug_Product_Grid_Widget' ],
                'mh_header_compare' => [ 'file' => 'mh-header-compare-widget.php', 'class' => 'MH_Plug_Header_Compare_Widget' ],
                'mh_product_compare_btn' => [ 'file' => 'mh-product-compare-btn-widget.php', 'class' => 'MH_Plug_Product_Compare_Btn_Widget' ],
                'mh_compare_table' => [ 'file' => 'mh-compare-table-widget.php', 'class' => 'MH_Plug_Compare_Table_Widget' ],
                'mh_product_slider' => [ 'file' => 'mh-product-slider-widget.php', 'class' => 'MH_Plug_Product_Slider_Widget' ],
                'mh_product_filter' => [ 'file' => 'mh-product-filter-widget.php', 'class' => 'MH_Plug_Product_Filter_Widget' ],
                'mh_product_attribute_filter' => [ 'file' => 'mh-product-attribute-filter-widget.php', 'class' => 'MH_Plug_Product_Attribute_Filter_Widget' ],
                'mh_combo_products'          => [ 'file' => 'mh-combo-products-widget.php',           'class' => 'MH_Plug_Combo_Products_Widget' ],
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
                    if (class_exists($class_name)) { 
                        $widget_instance = new $class_name();
                        
                        // The Safety Shield: Only register if it strictly matches Elementor's requirement
                        if ( $widget_instance instanceof \Elementor\Widget_Base ) {
                            $widgets_manager->register( $widget_instance );
                        } else {
                            // Log the broken widget so the developer can fix it, but DO NOT crash the site!
                            error_log( 'MH-Plug Widget Registration Failed: ' . get_class( $widget_instance ) . ' does not extend \Elementor\Widget_Base.' );
                        }
                    }
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
        
        // Register slick-js globally as mh-slick-js to avoid conflicts and ensure it's available
        wp_register_script('mh-slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
        wp_register_style('mh-slick-css', MH_PLUG_URL . 'assets/slick/slick.css', [], MH_PLUG_VERSION);
        
        wp_register_script('mh-woo-scripts', MH_PLUG_URL . 'elementor/assets/js/mh-woo-scripts.js', ['jquery'], MH_PLUG_VERSION, true);
        wp_register_script('mh-product-gallery-script', MH_PLUG_URL . 'elementor/assets/js/mh-product-gallery.js', ['jquery', 'mh-slick-js'], MH_PLUG_VERSION, true);
        wp_script_add_data('mh-widgets-js', 'group', 1);
        wp_enqueue_script('mh-woo-scripts');
        
        if (is_product()) {
            wp_enqueue_style('mh-slick-css');
            wp_enqueue_script('mh-product-gallery-script');
        }


        
        $ajax_data = [
            'ajax_url'        => admin_url('admin-ajax.php'),
            'login_url'       => wc_get_page_permalink('myaccount'),
            'wishlist_nonce'  => wp_create_nonce('mh_wishlist_nonce'),
            'quick_view_nonce'=> wp_create_nonce('mh_quick_view_nonce'),
            'compare_nonce'   => wp_create_nonce('mh_compare_nonce'),
            'live_search_nonce' => wp_create_nonce('mh_live_search_nonce'),
        ];
        $inline_js = 'var mh_plug_ajax = mh_plug_ajax || ' . wp_json_encode($ajax_data) . ';';
        wp_add_inline_script('mh-woo-scripts', $inline_js, 'before');
        wp_add_inline_script('mh-widgets-js', $inline_js, 'before');
    }
}

MH_Plug_Elementor_Loader::instance();

function mh_plug_enqueue_editor_icons() {
    wp_enqueue_style('mhi-icons', MH_PLUG_URL . 'elementor/assets/css/style.css', [], MH_PLUG_VERSION);
    wp_enqueue_style('style', MH_PLUG_URL . 'elementor/assets/css/widget-style.css', [], MH_PLUG_VERSION);
    wp_enqueue_script('mh-brush-color-filter-script', MH_PLUG_URL . 'elementor/assets/js/brush-color-filter.js', ['jquery'], MH_PLUG_VERSION, true);
    wp_enqueue_script('slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
}

add_action('elementor/editor/before_enqueue_scripts', 'mh_plug_enqueue_editor_icons');
add_action('elementor/frontend/after_register_scripts', 'mh_plug_enqueue_editor_icons');