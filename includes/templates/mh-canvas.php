<?php
/**
 * MH Plug - Canvas Template (Elementor Editor)
 *
 * Minimal, theme-agnostic HTML wrapper used exclusively as the WordPress
 * template for `mh_templates` Custom Post Type posts.
 *
 * It serves two purposes:
 *   1.  Provides the the_content() call that Elementor's editor scanner
 *       requires — without it the editor throws "content area not found".
 *   2.  For `single_product` template types it bootstraps a real WooCommerce
 *       product into the global $product variable so WC widgets render
 *       correctly inside the Elementor editor preview.
 *
 * This file is ONLY loaded for the editor and for direct frontend views of
 * mh_templates posts (e.g. the Elementor preview iframe). Real product pages
 * are served by mh-single-product-frontend.php via template_include.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Determine template type ───────────────────────────────────────────────────
// We read the template being edited from the main WP query (the mh_templates
// post). mh_plug_get_template_type_meta() reads both _mh_template_type and
// the legacy mh_template_type meta key for backward compatibility.
$mh_canvas_post_id = get_queried_object_id(); // more reliable than get_the_ID() before the loop
$mh_canvas_type    = function_exists( 'mh_plug_get_template_type_meta' )
    ? mh_plug_get_template_type_meta( $mh_canvas_post_id )
    : (string) get_post_meta( $mh_canvas_post_id, '_mh_template_type', true );

$mh_is_product_tpl = ( $mh_canvas_type === 'single_product' );


?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="mh-canvas-wrap">
    <?php
    // ── Main WordPress loop ───────────────────────────────────────────────────
    // Elementor scans the rendered HTML for the_content() to locate the
    // "content area". The loop must run against the main query (the
    // mh_templates post) so that the editor iframe context is correct.
    while ( have_posts() ) :
        the_post();

        if ( $mh_is_product_tpl ) {
            if ( class_exists( 'WooCommerce' ) ) {
                global $product, $post;

                $mock_query = new WP_Query( [
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => 1,
                ] );

                if ( $mock_query->have_posts() ) {
                    $mock_product = $mock_query->posts[0];
                    
                    // Save the actual mh_templates post object
                    $template_post = $post;
                    
                    // Overwrite the global $post with the fetched product for WC hooks
                    $post = $mock_product;
                    setup_postdata( $post );
                    
                    // Set the global product
                    $product = wc_get_product( $mock_product->ID );

                    ?>
                    <div class="woocommerce">
                        <div class="product">
                            <?php 
                            // CRITICAL: We render Elementor's content manually for the template,
                            // ensuring that global $post remains the $mock_product! 
                            // This perfectly mirrors our frontend wrapper so Royal Addons widgets work.
                            if ( class_exists( '\Elementor\Plugin' ) ) {
                                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_post->ID, true );
                            } else {
                                echo apply_filters( 'the_content', $template_post->post_content );
                            }

                            // Elementor's Editor UI scanner requires 'the_content' to exist in this file
                            if ( false ) {
                                the_content();
                            }
                            ?>
                        </div>
                    </div>
                    <?php

                    wp_reset_postdata();
                } else {
                    // Fallback for Empty Stores
                    ?>
                    <div class="mh-admin-notice" style="padding: 15px; background: #fff; border-left: 4px solid #dc3232; margin-bottom: 20px;">Please create at least one WooCommerce Product to preview this template.</div>
                    <?php
                    the_content();
                }
            } else {
                the_content();
            }

        } else {
            // ── All other template types ──────────────────────────────────────
            // header, footer, single_post, archive_post, archive_product
            // just need a plain content area for Elementor to scan.
            the_content();

        }

    endwhile;
    ?>
</div><!-- .mh-canvas-wrap -->

<?php wp_footer(); ?>
</body>
</html>
