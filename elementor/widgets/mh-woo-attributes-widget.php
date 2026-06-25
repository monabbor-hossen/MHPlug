<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * MH Plug - WooCommerce Product Attributes Widget (With JS Bridge Fix)
 */
class MH_Plug_Woo_Attributes_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_woo_attributes'; }
    public function get_title() { return esc_html__('Product Attributes', 'mh-plug-ecommerce-builder-widgets'); }
    public function get_icon() { return 'eicon-product-meta'; }
    public function get_categories() { return ['mh-plug-widgets']; }
    public function get_keywords() { return ['woocommerce', 'product', 'attributes', 'dropdown', 'select', 'mh']; }

    protected function register_controls()
    {
        /**
         * Layout Options
         */
        $this->start_controls_section( 'section_layout', [ 'label' => esc_html__('Layout', 'mh-plug-ecommerce-builder-widgets'), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT, ] );
        $this->add_control( 'attribute_layout', [
            'label' => esc_html__('Default Layout Style', 'mh-plug-ecommerce-builder-widgets'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'dropdown',
            'options' => [
                'dropdown' => esc_html__('Dropdown / Select', 'mh-plug-ecommerce-builder-widgets'),
                'dropdown_search' => esc_html__('Searchable Dropdown (Select2)', 'mh-plug-ecommerce-builder-widgets'),
                'pills'    => esc_html__('Tags / Pills', 'mh-plug-ecommerce-builder-widgets'),
                'radio'    => esc_html__('Radio Buttons (Vertical)', 'mh-plug-ecommerce-builder-widgets'),
                'grid2'    => esc_html__('Radio Buttons (Grid)', 'mh-plug-ecommerce-builder-widgets'),
            ],
        ] );

        $attribute_options = [];
        $attribute_options[''] = esc_html__( 'Select an Attribute...', 'mh-plug-ecommerce-builder-widgets' );
        if ( function_exists('wc_get_attribute_taxonomies') ) {
            $taxonomies = wc_get_attribute_taxonomies();
            if ( ! empty( $taxonomies ) ) {
                foreach ( $taxonomies as $tax ) {
                    $attribute_options[ $tax->attribute_name ] = $tax->attribute_label . ' (' . $tax->attribute_name . ')';
                }
            }
        }

        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control( 'attribute_type', [
            'label' => esc_html__('Attribute Type', 'mh-plug-ecommerce-builder-widgets'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'global' => [ 'title' => 'Global', 'icon' => 'eicon-globe' ],
                'custom' => [ 'title' => 'Custom', 'icon' => 'eicon-edit' ],
            ],
            'default' => 'global',
            'toggle' => false,
        ]);

        $repeater->add_control( 'attribute_name', [
            'label' => esc_html__( 'Select Global Attribute', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => $attribute_options,
            'label_block' => true,
            'condition' => [ 'attribute_type' => 'global' ],
        ] );

        $repeater->add_control( 'custom_attribute_name', [
            'label' => esc_html__( 'Custom Attribute Slug', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'description' => esc_html__( 'For attributes created directly on the product (e.g. Size).', 'mh-plug-ecommerce-builder-widgets' ),
            'condition' => [ 'attribute_type' => 'custom' ],
        ] );
        $repeater->add_control( 'layout', [
            'label' => esc_html__( 'Layout Style', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'pills',
            'options' => [
                'dropdown' => esc_html__('Dropdown / Select', 'mh-plug-ecommerce-builder-widgets'),
                'dropdown_search' => esc_html__('Searchable Dropdown (Select2)', 'mh-plug-ecommerce-builder-widgets'),
                'pills'    => esc_html__('Tags / Pills', 'mh-plug-ecommerce-builder-widgets'),
                'radio'    => esc_html__('Radio Buttons (Vertical)', 'mh-plug-ecommerce-builder-widgets'),
                'grid2'    => esc_html__('Radio Buttons (Grid)', 'mh-plug-ecommerce-builder-widgets'),
            ],
        ] );
        $this->add_control( 'custom_layouts', [
            'label' => esc_html__( 'Override by Attribute', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ attribute_type === "global" ? attribute_name : custom_attribute_name }}} - {{{ layout }}}',
        ] );
        $this->end_controls_section();

        /**
         * Label Styles
         */
        $this->start_controls_section( 'section_style_label', [ 'label' => esc_html__('Label Style', 'mh-plug-ecommerce-builder-widgets'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE, ] );
        $this->add_control( 'label_color', [ 'label' => esc_html__('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} .mh-woo-attribute-label' => 'color: {{VALUE}};', ], ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'label_typography', 'selector' => '{{WRAPPER}} .mh-woo-attribute-label', ] );
        $this->add_responsive_control( 'label_spacing', [ 'label' => esc_html__('Gap to Dropdown', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', 'em'], 'range' => [ 'px' => ['min' => 0, 'max' => 50], ], 'default' => ['unit' => 'px', 'size' => 10], 'selectors' => [ '{{WRAPPER}} .mh-woo-attribute-label' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: block;', ], ] );
        $this->end_controls_section();

        /**
         * General Layout (Applies to all layouts)
         */
        $this->start_controls_section( 'section_style_general', [ 'label' => esc_html__('General Layout', 'mh-plug-ecommerce-builder-widgets'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE, ] );
        $this->add_responsive_control( 'attribute_group_spacing', [ 'label' => esc_html__('Spacing Between Attributes', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', 'em'], 'default' => ['unit' => 'px', 'size' => 20], 'selectors' => [ '{{WRAPPER}} .mh-woo-attribute-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-woo-attribute-wrapper:last-child' => 'margin-bottom: 0;', ], ] );
        $this->end_controls_section();

        /**
         * Dropdown (<select>) Styles
         */
        $this->start_controls_section( 'section_style_dropdown', [ 
            'label' => esc_html__('Dropdown Style', 'mh-plug-ecommerce-builder-widgets'), 
            'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
        ] );
        $this->add_control( 'dropdown_color', [ 'label' => esc_html__('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#4a5568', 'selectors' => [ 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select' => 'color: {{VALUE}} !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}} !important;', ], ] );
        $this->add_control( 'dropdown_bg', [ 'label' => esc_html__('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#e8edf2', 'selectors' => [ 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select' => 'background-color: {{VALUE}} !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single' => 'background-color: {{VALUE}} !important;', ], ] );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'dropdown_typography', 'selector' => 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select, body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single .select2-selection__rendered', ] );
        
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'dropdown_border', 'selector' => 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select, body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single', ] );
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'dropdown_box_shadow', 'selector' => 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select, body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single', ] );

        $this->add_responsive_control( 'dropdown_padding', [ 'label' => esc_html__('Padding', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'default' => [ 'top' => 12, 'right' => 16, 'bottom' => 12, 'left' => 16, 'isLinked' => false, ], 'selectors' => [ 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select' => 'padding-top: {{TOP}}{{UNIT}} !important; padding-right: {{RIGHT}}{{UNIT}} !important; padding-bottom: {{BOTTOM}}{{UNIT}} !important; padding-left: {{LEFT}}{{UNIT}} !important; height: auto !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single' => 'height: auto !important; padding-top: {{TOP}}{{UNIT}} !important; padding-right: {{RIGHT}}{{UNIT}} !important; padding-bottom: {{BOTTOM}}{{UNIT}} !important; padding-left: {{LEFT}}{{UNIT}} !important; display: flex !important; align-items: center !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single .select2-selection__rendered' => 'padding: 0 !important; line-height: normal !important; flex: 1 !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single .select2-selection__arrow' => 'position: relative !important; top: auto !important; right: auto !important; height: auto !important;', ], ] );
        $this->add_responsive_control( 'dropdown_radius', [ 'label' => esc_html__('Border Radius', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'default' => ['unit' => 'px', 'size' => 12], 'selectors' => [ 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select' => 'border-radius: {{SIZE}}{{UNIT}} !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container--default .select2-selection--single' => 'border-radius: {{SIZE}}{{UNIT}} !important;', ], ] );
        $this->add_responsive_control( 'dropdown_width', [ 'label' => esc_html__('Width', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'default' => ['unit' => '%', 'size' => 100], 'selectors' => [ 'body {{WRAPPER}} .mh-woo-attributes-container select.mh-woo-attribute-select' => 'width: {{SIZE}}{{UNIT}} !important;', 'body {{WRAPPER}} .mh-woo-attributes-container .select2-container' => 'width: {{SIZE}}{{UNIT}} !important;', ], ] );
        $this->add_control( 'dropdown_arrow_color', [ 'label' => esc_html__('Arrow Color (Select2)', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow b' => 'border-color: {{VALUE}} transparent transparent transparent !important;', '{{WRAPPER}} .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b' => 'border-color: transparent transparent {{VALUE}} transparent !important;', '{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow' => 'color: {{VALUE}} !important;', '{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__arrow::after' => 'color: {{VALUE}} !important;', ], ] );
        $this->end_controls_section();

        /**
         * Select2 Menu Styles
         */
        $this->start_controls_section( 'section_style_select2_menu', [ 'label' => esc_html__('Select2 Menu Style', 'mh-plug-ecommerce-builder-widgets'), 'tab' => \Elementor\Controls_Manager::TAB_STYLE, ] );
        $this->add_control( 's2_menu_bg', [ 'label' => esc_html__('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .select2-dropdown' => 'background-color: {{VALUE}};', ], ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 's2_menu_border', 'selector' => '{{WRAPPER}} .select2-dropdown', ] );
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 's2_menu_box_shadow', 'selector' => '{{WRAPPER}} .select2-dropdown', ] );
        $this->add_control( 's2_option_color', [ 'label' => esc_html__('Option Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .select2-results__option' => 'color: {{VALUE}};', ], ] );
        $this->add_control( 's2_option_bg_hover', [ 'label' => esc_html__('Option Hover BG', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .select2-results__option--highlighted' => 'background-color: {{VALUE}};', ], ] );
        $this->end_controls_section();

        /**
         * Pills & Radio Styles
         */
        $this->start_controls_section( 'section_style_pills', [ 
            'label' => esc_html__('Pills & Radio Style', 'mh-plug-ecommerce-builder-widgets'), 
            'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
        ] );
        $this->start_controls_tabs('tabs_pills_style');
        
        $this->start_controls_tab('tab_pills_normal', ['label' => __('Normal', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control( 'pills_color', [ 'label' => esc_html__('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .mh-attr-pill, {{WRAPPER}} .mh-attr-radio-label' => 'color: {{VALUE}};', ], ] );
        $this->add_control( 'pills_bg', [ 'label' => esc_html__('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#f2f2f2', 'selectors' => [ '{{WRAPPER}} .mh-attr-pill' => 'background-color: {{VALUE}};', ] ] );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'pills_border', 'selector' => '{{WRAPPER}} .mh-attr-pill' ] );
        $this->end_controls_tab();

        $this->start_controls_tab('tab_pills_hover', ['label' => __('Hover', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control( 'pills_color_hover', [ 'label' => esc_html__('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-attr-pill:not(.mh-active):hover, {{WRAPPER}} .mh-attr-radio-item:hover .mh-attr-radio-label' => 'color: {{VALUE}};', ], ] );
        $this->add_control( 'pills_bg_hover', [ 'label' => esc_html__('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-attr-pill:not(.mh-active):hover' => 'background-color: {{VALUE}};', ] ] );
        $this->add_control( 'pills_border_hover_color', [ 'label' => esc_html__('Border Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-attr-pill:not(.mh-active):hover' => 'border-color: {{VALUE}};', ] ] );
        $this->end_controls_tab();

        $this->start_controls_tab('tab_pills_active', ['label' => __('Active', 'mh-plug-ecommerce-builder-widgets')]);
        $this->add_control( 'pills_color_active', [ 'label' => esc_html__('Text Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-attr-pill.mh-active, {{WRAPPER}} input:checked + .mh-attr-radio-mark + .mh-attr-radio-label' => 'color: {{VALUE}};', ], ] );
        $this->add_control( 'pills_bg_active', [ 'label' => esc_html__('Background Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} .mh-attr-pill.mh-active' => 'background-color: {{VALUE}};', ] ] );
        $this->add_control( 'pills_border_active_color', [ 'label' => esc_html__('Border Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} .mh-attr-pill.mh-active' => 'border-color: {{VALUE}};', ] ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'radio_color_active', [ 'label' => esc_html__('Radio Mark Color', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} input:checked + .mh-attr-radio-mark' => 'border-color: {{VALUE}}; background-color: {{VALUE}};', ], 'separator' => 'before' ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'pills_typography', 'selector' => '{{WRAPPER}} .mh-attr-pill, {{WRAPPER}} .mh-attr-radio-label', 'separator' => 'before' ] );
        $this->add_responsive_control( 'pills_padding', [ 'label' => esc_html__('Padding (Pills)', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'default' => [ 'top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16, 'isLinked' => false, ], 'selectors' => [ '{{WRAPPER}} .mh-attr-pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ] ] );
        $this->add_responsive_control( 'pills_radius', [ 'label' => esc_html__('Border Radius (Pills)', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'default' => ['unit' => 'px', 'size' => 6], 'selectors' => [ '{{WRAPPER}} .mh-attr-pill' => 'border-radius: {{SIZE}}{{UNIT}};', ] ] );
        $this->add_responsive_control( 'pills_gap', [ 'label' => esc_html__('Row Gap', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['unit' => 'px', 'size' => 10], 'selectors' => [ '{{WRAPPER}} .mh-attr-pills-container' => 'gap: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-attr-radio-container' => 'gap: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-attr-grid-container' => 'row-gap: {{SIZE}}{{UNIT}};', ], ] );
        $this->add_responsive_control( 'grid_column_gap', [ 'label' => esc_html__('Column Gap', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['unit' => 'px', 'size' => 20], 'selectors' => [ '{{WRAPPER}} .mh-attr-grid-container' => 'column-gap: {{SIZE}}{{UNIT}};', ] ] );
        $this->add_responsive_control( 'grid_columns', [ 'label' => esc_html__('Grid Columns', 'mh-plug-ecommerce-builder-widgets'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => [''], 'default' => ['size' => 2], 'range' => [ '' => ['min' => 1, 'max' => 6] ], 'selectors' => [ '{{WRAPPER}} .mh-attr-grid-container' => 'grid-template-columns: repeat({{SIZE}}, 1fr);', ] ] );
        $this->end_controls_section();
    }

    protected function render()
    {
        if (!class_exists('WooCommerce')) {
            return;
        }

        global $product;

        if (!is_a($product, 'WC_Product')) {
            $product = wc_get_product(get_the_ID());
        }

        if (!is_a($product, 'WC_Product')) {
            return;
        }

        // Ensure SelectWoo is available for searchable dropdowns
        if ( function_exists( 'wp_enqueue_script' ) ) {
            wp_enqueue_script( 'selectWoo' );
            wp_enqueue_style( 'select2' );
        }

        $attributes = $product->get_attributes();
        if (empty($attributes)) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $default_layout = !empty($settings['attribute_layout']) ? $settings['attribute_layout'] : 'dropdown';

        $custom_layouts = [];
        if (!empty($settings['custom_layouts'])) {
            foreach ($settings['custom_layouts'] as $item) {
                $is_custom = (!empty($item['attribute_type']) && $item['attribute_type'] === 'custom');
                $attr_slug = $is_custom ? (isset($item['custom_attribute_name']) ? $item['custom_attribute_name'] : '') : (isset($item['attribute_name']) ? $item['attribute_name'] : '');
                
                if (!empty($attr_slug) && !empty($item['layout'])) {
                    $custom_layouts[ strtolower(trim($attr_slug)) ] = $item['layout'];
                }
            }
        }

        echo '<div class="mh-woo-attributes-container" style="display:flex; flex-direction:column;">';

        // Provide a default select styling but allow Elementor settings to override it easily
        $default_select_css = 'outline: none; min-height: 45px; cursor: pointer;';

        foreach ($attributes as $attribute) {
            
            $attribute_name = $attribute->get_name();
            $label          = wc_attribute_label($attribute_name);
            $select_id      = sanitize_title($attribute_name);
            $select_name    = 'attribute_' . $select_id;
            $is_variation   = $attribute->get_variation() ? 'true' : 'false';

            $layout = $default_layout;
            $raw_name = strtolower($attribute_name);
            $clean_name = str_replace('pa_', '', $raw_name);

            if (isset($custom_layouts[$raw_name])) {
                $layout = $custom_layouts[$raw_name];
            } elseif (isset($custom_layouts[$clean_name])) {
                $layout = $custom_layouts[$clean_name];
            }

            $options_data = [];
            if ($attribute->is_taxonomy()) {
                $terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'all']);
                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $options_data[] = [
                            'value' => $term->slug,
                            'label' => $term->name
                        ];
                    }
                }
            } else {
                $options = $attribute->get_options();
                if (!empty($options)) {
                    foreach ($options as $option) {
                        $clean_option = trim($option);
                        if (!empty($clean_option)) {
                            $options_data[] = [
                                'value' => $clean_option,
                                'label' => $clean_option
                            ];
                        }
                    }
                }
            }

            echo '<div class="mh-woo-attribute-wrapper" style="position:relative;">';
            echo '<label class="mh-woo-attribute-label" for="' . esc_attr($select_id) . '" style="font-weight:600;">' . esc_html($label) . '</label>';
            
            // ALWAYS render the select for JS Bridge & Cart form, but hide if not dropdown
            $is_dropdown = ( $layout === 'dropdown' || $layout === 'dropdown_search' );
            $select_style = $is_dropdown ? $default_select_css : 'display:none;';
            $extra_class = ( $layout === 'dropdown_search' ) ? ' mh-select2-enabled' : '';
            
            echo '<select id="' . esc_attr($select_id) . '" name="' . esc_attr($select_name) . '" class="mh-woo-attribute-select' . $extra_class . '" style="' . esc_attr($select_style) . '" data-attribute_name="' . esc_attr($select_name) . '" data-is-variation="' . esc_attr($is_variation) . '">';
            echo '<option value="">' . esc_html(sprintf(__('Choose %s', 'mh-plug-ecommerce-builder-widgets'), $label)) . '</option>';
            foreach ($options_data as $opt) {
                echo '<option value="' . esc_attr($opt['value']) . '">' . esc_html($opt['label']) . '</option>';
            }
            echo '</select>';

            // Render Alternate Layouts
            if ($layout === 'pills') {
                echo '<div class="mh-attr-pills-container" style="display:flex; flex-wrap:wrap;">';
                foreach ($options_data as $opt) {
                    echo '<div class="mh-attr-pill" data-target="#' . esc_attr($select_id) . '" data-value="' . esc_attr($opt['value']) . '" style="cursor:pointer; border: 1px solid transparent; transition: 0.2s;">' . esc_html($opt['label']) . '</div>';
                }
                echo '</div>';
            } elseif ($layout === 'radio' || $layout === 'grid2') {
                $container_class = ($layout === 'grid2') ? 'mh-attr-grid-container' : 'mh-attr-radio-container';
                $container_style = ($layout === 'grid2') ? 'display:grid;' : 'display:flex; flex-direction:column;';
                echo '<div class="' . esc_attr($container_class) . '" style="' . esc_attr($container_style) . '">';
                foreach ($options_data as $opt) {
                    echo '<label class="mh-attr-radio-item" style="display:flex; align-items:center; cursor:pointer;">';
                    echo '<input type="radio" name="mh_radio_' . esc_attr($select_id) . '" value="' . esc_attr($opt['value']) . '" data-target="#' . esc_attr($select_id) . '" style="display:none;">';
                    echo '<span class="mh-attr-radio-mark" style="width:16px; height:16px; border:1px solid #ccc; border-radius:50%; margin-right:8px; display:inline-block; transition: 0.2s;"></span>';
                    echo '<span class="mh-attr-radio-label">' . esc_html($opt['label']) . '</span>';
                    echo '</label>';
                }
                echo '</div>';
            }

            echo '</div>';
        }

        echo '</div>';


        // ── Custom Variation Price Bridge ────────────────────────────────────
        // Inject the saved pricing rules into the page so the price update
        // logic can match selected attribute values to custom prices.
        $rules = get_post_meta( $product->get_id(), '_mh_custom_variation_rules', true );
        if ( ! empty( $rules ) && is_array( $rules ) ) {
            $js_rules = 'var mhVariationRules = ' . wp_json_encode( array_values( $rules ) ) . ';';
            wp_add_inline_script( 'jquery-core', $js_rules, 'before' );
        }

        $css = "
            .mh-woo-attribute-wrapper > .select2-container[style*=\"absolute\"] {
                top: 100% !important;
                left: 0 !important;
                width: 100% !important;
                margin-top: 4px;
            }
        ";
        wp_register_style( 'mh-woo-attributes-style', false );
        wp_enqueue_style( 'mh-woo-attributes-style' );
        wp_add_inline_style( 'mh-woo-attributes-style', $css );
        
        $js = "
            jQuery(document).ready(function($) {

                // ── Initialize Searchable Dropdowns ──────────────────────────
                if ($('.mh-select2-enabled').length > 0) {
                    $('.mh-select2-enabled').each(function() {
                        var \$el = $(this);
                        var options = {
                            width: '100%',
                            minimumResultsForSearch: 1,
                            dropdownParent: \$el.parent()
                        };
                        if ($.fn.selectWoo) {
                            \$el.selectWoo(options);
                        } else if ($.fn.select2) {
                            \$el.select2(options);
                        }

                        // Fix z-index stacking when using dropdownParent
                        \$el.on('select2:open', function() {
                            $(this).closest('.mh-woo-attribute-wrapper').css('z-index', 9999);
                        }).on('select2:close', function() {
                            $(this).closest('.mh-woo-attribute-wrapper').css('z-index', '');
                        });
                    });
                }

                // ── UI Sync for Pills & Radios ───────────────────────────────
                // This keeps the hidden <select> in perfect sync so the price update
                // and Cart logic continue working without breaking changes.
                $(document).on('click', '.mh-attr-pill', function() {
                    var \$this = $(this);
                    var targetId = \$this.data('target');
                    var val = \$this.data('value');
                    
                    // UI upate
                    \$this.siblings().removeClass('mh-active');
                    \$this.addClass('mh-active');
                    
                    // Data update
                    $(targetId).val(val).trigger('change');
                });

                $(document).on('change', '.mh-attr-radio-item input[type=\"radio\"]', function() {
                    var targetId = $(this).data('target');
                    var val = $(this).val();
                    $(targetId).val(val).trigger('change');
                });

                // ── Price Update Logic for .mh-woo-attribute-select ──────────
                if (typeof mhVariationRules === 'undefined' || !Array.isArray(mhVariationRules)) return;

                // Aggressive price target discovery
                // PRIORITIZE .mh-price-inner so we do not destroy the Elementor styling wrapper!
                var \$priceTarget = $('.mh-product-price .mh-price-inner');
                if (\$priceTarget.length === 0) \$priceTarget = $('.mh-product-price .elementor-widget-container');
                if (\$priceTarget.length === 0) \$priceTarget = $('.mh-product-price');
                if (\$priceTarget.length === 0) \$priceTarget = $('p.price').first();
                if (\$priceTarget.length === 0) {
                    \$priceTarget = $('.woocommerce-Price-amount').first().closest('p, div');
                }
                var defaultPriceHtml = \$priceTarget.length > 0 ? \$priceTarget.html() : '';

                // Disable cart button on load until a complete selection is made
                if ($('.mh-woo-attribute-select').length > 0) {
                    $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', true).addClass('disabled');
                }

                $(document).on('change', '.mh-woo-attribute-select', function() {
                    if (\$priceTarget.length === 0) return;

                    var variationPairs = [];
                    var allSelected = true;

                    $('.mh-woo-attribute-select').each(function() {
                        var val = $(this).val();
                        if (!val || val === '') {
                            allSelected = false;
                        } else {
                            var isVariation = $(this).data('is-variation');
                            if (isVariation === true || isVariation === 'true') {
                                var rawName = $(this).data('attribute_name') || $(this).attr('name') || '';
                                var attrKey = rawName.replace(/^attribute_/, '');
                                variationPairs.push({
                                    key: attrKey.toLowerCase(),
                                    val: String(val).trim().toLowerCase()
                                });
                            }
                        }
                    });

                    if (!allSelected) {
                        \$priceTarget.html(defaultPriceHtml);
                        $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', true).addClass('disabled');
                        return;
                    }

                    // Mirror PHP: ksort (sort by attribute key)
                    variationPairs.sort(function(a, b) {
                        if (a.key < b.key) return -1;
                        if (a.key > b.key) return 1;
                        return 0;
                    });

                    var comboString = variationPairs.map(function(p) { return p.val; }).join('|');
                    console.log('MH-Plug [attributes widget] searching:', comboString);

                    var matchedRule = mhVariationRules.find(function(rule) {
                        var rawKey = rule.combination_string || rule.combination;
                        if (!rawKey) return false;
                        return String(rawKey).trim().toLowerCase() === comboString;
                    });

                    if (matchedRule) {
                        console.log('MH-Plug: Match found!', matchedRule);
                        var currencySymbol = $('.woocommerce-Price-currencySymbol').first().text() || '৳';
                        var newPriceHtml = '';

                        if (matchedRule.sale_price && parseFloat(matchedRule.sale_price) > 0) {
                            newPriceHtml =
                                '<del aria-hidden=\"true\"><span class=\"woocommerce-Price-amount amount\"><bdi>' +
                                '<span class=\"woocommerce-Price-currencySymbol\">' + currencySymbol + '</span>' +
                                parseFloat(matchedRule.regular_price).toFixed(2) +
                                '</bdi></span></del> ' +
                                '<ins><span class=\"woocommerce-Price-amount amount\"><bdi>' +
                                '<span class=\"woocommerce-Price-currencySymbol\">' + currencySymbol + '</span>' +
                                parseFloat(matchedRule.sale_price).toFixed(2) +
                                '</bdi></span></ins>';
                        } else {
                            newPriceHtml =
                                '<span class=\"woocommerce-Price-amount amount\"><bdi>' +
                                '<span class=\"woocommerce-Price-currencySymbol\">' + currencySymbol + '</span>' +
                                parseFloat(matchedRule.regular_price).toFixed(2) +
                                '</bdi></span>';
                        }

                        \$priceTarget.hide().html(newPriceHtml).fadeIn(200);
                        $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', false).removeClass('disabled');

                    } else {
                        \$priceTarget.hide().html(
                            '<span style=\"color:#b32d2e; font-weight:bold; font-size:18px;\">Selection Unavailable</span>'
                        ).fadeIn(200);
                        $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', true).addClass('disabled');
                    }
                });

                // ── Form Submit Bridge ────────────────────────────────────────
                // The attribute dropdowns are Elementor widgets OUTSIDE the
                // WooCommerce cart form. On submit, inject the selected values
                // as hidden inputs so PHP receives them in \$_POST.
                // Uses mh_custom_attr[key] format matching the cart handler.
                $('body').on('submit', 'form.cart', function() {
                    var \$form = $(this);

                    $('.mh-woo-attribute-select').each(function() {
                        var \$select = $(this);
                        var val = \$select.val();
                        if (!val || val === '') return; // skip unselected

                        // Derive the raw attribute key: strip 'attribute_' prefix
                        // e.g. name=\"attribute_color\" → key \"color\"
                        var rawName = \$select.attr('name') || '';
                        var attrKey = rawName.replace(/^attribute_/, '');
                        if (!attrKey) attrKey = rawName;

                        // Remove any previous hidden input to avoid duplicates
                        \$form.find('input[name=\"mh_custom_attr[' + attrKey + ']\"]').remove();
                        // Inject fresh value
                        \$form.append(
                            '<input type=\"hidden\"' +
                            ' name=\"mh_custom_attr[' + attrKey + ']\"' +
                            ' value=\"' + val + '\">'
                        );
                        console.log('MH-Plug bridge: injected mh_custom_attr[' + attrKey + '] =', val);
                    });
                });

            });
        ";
        wp_add_inline_script( 'jquery-core', $js );
    }
}