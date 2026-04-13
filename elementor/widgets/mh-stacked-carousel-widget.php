<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

class MH_Stacked_Carousel_Widget extends Widget_Base {

	public function get_name() {
		return 'mh_stacked_carousel';
	}

	public function get_title() {
		return __( 'MH Stacked Carousel', 'mhds-plug' );
	}

	public function get_icon() {
		return 'eicon-post-slider';
	}

	public function get_categories() {
		return [ 'mh-plug-widgets' ];
	}

	public function get_script_depends() {
		return [ 'mh-slick-js' ];
	}

	protected function register_controls() {
		// Content Controls
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Carousel Items', 'mhds-plug' ),
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

		$this->add_control(
			'slides',
			[
				'label'       => __( 'Slides', 'mhds-plug' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
				],
				'title_field' => 'Slide',
			]
		);

		$this->end_controls_section();

		// Style Controls
		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Carousel Settings', 'mhds-plug' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slide_height',
			[
				'label'      => __( 'Card Height', 'mhds-plug' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 600,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors'  => [
					'{{WRAPPER}} .mh-stacked-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		$id = 'mh-stacked-slider-' . $this->get_id();
		?>

		<div class="mh-stacked-wrap">
			<div class="mh-stacked-slider" id="<?php echo esc_attr( $id ); ?>">
				<?php foreach ( $settings['slides'] as $slide ) : ?>
					<div class="mh-stacked-item item">
						<?php 
						$image_url = ! empty( $slide['image']['url'] ) ? $slide['image']['url'] : '';
						if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="Slide Image" />
							<div class="mh-overlay"></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var $slider = $('#<?php echo esc_attr( $id ); ?>');

			// 1. Image Conversion
			$slider.find('.mh-stacked-item').each(function() {
				var $img = $(this).find('img');
				var src = $img.attr('src');
				if(src) {
					$(this).css({
						'background-image': 'url(' + src + ')',
						'background-size': 'cover',
						'background-position': 'center'
					});
					$img.hide();
				}
			});

			// 2. Initialize Slick with "One-by-One" Focus
			$slider.slick({
				centerMode: true,
				variableWidth: true, /* Allows custom 400px width */
				slidesToShow: 1,     /* Strict focus on the center card */
				slidesToScroll: 1,   /* Slide one by one */
				arrows: false,
				dots: false,
				infinite: true,
				focusOnSelect: true,
				autoplay: false,
				autoplaySpeed: 3000,
				speed: 1000,         /* Slow, smooth slide (1s) */
				cssEase: 'cubic-bezier(0.25, 1, 0.5, 1)' /* Soft landing curve */
			});

			// 3. Update Classes for 3D Depth
			function updateClasses() {
				$slider.find('.slick-slide').removeClass('prev next');
				var $center = $slider.find('.slick-center');
				$center.prev('.slick-slide').addClass('prev');
				$center.next('.slick-slide').addClass('next');
			}

			$slider.on('init afterChange', function(){
				updateClasses();
			});
			
			// Initial Trigger
			setTimeout(updateClasses, 100);
		});
		</script>
		<?php
	}
}