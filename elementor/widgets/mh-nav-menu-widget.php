<?php
/**
 * MH Nav Menu Widget (Pro Version)
 * Advanced navigation menu with buttery smooth hover pointers, fade-in dropdowns, and sticky support.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Nav_Menu_Widget extends Widget_Base {

    public function get_name() { return 'mh_nav_menu'; }
    public function get_title() { return __( 'MH Nav Menu', 'mh-plug' ); }
    public function get_icon() { return 'eicon-nav-menu'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'menu', 'nav', 'header', 'hamburger', 'dropdown', 'border', 'align' ]; }

    public function get_style_depends() { return [ 'mh-nav-menu-css' ]; }
    public function get_script_depends() { return [ 'mh-nav-menu-js' ]; }

    private function get_available_menus() {
        $menus = wp_get_nav_menus();
        $options = [ '' => __( '— Select a Menu —', 'mh-plug' ) ]; 
        
        if ( ! empty( $menus ) ) {
            foreach ( $menus as $menu ) { 
                $options[ $menu->slug ] = $menu->name; 
            }
        }
        return $options;
    }

    protected function register_controls() {

        /* ==========================================
         * CONTENT TAB
         * ========================================== */

        $this->start_controls_section( 'section_layout', [ 'label' => __( 'Menu Settings', 'mh-plug' ) ] );

        $this->add_control( 'menu', [
            'label'   => __( 'Select Menu', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'options' => $this->get_available_menus(),
            'default' => '',
            'description' => __( 'Go to Appearance > Menus to create new menus.', 'mh-plug' ),
        ] );

        $this->add_control( 'menu_layout', [
            'label'   => __( 'Menu Layout', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [ 'horizontal' => 'Horizontal', 'vertical' => 'Vertical' ],
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'align_items', [
            'label'     => __( 'Menu Position Align', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
                'stretch'    => [ 'title' => 'Stretch', 'icon' => 'eicon-h-align-stretch' ],
            ],
            'prefix_class' => 'mh-nav-align%s-', 
        ] );

        $this->add_responsive_control( 'item_text_align', [
            'label'     => __( 'Item Text Align', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu > li > a' => 'justify-content: {{VALUE}}; text-align: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'enable_sticky', [
            'label'        => __( 'Consider Sticky Header', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'no',
            'separator'    => 'before'
        ] );

        $this->add_control( 'scroll_speed', [
            'label'      => __( 'Scroll Speed (ms)', 'mh-plug' ),
            'type'       => Controls_Manager::NUMBER,
            'default'    => 500,
            'description'=> 'Duration for smooth scroll anchor links.',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_menu_items', [ 'label' => __( 'Item Options', 'mh-plug' ) ] );

        $this->add_control( 'hover_effect', [
            'label'   => __( 'Hover Effect', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'underline',
            'options' => [ 'none' => 'None', 'underline' => 'Underline Pointer', 'background' => 'Background Fade' ],
        ] );

        $this->add_control( 'submenu_icon', [
            'label'   => __( 'Sub Menu Icon', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'fa-caret-down',
            'options' => [ 'none' => 'None', 'fa-caret-down' => 'Triangle', 'fa-angle-down' => 'Angle', 'fa-plus' => 'Plus' ],
        ] );

        $this->add_control( 'submenu_display', [
            'label'   => __( 'Sub Menu Display', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'hover',
            'options' => [ 'hover' => 'On Mouse Over', 'click' => 'On Click' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_mobile_menu', [ 'label' => __( 'Mobile Menu', 'mh-plug' ) ] );

        $this->add_control( 'mobile_breakpoint', [
            'label'   => __( 'Show On', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '768',
            'options' => [ '1024' => 'Tablet (≤ 1024px)', '768' => 'Mobile (≤ 768px)', 'none' => 'None' ],
        ] );

        $this->add_control( 'mobile_stretch', [
            'label'   => __( 'Stretch Dropdown', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'full',
            'options' => [ 'full' => 'Full Screen Width', 'custom' => 'Stay Inside Column' ],
        ] );

        $this->add_responsive_control( 'toggle_align', [
            'label'     => __( 'Toggle Align', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle-wrapper' => 'justify-content: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'mobile_dropdown_distance', [
            'label'      => __( 'Dropdown Distance (Fix Overlap)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => -50, 'max' => 200 ] ],
            'selectors'  => [ '{{WRAPPER}} .mh-nav-mobile-panel' => 'margin-top: {{SIZE}}{{UNIT}};' ],
            'description'=> __( 'Push the menu down so it does not overlap your header items.', 'mh-plug' ),
            'separator'  => 'before',
        ] );

        $this->end_controls_section();

        /* ==========================================
         * STYLE TAB
         * ========================================== */

        $this->start_controls_section( 'style_main_menu', [ 'label' => __( 'Main Menu', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'main_menu_typography',
            'selector' => '{{WRAPPER}} .mh-menu > li > a',
        ] );

        $this->start_controls_tabs( 'tabs_main_menu_style' );
        
        $this->start_controls_tab( 'tab_main_normal', [ 'label' => 'Normal' ] );
        $this->add_control( 'main_color', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_bg', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'main_menu_border_normal',
            'selector' => '{{WRAPPER}} .mh-menu > li > a',
        ] );
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_main_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'main_color_hover', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_bg_hover', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a:hover' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'main_menu_border_hover',
            'selector' => '{{WRAPPER}} .mh-menu > li > a:hover',
        ] );
        $this->add_control( 'main_pointer_color', [
            'label' => 'Pointer Color (If Underline)', 'type' => Controls_Manager::COLOR,
            'condition' => [ 'hover_effect' => 'underline' ],
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a::after' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_main_active', [ 'label' => 'Active' ] );
        $this->add_control( 'main_color_active', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_bg_active', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'main_menu_border_active',
            'selector' => '{{WRAPPER}} .mh-menu > li.current-menu-item > a',
        ] );
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->add_control( 'pointer_height', [
            'label' => 'Pointer Height (px)', 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 2 ],
            'condition' => [ 'hover_effect' => 'underline' ],
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a::after' => 'height: {{SIZE}}px;' ],
            'separator' => 'before'
        ] );

        $this->add_responsive_control( 'inner_h_spacing', [
            'label' => 'Inner Horizontal Spacing', 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 15 ],
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'padding-left: {{SIZE}}px; padding-right: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'vertical_spacing', [
            'label' => 'Vertical Spacing', 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 15 ],
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'padding-top: {{SIZE}}px; padding-bottom: {{SIZE}}px;' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style_sub_menu', [ 'label' => __( 'Sub Menu', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        // 🚀 NEW FEATURE: Submenu Text Alignment Control
        $this->add_responsive_control( 'sub_text_align', [
            'label'     => __( 'Submenu Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
            ],
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu .sub-menu a' => 'justify-content: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-nav-mobile-panel .mh-menu .sub-menu a' => 'justify-content: {{VALUE}} !important;',
            ],
        ] );

        $this->add_control( 'sub_offset', [
            'label' => 'Sub Menu Offset', 'type' => Controls_Manager::SLIDER,
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu' => 'margin-top: {{SIZE}}px;' ],
            'separator' => 'before',
        ] );

        $this->add_control( 'sub_divider', [
            'label' => 'Item Divider', 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes',
        ] );
        
        $this->add_control( 'sub_divider_color', [
            'label' => 'Divider Color', 'type' => Controls_Manager::COLOR, 'default' => '#eeeeee',
            'condition' => [ 'sub_divider' => 'yes' ],
            // 🚀 THE FIX: Applied !important to Elementor selectors so they always override the base CSS
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu .sub-menu li:not(:last-child) a' => 'border-bottom: 1px solid {{VALUE}} !important;',
                '{{WRAPPER}} .mh-nav-mobile-panel .mh-menu .sub-menu > li > a' => 'border-bottom: 1px solid {{VALUE}} !important;',
            ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name' => 'sub_border', 'selector' => '{{WRAPPER}} .mh-menu .sub-menu',
        ] );

        $this->add_control( 'sub_bg_color', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'default' => '#ffffff',
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu .sub-menu' => 'background-color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-nav-mobile-panel .mh-menu .sub-menu' => 'background-color: {{VALUE}} !important;',
            ],
        ] );

        $this->add_control( 'sub_text_color', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#555555',
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu .sub-menu a' => 'color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-nav-mobile-panel .mh-menu .sub-menu a' => 'color: {{VALUE}} !important;',
            ],
        ] );

        $this->add_control( 'sub_text_color_hover', [
            'label' => 'Hover Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#111111',
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu .sub-menu a:hover' => 'color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-nav-mobile-panel .mh-menu .sub-menu a:hover' => 'color: {{VALUE}} !important;',
            ],
        ] );
        
        $this->add_control( 'sub_bg_color_hover', [
            'label' => 'Hover Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu .sub-menu a:hover' => 'background-color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-nav-mobile-panel .mh-menu .sub-menu a:hover' => 'background-color: {{VALUE}} !important;',
            ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name' => 'sub_shadow', 'selector' => '{{WRAPPER}} .mh-menu .sub-menu',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style_toggle', [ 'label' => __( 'Toggle Button', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );

        $this->add_control( 'toggle_color', [
            'label' => 'Color', 'type' => Controls_Manager::COLOR, 'default' => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle i' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'toggle_bg', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'default' => 'transparent',
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'toggle_size', [
            'label' => 'Icon Size', 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 24 ],
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle i' => 'font-size: {{SIZE}}px;' ],
        ] );

        $this->add_responsive_control( 'toggle_padding', [
            'label' => 'Button Padding', 'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name' => 'toggle_border', 'selector' => '{{WRAPPER}} .mh-nav-mobile-toggle',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $menu_slug  = isset( $settings['menu'] ) ? $settings['menu'] : '';
        $layout     = $settings['menu_layout'];
        $breakpoint = $settings['mobile_breakpoint'];
        $hover      = $settings['hover_effect'];
        $icon       = $settings['submenu_icon'];
        $display    = $settings['submenu_display'];
        $widget_id  = $this->get_id();
        $unique_id  = str_replace('-', '_', $widget_id);

        if ( ! $menu_slug || $menu_slug === '' ) {
            echo '<div style="padding:15px; border:1px dashed #d63638; text-align:center; color: #d63638;"><strong>' . __( 'Please select a menu from the Elementor Panel.', 'mh-plug' ) . '</strong></div>';
            return;
        }

        if ( $settings['enable_sticky'] === 'yes' ) {
            $this->add_render_attribute( '_wrapper', 'class', 'mh-sticky-active' );
            echo '<style>.elementor-element-' . $widget_id . '.mh-sticky-active { position: sticky; top: 0; z-index: 999; transition: all 0.3s ease; }</style>';
        }

        ?>
        <style>
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> { position: relative; width: 100%; box-sizing: border-box; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu { list-style: none; margin: 0; padding: 0; }
            
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu { display: flex; flex-wrap: wrap; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu a { 
                display: flex; align-items: center; text-decoration: none; position: relative; box-sizing: border-box;
                transition: color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), background-color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), border-color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu li { position: relative; }
            
            <?php if ( $icon !== 'none' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .menu-item-has-children > a::after {
                    content: '\<?php echo ( $icon === 'fa-caret-down' ? 'f0d7' : ($icon === 'fa-angle-down' ? 'f107' : 'f067') ); ?>';
                    font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-left: 8px; font-size: 0.8em; transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .menu-item-has-children:hover > a::after { transform: rotate(180deg); }
            <?php endif; ?>

            <?php if ( $hover === 'underline' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu > li > a::after {
                    content: ''; position: absolute; bottom: 0; left: 0; width: 100%; background: #333; 
                    transform: scaleX(0); transform-origin: center; 
                    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu > li > a:hover::after,
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu > li.current-menu-item > a::after { 
                    transform: scaleX(1); 
                }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .menu-item-has-children > a::after { display: none; }
            <?php endif; ?>

            <?php if ( $layout === 'vertical' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu { flex-direction: column; width: 100%; }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu li { width: 100%; }
            <?php endif; ?>

            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu .sub-menu { 
                position: absolute; top: 100%; left: 0; min-width: 220px; 
                flex-direction: column; list-style: none; padding: 0; margin: 0; z-index: 999; 
                opacity: 0; visibility: hidden; pointer-events: none;
                transform: translateY(15px); transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); display: flex;
            }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu .sub-menu a { padding: 12px 20px; font-size: 14px; width: 100%; }
            
            <?php if ( $display === 'hover' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu li:hover > .sub-menu { 
                    opacity: 1; visibility: visible; pointer-events: auto; transform: translateY(0); 
                }
            <?php endif; ?>
            
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle-wrapper { display: none; width: 100%; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel { display: none; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle { cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; }
            
            <?php if ( $breakpoint !== 'none' ) : ?>
                @media (max-width: <?php echo esc_attr( $breakpoint ); ?>px) {
                    
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop { display: none !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle-wrapper { display: flex; }
                    
                    /* 🚀 THE FIX: Prevent squishing when "Stay Inside Column" is used */
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel { 
                        width: 100%; position: absolute; top: 100%; right: 0; left: auto; z-index: 9999; 
                        background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
                        display: none; box-sizing: border-box; min-width: 250px;
                    }

                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu { 
                        display: flex !important; flex-direction: column !important; width: 100% !important; margin: 0 !important; padding: 0 !important; 
                    }
                    
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu li { 
                        display: block !important; width: 100% !important; flex-grow: 0 !important; border: none !important; position: relative;
                    }
                    
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu > li > a { 
                        display: flex; align-items: center; justify-content: space-between; 
                        padding: 15px 20px; border-bottom: 1px solid #eee; width: 100%; box-sizing: border-box; 
                        text-decoration: none; 
                    }

                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu { 
                        position: static !important; display: none; 
                        box-shadow: none !important; border: none !important; background: #fafafa; 
                        opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; transform: none !important; 
                        width: 100% !important; flex-direction: column !important; margin: 0 !important; padding: 0 !important;
                    }
                    
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu > li > a { 
                        padding: 12px 20px 12px 40px !important; display: flex; 
                    }
                    
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .menu-item-has-children > a::after { display: none !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-mobile-caret { 
                        position: absolute; right: 0; top: 0; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; 
                        cursor: pointer; border-left: 1px solid #eee; z-index: 10; color: #555; font-size: 14px;
                    }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-mobile-caret i { transition: transform 0.3s ease; }
                }
            <?php endif; ?>

            <?php
            $align_breakpoints = [
                'desktop' => [ 'prefix' => '.mh-nav-align', 'media' => '' ],
                'tablet'  => [ 'prefix' => '.mh-nav-align-tablet', 'media' => '@media (max-width: 1024px)' ],
                'mobile'  => [ 'prefix' => '.mh-nav-align-mobile', 'media' => '@media (max-width: 767px)' ],
            ];

            foreach ( $align_breakpoints as $bp ) {
                if ( $bp['media'] ) echo $bp['media'] . " {\n";
                $p = $bp['prefix'];
                
                echo "{$p}-flex-start .mh-nav-desktop .mh-menu { justify-content: flex-start; }\n";
                echo "{$p}-center .mh-nav-desktop .mh-menu { justify-content: center; }\n";
                echo "{$p}-flex-end .mh-nav-desktop .mh-menu { justify-content: flex-end; }\n";
                echo "{$p}-stretch .mh-nav-desktop .mh-menu { justify-content: space-between; width: 100%; }\n";
                echo "{$p}-stretch .mh-nav-desktop .mh-menu > li { flex-grow: 1; }\n";
                echo "{$p}-stretch .mh-nav-desktop .mh-menu > li > a { width: 100%; }\n";
                
                if ( $bp['media'] ) echo "}\n";
            }
            ?>
        </style>

        <div class="mh-nav-wrapper mh-nav-wrapper-<?php echo esc_attr( $widget_id ); ?>" data-speed="<?php echo esc_attr($settings['scroll_speed']); ?>">
            
            <div class="mh-nav-mobile-toggle-wrapper">
                <div class="mh-nav-mobile-toggle"><i class="fas fa-bars"></i></div>
            </div>

            <div class="mh-nav-desktop">
                <?php 
                $menu_classes = 'mh-menu';
                if ( $display === 'click' ) {
                    $menu_classes .= ' mh-submenu-click';
                }
                wp_nav_menu([ 'menu' => $menu_slug, 'menu_class' => $menu_classes, 'container' => false ]); 
                ?>
            </div>

            <div class="mh-nav-mobile-panel">
                <?php wp_nav_menu([ 'menu' => $menu_slug, 'menu_class' => 'mh-menu', 'container' => false ]); ?>
            </div>

        </div>

        <script>
            (function($) {
                var initNavMenu_<?php echo $unique_id; ?> = function() {
                    var $wrapper = $('.mh-nav-wrapper-<?php echo esc_attr($widget_id); ?>');
                    if (!$wrapper.length) return; 
                    
                    var $panel = $wrapper.find('.mh-nav-mobile-panel');
                    var $toggle = $wrapper.find('.mh-nav-mobile-toggle');
                    
                    var isFullWidth = <?php echo $settings['mobile_stretch'] === 'full' ? 'true' : 'false'; ?>;
                    var breakpoint = <?php echo $breakpoint === 'none' ? 0 : intval($breakpoint); ?>;
                    var lastWidth = $(window).width(); 

                    function forceMobileFullWidth() {
                        if (isFullWidth && $(window).width() <= breakpoint) {
                            var offsetLeft = $wrapper[0].getBoundingClientRect().left;
                            $panel.css({
                                'width': $(window).width() + 'px',
                                'left': '-' + offsetLeft + 'px',
                                'right': 'auto',
                                'box-sizing': 'border-box'
                            });
                        } else {
                            $panel.css({ 'width': '', 'left': '', 'right': '' });
                        }
                    }

                    $(window).on('resize', function() {
                        var currentWidth = $(window).width();
                        if (currentWidth !== lastWidth) {
                            forceMobileFullWidth();
                            if (currentWidth > breakpoint) {
                                $panel.css('display', '');
                            }
                            lastWidth = currentWidth;
                        }
                    });

                    $toggle.off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        forceMobileFullWidth();
                        $panel.slideToggle(300);
                    });

                    $panel.find('.menu-item-has-children').each(function() {
                        if ($(this).children('.mh-mobile-caret').length === 0) {
                            $(this).children('a').after('<span class="mh-mobile-caret"><i class="fas fa-chevron-down"></i></span>');
                        }
                    });

                    $panel.find('.mh-mobile-caret').off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var $caret = $(this);
                        var $subMenu = $caret.siblings('.sub-menu');
                        
                        $caret.toggleClass('mh-caret-open');
                        
                        if ($caret.hasClass('mh-caret-open')) {
                            $caret.find('i').css('transform', 'rotate(180deg)');
                        } else {
                            $caret.find('i').css('transform', 'rotate(0deg)');
                        }
                        
                        $subMenu.slideToggle(300);
                    });
                };

                initNavMenu_<?php echo $unique_id; ?>();
                $(document).ready(initNavMenu_<?php echo $unique_id; ?>);

            })(jQuery);
        </script>
        <?php
    }
}