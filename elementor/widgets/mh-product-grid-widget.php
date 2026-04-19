<?php
/**
 * MH Product Grid Widget
 * Fixed Hover CSS overrides & Added Quick View trigger.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class MH_Product_Grid_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_grid'; }
    public function get_title() { return __( 'MH Product Grid', 'mh-plug' ); }
    public function get_icon() { return 'eicon-products'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {
        /* ==========================================
         * CONTENT: QUERY SETTINGS
         * ========================================== */
        $this->start_controls_section( 'section_query', [ 'label' => __( 'Query Settings', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'query_type', [ 'label' => __( 'Filter By', 'mh-plug' ), 'type' => Controls_Manager::SELECT, 'default' => 'latest', 'options' => [ 'latest' => __( 'Latest', 'mh-plug' ), 'best_sellers' => __( 'Best Sellers', 'mh-plug' ), 'top_rated' => __( 'Top Rated', 'mh-plug' ), 'sale' => __( 'On Sale', 'mh-plug' ), 'featured' => __( 'Featured', 'mh-plug' ), ], ] );
        $this->add_control( 'posts_per_page', [ 'label' => __( 'Number of Products', 'mh-plug' ), 'type' => Controls_Manager::NUMBER, 'default' => 8, 'min' => 1, 'max' => 50, ] );
        $this->add_responsive_control( 'columns', [ 'label' => __( 'Columns', 'mh-plug' ), 'type' => Controls_Manager::SELECT, 'default' => '4', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ], 'selectors' => [ '{{WRAPPER}} .mh-product-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ], ] );
        $this->end_controls_section();

        /* ==========================================
         * CONTENT: CARD ELEMENTS
         * ========================================== */
        $this->start_controls_section( 'section_elements', [ 'label' => __( 'Card Elements', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        $this->add_control( 'show_category', [ 'label' => __( 'Show Category', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_rating', [ 'label' => __( 'Show Star Rating', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_badge', [ 'label' => __( 'Show Sale Badge', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->end_controls_section();

        /* ==========================================
         * STYLE: GRID & CARD
         * ========================================== */
        $this->start_controls_section( 'section_style_grid', [ 'label' => __( 'Grid & Card Style', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        $this->add_responsive_control( 'grid_gap', [ 'label' => __( 'Grid Gap', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 20 ], 'selectors' => [ '{{WRAPPER}} .mh-product-grid' => 'gap: {{SIZE}}px;' ], ] );
        $this->add_control( 'card_bg', [ 'label' => __( 'Card Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-product-card' => 'background-color: {{VALUE}};' ], ] );
        $this->add_control( 'card_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 10 ], 'selectors' => [ '{{WRAPPER}} .mh-product-card' => 'border-radius: {{SIZE}}px;' ], ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .mh-product-card' ] );
        $this->end_controls_section();

        /* ==========================================
         * STYLE: TYPOGRAPHY & COLORS
         * ========================================== */
        $this->start_controls_section( 'section_style_typography', [ 'label' => __( 'Typography & Colors', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'color_title', [ 'label' => __( 'Title Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ '{{WRAPPER}} .mh-product-title a' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'color_title_hover', [ 'label' => __( 'Title Hover', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'selectors' => [ '{{WRAPPER}} .mh-product-title a:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'typo_title', 'selector' => '{{WRAPPER}} .mh-product-title a' ] );
        $this->add_control( 'color_price', [ 'label' => __( 'Price Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#d63638', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .mh-product-price' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'typo_price', 'selector' => '{{WRAPPER}} .mh-product-price' ] );
        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) return;
        $settings = $this->get_settings_for_display();

        $args = [ 'post_type' => 'product', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'posts_per_page' => $settings['posts_per_page'] ];
        switch ( $settings['query_type'] ) {
            case 'best_sellers': $args['meta_key'] = 'total_sales'; $args['orderby'] = 'meta_value_num'; break;
            case 'top_rated': $args['meta_key'] = '_wc_average_rating'; $args['orderby'] = 'meta_value_num'; break;
            case 'sale': $product_ids_on_sale = wc_get_product_ids_on_sale(); $args['post__in'] = !empty($product_ids_on_sale) ? $product_ids_on_sale : [0]; break;
            case 'featured': $args['tax_query'] = [['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured', 'operator' => 'IN']]; break;
            default: $args['orderby'] = 'date'; $args['order'] = 'DESC'; break;
        }

        $loop = new \WP_Query( $args );
        if ( ! $loop->have_posts() ) { echo '<p>' . esc_html__( 'No products found.', 'mh-plug' ) . '</p>'; return; }
        ?>
        <style>
            .mh-product-grid { display: grid; }
            .mh-product-card { background: #fff; position: relative; transition: all 0.3s ease; display: flex; flex-direction: column; }
            .mh-product-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.08); transform: translateY(-3px); z-index: 5; }
            
            .mh-product-image-wrap { position: relative; overflow: hidden; background: #f7f7f7; display: flex; align-items: center; justify-content: center; aspect-ratio: 1/1; }
            .mh-product-image-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
            .mh-product-card:hover .mh-product-image-wrap img { transform: scale(1.05); }

            .mh-product-badges { position: absolute; top: 15px; left: 15px; z-index: 2; display: flex; flex-direction: column; gap: 5px; }
            .mh-badge { background: #d63638; color: #fff; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 3px; text-transform: uppercase; }

            /* 🚀 THE FIX: Bulletproof Action Buttons */
            .mh-product-actions { 
                position: absolute; bottom: -60px; left: 0; width: 100%; display: flex; justify-content: center; gap: 8px; 
                opacity: 0; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); z-index: 10; padding-bottom: 15px;
            }
            .mh-product-card:hover .mh-product-actions { bottom: 0; opacity: 1; }
            
            /* Using !important to brutally override Astra/Elementor default button styles */
            .mh-product-grid .mh-action-btn,
            .mh-product-grid .mh-action-btn:visited { 
                width: 40px !important; height: 40px !important; min-height: 40px !important;
                background: #ffffff !important; border-radius: 50% !important; 
                display: flex !important; align-items: center !important; justify-content: center !important; 
                color: #333333 !important; font-size: 16px !important; padding: 0 !important; margin: 0 !important;
                box-shadow: 0 5px 15px rgba(0,0,0,0.15) !important; text-decoration: none !important; border: none !important; cursor: pointer !important;
                line-height: 1 !important;
            }
            .mh-product-grid .mh-action-btn:hover,
            .mh-product-grid .mh-action-btn.added { 
                background: #d63638 !important; color: #ffffff !important; 
            }
            .mh-product-grid .mh-action-btn svg { width: 16px; height: 16px; fill: currentColor; }

            /* Content Area */
            .mh-product-info { padding: 20px; text-align: left; display: flex; flex-direction: column; flex-grow: 1; }
            .mh-product-cat { font-size: 12px; color: #888; text-transform: uppercase; margin-bottom: 5px; font-weight: 500; }
            .mh-product-title { margin: 0 0 10px 0; font-size: 16px; font-weight: 600; line-height: 1.4; }
            .mh-product-title a { color: inherit; text-decoration: none; transition: color 0.3s; }
            .mh-product-rating { margin-bottom: 10px; font-size: 12px; }
            .mh-product-rating .star-rating { font-size: 13px; color: #f5b223; }
            .mh-product-price { font-weight: 700; font-size: 16px; margin-top: auto; }
            .mh-product-price del { color: #aaa; font-weight: 400; font-size: 14px; margin-right: 5px; }
            .mh-product-price ins { text-decoration: none; }
        </style>

        <div class="mh-product-grid">
            <?php
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                $product_id = $product->get_id();
                $in_wishlist = function_exists('mh_wishlist_has_product') ? mh_wishlist_has_product($product_id) : false;
                ?>
                <div class="mh-product-card">
                    <div class="mh-product-image-wrap">
                        <a href="<?php the_permalink(); ?>"><?php echo $product->get_image('woocommerce_thumbnail'); ?></a>
                        <?php if ( $settings['show_badge'] === 'yes' && $product->is_on_sale() ) : ?>
                            <div class="mh-product-badges"><span class="mh-badge"><?php esc_html_e('Sale', 'mh-plug'); ?></span></div>
                        <?php endif; ?>

                        <div class="mh-product-actions">
                            <a href="#" class="mh-action-btn mh-quick-view-trigger" data-product-id="<?php echo esc_attr($product_id); ?>" title="<?php esc_html_e( 'Quick View', 'mh-plug' ); ?>">
                                <i class="fas fa-shopping-bag"></i>
                            </a>

                            <a href="#" class="mh-action-btn mh-advanced-wishlist-btn <?php echo $in_wishlist ? 'added' : ''; ?>" data-product-id="<?php echo esc_attr($product_id); ?>" data-behavior="toggle" title="<?php esc_html_e('Wishlist', 'mh-plug'); ?>">
                                <span class="mh-icon-normal"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 429.3l175.2-161.3c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg></span>
                                <span class="mh-icon-added" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg></span>
                            </a>
                        </div>
                    </div>

                    <div class="mh-product-info">
                        <?php if ( $settings['show_category'] === 'yes' ) : ?><div class="mh-product-cat"><?php echo wc_get_product_category_list( $product_id, ', ' ); ?></div><?php endif; ?>
                        <h3 class="mh-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php if ( $settings['show_rating'] === 'yes' ) : ?><div class="mh-product-rating"><?php echo wc_get_rating_html( $product->get_average_rating() ); ?></div><?php endif; ?>
                        <div class="mh-product-price"><?php echo $product->get_price_html(); ?></div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <script>
            jQuery(document).ready(function($){
                var mhAjaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
                var mhNonce   = '<?php echo esc_attr( wp_create_nonce( 'mh_wishlist_nonce' ) ); ?>';

                // Wishlist Toggle Logic
                $('.mh-product-grid .mh-advanced-wishlist-btn').off('click').on('click', function(e){
                    e.preventDefault(); 
                    var $btn = $(this);
                    $btn.css({'opacity': '0.5', 'pointer-events': 'none'});
                    $.post(mhAjaxUrl, { action: 'mh_wishlist_toggle', product_id: $btn.data('product-id'), security: mhNonce }, function(response) {
                        $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                        if(response.success) {
                            var status = response.data.status;
                            if(status === 'added') {
                                $btn.addClass('added').find('.mh-icon-normal').hide(); $btn.find('.mh-icon-added').show();
                            } else {
                                $btn.removeClass('added').find('.mh-icon-added').hide(); $btn.find('.mh-icon-normal').show();
                            }
                            $(document).trigger('mh_wishlist_updated', [status]);
                        }
                    });
                });

                // Quick View Trigger Logic
                if ($('#mh-quick-view-modal').length === 0) {
                    $('body').append('<div id="mh-quick-view-modal" class="mh-qv-overlay"><div class="mh-qv-content"><span class="mh-qv-close"><i class="fas fa-times"></i></span><div class="mh-qv-body"></div></div></div>');
                }

                $('.mh-quick-view-trigger').on('click', function(e) {
                    e.preventDefault();
                    var product_id = $(this).data('product-id');
                    var $modal = $('#mh-quick-view-modal');
                    var $body = $modal.find('.mh-qv-body');
                    
                    $body.html('<div style="text-align:center; padding: 50px;"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
                    $modal.addClass('mh-open');
                    
                    $.post(mhAjaxUrl, { action: 'mh_quick_view_load', product_id: product_id }, function(response) {
                        if (response.success) {
                            $body.html(response.data);
                            // Initialize WooCommerce variation scripts for the newly loaded form
                            if (typeof $.fn.wc_variation_form !== 'undefined') {
                                $body.find('.variations_form').each(function() { $(this).wc_variation_form(); });
                            }
                        }
                    });
                });

                $(document).on('click', '.mh-qv-close, .mh-qv-overlay', function(e) {
                    if (e.target !== this) return;
                    $('#mh-quick-view-modal').removeClass('mh-open');
                });
            });
        </script>
        <?php
    }
}