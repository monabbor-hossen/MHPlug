<?php
/**
 * MH Feature Card Widget
 * Features: Title, Description, Button, Background (Image, Color, or None)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class MH_Feature_Card_Widget extends Widget_Base {

	public function get_name() {
		return 'mh-feature-card';
	}

	public function get_title() {
		return esc_html__( 'MH Feature Card', 'mh-plug' );
	}

	public function get_icon() {
		return 'mhi-card';
	}

	public function get_categories() {
		return [ 'mh-plug-widgets' ];
	}

	protected function register_controls() {

		// --- CONTENT SECTION ---
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Card Content', 'mh-plug' ),
			]
		);

		$this->add_control(
			'card_title',
			[
				'label' => esc_html__( 'Title', 'mh-plug' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Feature Title', 'mh-plug' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'card_description',
			[
				'label' => esc_html__( 'Description', 'mh-plug' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'mh-plug' ),
			]
		);

        $this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'mh-plug' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'mh-plug' ),
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Button Link', 'mh-plug' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'mh-plug' ),
				'default' => [
					'url' => '#',
				],
			]
		);
        
        $this->add_responsive_control(
			'text_align',
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
					'{{WRAPPER}} .mh-feature-card-wrapper' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .mh-feature-card-button-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'divider_bg',
			[
				'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
			]
		);

        // --- BACKGROUND CHOICE ---
        $this->add_control(
			'background_type',
			[
				'label' => esc_html__( 'Background Type', 'mh-plug' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'image' => [
						'title' => esc_html__( 'Image', 'mh-plug' ),
						'icon' => 'eicon-image',
					],
					'color' => [
						'title' => esc_html__( 'Color', 'mh-plug' ),
						'icon' => 'eicon-paint-brush',
					],
                    'none' => [
						'title' => esc_html__( 'None', 'mh-plug' ),
						'icon' => 'eicon-ban',
					],
				],
				'default' => 'image',
                'toggle' => false,
			]
		);

        // Option 1: Image
        $this->add_control(
			'card_image',
			[
				'label' => esc_html__( 'Background Image', 'mh-plug' ),
				'type' => Controls_Manager::MEDIA,
                'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'condition' => [
                    'background_type' => 'image',
                ],
			]
		);

        // Option 2: Color
        $this->add_control(
			'card_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
                'condition' => [
                    'background_type' => 'color',
                ],
			]
		);

		$this->end_controls_section();

		// --- STYLE: BOX ---
		$this->start_controls_section(
			'section_style_box',
			[
				'label' => esc_html__( 'Card Box', 'mh-plug' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
			'box_height',
			[
				'label' => esc_html__( 'Min Height', 'mh-plug' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
                'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 40,
                    'right' => 30,
                    'bottom' => 40,
                    'left' => 30,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        // Advanced Background Control (Position, Size, Repeat)
        // Only show if Image is selected, as these settings apply to the background image
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background_settings',
				'label' => esc_html__( 'Background Settings', 'mh-plug' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mh-feature-card-wrapper',
                'exclude' => ['image'], 
                'condition' => [
                    'background_type' => 'image',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'selector' => '{{WRAPPER}} .mh-feature-card-wrapper',
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .mh-feature-card-wrapper::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => esc_html__( 'Box Shadow', 'mh-plug' ),
				'selector' => '{{WRAPPER}} .mh-feature-card-wrapper',
			]
		);

		$this->end_controls_section();

        // --- STYLE: BACKGROUND OVERLAY ---
        $this->start_controls_section(
			'section_style_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'mh-plug' ),
				'tab' => Controls_Manager::TAB_STYLE,
                // Only show overlay options if Image is selected
                'condition' => [
                    'background_type' => 'image',
                ],
			]
		);

        $this->start_controls_tabs( 'tabs_overlay_style' );

        $this->start_controls_tab(
			'tab_overlay_normal',
			[
				'label' => esc_html__( 'Normal', 'mh-plug' ),
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay',
				'label' => esc_html__( 'Overlay', 'mh-plug' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mh-feature-card-wrapper::before',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
			'tab_overlay_hover',
			[
				'label' => esc_html__( 'Hover', 'mh-plug' ),
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay_hover',
				'label' => esc_html__( 'Overlay', 'mh-plug' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mh-feature-card-wrapper:hover::before',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

		// --- STYLE: TITLE ---
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'mh-plug' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF', // Default to white
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .mh-feature-card-title',
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: DESCRIPTION ---
		$this->start_controls_section(
			'section_style_desc',
			[
				'label' => esc_html__( 'Description', 'mh-plug' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#EEEEEE', 
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .mh-feature-card-description',
			]
		);
        
        $this->add_responsive_control(
			'desc_margin',
			[
				'label' => esc_html__( 'Margin', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        // --- STYLE: BUTTON ---
        $this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'mh-plug' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'mh-plug' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#004265',
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'mh-plug' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'mh-plug' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .mh-feature-card-button',
                'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 12,
                    'right' => 24,
                    'bottom' => 12,
                    'left' => 24,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'button_radius',
			[
				'label' => esc_html__( 'Border Radius', 'mh-plug' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => 4,
                    'right' => 4,
                    'bottom' => 4,
                    'left' => 4,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
				'selectors' => [
					'{{WRAPPER}} .mh-feature-card-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Determine background style based on selection
        $wrapper_style = '';
        if ( 'image' === $settings['background_type'] ) {
            if ( ! empty( $settings['card_image']['url'] ) ) {
                $wrapper_style = 'background-image: url(' . esc_url( $settings['card_image']['url'] ) . ');';
            }
        } elseif ( 'color' === $settings['background_type'] ) {
            if ( ! empty( $settings['card_bg_color'] ) ) {
                $wrapper_style = 'background-color: ' . esc_attr( $settings['card_bg_color'] ) . ';';
            }
        }
        // If 'none', no style attribute is added

		?>
		<div class="mh-feature-card-wrapper" style="<?php echo $wrapper_style; ?>">
			
			<?php if ( ! empty( $settings['card_title'] ) ) : ?>
				<h3 class="mh-feature-card-title">
					<?php echo esc_html( $settings['card_title'] ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $settings['card_description'] ) ) : ?>
				<div class="mh-feature-card-description">
					<?php echo esc_html( $settings['card_description'] ); ?>
				</div>
			<?php endif; ?>

            <?php if ( ! empty( $settings['button_text'] ) ) : ?>
                <div class="mh-feature-card-button-wrapper">
                    <a href="<?php echo esc_url( $settings['button_link']['url'] ); ?>" class="mh-feature-card-button">
                        <?php echo esc_html( $settings['button_text'] ); ?>
                    </a>
                </div>
            <?php endif; ?>

		</div>
		<?php
	}

    protected function _content_template() {
        ?>
        <#
        var wrapperStyle = '';
        if ( settings.background_type === 'image' && settings.card_image.url ) {
            wrapperStyle = 'background-image: url(' + settings.card_image.url + ');';
        } else if ( settings.background_type === 'color' && settings.card_bg_color ) {
            wrapperStyle = 'background-color: ' + settings.card_bg_color + ';';
        }
        #>
        <div class="mh-feature-card-wrapper" style="{{{ wrapperStyle }}}">
			
			<# if ( settings.card_title ) { #>
				<h3 class="mh-feature-card-title">
					{{{ settings.card_title }}}
				</h3>
			<# } #>

			<# if ( settings.card_description ) { #>
				<div class="mh-feature-card-description">
					{{{ settings.card_description }}}
				</div>
			<# } #>

            <# if ( settings.button_text ) { #>
                <div class="mh-feature-card-button-wrapper">
                    <a href="{{{ settings.button_link.url }}}" class="mh-feature-card-button">
                        {{{ settings.button_text }}}
                    </a>
                </div>
            <# } #>

		</div>
        <?php
    }
}