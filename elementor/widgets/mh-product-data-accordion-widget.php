<?php
/**
 * MH Product Data Accordion Widget
 *
 * Displays Description, Additional Info, and Shipping in a sleek Accordion layout.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class MH_Product_Data_Accordion_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_data_accordion'; }
    public function get_title() { return __( 'MH Product Accordion Data', 'mh-plug' ); }
    public function get_icon() { return 'eicon-accordion'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'accordion', 'toggle', 'description', 'shipping', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT: ACCORDION SETTINGS ── */
        $this->start_controls_section( 'section_accordion_content', [
            'label' => __( 'Accordion Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        // 1. Description
        $this->add_control( 'show_desc', [
            'label'   => __( 'Show Description', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ] );
        $this->add_control( 'title_desc', [
            'label'     => __( 'Description Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Description', 'mh-plug' ),
            'condition' => [ 'show_desc' => 'yes' ],
        ] );

        // 2. Additional Info
        $this->add_control( 'show_info', [
            'label'     => __( 'Show Additional Info', 'mh-plug' ),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
            'separator' => 'before',
        ] );
        $this->add_control( 'title_info', [
            'label'     => __( 'Additional Info Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Additional information', 'mh-plug' ),
            'condition' => [ 'show_info' => 'yes' ],
        ] );

        // 3. Shipping
        $this->add_control( 'show_shipping', [
            'label'     => __( 'Show Shipping & Delivery', 'mh-plug' ),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => 'yes',
            'separator' => 'before',
        ] );
        $this->add_control( 'title_shipping', [
            'label'     => __( 'Shipping Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Shipping & Delivery', 'mh-plug' ),
            'condition' => [ 'show_shipping' => 'yes' ],
        ] );
        $this->add_control( 'content_shipping', [
            'label'     => __( 'Shipping Content', 'mh-plug' ),
            'type'      => Controls_Manager::WYSIWYG,
            'default'   => __( 'Enter your shipping and delivery policy here.', 'mh-plug' ),
            'condition' => [ 'show_shipping' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ACCORDION HEADERS ── */
        $this->start_controls_section( 'style_accordion_header', [
            'label' => __( 'Accordion Headers', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'header_typography',
            'selector' => '{{WRAPPER}} .mh-accordion-header',
        ] );

        $this->start_controls_tabs( 'header_style_tabs' );

        $this->start_controls_tab( 'header_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'header_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-header' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#999999',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-icon' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'header_active', [ 'label' => __( 'Active', 'mh-plug' ) ] );
        $this->add_control( 'header_color_active', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-item.active .mh-accordion-header' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'icon_color_active', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-item.active .mh-accordion-icon' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'header_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 15, 'right' => 0, 'bottom' => 15, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-accordion-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
            'separator'  => 'before',
        ] );

        $this->add_control( 'border_color', [
            'label'     => __( 'Separator Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#eeeeee',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-item' => 'border-bottom: 1px solid {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ACCORDION CONTENT ── */
        $this->start_controls_section( 'style_accordion_content', [
            'label' => __( 'Accordion Content', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'content_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#555555',
            'selectors' => [ '{{WRAPPER}} .mh-accordion-content' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'content_typography',
            'selector' => '{{WRAPPER}} .mh-accordion-content',
        ] );

        $this->add_control( 'content_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 15, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

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
                echo '<div style="padding: 15px; border: 1px dashed #d63638; text-align: center;">Please create a product to preview the Accordion.</div>';
            }
            return;
        }

        $widget_id = $this->get_id();

        // 1. Gather Description
        $has_desc = false;
        $desc_content = '';
        if ( $settings['show_desc'] === 'yes' ) {
            $post = get_post( $product->get_id() );
            if ( $post && ! empty( $post->post_content ) ) {
                $has_desc = true;
                $desc_content = apply_filters( 'the_content', $post->post_content );
            }
        }

        // 2. Gather Additional Info
        $has_info = false;
        $info_content = '';
        if ( $settings['show_info'] === 'yes' && ( $product->has_attributes() || $product->has_dimensions() || $product->has_weight() ) ) {
            $has_info = true;
            ob_start();
            do_action( 'woocommerce_product_additional_information', $product );
            $info_content = ob_get_clean();
        }

        // 3. Gather Shipping
        $has_shipping = false;
        $shipping_content = '';
        if ( $settings['show_shipping'] === 'yes' && ! empty( $settings['content_shipping'] ) ) {
            $has_shipping = true;
            $shipping_content = $settings['content_shipping'];
        }

        // Output Nothing if Empty
        if ( ! $has_desc && ! $has_info && ! $has_shipping ) return;

        // Make the very first available item active by default
        $is_first = true; 

        ?>
        <style>
            .mh-accordion-container { width: 100%; border-top: 1px solid <?php echo esc_attr($settings['border_color']); ?>; }
            .mh-accordion-item { border-bottom: 1px solid <?php echo esc_attr($settings['border_color']); ?>; }
            .mh-accordion-header { display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 500; transition: color 0.3s ease; }
            .mh-accordion-icon { font-size: 14px; transition: 0.3s ease; display: inline-flex; align-items: center; justify-content: center; }
            
            /* Plus/Minus Icon Logic */
            .mh-accordion-item.active .fa-plus { display: none; }
            .mh-accordion-item:not(.active) .fa-minus { display: none; }
            
            /* Hide Default Woo Headings in Additional Info */
            .mh-accordion-content h2 { display: none; }
            .mh-accordion-content .woocommerce-product-attributes { width: 100%; border-collapse: collapse; margin: 0; }
            .mh-accordion-content .woocommerce-product-attributes th,
            .mh-accordion-content .woocommerce-product-attributes td { padding: 8px 0; border-bottom: 1px dotted #eaeaea; text-align: left; }
            .mh-accordion-content .woocommerce-product-attributes th { width: 40%; font-weight: 600; color: #111; }
            
            .mh-accordion-content p:last-child { margin-bottom: 0; }
        </style>

        <div class="mh-accordion-container mh-accordion-<?php echo esc_attr( $widget_id ); ?>">
            
            <?php if ( $has_desc ) : ?>
                <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                    <div class="mh-accordion-header">
                        <span class="mh-accordion-title"><?php echo esc_html( $settings['title_desc'] ); ?></span>
                        <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                    </div>
                    <div class="mh-accordion-content" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                        <?php echo wp_kses_post( $desc_content ); ?>
                    </div>
                </div>
                <?php $is_first = false; ?>
            <?php endif; ?>

            <?php if ( $has_info ) : ?>
                <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                    <div class="mh-accordion-header">
                        <span class="mh-accordion-title"><?php echo esc_html( $settings['title_info'] ); ?></span>
                        <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                    </div>
                    <div class="mh-accordion-content" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                        <?php echo $info_content; ?>
                    </div>
                </div>
                <?php $is_first = false; ?>
            <?php endif; ?>

            <?php if ( $has_shipping ) : ?>
                <div class="mh-accordion-item <?php echo $is_first ? 'active' : ''; ?>">
                    <div class="mh-accordion-header">
                        <span class="mh-accordion-title"><?php echo esc_html( $settings['title_shipping'] ); ?></span>
                        <span class="mh-accordion-icon"><i class="fas fa-plus"></i><i class="fas fa-minus"></i></span>
                    </div>
                    <div class="mh-accordion-content" style="display: <?php echo $is_first ? 'block' : 'none'; ?>;">
                        <?php echo wp_kses_post( $shipping_content ); ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <script>
            jQuery(document).ready(function($) {
                $('.mh-accordion-<?php echo esc_attr( $widget_id ); ?> .mh-accordion-header').on('click', function() {
                    var $this = $(this);
                    var $item = $this.parent('.mh-accordion-item');
                    var $content = $this.next('.mh-accordion-content');
                    var $container = $this.closest('.mh-accordion-container');

                    // Standard accordion behavior: click to toggle this one, close others
                    if ($item.hasClass('active')) {
                        $item.removeClass('active');
                        $content.slideUp(300);
                    } else {
                        // Close siblings
                        $container.find('.mh-accordion-item').removeClass('active');
                        $container.find('.mh-accordion-content').slideUp(300);
                        
                        // Open this one
                        $item.addClass('active');
                        $content.slideDown(300);
                    }
                });
            });
        </script>
        <?php
    }
}