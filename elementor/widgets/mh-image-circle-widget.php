<?php
/**
 * MH Image Circle Widget
 *
 * A widget that displays an image in a circle with text below it.
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

class MH_Image_Circle_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-image-circle';
    }

    public function get_title() {
        return esc_html__('MH Image Circle', 'mh-plug');
    }

    public function get_icon() {
        return 'mhi-border-image'; 
    }

    public function get_categories() {
        return ['mh-plug-widgets']; 
    }

    protected function register_controls() {

        // --- Content Tab ---
        $this->start_controls_section(
            'section_content_image_text',
            [
                'label' => esc_html__('Image & Text', 'mh-plug'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__('Choose Image', 'mh-plug'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image_size',
                'default' => 'large',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => esc_html__('Text', 'mh-plug'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Melody Mates', 'mh-plug'),
                'dynamic' => ['active' => true],
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'mh-plug'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'mh-plug'),
                'dynamic' => ['active' => true],
            ]
        );
        
        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'mh-plug' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'mh-plug' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'mh-plug' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'mh-plug' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                // This automatically generates classes like: 
                // .mh-align-left, .mh-align-tablet-center, .mh-align-mobile-right
                'prefix_class' => 'mh-align-', 
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
            'image_size_control',
            [
                'label' => esc_html__( 'Image Size (Diameter)', 'mh-plug' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 50, 'max' => 500],
                ],
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
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
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

        $this->start_controls_tabs( 'tabs_image_style' );

        // Normal Tab
        $this->start_controls_tab(
            'tab_image_normal',
            [ 'label' => esc_html__( 'Normal', 'mh-plug' ) ]
        );

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

        $this->add_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'mh-plug'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [ 'unit' => '%', 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'isLinked' => true ],
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-border' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .mh-image-circle-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'tab_image_hover',
            [ 'label' => esc_html__( 'Hover', 'mh-plug' ) ]
        );

        $this->add_control(
            'image_border_color_hover',
            [
                'label' => esc_html__('Border Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-wrapper:hover .mh-image-circle-border' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation_spin',
            [
                'label' => esc_html__('Spin Border on Hover', 'mh-plug'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'mh-plug'),
                'label_off' => esc_html__('No', 'mh-plug'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'hover_transition',
            [
                'label' => esc_html__( 'Transition Duration (s)', 'mh-plug' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => [ 'min' => 0.1, 'max' => 10, 'step' => 0.1 ] ],
                'default' => [ 'unit' => 'px', 'size' => 10 ], // Actually seconds, but using slider
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

        $this->start_controls_tabs( 'tabs_text_style' );

        // --- Normal Tab ---
        $this->start_controls_tab(
            'tab_text_normal',
            [ 'label' => esc_html__( 'Normal', 'mh-plug' ) ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // --- Hover Tab ---
        $this->start_controls_tab(
            'tab_text_hover',
            [ 'label' => esc_html__( 'Hover', 'mh-plug' ) ]
        );

        $this->add_control(
            'text_color_hover',
            [
                'label' => esc_html__('Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-wrapper:hover .mh-image-circle-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .mh-image-circle-text',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .mh-image-circle-text',
            ]
        );

        // --- NEW: Margin Control ---
        $this->add_responsive_control(
            'text_margin',
            [
                'label' => esc_html__( 'Margin', 'mh-plug' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .mh-image-circle-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 20,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('wrapper', 'class', 'mh-image-circle-wrapper');
        
        // Border Classes
        $this->add_render_attribute('border_div', 'class', 'mh-image-circle-border');
        if ( $settings['hover_animation_spin'] === 'yes' ) {
            $this->add_render_attribute('border_div', 'class', 'mh-spin-on-hover');
        }

        // Link
        if ( ! empty( $settings['link']['url'] ) ) {
            $this->add_link_attributes('link', $settings['link']);
            $link_tag = 'a';
        } else {
            $link_tag = 'div';
        }

        // Image URL
        $image_url = $settings['image']['url'];
        $image_style = '';
        if ( ! empty( $image_url ) ) {
            if ( isset( $settings['image']['id'] ) && $settings['image']['id'] ) {
                $image_data = wp_get_attachment_image_src( $settings['image']['id'], $settings['image_size_size'] );
                if ( $image_data ) {
                    $image_url = $image_data[0];
                }
            }
            $image_style = 'background-image: url(' . esc_url( $image_url ) . ');';
        }

        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
            <<?php echo $link_tag; ?> <?php echo $this->get_render_attribute_string('link'); ?>>
                
                <div class="mh-image-circle-image-wrapper">
                    <div <?php echo $this->get_render_attribute_string('border_div'); ?>></div>
                    
                    <div class="mh-image-circle-inner" style="<?php echo esc_attr($image_style); ?>"></div>
                </div>
                
                <?php if ( $settings['text'] ) : ?>
                    <div class="mh-image-circle-text">
                        <?php echo esc_html( $settings['text'] ); ?>
                    </div>
                <?php endif; ?>
            </<?php echo $link_tag; ?>>
        </div>
        <?php
    }

    protected function _content_template() {
        ?>
        <#
        var image = {
            id: settings.image.id,
            url: settings.image.url,
            size: settings.image_size_size,
            dimension: settings.image_size_custom_dimension,
            model: view.getEditModel()
        };
        var imageUrl = elementor.imagesManager.getImageUrl( image );
        var imageStyle = imageUrl ? 'background-image: url(' + imageUrl + ');' : '';

        var link_url = settings.link.url;
        var linkTag = link_url ? 'a' : 'div';
        var linkAttrs = link_url ? 'href="' + link_url + '"' : '';

        var borderClass = 'mh-image-circle-border';
        if ( settings.hover_animation_spin === 'yes' ) {
            borderClass += ' mh-spin-on-hover';
        }
        #>
        <div class="mh-image-circle-wrapper">
            <{{{ linkTag }}} {{{ linkAttrs }}}>
                <div class="mh-image-circle-image-wrapper">
                    <div class="{{{ borderClass }}}"></div>
                    <div class="mh-image-circle-inner" style="{{{ imageStyle }}}"></div>
                </div>
                <# if ( settings.text ) { #>
                    <div class="mh-image-circle-text">{{{ settings.text }}}</div>
                <# } #>
            </{{{ linkTag }}}>
        </div>
        <?php
    }
}