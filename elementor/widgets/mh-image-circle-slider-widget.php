<?php
/**
 * MH Image Circle Slider Widget
 *
 * A slider of MH Image Circle items.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class MH_Image_Circle_Slider_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-image-circle-slider';
    }

    public function get_title() {
        return esc_html__('MH Image Circle Slider', 'mh-plug');
    }

    public function get_icon() {
        return 'mhi-border-image-slider'; 
    }

    public function get_categories() {
        return ['mh-plug-widgets']; 
    }

    protected function register_controls() {

        // --- Content Tab: Slides ---
        $this->start_controls_section(
            'section_content_slides',
            [
                'label' => esc_html__('Slides', 'mh-plug'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__('Choose Image', 'mh-plug'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'text',
            [
                'label' => esc_html__('Text', 'mh-plug'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Slide Title', 'mh-plug'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'mh-plug'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'mh-plug'),
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => esc_html__('Slides', 'mh-plug'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [ 'text' => esc_html__('Slide 1', 'mh-plug') ],
                    [ 'text' => esc_html__('Slide 2', 'mh-plug') ],
                    [ 'text' => esc_html__('Slide 3', 'mh-plug') ],
                ],
                'title_field' => '{{{ text }}}',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image_size',
                'default' => 'large',
                'separator' => 'before',
                'description' => esc_html__('Select the image size to load for all slides.', 'mh-plug'),
            ]
        );

        $this->end_controls_section();

        // --- Content Tab: Slider Settings ---
        $this->start_controls_section(
            'section_slider_settings',
            [
                'label' => esc_html__('Slider Settings', 'mh-plug'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => esc_html__( 'Slides to Show', 'mh-plug' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'default' => 3,
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
            ]
        );

        $this->add_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'mh-plug' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'default' => 1,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'mh-plug'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed (ms)', 'mh-plug'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [ 'autoplay' => 'yes' ],
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label' => esc_html__('Infinite Loop', 'mh-plug'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label' => esc_html__('Show Arrows', 'mh-plug'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'arrow_prev_icon',
            [
                'label' => esc_html__( 'Previous Arrow', 'mh-plug' ),
                'type' => Controls_Manager::ICONS,
                'default' => [ 'value' => 'fas fa-chevron-left', 'library' => 'solid' ],
                'condition' => [ 'show_arrows' => 'yes' ],
            ]
        );

        $this->add_control(
            'arrow_next_icon',
            [
                'label' => esc_html__( 'Next Arrow', 'mh-plug' ),
                'type' => Controls_Manager::ICONS,
                'default' => [ 'value' => 'fas fa-chevron-right', 'library' => 'solid' ],
                'condition' => [ 'show_arrows' => 'yes' ],
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => esc_html__('Show Dots', 'mh-plug'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // --- Style Tab: Image ---
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__('Image', 'mh-plug'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slide_image_size', // Changed ID to avoid conflict
            [
                'label' => esc_html__( 'Image Size (Diameter)', 'mh-plug' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => ['min' => 50, 'max' => 500] ],
                'default' => [ 'unit' => 'px', 'size' => 180 ],
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-image-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_gap',
            [
                'label' => esc_html__('Border Gap', 'mh-plug'),
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => ['min' => 0, 'max' => 50] ],
                'default' => [ 'unit' => 'px', 'size' => 10 ],
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-image-wrapper' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_spacing',
            [
                'label' => esc_html__('Spacing Below Image', 'mh-plug'),
                'type' => Controls_Manager::SLIDER,
                'default' => [ 'unit' => 'px', 'size' => 20 ],
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-image-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Border Style Tabs
        $this->start_controls_tabs( 'tabs_image_style' );
        $this->start_controls_tab( 'tab_image_normal', [ 'label' => esc_html__( 'Normal', 'mh-plug' ) ] );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .mh-image-circle-border',
                'fields_options' => [
                    'border' => [ 'default' => 'dotted' ],
                    'width' => [ 'default' => [ 'unit' => 'px', 'size' => 2 ] ],
                    'color' => [ 'default' => '#DDDDDD' ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_image_hover', [ 'label' => esc_html__( 'Hover', 'mh-plug' ) ] );

        $this->add_control(
            'image_border_color_hover',
            [
                'label' => esc_html__('Border Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-slide-item:hover .mh-image-circle-border' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation_spin',
            [
                'label' => esc_html__('Spin Border on Hover', 'mh-plug'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'hover_transition',
            [
                'label' => esc_html__( 'Transition Duration (s)', 'mh-plug' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => [ 'min' => 0.1, 'max' => 3, 'step' => 0.1 ] ],
                'default' => [ 'unit' => 'px', 'size' => 10 ],
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-border' => 'animation-duration: {{SIZE}}s;',
                ],
                'condition' => [ 'hover_animation_spin' => 'yes' ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // --- Style Tab: Text ---
        $this->start_controls_section(
            'section_style_text',
            [
                'label' => esc_html__('Text', 'mh-plug'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'mh-plug' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [ 'title' => esc_html__( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                    'center' => [ 'title' => esc_html__( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                    'right' => [ 'title' => esc_html__( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-wrapper' => 'text-align: {{VALUE}}; align-items: {{VALUE}} == "left" ? flex-start : ({{VALUE}} == "right" ? flex-end : center);',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_text_style' );
        $this->start_controls_tab( 'tab_text_normal', [ 'label' => esc_html__( 'Normal', 'mh-plug' ) ] );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [ '{{WRAPPER}} .mh-image-circle-text' => 'color: {{VALUE}};' ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_text_hover', [ 'label' => esc_html__( 'Hover', 'mh-plug' ) ] );

        $this->add_control(
            'text_color_hover',
            [
                'label' => esc_html__('Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-slide-item:hover .mh-image-circle-text' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'text_typography', 'selector' => '{{WRAPPER}} .mh-image-circle-text', 'separator' => 'before' ] );
        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [ 'name' => 'text_shadow', 'selector' => '{{WRAPPER}} .mh-image-circle-text' ] );

        $this->end_controls_section();

        // --- Style Tab: Navigation ---
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => esc_html__('Navigation', 'mh-plug'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Copying Navigation Styles from Brush Slider
        $this->add_control( 'heading_style_arrows', [ 'label' => esc_html__( 'Arrows', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->add_responsive_control( 'arrow_size', [ 'label' => esc_html__( 'Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 10, 'max' => 100 ] ], 'default' => [ 'unit' => 'px', 'size' => 24 ], 'selectors' => [ '{{WRAPPER}} .mh-brush-slider-arrow' => 'font-size: {{SIZE}}{{UNIT}};' ], 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->start_controls_tabs( 'tabs_arrow_style' );
        $this->start_controls_tab( 'tab_arrow_normal', [ 'label' => esc_html__( 'Normal', 'mh-plug' ), 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->add_control( 'arrow_color_normal', [ 'label' => esc_html__('Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#000000', 'selectors' => [ '{{WRAPPER}} .mh-brush-slider-arrow' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_arrow_hover', [ 'label' => esc_html__( 'Hover', 'mh-plug' ), 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->add_control( 'arrow_color_hover', [ 'label' => esc_html__('Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-brush-slider-arrow:hover' => 'color: {{VALUE}};' ] ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'heading_style_dots', [ 'label' => esc_html__( 'Dots', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'condition' => [ 'show_dots' => 'yes' ], 'separator' => 'before' ] );
        $this->add_responsive_control( 'dots_size', [ 'label' => esc_html__( 'Size', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 5, 'max' => 20 ] ], 'default' => [ 'unit' => 'px', 'size' => 12 ], 'selectors' => [ '{{WRAPPER}} .mh-brush-slider-wrapper .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-brush-slider-wrapper .slick-dots li' => 'margin: 0 calc({{SIZE}}{{UNIT}} / 3);' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        
        $this->add_control( 'dots_color_normal', [ 'label' => esc_html__('Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#cccccc', 'selectors' => [ '{{WRAPPER}} .mh-brush-slider-wrapper .slick-dots li button' => 'border-color: {{VALUE}}; background-color: {{VALUE}};' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->add_control( 'dots_color_active', [ 'label' => esc_html__('Active Color', 'mh-plug'), 'type' => Controls_Manager::COLOR, 'default' => '#004265', 'selectors' => [ '{{WRAPPER}} .mh-brush-slider-wrapper .slick-dots li.slick-active button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ], 'condition' => [ 'show_dots' => 'yes' ] ] );
        
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        if ( empty( $settings['slides'] ) ) {
            return;
        }

        // --- SLICKSLIDER OPTIONS ---
        $slick_options = [
            'slidesToShow'   => (int) $settings['slides_to_show'],
            'slidesToScroll' => (int) $settings['slides_to_scroll'],
            'autoplay'       => ($settings['autoplay'] === 'yes'),
            'autoplaySpeed'  => (int) $settings['autoplay_speed'],
            'arrows'         => ($settings['show_arrows'] === 'yes'),
            'dots'           => ($settings['show_dots'] === 'yes'),
            'infinite'       => ($settings['infinite_loop'] === 'yes'),
        ];

        // Responsive
        if ( ! empty( $settings['slides_to_show_tablet'] ) || ! empty( $settings['slides_to_show_mobile'] ) ) {
            $slick_options['responsive'] = [];
            if ( ! empty( $settings['slides_to_show_tablet'] ) ) {
                $slick_options['responsive'][] = [ 'breakpoint' => 1024, 'settings' => [ 'slidesToShow' => (int) $settings['slides_to_show_tablet'] ] ];
            }
            if ( ! empty( $settings['slides_to_show_mobile'] ) ) {
                $slick_options['responsive'][] = [ 'breakpoint' => 767, 'settings' => [ 'slidesToShow' => (int) $settings['slides_to_show_mobile'] ] ];
            }
        }

        if ($settings['show_arrows'] === 'yes') {
            $slick_options['prevArrow'] = '.mh-brush-slider-prev-' . $widget_id;
            $slick_options['nextArrow'] = '.mh-brush-slider-next-' . $widget_id;
        }
        
        // Re-using the brush slider wrapper class to inherit the arrow/dot styles
        $this->add_render_attribute( 'slider-wrapper', 'class', 'mh-brush-slider-wrapper' );
        $this->add_render_attribute( 'slider', [
            'class' => 'mh-brush-slider',
            'data-slick' => json_encode( $slick_options ),
        ] );

        // Border Class for Animation
        $border_class = 'mh-image-circle-border';
        if ( $settings['hover_animation_spin'] === 'yes' ) {
            $border_class .= ' mh-spin-on-hover';
        }

        ?>
        <div <?php echo $this->get_render_attribute_string( 'slider-wrapper' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'slider' ); ?>>
                <?php foreach ( $settings['slides'] as $index => $slide ) : ?>
                    <div class="mh-brush-slide-item mh-image-circle-slide-item">
                        <?php
                        $wrapper_tag = 'div';
                        $link_key = 'link_' . $index;
                        
                        if ( ! empty( $slide['link']['url'] ) ) {
                            $wrapper_tag = 'a';
                            $this->add_link_attributes( $link_key, $slide['link'] );
                        }

                        // Get Image URL
                        $image_url = $slide['image']['url'];
                        if ( ! empty( $image_url ) && isset( $slide['image']['id'] ) ) {
                             $image_data = wp_get_attachment_image_src( $slide['image']['id'], $settings['image_size_size'] );
                             if ( $image_data ) { $image_url = $image_data[0]; }
                        }
                        $image_style = !empty($image_url) ? 'background-image: url(' . esc_url( $image_url ) . ');' : '';
                        ?>

                        <div class="mh-image-circle-wrapper">
                            <<?php echo $wrapper_tag; ?> <?php echo $this->get_render_attribute_string( $link_key ); ?>>
                                <div class="mh-image-circle-image-wrapper">
                                    <div class="<?php echo esc_attr($border_class); ?>"></div>
                                    <div class="mh-image-circle-inner" style="<?php echo esc_attr($image_style); ?>"></div>
                                </div>
                                <?php if ( $slide['text'] ) : ?>
                                    <div class="mh-image-circle-text"><?php echo esc_html( $slide['text'] ); ?></div>
                                <?php endif; ?>
                            </<?php echo $wrapper_tag; ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($settings['show_arrows'] === 'yes') : ?>
                <button class="mh-brush-slider-arrow mh-brush-slider-prev-<?php echo esc_attr($widget_id); ?>">
                    <?php Icons_Manager::render_icon( $settings['arrow_prev_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </button>
                <button class="mh-brush-slider-arrow mh-brush-slider-next-<?php echo esc_attr($widget_id); ?>">
                    <?php Icons_Manager::render_icon( $settings['arrow_next_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                </button>
            <?php endif; ?>
            
            <script>
            jQuery(document).ready(function($) {
                var $slider = $('.elementor-element-<?php echo esc_attr($widget_id); ?> .mh-brush-slider');
                if ($slider.length && typeof $.fn.slick === 'function') {
                    $slider.slick($slider.data('slick'));
                }
            });
            </script>
        </div>
        <?php
    }

    protected function _content_template() {
        ?>
        <#
        var borderClass = 'mh-image-circle-border';
        if ( settings.hover_animation_spin === 'yes' ) {
            borderClass += ' mh-spin-on-hover';
        }
        #>
        <div class="mh-brush-slider-wrapper">
            <div class="mh-brush-slider-preview-editor">
                <# if ( settings.slides ) {
                    _.each( settings.slides, function( slide ) {
                        var imageUrl = slide.image.url; 
                        // Note: getting correct size in JS loop is complex, fallback to url
                        var imageStyle = imageUrl ? 'background-image: url(' + imageUrl + ');' : '';
                        var link_url = slide.link.url;
                        var linkTag = link_url ? 'a' : 'div';
                        var linkAttrs = link_url ? 'href="' + link_url + '"' : '';
                #>
                    <div class="mh-brush-slide-item mh-image-circle-slide-item">
                         <div class="mh-image-circle-wrapper">
                            <{{{ linkTag }}} {{{ linkAttrs }}}>
                                <div class="mh-image-circle-image-wrapper">
                                    <div class="{{{ borderClass }}}"></div>
                                    <div class="mh-image-circle-inner" style="{{{ imageStyle }}}"></div>
                                </div>
                                <# if ( slide.text ) { #>
                                    <div class="mh-image-circle-text">{{{ slide.text }}}</div>
                                <# } #>
                            </{{{ linkTag }}}>
                        </div>
                    </div>
                <# }); } #>
            </div>
        </div>
        <?php
    }
}