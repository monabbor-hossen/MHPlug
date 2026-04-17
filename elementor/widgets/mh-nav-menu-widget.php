<?php
/**
 * MH Nav Menu Widget (Pro Version)
 *
 * Advanced navigation menu with hover borders, item alignment, and sticky support.
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

        // 🚀 NEW: Alignment for the entire menu block
        $this->add_responsive_control( 'align_items', [
            'label'     => __( 'Menu Position Align', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
                'stretch'    => [ 'title' => 'Stretch', 'icon' => 'eicon-h-align-stretch' ],
            ],
            'selectors' => [ '{{WRAPPER}} .mh-nav-desktop .mh-menu' => 'justify-content: {{VALUE}};' ],
        ] );

        // 🚀 NEW: Alignment for the text inside the menu items
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
            'options' => [ 'full' => 'Full Width', 'custom' => 'Custom' ],
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
        
        // --- NORMAL TAB ---
        $this->start_controls_tab( 'tab_main_normal', [ 'label' => 'Normal' ] );
        $this->add_control( 'main_color', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_bg', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'background-color: {{VALUE}};' ],
        ] );
        // 🚀 NEW: Border for Normal State
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'main_menu_border_normal',
            'selector' => '{{WRAPPER}} .mh-menu > li > a',
        ] );
        $this->end_controls_tab();
        
        // --- HOVER TAB ---
        $this->start_controls_tab( 'tab_main_hover', [ 'label' => 'Hover' ] );
        $this->add_control( 'main_color_hover', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_bg_hover', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a:hover' => 'background-color: {{VALUE}};' ],
        ] );
        // 🚀 NEW: Border for Hover State
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

        // --- ACTIVE TAB ---
        $this->start_controls_tab( 'tab_main_active', [ 'label' => 'Active' ] );
        $this->add_control( 'main_color_active', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'main_bg_active', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'background-color: {{VALUE}};' ],
        ] );
        // 🚀 NEW: Border for Active State
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

        $this->add_control( 'sub_offset', [
            'label' => 'Sub Menu Offset', 'type' => Controls_Manager::SLIDER,
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu' => 'margin-top: {{SIZE}}px;' ],
        ] );

        $this->add_control( 'sub_divider', [
            'label' => 'Item Divider', 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes',
        ] );
        
        $this->add_control( 'sub_divider_color', [
            'label' => 'Divider Color', 'type' => Controls_Manager::COLOR, 'default' => '#eeeeee',
            'condition' => [ 'sub_divider' => 'yes' ],
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu li:not(:last-child) a' => 'border-bottom: 1px solid {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name' => 'sub_border', 'selector' => '{{WRAPPER}} .mh-menu .sub-menu',
        ] );

        $this->add_control( 'sub_bg_color', [
            'label' => 'Background Color', 'type' => Controls_Manager::COLOR, 'default' => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'sub_text_color', [
            'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#555555',
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu a' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'sub_text_color_hover', [
            'label' => 'Hover Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#111111',
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu a:hover' => 'color: {{VALUE}};' ],
        ] );
        
        $this->add_control( 'sub_bg_color_hover', [
            'label' => 'Hover Background Color', 'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu a:hover' => 'background-color: {{VALUE}};' ],
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
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> { position: relative; width: 100%; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu a { display: flex; align-items: center; text-decoration: none; transition: all 0.3s ease; position: relative; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu li { position: relative; }
            
            /* Hide Mobile Tools on Desktop */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle-wrapper { display: none; width: 100%; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel { display: none; }
            
            /* Submenu Icon */
            <?php if ( $icon !== 'none' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .menu-item-has-children > a::after {
                    content: '\<?php echo ( $icon === 'fa-caret-down' ? 'f0d7' : ($icon === 'fa-angle-down' ? 'f107' : 'f067') ); ?>';
                    font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-left: 8px; font-size: 0.8em; transition: 0.3s;
                }
            <?php endif; ?>

            /* Hover Effect: Underline */
            <?php if ( $hover === 'underline' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu > li > a::before {
                    content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 0; background: #333; transition: width 0.3s ease;
                }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu > li > a:hover::before,
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu > li.current-menu-item > a::before { width: 100%; }
                
                <?php if ( $icon !== 'none' ) : ?>
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu > .menu-item-has-children > a i.sub-icon { margin-left: 8px; font-size: 0.8em; }
                <?php endif; ?>
            <?php endif; ?>

            /* Layout: Vertical */
            <?php if ( $layout === 'vertical' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu { flex-direction: column; width: 100%; }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu li { width: 100%; }
            <?php endif; ?>

            /* Dropdowns */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu .sub-menu { position: absolute; top: 100%; left: 0; min-width: 220px; display: none; flex-direction: column; list-style: none; padding: 0; margin: 0; z-index: 999; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu .sub-menu a { padding: 12px 20px; font-size: 14px; width: 100%; box-sizing: border-box; }
            
            <?php if ( $display === 'hover' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu li:hover > .sub-menu { display: flex; animation: mhFadeIn 0.3s ease; }
            <?php endif; ?>
            
            @keyframes mhFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle { cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; }
            
            /* Breakpoint Logic */
            <?php if ( $breakpoint !== 'none' ) : ?>
                @media (max-width: <?php echo esc_attr( $breakpoint ); ?>px) {
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop { display: none !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle-wrapper { display: flex; }
                    
                    /* Mobile Panel overrides */
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel { width: 100%; position: absolute; top: 100%; left: 0; z-index: 9999; background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu { flex-direction: column; width: 100%; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu li { width: 100%; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu > li > a { padding: 15px 20px; border-bottom: 1px solid #eee; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu { position: static; display: none; box-shadow: none; border: none; background: #fafafa; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu a { padding-left: 40px; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-mobile-caret { position: absolute; right: 0; top: 0; width: 50px; height: 100%; display: flex; align-items: center; justify-content: center; cursor: pointer; border-left: 1px solid #eee; z-index: 10; }
                }
            <?php endif; ?>
        </style>

        <div class="mh-nav-wrapper mh-nav-wrapper-<?php echo esc_attr( $widget_id ); ?>" data-speed="<?php echo esc_attr($settings['scroll_speed']); ?>">
            
            <div class="mh-nav-mobile-toggle-wrapper">
                <div class="mh-nav-mobile-toggle"><i class="fas fa-bars"></i></div>
            </div>

            <div class="mh-nav-desktop">
                <?php wp_nav_menu([ 'menu' => $menu_slug, 'menu_class' => 'mh-menu', 'container' => false ]); ?>
            </div>

            <div class="mh-nav-mobile-panel">
                <?php wp_nav_menu([ 'menu' => $menu_slug, 'menu_class' => 'mh-menu', 'container' => false ]); ?>
            </div>

        </div>

        <script>
            jQuery(document).ready(function($) {
                var $wrapper = $('.mh-nav-wrapper-<?php echo esc_attr( $widget_id ); ?>');
                
                // 1. Mobile Menu Logic
                $wrapper.find('.mh-nav-mobile-toggle').on('click', function() {
                    $wrapper.find('.mh-nav-mobile-panel').slideToggle(300);
                    $(this).find('i').toggleClass('fa-bars fa-times');
                });

                $wrapper.find('.mh-nav-mobile-panel .menu-item-has-children').each(function() {
                    $(this).prepend('<div class="mh-mobile-caret"><i class="fas fa-chevron-down"></i></div>');
                });

                $wrapper.find('.mh-mobile-caret').on('click', function(e) {
                    e.preventDefault();
                    $(this).parent('li').children('.sub-menu').slideToggle(300);
                    $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
                });

                // 2. Desktop Click Display Logic
                <?php if ( $display === 'click' ) : ?>
                    $wrapper.find('.mh-nav-desktop .menu-item-has-children > a').on('click', function(e) {
                        if ($(this).attr('href') === '#' || $(this).attr('href') === '') {
                            e.preventDefault();
                        }
                        $(this).siblings('.sub-menu').fadeToggle(200);
                    });
                <?php endif; ?>

                // 3. Smooth Scroll Logic
                var scrollSpeed = parseInt($wrapper.data('speed')) || 500;
                $wrapper.find('a[href^="#"]').not('[href="#"]').on('click', function(e) {
                    var target = $(this.getAttribute('href'));
                    if (target.length) {
                        e.preventDefault();
                        $wrapper.find('.mh-nav-mobile-panel').slideUp(300);
                        $wrapper.find('.mh-nav-mobile-toggle i').removeClass('fa-times').addClass('fa-bars');
                        $('html, body').stop().animate({ scrollTop: target.offset().top - 80 }, scrollSpeed);
                    }
                });
            });
        </script>
        <?php
    }
}