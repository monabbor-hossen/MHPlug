<?php
/**
 * MH Plug - Canvas Template
 *
 * Minimal, theme-agnostic HTML wrapper used exclusively for the `mh_templates`
 * Custom Post Type. It provides the the_content() call Elementor requires in
 * order to open the editor without throwing "content area not found".
 *
 * For `single_product` template types the content is wrapped in WooCommerce's
 * standard product class hierarchy so WC widgets receive proper product context
 * from Elementor's frontend rendering engine.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Determine the template type of the post being edited so we can apply the
// appropriate wrapper. We call mh_plug_get_template_type_meta() which reads
// both the new `_mh_template_type` and the old `mh_template_type` keys.
$mh_canvas_post_id   = get_the_ID();
$mh_canvas_type      = function_exists( 'mh_plug_get_template_type_meta' )
                            ? mh_plug_get_template_type_meta( $mh_canvas_post_id )
                            : get_post_meta( $mh_canvas_post_id, '_mh_template_type', true );
$mh_is_product_tpl   = ( $mh_canvas_type === 'single_product' );
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
    // Standard WordPress loop — the_content() is the call Elementor scans for.
    // Without it the editor throws "content area not found".
    while ( have_posts() ) :
        the_post();

        if ( $mh_is_product_tpl ) :
            /**
             * WooCommerce product wrapper.
             *
             * Elementor's WooCommerce widgets (Add to Cart, Product Price, etc.)
             * detect their context by checking for these classes / globals.
             * Wrapping the content this way ensures:
             *   1. wc_get_product( get_the_ID() ) resolves to a product object
             *      (Elementor's preview already switches post_id via the filter
             *       in theme-builder-cpt.php).
             *   2. Widget templates that use is_product() render correctly.
             */
            ?>
            <div class="woocommerce">
                <div class="product type-product">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php
        else :
            // All other template types (header, footer, single_post, archives)
            // just need a plain content output.
            the_content();
        endif;

    endwhile;
    ?>
</div><!-- .mh-canvas-wrap -->

<?php wp_footer(); ?>
</body>
</html>
