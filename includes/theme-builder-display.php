<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Logged-Out CSS Fix)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1. Safe Template Fetcher (Now strictly respects the Active Status toggle!)
if ( ! function_exists( 'mh_plug_get_active_template' ) ) {
    function mh_plug_get_active_template( $type ) {
        $type = sanitize_key( $type );
        $posts = get_posts([
            'post_type'      => 'mh_templates',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
            'meta_query'     => [
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

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 THE FIX FOR LOGGED OUT USERS: Force Elementor CSS into the <head>
// Caching plugins strip body CSS for logged-out users. This ensures the styles 
// load natively in the header so the design never breaks!
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'mh_plug_enqueue_elementor_template_css', 9999 );
function mh_plug_enqueue_elementor_template_css() {
    if ( ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) return;

    $templates_to_load = [];

    // Load Header CSS
    $header = mh_plug_get_active_template( 'header' );
    if ( $header ) $templates_to_load[] = $header->ID;

    // Load Footer CSS
    $footer = mh_plug_get_active_template( 'footer' );
    if ( $footer ) $templates_to_load[] = $footer->ID;

    // Detect and Load the Active Body Template CSS
    $active_template = null;
    if ( is_tax( 'product_cat' ) || is_category() ) {
        $term_id = get_queried_object_id();
        $custom_cat_template_id = get_term_meta( $term_id, '_mh_category_template', true );
        if ( ! empty( $custom_cat_template_id ) ) {
            $active_template = get_post( $custom_cat_template_id );
        } else {
            $cat_type = is_tax( 'product_cat' ) ? 'product_category' : 'post_category';
            $active_template = mh_plug_get_active_template( $cat_type );
            if ( ! $active_template ) {
                $archive_type = is_tax( 'product_cat' ) ? 'archive_product' : 'archive_post';
                $active_template = mh_plug_get_active_template( $archive_type );
            }
        }
    } elseif ( class_exists('WooCommerce') && ( is_shop() || is_post_type_archive( 'product' ) ) ) {
        $active_template = mh_plug_get_active_template( 'archive_product' );
    } elseif ( is_archive() || is_home() || is_search() ) {
        $active_template = mh_plug_get_active_template( 'archive_post' );
    } elseif ( is_singular( 'product' ) ) {
        $active_template = mh_plug_get_active_template( 'single_product' );
    } elseif ( is_singular( 'post' ) || is_page() ) {
        $active_template = mh_plug_get_active_template( 'single_post' );
    }

    if ( $active_template ) {
        $templates_to_load[] = $active_template->ID;
    }

    // Enqueue all matched template CSS files safely
    foreach ( $templates_to_load as $template_id ) {
        $css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
        $css_file->enqueue();
    }
}

// 4. Clean Universal Router
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
add_filter( 'template_include', 'mh_plug_universal_router', 99999 );
add_filter( 'single_template', 'mh_plug_universal_router', 99999 );