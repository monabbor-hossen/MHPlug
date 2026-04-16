<?php
/**
 * MH Product Rating Widget
 *
 * Displays WooCommerce Product Rating (Stars, Numeric Value, and Review Count).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Rating_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_rating'; }
    public function get_title() { return __( 'MH Product Rating', 'mh-plug' ); }
    public function get_icon() { return 'eicon-rating'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'product', 'rating', 'stars', 'reviews', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT: RATING ELEMENTS ── */
        $this->start_controls_section( 'section_rating_content', [
            'label' => __( 'Rating Elements', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_numeric', [
            'label'        => __( 'Show Numeric Rating (e.g., 4.8)', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_stars', [
            'label'        => __( 'Show Stars', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_count', [
            'label'        => __( 'Show Review Count', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'hide_empty', [
            'label'        => __( 'Hide if No Reviews', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug' ),
            'label_off'    => __( 'No', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'no',
            'description'  => __( 'If enabled, the widget will completely hide if the product has 0 reviews.', 'mh-plug' ),
            'separator'    => 'before',
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
            'selectors' => [ '{{WRAPPER}} .mh-rating-wrapper' => 'justify-content: {{VALUE}};', ],
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'gap', [
            'label'      => __( 'Gap Between Elements', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 10 ],
            'selectors'  => [ '{{WRAPPER}} .mh-rating-wrapper' => 'gap: {{SIZE}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: STARS ── */
        $this->start_controls_section( 'style_stars', [
            'label'     => __( 'Stars Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_stars' => 'yes' ],
        ] );

        $this->add_control( 'star_color', [
            'label'     => __( 'Star Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffb200', // Classic gold
            'selectors' => [ '{{WRAPPER}} .mh-rating-wrapper .star-rating span::before' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'empty_star_color', [
            'label'     => __( 'Empty Star Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#e2e2e2',
            'selectors' => [ '{{WRAPPER}} .mh-rating-wrapper .star-rating::before' => 'color: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'star_size', [
            'label'      => __( 'Star Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'size' => 14, 'unit' => 'px' ],
            'selectors'  => [ 
                '{{WRAPPER}} .mh-rating-wrapper .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: NUMERIC RATING ── */
        $this->start_controls_section( 'style_numeric', [
            'label'     => __( 'Numeric Rating Style (4.8)', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_numeric' => 'yes' ],
        ] );

        $this->add_control( 'numeric_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-numeric-rating' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'numeric_typography',
            'selector' => '{{WRAPPER}} .mh-numeric-rating',
        ] );

        $this->end_controls_section();

        /* ── STYLE: REVIEW COUNT ── */
        $this->start_controls_section( 'style_count', [
            'label'     => __( 'Review Count Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_count' => 'yes' ],
        ] );

        $this->add_control( 'count_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .mh-rating-count' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'count_typography',
            'selector' => '{{WRAPPER}} .mh-rating-count',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        // Mock product for Elementor Editor if none exists
        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 10px; color: #d63638; text-align: center;">Please create a product to preview the rating.</div>';
            }
            return;
        }

        $rating_count   = $product->get_rating_count();
        $average_rating = $product->get_average_rating();

        // Hide if setting is enabled and there are no reviews
        if ( 'yes' === $settings['hide_empty'] && $rating_count == 0 ) {
            return;
        }

        // Formatting for display
        $formatted_average = number_format( (float) $average_rating, 1, '.', '' ); // Formats to 1 decimal point (e.g., "4.8" or "0.0")

        ?>
        <style>
            .mh-rating-wrapper { display: flex; align-items: center; flex-wrap: wrap; }
            .mh-numeric-rating { font-weight: bold; line-height: 1; }
            .mh-rating-count { line-height: 1; }
            
            /* WooCommerce Native Star Rating Overrides */
            .mh-rating-wrapper .star-rating {
                margin: 0;
                float: none;
                width: 5.4em; /* Standard Woo Width */
            }
        </style>

        <div class="mh-rating-wrapper">
            
            <?php // 1. Numeric Rating (e.g., 4.8) ?>
            <?php if ( 'yes' === $settings['show_numeric'] ) : ?>
                <div class="mh-numeric-rating">
                    <?php echo esc_html( $formatted_average ); ?>
                </div>
            <?php endif; ?>

            <?php // 2. Star Ratings ?>
            <?php if ( 'yes' === $settings['show_stars'] ) : ?>
                <div class="mh-stars-container">
                    <?php echo wc_get_rating_html( $average_rating, $rating_count ); ?>
                </div>
            <?php endif; ?>

            <?php // 3. Review Count ?>
            <?php if ( 'yes' === $settings['show_count'] ) : ?>
                <div class="mh-rating-count">
                    (<?php echo esc_html( $rating_count ); ?> <?php echo _n( 'Review', 'Reviews', $rating_count, 'mh-plug' ); ?>)
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}