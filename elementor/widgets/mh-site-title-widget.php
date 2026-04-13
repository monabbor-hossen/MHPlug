<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Import necessary Elementor classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

/**
 * MH Site Title Widget Class
 */
class MH_Site_Title_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-site-title';
    }

    public function get_title() {
        return esc_html__('MH Site Title', 'mh-plug');
    }

    public function get_icon() {
        // Use an appropriate icon from your custom library or Elementor's library
        return 'mhi-site-title'; // Example using your custom icon
        // return 'eicon-site-title'; // Alternative Elementor icon
    }

    public function get_categories() {
        return ['mh-plug-widgets']; // Your custom category
    }

    protected function _register_controls() {

        // --- Content Tab ---
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Content', 'mh-plug'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label' => esc_html__( 'Link', 'mh-plug' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'home',
                'options' => [
                    'none' => esc_html__( 'None', 'mh-plug' ),
                    'home' => esc_html__( 'Home URL', 'mh-plug' ),
                    'custom' => esc_html__( 'Custom URL', 'mh-plug' ),
                ],
            ]
        );

        $this->add_control(
            'custom_link',
            [
                'label' => esc_html__( 'Custom URL', 'mh-plug' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'mh-plug' ),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'title_html_tag',
            [
                'label'   => esc_html__('HTML Tag', 'mh-plug'),
                'type'    => Controls_Manager::SELECT,
                'options' => [ 'h1'=>'H1', 'h2'=>'H2', 'h3'=>'H3', 'h4'=>'H4', 'h5'=>'H5', 'h6'=>'H6', 'p'=>'P', 'div'=>'DIV', 'span'=>'Span' ],
                'default' => 'h1',
            ]
        );

        $this->end_controls_section();


        // --- Style Tab ---
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'mh-plug'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

         $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'mh-plug' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [ 'title' => esc_html__( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left', ],
                    'center' => [ 'title' => esc_html__( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center', ],
                    'right' => [ 'title' => esc_html__( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right', ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .mh-site-title-container' => 'text-align: {{VALUE}};', // Target the container for alignment
                ],
            ]
        );

        // --- Start Color Tabs ---
        $this->start_controls_tabs( 'title_color_tabs' );

        // --- Normal Tab ---
        $this->start_controls_tab(
            'title_color_normal',
            [ 'label' => esc_html__( 'Normal', 'mh-plug' ) ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Text Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-site-title-content' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .mh-site-title-content a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label'     => esc_html__('Background Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-site-title-content' => 'background-color: {{VALUE}}; display: inline-block;', // Apply to inner element
                ],
            ]
        );

        $this->end_controls_tab(); // End Normal Tab

        // --- Hover Tab ---
        $this->start_controls_tab(
            'title_color_hover',
            [ 'label' => esc_html__( 'Hover', 'mh-plug' ) ]
        );

        $this->add_control(
            'text_hover_color',
            [
                'label'     => esc_html__('Text Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    // Apply hover color only when hovering the container if linked, or the content itself if not linked
                    '{{WRAPPER}} .mh-site-title-container a:hover .mh-site-title-content' => 'color: {{VALUE}};', // Hover link -> change content color
                    '{{WRAPPER}} .mh-site-title-container > div:hover .mh-site-title-content' => 'color: {{VALUE}};', // Hover div -> change content color
                    '{{WRAPPER}} .mh-site-title-content a:hover' => 'color: {{VALUE}};', // Directly hover link (fallback)
                ],
            ]
        );

        $this->add_control(
            'background_hover_color',
            [
                'label'     => esc_html__('Background Color', 'mh-plug'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                     // Apply hover bg color similarly
                    '{{WRAPPER}} .mh-site-title-container a:hover .mh-site-title-content' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .mh-site-title-container > div:hover .mh-site-title-content' => 'background-color: {{VALUE}};',
                     '{{WRAPPER}} .mh-site-title-content a:hover' => 'background-color: {{VALUE}};', // fallback
                ],
            ]
        );
        
        // Add Transition Duration Control
        $this->add_control(
            'hover_transition',
            [
                'label' => esc_html__( 'Transition Duration', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                 'selectors' => [
                    '{{WRAPPER}} .mh-site-title-content, {{WRAPPER}} .mh-site-title-content a' => 'transition: color {{SIZE}}s, background-color {{SIZE}}s;',
                 ],
                  'separator' => 'before',
            ]
        );


        $this->end_controls_tab(); // End Hover Tab

        $this->end_controls_tabs(); // End Color Tabs

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'selector' => '{{WRAPPER}} .mh-site-title-content', // Target the inner element
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'     => 'text_shadow',
                'selector' => '{{WRAPPER}} .mh-site-title-content', // Target the inner element
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label'      => esc_html__('Padding', 'mh-plug'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .mh-site-title-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'margin',
            [
                'label'      => esc_html__('Margin', 'mh-plug'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .mh-site-title-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', // Apply margin to the outer container
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get the actual site title from WordPress
        $site_title = get_bloginfo('name');
        if (empty($site_title)) {
             if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                 echo '<div style="text-align:center; padding: 20px; border: 1px dashed #ccc;">' . esc_html__('Site Title is Empty. Set one in Settings > General.', 'mh-plug') . '</div>';
             }
             return;
        }

        // Determine the link URL
        $link_url = '';
        $link_attributes_string = '';
        $has_link = false;

        if ($settings['link_to'] === 'home') {
            $link_url = home_url('/');
            $has_link = true;
            $this->add_render_attribute('link', 'href', esc_url($link_url));
            $link_attributes_string = $this->get_render_attribute_string('link');

        } elseif ($settings['link_to'] === 'custom' && !empty($settings['custom_link']['url'])) {
            $link_url = $settings['custom_link']['url'];
            $has_link = true;
            $this->add_link_attributes('link', $settings['custom_link']); // Adds href, target, rel
            $link_attributes_string = $this->get_render_attribute_string('link');
        }

        // Get the chosen HTML tag
        $title_tag = tag_escape($settings['title_html_tag']); // Sanitize the tag

        ?>
        <div class="mh-site-title-container">
            <<?php echo $title_tag; ?> class="mh-site-title-content">
                <?php if ($has_link) : ?>
                    <a <?php echo $link_attributes_string; ?>>
                        <?php echo esc_html($site_title); ?>
                    </a>
                <?php else : ?>
                    <?php echo esc_html($site_title); ?>
                <?php endif; ?>
            </<?php echo $title_tag; ?>>
        </div>

        <?php // --- ADD CSS TO REMOVE UNDERLINE --- ?>
        <style>
            .elementor-element-<?php echo $this->get_id(); ?> .mh-site-title-content a {
                text-decoration: none; /* Remove default underline */
            }
            
        </style>
        <?php
    }

}