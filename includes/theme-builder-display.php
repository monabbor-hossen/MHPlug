<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Clean & Native)
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

// 3. Live Site Universal Router (Only wraps the frontend, ignores the editor completely)
if ( ! function_exists( 'mh_plug_universal_router' ) ) {
    function mh_plug_universal_router( $template ) {
        
        // COMPLETELY IGNORE Admin, AJAX, and REST API (This prevents the JSON error!)
        if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return $template;
        }

        // COMPLETELY IGNORE Elementor Editor
        if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) ) {
            return $template;
        }

        // Let Elementor natively handle viewing the templates directly
        if ( is_singular( 'mh_templates' ) ) {
            return $template;
        }

        // Wrap the LIVE website with our universal designs
        $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
        if ( file_exists( $wrapper ) ) {
            return $wrapper;
        }

        return $template;
    }
}
add_filter( 'template_include', 'mh_plug_universal_router', 999 );