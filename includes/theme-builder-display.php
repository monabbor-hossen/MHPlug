<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Clean Version)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Safe Template Fetcher
if ( ! function_exists( 'mh_plug_get_active_template' ) ) {
    function mh_plug_get_active_template( $type ) {
        $type = sanitize_key( $type );
        $posts = get_posts([
            'post_type'      => 'mh_templates',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
            'meta_query'     => [
                'relation' => 'OR',
                [ [ 'key' => '_mh_template_type', 'value' => $type ] ],
                [ [ 'key' => 'mh_template_type', 'value' => $type ] ]
            ]
        ]);
        return ! empty( $posts ) ? $posts[0] : null;
    }
}

// 2. Safe Elementor Renderer
if ( ! function_exists( 'mh_plug_render_template' ) ) {
    function mh_plug_render_template( $template_post ) {
        if ( ! $template_post ) return;
        
        if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
            $instance = \Elementor\Plugin::instance();
            if ( isset( $instance->frontend ) ) {
                echo $instance->frontend->get_builder_content_for_display( $template_post->ID, true );
                return;
            }
        }
        echo apply_filters( 'the_content', $template_post->post_content );
    }
}

// 3. Clean Universal Router
if ( ! function_exists( 'mh_plug_universal_router' ) ) {
    function mh_plug_universal_router( $template ) {
        
        // 🚀 FORCE OUR CANVAS for Elementor Editor so FSE Themes don't crash it
        if ( is_singular( 'mh_templates' ) ) {
            $canvas = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
            if ( file_exists( $canvas ) ) {
                return $canvas;
            }
        }

        // Do not interfere with standard Elementor edits
        if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) ) {
            return $template;
        }

        // Apply our Universal Wrapper to the frontend live site
        $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
        if ( file_exists( $wrapper ) ) {
            return $wrapper;
        }

        return $template;
    }
}
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );
add_filter( 'single_template', 'mh_plug_universal_router', 99999 );