<?php
/**
 * MH Nav Menu Widget
 *
 * A fully responsive navigation menu with desktop horizontal and mobile hamburger layouts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class MH_Nav_Menu_Widget extends Widget_Base {

    public function get_name() { return 'mh_nav_menu'; }
    public function get_title() { return __( 'MH Nav Menu', 'mh-plug' ); }
    public function get_icon() { return 'eicon-nav-menu'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'menu', 'nav', 'navigation', 'header', 'mh' ]; }

    // Helper function to get WordPress menus
    private function get_available_menus() {
        $menus = wp_get_nav_menus();
        $options = [];
        foreach ( $menus as $menu ) {
            $options[ $menu->slug ] = $menu->name;
        }
        return $options;
    }

    protected function register_controls() {

        /* ── CONTENT: MENU SETTINGS ── */
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Menu Layout', 'mh-plug' ),
        ] );

        $menus = $this->get_available_menus();
        if ( ! empty( $menus ) ) {
            $this->add_control( 'menu', [
                'label'   => __( 'Menu', 'mh-plug' ),
                'type'    => Controls_Manager::SELECT,
                'options' => $menus,
                'default' => array_keys( $menus )[0],
                'save_default' => true,
            ] );
        } else {
            $this->add_control( 'menu', [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => '<strong>' . __( 'There are no menus in your site.', 'mh-plug' ) . '</strong><br>' . sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'mh-plug' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
            ] );
        }

        $this->add_responsive_control( 'align_items', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug' ),   'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug' ),  'icon' => 'eicon-h-align-right' ],
                'stretch'    => [ 'title' => __( 'Stretch', 'mh-plug' ),'icon' => 'eicon-h-align-stretch' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-nav-desktop .mh-menu' => 'justify-content: {{VALUE}};',
                '{{WRAPPER}} .mh-nav-mobile-toggle'    => 'justify-content: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'mobile_breakpoint', [
            'label'   => __( 'Mobile Breakpoint', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '768',
            'options' => [
                '1024' => __( 'Tablet (1024px)', 'mh-plug' ),
                '768'  => __( 'Mobile (768px)', 'mh-plug' ),
                'none' => __( 'None', 'mh-plug' ),
            ],
            'description' => __( 'At what screen size should the hamburger menu appear?', 'mh-plug' ),
        ] );

        $this->end_controls_section();

        /* ── STYLE: MAIN MENU ── */
        $this->start_controls_section( 'section_style_main_menu', [
            'label' => __( 'Main Menu', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'main_menu_typography',
            'selector' => '{{WRAPPER}} .mh-menu > li > a',
        ] );

        $this->start_controls_tabs( 'tabs_main_menu_style' );

        // Normal
        $this->start_controls_tab( 'tab_main_menu_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'main_menu_text_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_menu_bg_color', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab( 'tab_main_menu_hover', [ 'label' => __( 'Hover / Active', 'mh-plug' ) ] );
        $this->add_control( 'main_menu_text_color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu > li > a:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'main_menu_bg_color_hover', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu > li > a:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'main_menu_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );
        
        $this->add_responsive_control( 'main_menu_gap', [
            'label'      => __( 'Item Spacing', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-menu' => 'gap: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: DROPDOWN ── */
        $this->start_controls_section( 'section_style_dropdown', [
            'label' => __( 'Dropdown Menu', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'dropdown_bg_color', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'dropdown_text_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu a' => 'color: {{VALUE}};' ],
        ] );
        
        $this->add_control( 'dropdown_text_color_hover', [
            'label'     => __( 'Hover Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#004265',
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu a:hover' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'dropdown_typography',
            'selector' => '{{WRAPPER}} .mh-menu .sub-menu a',
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'dropdown_box_shadow',
            'selector' => '{{WRAPPER}} .mh-menu .sub-menu',
        ] );

        $this->end_controls_section();

        /* ── STYLE: MOBILE TOGGLE ── */
        $this->start_controls_section( 'section_style_toggle', [
            'label' => __( 'Mobile Toggle Button', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'toggle_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle i' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'toggle_bg_color', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'toggle_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 24 ],
            'selectors'  => [ '{{WRAPPER}} .mh-nav-mobile-toggle i' => 'font-size: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $menu_slug = isset( $settings['menu'] ) ? $settings['menu'] : '';
        $breakpoint = isset( $settings['mobile_breakpoint'] ) ? $settings['mobile_breakpoint'] : '768';
        $widget_id = $this->get_id();

        if ( ! $menu_slug ) {
            echo '<div style="padding:10px; border:1px dashed #ccc; text-align:center;">' . __( 'Please select a menu.', 'mh-plug' ) . '</div>';
            return;
        }

        ?>
        <style>
            /* Base Reset */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu a { display: block; text-decoration: none; transition: 0.3s ease; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu li { position: relative; }
            
            /* Dropdowns (Desktop) */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu .sub-menu { position: absolute; top: 100%; left: 0; min-width: 200px; display: none; flex-direction: column; list-style: none; padding: 10px 0; margin: 0; z-index: 999; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu li:hover > .sub-menu { display: flex; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu .sub-menu a { padding: 10px 20px; font-size: 0.9em; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu .sub-menu .sub-menu { top: 0; left: 100%; }

            /* Mobile Toggle */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle { display: none; cursor: pointer; padding: 10px; border-radius: 4px; }
            
            /* Responsive Logic */
            <?php if ( $breakpoint !== 'none' ) : ?>
                @media (max-width: <?php echo esc_attr( $breakpoint ); ?>px) {
                    /* Hide Desktop Menu */
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop { display: none !important; }
                    
                    /* Show Hamburger */
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle { display: flex; align-items: center; }
                    
                    /* Mobile Menu Dropdown Panel */
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel { display: none; width: 100%; position: absolute; top: 100%; left: 0; z-index: 9999; background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu { flex-direction: column; width: 100%; gap: 0 !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu li { width: 100%; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu a { padding: 15px 20px !important; border-bottom: 1px solid #eee; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu { position: static; display: none; box-shadow: none; padding: 0; background: #f9f9f9; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu a { padding-left: 40px !important; }
                    
                    /* Caret for mobile dropdowns */
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-mobile-caret { position: absolute; right: 0; top: 0; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-left: 1px solid #eee; z-index: 10; }
                }
            <?php endif; ?>
        </style>

        <div class="mh-nav-wrapper mh-nav-wrapper-<?php echo esc_attr( $widget_id ); ?>">
            
            <div class="mh-nav-mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>

            <div class="mh-nav-desktop">
                <?php wp_nav_menu([
                    'menu'       => $menu_slug,
                    'menu_class' => 'mh-menu',
                    'container'  => false,
                ]); ?>
            </div>

            <div class="mh-nav-mobile-panel">
                <?php wp_nav_menu([
                    'menu'       => $menu_slug,
                    'menu_class' => 'mh-menu',
                    'container'  => false,
                ]); ?>
            </div>

        </div>

        <script>
            jQuery(document).ready(function($) {
                var wrapperId = '.mh-nav-wrapper-<?php echo esc_attr( $widget_id ); ?>';
                
                // Toggle Mobile Menu Open/Close
                $(wrapperId + ' .mh-nav-mobile-toggle').on('click', function() {
                    $(wrapperId + ' .mh-nav-mobile-panel').slideToggle(300);
                    $(this).find('i').toggleClass('fa-bars fa-times');
                });

                // Add Caret arrows to mobile menu items that have sub-menus
                $(wrapperId + ' .mh-nav-mobile-panel .menu-item-has-children').each(function() {
                    $(this).prepend('<div class="mh-mobile-caret"><i class="fas fa-chevron-down"></i></div>');
                });

                // Handle Sub-Menu Toggles in Mobile View
                $(wrapperId + ' .mh-mobile-caret').on('click', function(e) {
                    e.preventDefault();
                    var $parentLi = $(this).parent('li');
                    $parentLi.children('.sub-menu').slideToggle(300);
                    $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
                });
            });
        </script>
        <?php
    }
}