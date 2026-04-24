<?php
/**
 * MH Plug - Quick View Engine (Separated Trait Module)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

trait MH_Quick_View_Trait {

    protected function get_qv_templates() {
        $templates = [ '' => __( 'Default (Built-in Layout)', 'mh-plug' ) ];
        $query = new \WP_Query( [ 
            'post_type'      => 'mh_templates', 
            'posts_per_page' => -1, 
            'post_status'    => ['publish', 'draft'] 
        ] );
        if ( $query->have_posts() ) {
            foreach ( $query->posts as $post ) {
                $templates[ $post->ID ] = $post->post_title;
            }
        }
        return $templates;
    }

    protected function register_quick_view_controls() {
        // --- CONTENT ---
        $this->start_controls_section( 'section_quick_view_config', [
            'label' => __( 'Quick View Button', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT
        ]);

        $this->add_control( 'show_quick_view', [
            'label'   => __( 'Show Quick View Button', 'mh-plug' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes'
        ]);

        $this->add_control( 'quick_view_template', [
            'label'       => __( 'Select Popup Template', 'mh-plug' ),
            'type'        => Controls_Manager::SELECT,
            'options'     => $this->get_qv_templates(),
            'default'     => '',
            'description' => __( 'Select a Theme Builder template to override the default Quick View design.', 'mh-plug' ),
            'condition'   => [ 'show_quick_view' => 'yes' ]
        ]);

        $this->add_control( 'quick_view_icon', [
            'label'     => __( 'Button Icon', 'mh-plug' ),
            'type'      => Controls_Manager::ICONS,
            'default'   => [ 'value' => 'fas fa-shopping-bag', 'library' => 'fa-solid' ],
            'condition' => [ 'show_quick_view' => 'yes' ]
        ]);
        
        $this->end_controls_section();

        // --- STYLE ---
        $this->start_controls_section( 'section_quick_view_style', [
            'label'     => __( 'Quick View Style', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_quick_view' => 'yes' ]
        ]);

        // 🚀 FIX: Added Default 16px size to prevent vanishing icon
        $this->add_responsive_control( 'qv_icon_size', [
            'label'     => __( 'Icon Size', 'mh-plug' ),
            'type'      => Controls_Manager::SLIDER,
            'default'   => [ 'size' => 16 ],
            'range'     => [ 'px' => [ 'min' => 10, 'max' => 100 ] ],
            'selectors' => [ 
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger i' => 'font-size: {{SIZE}}px !important;', 
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger svg' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important;' 
            ]
        ]);

        // 🚀 FIX: Added Default 40px width/height to prevent 0x0 collapse
        $this->add_responsive_control( 'qv_box_size', [
            'label'     => __( 'Button Size', 'mh-plug' ),
            'type'      => Controls_Manager::SLIDER,
            'default'   => [ 'size' => 40 ],
            'range'     => [ 'px' => [ 'min' => 20, 'max' => 150 ] ],
            'selectors' => [ 
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important; min-width: {{SIZE}}px !important; min-height: {{SIZE}}px !important;' 
            ]
        ]);

        // 🚀 FIX: Added Default 50% border radius to make it a perfect circle immediately
        $this->add_responsive_control( 'qv_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;' ]
        ]);

        $this->start_controls_tabs( 'tabs_qv_style' );

        // Normal
        $this->start_controls_tab( 'tab_qv_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'qv_color', [
            'label'     => __( 'Icon Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ 
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger' => 'color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger i' => 'color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger svg' => 'fill: {{VALUE}} !important;'
            ]
        ]);
        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'qv_bg',
            'label'    => __( 'Background (Supports Gradient)', 'mh-plug' ),
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger'
        ]);
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'qv_border', 'selector' => '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'qv_shadow', 'selector' => '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger' ] );
        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab( 'tab_qv_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'qv_hover_color', [
            'label'     => __( 'Icon Hover Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ 
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger:hover' => 'color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger:hover i' => 'color: {{VALUE}} !important;',
                '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger:hover svg' => 'fill: {{VALUE}} !important;' 
            ]
        ]);
        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'qv_hover_bg',
            'label'    => __( 'Hover Background', 'mh-plug' ),
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger:hover'
        ]);
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'qv_hover_border', 'selector' => '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger:hover' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'qv_hover_shadow', 'selector' => '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger:hover' ] );
        $this->add_control( 'qv_hover_anim', [ 'label' => __( 'Hover Animation', 'mh-plug' ), 'type'  => Controls_Manager::HOVER_ANIMATION ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control( 'qv_transition', [
            'label'     => __( 'Transition Speed (s)', 'mh-plug' ),
            'type'      => Controls_Manager::SLIDER,
            'default'   => [ 'size' => 0.3 ],
            'selectors' => [ '{{WRAPPER}} .mh-product-grid .mh-action-btn.mh-quick-view-trigger' => 'transition: all {{SIZE}}s ease !important;' ],
            'separator' => 'before'
        ]);
        $this->end_controls_section();
    }

    protected function render_quick_view_button( $product_id, $settings ) {
        // Fallback for unset widget values
        $show_qv = isset( $settings['show_quick_view'] ) ? $settings['show_quick_view'] : 'yes';
        if ( $show_qv !== 'yes' ) return;

        $template_id = !empty( $settings['quick_view_template'] ) ? $settings['quick_view_template'] : '';
        $animation   = !empty( $settings['qv_hover_anim'] ) ? ' elementor-animation-' . $settings['qv_hover_anim'] : '';
        ?>
        <a href="#" class="mh-action-btn mh-quick-view-trigger<?php echo esc_attr( $animation ); ?>" 
           data-product-id="<?php echo esc_attr( $product_id ); ?>" 
           data-template-id="<?php echo esc_attr( $template_id ); ?>"
           title="<?php esc_html_e( 'Quick View', 'mh-plug' ); ?>"
           style="display:flex; align-items:center; justify-content:center;">
            <?php 
            // 🚀 FIX: Bulletproof Icon injection. Avoids Elementor render_icon glitches entirely.
            $icon = isset($settings['quick_view_icon']) ? $settings['quick_view_icon'] : [];
            if ( !empty( $icon['value'] ) ) {
                if ( isset($icon['library']) && $icon['library'] === 'svg' ) {
                    \Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
                } else {
                    echo '<i class="' . esc_attr( $icon['value'] ) . '" aria-hidden="true"></i>';
                }
            } else {
                echo '<i class="fas fa-shopping-bag" aria-hidden="true"></i>';
            }
            ?>
        </a>
        <?php
    }
}