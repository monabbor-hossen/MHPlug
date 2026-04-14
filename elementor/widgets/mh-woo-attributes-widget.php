<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * MH Plug - WooCommerce Product Attributes Widget
 */
class MH_Woo_Attributes_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'mh_woo_attributes';
    }

    public function get_title() {
        return esc_html__( 'Product Attributes', 'mh-plug' );
    }

    public function get_icon() {
        return 'eicon-product-meta';
    }

    public function get_categories() {
        return [ 'mh-plug-widgets' ];
    }

    public function get_keywords() {
        return [ 'woocommerce', 'product', 'attributes', 'dropdown', 'select', 'mh' ];
    }

    protected function register_controls() {
        /**
         * Label Styles
         */
        $this->start_controls_section(
            'section_style_label',
            [
                'label' => esc_html__( 'Label Style', 'mh-plug' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label'     => esc_html__( 'Text Color', 'mh-plug' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#004265',
                'selectors' => [
                    '{{WRAPPER}} .mh-woo-attribute-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'selector' => '{{WRAPPER}} .mh-woo-attribute-label',
            ]
        );

        $this->add_responsive_control(
            'label_spacing',
            [
                'label'      => esc_html__( 'Gap to Dropdown', 'mh-plug' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 50 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 10 ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-woo-attribute-label' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: block;',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Dropdown (<select>) Styles
         */
        $this->start_controls_section(
            'section_style_dropdown',
            [
                'label' => esc_html__( 'Dropdown Style', 'mh-plug' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'dropdown_color',
            [
                'label'     => esc_html__( 'Text Color', 'mh-plug' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#4a5568',
                'selectors' => [
                    '{{WRAPPER}} .mh-woo-attribute-select' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dropdown_bg',
            [
                'label'     => esc_html__( 'Background Color', 'mh-plug' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#e8edf2', // Neumorphic base
                'selectors' => [
                    '{{WRAPPER}} .mh-woo-attribute-select' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'dropdown_typography',
                'selector' => '{{WRAPPER}} .mh-woo-attribute-select',
            ]
        );

        $this->add_responsive_control(
            'dropdown_padding',
            [
                'label'      => esc_html__( 'Padding', 'mh-plug' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'      => 12,
                    'right'    => 16,
                    'bottom'   => 12,
                    'left'     => 16,
                    'isLinked' => false,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-woo-attribute-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'dropdown_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'mh-plug' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default'    => [ 'unit' => 'px', 'size' => 12 ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-woo-attribute-select' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'dropdown_width',
            [
                'label'      => esc_html__( 'Width', 'mh-plug' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default'    => [ 'unit' => '%', 'size' => 100 ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-woo-attribute-select' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'dropdown_spacing',
            [
                'label'      => esc_html__( 'Spacing Between Attributes', 'mh-plug' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'default'    => [ 'unit' => 'px', 'size' => 20 ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-woo-attribute-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mh-woo-attribute-wrapper:last-child' => 'margin-bottom: 0;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        global $product;
        
        // Ensure we have a valid product object, falling back to ID if necessary
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }

        if ( ! is_a( $product, 'WC_Product' ) ) {
            return;
        }

        $attributes = $product->get_attributes();
        if ( empty( $attributes ) ) {
            return;
        }

        echo '<div class="mh-woo-attributes-container" style="display:flex; flex-direction:column;">';

        // Neumorphic default styles that can be partially overridden by controls
        // box-shadow matches the MH-Plug Neumorphic style palette
        $default_select_css = 'border: none; outline: none; appearance: none; box-shadow: inset 3px 3px 7px #bec8d4, inset -3px -3px 7px #ffffff; cursor: pointer;';

        foreach ( $attributes as $attribute ) {
            $label = wc_attribute_label( $attribute->get_name() );

            echo '<div class="mh-woo-attribute-wrapper">';
            echo '<label class="mh-woo-attribute-label" style="font-weight:600;">' . esc_html( $label ) . '</label>';
            echo '<select class="mh-woo-attribute-select" style="' . esc_attr( $default_select_css ) . '">';
            echo '<option value="">' . esc_html( sprintf( __( 'Choose %s', 'mh-plug' ), $label ) ) . '</option>';

            if ( $attribute->is_taxonomy() ) {
                $terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'all' ] );
                if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                    foreach ( $terms as $term ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                    }
                }
            } else {
                $options = $attribute->get_options();
                if ( ! empty( $options ) ) {
                    foreach ( $options as $option ) {
                        $clean_option = trim( $option );
                        if ( ! empty( $clean_option ) ) {
                            echo '<option value="' . esc_attr( $clean_option ) . '">' . esc_html( $clean_option ) . '</option>';
                        }
                    }
                }
            }

            echo '</select>';
            echo '</div>';
        }

        echo '</div>';
    }
}
