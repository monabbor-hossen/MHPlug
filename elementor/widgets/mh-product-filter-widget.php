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
            'description' => __( 'The text users will see on the button/dropdown.', 'mh-plug' ),
        ] );

        $this->add_control( 'filters', [
            'label'       => __( 'Sorting Options', 'mh-plug' ),
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
        // STYLE: LAYOUT & ALIGNMENT
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_layout', [
            'label' => __( 'Layout Setting', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'display_style', [
            'label'   => __( 'Display Style', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'buttons',
            'options' => [
                'buttons'  => __( 'Inline Buttons', 'mh-plug' ),
                'dropdown' => __( 'Dropdown Menu', 'mh-plug' ),
            ],
        ] );

        $this->add_responsive_control( 'layout_direction', [
            'label'     => __( 'Direction', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'row'    => [ 'title' => __( 'Horizontal', 'mh-plug' ), 'icon' => 'eicon-ellipsis-h' ],
                'column' => [ 'title' => __( 'Vertical', 'mh-plug' ), 'icon' => 'eicon-editor-list-ul' ],
            ],
            'default'   => 'row',
            'selectors' => [ '{{WRAPPER}} .mh-filter-wrap' => 'flex-direction: {{VALUE}};' ],
            'condition' => [ 'display_style' => 'buttons' ],
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
            'condition'  => [ 'display_style' => 'buttons' ],
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: BUTTONS / TOGGLE
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_buttons', [
            'label' => __( 'Filter Buttons / Toggle', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $btn_target = '{{WRAPPER}} .mh-filter-btn, {{WRAPPER}} .mh-filter-toggle';

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => $btn_target,
        ] );

        $this->add_responsive_control( 'btn_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 10, 'right' => 20, 'bottom' => 10, 'left' => 20, 'isLinked' => true ],
            'selectors'  => [ $btn_target => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'btn_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'isLinked' => true ],
            'selectors'  => [ $btn_target => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->start_controls_tabs( 'tabs_btn_style' );

        $this->start_controls_tab( 'tab_btn_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'btn_color', [ 'label' => __( 'Text/Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#555555', 'selectors' => [ $btn_target => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#f5f5f5', 'selectors' => [ $btn_target => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_border', 'selector' => $btn_target ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_shadow', 'selector' => $btn_target ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_btn_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'btn_hover_color', [ 'label' => __( 'Text/Icon Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_hover_border', 'selector' => '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_hover_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn:hover, {{WRAPPER}} .mh-filter-toggle:hover' ] );
        $this->add_control( 'hover_scale', [
            'label' => __( 'Hover Scale (Animation)', 'mh-plug' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => 1, 'max' => 1.2, 'step' => 0.01 ] ],
            'default' => [ 'size' => 1.05 ],
            'selectors' => [ '{{WRAPPER}} .mh-filter-btn:hover' => 'transform: scale({{SIZE}});' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_btn_active', [ 'label' => __( 'Active', 'mh-plug' ) ] );
        $this->add_control( 'btn_active_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'btn_active_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'btn_active_border', 'selector' => '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'btn_active_shadow', 'selector' => '{{WRAPPER}} .mh-filter-btn.mh-active, {{WRAPPER}} .mh-dropdown-open .mh-filter-toggle' ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control( 'transition_speed', [
            'label'      => __( 'Animation Speed (s)', 'mh-plug' ),
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
            'label'     => __( 'Dropdown Menu', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'display_style' => 'dropdown' ],
        ] );

        $this->add_control( 'dropdown_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'dropdown_width', [
            'label'      => __( 'Menu Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 100, 'max' => 400 ] ],
            'default'    => [ 'size' => 200, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'min-width: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'dropdown_padding', [
            'label'      => __( 'Inner Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}} .mh-filter-dropdown-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'dropdown_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
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

        $this->add_control( 'heading_dropdown_items', [ 'label' => __( 'Dropdown Items', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'dropdown_item_typography',
            'selector' => '{{WRAPPER}} .mh-filter-dropdown-item',
        ] );

        $this->start_controls_tabs( 'tabs_dropdown_item_style' );
        $this->start_controls_tab( 'tab_dropdown_item_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'item_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#333333', 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'item_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item' => 'background-color: {{VALUE}};' ] ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_dropdown_item_hover', [ 'label' => __( 'Hover / Active', 'mh-plug' ) ] );
        $this->add_control( 'item_hover_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item:hover, {{WRAPPER}} .mh-filter-dropdown-item.mh-active' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'item_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#f9f9f9', 'selectors' => [ '{{WRAPPER}} .mh-filter-dropdown-item:hover, {{WRAPPER}} .mh-filter-dropdown-item.mh-active' => 'background-color: {{VALUE}};' ] ] );
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

        <style>
            .mh-filter-wrap { display: flex; flex-wrap: wrap; width: 100%; position: relative; }
            .mh-filter-btn, .mh-filter-toggle { display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; font-weight: 600; text-align: center; cursor: pointer; border: none; outline: none; }
            
            /* Dropdown Specific Styles */
            .mh-dropdown-container { position: relative; display: inline-block; }
            .mh-filter-toggle i { transition: transform 0.3s ease; }
            .mh-dropdown-open .mh-filter-toggle i { transform: rotate(180deg); }
            
            .mh-filter-dropdown-menu { position: absolute; top: calc(100% + 10px); left: 0; z-index: 99; display: flex; flex-direction: column; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; overflow: hidden; }
            .mh-dropdown-open .mh-filter-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
            
            .mh-filter-dropdown-item { padding: 10px 20px; text-decoration: none; display: block; transition: all 0.2s ease; }
        </style>

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
                            $filter_url = add_query_arg( $query_args, $current_url );
                            $is_active = ( $current_orderby === $filter['orderby_val'] ) ? 'mh-active' : '';
                        ?>
                            <a href="<?php echo esc_url( $filter_url ); ?>" class="mh-filter-dropdown-item <?php echo esc_attr( $is_active ); ?>">
                                <?php echo esc_html( $filter['custom_label'] ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <script>
                jQuery(document).ready(function($) {
                    var $dropdown = $('#mh-dropdown-<?php echo esc_js( $widget_id ); ?>');
                    var $toggle   = $dropdown.find('.mh-filter-toggle');

                    $toggle.on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $('.mh-dropdown-container').not($dropdown).removeClass('mh-dropdown-open'); // Close others
                        $dropdown.toggleClass('mh-dropdown-open');
                    });

                    // Close dropdown when clicking outside
                    $(document).on('click', function(e) {
                        if ( !$dropdown.is(e.target) && $dropdown.has(e.target).length === 0 ) {
                            $dropdown.removeClass('mh-dropdown-open');
                        }
                    });
                });
                </script>

            <?php else : ?>
                <?php foreach ( $filters as $filter ) : 
                    $query_args['orderby'] = $filter['orderby_val'];
                    $filter_url = add_query_arg( $query_args, $current_url );
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