<?php
/**
 * MH Combo Products Widget
 *
 * Displays the bundled sub-products of a "combo" product type in
 * three switchable layouts: Grid · List · Carousel (Slick).
 *
 * @package MH_Plug
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

class MH_Plug_Combo_Products_Widget extends \Elementor\Widget_Base {

    // ──────────────────────────────────────────────────────────────────────────
    // Identity
    // ──────────────────────────────────────────────────────────────────────────

    public function get_name()       { return 'mh_combo_products'; }
    public function get_title()      { return __( 'MH Combo Products', 'mh-plug-ecommerce-builder-widgets' ); }
    public function get_icon()       { return 'eicon-products'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    public function get_style_depends()  { return [ 'mh-widgets-css', 'mh-slick-css' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js', 'mh-slick-js' ]; }

    // ──────────────────────────────────────────────────────────────────────────
    // Controls
    // ──────────────────────────────────────────────────────────────────────────

    protected function register_controls() {

        /* ═══════════════════════════════════════════════════════════════════
           CONTENT TAB
           ═══════════════════════════════════════════════════════════════════ */

        /* ── Layout ── */
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Layout', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'combo_layout', [
            'label'   => __( 'Layout', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'default' => 'grid',
            'options' => [
                'grid'     => [ 'title' => __( 'Grid', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-apps' ],
                'list'     => [ 'title' => __( 'List', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-editor-list-ul' ],
                'carousel' => [ 'title' => __( 'Carousel', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-slider-push' ],
            ],
            'toggle' => false,
        ] );

        /* Columns – visible for grid & carousel */
        $this->add_responsive_control( 'columns', [
            'label'          => __( 'Columns', 'mh-plug-ecommerce-builder-widgets' ),
            'type'           => Controls_Manager::NUMBER,
            'default'        => 3,
            'tablet_default' => 2,
            'mobile_default' => 1,
            'min'            => 1,
            'max'            => 6,
            'condition'      => [ 'combo_layout' => [ 'grid', 'carousel' ] ],
            'selectors'      => [
                '{{WRAPPER}} .mh-combo-wrapper.mh-layout-grid' => '--mh-columns-desktop: {{VALUE}};',
            ],
        ] );

        /* Gap */
        $this->add_responsive_control( 'items_gap', [
            'label'      => __( 'Gap Between Items', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'rem' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 20 ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-wrapper.mh-layout-grid' => '--mh-gap: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-combo-wrapper.mh-layout-list' => 'gap: {{SIZE}}{{UNIT}} !important;',
            ],
        ] );

        /* Image size (Group_Control_Image_Size) */
        $this->add_group_control( Group_Control_Image_Size::get_type(), [
            'name'    => 'combo_image',
            'default' => 'woocommerce_thumbnail',
            'exclude' => [ 'custom' ],
        ] );

        /* Content toggles */
        $this->add_control( 'show_price', [
            'label'        => __( 'Show Price', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'link_items', [
            'label'        => __( 'Link to Product', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'empty_message', [
            'label'       => __( 'Empty State Message', 'mh-plug-ecommerce-builder-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'No products found in this combo.', 'mh-plug-ecommerce-builder-widgets' ),
        ] );

        $this->end_controls_section();

        /* ── Carousel Options (only when layout = carousel) ── */
        $this->start_controls_section( 'section_carousel', [
            'label'     => __( 'Carousel Options', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'combo_layout' => 'carousel' ],
        ] );

        $this->add_control( 'carousel_cols_tablet', [
            'label'   => __( 'Columns (Tablet)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 2,
            'min'     => 1,
            'max'     => 4,
        ] );

        $this->add_control( 'carousel_cols_mobile', [
            'label'   => __( 'Columns (Mobile)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 1,
            'min'     => 1,
            'max'     => 3,
        ] );

        $this->add_control( 'carousel_arrows', [
            'label'        => __( 'Show Arrows', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'carousel_dots', [
            'label'        => __( 'Show Dots', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'carousel_autoplay', [
            'label'        => __( 'Autoplay', 'mh-plug-ecommerce-builder-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => '',
        ] );

        $this->add_control( 'carousel_autoplay_speed', [
            'label'     => __( 'Autoplay Speed (ms)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 3000,
            'min'       => 500,
            'max'       => 10000,
            'condition' => [ 'carousel_autoplay' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ═══════════════════════════════════════════════════════════════════
           STYLE TAB
           ═══════════════════════════════════════════════════════════════════ */

        /* ── Card / Box ── */
        $this->start_controls_section( 'section_style_card', [
            'label' => __( 'Card / Box', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'card_background',
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .mh-combo-card',
        ] );

        $this->add_responsive_control( 'card_padding', [
            'label'      => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'card_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
            ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'card_border',
            'selector' => '{{WRAPPER}} .mh-combo-card',
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_box_shadow',
            'selector' => '{{WRAPPER}} .mh-combo-card',
        ] );

        $this->end_controls_section();

        /* ── Image ── */
        $this->start_controls_section( 'section_style_image', [
            'label' => __( 'Image', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'image_width', [
            'label'      => __( 'Width', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [
                'px' => [ 'min' => 50, 'max' => 800 ],
                '%'  => [ 'min' => 10, 'max' => 100 ],
            ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-image img' => 'width: {{SIZE}}{{UNIT}} !important;',
                '{{WRAPPER}} .mh-layout-list .mh-combo-image' => 'flex: 0 0 {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
            ],
        ] );

        $this->add_responsive_control( 'image_height', [
            'label'      => __( 'Height', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vh' ],
            'range'      => [ 'px' => [ 'min' => 50, 'max' => 600 ] ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-image img' => 'height: {{SIZE}}{{UNIT}} !important; object-fit: cover;',
            ],
        ] );

        $this->add_control( 'image_object_fit', [
            'label'     => __( 'Object Fit', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'cover',
            'options'   => [
                'cover'   => __( 'Cover',   'mh-plug-ecommerce-builder-widgets' ),
                'contain' => __( 'Contain', 'mh-plug-ecommerce-builder-widgets' ),
                'fill'    => __( 'Fill',    'mh-plug-ecommerce-builder-widgets' ),
                'none'    => __( 'None',    'mh-plug-ecommerce-builder-widgets' ),
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-combo-image img' => 'object-fit: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'image_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'image_spacing', [
            'label'      => __( 'Spacing (margin-bottom)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'unit' => 'px', 'size' => 0 ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-image'                        => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-layout-list .mh-combo-image'        => 'margin-bottom: 0; margin-right: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── Content (Title & Price) ── */
        $this->start_controls_section( 'section_style_content', [
            'label' => __( 'Content', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'content_align', [
            'label'     => __( 'Alignment', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => __( 'Left',   'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right',  'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-combo-info' => 'text-align: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'info_padding', [
            'label'      => __( 'Info Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => '12', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-combo-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        /* Title */
        $this->add_control( 'heading_title', [
            'label'     => __( 'Title', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'title_color', [
            'label'     => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .mh-combo-title'   => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-combo-title a' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'title_hover_color', [
            'label'     => __( 'Hover Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-combo-title a:hover' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .mh-combo-title',
        ] );

        $this->add_responsive_control( 'title_spacing', [
            'label'      => __( 'Spacing Below Title', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'unit' => 'px', 'size' => 6 ],
            'selectors'  => [ '{{WRAPPER}} .mh-combo-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ] );

        /* Price */
        $this->add_control( 'heading_price', [
            'label'     => __( 'Price', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [ 'show_price' => 'yes' ],
        ] );

        $this->add_control( 'price_color', [
            'label'     => __( 'Price Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'condition' => [ 'show_price' => 'yes' ],
            'selectors' => [
                '{{WRAPPER}} .mh-combo-price .amount'     => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-combo-price ins .amount' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'price_del_color', [
            'label'     => __( 'Old / Sale Price Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#aaaaaa',
            'condition' => [ 'show_price' => 'yes' ],
            'selectors' => [ '{{WRAPPER}} .mh-combo-price del .amount' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'price_typography',
            'selector'  => '{{WRAPPER}} .mh-combo-price',
            'condition' => [ 'show_price' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────────────────────

    protected function render() {
        global $product;

        /* 1. Resolve global product */
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
                $this->_render_placeholder(
                    '#cccccc',
                    __( 'MH Combo Products', 'mh-plug-ecommerce-builder-widgets' ),
                    __( 'Place this widget inside a Single Product template for a Combo product.', 'mh-plug-ecommerce-builder-widgets' )
                );
            }
            return;
        }

        /* 2. Guard: must be a combo */
        if ( ! $product->is_type( 'combo' ) ) {
            if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
                $this->_render_placeholder(
                    '#f0a500',
                    __( 'Not a Combo product', 'mh-plug-ecommerce-builder-widgets' ),
                    __( 'This widget only renders for "Combo" product types.', 'mh-plug-ecommerce-builder-widgets' )
                );
            }
            return;
        }

        $settings = $this->get_settings_for_display();
        $layout   = ! empty( $settings['combo_layout'] ) ? $settings['combo_layout'] : 'grid';

        /* 3. Fetch linked product IDs from _mh_combo_products meta */
        $ids = get_post_meta( $product->get_id(), '_mh_combo_products', true );

        if ( empty( $ids ) ) {
            $ids = [];
        } elseif ( ! is_array( $ids ) ) {
            $decoded = json_decode( $ids, true );
            $ids     = is_array( $decoded ) ? $decoded : array_map( 'trim', explode( ',', $ids ) );
        }

        $ids = array_filter( array_map( 'absint', $ids ) );

        /* 4. Empty state */
        if ( empty( $ids ) ) {
            $msg = ! empty( $settings['empty_message'] )
                ? $settings['empty_message']
                : __( 'No products found in this combo.', 'mh-plug-ecommerce-builder-widgets' );
            echo '<p class="mh-combo-empty">' . esc_html( $msg ) . '</p>';
            return;
        }

        /* 5. Build wrapper attributes */
        $image_size = ! empty( $settings['combo_image_size'] ) ? $settings['combo_image_size'] : 'woocommerce_thumbnail';
        $show_price = 'yes' === ( $settings['show_price'] ?? '' );
        $link_items = 'yes' === ( $settings['link_items'] ?? 'yes' );

        // Extract responsive layout settings
        $desktop_layout = !empty( $settings['combo_layout'] ) ? $settings['combo_layout'] : 'grid';
        $tablet_layout  = !empty( $settings['combo_layout_tablet'] ) ? $settings['combo_layout_tablet'] : $desktop_layout;
        $mobile_layout  = !empty( $settings['combo_layout_mobile'] ) ? $settings['combo_layout_mobile'] : $tablet_layout;

        $wrapper_class = 'mh-combo-wrapper mh-responsive-combo mh-layout-' . esc_attr( $desktop_layout );
        if ( 'carousel' === $desktop_layout ) {
            $wrapper_class .= ' mh-slick-carousel';
        }

        $cols_desktop = intval( $settings['columns'] ?? 3 );
        $cols_tablet  = intval( $settings['carousel_cols_tablet'] ?? 2 );
        $cols_mobile  = intval( $settings['carousel_cols_mobile'] ?? 1 );
        $autoplay     = 'yes' === ( $settings['carousel_autoplay'] ?? '' );
        $autoplay_spd = intval( $settings['carousel_autoplay_speed'] ?? 3000 );
        $arrows       = 'yes' === ( $settings['carousel_arrows'] ?? 'yes' );
        $dots         = 'yes' === ( $settings['carousel_dots'] ?? 'yes' );

        $wrapper_data = sprintf(
            ' data-layout-desktop="%s" data-layout-tablet="%s" data-layout-mobile="%s" data-columns="%d" data-columns-tablet="%d" data-columns-mobile="%d" data-autoplay="%s" data-autoplay-speed="%d" data-arrows="%s" data-dots="%s"',
            esc_attr($desktop_layout),
            esc_attr($tablet_layout),
            esc_attr($mobile_layout),
            $cols_desktop,
            $cols_tablet,
            $cols_mobile,
            $autoplay ? 'true' : 'false',
            $autoplay_spd,
            $arrows ? 'true' : 'false',
            $dots   ? 'true' : 'false'
        );

        echo '<div class="' . esc_attr( $wrapper_class ) . '"' . $wrapper_data . '>';

        /* 6. Loop products */
        foreach ( $ids as $pid ) {
            $linked = wc_get_product( $pid );
            if ( ! $linked instanceof WC_Product ) {
                continue;
            }

            $permalink = get_permalink( $pid );
            $name      = $linked->get_name();
            $img       = $linked->get_image( $image_size, [ 'class' => 'mh-combo-img' ] );

            echo '<div class="mh-combo-card">';

            /* Image */
            echo '<div class="mh-combo-image">';
            if ( $link_items ) {
                echo '<a href="' . esc_url( $permalink ) . '" aria-label="' . esc_attr( $name ) . '">' . $img . '</a>';
            } else {
                echo $img;
            }
            echo '</div>';

            /* Info */
            echo '<div class="mh-combo-info">';

            echo '<h4 class="mh-combo-title">';
            if ( $link_items ) {
                echo '<a href="' . esc_url( $permalink ) . '">' . esc_html( $name ) . '</a>';
            } else {
                echo esc_html( $name );
            }
            echo '</h4>';

            if ( $show_price ) {
                echo '<div class="mh-combo-price">' . $linked->get_price_html() . '</div>';
            }

            echo '</div>'; // .mh-combo-info
            echo '</div>'; // .mh-combo-card
        }

        echo '</div>'; // .mh-combo-wrapper
    }

    /**
     * Renders an editor-only placeholder box.
     */
    private function _render_placeholder( string $border_color, string $heading, string $message ): void {
        printf(
            '<div class="mh-combo-editor-placeholder" style="padding:24px;border:2px dashed %s;text-align:center;color:#888;font-family:sans-serif;">
                <span class="eicon-products" style="font-size:36px;display:block;margin-bottom:8px;"></span>
                <strong>%s</strong><br><span style="font-size:13px;">%s</span>
            </div>',
            esc_attr( $border_color ),
            esc_html( $heading ),
            esc_html( $message )
        );
    }
}
