<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Product_Compare_Btn_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_compare_btn'; }
    public function get_title() { return __( 'MH Add to Compare Button', 'mh-plug' ); }
    public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        $this->start_controls_section('section_style', ['label' => __('Style', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .mh-compare-btn']);
        $this->add_control('text_color', [
            'label' => __('Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('text_color_added', [
            'label' => __('Text Color (When Added)', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-btn.added' => 'color: {{VALUE}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        if ( ! class_exists( 'WooCommerce' ) ) return;
        $product_id = get_the_ID();
        if ( ! $product_id ) return;
        ?>
        <div class="mh-single-compare-wrap">
            <a href="#" class="mh-compare-btn" data-product-id="<?php echo esc_attr($product_id); ?>" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; cursor:pointer; transition: 0.3s;">
                <i class="fas fa-exchange-alt"></i> 
                <span class="mh-compare-text"><?php esc_html_e('Add to Compare', 'mh-plug'); ?></span>
            </a>
        </div>
        <?php
    }
}