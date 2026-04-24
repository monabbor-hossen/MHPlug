<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Header_Compare_Widget extends Widget_Base {

    public function get_name() { return 'mh_header_compare'; }
    public function get_title() { return __( 'MH Header Compare', 'mh-plug' ); }
    public function get_icon() { return 'eicon-exchange'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_script_depends() { return [ 'mh-widgets-js' ]; }

    protected function register_controls() {
        $this->start_controls_section('section_content', ['label' => __('Compare Settings', 'mh-plug')]);
        
        $this->add_control('compare_page_url', [
            'label' => __('Compare Page URL', 'mh-plug'),
            'type' => Controls_Manager::URL,
            'placeholder' => __('https://your-site.com/compare', 'mh-plug'),
            'description' => __('Link this to the page where you will build your Compare Table.', 'mh-plug'),
        ]);
        
        $this->end_controls_section();

        $this->start_controls_section('section_style', ['label' => __('Style', 'mh-plug'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('icon_color', [
            'label' => __('Icon Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('badge_bg', [
            'label' => __('Badge Background', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-count' => 'background-color: {{VALUE}};'],
        ]);
        $this->add_control('badge_color', [
            'label' => __('Badge Text Color', 'mh-plug'),
            'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .mh-compare-count' => 'color: {{VALUE}};'],
        ]);
        $this->add_responsive_control('icon_size', [
            'label' => __('Icon Size', 'mh-plug'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => ['{{WRAPPER}} .mh-header-compare-icon i' => 'font-size: {{SIZE}}{{UNIT}};'],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $target = $settings['compare_page_url']['is_external'] ? ' target="_blank"' : '';
        $url = !empty($settings['compare_page_url']['url']) ? $settings['compare_page_url']['url'] : '#';
        ?>
        <div class="mh-header-compare-wrapper" style="position: relative; display: inline-block;">
            <a href="<?php echo esc_url($url); ?>" <?php echo $target; ?> class="mh-header-compare-icon" style="text-decoration:none;">
                <i class="fas fa-exchange-alt"></i>
                <span class="mh-compare-count" style="position: absolute; top: -8px; right: -10px; font-size: 11px; padding: 2px 5px; border-radius: 50%; min-width: 18px; text-align: center; font-weight: bold; line-height: 1;">0</span>
            </a>
        </div>
        <?php
    }
}