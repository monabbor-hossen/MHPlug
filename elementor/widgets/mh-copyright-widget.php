<?php
/**
 * MH Copyright Widget
 *
 * Displays a dynamic copyright notice with full typography and link styling controls.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Copyright_Widget extends Widget_Base {

    public function get_name() { return 'mh_copyright'; }
    public function get_title() { return __( 'MH Copyright', 'mh-plug' ); }
    public function get_icon() { return 'eicon-t-letter'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'copyright', 'footer', 'text', 'year', 'mh' ]; }

    protected function register_controls() {

        /* ==========================================
         * CONTENT TAB
         * ========================================== */
        $this->start_controls_section( 'section_content', [
            'label' => __( 'Copyright Text', 'mh-plug' ),
        ] );

        $this->add_control( 'copyright_text', [
            'label'       => __( 'Content', 'mh-plug' ),
            'type'        => Controls_Manager::WYSIWYG,
            'default'     => __( '&copy; [year] [site_title]. All rights reserved.', 'mh-plug' ),
            'description' => __( 'Use <strong>[year]</strong> to display the current year, and <strong>[site_title]</strong> to display your website name.', 'mh-plug' ),
        ] );

        $this->add_responsive_control( 'align', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ),   'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ),  'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-copyright-wrapper' => 'text-align: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        /* ==========================================
         * STYLE TAB
         * ========================================== */
        $this->start_controls_section( 'section_style', [
            'label' => __( 'Text Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'text_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .mh-copyright-wrapper' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'typography',
            'selector' => '{{WRAPPER}} .mh-copyright-wrapper',
        ] );

        // Link Colors
        $this->add_control( 'link_heading', [
            'label'     => __( 'Link Colors', 'mh-plug' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->start_controls_tabs( 'tabs_link_colors' );

        $this->start_controls_tab( 'tab_link_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'link_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-copyright-wrapper a' => 'color: {{VALUE}}; text-decoration: none; transition: 0.3s;' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_link_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'link_hover_color', [
            'label'     => __( 'Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-copyright-wrapper a:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $text = $settings['copyright_text'];

        // Automatically fetch the current year and site title
        $current_year = date( 'Y' );
        $site_title   = get_bloginfo( 'name' );

        // Replace the shortcodes in the text
        $text = str_replace( '[year]', $current_year, $text );
        $text = str_replace( '[site_title]', $site_title, $text );

        echo '<div class="mh-copyright-wrapper">' . wp_kses_post( $text ) . '</div>';
    }
}