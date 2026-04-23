<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (The Kill Switch)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Force Elementor Free to see all templates as standard pages (Prevents Pro Document crashes)
add_filter( 'get_post_metadata', function( $value, $object_id, $meta_key, $single ) {
    if ( $meta_key === '_elementor_template_type' && get_post_type( $object_id ) === 'mh_templates' ) {
        return $single ? 'wp-page' : [ 'wp-page' ];
    }
    return $value;
}, 10, 4 );

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
        echo apply_filters( 'the_content', $template_post->post_content );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 THE KILL SWITCH: Force Canvas and STOP the active theme!
// Block Themes ignore standard routing. We MUST exit the script manually.
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'template_redirect', 'mh_plug_force_canvas_killswitch', 99999 );
function mh_plug_force_canvas_killswitch() {
    if ( is_singular( 'mh_templates' ) ) {
        $canvas = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
        if ( file_exists( $canvas ) ) {
            include $canvas;
            exit; // 🚀 THIS IS THE MAGIC. It completely kills the WordPress theme.
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 5. Ultimate Clean Router for the Live Site
// ─────────────────────────────────────────────────────────────────────────────
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );
function mh_plug_universal_router( $template ) {
    // If we are editing standard pages in Elementor, step aside!
    if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) ) {
        return $template;
    }

    // Route live frontend traffic through our Universal Wrapper
    $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
    if ( file_exists( $wrapper ) ) {
        return $wrapper;
    }

    return $template;
}