<?php
// Exit if this file is called directly to prevent security vulnerabilities.
if (!defined('ABSPATH')) {
    exit;
}

// These 'use' statements import all necessary Elementor classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background; // <-- ADD THIS LINE
use Elementor\Repeater;

/**
 * MH Advanced Heading Widget Class
 * Final Corrected Version with Full Interactivity and Advanced Underline
 */
class MH_Heading_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-heading';
    }

    public function get_title() {
        return esc_html__('MH Heading', 'mh-plug');
    }

    public function get_icon() {
        return 'mhi-text';
    }

    public function get_categories() {
        return ['mh-plug-widgets'];
    }

    protected function _register_controls() {

        // --- Content Tab: Heading Parts (Repeater) ---
        $this->start_controls_section(
            'section_heading_parts',
            [
                'label' => esc_html__('Heading Parts', 'mh-plug'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        // Content Control inside Repeater
        $repeater->add_control(
            'part_text',
            [
                'label'   => esc_html__('Text', 'mh-plug'),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__('Text Part', 'mh-plug'),
                'label_block' => true,
                'dynamic' => ['active' => true],
            ]
        );

        // --- Start of Style Tab inside Repeater ---
        $repeater->add_control(
            'part_styles_heading',
            [
                'label' => esc_html__('Styling', 'mh-plug'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // Style Controls inside Repeater
        $repeater->add_control(
            'part_color',
            [
                'label'     => esc_html__('Text Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    // CORRECTED SELECTOR: {{CURRENT_ITEM}} makes it interactive.
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $repeater->add_control(
            'part_background_color',
            [
                'label'     => esc_html__('Background Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $repeater->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'part_typography',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'     => 'part_text_shadow',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
            ]
        );

        // Responsive Margin Control for each part
        $repeater->add_responsive_control(
            'part_margin', // Unique ID for the margin control within the repeater
            [
                'label'      => esc_html__('Margin', 'mh-plug'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    // This targets the specific span element for the current repeater item
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before', // Optional: Adds a line above this control in the panel
            ]
        );
        // --- End of Style Tab inside Repeater ---

        // Add the repeater control to the section
        $this->add_control(
            'heading_parts',
            [
                'label'   => esc_html__('Text Parts', 'mh-plug'),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    ['part_text' => esc_html__('Advanced', 'mh-plug')],
                    ['part_text' => esc_html__('Heading', 'mh-plug')],
                ],
                'title_field' => '{{{ part_text }}}',
            ]
        );
        
        $this->end_controls_section();

        // --- Content Tab: General Settings ---
        $this->start_controls_section(
            'section_general_settings',
            [
                'label' => esc_html__('General Settings', 'mh-plug'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'heading_alignment',
            [
                'label'   => esc_html__('Alignment', 'mh-plug'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [ 'left' => ['title' => esc_html__('Left', 'mh-plug'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'mh-plug'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'mh-plug'), 'icon' => 'eicon-text-align-right'], ],
                'default'   => 'left',
                'selectors' => [ '{{WRAPPER}}' => 'text-align: {{VALUE}};' ],
            ]
        );
        
        $this->add_control(
            'heading_html_tag',
            [ 'label' => esc_html__('HTML Tag', 'mh-plug'), 
            'type' => Controls_Manager::SELECT, 
            'options' => [ 'h1'=>'H1', 'h2'=>'H2', 'h3'=>'H3', 'h4'=>'H4', 'h5'=>'H5', 'h6'=>'H6', 'p'=>'P', 'div'=>'DIV' ], 'default' => 'h2' ]
        );

        $this->end_controls_section();

        // --- Style Tab: Underline (Completely Reworked) ---
        
// --- Style Tab: Underline (Corrected Logic) ---
        $this->start_controls_section(
            'section_underline_style',
            [ 'label' => esc_html__('Underline', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'underline_apply_to',
            [
                'label' => esc_html__('Apply Underline To', 'mh-plug'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All Parts', 'mh-plug'),
                    'last' => esc_html__('Last Part Only', 'mh-plug'),
                ],
                'default' => 'all',
                'condition' => [ 'underline_style!' => 'none' ],
            ]
        );

        $this->add_control(
            'underline_style',
            [
                'label' => esc_html__('Style', 'mh-plug'),
                'type' => Controls_Manager::SELECT,
                'options' => [ 
                    'none' => esc_html__('None', 'mh-plug'), 
                    'solid' => esc_html__('Solid', 'mh-plug'), 
                    'dotted' => esc_html__('Dotted', 'mh-plug'), 
                    'dashed' => esc_html__('Dashed', 'mh-plug'), 
                    'double' => esc_html__('Double', 'mh-plug'), 
                    'wavy' => esc_html__('Wavy (SVG)', 'mh-plug'), 
                    'jagged' => esc_html__('Jagged (SVG)', 'mh-plug'),
                ],
                'default' => 'none',
            ]
        );
        
        // Use a Group Control for Background for SVG-based styles that use a background image.
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'underline_background',
                'label' => esc_html__( 'Color', 'mh-plug' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .mh-underline-wrapper.mh-underline--solid::after', // Only for solid style now
                'condition' => [ 'underline_style' => 'solid' ],
            ]
        );

        // A single color picker for ALL other styles (dotted, dashed, double, wavy, jagged).
        $this->add_control(
            'underline_simple_color',
            [
                'label' => esc_html__('Color', 'mh-plug'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'condition' => [ 'underline_style' => ['dotted', 'dashed', 'double', 'wavy', 'jagged'] ],
            ]
        );

        $this->add_responsive_control( 'underline_width', [ 'label' => esc_html__('Width', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 0, 'max' => 200], 'px' => ['min' => 0, 'max' => 1000]], 'default' => ['unit' => '%', 'size' => 100], 'condition' => [ 'underline_style!' => 'none' ], 'selectors' => [ '{{WRAPPER}} .mh-underline-wrapper::after' => 'width: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'underline_height', [ 'label' => esc_html__('Height (Thickness)', 'mh-plug'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 50]], 'default' => ['unit' => 'px', 'size' => 3], 'condition' => [ 'underline_style!' => 'none' ], 'selectors' => [ '{{WRAPPER}} .mh-underline-wrapper::after' => '--underline-height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .mh-has-underline' => 'text-decoration-thickness: {{SIZE}}{{UNIT}};' ] ] );
        
        $this->add_control(
            'underline_position',
            [
                'label' => esc_html__('Position', 'mh-plug'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [ 'top' => [ 'title' => esc_html__('Top', 'mh-plug'), 'icon' => 'eicon-v-align-top' ], 'bottom' => [ 'title' => esc_html__('Bottom', 'mh-plug'), 'icon' => 'eicon-v-align-bottom' ], ],
                'default' => 'bottom', 'toggle' => false, 'condition' => [ 'underline_style!' => 'none' ],
            ]
        );

       $this->add_responsive_control( 
        'underline_y_offset', [ 'label' => esc_html__('Y Offset', 'mh-plug'), 
        'type' => Controls_Manager::SLIDER, 
        'size_units' => ['px'], 'range' => ['px' => ['min' => -100, 'max' => 100]],
        'default' => ['unit' => 'px', 'size' => 0], 
        'condition' => [ 'underline_style' => ['solid', 'wavy', 'jagged'] ], 
        'selectors' => [ '{{WRAPPER}} .mh-underline-wrapper::after' => '--underline-offset: {{SIZE}}{{UNIT}};' ] ] 
    );
        
        // A separate offset control for native text-decoration underlines.
        $this->add_responsive_control(
             'underline_native_y_offset', [ 'label' => esc_html__('Y Offset', 'mh-plug'), 
             'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 
             'range' => ['px' => ['min' => -50, 'max' => 50]], 'default' => ['unit' => 'px', 'size' => 0],
              'condition' => [ 'underline_style' => ['dotted', 'dashed', 'double'] ], 
              'selectors' => [ '{{WRAPPER}} .mh-has-underline' => 'text-underline-offset: {{SIZE}}{{UNIT}};' ] ] );

        $this->end_controls_section();




    }

    protected function render() {
    $settings = $this->get_settings_for_display();
    $tag = esc_attr($settings['heading_html_tag']);
    

    // Determine if the selected style uses native text-decoration or a pseudo-element
    $is_native_underline = in_array($settings['underline_style'], ['dotted', 'dashed', 'double']);
    $is_pseudo_underline = in_array($settings['underline_style'], ['solid', 'wavy', 'jagged']);

    // --- HTML Rendering ---
    $this->add_render_attribute('wrapper', 'class', 'mh-underline-wrapper');
    if ($is_pseudo_underline) {
        $this->add_render_attribute('wrapper', 'class', 'mh-underline--' . $settings['underline_style']);
        $this->add_render_attribute('wrapper', 'class', 'mh-underline-pos--' . $settings['underline_position']);
    }
    ?>
    <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
        <<?php echo $tag; ?> class="mh-advanced-heading-wrapper">
            <?php foreach ($settings['heading_parts'] as $item) :
                $part_classes = ['elementor-repeater-item-' . esc_attr($item['_id']), 'mh-heading-part'];
                if ($is_native_underline) {
                    $part_classes[] = 'mh-has-underline';
                }
            ?>
                <span class="<?php echo implode(' ', $part_classes); ?>">
                    <?php echo esc_html($item['part_text']); ?>
                </span>
            <?php endforeach; ?>
        </<?php echo $tag; ?>>
    </div>

    <?php // --- Inline CSS for Alignment and Underline --- ?>
    <style>
        .elementor-element-<?php echo $this->get_id(); ?> .mh-advanced-heading-wrapper {
            display: flex; flex-wrap: wrap; align-items: baseline;
        }
        .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-wrapper {
            position: relative; display: inline-block; line-height: 1;
        }
        
        <?php // --- NATIVE UNDERLINE STYLES (Dotted, Dashed, Double) ---
        if ($is_native_underline): ?>
        .elementor-element-<?php echo $this->get_id(); ?> .mh-has-underline {
            text-decoration-line: <?php echo $settings['underline_position'] === 'top' ? 'overline' : 'underline'; ?>;
            text-decoration-style: <?php echo esc_attr($settings['underline_style']); ?>;
            text-decoration-color: <?php echo esc_attr($settings['underline_simple_color']); ?>;
        }
        <?php endif; ?>

        <?php // --- PSEUDO-ELEMENT UNDERLINE STYLES (Solid, Wavy, Jagged) ---
        if ($is_pseudo_underline): ?>
        .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-wrapper::after {
            content: ''; position: absolute; left: 50%; transform: translateX(-50%);
            background-repeat: no-repeat; height: var(--underline-height, 3px);
        }
        .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-wrapper.mh-underline-pos--bottom::after {
            bottom: var(--underline-offset, -5px);
        }
        .elementor-element-<?php echo $this->get_id(); ?> .mh-underline-wrapper.mh-underline-pos--top::after {
            top: var(--underline-offset, -5px);
        }
        <?php // SVG styles
            $color = !empty($settings['underline_background_color']) ? $settings['underline_background_color'] : $settings['underline_simple_color'];
            if ($settings['underline_style'] === 'wavy') {
                $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 10' preserveAspectRatio='none'%3E%3Cpath d='M0,5 Q25,-1 50,5 T100,5' stroke='" . str_replace('#', '%23', $color) . "' stroke-width='2' fill='none'/%3E%3C/svg%3E";
                echo '.elementor-element-' . $this->get_id() . ' .mh-underline-wrapper.mh-underline--wavy::after { background-image: url("' . $svg . '"); background-size: cover; }';
            }
            if ($settings['underline_style'] === 'jagged') {
                 $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 10' preserveAspectRatio='none'%3E%3Cpolyline points='0,5 15,0 30,5 45,0 60,5 75,0 90,5' stroke='" . str_replace('#', '%23', $color) . "' stroke-width='2' fill='none'/%3E%3C/svg%3E";
                 echo '.elementor-element-' . $this->get_id() . ' .mh-underline-wrapper.mh-underline--jagged::after { background-image: url("' . $svg . '"); background-size: cover; }';
            }
        ?>
        <?php endif; ?>
    </style>
    <?php
}
}