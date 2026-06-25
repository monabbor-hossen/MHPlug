<?php
/**
 * MH Product Sorting Filter Widget
 * Highly customizable, responsive, and animated sorting options for WooCommerce Archives.
 * Added: Dropdown Style Layout with full customization.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

class MH_Plug_Product_Filter_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_product_filter'; }
    public function get_title() { return __( 'MH Product Sorting Filter', 'mh-plug-ecommerce-builder-widgets' ); }
    public function get_icon() { return 'eicon-filter'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {
        
        // ----------------------------------------------------
        // CONTENT: FILTER OPTIONS (REPEATER)
        // ----------------------------------------------------
        $this->start_controls_section( 'section_filters', [
            'label' => __( 'Sorting Options', 'mh-plug-ecommerce-builder-widgets' ),
        ] );

        $repeater = new Repeater();

        $repeater->add_control( 'orderby_val', [
            'label'   => __( 'Sort Type', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'menu_order' => __( 'Default Sorting', 'mh-plug-ecommerce-builder-widgets' ),
                'popularity' => __( 'Popularity (Sales)', 'mh-plug-ecommerce-builder-widgets' ),
                'rating'     => __( 'Average Rating', 'mh-plug-ecommerce-builder-widgets' ),
                'date'       => __( 'Latest (Newest)', 'mh-plug-ecommerce-builder-widgets' ),
                'price'      => __( 'Price: Low to High', 'mh-plug-ecommerce-builder-widgets' ),
                'price-desc' => __( 'Price: High to Low', 'mh-plug-ecommerce-builder-widgets' ),
            ],
        ] );

        $repeater->add_control( 'custom_label', [
            'label'       => __( 'Button Label', 'mh-plug-ecommerce-builder-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Sort Option', 'mh-plug-ecommerce-builder-widgets' ),
            'description' => __( 'The text users will see on the button/dropdown.', 'mh-plug-ecommerce-builder-widgets' ),
        ] );

        $this->add_control( 'filters', [
            'label'       => __( 'Sorting Options', 'mh-plug-ecommerce-builder-widgets' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'orderby_val' => 'date', 'custom_label' => __( 'Latest', 'mh-plug-ecommerce-builder-widgets' ) ],
                [ 'orderby_val' => 'popularity', 'custom_label' => __( 'Best Sellers', 'mh-plug-ecommerce-builder-widgets' ) ],
                [ 'orderby_val' => 'rating', 'custom_label' => __( 'Top Rated', 'mh-plug-ecommerce-builder-widgets' ) ],
                [ 'orderby_val' => 'price', 'custom_label' => __( 'Price: Low to High', 'mh-plug-ecommerce-builder-widgets' ) ],
                [ 'orderby_val' => 'price-desc', 'custom_label' => __( 'Price: High to Low', 'mh-plug-ecommerce-builder-widgets' ) ],
            ],
            'title_field' => '{{{ custom_label }}}',
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: LAYOUT & ALIGNMENT
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_layout', [
            'label' => __( 'Layout Setting', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'display_style', [
            'label'   => __( 'Display Style', 'mh-plug-ecommerce-builder-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'buttons',
            'options' => [
                'buttons'  => __( 'Inline Buttons', 'mh-plug-ecommerce-builder-widgets' ),
                'dropdown' => __( 'Dropdown Menu', 'mh-plug-ecommerce-builder-widgets' ),
            ],
        ] );

        $this->add_responsive_control( 'layout_direction', [
            'label'     => __( 'Direction', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'row'    => [ 'title' => __( 'Horizontal', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-ellipsis-h' ],
                'column' => [ 'title' => __( 'Vertical', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-editor-list-ul' ],
            ],
            'default'   => 'row',
            'selectors' => [ '{{WRAPPER}} .mh-filter-wrap' => 'flex-direction: {{VALUE}};' ],
            'condition' => [ 'display_style' => 'buttons' ],
        ] );

        $this->add_responsive_control( 'alignment', [
            'label'     => __( 'Alignment', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'flex-start',
            'selectors' => [ 
                '{{WRAPPER}} .mh-filter-wrap' => 'justify-content: {{VALUE}}; align-items: {{VALUE}};' 
            ],
        ] );

        $this->add_responsive_control( 'gap', [
            'label'      => __( 'Gap Between Buttons', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-wrap' => 'gap: {{SIZE}}{{UNIT}};' ],
            'condition'  => [ 'display_style' => 'buttons' ],
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: BUTTONS / TOGGLE
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_buttons', [
            'label' => __( 'Filter Buttons / Toggle', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $btn_target = '{{WRAPPER}} .mh-filter-btn, {{WRAPPER}} .mh-filter-toggle';

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => $btn_target,
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20, 'isLinked' => true ],
            'selectors'  => [ $btn_target => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'btn_radius', [
            'label'      => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'isLinked' => true ],
            'selectors'  => [ $btn_target => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->start_controls_tabs( 'tabs_btn_style' );

        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'btn_color', [ 'label' => __( 'Text/Icon Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#555555', 'selectors' => [ $btn_target => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#f5f5f5', 'selectors' => [ $btn_target => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => $btn_target ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_shadow', 'selector' => $btn_target ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => __( 'Hover', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'btn_hover_color', [ 'label' => __( 'Text/Icon Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_hover_border', 'selector' => '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_hover_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' ] );
        $this->add_control( 'hover_scale', [
            'label' => __( 'Hover Scale (Animation)', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => 1, 'max' => 1.2, 'step' => 0.01 ] ],
            'default' => [ 'size' => 1.05 ],
            'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover' => 'transform: scale({{SIZE}});' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_btn_active', [ 'label' => __( 'Active', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'btn_active_color', [ 'label' => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_active_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_active_border', 'selector' => '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_active_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'transition_speed', [
            'label'      => __( 'Animation Speed (s)', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 's' ],
            'range'      => [ 's' => [ 'min' => 0.1, 'max' => 2, 'step' => 0.1 ] ],
            'default'    => [ 'size' => 0.3, 'unit' => 's' ],
            'selectors'  => [ $btn_target => 'transition: all {{SIZE}}s ease;' ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: DROPDOWN MENU 
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_dropdown', [
            'label'     => __( 'Dropdown Menu', 'mh-plug-ecommerce-builder-widgets' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'display_style' => 'dropdown' ],
        ] );

        $this->add_control( 'dropdown_bg', [
            'label'     => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'dropdown_width', [
            'label'      => __( 'Menu Width', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 100, 'max' => 400 ] ],
            'default'    => [ 'size' => 200, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'min-width: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'dropdown_padding', [
            'label'      => __( 'Inner Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'dropdown_radius', [
            'label'      => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'isLinked' => true ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'dropdown_border',
            'selector' => '{{WRAPPER}} .mh-filter-dropdown-menu',
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'dropdown_shadow',
            'selector' => '{{WRAPPER}} .mh-filter-dropdown-menu',
            'default'  => [
                'horizontal' => 0, 'vertical' => 8, 'blur' => 20, 'color' => 'rgba(0,0,0,0.1)'
            ]
        ] );

        $this->add_control( 'heading_dropdown_items', [ 'label' => __( 'Dropdown Items', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'dropdown_item_typography',
            'selector' => '{{WRAPPER}} .mh-filter-dropdown-item',
        ] );

        $this->start_controls_tabs( 'tabs_dropdown_item_style' );
        $this->start_controls_tab( 'tab_dropdown_item_normal', [ 'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'item_color', [ 'label' => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'item_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_dropdown_item_hover', [ 'label' => __( 'Hover / Active', 'mh-plug-ecommerce-builder-widgets' ) ] );
        $this->add_control( 'item_hover_color', [ 'label' => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item:hover, {{WRAPPER}} .mh-filter-dropdown-item.mh-active' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'item_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '#f9f9f9', 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item:hover, {{WRAPPER}} .mh-filter-dropdown-item.mh-active' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $filters  = $settings['filters'];

        if ( empty( $filters ) || ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        // Securely grab current sorting order
        $current_orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';

        global $wp;
        $current_url = home_url( add_query_arg( [], $wp->request ) );
        $query_args  = $_GET;
        unset( $query_args['paged'] ); // Reset pagination

        // Find Active Label for Dropdown
        $active_label = $filters[0]['custom_label']; // Fallback
        foreach ( $filters as $filter ) {
            if ( $current_orderby === $filter['orderby_val'] ) {
                $active_label = $filter['custom_label'];
                break;
            }
        }
        
        $is_dropdown = ( $settings['display_style'] === 'dropdown' );
        $widget_id   = $this->get_id();
        ?>


        <div class="mh-filter-wrap">
            
            <?php if ( $is_dropdown ) : ?>
                <div class="mh-dropdown-container" id="mh-dropdown-<?php echo esc_attr( $widget_id ); ?>">
                    <button class="mh-filter-toggle">
                        <span class="mh-toggle-text"><?php echo esc_html( $active_label ); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="mh-filter-dropdown-menu">
                        <?php foreach ( $filters as $filter ) : 
                            $query_args['orderby'] = $filter['orderby_val'];
                            $filter_url = esc_url( add_query_arg( $query_args, $current_url ) );
                            $is_active = ( $current_orderby === $filter['orderby_val'] ) ? 'mh-active' : '';
                        ?>
                            <a href="<?php echo esc_url( $filter_url ); ?>" class="mh-filter-dropdown-item <?php echo esc_attr( $is_active ); ?>">
                                <?php echo esc_html( $filter['custom_label'] ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>


            <?php else : ?>
                <?php foreach ( $filters as $filter ) : 
                    $query_args['orderby'] = $filter['orderby_val'];
                    $filter_url = esc_url( add_query_arg( $query_args, $current_url ) );
                    $is_active = ( $current_orderby === $filter['orderby_val'] ) ? 'mh-active' : '';
                ?>
                    <a href="<?php echo esc_url( $filter_url ); ?>" class="mh-filter-btn <?php echo esc_attr( $is_active ); ?>">
                        <?php echo esc_html( $filter['custom_label'] ); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        <?php
    }
}