<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Bulletproof Version)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Safe Editor Check
if ( ! function_exists( 'mh_plug_is_elementor_edit_mode' ) ) {
    function mh_plug_is_elementor_edit_mode() {
        if ( ! did_action( 'elementor/loaded' ) ) return false;
        if ( ! class_exists( '\Elementor\Plugin' ) ) return false;
        
        $instance = \Elementor\Plugin::instance();
        if ( empty( $instance ) || empty( $instance->editor ) ) return false;
        
        return $instance->editor->is_edit_mode();
    }
}

// 2. Safe Template Fetcher
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

// 3. Safe Elementor Renderer
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
        
        // Fallback if Elementor engine isn't ready
        echo apply_filters( 'the_content', $template_post->post_content );
    }
}

// 4. Safe Universal Router
if ( ! function_exists( 'mh_plug_universal_router' ) ) {
    function mh_plug_universal_router( $template ) {
        // Do not interfere with Elementor Editor or the CPT directly
        if ( mh_plug_is_elementor_edit_mode() ) return $template;
        if ( is_singular( 'mh_templates' ) ) return $template;

        // Route everything else through the Universal Wrapper
        $universal_wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
        
        if ( file_exists( $universal_wrapper ) ) {
            return $universal_wrapper;
        }

        return $template;
    }
}
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );