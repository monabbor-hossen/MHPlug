<?php
/**
 * MH Product Sorting Filter Widget
 * Highly customizable, responsive, and animated sorting buttons for WooCommerce Archives.
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

class MH_Product_Filter_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_filter'; }
    public function get_title() { return __( 'MH Product Sorting Filter', 'mh-plug' ); }
    public function get_icon() { return 'eicon-filter'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {
        
        // ----------------------------------------------------
        // CONTENT: FILTER OPTIONS (REPEATER)
        // ----------------------------------------------------
        $this->start_controls_section( 'section_filters', [
            'label' => __( 'Sorting Options', 'mh-plug' ),
        ] );

        $repeater = new Repeater();

        $repeater->add_control( 'orderby_val', [
            'label'   => __( 'Sort Type', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'menu_order' => __( 'Default Sorting', 'mh-plug' ),
                'popularity' => __( 'Popularity (Sales)', 'mh-plug' ),
                'rating'     => __( 'Average Rating', 'mh-plug' ),
                'date'       => __( 'Latest (Newest)', 'mh-plug' ),
                'price'      => __( 'Price: Low to High', 'mh-plug' ),
                'price-desc' => __( 'Price: High to Low', 'mh-plug' ),
            ],
        ] );

        $repeater->add_control( 'custom_label', [
            'label'       => __( 'Button Label', 'mh-plug' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Sort Option', 'mh-plug' ),
            'description' => __( 'The text users will see on the button.', 'mh-plug' ),
        ] );

        $this->add_control( 'filters', [
            'label'       => __( 'Filter Buttons', 'mh-plug' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'orderby_val' => 'date', 'custom_label' => __( 'Latest', 'mh-plug' ) ],
                [ 'orderby_val' => 'popularity', 'custom_label' => __( 'Best Sellers', 'mh-plug' ) ],
                [ 'orderby_val' => 'rating', 'custom_label' => __( 'Top Rated', 'mh-plug' ) ],
                [ 'orderby_val' => 'price', 'custom_label' => __( 'Price: Low to High', 'mh-plug' ) ],
                [ 'orderby_val' => 'price-desc', 'custom_label' => __( 'Price: High to Low', 'mh-plug' ) ],
            ],
            'title_field' => '{{{ custom_label }}}',
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: LAYOUT & CONTAINER
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_layout', [
            'label' => __( 'Layout & Alignment', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'layout_direction', [
            'label'     => __( 'Layout Direction', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'row'    => [ 'title' => __( 'Horizontal', 'mh-plug' ), 'icon' => 'eicon-ellipsis-h' ],
                'column' => [ 'title' => __( 'Vertical', 'mh-plug' ), 'icon' => 'eicon-editor-list-ul' ],
            ],
            'default'   => 'row',
            'selectors' => [ '{{WRAPPER}} .mh-filter-wrap' => 'flex-direction: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'alignment', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'flex-start',
            'selectors' => [ 
                '{{WRAPPER}} .mh-filter-wrap' => 'justify-content: {{VALUE}}; align-items: {{VALUE}};' 
            ],
        ] );

        $this->add_responsive_control( 'gap', [
            'label'      => __( 'Gap Between Buttons', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-wrap' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: BUTTONS (NORMAL / HOVER / ACTIVE)
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_buttons', [
            'label' => __( 'Filter Buttons', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => '{{WRAPPER}} .mh-filter-btn',
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20, 'isLinked' => true ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'btn_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'isLinked' => true ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        // 🚀 TABS FOR STATES
        $this->start_controls_tabs( 'tabs_btn_style' );

        // NORMAL
        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#555555', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#f5f5f5', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => '{{WRAPPER}} .mh-filter-btn' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn' ] );
        $this->end_controls_tab();

        // HOVER
        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'btn_hover_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_hover_border', 'selector' => '{{WRAPPER}} .mh-filter-btn:hover' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_hover_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn:hover' ] );
        $this->add_control( 'hover_scale', [
            'label' => __( 'Hover Scale (Animation)', 'mh-plug' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => 1, 'max' => 1.2, 'step' => 0.01 ] ],
            'default' => [ 'size' => 1.05 ],
            'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover' => 'transform: scale({{SIZE}});' ],
        ] );
        $this->end_controls_tab();

        // ACTIVE (CURRENT SORT)
        $this->start_controls_tab( 'tab_btn_active', [ 'label' => __( 'Active', 'mh-plug' ) ] );
        $this->add_control( 'btn_active_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn.mh-active' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_active_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn.mh-active' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_active_border', 'selector' => '{{WRAPPER}} .mh-filter-btn.mh-active' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_active_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn.mh-active' ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'transition_speed', [
            'label'      => __( 'Animation Speed (s)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 's' ],
            'range'      => [ 's' => [ 'min' => 0.1, 'max' => 2, 'step' => 0.1 ] ],
            'default'    => [ 'size' => 0.3, 'unit' => 's' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-btn' => 'transition: all {{SIZE}}s ease;' ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $filters  = $settings['filters'];

        if ( empty( $filters ) || ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        // 🚀 Detect current sorting parameter securely
        $current_orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';

        // 🚀 Generate base URL (Preserves other query params, like active category or price filters, but resets pagination)
        global $wp;
        $current_url = home_url( add_query_arg( [], $wp->request ) );
        $query_args  = $_GET;
        unset( $query_args['paged'] ); // Reset pagination when sorting changes
        ?>

        <style>
            .mh-filter-wrap { display: flex; flex-wrap: wrap; width: 100%; }
            .mh-filter-btn { display: inline-block; text-decoration: none; font-weight: 600; text-align: center; cursor: pointer; border: none; }
            .mh-filter-btn:focus { outline: none; }
        </style>

        <div class="mh-filter-wrap">
            <?php foreach ( $filters as $filter ) : 
                // Build the specific URL for this button
                $query_args['orderby'] = $filter['orderby_val'];
                $filter_url = add_query_arg( $query_args, $current_url );
                
                // Determine if this is the active button
                $is_active = ( $current_orderby === $filter['orderby_val'] ) ? 'mh-active' : '';
            ?>
                <a href="<?php echo esc_url( $filter_url ); ?>" class="mh-filter-btn <?php echo esc_attr( $is_active ); ?>">
                    <?php echo esc_html( $filter['custom_label'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php
    }
}