<?php
/**
 * MH Plug - Single Product Frontend Wrapper
 *
 * This file acts as a custom WordPress theme template specifically for
 * WooCommerce single product pages when an active MH-Plug `single_product`
 * template exists.
 *
 * Execution flow:
 *   1. WordPress runs template_include (priority 99) in theme-builder-display.php
 *      → finds an active single_product mh_template
 *      → stores its ID in $mh_plug_product_template_id
 *      → returns this file's path
 *   2. WordPress loads this file as the page template.
 *   3. We run the standard WP loop so the queried product post is current.
 *   4. We explicitly set up WooCommerce's global $product via wc_get_product().
 *   5. We render the Elementor template via get_builder_content_for_display().
 *
 * Why this approach works:
 *   Elementor WooCommerce widgets (Add to Cart, Product Price, Product Gallery,
 *   etc.) resolve their data through the global $product object AND through
 *   WC's internal product data setup. WooCommerce normally populates these
 *   via wc_setup_product_data() inside its own single-product.php template.
 *   By doing that setup here — before we hand off to Elementor — every widget
 *   receives a fully populated $product and renders correctly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Retrieve the template post ID passed by the template_include filter ───────
global $mh_plug_product_template_id;
$mh_template_id = intval( $mh_plug_product_template_id );

if ( ! $mh_template_id ) {
    // Fallback: this file was somehow loaded without a template ID.
    // Load WooCommerce's default template instead.
    $wc_default = WC()->template_path() . 'single-product.php';
    if ( file_exists( get_stylesheet_directory() . '/' . $wc_default ) ) {
        include get_stylesheet_directory() . '/' . $wc_default;
    } else {
        include WC()->plugin_path() . '/templates/single-product.php';
    }
    return;
}

// ── Standard page chrome ──────────────────────────────────────────────────────
get_header();
?>

<div class="mh-single-product-wrap">
    <?php
    // ── Standard WordPress loop ───────────────────────────────────────────────
    // The global $wp_query is already set to the product post. We run the
    // loop so that all standard conditional checks (is_product(), get_the_ID(),
    // etc.) resolve correctly throughout Elementor's rendering pipeline.
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // ── Set up WooCommerce product context ────────────────────────────
            // This is the critical step. Declaring $product as a global and
            // populating it with wc_get_product() ensures:
            //
            //   • Elementor's WooCommerce widgets can call global $product
            //     and receive a valid WC_Product object.
            //   • WooCommerce template functions (like wc_get_product_image())
            //     that rely on the global $product work without modification.
            //   • wc_setup_product_data() fires, which triggers all the WC
            //     hooks that widgets and extensions listen to.
            global $product;
            $product = wc_get_product( get_the_ID() );

            if ( $product && $product->is_type( 'variation' ) ) {
                // Edge case: if somehow a variation is the queried post,
                // switch to its parent product for a sensible preview.
                $product = wc_get_product( $product->get_parent_id() );
            }

            // Fire WooCommerce's product data setup action so all hooks run
            // exactly as they would on a native WC product template.
            if ( $product instanceof \WC_Product ) {
                do_action( 'woocommerce_before_single_product' );
                wc_setup_product_data( $product );
            }

            // ── Render the Elementor template ─────────────────────────────────
            if (
                did_action( 'elementor/loaded' ) &&
                class_exists( '\Elementor\Plugin' ) &&
                isset( \Elementor\Plugin::$instance->frontend )
            ) {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display(
                    $mh_template_id,
                    true  // with_css — include Elementor's inline styles
                );
            } else {
                // Elementor not available — fall back to raw post content.
                // This should never happen in normal operation since we
                // only create mh_templates posts to be edited in Elementor.
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo apply_filters( 'the_content', get_post_field( 'post_content', $mh_template_id ) );
            }

            // Fire the after-product action for completeness
            if ( $product instanceof \WC_Product ) {
                do_action( 'woocommerce_after_single_product' );
            }

        endwhile;
    endif;
    ?>
</div><!-- .mh-single-product-wrap -->

<?php
get_footer();
