<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

class MH_Testimonial_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mh_testimonial';
	}

	public function get_title() {
		return __( 'MH Testimonial', 'mh-plug-ecommerce-builder-widgets' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return [ 'mh-plug-widgets' ];
	}
    
    public function get_style_depends() {
        return [ 'mh-slick-css' ];
    }
    
    public function get_script_depends() {
        return [ 'mh-slick-js' ];
    }

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Testimonials', 'mh-plug-ecommerce-builder-widgets' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'author_name',
			[
				'label' => __( 'Author Name', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'John Doe', 'mh-plug-ecommerce-builder-widgets' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'author_designation',
			[
				'label' => __( 'Designation / Location', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'CEO, Company', 'mh-plug-ecommerce-builder-widgets' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'author_image',
			[
				'label' => __( 'Author Image', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'review_text',
			[
				'label' => __( 'Review Text', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'This is a great product! I highly recommend it to everyone.', 'mh-plug-ecommerce-builder-widgets' ),
			]
		);

		$repeater->add_control(
			'star_rating',
			[
				'label' => __( 'Star Rating', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 5,
				'step' => 1,
				'default' => 5,
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label' => __( 'Testimonial List', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'author_name' => __( 'John Doe', 'mh-plug-ecommerce-builder-widgets' ),
						'author_designation' => __( 'CEO, Company', 'mh-plug-ecommerce-builder-widgets' ),
						'review_text' => __( 'This is an amazing product. It changed my life completely!', 'mh-plug-ecommerce-builder-widgets' ),
						'star_rating' => 5,
					],
					[
						'author_name' => __( 'Jane Smith', 'mh-plug-ecommerce-builder-widgets' ),
						'author_designation' => __( 'Designer', 'mh-plug-ecommerce-builder-widgets' ),
						'review_text' => __( 'Excellent design and great support. Highly recommended.', 'mh-plug-ecommerce-builder-widgets' ),
						'star_rating' => 4,
					],
                    [
						'author_name' => __( 'Mike Johnson', 'mh-plug-ecommerce-builder-widgets' ),
						'author_designation' => __( 'Developer', 'mh-plug-ecommerce-builder-widgets' ),
						'review_text' => __( 'Very clean code and easy to use. Great job!', 'mh-plug-ecommerce-builder-widgets' ),
						'star_rating' => 5,
					],
				],
				'title_field' => '{{{ author_name }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout Settings', 'mh-plug-ecommerce-builder-widgets' ),
			]
		);

		$this->add_control(
			'enable_slider',
			[
				'label' => __( 'Enable Slider', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'mh-plug-ecommerce-builder-widgets' ),
				'label_off' => __( 'No', 'mh-plug-ecommerce-builder-widgets' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'label' => __( 'Columns', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 6,
				'default' => 3,
				'condition' => [
					'enable_slider' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label' => __( 'Gap', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'condition' => [
					'enable_slider' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slides_to_show',
			[
				'label' => __( 'Slides to Show', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 6,
				'default' => 3,
				'condition' => [
					'enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed (ms)', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3000,
				'condition' => [
					'enable_slider' => 'yes',
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label' => __( 'Show Arrows', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'enable_slider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label' => __( 'Show Dots', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'enable_slider' => 'yes',
				],
			]
		);

        $this->add_control(
            'dots_style',
            [
                'label' => __( 'Dots Style', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'circles',
                'options' => [
                    'circles'  => __( 'Circles', 'mh-plug-ecommerce-builder-widgets' ),
                    'lines'    => __( 'Lines', 'mh-plug-ecommerce-builder-widgets' ),
                    'squares'  => __( 'Squares', 'mh-plug-ecommerce-builder-widgets' ),
                ],
                'condition' => [
                    'enable_slider' => 'yes',
                    'show_dots' => 'yes',
                ],
            ]
        );

		$this->end_controls_section();

		// Style Tabs
        
        $this->start_controls_section(
			'section_slider_style',
			[
				'label' => __( 'Slider Navigation', 'mh-plug-ecommerce-builder-widgets' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_slider' => 'yes',
				],
			]
		);

        $this->add_control( 'heading_arrows', [ 'label' => __( 'Arrows', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::HEADING, 'condition' => [ 'show_arrows' => 'yes' ] ] );

        $this->add_control(
            'arrows_position',
            [
                'label' => __( 'Arrows Position', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inside',
                'options' => [
                    'inside'  => __( 'Inside Slider', 'mh-plug-ecommerce-builder-widgets' ),
                    'outside' => __( 'Outside Slider', 'mh-plug-ecommerce-builder-widgets' ),
                ],
                'condition' => [ 'show_arrows' => 'yes' ],
            ]
        );

        $this->add_responsive_control( 'arrows_outside_spacing', [
            'label' => __( 'Slider Padding (For Arrows)', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [ 'size' => 50, 'unit' => 'px' ],
            'selectors' => [ '{{WRAPPER}} .mh-arrows-pos-outside .mh-testimonial-slider' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};' ],
            'condition' => [ 'show_arrows' => 'yes', 'arrows_position' => 'outside' ],
        ]);

        $this->add_responsive_control( 'arrows_horizontal_offset', [
            'label' => __( 'Horizontal Offset', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
            'selectors' => [
                '{{WRAPPER}} .mh-arrows-pos-inside .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-arrows-pos-inside .slick-next' => 'right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-arrows-pos-outside .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-arrows-pos-outside .slick-next' => 'right: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'show_arrows' => 'yes' ],
        ]);
        
        $this->add_responsive_control( 'arrows_vertical_offset', [
            'label' => __( 'Vertical Offset', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => -200, 'max' => 200 ] ],
            'selectors' => [
                '{{WRAPPER}} .mh-testimonial-slider .slick-arrow' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'show_arrows' => 'yes' ],
        ]);

        $this->start_controls_tabs( 'arrows_tabs', [ 'condition' => [ 'show_arrows' => 'yes' ] ] );
        $this->start_controls_tab( 'arrows_normal', [ 'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ) ] );
        
        $this->add_control( 'arrow_color', [ 'label' => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow i' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'arrow_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow' => 'background-color: {{VALUE}};' ] ] );
        $this->add_responsive_control( 'arrow_size', [ 'label' => __( 'Box Size', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'arrow_icon_size', [ 'label' => __( 'Icon Size', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_control( 'arrow_border_radius', [ 'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'arrow_border', 'selector' => '{{WRAPPER}} .mh-testimonial-slider .slick-arrow' ] );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'arrows_hover', [ 'label' => __( 'Hover', 'mh-plug-ecommerce-builder-widgets' ) ] );
        
        $this->add_control( 'arrow_hover_color', [ 'label' => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow:hover i' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'arrow_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_control( 'arrow_hover_border_color', [ 'label' => __( 'Border Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-arrow:hover' => 'border-color: {{VALUE}};' ] ] );
        
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'heading_dots', [ 'label' => __( 'Dots', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => [ 'show_dots' => 'yes' ] ] );
        
        $this->add_control(
            'dots_position',
            [
                'label' => __( 'Dots Position', 'mh-plug-ecommerce-builder-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'outside',
                'options' => [
                    'inside'  => __( 'Inside Slider', 'mh-plug-ecommerce-builder-widgets' ),
                    'outside' => __( 'Outside Slider', 'mh-plug-ecommerce-builder-widgets' ),
                ],
                'condition' => [ 'show_dots' => 'yes' ],
            ]
        );

        $this->add_responsive_control( 'dots_outside_spacing', [
            'label' => __( 'Slider Padding (For Dots)', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [ 'size' => 40, 'unit' => 'px' ],
            'selectors' => [ '{{WRAPPER}} .mh-dots-pos-outside .mh-testimonial-slider' => 'padding-bottom: {{SIZE}}{{UNIT}};' ],
            'condition' => [ 'show_dots' => 'yes', 'dots_position' => 'outside' ],
        ]);

        $this->add_responsive_control( 'dots_vertical_offset', [
            'label' => __( 'Vertical Offset', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => -100, 'max' => 100 ] ],
            'selectors' => [
                '{{WRAPPER}} .mh-testimonial-slider .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'show_dots' => 'yes' ],
        ]);

        $this->start_controls_tabs( 'dots_tabs', [ 'condition' => [ 'show_dots' => 'yes' ] ] );
        $this->start_controls_tab( 'dots_normal', [ 'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ) ] );
        
        $this->add_control( 'dot_color', [ 'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li button' => 'background-color: {{VALUE}};' ] ] );
        
        $this->add_responsive_control( 'dot_width', [ 'label' => __( 'Width', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li button' => 'width: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'dot_height', [ 'label' => __( 'Height', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li button' => 'height: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'dot_gap', [ 'label' => __( 'Gap', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li' => 'margin: 0 {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'dot_border_radius', [ 'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->end_controls_tab();
        
        $this->start_controls_tab( 'dots_active', [ 'label' => __( 'Active / Hover', 'mh-plug-ecommerce-builder-widgets' ) ] );
        
        $this->add_control( 'dot_active_color', [ 'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li.slick-active button' => 'background-color: {{VALUE}};', '{{WRAPPER}} .mh-testimonial-slider .slick-dots li button:hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_responsive_control( 'dot_active_width', [ 'label' => __( 'Active Width', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-testimonial-slider .slick-dots li.slick-active button' => 'width: {{SIZE}}{{UNIT}};' ] ] );
        
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_style',
			[
				'label' => __( 'Card', 'mh-plug-ecommerce-builder-widgets' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        
        $this->add_control( 'card_alignment', [
            'label' => __( 'Text Alignment', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [ 'title' => __( 'Left', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-center' ],
                'right' => [ 'title' => __( 'Right', 'mh-plug-ecommerce-builder-widgets' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .mh-testimonial-card' => 'text-align: {{VALUE}};',
                '{{WRAPPER}} .mh-testimonial-author-block' => 'justify-content: {{VALUE}};',
            ]
        ]);

		$this->add_responsive_control(
			'card_padding',
			[
				'label' => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '30',
					'right' => '30',
					'bottom' => '30',
					'left' => '30',
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '10',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs( 'card_hover_tabs' );
        
        $this->start_controls_tab(
            'card_normal_tab',
            [
                'label' => __( 'Normal', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );
        
        $this->add_control(
			'card_bg_color',
			[
				'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-card' => 'background-color: {{VALUE}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'label' => __( 'Border', 'mh-plug-ecommerce-builder-widgets' ),
				'selector' => '{{WRAPPER}} .mh-testimonial-card',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => __( 'Box Shadow', 'mh-plug-ecommerce-builder-widgets' ),
				'selector' => '{{WRAPPER}} .mh-testimonial-card',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'card_hover_tab',
            [
                'label' => __( 'Hover', 'mh-plug-ecommerce-builder-widgets' ),
            ]
        );
        
        $this->add_control(
			'card_hover_bg_color',
			[
				'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-card:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'card_hover_border_color',
			[
				'label' => __( 'Border Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-card:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'hover_animation',
			[
				'label' => __( 'Lift Up on Hover', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_hover_box_shadow',
				'label' => __( 'Hover Box Shadow', 'mh-plug-ecommerce-builder-widgets' ),
				'selector' => '{{WRAPPER}} .mh-testimonial-card:hover',
			]
		);
        
        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->end_controls_section();
        
        $this->start_controls_section(
			'section_image_style',
			[
				'label' => __( 'Author Image', 'mh-plug-ecommerce-builder-widgets' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        
        $this->add_responsive_control( 'image_size', [ 'label' => __( 'Size', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px', '%' ], 'default' => [ 'size' => 50, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'image_border_radius', [ 'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'image_border', 'selector' => '{{WRAPPER}} .mh-testimonial-avatar' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .mh-testimonial-avatar' ] );
        $this->add_responsive_control( 'image_margin', [ 'label' => __( 'Margin', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        
        $this->end_controls_section();
        
        $this->start_controls_section(
			'section_typography_style',
			[
				'label' => __( 'Content Elements', 'mh-plug-ecommerce-builder-widgets' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'heading_stars_style',
			[
				'label' => __( 'Stars', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'star_color',
			[
				'label' => __( 'Star Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFD700',
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-stars i' => 'color: {{VALUE}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'star_size',
			[
				'label' => __( 'Star Size', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-stars i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control( 'stars_margin', [ 'label' => __( 'Margin', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-stars' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->add_control(
			'heading_review_style',
			[
				'label' => __( 'Review Text', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

		$this->add_control(
			'review_color',
			[
				'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#666666',
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-review' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'review_typography',
				'selector' => '{{WRAPPER}} .mh-testimonial-review',
			]
		);
        
        $this->add_responsive_control( 'review_margin', [ 'label' => __( 'Margin', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-review' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->add_control( 'author_block_margin', [ 'label' => __( 'Author Area Margin', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-author-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], 'separator' => 'before' ] );

        $this->add_control(
			'heading_name_style',
			[
				'label' => __( 'Author Name', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .mh-testimonial-name',
			]
		);
        
        $this->add_responsive_control( 'name_margin', [ 'label' => __( 'Margin', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->add_control(
			'heading_designation_style',
			[
				'label' => __( 'Designation', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

		$this->add_control(
			'designation_color',
			[
				'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#999999',
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-designation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'designation_typography',
				'selector' => '{{WRAPPER}} .mh-testimonial-designation',
			]
		);
        
        $this->add_responsive_control( 'designation_margin', [ 'label' => __( 'Margin', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .mh-testimonial-designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

		$this->end_controls_section();

        $this->start_controls_section(
			'section_separator_style',
			[
				'label' => __( 'Separator', 'mh-plug-ecommerce-builder-widgets' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        
        $this->add_control(
			'separator_color',
			[
				'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#eeeeee',
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-separator' => 'background-color: {{VALUE}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'separator_thickness',
			[
				'label' => __( 'Thickness', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-separator' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'separator_width',
			[
				'label' => __( 'Width', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-separator' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'separator_margin',
			[
				'label' => __( 'Margin', 'mh-plug-ecommerce-builder-widgets' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'right' => 'auto',
					'bottom' => '20',
					'left' => 'auto',
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .mh-testimonial-separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$testimonials = $settings['testimonials'];

		if ( empty( $testimonials ) ) {
			return;
		}

        $enable_slider = $settings['enable_slider'] === 'yes';
        $lift_up_class = ( isset($settings['hover_animation']) && $settings['hover_animation'] === 'yes' ) ? 'mh-lift-up' : '';
        $dots_style_class = $enable_slider && isset($settings['dots_style']) ? 'mh-dots-style-' . esc_attr($settings['dots_style']) : '';
        $arrows_pos_class = $enable_slider && isset($settings['arrows_position']) ? 'mh-arrows-pos-' . esc_attr($settings['arrows_position']) : '';
        $dots_pos_class = $enable_slider && isset($settings['dots_position']) ? 'mh-dots-pos-' . esc_attr($settings['dots_position']) : '';

        // Generate Inline CSS for structural layout
        $widget_id = $this->get_id();

        $css = "
            /* Equal Height Flex Grid/Slider */
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider .slick-track {
                display: flex !important;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider .slick-slide {
                height: inherit !important;
                display: flex !important;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider .slick-slide > div {
                display: flex;
                width: 100%;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider-item {
                height: 100%;
                width: 100%;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-grid-item {
                height: 100%;
            }
            
            /* Card Layout */
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-card {
                display: flex;
                flex-direction: column;
                transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
                height: 100%;
                box-sizing: border-box;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-card.mh-lift-up:hover {
                transform: translateY(-5px);
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-grid {
                display: grid;
            }
            
            /* Content Layout */
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-stars {
                margin-bottom: 15px;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-stars i {
                margin: 0 2px;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-review {
                font-style: italic;
                margin-bottom: 0;
                flex-grow: 1; /* Pushes author block perfectly to the bottom for equal height cards */
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-author-block {
                display: flex;
                align-items: center;
                width: 100%;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
                margin-right: 15px;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-info {
                display: flex;
                flex-direction: column;
                text-align: left;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-name {
                font-weight: bold;
                margin: 0 0 5px 0;
                line-height: 1.2;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-designation {
                margin: 0;
                line-height: 1.2;
            }
            
            /* Slider Spacing */
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider .slick-slide {
                margin: 0 10px;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider .slick-list {
                margin: 0 -10px;
            }
            .mh-testimonial-wrapper-{$widget_id} .mh-testimonial-slider-item {
                padding: 10px 0;
            }
            
            /* Slick Slider Navigation Custom CSS */
            .mh-testimonial-wrapper-{$widget_id} .slick-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                z-index: 10;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #ffffff;
                border: 1px solid #eeeeee;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                padding: 0;
            }
            .mh-testimonial-wrapper-{$widget_id} .slick-arrow:before {
                display: none !important;
            }
            .mh-testimonial-wrapper-{$widget_id} .slick-arrow:hover {
                background: #f8f8f8;
            }
            .mh-testimonial-wrapper-{$widget_id} .slick-arrow i {
                color: #333;
                font-size: 16px;
                transition: color 0.3s ease;
            }

            /* Separated Arrow and Dot Positioning Control Logic */
            
            /* Arrow Position Logic */
            .mh-arrows-pos-outside .slick-prev { left: 0; }
            .mh-arrows-pos-outside .slick-next { right: 0; }
            .mh-arrows-pos-inside .slick-prev { left: 10px; }
            .mh-arrows-pos-inside .slick-next { right: 10px; }
            
            /* Dot Position Logic */
            .mh-dots-pos-inside .slick-dots { bottom: 15px; }
            .mh-dots-pos-outside .slick-dots { bottom: 0; }

            /* Slick Dots Custom CSS */
            .mh-testimonial-wrapper-{$widget_id} .slick-dots {
                position: absolute;
                display: flex !important;
                justify-content: center;
                align-items: center;
                list-style: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
            .mh-testimonial-wrapper-{$widget_id} .slick-dots li {
                margin: 0 5px;
            }
            .mh-testimonial-wrapper-{$widget_id} .slick-dots li button {
                font-size: 0;
                line-height: 0;
                display: block;
                padding: 0;
                cursor: pointer;
                color: transparent;
                border: 0;
                outline: none;
                /* Super Smooth Animation */
                transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            }
            .mh-testimonial-wrapper-{$widget_id} .slick-dots li button:before {
                display: none !important;
            }
            
            /* Dots Style: Circles */
            .mh-dots-style-circles .slick-dots li button {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background-color: #cccccc;
            }
            .mh-dots-style-circles .slick-dots li.slick-active button {
                background-color: #333333;
            }
            
            /* Dots Style: Lines */
            .mh-dots-style-lines .slick-dots li button {
                width: 25px;
                height: 4px;
                border-radius: 2px;
                background-color: #cccccc;
            }
            .mh-dots-style-lines .slick-dots li.slick-active button {
                width: 35px;
                background-color: #333333;
            }
            
            /* Dots Style: Squares */
            .mh-dots-style-squares .slick-dots li button {
                width: 10px;
                height: 10px;
                border-radius: 0;
                background-color: #cccccc;
            }
            .mh-dots-style-squares .slick-dots li.slick-active button {
                transform: rotate(45deg);
                background-color: #333333;
            }
        ";
        echo "<style>
" . $css . "
</style>";
        ?>

        <div class="mh-testimonial-wrapper mh-testimonial-wrapper-<?php echo $widget_id; ?> <?php echo esc_attr($dots_style_class); ?> <?php echo esc_attr($arrows_pos_class); ?> <?php echo esc_attr($dots_pos_class); ?>">
            <?php if ( $enable_slider ) : 
                $slider_settings = [
                    'slidesToShow' => absint( $settings['slides_to_show'] ),
                    'autoplay' => $settings['autoplay'] === 'yes',
                    'autoplaySpeed' => absint( $settings['autoplay_speed'] ),
                    'arrows' => $settings['show_arrows'] === 'yes',
                    'dots' => $settings['show_dots'] === 'yes',
                    'prevArrow' => '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
                    'nextArrow' => '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
                    'responsive' => [
                        [
                            'breakpoint' => 1024,
                            'settings' => [
                                'slidesToShow' => min(2, absint( $settings['slides_to_show'] )),
                            ]
                        ],
                        [
                            'breakpoint' => 767,
                            'settings' => [
                                'slidesToShow' => 1,
                            ]
                        ]
                    ]
                ];
            ?>
                <div class="mh-testimonial-slider mh-testimonial-slider-<?php echo $widget_id; ?>" data-settings='<?php echo wp_json_encode( $slider_settings ); ?>'>
                    <?php foreach ( $testimonials as $item ) : ?>
                        <div class="mh-testimonial-slider-item">
                            <?php $this->render_testimonial_card($item, $lift_up_class); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="mh-testimonial-grid">
                    <?php foreach ( $testimonials as $item ) : ?>
                        <div class="mh-testimonial-grid-item">
                            <?php $this->render_testimonial_card($item, $lift_up_class); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $enable_slider ) : 
        $js = "
            jQuery(document).ready(function($) {
                var initSlick = function() {
                    var \$slider = $('.mh-testimonial-slider-{$widget_id}');
                    if (\$slider.length && $.fn.slick && !\$slider.hasClass('slick-initialized')) {
                        var settings = \$slider.data('settings');
                        \$slider.slick(settings);
                    }
                };
                
                if (typeof jQuery.fn.slick !== 'undefined') {
                    initSlick();
                } else {
                    setTimeout(initSlick, 500);
                }

                // Safely hook into Elementor after it fully initializes
                $(window).on('elementor/frontend/init', function() {
                    if (window.elementorFrontend && window.elementorFrontend.hooks) {
                        elementorFrontend.hooks.addAction('frontend/element_ready/mh_testimonial.default', function(\$scope) {
                            initSlick();
                        });
                    }
                });
            });
        ";
        echo "<script type='text/javascript'>\n" . $js . "\n</script>";
        endif;

	}
    
    private function render_testimonial_card($item, $lift_up_class) {
        ?>
        <div class="mh-testimonial-card <?php echo esc_attr( $lift_up_class ); ?>">
            <div class="mh-testimonial-stars">
                <?php
                $rating = isset( $item['star_rating'] ) ? absint( $item['star_rating'] ) : 5;
                for ( $i = 1; $i <= 5; $i++ ) {
                    if ( $i <= $rating ) {
                        echo '<i class="fas fa-star"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
            </div>
            
            <div class="mh-testimonial-review">
                <?php echo wp_kses_post( $item['review_text'] ); ?>
            </div>
            
            <div class="mh-testimonial-separator"></div>
            
            <div class="mh-testimonial-author-block">
                <?php if ( ! empty( $item['author_image']['url'] ) ) : ?>
                    <img src="<?php echo esc_url( $item['author_image']['url'] ); ?>" alt="<?php echo esc_attr( $item['author_name'] ); ?>" class="mh-testimonial-avatar">
                <?php endif; ?>
                
                <div class="mh-testimonial-info">
                    <h4 class="mh-testimonial-name"><?php echo esc_html( $item['author_name'] ); ?></h4>
                    <span class="mh-testimonial-designation"><?php echo esc_html( $item['author_designation'] ); ?></span>
                </div>
            </div>
        </div>
        <?php
    }
}
