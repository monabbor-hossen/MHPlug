<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Editor Fix)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 FORCE HOSTINGER SERVER CACHE TO FLUSH
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'send_headers', function() {
    if ( ! is_admin() ) {
        header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        header( 'X-LiteSpeed-Cache-Control: no-cache' );
    }
}, 1 );

// 1. Safe Template Fetcher
if ( ! function_exists( 'mh_plug_get_active_template' ) ) {
    function mh_plug_get_active_template( $type ) {
        $type = sanitize_key( $type );
        $posts = get_posts([
            'post_type'        => 'mh_templates',
            'post_status'      => 'publish',
            'posts_per_page'   => 1,
            'no_found_rows'    => true,
            'suppress_filters' => true, 
            'meta_query'       => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [ 'key' => '_mh_template_type', 'value' => $type ],
                    [ 'key' => 'mh_template_type', 'value' => $type ]
                ],
                [
                    'relation' => 'OR',
                    [ 'key' => '_mh_template_active', 'value' => 'yes' ],
                    [ 'key' => 'mh_template_active', 'value' => 'yes' ]
                ]
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
        
        // PREVENT JSON ERRORS: Completely ignore the REST API, Admin, and AJAX
        if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return $template;
        }

        // When Elementor opens the editor FOR A THEME TEMPLATE, force our blank canvas
        if ( is_singular( 'mh_templates' ) ) {
            $canvas = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
            if ( file_exists( $canvas ) ) {
                return $canvas;
            }
        }

        // 🚀 NEW FIX: If the user explicitly sets the page to "Elementor Canvas" (No Header/Footer), respect it.
        if ( get_post_meta( get_the_ID(), '_wp_page_template', true ) === 'elementor_canvas' ) {
            return $template;
        }

        // 🚀 THE BYPASS WAS DELETED HERE.
        // Elementor Editor will now correctly load the universal wrapper below!

        // Apply our universal design wrapper to the live website AND inside the editor
        $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
        if ( file_exists( $wrapper ) ) {
            return $wrapper;
        }

        return $template;
    }
}

// Force the wrapper on every single possible page load
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );
add_filter( 'single_template', 'mh_plug_universal_router', 99999 );
add_filter( 'archive_template', 'mh_plug_universal_router', 99999 );
add_filter( 'page_template', 'mh_plug_universal_router', 99999 );
add_filter( 'index_template', 'mh_plug_universal_router', 99999 );