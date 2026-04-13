<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Import necessary Elementor classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * MH Site Logo Widget Class
 */
class MH_Site_Logo_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-site-logo';
    }

    public function get_title() {
        return esc_html__('MH Site Logo', 'mh-plug');
    }

    public function get_icon() {
        return 'mhi-site-logo'; // Elementor's site logo icon
    }

    public function get_categories() {
        return ['mh-plug-widgets']; // Your custom category
    }

    protected function _register_controls() {

        // --- Content Tab ---
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Logo Settings', 'mh-plug'),
                'tab'   => Controls_Manager::TAB_CONTENT,
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
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .mh-site-logo-container' => 'text-align: {{VALUE}};',
                ],
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
            ]
        );

        $this->end_controls_section();


        // --- Style Tab ---
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Logo Style', 'mh-plug'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__( 'Width', 'mh-plug' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range' => [
                    'px' => [ 'min' => 10, 'max' => 1000 ],
                    '%' => [ 'min' => 1, 'max' => 100 ],
                    'vw' => [ 'min' => 1, 'max' => 100 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mh-site-logo img' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; height: auto;', // Maintain aspect ratio
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .mh-site-logo img',
			]
		);

        $this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .mh-site-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .mh-site-logo img',
			]
		);

        $this->add_responsive_control(
            'padding',
            [
                'label'      => esc_html__('Padding', 'mh-plug'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .mh-site-logo img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                 'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'margin',
            [
                'label'      => esc_html__('Margin', 'mh-plug'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .mh-site-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', // Apply margin to the container
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
    $settings = $this->get_settings_for_display();

    // Get the Site Logo ID
    $logo_id = get_theme_mod('custom_logo');

    if (!$logo_id) {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
             echo '<div style="text-align:center; padding: 20px; border: 1px dashed #ccc;">' . esc_html__('No Site Logo Set. Set one in Appearance > Customize > Site Identity.', 'mh-plug') . '</div>';
        }
        return;
    }

    // Get logo image details
    $logo_url = wp_get_attachment_image_url($logo_id, 'full');
    $logo_alt = get_post_meta($logo_id, '_wp_attachment_image_alt', true);
    if (empty($logo_alt)) {
         $logo_alt = get_bloginfo('name');
    }

    // Determine the link URL
    $link_url = '';
    $link_tag_key = 'logo_div'; // Default to a div if no link
    $link_attributes_string = ''; // Start with empty attributes

    if ($settings['link_to'] === 'home') {
        $link_url = home_url('/');
        $link_tag_key = 'logo_link'; // Use 'a' tag
        $this->add_render_attribute('logo_link', 'href', esc_url($link_url));
        $link_attributes_string = $this->get_render_attribute_string('logo_link');

    } elseif ($settings['link_to'] === 'custom' && !empty($settings['custom_link']['url'])) {
        $link_url = $settings['custom_link']['url'];
        $link_tag_key = 'logo_link'; // Use 'a' tag
        // Use add_link_attributes ONLY for the custom URL setting
        $this->add_link_attributes('logo_link', $settings['custom_link']);
        $link_attributes_string = $this->get_render_attribute_string('logo_link');
    }

    // Add attributes for the image tag
    $this->add_render_attribute('logo_image', 'src', esc_url($logo_url));
    $this->add_render_attribute('logo_image', 'alt', esc_attr($logo_alt));

    // Determine the tag name (a or div)
    $link_tag = ($link_tag_key === 'logo_link') ? 'a' : 'div';
    ?>
    <div class="mh-site-logo-container">
        <<?php echo $link_tag; ?> class="mh-site-logo" <?php echo $link_attributes_string; ?>>
            <img <?php echo $this->get_render_attribute_string('logo_image'); ?> />
        </<?php echo $link_tag; ?>>
    </div>
    <?php
}
}