<?php
/**
 * MH Product Rating Widget
 *
 * Displays the WooCommerce Product Star Rating and Review Count.
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

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Rating Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_empty', [
            'label'        => __( 'Show if Zero Reviews', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
            'description'  => __( 'Display empty stars if the product has not been rated yet.', 'mh-plug' ),
        ] );

        $this->add_control( 'show_count', [
            'label'        => __( 'Show Review Count', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Show', 'mh-plug' ),
            'label_off'    => __( 'Hide', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'count_format', [
            'label'       => __( 'Count Format', 'mh-plug' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => '(%s)',
            'description' => __( 'Use %s where you want the number to appear. Example: (%s reviews)', 'mh-plug' ),
            'condition'   => [ 'show_count' => 'yes' ],
        ] );

        $this->add_responsive_control( 'align', [
            'label'        => __( 'Alignment', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'    => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center'  => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'   => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'      => 'left',
            'selectors'    => [
                '{{WRAPPER}} .mh-product-rating-wrap' => 'justify-content: {{VALUE}};',
                // Map Elementor alignment values to flexbox
                '{{WRAPPER}} .mh-product-rating-wrap[style*="justify-content: left"]' => 'justify-content: flex-start;',
                '{{WRAPPER}} .mh-product-rating-wrap[style*="justify-content: right"]' => 'justify-content: flex-end;',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: STARS ── */
        $this->start_controls_section( 'style_stars_section', [
            'label' => __( 'Stars Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'star_color', [
            'label'     => __( 'Star Color (Filled)', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#FFB800',
            'selectors' => [
                '{{WRAPPER}} .mh-product-rating-wrap .star-rating span::before' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'empty_star_color', [
            'label'     => __( 'Star Color (Empty)', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#e5e5e5',
            'selectors' => [
                '{{WRAPPER}} .mh-product-rating-wrap .star-rating::before' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'star_size', [
            'label'      => __( 'Star Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 50 ] ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-rating-wrap .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'star_spacing', [
            'label'      => __( 'Spacing between Stars & Text', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-product-rating-wrap .star-rating' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [ 'show_count' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: REVIEW COUNT ── */
        $this->start_controls_section( 'style_count_section', [
            'label'     => __( 'Review Count Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_count' => 'yes' ],
        ] );

        $this->add_control( 'count_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [
                '{{WRAPPER}} .mh-rating-count, {{WRAPPER}} .mh-rating-count a' => 'color: {{VALUE}}; transition: all 0.3s ease;',
            ],
        ] );

        $this->add_control( 'count_hover_color', [
            'label'     => __( 'Hover Color (If Linked)', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [
                '{{WRAPPER}} .mh-rating-count a:hover' => 'color: {{VALUE}};',
            ],
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

        // Elementor Editor Mock Context
        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $mock_products = wc_get_products( [ 'limit' => 1, 'status' => 'publish' ] );
                if ( ! empty( $mock_products ) ) $product = $mock_products[0];
            }
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 15px; border: 1px dashed #d63638; color: #d63638; text-align: center;"><strong>MH Plug:</strong> Please create a product to preview ratings.</div>';
            }
            return;
        }

        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        $average      = $product->get_average_rating();

        // Check if we should hide when there are no reviews
        if ( $rating_count === 0 && $settings['show_empty'] !== 'yes' ) {
            return;
        }

        // Get the native WooCommerce star rating HTML block
        $rating_html = wc_get_rating_html( $average, $rating_count );

        // Fallback HTML if Woo rating HTML is empty (sometimes happens on completely new products)
        if ( empty( $rating_html ) && $settings['show_empty'] === 'yes' ) {
            $rating_html = '<div class="star-rating" role="img" aria-label="Rated 0 out of 5"><span style="width:0%">Rated <strong class="rating">0</strong> out of 5</span></div>';
        }

        // Format the review count text securely
        $count_text = '';
        if ( $settings['show_count'] === 'yes' ) {
            $format = ! empty( $settings['count_format'] ) ? $settings['count_format'] : '(%s)';
            // Replace %s with the actual number
            $formatted_string = sprintf( esc_html( $format ), '<span class="count">' . esc_html( $review_count ) . '</span>' );
            
            // Wrap in an anchor tag that jumps to the reviews tab
            $count_text = '<span class="mh-rating-count"><a href="#reviews" class="woocommerce-review-link" rel="nofollow">' . $formatted_string . '</a></span>';
        }

        // Render
        ?>
        <div class="mh-product-rating-wrap" style="display: flex; align-items: center; flex-wrap: wrap;">
            <?php echo $rating_html; // Contains safe Woo HTML ?>
            <?php echo $count_text; ?>
        </div>
        <?php
    }
}