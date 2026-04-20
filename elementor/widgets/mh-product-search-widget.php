<?php
/**
 * MH Product Search Widget (Live AJAX Search)
 * Fully Responsive & Optimized AJAX Script included.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class MH_Plug_Product_Search_Widget extends Widget_Base {

    public function get_name() { return 'mh_product_search'; }
    public function get_title() { return __( 'MH Product Search', 'mh-plug' ); }
    public function get_icon() { return 'eicon-search'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }
    public function get_keywords() { return [ 'search', 'product', 'ajax', 'live', 'woocommerce' ]; }

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
            'default'   => [ 'value' => 'fas fa-search', 'library' => 'fa-solid' ],
            'condition' => [ 'design_style' => 'modern' ],
        ] );

        $this->add_control( 'placeholder', [
            'label'   => __( 'Placeholder Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Search for premium vapes, pods...', 'mh-plug' ),
        ] );

        $this->add_control( 'not_found_text', [
            'label'   => __( 'Not Found Message', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'No products found.', 'mh-plug' ),
        ] );

        $this->end_controls_section();

        /* ── STYLE: INPUT ── */
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
            'default'   => '#f1f1f1',
            'selectors' => [ '{{WRAPPER}} .mh-search-input' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_control( 'input_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-search-input' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'placeholder_color', [
            'label'     => __( 'Placeholder Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#888888',
            'selectors' => [ 
                '{{WRAPPER}} .mh-search-input::placeholder' => 'color: {{VALUE}}; opacity: 1;',
                '{{WRAPPER}} .mh-search-input:-ms-input-placeholder' => 'color: {{VALUE}};',
                '{{WRAPPER}} .mh-search-input::-ms-input-placeholder' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'input_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'input_border',
            'selector'  => '{{WRAPPER}} .mh-search-input',
        ] );

        $this->add_responsive_control( 'input_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: ICON ── */
        $this->start_controls_section( 'style_icon_section', [
            'label'     => __( 'Icon Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'design_style' => 'modern' ],
        ] );

        $this->add_control( 'icon_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#888888',
            'selectors' => [ '{{WRAPPER}} .mh-search-icon i, {{WRAPPER}} .mh-search-spinner i' => 'color: {{VALUE}};', '{{WRAPPER}} .mh-search-icon svg' => 'fill: {{VALUE}};' ],
        ] );

        $this->add_responsive_control( 'icon_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 50 ] ],
            'selectors'  => [
                '{{WRAPPER}} .mh-search-icon i, {{WRAPPER}} .mh-search-spinner i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-search-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'icon_spacing', [
            'label'      => __( 'Icon Left Position', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-icon' => 'left: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: DROPDOWN RESULTS ── */
        $this->start_controls_section( 'style_results_section', [
            'label' => __( 'Dropdown Results', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'results_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-search-results' => 'background-color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'results_shadow',
            'selector' => '{{WRAPPER}} .mh-search-results',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $design   = $settings['design_style'];
        $icon     = $settings['search_icon'];
        $not_found = esc_attr( $settings['not_found_text'] );
        ?>

        <style>
            .mh-live-search-wrapper { position: relative; width: 100%; display: block; }
            .mh-search-form { position: relative; display: flex; align-items: center; margin: 0; width: 100%; }
            
            /* Input Base Styles */
            .mh-search-input { width: 100%; outline: none; transition: 0.3s; }
            .mh-search-input::-webkit-search-cancel-button { cursor: pointer; }
            
            /* Modern Design specific overrides */
            <?php if ( $design === 'modern' ) : ?>
                /* Fallback padding if Elementor control isn't set yet */
                .mh-search-input { padding-left: 45px; } 
                .mh-search-icon { position: absolute; left: 15px; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 2; }
            <?php endif; ?>
            
            .mh-search-spinner { position: absolute; right: 15px; display: none; z-index: 2; }
            
            /* Results Dropdown */
            .mh-search-results { 
                display: none; position: absolute; top: calc(100% + 5px); left: 0; width: 100%; 
                z-index: 99999; border-radius: 4px; max-height: 400px; overflow-y: auto; 
            }
            .mh-search-results a { transition: background 0.2s; }
            .mh-search-results a:hover { background: #f9f9f9; }
        </style>

        <div class="mh-live-search-wrapper">
            <form role="search" method="get" class="mh-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="hidden" name="post_type" value="product">
                
                <?php if ( $design === 'modern' && ! empty( $icon['value'] ) ) : ?>
                    <span class="mh-search-icon">
                        <?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>
                
                <input 
                    type="search" 
                    name="s"
                    class="mh-search-input" 
                    placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>" 
                    autocomplete="off"
                >
                
                <span class="mh-search-spinner"><i class="fas fa-spinner fa-spin"></i></span>
            </form>
            
            <div class="mh-search-results"></div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
                var searchTimer;
                var notFoundText = '<?php echo $not_found; ?>';

                $('.mh-search-input').on('keyup', function() {
                    var keyword = $(this).val().trim();
                    var $wrapper = $(this).closest('.mh-live-search-wrapper');
                    var $results = $wrapper.find('.mh-search-results');
                    var $spinner = $wrapper.find('.mh-search-spinner');

                    // Clear previous timer if typing continues
                    clearTimeout(searchTimer);

                    // If less than 3 characters, hide results to save server resources
                    if (keyword.length < 3) {
                        $results.hide().empty();
                        $spinner.hide();
                        return;
                    }

                    $spinner.show();

                    // Wait 500ms after the user STOPS typing before sending request (Debouncing)
                    searchTimer = setTimeout(function() {
                        $.post(ajaxUrl, { action: 'mh_live_product_search', keyword: keyword }, function(response) {
                            $spinner.hide();
                            if (response.success && response.data !== '') {
                                $results.html(response.data).slideDown(200);
                            } else {
                                $results.html('<div style="padding: 15px; color: #888; text-align: center;">' + notFoundText + '</div>').slideDown(200);
                            }
                        });
                    }, 500); 
                });

                // Hide dropdown when clicking outside the search box
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.mh-live-search-wrapper').length) {
                        $('.mh-search-results').hide();
                    }
                });
            });
        </script>
        <?php
    }
}