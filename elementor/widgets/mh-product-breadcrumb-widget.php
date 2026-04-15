<?php
/**
 * MH Product Breadcrumb Widget
 *
 * Displays the WooCommerce Breadcrumbs dynamically.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Breadcrumb_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_breadcrumb'; }
    public function get_title() { return __( 'MH Product Breadcrumb', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-breadcrumbs'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'breadcrumb', 'breadcrumbs', 'woocommerce', 'mh', 'navigation' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Breadcrumb Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'home_text', [
            'label'   => __( 'Home Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Home', 'mh-plug' ),
        ] );

        $this->add_control( 'delimiter', [
            'label'   => __( 'Separator', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => ' / ',
            'description' => __( 'The character(s) between breadcrumb links.', 'mh-plug' ),
        ] );

        $this->add_responsive_control( 'align', [
            'label'        => __( 'Alignment', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'    => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'  => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'      => 'left',
            'selectors'    => [
                '{{WRAPPER}} .mh-product-breadcrumb' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Text & Links Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'typography',
            'selector' => '{{WRAPPER}} .mh-product-breadcrumb',
        ] );

        $this->start_controls_tabs( 'breadcrumb_color_tabs' );

        /* Normal Links */
        $this->start_controls_tab( 'tab_links', [ 'label' => __( 'Links', 'mh-plug' ) ] );
        $this->add_control( 'link_color', [
            'label'     => __( 'Link Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#555555',
            'selectors' => [
                '{{WRAPPER}} .mh-product-breadcrumb a' => 'color: {{VALUE}}; transition: color 0.3s ease;',
            ],
        ] );
        $this->add_control( 'link_hover_color', [
            'label'     => __( 'Hover Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [
                '{{WRAPPER}} .mh-product-breadcrumb a:hover' => 'color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();

        /* Current Page Text */
        $this->start_controls_tab( 'tab_current', [ 'label' => __( 'Current Page', 'mh-plug' ) ] );
        $this->add_control( 'current_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-product-breadcrumb' => 'color: {{VALUE}};', // Applies to the unlinked text at the end
            ],
        ] );
        $this->end_controls_tab();

        /* Separator */
        $this->start_controls_tab( 'tab_separator', [ 'label' => __( 'Separator', 'mh-plug' ) ] );
        $this->add_control( 'separator_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#cccccc',
            'selectors' => [
                '{{WRAPPER}} .mh-product-breadcrumb .mh-crumb-divider' => 'color: {{VALUE}}; margin: 0 6px;',
            ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'margin', [
            'label'      => __( 'Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-breadcrumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $delimiter = '<span class="mh-crumb-divider">' . esc_html( $settings['delimiter'] ) . '</span>';
        $home_text = ! empty( $settings['home_text'] ) ? esc_html( $settings['home_text'] ) : 'Home';

        // Display Mock Breadcrumb inside Elementor Editor to make styling easy
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            ?>
            <nav class="mh-product-breadcrumb woocommerce-breadcrumb">
                <a href="#"><?php echo $home_text; ?></a> 
                <?php echo $delimiter; ?> 
                <a href="#">Category</a> 
                <?php echo $delimiter; ?> 
                <a href="#">Subcategory</a> 
                <?php echo $delimiter; ?> 
                Current Product Name
            </nav>
            <?php
            return;
        }

        // On the live frontend, output the real WooCommerce Breadcrumb
        $args = [
            'delimiter'   => $delimiter,
            'wrap_before' => '<nav class="mh-product-breadcrumb woocommerce-breadcrumb">',
            'wrap_after'  => '</nav>',
            'home'        => $home_text,
        ];

        // WooCommerce core function for breadcrumbs
        if ( function_exists( 'woocommerce_breadcrumb' ) ) {
            woocommerce_breadcrumb( $args );
        }
    }
}