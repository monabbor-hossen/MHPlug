<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (God Mode Version)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Safe Editor Check
if ( ! function_exists( 'mh_plug_is_elementor_edit_mode' ) ) {
    function mh_plug_is_elementor_edit_mode() {
        if ( isset( $_GET['elementor-preview'] ) ) return true;
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) return true;
        if ( class_exists( '\Elementor\Plugin' ) ) {
            $plugin = \Elementor\Plugin::instance();
            if ( isset( $plugin->editor ) && $plugin->editor->is_edit_mode() ) return true;
            if ( isset( $plugin->preview ) && $plugin->preview->is_preview_mode() ) return true;
        }
        return false;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 THE ULTIMATE FIX: Trick Elementor Free into loading the editor!
// Elementor Free blocks "Header" and "Archive" document types. 
// We dynamically force it to see them as normal "pages" during editing so it doesn't crash!
// ─────────────────────────────────────────────────────────────────────────────
add_filter( 'get_post_metadata', function( $value, $object_id, $meta_key, $single ) {
    if ( get_post_type( $object_id ) === 'mh_templates' ) {
        if ( $meta_key === '_elementor_template_type' ) {
            return $single ? 'wp-page' : [ 'wp-page' ];
        }
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

// 4. Safe Universal Router
if ( ! function_exists( 'mh_plug_universal_router' ) ) {
    function mh_plug_universal_router( $template ) {
        
        $canvas  = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
        $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';

        // 🚀 Force OUR custom canvas when viewing/editing the template directly!
        if ( is_singular( 'mh_templates' ) ) {
            if ( file_exists( $canvas ) ) {
                return $canvas;
            }
        }

        // Do not interfere with Elementor Editor for standard pages/posts
        if ( mh_plug_is_elementor_edit_mode() ) {
            return $template;
        }

        // Route frontend traffic through the Universal Wrapper
        if ( file_exists( $wrapper ) ) {
            return $wrapper;
        }

        return $template;
    }
}
// 🚀 High priority to beat FSE themes (like Twenty Twenty-Four)
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );
add_filter( 'single_template', 'mh_plug_universal_router', 99999 );