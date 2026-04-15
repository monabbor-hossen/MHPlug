<?php
/**
 * MH Plug - Universal Theme Builder Display Logic
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// --- Helpers ---
function mh_plug_is_elementor_edit_mode() {
    return (did_action('elementor/loaded') && \Elementor\Plugin::$instance->editor->is_edit_mode());
}

function mh_plug_get_active_template( $type ) {
    $posts = get_posts([
        'post_type' => 'mh_templates',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => [
            'relation' => 'OR',
            [['key' => '_mh_template_type', 'value' => $type]],
            [['key' => 'mh_template_type', 'value' => $type]]
        ]
    ]);
    return ! empty( $posts ) ? $posts[0] : null;
}

function mh_plug_render_template( $template_post ) {
    echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_post->ID, true );
}

// --- THE UNIVERSAL ROUTER ---
function mh_plug_universal_router( $template ) {
    // 1. Bypass if in Elementor Editor
    if ( mh_plug_is_elementor_edit_mode() ) {
        return $template;
    }

    // 2. Bypass if viewing the Template CPT directly (Editor Canvas handles this)
    if ( is_singular( 'mh_templates' ) ) {
        return $template;
    }

    // 3. FORCE the Universal Wrapper for ALL frontend pages
    $universal_wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
    
    if ( file_exists( $universal_wrapper ) ) {
        return $universal_wrapper;
    }

    return $template;
}
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );