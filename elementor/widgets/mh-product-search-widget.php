<?php
// Command: Mandatory Security check.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

/**
 * MH Plug Responsive Product Search Widget.
 *
 * @since 1.0.0
 */
class MH_Plug_Product_Search_Widget extends Widget_Base {

    public function get_name() {
        return 'mh-product-search';
    }

    public function get_title() {
        return __( 'MH Product Search', 'mh-plug' );
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return [ 'general' ]; // Or your custom MH category
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {

        // === CONTENT TAB ===
        $this->start_controls_section(
            'section_search_content',
            [
                'label' => __( 'Search Form', 'mh-plug' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'placeholder_text',
            [
                'label'       => __( 'Placeholder Text', 'mh-plug' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Search products...', 'mh-plug' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'       => __( 'Button Text', 'mh-plug' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Search', 'mh-plug' ),
            ]
        );

        $this->end_controls_section();

        // === STYLE TAB: INPUT FIELD ===
        $this->start_controls_section(
            'section_input_style',
            [
                'label' => __( 'Input Field', 'mh-plug' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'input_typography',
                'selector' => '{{WRAPPER}} .mh-search-field',
            ]
        );

        // Command: Mandatory responsive control for flexible widths across devices.
        $this->add_responsive_control(
            'input_width',
            [
                'label'      => __( 'Width', 'mh-plug' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    '%' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => '%', 'size' => 70 ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-search-field' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_padding',
            [
                'label'      => __( 'Padding', 'mh-plug' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-search-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // === STYLE TAB: BUTTON ===
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __( 'Search Button', 'mh-plug' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_width',
            [
                'label'      => __( 'Width', 'mh-plug' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'vw' ],
                'range'      => [
                    '%' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => '%', 'size' => 30 ],
                'selectors'  => [
                    '{{WRAPPER}} .mh-search-submit' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label'     => __( 'Background Color', 'mh-plug' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-search-submit' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label'     => __( 'Text Color', 'mh-plug' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mh-search-submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="mh-product-search-wrapper">
            <form role="search" method="get" class="mh-product-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; flex-wrap: wrap; width: 100%;">
                
                <input type="search" class="mh-search-field" placeholder="<?php echo esc_attr( $settings['placeholder_text'] ); ?>" value="<?php echo get_search_query(); ?>" name="s" style="box-sizing: border-box;" />
                
                <input type="hidden" name="post_type" value="product" />
                
                <button type="submit" class="mh-search-submit" style="box-sizing: border-box; cursor: pointer;">
                    <?php echo esc_html( $settings['button_text'] ); ?>
                </button>
            </form>
        </div>
        <?php
    }
}