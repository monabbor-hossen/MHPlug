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

// ── For product templates: fetch a real product to use as preview context ─────
// We do this BEFORE the main loop so that by the time Elementor's widgets
// call global $product they find a valid WC_Product object.
$mh_preview_product    = null; // WC_Product | null
$mh_preview_product_id = 0;

if ( $mh_is_product_tpl && class_exists( 'WooCommerce' ) ) {
    // Fetch the most recently published product.
    // WP_Query is used (not get_posts) so we can call setup_postdata() on it.
    $mh_product_query = new WP_Query( [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
        'fields'         => 'ids', // we only need the ID here
    ] );

    if ( ! empty( $mh_product_query->posts ) ) {
        $mh_preview_product_id = (int) $mh_product_query->posts[0];
        $mh_preview_product    = wc_get_product( $mh_preview_product_id );
    }
}
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
            // ── Single Product editor context ─────────────────────────────────
            //
            // Step 1: Declare and populate the global $product.
            //         Elementor WooCommerce widgets resolve their data through
            //         this global. Without it they show "No product context".
            global $product;
            $product = $mh_preview_product; // WC_Product object or null

            // Step 2: If we have a real product, run setup_postdata() so that
            //         WC's internal template functions (is_product(), etc.)
            //         resolve against the preview product rather than the
            //         mh_templates post.
            if ( $mh_preview_product_id && $mh_preview_product instanceof \WC_Product ) {
                $mh_preview_post_obj = get_post( $mh_preview_product_id );
                if ( $mh_preview_post_obj ) {
                    setup_postdata( $mh_preview_post_obj );
                    wc_setup_product_data( $mh_preview_product );
                }
            }

            // Step 3: Output the content inside WooCommerce's standard class
            //         hierarchy. Elementor's WC widgets detect their rendering
            //         environment by checking for .woocommerce and .product.
            ?>
            <div class="woocommerce">
                <div class="product type-product <?php echo esc_attr( $mh_preview_product ? implode( ' ', (array) $mh_preview_product->get_type() ) : '' ); ?>">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php

            // Step 4: Reset post data to restore the original mh_templates
            //         post context after we're done with the product preview.
            if ( $mh_preview_product_id ) {
                wp_reset_postdata();
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
