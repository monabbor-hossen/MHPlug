<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;

class MH_Button_Widget extends Widget_Base {

	public function get_name() {
		return 'mh_button';
	}

	public function get_title() {
		return __( 'MH Button', 'mhds-plug' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'mh-plug-widgets' ]; // Updated Category ID
	}

	protected function register_controls() {
		// =========================================
		// Content Tab
		// =========================================
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'mhds-plug' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Text', 'mhds-plug' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click Here', 'mhds-plug' ),
				'placeholder' => __( 'Click Here', 'mhds-plug' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_url',
			[
				'label'       => __( 'Link', 'mhds-plug' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'mhds-plug' ),
				'default'     => [
					'url' => '#',
				],
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __( 'Alignment', 'mhds-plug' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'    => [
						'title' => __( 'Left', 'mhds-plug' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'mhds-plug' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'mhds-plug' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'mhds-plug' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			]
		);

		$this->add_control(
			'size',
			[
				'label'   => __( 'Size', 'mhds-plug' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __( 'Extra Small', 'mhds-plug' ),
					'sm' => __( 'Small', 'mhds-plug' ),
					'md' => __( 'Medium', 'mhds-plug' ),
					'lg' => __( 'Large', 'mhds-plug' ),
					'xl' => __( 'Extra Large', 'mhds-plug' ),
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => __( 'Icon', 'mhds-plug' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'     => __( 'Icon Position', 'mhds-plug' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'  => __( 'Before', 'mhds-plug' ),
					'right' => __( 'After', 'mhds-plug' ),
				],
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label'     => __( 'Icon Spacing', 'mhds-plug' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mh-button-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mh-button-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();

		// =========================================
		// Style Tab
		// =========================================
		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Button Style', 'mhds-plug' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .mh-button',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .mh-button',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		// Normal State
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'mhds-plug' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __( 'Text Color', 'mhds-plug' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .mh-button'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .mh-button svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .mh-button i'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'background',
				'label'    => __( 'Background', 'mhds-plug' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mh-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .mh-button',
			]
		);

		$this->end_controls_tab();

		// Hover State
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'mhds-plug' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => __( 'Text Color', 'mhds-plug' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mh-button:hover, {{WRAPPER}} .mh-button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .mh-button:hover svg, {{WRAPPER}} .mh-button:focus svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .mh-button:hover i, {{WRAPPER}} .mh-button:focus i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_hover',
				'label'    => __( 'Background', 'mhds-plug' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mh-button:hover, {{WRAPPER}} .mh-button:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .mh-button:hover',
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'mhds-plug' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		// Icon Slide Effect Toggle
		$this->add_control(
			'icon_hover_animation',
			[
				'label'        => __( 'Icon Slide Effect', 'mhds-plug' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'mhds-plug' ),
				'label_off'    => __( 'No', 'mhds-plug' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'icon[value]!' => '',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'border',
				'selector'  => '{{WRAPPER}} .mh-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'mhds-plug' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mh-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => __( 'Padding', 'mhds-plug' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mh-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'mh-button-wrapper' );
		$this->add_render_attribute( 'button', 'class', 'mh-button' );

		// Size Class
		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'mh-button-size-' . $settings['size'] );
		}

		// Link
		if ( ! empty( $settings['button_url']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_url'] );
		}

		// Hover Animation Class
		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		// Icon Slide Class
		if ( 'yes' === $settings['icon_hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'mh-icon-slide' );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
				<?php $this->render_button_content( $settings ); ?>
			</a>
		</div>
		<?php
	}

	protected function render_button_content( $settings ) {
		$this->add_render_attribute( 'content-wrapper', 'class', 'mh-button-content-wrapper' );

		$icon_align = $settings['icon_align'];
		$this->add_render_attribute( 'icon-align', 'class', [
			'mh-button-icon',
			'mh-button-icon-' . $icon_align,
		] );

		echo '<span ' . $this->get_render_attribute_string( 'content-wrapper' ) . '>';

		// Render Icon (Left)
		if ( ! empty( $settings['icon']['value'] ) && 'left' === $icon_align ) : ?>
			<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
				<?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
		<?php endif; ?>

		<span class="mh-button-text"><?php echo esc_html( $settings['button_text'] ); ?></span>

		<?php
		// Render Icon (Right)
		if ( ! empty( $settings['icon']['value'] ) && 'right' === $icon_align ) : ?>
			<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
				<?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
		<?php endif; ?>

		<?php echo '</span>';
	}
}