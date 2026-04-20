<?php
/**
 * MH Product Share Widget
 *
 * Allows customers to share the product on various social media platforms.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Share_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_share'; }
    public function get_title() { return __( 'MH Product Share', 'mh-plug' ); }
    public function get_icon() { return 'eicon-share'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'share', 'social', 'facebook', 'twitter', 'whatsapp', 'mh' ]; }

    public function get_style_depends() { return [ 'mh-widgets-css' ]; }

    protected function register_controls() {

        /* ── CONTENT: PLATFORMS ── */
        $this->start_controls_section( 'section_platforms', [
            'label' => __( 'Share Platforms', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'share_label', [
            'label'   => __( 'Label Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Share:', 'mh-plug' ),
        ] );

        $this->add_control( 'show_facebook', [
            'label' => __( 'Facebook', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );
        $this->add_control( 'show_twitter', [
            'label' => __( 'Twitter / X', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );
        $this->add_control( 'show_pinterest', [
            'label' => __( 'Pinterest', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );
        $this->add_control( 'show_linkedin', [
            'label' => __( 'LinkedIn', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );
        $this->add_control( 'show_whatsapp', [
            'label' => __( 'WhatsApp', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );
        $this->add_control( 'show_telegram', [
            'label' => __( 'Telegram', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );
        $this->add_control( 'show_email', [
            'label' => __( 'Email', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes',
        ] );

        $this->add_responsive_control( 'align', [
            'label'     => __( 'Alignment', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'   => 'flex-start',
            'selectors' => [ '{{WRAPPER}} .mh-share-wrapper' => 'justify-content: {{VALUE}};', ],
            'separator' => 'before',
        ] );

        $this->end_controls_section();

        /* ── STYLE: LABEL ── */
        $this->start_controls_section( 'style_label', [
            'label' => __( 'Label Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'label_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-share-label' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'label_typography',
            'selector' => '{{WRAPPER}} .mh-share-label',
        ] );

        $this->add_responsive_control( 'label_spacing', [
            'label'      => __( 'Spacing After Label', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 15 ],
            'selectors'  => [ '{{WRAPPER}} .mh-share-label' => 'margin-right: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ICONS ── */
        $this->start_controls_section( 'style_icons', [
            'label' => __( 'Icon Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'icon_color_type', [
            'label'   => __( 'Color Type', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'custom',
            'options' => [
                'custom'   => __( 'Custom Colors', 'mh-plug' ),
                'official' => __( 'Official Brand Colors', 'mh-plug' ),
            ],
        ] );

        $this->start_controls_tabs( 'icon_color_tabs', [
            'condition' => [ 'icon_color_type' => 'custom' ],
        ] );

        $this->start_controls_tab( 'icon_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .mh-share-icon' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'icon_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'icon_hover_color', [
            'label'     => __( 'Icon Hover Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-share-icon:hover' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'size' => 16, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-share-icon i' => 'font-size: {{SIZE}}{{UNIT}};', ],
            'separator'  => 'before',
        ] );

        $this->add_responsive_control( 'icon_gap', [
            'label'      => __( 'Spacing Between Icons', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 15, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-share-icons-container' => 'gap: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get current post/product details securely
        $post_url   = urlencode( get_permalink() );
        $post_title = urlencode( get_the_title() );
        $image_url  = '';

        if ( is_singular( 'product' ) ) {
            global $product;
            if ( $product && $product->get_image_id() ) {
                $image_url = urlencode( wp_get_attachment_url( $product->get_image_id() ) );
            }
        }

        // Generate Sharing URLs
        $platforms = [
            'facebook'  => [ 'url' => "https://www.facebook.com/sharer/sharer.php?u={$post_url}", 'icon' => 'fab fa-facebook-f', 'color' => '#3b5998', 'show' => $settings['show_facebook'] ],
            'twitter'   => [ 'url' => "https://twitter.com/intent/tweet?text={$post_title}&url={$post_url}", 'icon' => 'fab fa-twitter', 'color' => '#1da1f2', 'show' => $settings['show_twitter'] ],
            'pinterest' => [ 'url' => "https://pinterest.com/pin/create/button/?url={$post_url}&description={$post_title}&media={$image_url}", 'icon' => 'fab fa-pinterest-p', 'color' => '#bd081c', 'show' => $settings['show_pinterest'] ],
            'linkedin'  => [ 'url' => "https://www.linkedin.com/shareArticle?mini=true&url={$post_url}&title={$post_title}", 'icon' => 'fab fa-linkedin-in', 'color' => '#0077b5', 'show' => $settings['show_linkedin'] ],
            'whatsapp'  => [ 'url' => "https://api.whatsapp.com/send?text={$post_title}%20{$post_url}", 'icon' => 'fab fa-whatsapp', 'color' => '#25d366', 'show' => $settings['show_whatsapp'] ],
            'telegram'  => [ 'url' => "https://t.me/share/url?url={$post_url}&text={$post_title}", 'icon' => 'fab fa-telegram-plane', 'color' => '#0088cc', 'show' => $settings['show_telegram'] ],
            'email'     => [ 'url' => "mailto:?subject={$post_title}&body={$post_url}", 'icon' => 'fas fa-envelope', 'color' => '#777777', 'show' => $settings['show_email'] ],
        ];

        $is_official = $settings['icon_color_type'] === 'official';

        ?>
        <div class="mh-share-wrapper">
            <?php if ( ! empty( $settings['share_label'] ) ) : ?>
                <div class="mh-share-label"><?php echo esc_html( $settings['share_label'] ); ?></div>
            <?php endif; ?>

            <div class="mh-share-icons-container">
                <?php foreach ( $platforms as $key => $data ) : ?>
                    <?php if ( $data['show'] === 'yes' ) : ?>
                        <a href="<?php echo esc_url( $data['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="mh-share-icon mh-share-<?php echo esc_attr( $key ); ?>" <?php echo $is_official ? 'style="color: ' . esc_attr( $data['color'] ) . ';"' : ''; ?>>
                            <i class="<?php echo esc_attr( $data['icon'] ); ?>"></i>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}