<?php
/**
 * MH Blog Post Widget
 * Displays a highly customizable, responsive grid of blog posts with a seamless AJAX-style Load More button.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Blog_Post_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_blog_post'; }
    public function get_title() { return __( 'MH Blog Post Grid', 'mh-plug' ); }
    public function get_icon() { return 'eicon-posts-grid'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    // Helper to fetch categories
    private function get_post_categories() {
        $categories = get_categories( [ 'hide_empty' => false ] );
        $options = [];
        foreach ( $categories as $category ) {
            $options[ $category->term_id ] = $category->name;
        }
        return $options;
    }

    protected function register_controls() {
        
        // ----------------------------------------------------
        // CONTENT: QUERY SETTINGS
        // ----------------------------------------------------
        $this->start_controls_section( 'section_query', [ 'label' => __( 'Query Settings', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        
        $this->add_control( 'categories', [
            'label'       => __( 'Categories', 'mh-plug' ),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $this->get_post_categories(),
            'multiple'    => true,
            'label_block' => true,
            'description' => __( 'Leave blank to show all categories.', 'mh-plug' ),
        ] );

        $this->add_control( 'posts_per_page', [
            'label'   => __( 'Posts Per Page', 'mh-plug' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 8,
            'min'     => 1,
            'max'     => 100,
        ] );

        $this->add_responsive_control( 'columns', [
            'label'          => __( 'Columns', 'mh-plug' ),
            'type'           => Controls_Manager::SELECT,
            'default'        => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options'        => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
            'selectors'      => [ '{{WRAPPER}} .mh-blog-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ],
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // CONTENT: CARD ELEMENTS
        // ----------------------------------------------------
        $this->start_controls_section( 'section_elements', [ 'label' => __( 'Card Elements', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        
        $this->add_control( 'show_image', [ 'label' => __( 'Show Featured Image', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_badge', [ 'label' => __( 'Show Category Badge', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta', [ 'label' => __( 'Show Meta (Date/Author)', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_excerpt', [ 'label' => __( 'Show Excerpt', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        
        $this->add_control( 'excerpt_length', [
            'label'     => __( 'Excerpt Length (Words)', 'mh-plug' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 15,
            'condition' => [ 'show_excerpt' => 'yes' ],
        ] );

        $this->add_control( 'read_more_text', [
            'label'   => __( 'Read More Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Read More', 'mh-plug' ),
            'separator' => 'before',
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // CONTENT: LOAD MORE BUTTON
        // ----------------------------------------------------
        $this->start_controls_section( 'section_load_more', [ 'label' => __( 'Load More Button', 'mh-plug' ), 'tab' => Controls_Manager::TAB_CONTENT ] );
        
        $this->add_control( 'enable_load_more', [
            'label'       => __( 'Enable Load More', 'mh-plug' ),
            'type'        => Controls_Manager::SWITCHER,
            'default'     => 'yes',
            'description' => __( 'Shows a button at the bottom if there are more posts to display.', 'mh-plug' ),
        ] );

        $this->add_control( 'load_more_text', [
            'label'     => __( 'Button Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Load More', 'mh-plug' ),
            'condition' => [ 'enable_load_more' => 'yes' ],
        ] );

        $this->add_control( 'loading_text', [
            'label'     => __( 'Loading Text', 'mh-plug' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Loading...', 'mh-plug' ),
            'condition' => [ 'enable_load_more' => 'yes' ],
        ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: GRID & CARD CONTAINER
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_card', [ 'label' => __( 'Grid & Card', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        
        $this->add_responsive_control( 'grid_gap', [ 'label' => __( 'Grid Gap', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 30 ], 'selectors' => [ '{{WRAPPER}} .mh-blog-grid' => 'gap: {{SIZE}}px;' ] ] );
        $this->add_control( 'card_bg', [ 'label' => __( 'Card Background', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-blog-card' => 'background-color: {{VALUE}};' ] ] );
        $this->add_responsive_control( 'card_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .mh-blog-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'card_border', 'selector' => '{{WRAPPER}} .mh-blog-card' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow', 'selector' => '{{WRAPPER}} .mh-blog-card' ] );
        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'card_shadow_hover', 'label' => __( 'Hover Box Shadow', 'mh-plug' ), 'selector' => '{{WRAPPER}} .mh-blog-card:hover' ] );
        
        $this->add_control( 'hover_lift', [ 'label' => __( 'Enable Hover Lift Effect', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'selectors' => [ '{{WRAPPER}} .mh-blog-card:hover' => 'transform: translateY(-8px);' ] ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: IMAGE
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_image', [ 'label' => __( 'Image', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_image' => 'yes' ] ] );
        
        $this->add_control( 'image_aspect_ratio', [
            'label' => __( 'Aspect Ratio', 'mh-plug' ), 'type' => Controls_Manager::SELECT, 'default' => '56.25%', // 16:9
            'options' => [ '100%' => '1:1 Square', '75%' => '4:3 Landscape', '56.25%' => '16:9 Widescreen', '133%' => '3:4 Portrait' ],
            'selectors' => [ '{{WRAPPER}} .mh-blog-image' => 'padding-bottom: {{VALUE}};' ]
        ] );
        $this->add_control( 'hover_zoom', [ 'label' => __( 'Enable Hover Zoom Effect', 'mh-plug' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        
        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: CONTENT (TITLE, META, EXCERPT)
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_content', [ 'label' => __( 'Content (Typography)', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE ] );
        
        $this->add_responsive_control( 'content_padding', [ 'label' => __( 'Content Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em', '%' ], 'default' => [ 'top' => 25, 'right' => 25, 'bottom' => 25, 'left' => 25, 'isLinked' => true ], 'selectors' => [ '{{WRAPPER}} .mh-blog-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        
        // Category Badge
        $this->add_control( 'heading_cat', [ 'label' => __( 'Category Badge', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'cat_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .mh-blog-cat' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'cat_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-blog-cat' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'cat_typo', 'selector' => '{{WRAPPER}} .mh-blog-cat' ] );
        $this->add_responsive_control( 'cat_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => [ '{{WRAPPER}} .mh-blog-cat' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

        // Title
        $this->add_control( 'heading_title', [ 'label' => __( 'Title', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'title_color', [ 'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ '{{WRAPPER}} .mh-blog-title a' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'title_hover_color', [ 'label' => __( 'Hover Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-blog-title a:hover' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .mh-blog-title' ] );
        $this->add_responsive_control( 'title_margin', [ 'label' => __( 'Margin Bottom', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'selectors' => [ '{{WRAPPER}} .mh-blog-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ] ] );

        // Meta
        $this->add_control( 'heading_meta', [ 'label' => __( 'Meta Info', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'meta_color', [ 'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .mh-blog-meta' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'meta_typo', 'selector' => '{{WRAPPER}} .mh-blog-meta' ] );

        // Excerpt
        $this->add_control( 'heading_excerpt', [ 'label' => __( 'Excerpt', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'excerpt_color', [ 'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#666666', 'selectors' => [ '{{WRAPPER}} .mh-blog-excerpt' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'excerpt_typo', 'selector' => '{{WRAPPER}} .mh-blog-excerpt' ] );

        // Read More
        $this->add_control( 'heading_read_more', [ 'label' => __( 'Read More Link', 'mh-plug' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
        $this->add_control( 'read_more_color', [ 'label' => __( 'Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ '{{WRAPPER}} .mh-blog-read-more' => 'color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'read_more_typo', 'selector' => '{{WRAPPER}} .mh-blog-read-more' ] );

        $this->end_controls_section();

        // ----------------------------------------------------
        // STYLE: LOAD MORE BUTTON
        // ----------------------------------------------------
        $this->start_controls_section( 'section_style_load_more', [ 'label' => __( 'Load More Button', 'mh-plug' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'enable_load_more' => 'yes' ] ] );
        
        $btn_selector = '{{WRAPPER}} .mh-load-more-btn';
        
        $this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'lm_typo', 'selector' => $btn_selector ] );
        $this->add_responsive_control( 'lm_padding', [ 'label' => __( 'Padding', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ], 'default' => [ 'top' => 12, 'right' => 30, 'bottom' => 12, 'left' => 30, 'isLinked' => false ], 'selectors' => [ $btn_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'lm_radius', [ 'label' => __( 'Border Radius', 'mh-plug' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%' ], 'default' => [ 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'isLinked' => true ], 'selectors' => [ $btn_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $this->add_responsive_control( 'lm_margin_top', [ 'label' => __( 'Margin Top', 'mh-plug' ), 'type' => Controls_Manager::SLIDER, 'default' => [ 'size' => 40 ], 'selectors' => [ '{{WRAPPER}} .mh-load-more-wrap' => 'margin-top: {{SIZE}}{{UNIT}};' ] ] );

        $this->start_controls_tabs( 'tabs_lm_style' );
        $this->start_controls_tab( 'tab_lm_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'lm_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ $btn_selector => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'lm_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => [ $btn_selector => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'lm_border', 'selector' => $btn_selector ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_lm_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'lm_hover_color', [ 'label' => __( 'Text Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ $btn_selector . ':hover' => 'color: {{VALUE}};' ] ] );
        $this->add_control( 'lm_hover_bg', [ 'label' => __( 'Background Color', 'mh-plug' ), 'type' => Controls_Manager::COLOR, 'default' => '#2293e9', 'selectors' => [ $btn_selector . ':hover' => 'background-color: {{VALUE}};' ] ] );
        $this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'lm_hover_border', 'selector' => $btn_selector . ':hover' ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        // Determine current page for pagination
        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } elseif ( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        $args = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => $settings['posts_per_page'],
            'paged'          => $paged,
        ];

        if ( ! empty( $settings['categories'] ) ) {
            $args['category__in'] = $settings['categories'];
        }

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) {
            echo '<p>' . esc_html__( 'No posts found.', 'mh-plug' ) . '</p>';
            return;
        }

        $zoom_class = ( $settings['show_image'] === 'yes' && $settings['hover_zoom'] === 'yes' ) ? 'mh-zoom-enabled' : '';
        $css = "
            .mh-blog-wrapper { position: relative; width: 100%; display: block; }
            .mh-blog-grid { display: grid; width: 100%; }
            .mh-blog-card { position: relative; display: flex; flex-direction: column; overflow: hidden; transition: all 0.3s ease; }
            
            /* Image Area */
            .mh-blog-image { position: relative; width: 100%; overflow: hidden; display: block; }
            .mh-blog-image img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
            .mh-zoom-enabled .mh-blog-card:hover .mh-blog-image img { transform: scale(1.08); }
            
            /* Category Badge */
            .mh-blog-cat { position: absolute; top: 15px; left: 15px; z-index: 2; padding: 4px 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; line-height: 1; pointer-events: none; }
            
            /* Content Area */
            .mh-blog-content { display: flex; flex-direction: column; flex-grow: 1; }
            .mh-blog-meta { display: flex; align-items: center; gap: 15px; font-size: 13px; margin-bottom: 10px; }
            .mh-blog-meta i { margin-right: 5px; color: inherit; }
            .mh-blog-title { margin: 0 0 12px 0; line-height: 1.3; }
            .mh-blog-title a { text-decoration: none; transition: color 0.3s ease; }
            .mh-blog-excerpt { margin: 0 0 15px 0; flex-grow: 1; }
            
            /* Read More */
            .mh-blog-read-more { display: inline-flex; align-items: center; font-weight: 600; text-decoration: none; transition: 0.3s; margin-top: auto; }
            .mh-blog-read-more i { margin-left: 6px; font-size: 14px; transition: transform 0.3s ease; }
            .mh-blog-read-more:hover i { transform: translateX(5px); }

            /* Load More Button */
            .mh-load-more-wrap { display: flex; justify-content: center; width: 100%; }
            .mh-load-more-btn { display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.3s ease; text-decoration: none; font-weight: 600; }
            .mh-load-more-btn.loading { opacity: 0.7; pointer-events: none; }
        ";
        wp_register_style( 'mh-blog-post-style', false );
        wp_enqueue_style( 'mh-blog-post-style' );
        wp_add_inline_style( 'mh-blog-post-style', $css );
        ?>

        <div class="mh-blog-wrapper" data-widget-id="<?php echo esc_attr( $widget_id ); ?>">
            <div class="mh-blog-grid <?php echo esc_attr( $zoom_class ); ?>">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <article class="mh-blog-card">
                        
                        <?php if ( $settings['show_image'] === 'yes' && has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="mh-blog-image">
                                <?php the_post_thumbnail( 'large' ); ?>
                                <?php if ( $settings['show_badge'] === 'yes' ) : 
                                    $categories = get_the_category();
                                    if ( ! empty( $categories ) ) {
                                        echo '<span class="mh-blog-cat">' . esc_html( $categories[0]->name ) . '</span>';
                                    }
                                endif; ?>
                            </a>
                        <?php endif; ?>

                        <div class="mh-blog-content">
                            <?php if ( $settings['show_meta'] === 'yes' ) : ?>
                                <div class="mh-blog-meta">
                                    <span class="mh-meta-date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                                    <span class="mh-meta-author"><i class="far fa-user"></i> <?php the_author(); ?></span>
                                </div>
                            <?php endif; ?>

                            <h3 class="mh-blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <?php if ( $settings['show_excerpt'] === 'yes' ) : ?>
                                <div class="mh-blog-excerpt">
                                    <?php echo wp_trim_words( get_the_excerpt(), $settings['excerpt_length'], '...' ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $settings['read_more_text'] ) ) : ?>
                                <a href="<?php the_permalink(); ?>" class="mh-blog-read-more">
                                    <?php echo esc_html( $settings['read_more_text'] ); ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                    </article>
                <?php endwhile; ?>
            </div>

            <?php 
            // 🚀 INTELLIGENT LOAD MORE LOGIC
            if ( $settings['enable_load_more'] === 'yes' && $query->max_num_pages > 1 && $paged < $query->max_num_pages ) : 
                $next_page_url = get_pagenum_link( $paged + 1 );
            ?>
                <div class="mh-load-more-wrap">
                    <button class="mh-load-more-btn" 
                            data-next-url="<?php echo esc_url( $next_page_url ); ?>" 
                            data-loading-text="<?php echo esc_attr( $settings['loading_text'] ); ?>">
                        <?php echo esc_html( $settings['load_more_text'] ); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php wp_reset_postdata(); ?>

        <?php if ( $settings['enable_load_more'] === 'yes' && $query->max_num_pages > 1 ) : 
        $js = "
        jQuery(document).ready(function($) {
            var \$wrapper = $('.mh-blog-wrapper[data-widget-id=\"" . esc_js( $widget_id ) . "\"]');
            var \$grid    = \$wrapper.find('.mh-blog-grid');
            var \$btnWrap = \$wrapper.find('.mh-load-more-wrap');
            var \$btn     = \$btnWrap.find('.mh-load-more-btn');

            \$btn.on('click', function(e) {
                e.preventDefault();
                if ( \$btn.hasClass('loading') ) return;

                var nextUrl     = \$btn.attr('data-next-url');
                var currentText = \$btn.text();
                var loadingText = \$btn.attr('data-loading-text');

                \$btn.addClass('loading').text(loadingText);

                $.get(nextUrl, function(data) {
                    var \$html = $(data);
                    var \$newWrapper = \$html.find('.mh-blog-wrapper[data-widget-id=\"" . esc_js( $widget_id ) . "\"]');
                    var \$newItems   = \$newWrapper.find('.mh-blog-card');
                    var \$newBtn     = \$newWrapper.find('.mh-load-more-btn');

                    if ( \$newItems.length ) {
                        // Append new items smoothly
                        \$newItems.css('opacity', 0);
                        \$grid.append(\$newItems);
                        \$newItems.animate({ opacity: 1 }, 500);

                        // Update Button URL or Remove if no more pages
                        if ( \$newBtn.length ) {
                            \$btn.attr('data-next-url', \$newBtn.attr('data-next-url'));
                            \$btn.removeClass('loading').text(currentText);
                        } else {
                            \$btnWrap.slideUp(300, function() { $(this).remove(); });
                        }
                    } else {
                        \$btnWrap.remove();
                    }
                }).fail(function() {
                    \$btn.removeClass('loading').text(currentText); // Revert on failure
                });
            });
        });
        ";
        wp_add_inline_script( 'jquery-core', $js );
        endif; ?>
        
        <?php
    }
}