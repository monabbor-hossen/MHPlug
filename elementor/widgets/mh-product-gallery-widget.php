<?php
/**
 * MH Product Gallery Slider Widget
 * * Includes Premium Layout & Native Elementor Lightbox Integration
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
            'selectors'  => [
                '{{WRAPPER}} .mh-premium-gallery-container' => 'max-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
            ],
        ] );

        // 🚀 NEW: Lightbox Toggle Control
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
        $gallery_id = 'mh-gallery-' . $this->get_id(); // Unique ID for Lightbox grouping

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 10px; color: #d63638; text-align: center;">Please create a product to preview the gallery.</div>';
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
            .mh-premium-gallery-container { width: 100%; display: flex; flex-direction: column; }
            .mh-main-slider-wrapper { position: relative; width: 100%; margin-bottom: 20px; border-radius: 10px; overflow: hidden; background: #fff; }
            .mh-gallery-main-viewport { width: 100%; overflow: hidden; }
            .mh-main-slide-item a { display: block; outline: none; cursor: zoom-in; } /* Lightbox Cursor */
            .mh-main-slide-item img { width: 100%; height: auto; display: block; object-fit: contain; aspect-ratio: 1/1; cursor: grab; }
            .mh-main-slide-item a img { cursor: zoom-in; }
            .mh-main-slide-item img:active { cursor: grabbing; }
            .mh-gallery-arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 28px; color: #aaaaaa; cursor: pointer; z-index: 10; transition: 0.3s ease; }
            .mh-gallery-arrow:hover { color: #111111; }
            .mh-main-prev { left: 15px; }
            .mh-main-next { right: 15px; }
            .mh-thumb-slider-wrapper { position: relative; width: 100%; padding: 0 25px; box-sizing: border-box; }
            .mh-thumb-slide-item { cursor: pointer; outline: none; padding: 0 6px; }
            .mh-thumb-slide-item img { width: 100%; height: auto; display: block; aspect-ratio: 1/1; object-fit: cover; border-radius: 6px; border: 2px solid transparent; opacity: 0.5; transition: 0.3s ease; }
            .mh-thumb-slide-item.slick-current img, .mh-thumb-slide-item:hover img { opacity: 1; border-color: #111; }
            .mh-thumb-arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 16px; color: #888; cursor: pointer; z-index: 10; transition: 0.3s; }
            .mh-thumb-prev { left: 0; }
            .mh-thumb-next { right: 0; }
            .mh-gallery-thumb-slider:not(.slick-initialized) { display: flex; overflow: hidden; }
            .mh-gallery-thumb-slider:not(.slick-initialized) .mh-thumb-slide-item { width: 25%; flex-shrink: 0; }
        </style>

        <div class="mh-premium-gallery-container">
            <div class="mh-main-slider-wrapper">
                <div class="mh-gallery-main-viewport">
                    <?php foreach ( $all_image_ids as $attachment_id ) : 
                        $full_image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
                    ?>
                        <div class="mh-main-slide-item">
                            <?php if ( $enable_lightbox && $full_image_url ) : ?>
                                <a href="<?php echo esc_url( $full_image_url ); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="<?php echo esc_attr( $gallery_id ); ?>">
                                    <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_single' ); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mh-gallery-arrow mh-main-prev"><i class="eicon-chevron-left"></i></div>
                <div class="mh-gallery-arrow mh-main-next"><i class="eicon-chevron-right"></i></div>
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