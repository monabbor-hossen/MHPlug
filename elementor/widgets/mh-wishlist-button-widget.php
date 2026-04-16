<?php
/**
 * MH Wishlist Button Widget (Smart Dual-Mode)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

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
            'description' => __( 'Choose what happens after a product is added to the wishlist.', 'mh-plug' ),
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
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
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
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-advanced-wishlist-wrap' => 'text-align: {{VALUE}};', ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ICONS & TEXT ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Button Styling', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}} .mh-advanced-wishlist-btn i' => 'font-size: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->add_control( 'icon_spacing', [
            'label'      => __( 'Space Between Icon & Text', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 8 ],
            'selectors'  => [ '{{WRAPPER}} .mh-wishlist-label' => 'margin-left: {{SIZE}}{{UNIT}};', ],
            'condition'  => [ 'show_label' => 'yes' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'typography',
            'selector'  => '{{WRAPPER}} .mh-wishlist-label',
            'condition' => [ 'show_label' => 'yes' ],
        ] );

        $this->start_controls_tabs( 'style_tabs' );

        // Normal State (Empty)
        $this->start_controls_tab( 'tab_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'color_normal', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-advanced-wishlist-btn' => 'color: {{VALUE}}; text-decoration: none;' ],
        ] );
        $this->end_controls_tab();

        // Active State (Added/Filled)
        $this->start_controls_tab( 'tab_active', [ 'label' => __( 'Added (Filled)', 'mh-plug' ) ] );
        $this->add_control( 'color_active', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638', // Standard red fill
            'selectors' => [ '{{WRAPPER}} .mh-advanced-wishlist-btn.added' => 'color: {{VALUE}}; text-decoration: none;' ],
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
                echo '<div style="padding: 10px; color: #d63638;">Please create a product to preview the wishlist button.</div>';
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

        // Determine current state
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

        ?>
        <style>
            .mh-advanced-wishlist-btn { display: inline-flex; align-items: center; cursor: pointer; transition: 0.3s ease; }
            .mh-advanced-wishlist-btn i { transition: 0.3s ease; }
            .mh-advanced-wishlist-btn.added .mh-icon-normal { display: none; }
            .mh-advanced-wishlist-btn.added .mh-icon-added { display: inline-block !important; }
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
                
                <i class="far fa-heart mh-icon-normal" style="display: <?php echo $in_wishlist ? 'none' : 'inline-block'; ?>;"></i>
                <i class="fas fa-heart mh-icon-added" style="display: <?php echo $in_wishlist ? 'inline-block' : 'none'; ?>;"></i>

                <?php if ( $show_label ) : ?>
                    <span class="mh-wishlist-label"><?php echo esc_html( $current_text ); ?></span>
                <?php endif; ?>
            </a>
        </div>

        <script>
            jQuery(document).ready(function($){
                $('.mh-advanced-wishlist-btn').off('click.mhSmartWishlist').on('click.mhSmartWishlist', function(e){
                    var $btn = $(this);
                    var behavior = $btn.data('behavior');
                    
                    // If behavior is Browse AND it's already added, act as a normal link to the Wishlist Page!
                    if( behavior === 'browse' && $btn.hasClass('added') ) {
                        return; 
                    }
                    
                    e.preventDefault(); // Stop normal click logic if we need to fire AJAX
                    
                    if(typeof mhWishlist === 'undefined') {
                        alert('Wishlist script is missing.');
                        return;
                    }

                    var pid = $btn.data('product-id');
                    $btn.css({'opacity': '0.5', 'pointer-events': 'none'});

                    $.post(mhWishlist.ajaxUrl, {
                        action: 'mh_wishlist_toggle',
                        product_id: pid,
                        security: mhWishlist.nonce
                    }, function(response) {
                        $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                        
                        if(response.success) {
                            var status = response.data.status;
                            var $label = $btn.find('.mh-wishlist-label');
                            
                            if(status === 'added') {
                                $btn.addClass('added');
                                
                                // Update text and link based on behavior
                                if(behavior === 'browse') {
                                    if($label.length) $label.text($btn.data('browse-text'));
                                    $btn.attr('href', $btn.data('wishlist-url'));
                                } else {
                                    if($label.length) $label.text($btn.data('remove-text'));
                                }
                            } else {
                                // Status is Removed (Toggle Mode)
                                $btn.removeClass('added');
                                if($label.length) $label.text($btn.data('add-text'));
                                $btn.attr('href', '#');
                            }
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