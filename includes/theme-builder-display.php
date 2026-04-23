<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Ultimate Fix)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 THE SILVER BULLET: Trick Elementor Free into loading the editor!
// Elementor Free blocks "Header" and "Archive" document types. 
// By returning an empty string, we force Elementor to treat this exactly like 
// a standard Custom Post Type, bypassing all Pro restrictions and crashes!
// ─────────────────────────────────────────────────────────────────────────────
add_filter( 'get_post_metadata', function( $value, $object_id, $meta_key, $single ) {
    if ( $meta_key === '_elementor_template_type' && get_post_type( $object_id ) === 'mh_templates' ) {
        return $single ? '' : [ '' ];
    }
    return $value;
}, 10, 4 );

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

// 4. Ultimate Canvas Router
if ( ! function_exists( 'mh_plug_universal_router' ) ) {
    function mh_plug_universal_router( $template ) {
        
        // 🚀 THE FIX: Force Elementor's Native Canvas for Templates!
        // This guarantees `the_content` is present, bypassing all Block Theme conflicts.
        if ( is_singular( 'mh_templates' ) ) {
            if ( defined( 'ELEMENTOR_PATH' ) ) {
                $elementor_canvas = ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';
                if ( file_exists( $elementor_canvas ) ) {
                    return $elementor_canvas;
                }
            }
            
            // Fallback to our canvas if Elementor's is missing
            $canvas = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
            if ( file_exists( $canvas ) ) {
                return $canvas;
            }
        }

        // Do not interfere with Elementor Editor for standard pages/posts
        if ( mh_plug_is_elementor_edit_mode() ) {
            return $template;
        }

        // Route frontend traffic through the Universal Wrapper
        $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
        if ( file_exists( $wrapper ) ) {
            return $wrapper;
        }

        return $template;
    }
}
// Run at maximum priority to defeat all FSE Themes and third-party overrides
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );
add_filter( 'single_template', 'mh_plug_universal_router', 99999 );