<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

class MH_Synced_Slider_Widget extends Widget_Base {

    public function get_name() { return 'mh_synced_slider'; }
    public function get_title() { return __( 'MH Synced Slider', 'mhds-plug' ); }
    public function get_icon() { return 'eicon-slides'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    
    // 🚀 FIX 1: Corrected the script dependency to match your elementor-loader.php
    public function get_script_depends() { return [ 'slick-js' ]; } 

    protected function register_controls() {
        
        // ==========================================
        // CONTENT TAB
        // ==========================================
        $this->start_controls_section('content_section', ['label' => __( 'Slides', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_CONTENT]);

        $repeater = new \Elementor\Repeater();
        $repeater->add_control('image', ['label' => __( 'Image', 'mhds-plug' ), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => Utils::get_placeholder_image_src()]]);
        $repeater->add_control('meta', ['label' => __( 'Top Label', 'mhds-plug' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'SEASONAL SALE', 'mhds-plug' )]);
        $repeater->add_control('heading', ['label' => __( 'Heading', 'mhds-plug' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'Digital<br>Dreams', 'mhds-plug' )]);
        $repeater->add_control('price', ['label' => __( 'Price', 'mhds-plug' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Price from : $850', 'mhds-plug' )]);
        $repeater->add_control('button_text', ['label' => __( 'Button Text', 'mhds-plug' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Shop now', 'mhds-plug' )]);
        $repeater->add_control('button_link', ['label' => __( 'Link', 'mhds-plug' ), 'type' => Controls_Manager::URL, 'placeholder' => __( 'https://your-link.com', 'mhds-plug' )]);

        $this->add_control('slides', [
            'label' => __( 'Slides', 'mhds-plug' ),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [ 'heading' => 'Digital<br>Dreams', 'meta' => 'SEASONAL SALE', 'price' => 'Price from : $850' ],
                [ 'heading' => 'Bold<br>Impact', 'meta' => 'NEW ARRIVAL', 'price' => 'Price from : $420' ],
                [ 'heading' => 'Clear<br>Vision', 'meta' => 'LIMITED EDITION', 'price' => 'Price from : $999' ],
            ],
            'title_field' => '{{{ heading }}}',
        ]);
        $this->end_controls_section();

        // SLIDER SETTINGS
        $this->start_controls_section('slider_settings_section', ['label' => __( 'Slider Settings', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('autoplay', ['label' => __('Autoplay', 'mhds-plug'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('autoplay_speed', ['label' => __('Autoplay Speed (ms)', 'mhds-plug'), 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'condition' => ['autoplay' => 'yes']]);
        $this->add_control('transition_speed', ['label' => __('Transition Speed (ms)', 'mhds-plug'), 'type' => Controls_Manager::NUMBER, 'default' => 500]);
        $this->end_controls_section();

        // ==========================================
        // STYLE TAB
        // ==========================================
        
        // 1. CONTAINER STYLE
        $this->start_controls_section('style_container_section', ['label' => __( 'Container', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'container_bg', 'selector' => '{{WRAPPER}} .mh-synced-container']);
        $this->add_responsive_control('container_padding', ['label' => __('Padding', 'mhds-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .mh-synced-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('container_radius', ['label' => __('Border Radius', 'mhds-plug'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .mh-synced-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'container_border', 'selector' => '{{WRAPPER}} .mh-synced-container']);
        $this->end_controls_section();

        // 2. TEXT CONTENT STYLE
        $this->start_controls_section('style_text_section', ['label' => __( 'Text Content', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_STYLE]);
        
        // Meta
        $this->add_control('heading_meta', ['label' => __('Top Label (Meta)', 'mhds-plug'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('meta_color', ['label' => __('Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-text-content .mh-meta' => 'color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'meta_typo', 'selector' => '{{WRAPPER}} .mh-text-content .mh-meta']);
        $this->add_responsive_control('meta_margin', ['label' => __('Margin Bottom', 'mhds-plug'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-text-content .mh-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);

        // Heading
        $this->add_control('heading_heading', ['label' => __('Main Heading', 'mhds-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('heading_color', ['label' => __('Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-text-content h2' => 'color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'heading_typo', 'selector' => '{{WRAPPER}} .mh-text-content h2']);
        $this->add_responsive_control('heading_margin', ['label' => __('Margin Bottom', 'mhds-plug'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-text-content h2' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);

        // Price
        $this->add_control('heading_price', ['label' => __('Price', 'mhds-plug'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('price_color', ['label' => __('Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-text-content .mh-price' => 'color: {{VALUE}} !important;']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'price_typo', 'selector' => '{{WRAPPER}} .mh-text-content .mh-price']);
        $this->add_responsive_control('price_margin', ['label' => __('Margin Bottom', 'mhds-plug'), 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .mh-text-content .mh-price' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();

        // 3. BUTTON STYLE
        $this->start_controls_section('style_button_section', ['label' => __( 'Button', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'btn_typo', 'selector' => '{{WRAPPER}} .mh-shop-btn']);
        $this->add_responsive_control('btn_padding', ['label' => __('Padding', 'mhds-plug'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .mh-shop-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('btn_radius', ['label' => __('Border Radius', 'mhds-plug'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .mh-shop-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        
        $this->start_controls_tabs('tabs_btn_style');
        // Normal
        $this->start_controls_tab('tab_btn_normal', ['label' => __('Normal', 'mhds-plug')]);
        $this->add_control('btn_color', ['label' => __('Text Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-shop-btn' => 'color: {{VALUE}};']]);
        $this->add_control('btn_bg', ['label' => __('Background Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-shop-btn' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_border', 'selector' => '{{WRAPPER}} .mh-shop-btn']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'btn_shadow', 'selector' => '{{WRAPPER}} .mh-shop-btn']);
        $this->end_controls_tab();
        // Hover
        $this->start_controls_tab('tab_btn_hover', ['label' => __('Hover', 'mhds-plug')]);
        $this->add_control('btn_hover_color', ['label' => __('Text Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-shop-btn:hover' => 'color: {{VALUE}};']]);
        $this->add_control('btn_hover_bg', ['label' => __('Background Color', 'mhds-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .mh-shop-btn:hover' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'btn_hover_border', 'selector' => '{{WRAPPER}} .mh-shop-btn:hover']);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'btn_hover_shadow', 'selector' => '{{WRAPPER}} .mh-shop-btn:hover']);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        // 4. IMAGE STYLE
        $this->start_controls_section('style_image_section', ['label' => __( 'Images', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('image_height', [
            'label' => __('Image Height', 'mhds-plug'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 100, 'max' => 800]],
            'selectors' => ['{{WRAPPER}} .mh-image-card' => 'height: {{SIZE}}{{UNIT}};']
        ]);
        $this->add_responsive_control('image_radius', [
            'label' => __('Border Radius', 'mhds-plug'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => ['{{WRAPPER}} .mh-image-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
        ]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_shadow', 'selector' => '{{WRAPPER}} .mh-image-card']);
        $this->end_controls_section();

        // 5. ANIMATIONS
        $this->start_controls_section('style_animation_section', ['label' => __( 'Entrance Animations', 'mhds-plug' ), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('anim_duration', [
            'label' => __('Animation Duration (s)', 'mhds-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.5],
            'range' => ['px' => ['max' => 2, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .slick-active .mh-text-content .mh-meta, {{WRAPPER}} .slick-active .mh-text-content h2, {{WRAPPER}} .slick-active .mh-text-content .mh-price, {{WRAPPER}} .slick-active .mh-text-content .mh-shop-btn' => 'animation-duration: {{SIZE}}s;']
        ]);
        $this->add_control('delay_meta', [
            'label' => __('Top Label Delay (s)', 'mhds-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.1],
            'range' => ['px' => ['max' => 2, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .slick-active .mh-text-content .mh-meta' => 'animation-delay: {{SIZE}}s;']
        ]);
        $this->add_control('delay_heading', [
            'label' => __('Heading Delay (s)', 'mhds-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.2],
            'range' => ['px' => ['max' => 2, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .slick-active .mh-text-content h2' => 'animation-delay: {{SIZE}}s;']
        ]);
        $this->add_control('delay_price', [
            'label' => __('Price Delay (s)', 'mhds-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.3],
            'range' => ['px' => ['max' => 2, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .slick-active .mh-text-content .mh-price' => 'animation-delay: {{SIZE}}s;']
        ]);
        $this->add_control('delay_button', [
            'label' => __('Button Delay (s)', 'mhds-plug'),
            'type' => Controls_Manager::SLIDER,
            'default' => ['size' => 0.4],
            'range' => ['px' => ['max' => 2, 'step' => 0.1]],
            'selectors' => ['{{WRAPPER}} .slick-active .mh-text-content .mh-shop-btn' => 'animation-delay: {{SIZE}}s;']
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id       = $this->get_id();

        if ( empty( $settings['slides'] ) ) return;

        // 🚀 FIX 2: Bulletproof Fallbacks to prevent Syntax Errors in JavaScript
        $t_speed = !empty($settings['transition_speed']) ? absint($settings['transition_speed']) : 500;
        $a_speed = !empty($settings['autoplay_speed']) ? absint($settings['autoplay_speed']) : 3000;
        $auto    = (!empty($settings['autoplay']) && $settings['autoplay'] === 'yes') ? 'true' : 'false';
        ?>

        <style>
            /* The Unbreakable Foundation CSS */
            .mh-synced-container {
                display: flex; width: 100%; max-width: 1200px; margin: 0 auto;
                padding: 20px; align-items: center; gap: 60px; background-color: #1a202c; overflow: hidden;
            }
            .mh-left-col { width: 35%; padding: 20px; cursor: grab; }
            .mh-left-col:active { cursor: grabbing; }
            .mh-right-col { width: 65%; position: relative; }

            @keyframes slideUpFade {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .mh-text-slider .slick-slide { display: block; min-height: 200px; outline: none; padding: 10px; }
            .mh-text-content {
                background-color: transparent; color: white; padding: 20px 0; border-radius: 0; box-shadow: none;              
                display: flex; flex-direction: column; justify-content: center; align-items: flex-start; min-height: 350px;
            }
            
            .mh-text-content h2, .mh-text-content p, .mh-text-content .mh-meta, .mh-text-content .mh-price, .mh-text-content .mh-shop-btn { opacity: 0; }
            .slick-active .mh-text-content .mh-meta { animation: slideUpFade 0.5s ease forwards 0.1s; }
            .slick-active .mh-text-content h2 { animation: slideUpFade 0.5s ease forwards 0.2s; }
            .slick-active .mh-text-content .mh-price { animation: slideUpFade 0.5s ease forwards 0.3s; }
            .slick-active .mh-text-content .mh-shop-btn { animation: slideUpFade 0.5s ease forwards 0.4s; }
            
            .mh-text-content .mh-meta { display: block; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; color: #94a3b8; font-weight: 600; }
            .mh-text-content h2 { font-size: 3.5rem; font-weight: 800; margin: 0 0 1rem 0; line-height: 1.1; color: #ffffff; }
            .mh-text-content .mh-price { font-size: 1.25rem; color: #e2e8f0; margin-bottom: 30px; font-weight: 500; }
            .mh-shop-btn {
                background-color: #ff8787; color: white; border: none; padding: 14px 35px; border-radius: 50px; font-size: 1rem;
                font-weight: 700; cursor: pointer; transition: all 0.3s ease; display: inline-block; box-shadow: 0 4px 15px rgba(255, 135, 135, 0.4); text-decoration: none;
            }
            .mh-shop-btn:hover { background-color: #ff6b6b; transform: translateY(-3px); box-shadow: 0 6px 20px rgba(255, 135, 135, 0.6); color: white; }

            .mh-image-slider .slick-slide { z-index: 0; position: relative; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); }
            .mh-image-slider .slick-center { z-index: 20 !important; }

            .mh-image-card {
                border-radius: 15px; width: 150%; margin-left: -25%; height: 400px; overflow: hidden; display: flex !important;
                justify-content: center; align-items: center; transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                opacity: 0; pointer-events: none; transform: scale(0.7) translateX(-50px);
            }
            
            .mh-image-slider .slick-slide.slide-prev .mh-image-card { opacity: 0.6; pointer-events: auto; transform: translateX(40%) scale(0.85); z-index: 10; }
            .mh-image-slider .slick-slide.slide-next .mh-image-card { opacity: 0.6; pointer-events: auto; transform: translateX(-40%) scale(0.85); z-index: 10; }
            .mh-image-slider .slick-center .mh-image-card { opacity: 1; transform: scale(1.1); z-index: 20; }
            .mh-image-card img { width: 100%; height: 100%; object-fit: cover; display: block; }

            @media (max-width: 900px) {
                .mh-synced-container { flex-direction: column-reverse; padding: 10px; gap: 20px; }
                .mh-left-col, .mh-right-col { width: 100%; }
                .mh-text-slider { text-align: center; }
                .mh-text-content { align-items: center; text-align: center; } 
                .mh-text-content h2 { font-size: 2.5rem; }
                .mh-image-card { height: 250px; width: 120%; margin-left: -10%;}
                .mh-image-slider .slick-center .mh-image-card { transform: scale(1.05); }
                .mh-image-slider .slick-slide.slide-prev .mh-image-card { transform: translateX(10%) scale(0.9); }
                .mh-image-slider .slick-slide.slide-next .mh-image-card { transform: translateX(-10%) scale(0.9); }
            }
        </style>

        <div class="mh-synced-container">
            <div class="mh-left-col">
                <div class="mh-text-slider" id="text-<?php echo esc_attr( $id ); ?>">
                    <?php foreach ( $settings['slides'] as $slide ) : ?>
                        <div>
                            <div class="mh-text-content">
                                <?php if ( $slide['meta'] ) : ?>
                                    <span class="mh-meta"><?php echo esc_html( $slide['meta'] ); ?></span>
                                <?php endif; ?>
                                
                                <?php if ( $slide['heading'] ) : ?>
                                    <h2 class="mh-heading"><?php echo wp_kses_post( $slide['heading'] ); ?></h2>
                                <?php endif; ?>
                                
                                <?php if ( $slide['price'] ) : ?>
                                    <div class="mh-price"><?php echo esc_html( $slide['price'] ); ?></div>
                                <?php endif; ?>

                                <?php if ( $slide['button_text'] ) : 
                                    $link_url = !empty($slide['button_link']['url']) ? $slide['button_link']['url'] : '#';
                                    $link_attrs = '';
                                    if ( ! empty( $slide['button_link']['url'] ) ) {
                                        $this->add_link_attributes( 'btn_' . $slide['_id'], $slide['button_link'] );
                                        $link_attrs = $this->get_render_attribute_string( 'btn_' . $slide['_id'] );
                                    }
                                ?>
                                    <a href="<?php echo esc_url( $link_url ); ?>" class="mh-shop-btn" <?php echo $link_attrs; ?>>
                                        <?php echo esc_html( $slide['button_text'] ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mh-right-col">
                <div class="mh-image-slider" id="image-<?php echo esc_attr( $id ); ?>">
                    <?php foreach ( $settings['slides'] as $slide ) : ?>
                        <div>
                            <div class="mh-image-card">
                                <?php if ( ! empty( $slide['image']['url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $slide['image']['url'] ); ?>" alt="Slider Image">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var textSlider = $('#text-<?php echo esc_attr( $id ); ?>');
            var imageSlider = $('#image-<?php echo esc_attr( $id ); ?>');

            // 🚀 FIX 3: Teardown logic! Prevents the Elementor Editor from duplicating/crashing Slick when you change settings
            if (textSlider.hasClass('slick-initialized')) { textSlider.slick('unslick'); }
            if (imageSlider.hasClass('slick-initialized')) { imageSlider.slick('unslick'); }

            textSlider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: false,
                asNavFor: '#image-<?php echo esc_attr( $id ); ?>',
                draggable: true, 
                swipe: true,
                speed: <?php echo $t_speed; ?>,           
                touchThreshold: 10
            });

            var $imgSlider = imageSlider.slick({
                centerMode: true,
                centerPadding: '0px',
                slidesToShow: 3,
                infinite: true,
                autoplay: <?php echo $auto; ?>,
                autoplaySpeed: <?php echo $a_speed; ?>,
                speed: <?php echo $t_speed; ?>,        
                arrows: false,
                asNavFor: '#text-<?php echo esc_attr( $id ); ?>',
                focusOnSelect: true,
                responsive: [
                    { breakpoint: 900, settings: { slidesToShow: 3, centerPadding: '20px' } },
                    { breakpoint: 600, settings: { slidesToShow: 3, centerPadding: '10px' } }
                ]
            });

            function updateOverlapClasses() {
                imageSlider.find('.slick-slide').removeClass('slide-prev slide-next');
                var $center = imageSlider.find('.slick-center');
                $center.prev().addClass('slide-prev');
                $center.next().addClass('slide-next');
            }

            $imgSlider.on('init afterChange setPosition', function() { updateOverlapClasses(); });
            updateOverlapClasses();
        });
        </script>
        <?php
    }
}