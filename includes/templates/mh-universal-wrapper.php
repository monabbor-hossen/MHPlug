<?php
/**
 * MH Plug - Universal Theme Wrapper (Safe Version)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$header = mh_plug_get_active_template( 'header' );
$footer = mh_plug_get_active_template( 'footer' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?php if ( $header ) : ?>
        <header class="mh-custom-header">
            <?php mh_plug_render_template( $header ); ?>
        </header>
    <?php endif; ?>

    <main id="primary" class="site-main mh-universal-content">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                
                // WooCommerce Context Setup
                if ( is_singular( 'product' ) && class_exists( 'WooCommerce' ) ) {
                    global $product;
                    $product = wc_get_product( get_the_ID() );
                }

                $type = is_singular( 'product' ) ? 'single_product' : 'single_post';
                $single_template = mh_plug_get_active_template( $type );

                if ( $single_template && !is_singular('mh_templates') ) {
                    mh_plug_render_template( $single_template );
                } else {
                    the_content();
                }

            endwhile;
        endif;
        ?>
    </main>

    <?php if ( $footer ) : ?>
        <footer class="mh-custom-footer">
            <?php mh_plug_render_template( $footer ); ?>
        </footer>
    <?php endif; ?>

    <?php wp_footer(); ?>
</body>
</html>