<?php
/**
 * MH Nav Menu Widget
 *
 * Advanced responsive navigation menu with layouts, sticky header, and smooth scroll.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class MH_Nav_Menu_Widget extends Widget_Base {

    public function get_name() { return 'mh_nav_menu'; }
    public function get_title() { return __( 'MH Nav Menu', 'mh-plug' ); }
    public function get_icon() { return 'eicon-nav-menu'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'menu', 'nav', 'navigation', 'header', 'sticky', 'scroll' ]; }

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
                'label'   => __( 'Select Menu', 'mh-plug' ),
                'type'    => Controls_Manager::SELECT,
                'options' => $menus,
                'default' => array_keys( $menus )[0],
            ] );
        } else {
            $this->add_control( 'menu', [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => '<strong>' . __( 'There are no menus in your site.', 'mh-plug' ) . '</strong>',
            ] );
        }

        $this->add_control( 'menu_layout', [
            'label'   => __( 'Menu Layout', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [
                'horizontal' => __( 'Horizontal', 'mh-plug' ),
                'vertical'   => __( 'Vertical', 'mh-plug' ),
            ],
            'prefix_class' => 'mh-nav-layout-',
        ] );

        $this->add_responsive_control( 'align_items', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug' ),   'icon' => 'eicon-h-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-h-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug' ),  'icon' => 'eicon-h-align-right' ],
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
        ] );

        $this->end_controls_section();

        /* ── CONTENT: STICKY HEADER ── */
        $this->start_controls_section( 'section_sticky', [
            'label' => __( 'Sticky Settings', 'mh-plug' ),
        ] );

        $this->add_control( 'enable_sticky', [
            'label'        => __( 'Enable Sticky Menu', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'no',
            'description'  => __( 'Sticks the menu to the top of the screen when scrolling.', 'mh-plug' ),
        ] );

        $this->add_control( 'sticky_bg_color', [
            'label'     => __( 'Sticky Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'condition' => [ 'enable_sticky' => 'yes' ],
            'selectors' => [ '{{WRAPPER}}.mh-is-sticky' => 'background-color: {{VALUE}}; box-shadow: 0 5px 15px rgba(0,0,0,0.1);' ],
        ] );

        $this->add_control( 'sticky_offset', [
            'label'      => __( 'Top Offset (px)', 'mh-plug' ),
            'type'       => Controls_Manager::NUMBER,
            'default'    => 0,
            'condition'  => [ 'enable_sticky' => 'yes' ],
            'selectors'  => [ '{{WRAPPER}}.mh-sticky-active' => 'top: {{VALUE}}px;' ],
        ] );

        $this->end_controls_section();

        /* ── CONTENT: SMOOTH SCROLL ── */
        $this->start_controls_section( 'section_scroll', [
            'label' => __( 'Smooth Scroll (One Page)', 'mh-plug' ),
        ] );

        $this->add_control( 'enable_smooth_scroll', [
            'label'        => __( 'Enable Smooth Scroll', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'no',
            'description'  => __( 'For anchor links (e.g. #about).', 'mh-plug' ),
        ] );

        $this->add_control( 'scroll_speed', [
            'label'      => __( 'Scroll Speed (ms)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'range'      => [ 'px' => [ 'min' => 100, 'max' => 2000, 'step' => 100 ] ],
            'default'    => [ 'size' => 500 ],
            'condition'  => [ 'enable_smooth_scroll' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: MAIN MENU ── */
        $this->start_controls_section( 'section_style_main_menu', [
            'label' => __( 'Main Menu Styles', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'main_menu_typography',
            'selector' => '{{WRAPPER}} .mh-menu > li > a',
        ] );

        $this->start_controls_tabs( 'tabs_main_menu_style' );
        $this->start_controls_tab( 'tab_main_menu_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'main_menu_text_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-menu > li > a' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_main_menu_hover', [ 'label' => __( 'Hover/Active', 'mh-plug' ) ] );
        $this->add_control( 'main_menu_text_color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ 
                '{{WRAPPER}} .mh-menu > li > a:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-menu > li.current-menu-item > a' => 'color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'main_menu_padding', [
            'label'      => __( 'Item Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}} .mh-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );

        $this->end_controls_section();

        /* ── STYLE: DROPDOWN ── */
        $this->start_controls_section( 'section_style_dropdown', [
            'label' => __( 'Dropdown Styles', 'mh-plug' ),
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
            'selectors' => [ '{{WRAPPER}} .mh-menu .sub-menu a' => 'color: {{VALUE}};' ],
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
            'selectors' => [ '{{WRAPPER}} .mh-nav-mobile-toggle i' => 'color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'toggle_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'default'    => [ 'size' => 24 ],
            'selectors'  => [ '{{WRAPPER}} .mh-nav-mobile-toggle i' => 'font-size: {{SIZE}}px;' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $menu_slug  = isset( $settings['menu'] ) ? $settings['menu'] : '';
        $breakpoint = isset( $settings['mobile_breakpoint'] ) ? $settings['mobile_breakpoint'] : '768';
        $layout     = isset( $settings['menu_layout'] ) ? $settings['menu_layout'] : 'horizontal';
        
        $is_sticky    = $settings['enable_sticky'] === 'yes';
        $smooth_scroll= $settings['enable_smooth_scroll'] === 'yes';
        $scroll_speed = isset($settings['scroll_speed']['size']) ? $settings['scroll_speed']['size'] : 500;
        
        $widget_id = $this->get_id();

        if ( ! $menu_slug ) {
            echo '<div style="padding:10px; border:1px dashed #ccc; text-align:center;">' . __( 'Please select a menu.', 'mh-plug' ) . '</div>';
            return;
        }

        // Add wrapper class for sticky JS target
        if ($is_sticky) {
            $this->add_render_attribute( '_wrapper', 'class', 'mh-sticky-active' );
            // Inline style to enforce position sticky on the wrapper
            echo '<style>.elementor-element-' . $widget_id . '.mh-sticky-active { position: sticky; z-index: 999; transition: background 0.3s ease, box-shadow 0.3s ease; }</style>';
        }

        ?>
        <style>
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu a { display: block; text-decoration: none; transition: 0.3s ease; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu li { position: relative; }
            
            /* Layout Mode: Vertical */
            <?php if ( $layout === 'vertical' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu { flex-direction: column; width: 100%; }
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu li { width: 100%; }
            <?php endif; ?>

            /* Dropdowns (Desktop) */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu .sub-menu { position: absolute; top: 100%; left: 0; min-width: 200px; display: none; flex-direction: column; list-style: none; padding: 10px 0; margin: 0; z-index: 999; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu li:hover > .sub-menu { display: flex; }
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu .sub-menu a { padding: 10px 20px; font-size: 0.9em; }
            
            /* If vertical layout, push sub-menu to the right instead of bottom */
            <?php if ( $layout === 'vertical' ) : ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop .mh-menu .sub-menu { top: 0; left: 100%; }
            <?php else: ?>
                .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-menu .sub-menu .sub-menu { top: 0; left: 100%; }
            <?php endif; ?>

            /* Mobile Toggle */
            .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle { display: none; cursor: pointer; padding: 10px; border-radius: 4px; }
            
            /* Responsive Logic */
            <?php if ( $breakpoint !== 'none' ) : ?>
                @media (max-width: <?php echo esc_attr( $breakpoint ); ?>px) {
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-desktop { display: none !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-toggle { display: flex; align-items: center; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel { display: none; width: 100%; position: absolute; top: 100%; left: 0; z-index: 9999; background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu { flex-direction: column; width: 100%; gap: 0 !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu li { width: 100%; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu a { padding: 15px 20px !important; border-bottom: 1px solid #eee; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu { position: static; display: none; box-shadow: none; padding: 0; background: #f9f9f9; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-nav-mobile-panel .mh-menu .sub-menu a { padding-left: 40px !important; }
                    .mh-nav-wrapper-<?php echo esc_attr($widget_id); ?> .mh-mobile-caret { position: absolute; right: 0; top: 0; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-left: 1px solid #eee; z-index: 10; }
                }
            <?php endif; ?>
        </style>

        <div class="mh-nav-wrapper mh-nav-wrapper-<?php echo esc_attr( $widget_id ); ?>" 
             data-smooth="<?php echo $smooth_scroll ? 'yes' : 'no'; ?>" 
             data-speed="<?php echo esc_attr( $scroll_speed ); ?>">
            
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
                var $wrapper = $(wrapperId);
                var elementorWrapper = $wrapper.closest('.elementor-element-<?php echo esc_attr( $widget_id ); ?>');

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

                // 2. Sticky Header Logic (Background Color Trigger)
                <?php if ( $is_sticky ) : ?>
                    $(window).on('scroll', function() {
                        if ($(window).scrollTop() > 50) {
                            elementorWrapper.addClass('mh-is-sticky');
                        } else {
                            elementorWrapper.removeClass('mh-is-sticky');
                        }
                    });
                <?php endif; ?>

                // 3. Smooth Scroll Logic
                if ($wrapper.data('smooth') === 'yes') {
                    var speed = parseInt($wrapper.data('speed')) || 500;
                    $wrapper.find('a[href^="#"]').on('click', function(e) {
                        var target = $(this.getAttribute('href'));
                        if (target.length) {
                            e.preventDefault();
                            // Close mobile menu if open
                            $wrapper.find('.mh-nav-mobile-panel').slideUp(300);
                            $wrapper.find('.mh-nav-mobile-toggle i').removeClass('fa-times').addClass('fa-bars');
                            
                            $('html, body').stop().animate({
                                scrollTop: target.offset().top - 80 // 80px offset for headers
                            }, speed);
                        }
                    });
                }
            });
        </script>
        <?php
    }
}