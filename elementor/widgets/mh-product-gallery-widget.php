<?php
/**
 * MH Product Image & Gallery Slider Widget
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

    protected function register_controls() {
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Gallery Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_responsive_control( 'gallery_width', [
            'label'      => __( 'Gallery Max Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ] ],
            'default'    => [ 'size' => 100, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-premium-gallery-container' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
            ],
        ] );

        $this->add_control( 'active_thumb_border', [
            'label'     => __( 'Active Thumbnail Border Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-thumb-slide-item.slick-current img' => 'border: 2px solid {{VALUE}}; opacity: 1;',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $mock_products = wc_get_products( [ 'limit' => 1, 'status' => 'publish' ] );
                if ( ! empty( $mock_products ) ) $product = $mock_products[0];
            }
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) return;

        $main_image_id = $product->get_image_id();
        $gallery_ids   = $product->get_gallery_image_ids();
        
        if ( ! $main_image_id && empty( $gallery_ids ) ) return; 

        // Combine all IDs for the Lightbox loop
        $all_image_ids = array_merge( [ $main_image_id ], $gallery_ids );
        ?>

        <style>
            .mh-premium-gallery-container { width: 100%; display: flex; flex-direction: column; }
            
            /* Main Image Area */
            .mh-main-slider-wrapper { position: relative; width: 100%; margin-bottom: 20px; border-radius: 10px; overflow: hidden; background: #fff; }
            .mh-gallery-main-viewport { width: 100%; overflow: hidden; }
            .mh-main-slide-item img { width: 100%; height: auto; display: block; object-fit: contain; aspect-ratio: 1/1; cursor: grab; }
            .mh-main-slide-item img:active { cursor: grabbing; }
            
            /* Main Thin Arrows */
            .mh-gallery-arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 28px; color: #aaaaaa; cursor: pointer; z-index: 10; transition: 0.3s ease; }
            .mh-gallery-arrow:hover { color: #111111; }
            .mh-main-prev { left: 15px; }
            .mh-main-next { right: 15px; }
            
            /* Lightbox "Click to enlarge" Button */
            .mh-lightbox-trigger { position: absolute; bottom: 20px; left: 20px; background: rgba(255,255,255,0.9); height: 36px; width: 36px; border-radius: 20px; display: flex; align-items: center; text-decoration: none; color: #333; z-index: 15; overflow: hidden; transition: width 0.3s ease, background 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .mh-lightbox-trigger i { flex-shrink: 0; width: 36px; text-align: center; font-size: 14px; }
            .mh-lightbox-text { opacity: 0; white-space: nowrap; font-size: 13px; font-weight: 500; transition: opacity 0.3s ease; padding-right: 15px; }
            .mh-lightbox-trigger:hover { width: 145px; background: #ffffff; }
            .mh-lightbox-trigger:hover .mh-lightbox-text { opacity: 1; }

            /* Thumbnails Area */
            .mh-thumb-slider-wrapper { position: relative; width: 100%; padding: 0 25px; box-sizing: border-box; }
            .mh-thumb-slide-item { cursor: pointer; outline: none; padding: 0 6px; }
            .mh-thumb-slide-item img { width: 100%; height: auto; display: block; aspect-ratio: 1/1; object-fit: cover; border-radius: 6px; border: 2px solid transparent; opacity: 0.5; transition: 0.3s ease; }
            .mh-thumb-slide-item:hover img { opacity: 1; }
            
            /* Thumbnail Tiny Arrows */
            .mh-thumb-arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 16px; color: #888; cursor: pointer; z-index: 10; transition: 0.3s; }
            .mh-thumb-arrow:hover { color: #111; }
            .mh-thumb-prev { left: 0; }
            .mh-thumb-next { right: 0; }

            /* Fallback before Slick JS loads */
            .mh-gallery-thumb-slider:not(.slick-initialized) { display: flex; overflow: hidden; }
            .mh-gallery-thumb-slider:not(.slick-initialized) .mh-thumb-slide-item { width: 25%; flex-shrink: 0; }
        </style>

        <div class="mh-premium-gallery-container">
            
            <div class="mh-main-slider-wrapper">
                <div class="mh-gallery-main-viewport">
                    <?php foreach ( $all_image_ids as $index => $attachment_id ) : 
                        $full_img_url = wp_get_attachment_image_url( $attachment_id, 'full' );
                    ?>
                        <div class="mh-main-slide-item">
                            <a href="<?php echo esc_url( $full_img_url ); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="product-gallery-<?php echo esc_attr( $product->get_id() ); ?>">
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mh-gallery-arrow mh-main-prev"><i class="eicon-chevron-left"></i></div>
                <div class="mh-gallery-arrow mh-main-next"><i class="eicon-chevron-right"></i></div>

                <?php $first_full_img = wp_get_attachment_image_url( $main_image_id, 'full' ); ?>
                <a href="<?php echo esc_url( $first_full_img ); ?>" class="mh-lightbox-trigger" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="product-gallery-<?php echo esc_attr( $product->get_id() ); ?>">
                    <i class="eicon-qr-code"></i> <span class="mh-lightbox-text"><?php echo esc_html__( 'Click to enlarge', 'mh-plug' ); ?></span>
                </a>
            </div>

            <?php if ( ! empty( $gallery_ids ) ) : ?>
                <div class="mh-thumb-slider-wrapper">
                    <div class="mh-gallery-thumb-slider">
                        <?php foreach ( $all_image_ids as $attachment_id ) : ?>
                            <div class="mh-thumb-slide-item">
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' ); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mh-thumb-arrow mh-thumb-prev"><i class="eicon-chevron-left"></i></div>
                    <div class="mh-thumb-arrow mh-thumb-next"><i class="eicon-chevron-right"></i></div>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}