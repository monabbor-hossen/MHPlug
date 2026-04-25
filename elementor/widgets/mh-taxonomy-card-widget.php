<?php
/**
 * MH Taxonomy Card Widget
 * Displays Categories, Brands, Tags, or Custom Content with WooCommerce Dependency Checks and a Call to Action Button.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Plug_Taxonomy_Card_Widget extends Widget_Base {

    public function get_name() { return 'mh_taxonomy_card'; }
    public function get_title() { return __( 'MH Taxonomy Card', 'mh-plug' ); }
    public function get_icon() { return 'eicon-image-box'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    // Helper to get taxonomy terms for the dropdowns
    private function get_term_options( $taxonomy ) {
        $options = [];
        if ( taxonomy_exists( $taxonomy ) ) {
            $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false, 'number' => 200 ] );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                foreach ( $terms as $term ) {
                    $options[ $term->term_id ] = $term->name . ' (ID: ' . $term->term_id . ')';
                }
            }
        }
        return $options;
    }

    protected function register_controls() {

        $source_options = [
            'custom'   => __( 'Custom Content', 'mh-plug' ),
            'category' => __( 'Post Category', 'mh-plug' ),
            'post_tag' => __( 'Post Tag', 'mh-plug' ),
        ];

        $taxonomies = [
            'category' => __( 'Select Post Category', 'mh-plug' ),
            'post_tag' => __( 'Select Post Tag', 'mh-plug' ),
        ];

        // Only add WooCommerce options if WooCommerce is actually installed and active
        if ( class_exists( 'WooCommerce' ) ) {
            $source_options['product_cat']   = __( 'Product Category', 'mh-plug' );
            $source_options['product_brand'] = __( 'Product Brand', 'mh-plug' );
            $source_options['product_tag']   = __( 'Product Tag', 'mh-plug' );

            $taxonomies['product_cat']   = __( 'Select Product Category', 'mh-plug' );
            $taxonomies['product_brand'] = __( 'Select Product Brand', 'mh-plug' );
            $taxonomies['product_tag']   = __( 'Select Product Tag', 'mh-plug' );
        }

        /* ── CONTENT: SOURCE ── */
        $this->start_controls_section( 'section_content', [
            'label' => __( 'Card Content', 'mh-plug' ),
        ] );

        $this->add_control( 'source_type', [
            'label'   => __( 'Data Source', 'mh-plug' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'custom',
            'options' => $source_options,
        ] );

        // Loop through our dynamically generated taxonomies list
        foreach ( $taxonomies as $tax_slug => $tax_label ) {
            $this->add_control( $tax_slug . '_id', [
                'label'     => $tax_label,
                'type'      => Controls_Manager::SELECT2,
                'options'   => $this->get_term_options( $tax_slug ),
                'condition' => [ 'source_type' => $tax_slug ],
            ] );
        }

        // Custom Inputs
        $this->add_control( 'custom_title', [
            'label'     => __( 'Title', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Card Title', 'mh-plug' ),
            'condition' => [ 'source_type' => 'custom' ],
        ] );

        $this->add_control( 'custom_desc', [
            'label'     => __( 'Description', 'mh-plug' ),
            'type'      => Controls_Manager::TEXTAREA,
            'default'   => __( 'Enter your description here...', 'mh-plug' ),
            'condition' => [ 'source_type' => 'custom' ],
        ] );

        $this->add_control( 'custom_image', [
            'label'     => __( 'Background Image', 'mh-plug' ),
            'type'      => Controls_Manager::MEDIA,
            'condition' => [ 'source_type' => 'custom' ],
        ] );

        $this->add_control( 'custom_link', [
            'label'     => __( 'Custom Link', 'mh-plug' ),
            'type'      => Controls_Manager::URL,
            'condition' => [ 'source_type' => 'custom' ],
            'description' => __( 'If using a taxonomy, the link is generated automatically.', 'mh-plug' ),
        ] );

        $this->add_control( 'show_description', [
            'label'        => __( 'Show Description', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
            'separator'    => 'before',
        ] );

        // 🚀 NEW: Button Controls
        $this->add_control( 'show_button', [
            'label'        => __( 'Show Button', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'no',
            'separator'    => 'before',
        ] );

        $this->add_control( 'button_text', [
            'label'     => __( 'Button Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Shop Now', 'mh-plug' ),
            'condition' => [ 'show_button' => 'yes' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: BOX LAYOUT & POSITION ── */
        $this->start_controls_section( 'section_style_box', [
            'label' => __( 'Box & Content Position', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'min_height', [
            'label'      => __( 'Card Minimum Height', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vh', 'em' ],
            'range'      => [ 'px' => [ 'min' => 100, 'max' => 800 ] ],
            'default'    => [ 'size' => 300, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tax-card' => 'min-height: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'content_padding', [
            'label'      => __( 'Content Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tax-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_control( 'heading_alignment', [
            'label'     => __( 'Content Position', 'mh-plug' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'vertical_align', [
            'label'     => __( 'Vertical Position', 'mh-plug' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => __( 'Top', 'mh-plug' ), 'icon' => 'eicon-v-align-top' ],
                'center'     => [ 'title' => __( 'Middle', 'mh-plug' ), 'icon' => 'eicon-v-align-middle' ],
                'flex-end'   => [ 'title' => __( 'Bottom', 'mh-plug' ), 'icon' => 'eicon-v-align-bottom' ],
            ],
            'default'   => 'center',
            'selectors' => [ '{{WRAPPER}} .mh-tax-card' => 'justify-content: {{VALUE}} !important;' ],
        ] );

        $this->add_responsive_control( 'horizontal_align', [
            'label'        => __( 'Horizontal Position', 'mh-plug' ),
            'type'         => Controls_Manager::CHOOSE,
            'options'      => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'default'      => 'center',
            'prefix_class' => 'mh-card-align%s-', 
        ] );

        $this->add_responsive_control( 'border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'separator'  => 'before',
            'selectors'  => [ '{{WRAPPER}} .mh-tax-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'box_shadow',
            'selector' => '{{WRAPPER}} .mh-tax-card',
        ] );

        $this->add_control( 'hover_animation', [
            'label'        => __( 'Enable Image Zoom on Hover', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'transition_time', [
            'label'      => __( 'Hover Transition Time (s)', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 's' ],
            'range'      => [ 's' => [ 'min' => 0.1, 'max' => 2.0, 'step' => 0.1 ] ],
            'default'    => [ 'size' => 0.4, 'unit' => 's' ],
            'selectors'  => [ 
                '{{WRAPPER}} .mh-tax-card-bg' => 'transition-duration: {{SIZE}}s;',
                '{{WRAPPER}} .mh-tax-card-overlay' => 'transition-duration: {{SIZE}}s;',
                '{{WRAPPER}} .mh-tax-card-title' => 'transition-duration: {{SIZE}}s;',
                '{{WRAPPER}} .mh-tax-card-btn' => 'transition-duration: {{SIZE}}s;'
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: BACKGROUND & OVERLAY ── */
        $this->start_controls_section( 'section_style_bg', [
            'label' => __( 'Background & Overlay', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'heading_fallback_bg', [
            'label' => __( 'Fallback Background', 'mh-plug' ),
            'type' => Controls_Manager::HEADING,
            'description' => __( 'This background applies if the category has no image.', 'mh-plug' ),
        ] );

        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'fallback_background',
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .mh-tax-card-bg-fallback',
        ] );

        $this->add_control( 'heading_overlay', [
            'label' => __( 'Color Overlay', 'mh-plug' ),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->start_controls_tabs( 'tabs_overlay' );
        
        $this->start_controls_tab( 'tab_overlay_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'overlay_bg',
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .mh-tax-card-overlay',
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_overlay_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'overlay_bg_hover',
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .mh-tax-card:hover .mh-tax-card-overlay',
        ] );
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();

        /* ── STYLE: CONTENT TYPOGRAPHY ── */
        $this->start_controls_section( 'section_style_content', [
            'label' => __( 'Typography & Colors', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'heading_title', [ 'label' => __( 'Title', 'mh-plug' ), 'type' => Controls_Manager::HEADING ] );
        $this->add_control( 'title_color', [ 'label' => __( 'Title Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-tax-card-title' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'title_color_hover', [ 'label' => __( 'Title Color (Hover)', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .mh-tax-card:hover .mh-tax-card-title' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .mh-tax-card-title' ] );
        $this->add_responsive_control( 'title_margin', [ 'label' => __( 'Margin', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .mh-tax-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->add_control( 'heading_desc', [ 'label' => __( 'Description', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'desc_color', [ 'label' => __( 'Description Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#eeeeee', 'selectors' => [ '{{WRAPPER}} .mh-tax-card-desc' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .mh-tax-card-desc' ] );
        $this->add_responsive_control( 'desc_margin', [ 'label' => __( 'Margin', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .mh-tax-card-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        $this->end_controls_section();


        /* 🚀 NEW: STYLE: BUTTON ── */
        $this->start_controls_section( 'section_style_button', [
            'label'     => __( 'Button Styles', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_button' => 'yes' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'button_typography',
            'selector' => '{{WRAPPER}} .mh-tax-card-btn',
        ] );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab( 'tab_button_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'button_text_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-tax-card-btn' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'button_bg_color', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-tax-card-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'button_border',
            'selector'  => '{{WRAPPER}} .mh-tax-card-btn',
        ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'button_shadow',
            'selector' => '{{WRAPPER}} .mh-tax-card-btn',
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_button_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'button_text_color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-tax-card:hover .mh-tax-card-btn' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'button_bg_color_hover', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2293e9',
            'selectors' => [ '{{WRAPPER}} .mh-tax-card:hover .mh-tax-card-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'button_border_hover',
            'selector'  => '{{WRAPPER}} .mh-tax-card:hover .mh-tax-card-btn',
        ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'button_shadow_hover',
            'selector' => '{{WRAPPER}} .mh-tax-card:hover .mh-tax-card-btn',
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control( 'button_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 12, 'right' => 25, 'bottom' => 12, 'left' => 25, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tax-card-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );

        $this->add_responsive_control( 'button_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tax-card-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        
        $this->add_responsive_control( 'button_margin', [
            'label'      => __( 'Margin', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 15, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-tax-card-btn-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $source = $settings['source_type'];
        $title  = '';
        $desc   = '';
        $link   = '';
        $bg_url = '';

        if ( $source === 'custom' ) {
            $title  = $settings['custom_title'];
            $desc   = $settings['custom_desc'];
            $link   = ! empty( $settings['custom_link']['url'] ) ? $settings['custom_link']['url'] : '';
            $bg_url = ! empty( $settings['custom_image']['url'] ) ? $settings['custom_image']['url'] : '';
        } else {
            $term_id = ! empty( $settings[ $source . '_id' ] ) ? intval( $settings[ $source . '_id' ] ) : 0;
            if ( $term_id ) {
                $term = get_term( $term_id );
                if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
                    $title = $term->name;
                    $desc  = $term->description;
                    $link  = get_term_link( $term );

                    if ( in_array( $source, [ 'product_cat', 'product_brand' ] ) ) {
                        $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );
                        if ( $thumbnail_id ) {
                            $bg_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
                        }
                    }
                }
            }
        }

        $zoom_class = $settings['hover_animation'] === 'yes' ? 'mh-hover-zoom' : '';
        $inline_bg  = $bg_url ? 'background-image: url(' . esc_url( $bg_url ) . ');' : '';

        $target = ($source === 'custom' && ! empty($settings['custom_link']['is_external'])) ? ' target="_blank"' : '';
        $nofollow = ($source === 'custom' && ! empty($settings['custom_link']['nofollow'])) ? ' rel="nofollow"' : '';
        ?>

        <style>
            .mh-tax-card { position: relative; display: flex; flex-direction: column; width: 100%; overflow: hidden; box-sizing: border-box; }
            .mh-tax-card-bg-fallback { position: absolute; inset: 0; z-index: 1; }
            .mh-tax-card-bg { position: absolute; inset: 0; background-size: cover; background-position: center; transition-property: transform; transition-timing-function: cubic-bezier(0.25, 0.8, 0.25, 1); z-index: 2; }
            .mh-tax-card.mh-hover-zoom:hover .mh-tax-card-bg { transform: scale(1.1); }
            .mh-tax-card-overlay { position: absolute; inset: 0; z-index: 3; transition-property: background-color; transition-timing-function: ease; background-color: rgba(0,0,0,0.4); }
            
            .mh-tax-card-content { position: relative; z-index: 4; width: 100%; display: flex; flex-direction: column; pointer-events: none; }
            
            .mh-tax-card-title { margin: 0 0 10px 0; transition-property: color; transition-timing-function: ease; }
            .mh-tax-card-desc { margin: 0; }
            
            .mh-tax-card-btn-wrap { display: inline-block; pointer-events: auto; z-index: 11; position: relative; }
            .mh-tax-card-btn { display: inline-block; text-decoration: none; font-weight: 600; text-align: center; border: none; cursor: pointer; transition-property: color, background-color, border-color, box-shadow; transition-timing-function: ease; }
            
            /* The absolute overlay makes the entire box clickable EXCEPT the button if it exists */
            .mh-tax-card-link-overlay { position: absolute; inset: 0; z-index: 10; text-decoration: none; }

            /* =======================================
               BULLETPROOF DYNAMIC RESPONSIVE ALIGNMENT ENGINE
               ======================================= */
            <?php
            $breakpoints = [
                'desktop' => [ 'prefix' => '.mh-card-align', 'media' => '' ],
                'tablet'  => [ 'prefix' => '.mh-card-align-tablet', 'media' => '@media (max-width: 1024px)' ],
                'mobile'  => [ 'prefix' => '.mh-card-align-mobile', 'media' => '@media (max-width: 767px)' ],
            ];

            foreach ( $breakpoints as $bp ) {
                if ( $bp['media'] ) echo $bp['media'] . " {\n";
                $p = $bp['prefix'];
                
                /* LEFT */
                echo "{$p}-left .mh-tax-card-content { align-items: flex-start !important; text-align: left !important; }\n";
                echo "{$p}-left .mh-tax-card-content > * { text-align: left !important; }\n"; 
                
                /* CENTER */
                echo "{$p}-center .mh-tax-card-content { align-items: center !important; text-align: center !important; }\n";
                echo "{$p}-center .mh-tax-card-content > * { text-align: center !important; }\n";
                
                /* RIGHT */
                echo "{$p}-right .mh-tax-card-content { align-items: flex-end !important; text-align: right !important; }\n";
                echo "{$p}-right .mh-tax-card-content > * { text-align: right !important; }\n";
                
                if ( $bp['media'] ) echo "}\n";
            }
            ?>
        </style>

        <div class="mh-tax-card <?php echo esc_attr( $zoom_class ); ?>">
            
            <div class="mh-tax-card-bg-fallback"></div>

            <?php if ( $inline_bg ) : ?>
                <div class="mh-tax-card-bg" style="<?php echo esc_attr( $inline_bg ); ?>"></div>
            <?php endif; ?>

            <div class="mh-tax-card-overlay"></div>

            <div class="mh-tax-card-content">
                <?php if ( ! empty( $title ) ) : ?>
                    <h3 class="mh-tax-card-title"><?php echo esc_html( $title ); ?></h3>
                <?php endif; ?>

                <?php if ( ! empty( $desc ) && $settings['show_description'] === 'yes' ) : ?>
                    <div class="mh-tax-card-desc"><?php echo wp_kses_post( $desc ); ?></div>
                <?php endif; ?>

                <?php if ( $settings['show_button'] === 'yes' && ! empty( $link ) ) : ?>
                    <div class="mh-tax-card-btn-wrap">
                        <a href="<?php echo esc_url( $link ); ?>" class="mh-tax-card-btn" <?php echo $target . $nofollow; ?>>
                            <?php echo esc_html( $settings['button_text'] ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php 
            // The Box Link Overlay (ensures the whole card is clickable if they want)
            if ( ! empty( $link ) ) : ?>
                <a href="<?php echo esc_url( $link ); ?>" class="mh-tax-card-link-overlay" <?php echo $target . $nofollow; ?> aria-label="<?php echo esc_attr( $title ); ?>"></a>
            <?php endif; ?>

        </div>

        <?php
    }
}