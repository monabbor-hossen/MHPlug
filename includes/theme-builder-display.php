<?php
/**
 * MH Plug - Universal Theme Builder Display Logic (Editor Fix)
 */

if (!defined('ABSPATH')) {
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 FORCE HOSTINGER SERVER CACHE TO FLUSH
// ─────────────────────────────────────────────────────────────────────────────
add_action('send_headers', function () {
    if (!is_admin()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('X-LiteSpeed-Cache-Control: no-cache');
    }
}, 1);

// 1. Safe Template Fetcher
if (!function_exists('mh_plug_get_active_template')) {
    function mh_plug_get_active_template($type)
    {
        $type = sanitize_key($type);
        $posts = get_posts([
            'post_type' => 'mh_templates',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'suppress_filters' => true,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    ['key' => '_mh_template_type', 'value' => $type],
                    ['key' => 'mh_template_type', 'value' => $type]
                ],
                [
                    'relation' => 'OR',
                    ['key' => '_mh_template_active', 'value' => 'yes'],
                    ['key' => 'mh_template_active', 'value' => 'yes']
                ]
            ]
        ]);
        return !empty($posts) ? $posts[0] : null;
    }
}

// 2. Safe Elementor Renderer
if (!function_exists('mh_plug_render_template')) {
    function mh_plug_render_template($template_post)
    {
        if (!$template_post)
            return;

        if (did_action('elementor/loaded') && class_exists('\Elementor\Plugin')) {
            $instance = \Elementor\Plugin::instance();
            if (isset($instance->frontend)) {
                echo $instance->frontend->get_builder_content_for_display($template_post->ID, true);
                return;
            }
        }
        echo apply_filters('the_content', $template_post->post_content);
    }
}

// 3. Clean Universal Router
if (!function_exists('mh_plug_universal_router')) {
    function mh_plug_universal_router($template)
    {
        // 1. ULTIMATE ELEMENTOR BYPASS (Fixes "Can't Edit" & Infinite Loading)
        // Relies strictly on globals/requests, NO fragile object initialization.
        if ( isset( $_REQUEST['action'] ) && strpos( $_REQUEST['action'], 'elementor' ) !== false ) {
            return $template;
        }
        if ( isset( $_REQUEST['elementor-preview'] ) || isset( $_GET['elementor-preview'] ) ) {
            return $template;
        }
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return $template;
        }
        if ( is_admin() && ! wp_doing_ajax() ) {
            return $template;
        }

        // 2. MH-TEMPLATES EDITOR CANVAS
        if ( is_singular( 'mh_templates' ) ) {
            $canvas = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
            if ( file_exists( $canvas ) ) return $canvas;
        }
        $current_id = get_the_ID();
        if ( $current_id && get_post_meta( $current_id, '_wp_page_template', true ) === 'elementor_canvas' ) {
            return $template;
        }

        // 3. DETERMINE WRAPPER FOR ALL PAGES (Header/Footer/Content)
        $has_template = false;

        // If a header or footer template exists, ALWAYS load the wrapper so they display.
        if ( mh_plug_get_active_template('header') || mh_plug_get_active_template('footer') ) {
            $has_template = true;
        }

        if ( is_tax( 'product_cat' ) || is_category() ) {
            $term_id = get_queried_object_id();
            if ( get_term_meta( $term_id, '_mh_category_template', true ) ) {
                $has_template = true;
            } else {
                $cat_type = is_tax( 'product_cat' ) ? 'product_category' : 'post_category';
                if ( mh_plug_get_active_template( $cat_type ) ) {
                    $has_template = true;
                } else {
                    $archive_type = is_tax( 'product_cat' ) ? 'archive_product' : 'archive_post';
                    if ( mh_plug_get_active_template( $archive_type ) ) {
                        $has_template = true;
                    }
                }
            }
        } elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
            if ( mh_plug_get_active_template( 'product_tag' ) || mh_plug_get_active_template( 'archive_product' ) ) {
                $has_template = true;
            }
        } elseif ( is_tax( array( 'pwb-brand', 'yith_product_brand', 'product_brand', 'wooco_brand' ) ) ) {
            if ( mh_plug_get_active_template( 'product_brand' ) || mh_plug_get_active_template( 'archive_product' ) ) {
                $has_template = true;
            }
        } elseif ( class_exists('WooCommerce') && ( is_shop() || is_post_type_archive( 'product' ) ) ) {
            if ( mh_plug_get_active_template( 'archive_product' ) ) $has_template = true;
        } elseif ( is_archive() || is_home() || is_search() ) {
            if ( mh_plug_get_active_template( 'archive_post' ) ) $has_template = true;
        } elseif ( is_singular( 'product' ) ) {
            if ( mh_plug_get_active_template( 'single_product' ) ) $has_template = true;
        } elseif ( is_singular( 'post' ) ) {
            if ( mh_plug_get_active_template( 'single_post' ) ) $has_template = true;
        } elseif ( is_page() ) {
            $is_woo_functional = false;
            if ( class_exists( 'WooCommerce' ) ) {
                if ( is_cart() && mh_plug_get_active_template('cart') ) {
                    $has_template = true;
                    $is_woo_functional = true;
                } elseif ( is_checkout() && mh_plug_get_active_template('checkout') ) {
                    $has_template = true;
                    $is_woo_functional = true;
                } elseif ( is_account_page() && mh_plug_get_active_template('my_account') ) {
                    $has_template = true;
                    $is_woo_functional = true;
                } elseif ( is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url() ) {
                    $is_woo_functional = true;
                    // No body template assigned, but header/footer still apply via $has_template above.
                }
            }
            if ( ! $is_woo_functional ) {
                if ( mh_plug_get_active_template( 'single_page' ) ) $has_template = true;
            }
        }

        // 5. RENDER THE UNIVERSAL WRAPPER
        if ( $has_template ) {
            $wrapper = MH_PLUG_PATH . 'includes/templates/mh-universal-wrapper.php';
            if ( file_exists( $wrapper ) ) {
                return $wrapper;
            }
        }

        return $template;
    }
}

// template_include is the single canonical hook for overriding the loaded
// template file. The specific filters (page_template, single_template, etc.)
// fire BEFORE template_include and their result feeds into it — registering
// the router on both causes double-execution, doubling DB queries and risking
// race conditions on resource-constrained hosting (Hostinger).
add_filter('template_include', 'mh_plug_universal_router', 99999);

