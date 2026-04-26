<?php
/**
 * MH Product Gallery Slider Widget
 * Includes Premium Layout, Native Elementor Lightbox Integration & Structural Slick Fixes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class MH_Product_Gallery_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_gallery'; }
    public function get_title() { return __( 'MH Product Gallery', 'mh-plug' ); }
    public function get_icon() { return 'eicon-product-gallery'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    public function get_style_depends() { return [ 'mh-widgets-css' ]; }
    
    // 🚀 THE FIX: This forces Elementor to load the Slick Slider engine for this widget
    public function get_script_depends() { return [ 'slick-js' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Gallery Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'gallery_width', [
            'label'      => __( 'Gallery Max Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-premium-gallery-container' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
            ],
        ] );

        $this->add_control( 'enable_lightbox', [
            'label'        => __( 'Enable Image Lightbox', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug' ),
            'label_off'    => __( 'No', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
            'separator'    => 'before',
            'description'  => __( 'Clicking the main image will open it in a fullscreen popup.', 'mh-plug' ),
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $enable_lightbox = $settings['enable_lightbox'] === 'yes';
        $widget_id = $this->get_id(); // Unique ID for Lightbox and Slick targeting

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 10px; color: #d63638; text-align: center;">Please assign a product to preview the gallery.</div>';
            }
            return;
        }

        $main_image_id = $product->get_image_id();
        $gallery_ids   = $product->get_gallery_image_ids();
        if ( ! is_array( $gallery_ids ) ) $gallery_ids = [];
        
        if ( ! $main_image_id && empty( $gallery_ids ) ) return; 

        $all_image_ids = array_merge( [ $main_image_id ], $gallery_ids );
        ?>

        <style>
            .mh-premium-gallery-container { display: block; width: 100%; position: relative; }
            
            /* Main Slider Styles */
            .mh-main-slider-wrapper { position: relative; margin-bottom: 10px; border-radius: 8px; overflow: hidden; }
            .mh-gallery-main-viewport { display: block; width: 100%; }
            .mh-gallery-main-viewport:not(.slick-initialized) .mh-main-slide-item:not(:first-child) { display: none; } /* Hide all but first image before load */
            .mh-gallery-main-viewport .slick-slide img { width: 100%; height: auto; display: block; object-fit: cover; }
            
            /* Thumbnail Slider Styles */
            .mh-thumb-slider-wrapper { position: relative; padding: 0 15px; }
            .mh-gallery-thumb-slider { display: block; width: 100%; }
            .mh-gallery-thumb-slider:not(.slick-initialized) { display: flex; overflow: hidden; gap: 10px; } /* Prevents giant stacking before load */
            .mh-gallery-thumb-slider:not(.slick-initialized) .mh-thumb-slide-item { width: 25%; flex-shrink: 0; }
            .mh-gallery-thumb-slider .slick-slide { padding: 0 5px; cursor: pointer; outline: none; }
            .mh-gallery-thumb-slider .slick-slide img { width: 100%; height: auto; display: block; border-radius: 6px; opacity: 0.5; transition: 0.3s; border: 2px solid transparent; }
            .mh-gallery-thumb-slider .slick-current img, 
            .mh-gallery-thumb-slider .slick-slide:hover img { opacity: 1; border-color: #2293e9; }
            
            /* Arrow Controls */
            .mh-gallery-arrow, .mh-thumb-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 10; background: #fff; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: 0.3s; }
            .mh-gallery-arrow:hover, .mh-thumb-arrow:hover { background: #2293e9; color: #fff; }
            .mh-main-prev { left: 10px; } .mh-main-next { right: 10px; }
            .mh-thumb-prev { left: -10px; width: 25px; height: 25px; } .mh-thumb-next { right: -10px; width: 25px; height: 25px; }
            .mh-gallery-arrow i, .mh-thumb-arrow i { line-height: 1; }
        </style>

        <div class="mh-premium-gallery-container" id="mh-gallery-container-<?php echo esc_attr( $widget_id ); ?>">
            
            <div class="mh-main-slider-wrapper">
                <div class="mh-gallery-main-viewport" id="mh-main-gallery-<?php echo esc_attr( $widget_id ); ?>">
                    <?php foreach ( $all_image_ids as $attachment_id ) : 
                        $full_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
                    ?>
                        <div class="mh-main-slide-item">
                            <?php if ( $enable_lightbox && $full_image_url ) : ?>
                                <a href="<?php echo esc_url( $full_image_url ); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="gallery-<?php echo esc_attr( $widget_id ); ?>">
                                    <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mh-gallery-arrow mh-main-prev" id="mh-main-prev-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-left"></i></div>
                <div class="mh-gallery-arrow mh-main-next" id="mh-main-next-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-right"></i></div>
            </div>

            <?php if ( count( $all_image_ids ) > 1 ) : ?>
                <div class="mh-thumb-slider-wrapper">
                    <div class="mh-gallery-thumb-slider" id="mh-thumb-gallery-<?php echo esc_attr( $widget_id ); ?>">
                        <?php foreach ( $all_image_ids as $attachment_id ) : ?>
                            <div class="mh-thumb-slide-item">
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' ); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mh-thumb-arrow mh-thumb-prev" id="mh-thumb-prev-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-left"></i></div>
                    <div class="mh-thumb-arrow mh-thumb-next" id="mh-thumb-next-<?php echo esc_attr( $widget_id ); ?>"><i class="eicon-chevron-right"></i></div>
                </div>
            <?php endif; ?>
            
        </div>

        <script>
        jQuery(document).ready(function($) {
            var initProductGallery = function() {
                var $main = $('#mh-main-gallery-<?php echo esc_js( $widget_id ); ?>');
                var $thumbs = $('#mh-thumb-gallery-<?php echo esc_js( $widget_id ); ?>');

                // Initialize Main Slider
                if ($main.length && typeof $.fn.slick === 'function' && !$main.hasClass('slick-initialized')) {
                    $main.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        fade: false,
                        asNavFor: $thumbs.length ? '#mh-thumb-gallery-<?php echo esc_js( $widget_id ); ?>' : null,
                        prevArrow: '#mh-main-prev-<?php echo esc_js( $widget_id ); ?>',
                        nextArrow: '#mh-main-next-<?php echo esc_js( $widget_id ); ?>'
                    });
                }

                // Initialize Thumbnail Slider
                if ($thumbs.length && typeof $.fn.slick === 'function' && !$thumbs.hasClass('slick-initialized')) {
                    $thumbs.slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        asNavFor: '#mh-main-gallery-<?php echo esc_js( $widget_id ); ?>',
                        dots: false,
                        arrows: true,
                        focusOnSelect: true,
                        prevArrow: '#mh-thumb-prev-<?php echo esc_js( $widget_id ); ?>',
                        nextArrow: '#mh-thumb-next-<?php echo esc_js( $widget_id ); ?>',
                        responsive: [
                            { breakpoint: 768, settings: { slidesToShow: 3 } },
                            { breakpoint: 480, settings: { slidesToShow: 2 } }
                        ]
                    });
                }
            };

            // Run immediately
            initProductGallery();

            // Run if loaded inside Elementor Editor
            if (typeof elementorFrontend !== 'undefined') {
                elementorFrontend.hooks.addAction('frontend/element_ready/mh_product_gallery.default', initProductGallery);
            }
        });
        </script>
        <?php
    }
}