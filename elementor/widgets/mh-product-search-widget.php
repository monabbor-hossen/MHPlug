<?php
/**
 * MH Product Search Widget (Live AJAX Search)
 * Fully Responsive, Optimized AJAX, and Expandable Layout.
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
    public function get_keywords() { return [ 'search', 'product', 'ajax', 'live', 'woocommerce', 'expandable' ]; }

    protected function register_controls() {
        
        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Search Settings', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'search_layout', [
            'label'   => __( 'Layout', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'standard',
            'options' => [
                'standard'   => __( 'Standard (Always Open)', 'mh-plug' ),
                'expandable' => __( 'Expandable (Icon Click)', 'mh-plug' ),
            ],
        ] );

        $this->add_control( 'design_style', [
            'label'   => __( 'Input Design Style', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'modern',
            'options' => [
                'classic' => __( 'Classic (Standard Box)', 'mh-plug' ),
                'modern'  => __( 'Modern (Icon Inside Input)', 'mh-plug' ),
            ],
        ] );

        $this->add_control( 'search_icon', [
            'label'     => __( 'Search Icon', 'mh-plug' ),
            'type'      => Controls_Manager::ICONS,
            'default'   => [ 'value' => 'fas fa-search', 'library' => 'fa-solid' ],
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

        /* ── STYLE: TRIGGER ICON (Expandable Only) ── */
        $this->start_controls_section( 'style_trigger_section', [
            'label'     => __( 'Trigger Icon (Expandable)', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'search_layout' => 'expandable' ],
        ] );

        $this->start_controls_tabs( 'tabs_trigger_style' );
        
        $this->start_controls_tab( 'tab_trigger_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'trigger_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-search-trigger' => 'color: {{VALUE}}; fill: {{VALUE}};' ],
        ] );
        $this->add_control( 'trigger_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-search-trigger' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_trigger_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'trigger_hover_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-search-trigger:hover' => 'color: {{VALUE}}; fill: {{VALUE}};' ],
        ] );
        $this->add_control( 'trigger_hover_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-search-trigger:hover' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'trigger_size', [
            'label'      => __( 'Icon Size', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'size' => 20 ],
            'separator'  => 'before',
            'selectors'  => [
                '{{WRAPPER}} .mh-search-trigger i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .mh-search-trigger svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'trigger_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'trigger_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: INPUT ── */
        $this->start_controls_section( 'style_input_section', [
            'label' => __( 'Input Box Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'expandable_width', [
            'label'      => __( 'Expandable Box Width', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 200, 'max' => 600 ] ],
            'default'    => [ 'size' => 320 ],
            'condition'  => [ 'search_layout' => 'expandable' ],
            'selectors'  => [ '{{WRAPPER}} .mh-search-expandable-container' => 'width: {{SIZE}}{{UNIT}};' ],
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

        /* ── STYLE: INSIDE ICON ── */
        $this->start_controls_section( 'style_icon_section', [
            'label'     => __( 'Inside Icon Style', 'mh-plug' ),
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
        $settings  = $this->get_settings_for_display();
        $layout    = $settings['search_layout'];
        $design    = $settings['design_style'];
        $icon      = $settings['search_icon'];
        $not_found = esc_attr( $settings['not_found_text'] );
        ?>

        <style>
            .mh-live-search-wrapper { position: relative; display: flex; align-items: center; justify-content: flex-end; }
            .mh-live-search-wrapper.layout-standard { width: 100%; }
            
            /* Expandable Trigger Icon */
            .mh-search-trigger {
                background: transparent; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
                transition: all 0.3s ease; padding: 10px; z-index: 10;
            }
            
            /* Expandable Container (Hidden by default) */
            .mh-search-expandable-container {
                position: absolute; top: calc(100% + 15px); right: 0; z-index: 9999;
                opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s ease;
                background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                max-width: 90vw; /* Keeps it from breaking mobile screens */
            }
            .mh-search-is-open .mh-search-expandable-container {
                opacity: 1; visibility: visible; transform: translateY(0);
            }

            /* Search Form Styles */
            .mh-search-form { position: relative; display: flex; align-items: center; margin: 0 !important; padding: 0 !important; width: 100%; box-sizing: border-box; }
            
            .mh-search-input { 
                width: 100%; outline: none; transition: 0.3s; margin: 0 !important; box-sizing: border-box; display: block;
            }
            .mh-search-input::-webkit-search-cancel-button { cursor: pointer; }
            
            <?php if ( $design === 'modern' ) : ?>
                .mh-search-input { padding-left: 45px; } 
                .mh-search-icon { 
                    position: absolute; left: 15px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 2; line-height: 1;
                }
            <?php endif; ?>
            
            .mh-search-spinner { 
                position: absolute; right: 15px; top: 50%; transform: translateY(-50%); display: none; z-index: 2; line-height: 1;
            }
            
            /* Results Dropdown */
            .mh-search-results { 
                display: none; position: absolute; top: calc(100% + 5px); left: 0; width: 100%; 
                z-index: 99999; border-radius: 4px; max-height: 400px; overflow-y: auto; 
            }
            /* Expandable overrides for results dropdown */
            .layout-expandable .mh-search-results { position: static; box-shadow: none; border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px; }
            
            .mh-search-results a { transition: background 0.2s; }
            .mh-search-results a:hover { background: #f9f9f9; }
        </style>

        <div class="mh-live-search-wrapper layout-<?php echo esc_attr( $layout ); ?>">
            
            <?php if ( $layout === 'expandable' ) : ?>
                <button class="mh-search-trigger" aria-label="<?php esc_attr_e('Open Search', 'mh-plug'); ?>">
                    <?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
                </button>
                <div class="mh-search-expandable-container">
            <?php endif; ?>

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

            <?php if ( $layout === 'expandable' ) : ?>
                </div> <?php endif; ?>
            
        </div>

        <script>
            jQuery(document).ready(function($) {
                var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
                var searchTimer;
                var notFoundText = '<?php echo $not_found; ?>';

                // Expandable Trigger Logic
                $('.mh-search-trigger').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var $wrapper = $(this).closest('.mh-live-search-wrapper');
                    $wrapper.toggleClass('mh-search-is-open');
                    if ($wrapper.hasClass('mh-search-is-open')) {
                        $wrapper.find('.mh-search-input').focus();
                    }
                });

                // Close Expandable when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.mh-live-search-wrapper').length) {
                        $('.mh-live-search-wrapper').removeClass('mh-search-is-open');
                        $('.mh-search-results').hide(); // Also hide the results dropdown
                    }
                });

                // Prevent closing when clicking inside the expanded container
                $('.mh-search-expandable-container').on('click', function(e) {
                    e.stopPropagation();
                });

                // AJAX Search Logic
                $('.mh-search-input').on('keyup', function() {
                    var keyword = $(this).val().trim();
                    var $wrapper = $(this).closest('.mh-live-search-wrapper');
                    var $results = $wrapper.find('.mh-search-results');
                    var $spinner = $wrapper.find('.mh-search-spinner');

                    clearTimeout(searchTimer);

                    if (keyword.length < 3) {
                        $results.hide().empty();
                        $spinner.hide();
                        return;
                    }

                    $spinner.show();

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
            });
        </script>
        <?php
    }
}