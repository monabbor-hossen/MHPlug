<?php
/**
 * MH Wishlist Button Widget (100% Bulletproof SVG Version)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Wishlist_Button_Widget extends Widget_Base {

    public function get_name() { return 'mh_wishlist_button'; }
    public function get_title() { return __( 'MH Wishlist Button', 'mh-plug' ); }
    public function get_icon() { return 'eicon-heart'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {

        /* ── CONTENT: LOGIC & BEHAVIOR ── */
        $this->start_controls_section( 'content_logic', [
            'label' => __( 'Wishlist Behavior', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'behavior', [
            'label'   => __( 'Button Action', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'toggle',
            'options' => [
                'toggle' => __( 'Toggle (Add / Remove instantly)', 'mh-plug' ),
                'browse' => __( 'Browse (Go to Wishlist Page)', 'mh-plug' ),
            ],
        ] );

        $this->add_control( 'wishlist_url', [
            'label'       => __( 'Wishlist Page URL', 'mh-plug' ),
            'type'        => Controls_Manager::URL,
            'placeholder' => __( 'https://your-site.com/wishlist', 'mh-plug' ),
            'condition'   => [ 'behavior' => 'browse' ],
        ] );

        $this->add_control( 'show_label', [
            'label'        => __( 'Show Text Label', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'separator'    => 'before',
        ] );

        $this->add_control( 'add_text', [
            'label'     => __( '"Add" Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Add to wishlist', 'mh-plug' ),
            'condition' => [ 'show_label' => 'yes' ],
        ] );

        $this->add_control( 'remove_text', [
            'label'     => __( '"Remove" Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Remove from wishlist', 'mh-plug' ),
            'condition' => [ 'show_label' => 'yes', 'behavior' => 'toggle' ],
        ] );

        $this->add_control( 'browse_text', [
            'label'     => __( '"Browse" Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Browse Wishlist', 'mh-plug' ),
            'condition' => [ 'show_label' => 'yes', 'behavior' => 'browse' ],
        ] );

        $this->add_responsive_control( 'align', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-advanced-wishlist-wrap' => 'text-align: {{VALUE}};', ],
        ] );

        $this->end_controls_section();

        /* ── STYLING ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Icon & Text Styling', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'size' => 16 ],
            'selectors'  => [ 
                '{{WRAPPER}} .mh-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', 
            ],
        ] );

        $this->add_responsive_control( 'icon_spacing', [
            'label'      => __( 'Space Between Icon & Text', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 8 ],
            'selectors'  => [ '{{WRAPPER}} .mh-wishlist-label' => 'margin-left: {{SIZE}}{{UNIT}};', ],
            'condition'  => [ 'show_label' => 'yes' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'typography',
            'selector'  => '{{WRAPPER}} .mh-advanced-wishlist-btn', 
            'condition' => [ 'show_label' => 'yes' ],
            'separator' => 'before',
        ] );

        $this->start_controls_tabs( 'style_tabs' );

        $this->start_controls_tab( 'tab_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'color_normal', [
            'label'     => __( 'Text & Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ 
                '{{WRAPPER}} .mh-advanced-wishlist-btn' => 'color: {{VALUE}}; text-decoration: none;',
                '{{WRAPPER}} .mh-advanced-wishlist-btn svg' => 'fill: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_active', [ 'label' => __( 'Added', 'mh-plug' ) ] );
        $this->add_control( 'color_active', [
            'label'     => __( 'Text & Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ 
                '{{WRAPPER}} .mh-advanced-wishlist-btn.added' => 'color: {{VALUE}}; text-decoration: none;',
                '{{WRAPPER}} .mh-advanced-wishlist-btn.added svg' => 'fill: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 10px; color: #d63638;">Please assign this template to a Product to preview.</div>';
            }
            return;
        }

        $product_id  = $product->get_id();
        $in_wishlist = function_exists('mh_wishlist_has_product') ? mh_wishlist_has_product($product_id) : false;
        
        $behavior    = $settings['behavior'];
        $show_label  = $settings['show_label'] === 'yes';
        $add_text    = ! empty( $settings['add_text'] ) ? $settings['add_text'] : 'Add to wishlist';
        $remove_text = ! empty( $settings['remove_text'] ) ? $settings['remove_text'] : 'Remove from wishlist';
        $browse_text = ! empty( $settings['browse_text'] ) ? $settings['browse_text'] : 'Browse Wishlist';
        $browse_url  = ! empty( $settings['wishlist_url']['url'] ) ? $settings['wishlist_url']['url'] : '#';

        $current_text = $add_text;
        $current_href = '#';

        if ( $in_wishlist ) {
            if ( $behavior === 'browse' ) {
                $current_text = $browse_text;
                $current_href = $browse_url;
            } else {
                $current_text = $remove_text;
            }
        }

        $ajax_url = admin_url( 'admin-ajax.php' );
        $nonce    = wp_create_nonce( 'mh_wishlist_nonce' );

        // 🚀 BULLETPROOF SVGs (No External Fonts Required)
        $svg_empty = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 429.3l175.2-161.3c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/></svg>';
        $svg_filled = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>';

        ?>
        <style>
            .mh-advanced-wishlist-btn { display: inline-flex; align-items: center; cursor: pointer; transition: color 0.3s ease; }
            .mh-icon-wrap { display: inline-flex; align-items: center; justify-content: center; }
            .mh-icon-wrap svg { transition: transform 0.3s ease, fill 0.3s ease; }
            
            /* Hover Pop Animation */
            .mh-advanced-wishlist-btn:hover .mh-icon-wrap svg { transform: scale(1.15); }
            
            /* Logic for showing Empty vs Filled Heart */
            .mh-advanced-wishlist-btn .mh-icon-added { display: none; }
            .mh-advanced-wishlist-btn .mh-icon-normal { display: inline-flex; }
            
            .mh-advanced-wishlist-btn.added .mh-icon-normal { display: none !important; }
            .mh-advanced-wishlist-btn.added .mh-icon-added { display: inline-flex !important; }
        </style>

        <div class="mh-advanced-wishlist-wrap">
            <a href="<?php echo esc_url( $current_href ); ?>" 
               class="mh-advanced-wishlist-btn <?php echo $in_wishlist ? 'added' : ''; ?>" 
               data-product-id="<?php echo esc_attr( $product_id ); ?>" 
               data-behavior="<?php echo esc_attr( $behavior ); ?>"
               data-add-text="<?php echo esc_attr( $add_text ); ?>"
               data-remove-text="<?php echo esc_attr( $remove_text ); ?>"
               data-browse-text="<?php echo esc_attr( $browse_text ); ?>"
               data-wishlist-url="<?php echo esc_url( $browse_url ); ?>">
                
                <span class="mh-icon-wrap mh-icon-normal">
                    <?php echo $svg_empty; ?>
                </span>
                
                <span class="mh-icon-wrap mh-icon-added">
                    <?php echo $svg_filled; ?>
                </span>

                <?php if ( $show_label ) : ?>
                    <span class="mh-wishlist-label"><?php echo esc_html( $current_text ); ?></span>
                <?php endif; ?>
            </a>
        </div>

        <script>
            jQuery(document).ready(function($){
                var mhAjaxUrl = '<?php echo esc_url( $ajax_url ); ?>';
                var mhNonce   = '<?php echo esc_attr( $nonce ); ?>';

                $('.mh-advanced-wishlist-btn').off('click.mhSmartWishlist').on('click.mhSmartWishlist', function(e){
                    var $btn = $(this);
                    var behavior = $btn.data('behavior');
                    
                    if( behavior === 'browse' && $btn.hasClass('added') ) { return; }
                    
                    e.preventDefault(); 
                    
                    var pid = $btn.data('product-id');
                    $btn.css({'opacity': '0.5', 'pointer-events': 'none'});

                    $.post(mhAjaxUrl, {
                        action: 'mh_wishlist_toggle',
                        product_id: pid,
                        security: mhNonce
                    }, function(response) {
                        $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                        
                        if(response.success) {
                            var status = response.data.status;
                            var $label = $btn.find('.mh-wishlist-label');
                            
                            if(status === 'added') {
                                $btn.addClass('added');
                                
                                if(behavior === 'browse') {
                                    if($label.length) $label.text($btn.data('browse-text'));
                                    $btn.attr('href', $btn.data('wishlist-url'));
                                } else {
                                    if($label.length) $label.text($btn.data('remove-text'));
                                }
                            } else {
                                $btn.removeClass('added');
                                if($label.length) $label.text($btn.data('add-text'));
                                $btn.attr('href', '#');
                            }
                            
                            $(document).trigger('mh_wishlist_updated', [status]);

                        } else {
                            alert(response.data.message || 'Please log in to add to wishlist.');
                        }
                    });
                });
            });
        </script>
        <?php
    }
}