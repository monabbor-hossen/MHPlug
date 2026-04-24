<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

class MH_Synced_Slider_Widget extends Widget_Base {

	public function get_name() {
		return 'mh_synced_slider';
	}

	public function get_title() {
		return __( 'MH Synced Slider', 'mhds-plug' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return [ 'mh-plug-widgets' ];
	}

	public function get_script_depends() {
		return [ 'mh-slick-js' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Slides', 'mhds-plug' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Image', 'mhds-plug' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'meta',
			[
				'label'   => __( 'Top Label', 'mhds-plug' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'SEASONAL SALE', 'mhds-plug' ),
			]
		);

		$repeater->add_control(
			'heading',
			[
				'label'   => __( 'Heading', 'mhds-plug' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Digital<br>Dreams', 'mhds-plug' ),
			]
		);

		$repeater->add_control(
			'price',
			[
				'label'   => __( 'Price', 'mhds-plug' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Price from : $850', 'mhds-plug' ),
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'   => __( 'Button Text', 'mhds-plug' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Shop now', 'mhds-plug' ),
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'       => __( 'Link', 'mhds-plug' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'mhds-plug' ),
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => __( 'Slides', 'mhds-plug' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'heading' => 'Digital<br>Dreams', 'meta' => 'SEASONAL SALE', 'price' => 'Price from : $850' ],
					[ 'heading' => 'Bold<br>Impact', 'meta' => 'NEW ARRIVAL', 'price' => 'Price from : $420' ],
					[ 'heading' => 'Clear<br>Vision', 'meta' => 'LIMITED EDITION', 'price' => 'Price from : $999' ],
					[ 'heading' => 'Warm<br>Horizons', 'meta' => 'BEST SELLER', 'price' => 'Price from : $600' ],
				],
				'title_field' => '{{{ heading }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		if ( empty( $settings['slides'] ) ) {
			return;
		}
		?>

		<style>
			/* Layout Grid */
			.mh-synced-container {
				display: flex;
				width: 100%;
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
				align-items: center;
				gap: 60px;
				background-color: #1a202c; 
				overflow: hidden;
			}

			.mh-left-col {
				width: 35%;
				padding: 20px;
				cursor: grab;
			}
			.mh-left-col:active {
				cursor: grabbing;
			}

			.mh-right-col {
				width: 65%;
				position: relative;
			}

			/* --- LEFT SLIDER (CONTENT) --- */
			@keyframes slideUpFade {
				from { opacity: 0; transform: translateY(20px); }
				to { opacity: 1; transform: translateY(0); }
			}

			.mh-text-slider .slick-slide {
				display: block; 
				min-height: 200px;
				outline: none;
				padding: 10px; 
			}

			.mh-text-content {
				background-color: transparent; 
				color: white;                  
				padding: 20px 0;               
				border-radius: 0;       
				box-shadow: none;              
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: flex-start;
				min-height: 350px;
			}
			
			.mh-text-content h2, 
			.mh-text-content p, 
			.mh-text-content .mh-meta,
			.mh-text-content .mh-price,
			.mh-text-content .mh-shop-btn {
				opacity: 0; 
			}

			.slick-active .mh-text-content .mh-meta { animation: slideUpFade 0.5s ease forwards 0.1s; }
			.slick-active .mh-text-content h2 { animation: slideUpFade 0.5s ease forwards 0.2s; }
			.slick-active .mh-text-content .mh-price { animation: slideUpFade 0.5s ease forwards 0.3s; }
			.slick-active .mh-text-content .mh-shop-btn { animation: slideUpFade 0.5s ease forwards 0.4s; }
			
			.mh-text-content .mh-meta {
				display: block;
				margin-bottom: 0.5rem;
				text-transform: uppercase;
				letter-spacing: 2px;
				font-size: 0.85rem;
				color: #94a3b8; 
				font-weight: 600;
			}

			.mh-text-content h2 {
				font-size: 3.5rem; 
				font-weight: 800;
				margin: 0 0 1rem 0;
				line-height: 1.1;
				color: #ffffff; 
			}

			.mh-text-content .mh-price {
				font-size: 1.25rem;
				color: #e2e8f0; 
				margin-bottom: 30px;
				font-weight: 500;
			}

			.mh-shop-btn {
				background-color: #ff8787; 
				color: white;
				border: none;
				padding: 14px 35px;
				border-radius: 50px; 
				font-size: 1rem;
				font-weight: 700;
				cursor: pointer;
				transition: all 0.3s ease;
				display: inline-block;
				box-shadow: 0 4px 15px rgba(255, 135, 135, 0.4); 
				text-decoration: none;
			}
			
			.mh-shop-btn:hover {
				background-color: #ff6b6b;
				transform: translateY(-3px);
				box-shadow: 0 6px 20px rgba(255, 135, 135, 0.6);
				color: white;
			}

			/* --- RIGHT SLIDER (IMAGE) --- */
			.mh-image-slider .slick-slide {
				z-index: 0;
				position: relative;
				transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
			}

			.mh-image-slider .slick-center { z-index: 20 !important; }

			.mh-image-card {
				border-radius: 15px;
				width: 150%; 
				margin-left: -25%; 
				height: 400px;
				overflow: hidden; 
				display: flex !important;
				justify-content: center;
				align-items: center;
				transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
				opacity: 0; 
				pointer-events: none;
				transform: scale(0.7) translateX(-50px);
			}
			
			/* OVERLAP LOGIC via JS Classes */
			
			/* Left Neighbor: Push RIGHT (60%) */
			.mh-image-slider .slick-slide.slide-prev .mh-image-card {
				opacity: 0.6;
				pointer-events: auto;
				transform: translateX(40%) scale(0.85);
				z-index: 10;
			}

			/* Right Neighbor: Push LEFT (-60%) */
			.mh-image-slider .slick-slide.slide-next .mh-image-card {
				opacity: 0.6;
				pointer-events: auto;
				transform: translateX(-40%) scale(0.85);
				z-index: 10;
			}

			/* Center Slide */
			.mh-image-slider .slick-center .mh-image-card {
				opacity: 1;
				transform: scale(1.1);
				z-index: 20;
			}

			.mh-image-card img {
				width: 100%;
				height: 100%;
				object-fit: cover; 
				display: block;
			}

			/* Arrows */
			.mh-image-slider .slick-arrow {
				position: absolute;
				bottom: -60px;
				top: auto;
				transform: none;
				width: 50px;
				height: 50px;
				border: 2px solid rgba(255,255,255,0.2);
				border-radius: 50%;
				transition: all 0.2s;
				z-index: 100;
				cursor: pointer;
				background: transparent;
			}
			.mh-image-slider .slick-arrow:hover {
				background: rgba(255,255,255,0.1);
				border-color: white;
			}
			.mh-image-slider .slick-arrow:before { 
				color: white; 
				font-size: 24px;
				opacity: 1;
			}

			.mh-image-slider .slick-prev { left: calc(50% - 60px); }
			.mh-image-slider .slick-next { right: calc(50% - 60px); }

			/* Responsive */
			@media (max-width: 900px) {
				.mh-synced-container {
					flex-direction: column-reverse;
					padding: 10px;
					gap: 20px;
				}
				.mh-left-col, .mh-right-col { width: 100%; }
				.mh-text-slider { text-align: center; }
				.mh-text-content { align-items: center; text-align: center; } 
				.mh-text-content h2 { font-size: 2.5rem; }
				.mh-image-card { height: 250px; width: 120%; margin-left: -10%;}
				.mh-image-slider .slick-center .mh-image-card { transform: scale(1.05); }
				.mh-image-slider .slick-arrow { bottom: -40px; width: 40px; height: 40px; }
				
				/* Reduce overlap on mobile */
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
									<h2 class="mh-heading"><?php echo $slide['heading']; // Allow <br> ?></h2>
								<?php endif; ?>
								
								<?php if ( $slide['price'] ) : ?>
									<div class="mh-price"><?php echo esc_html( $slide['price'] ); ?></div>
								<?php endif; ?>

								<?php if ( $slide['button_text'] ) : 
									$link_attrs = '';
									if ( ! empty( $slide['button_link']['url'] ) ) {
										$this->add_link_attributes( 'btn_' . $slide['_id'], $slide['button_link'] );
										$link_attrs = $this->get_render_attribute_string( 'btn_' . $slide['_id'] );
									}
								?>
									<a href="<?php echo esc_url( $slide['button_link']['url'] ); ?>" class="mh-shop-btn" <?php echo $link_attrs; ?>>
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
									<img src="<?php echo esc_url( $slide['image']['url'] ); ?>" alt="Product Image">
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

		</div>

		<script>
		jQuery(document).ready(function($) {
			var textSlider = '#text-<?php echo esc_attr( $id ); ?>';
			var imageSlider = '#image-<?php echo esc_attr( $id ); ?>';

			// 1. Initialize Left Slider (Content)
			$(textSlider).slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
				fade: false,
				asNavFor: imageSlider,
				draggable: true, 
				swipe: true,
				speed: 500,           
				touchThreshold: 10
			});

			// 2. Initialize Right Slider (Images)
			var $imgSlider = $(imageSlider).slick({
				centerMode: true,
				centerPadding: '0px',
				slidesToShow: 3,
				infinite: true,
				autoplay: true,
				autoplaySpeed: 3000,
				speed: 500,        
				arrows: false,
				asNavFor: textSlider,
				focusOnSelect: true,
				responsive: [
					{
						breakpoint: 900,
						settings: {
							slidesToShow: 3,
							centerPadding: '20px'
						}
					},
					{
						breakpoint: 600,
						settings: {
							slidesToShow: 3, 
							centerPadding: '10px'
						}
					}
				]
			});

			// 3. JS Logic for Overlap Classes (The "Magic" Part)
			function updateOverlapClasses() {
				// Remove old classes
				$(imageSlider).find('.slick-slide').removeClass('slide-prev slide-next');
				
				// Find center
				var $center = $(imageSlider).find('.slick-center');
				
				// Add classes to immediate neighbors
				$center.prev().addClass('slide-prev');
				$center.next().addClass('slide-next');
			}

			// Bind to events
			$imgSlider.on('init afterChange setPosition', function() {
				updateOverlapClasses();
			});
			// Initial run
			updateOverlapClasses();
		});
		</script>
		<?php
	}
}