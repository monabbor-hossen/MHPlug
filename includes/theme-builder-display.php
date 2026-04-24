<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Server Cache Override)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 THE MAGIC FIX: FORCE HOSTINGER SERVER CACHE TO FLUSH!
// Since the LiteSpeed plugin is gone, the server cache got "stuck".
// This forces Hostinger to serve the fresh, working page to logged-out users!
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
            'suppress_filters' => true, // 🚀 Guarantees no other plugins hide this from logged-out users!
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

        // When Elementor opens the editor, force our blank canvas so it has a clean workspace
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

        // Apply our universal design wrapper to the live website
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