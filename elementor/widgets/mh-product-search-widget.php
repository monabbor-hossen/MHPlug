<?php
/**
 * MH Product Search Widget (Live AJAX Search)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

class MH_Plug_Product_Search_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_search'; }
    public function get_title() { return __( 'MH Product Search', 'mh-plug' ); }
    public function get_icon() { return 'eicon-search'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'search', 'product', 'ajax', 'live', 'woocommerce' ]; }

    // Tell Elementor to load our scripts so the AJAX works
    public function get_script_depends() {
        return [ 'mh-woo-scripts' ];
    }

    protected function register_controls() {
        
        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Search Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'design_style', [
            'label'   => __( 'Design Style', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'modern',
            'options' => [
                'classic' => __( 'Classic (Standard Box)', 'mh-plug' ),
                'modern'  => __( 'Modern (Icon Inside)', 'mh-plug' ),
            ],
        ] );

        $this->add_control( 'search_icon', [
            'label'     => __( 'Search Icon', 'mh-plug' ),
            'type'      => Controls_Manager::ICONS,
            'default'   => [
                'value'   => 'fas fa-search',
                'library' => 'fa-solid',
            ],
            'condition' => [ 'design_style' => 'modern' ],
        ] );

        $this->add_control( 'placeholder', [
            'label'   => __( 'Placeholder Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Search for premium vapes, pods, devices...', 'mh-plug' ),
        ] );

        $this->add_control( 'not_found_text', [
            'label'   => __( 'Not Found Message', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'No products found.', 'mh-plug' ),
        ] );

        $this->end_controls_section();

        /* ── STYLE ── */
        $this->start_controls_section( 'style_input_section', [
            'label' => __( 'Input Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'input_typography',
            'selector' => '{{WRAPPER}} .mh-search-input',
        ] );

        $this->add_control( 'input_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-search-input' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'input_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-search-input' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'placeholder_color', [
            'label'     => __( 'Placeholder Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ 
                '{{WRAPPER}} .mh-search-input::placeholder' => 'color: {{VALUE}}; opacity: 1;',
                '{{WRAPPER}} .mh-search-input:-ms-input-placeholder' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-search-input::-ms-input-placeholder' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'input_border',
            'selector'  => '{{WRAPPER}} .mh-search-input',
            'separator' => 'before',
        ] );

        $this->add_control( 'input_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style_icon_section', [
            'label'     => __( 'Icon Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'design_style' => 'modern' ],
        ] );

        $this->add_control( 'icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ 
                '{{WRAPPER}} .mh-search-icon i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-search-icon svg' => 'fill: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 50 ] ],
            'selectors'  => [
                '{{WRAPPER}} .mh-search-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-search-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $design   = $settings['design_style'];
        $icon     = $settings['search_icon'];

        // Determine inline styles based on the chosen design
        if ( $design === 'modern' ) {
            $input_style = "width: 100%; padding: 12px 15px 12px 45px; background-color: #f1f1f1; border: none; border-radius: 4px; outline: none;";
        } else {
            $input_style = "width: 100%; padding: 12px 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 4px; outline: none;";
        }
        ?>
        <div class="mh-live-search-wrapper" style="position: relative; width: 100%;">
            
            <form role="search" method="get" class="mh-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="position: relative; display: flex; align-items: center; margin: 0;">
                <input type="hidden" name="post_type" value="product">
                
                <?php if ( $design === 'modern' && ! empty( $icon['value'] ) ) : ?>
                    <span class="mh-search-icon" style="position: absolute; left: 15px; display: flex; align-items: center; justify-content: center; color: #888; pointer-events: none;">
                        <?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>
                
                <input 
                    type="search" 
                    name="s"
                    class="mh-search-input" 
                    placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>" 
                    data-not-found="<?php echo esc_attr( $settings['not_found_text'] ); ?>"
                    autocomplete="off"
                    style="<?php echo esc_attr( $input_style ); ?>"
                >
            </form>
            
            <div class="mh-search-results" style="display: none; position: absolute; top: calc(100% + 5px); left: 0; width: 100%; background: #fff; z-index: 9999; border: 1px solid #eee; border-radius: 4px; box-shadow: 0 10px 20px rgba(0,0,0,0.08); max-height: 400px; overflow-y: auto;">
                </div>
        </div>
        <?php
    }
}