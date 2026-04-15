<?php
/**
 * MH Plug - Canvas Template (Elementor Editor Override)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'elementor-page' ); ?>>
    <?php wp_body_open(); ?>

    <?php
    $template_id = get_the_ID();
    $type        = get_post_meta( $template_id, '_mh_template_type', true );

    // Legacy fallback check
    if ( empty( $type ) ) {
        $legacy_type = get_post_meta( $template_id, 'mh_template_type', true );
        if ( $legacy_type === 'product_single' ) {
            $type = 'single_product';
        }
    }

    if ( $type === 'single_product' && class_exists( 'WooCommerce' ) ) {
        $mock_products = get_posts( [
            'post_type'   => 'product',
            'post_status' => 'publish',
            'numberposts' => 1
        ] );

        if ( ! empty( $mock_products ) ) {
            global $post, $product;
            
            $template_post = $post; // Save the template
            
            // Setup mock product for WooCommerce widgets
            $post = $mock_products[0];
            setup_postdata( $post );
            $product = wc_get_product( $post->ID );

            echo '<div class="woocommerce"><div class="product">';
            
            // STRICT RULE: Restore the template post so Elementor edits the template, not the mock product
            $post = $template_post;
            setup_postdata( $post );

            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content(); 
                endwhile;
            endif;
            
            echo '</div></div>';

            wp_reset_postdata();
        } else {
            echo '<div class="mh-admin-notice" style="padding: 15px; background: #fff; border-left: 4px solid #dc3232; margin-bottom: 20px;">Please add a WooCommerce product to preview this template.</div>';
            
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            endif;
        }
    } else {
        // Standard template handling (Header, Footer, Single Post, etc.)
        // STRICT RULE: Elementor Editor absolutely requires the standard WordPress loop here.
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        endif;
    }
    ?>

    <?php wp_footer(); ?>
</body>
</html>