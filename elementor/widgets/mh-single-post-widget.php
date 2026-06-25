<?php
/**
 * MH Single Post Widget
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

class MH_Plug_Single_Post_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'mh_single_post'; }
    public function get_title() { return __( 'MH Single Post', 'mh-plug-ecommerce-builder-widgets' ); }
    public function get_icon() { return 'eicon-single-post'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {
        
        $this->start_controls_section( 'section_layout', [
            'label' => __( 'Layout & Elements', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'layout', [
            'label' => __( 'Select Layout', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SELECT,
            'default' => 'layout-1',
            'options' => [
                'layout-1' => __( 'Layout 1 (Classic)', 'mh-plug-ecommerce-builder-widgets' ),
                'layout-2' => __( 'Layout 2 (Hero Cover)', 'mh-plug-ecommerce-builder-widgets' ),
                'layout-3' => __( 'Layout 3 (Split View)', 'mh-plug-ecommerce-builder-widgets' ),
            ],
        ] );

        $this->add_control( 'show_title', [ 'label' => __( 'Show Title', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_meta', [ 'label' => __( 'Show Meta Info', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_image', [ 'label' => __( 'Show Featured Image', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_content', [ 'label' => __( 'Show Post Content', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_author_box', [ 'label' => __( 'Show Author Box', 'mh-plug-ecommerce-builder-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );

        $this->end_controls_section();

        // Style Sections
        $this->start_controls_section( 'section_style_general', [
            'label' => __( 'General Wrapper', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ] );
        $this->add_responsive_control( 'wrapper_padding', [
            'label' => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [ '{{WRAPPER}} .mh-single-post-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        $this->add_control( 'wrapper_bg', [
            'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-single-post-wrapper' => 'background-color: {{VALUE}};' ],
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_style_title', [
            'label' => __( 'Title', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_title' => 'yes' ],
        ] );
        $this->add_control( 'title_color', [
            'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-title' => 'color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'title_typo',
            'selector' => '{{WRAPPER}} .mh-sp-title',
        ] );
        $this->add_responsive_control( 'title_spacing', [
            'label' => __( 'Spacing Bottom', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'selectors' => [ '{{WRAPPER}} .mh-sp-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_style_meta', [
            'label' => __( 'Meta Info', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_meta' => 'yes' ],
        ] );
        $this->add_control( 'meta_color', [
            'label' => __( 'Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-meta, {{WRAPPER}} .mh-sp-meta a' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'meta_icon_color', [
            'label' => __( 'Icon Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-meta i' => 'color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'meta_typo',
            'selector' => '{{WRAPPER}} .mh-sp-meta',
        ] );
        $this->add_responsive_control( 'meta_spacing', [
            'label' => __( 'Spacing Bottom', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'selectors' => [ '{{WRAPPER}} .mh-sp-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_style_image', [
            'label' => __( 'Image', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_image' => 'yes' ],
        ] );
        $this->add_responsive_control( 'image_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [ '{{WRAPPER}} .mh-sp-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        $this->add_responsive_control( 'image_spacing', [
            'label' => __( 'Spacing Bottom', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'selectors' => [ '{{WRAPPER}} .mh-sp-image' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ] );
        // specific for layout 2
        $this->add_control( 'layout2_overlay', [
            'label' => __( 'Overlay Color (Layout 2)', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-hero-overlay' => 'background-color: {{VALUE}};' ],
            'condition' => [ 'layout' => 'layout-2' ],
        ] );
        $this->add_responsive_control( 'layout2_height', [
            'label' => __( 'Hero Height (Layout 2)', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [ 'px' => ['min' => 200, 'max' => 1000] ],
            'selectors' => [ '{{WRAPPER}} .mh-sp-hero' => 'min-height: {{SIZE}}{{UNIT}};' ],
            'condition' => [ 'layout' => 'layout-2' ],
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_style_content', [
            'label' => __( 'Post Content', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_content' => 'yes' ],
        ] );
        $this->add_control( 'content_color', [
            'label' => __( 'Text Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-content' => 'color: {{VALUE}};' ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'content_typo',
            'selector' => '{{WRAPPER}} .mh-sp-content',
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'section_style_author', [
            'label' => __( 'Author Box', 'mh-plug-ecommerce-builder-widgets' ),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_author_box' => 'yes' ],
        ] );
        $this->add_control( 'author_bg', [
            'label' => __( 'Background Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-author-box' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'author_name_color', [
            'label' => __( 'Name Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-author-name' => 'color: {{VALUE}};' ],
        ] );
        $this->add_control( 'author_bio_color', [
            'label' => __( 'Bio Color', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .mh-sp-author-bio' => 'color: {{VALUE}};' ],
        ] );
        $this->add_responsive_control( 'author_padding', [
            'label' => __( 'Padding', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [ '{{WRAPPER}} .mh-sp-author-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        $this->add_responsive_control( 'author_radius', [
            'label' => __( 'Border Radius', 'mh-plug-ecommerce-builder-widgets' ),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [ '{{WRAPPER}} .mh-sp-author-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $args = [ 'post_type' => 'post', 'posts_per_page' => 1 ];
            $query = new \WP_Query( $args );
            if ( $query->have_posts() ) {
                $query->the_post();
            } else {
                echo '<div style="padding: 20px; text-align: center;">Please create a post to preview this widget.</div>';
                return;
            }
        } elseif ( ! is_single() && ! is_singular( 'post' ) ) {
            return;
        }

        global $post;

        $layout = $settings['layout'];
        $has_image = has_post_thumbnail();
        $image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );

        $css = "
            .mh-single-post-wrapper { position: relative; width: 100%; display: block; }
            .mh-sp-meta { display: flex; flex-wrap: wrap; gap: 15px; font-size: 14px; margin-bottom: 20px; color: #666; }
            .mh-sp-meta i { margin-right: 5px; color: #2293e9; }
            .mh-sp-meta a { text-decoration: none; color: inherit; transition: 0.3s; }
            .mh-sp-meta a:hover { color: #2293e9; }
            .mh-sp-title { font-size: 2.5em; margin: 0 0 15px; font-weight: 700; line-height: 1.2; }
            .mh-sp-image { margin-bottom: 30px; width: 100%; overflow: hidden; }
            .mh-sp-image img { width: 100%; height: auto; display: block; border-radius: 8px; }
            .mh-sp-content { font-size: 16px; line-height: 1.8; color: #444; margin-bottom: 40px; }
            .mh-sp-content p { margin-bottom: 20px; }
            .mh-sp-content img { max-width: 100%; height: auto; border-radius: 6px; }

            /* Author Box */
            .mh-sp-author-box { display: flex; align-items: center; gap: 20px; padding: 30px; background: #f9f9f9; border-radius: 10px; margin-top: 40px; }
            .mh-sp-author-avatar img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
            .mh-sp-author-info { flex: 1; }
            .mh-sp-author-name { margin: 0 0 5px; font-size: 20px; font-weight: 600; }
            .mh-sp-author-bio { margin: 0; font-size: 15px; color: #555; }

            /* Layout 2: Hero Cover */
            .mh-sp-layout-2 .mh-sp-hero { position: relative; width: 100%; min-height: 450px; background-size: cover; background-position: center; display: flex; align-items: flex-end; padding: 50px; border-radius: 12px; margin-bottom: 40px; overflow: hidden; }
            .mh-sp-hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%); z-index: 1; }
            .mh-sp-hero-content { position: relative; z-index: 2; color: #fff; width: 100%; max-width: 800px; }
            .mh-sp-layout-2 .mh-sp-title, .mh-sp-layout-2 .mh-sp-meta, .mh-sp-layout-2 .mh-sp-meta a { color: #fff; }
            .mh-sp-layout-2 .mh-sp-meta i { color: #fff; opacity: 0.8; }

            /* Layout 3: Split View */
            .mh-sp-layout-3 { display: flex; flex-wrap: wrap; gap: 40px; }
            .mh-sp-layout-3 .mh-sp-left { width: calc(45% - 20px); }
            .mh-sp-layout-3 .mh-sp-right { width: calc(55% - 20px); }
            .mh-sp-layout-3 .mh-sp-image { position: sticky; top: 30px; }
            @media (max-width: 768px) {
                .mh-sp-layout-3 .mh-sp-left, .mh-sp-layout-3 .mh-sp-right { width: 100%; }
                .mh-sp-layout-3 .mh-sp-image { position: relative; top: 0; }
                .mh-sp-layout-2 .mh-sp-hero { padding: 30px 20px; min-height: 300px; }
            }
        ";
        wp_register_style( 'mh-single-post-style', false );
        wp_enqueue_style( 'mh-single-post-style' );
        wp_add_inline_style( 'mh-single-post-style', $css );
        ?>

        <div class="mh-single-post-wrapper mh-sp-<?php echo esc_attr( $layout ); ?>">

            <?php if ( $layout === 'layout-1' ) : ?>
                <!-- LAYOUT 1: Classic -->
                <div class="mh-sp-header">
                    <?php if ( $settings['show_title'] === 'yes' ) : ?>
                        <h1 class="mh-sp-title"><?php the_title(); ?></h1>
                    <?php endif; ?>

                    <?php if ( $settings['show_meta'] === 'yes' ) : ?>
                        <div class="mh-sp-meta">
                            <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                            <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                            <span><i class="far fa-folder-open"></i> <?php the_category(', '); ?></span>
                            <span><i class="far fa-comments"></i> <?php comments_number( '0 Comments', '1 Comment', '% Comments' ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( $settings['show_image'] === 'yes' && $has_image ) : ?>
                    <div class="mh-sp-image">
                        <?php the_post_thumbnail( 'full' ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $settings['show_content'] === 'yes' ) : ?>
                    <div class="mh-sp-content">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

            <?php elseif ( $layout === 'layout-2' ) : ?>
                <!-- LAYOUT 2: Hero Cover -->
                <?php if ( $settings['show_image'] === 'yes' && $has_image ) : ?>
                    <div class="mh-sp-hero" style="background-image: url('<?php echo esc_url( $image_url ); ?>');">
                        <div class="mh-sp-hero-overlay"></div>
                        <div class="mh-sp-hero-content">
                            <?php if ( $settings['show_meta'] === 'yes' ) : ?>
                                <div class="mh-sp-meta">
                                    <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                                    <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                                    <span><i class="far fa-folder-open"></i> <?php the_category(', '); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ( $settings['show_title'] === 'yes' ) : ?>
                                <h1 class="mh-sp-title"><?php the_title(); ?></h1>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="mh-sp-header">
                        <?php if ( $settings['show_title'] === 'yes' ) : ?><h1 class="mh-sp-title"><?php the_title(); ?></h1><?php endif; ?>
                        <?php if ( $settings['show_meta'] === 'yes' ) : ?>
                            <div class="mh-sp-meta">
                                <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                                <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                                <span><i class="far fa-folder-open"></i> <?php the_category(', '); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $settings['show_content'] === 'yes' ) : ?>
                    <div class="mh-sp-content">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

            <?php elseif ( $layout === 'layout-3' ) : ?>
                <!-- LAYOUT 3: Split View -->
                <div class="mh-sp-left">
                    <?php if ( $settings['show_image'] === 'yes' && $has_image ) : ?>
                        <div class="mh-sp-image">
                            <?php the_post_thumbnail( 'full' ); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mh-sp-right">
                    <?php if ( $settings['show_title'] === 'yes' ) : ?>
                        <h1 class="mh-sp-title"><?php the_title(); ?></h1>
                    <?php endif; ?>

                    <?php if ( $settings['show_meta'] === 'yes' ) : ?>
                        <div class="mh-sp-meta">
                            <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                            <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                            <span><i class="far fa-folder-open"></i> <?php the_category(', '); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $settings['show_content'] === 'yes' ) : ?>
                        <div class="mh-sp-content">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- AUTHOR BOX (Common to all layouts) -->
            <?php if ( $settings['show_author_box'] === 'yes' ) : ?>
                <div class="mh-sp-author-box">
                    <div class="mh-sp-author-avatar">
                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
                    </div>
                    <div class="mh-sp-author-info">
                        <h4 class="mh-sp-author-name"><?php echo esc_html__( 'Written by', 'mh-plug-ecommerce-builder-widgets' ); ?> <?php the_author(); ?></h4>
                        <p class="mh-sp-author-bio"><?php echo get_the_author_meta( 'description' ); ?></p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <?php
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            wp_reset_postdata();
        }
    }
}
