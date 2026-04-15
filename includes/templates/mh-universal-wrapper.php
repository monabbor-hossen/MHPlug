<?php
/**
 * MH Plug - Universal Theme Wrapper
 * This file replaces the entire theme's layout to force the Header/Footer.
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
    <?php else : 
        // Fallback to theme header if no MH header exists
        get_header(); 
    endif; ?>

    <main id="primary" class="site-main mh-universal-content">
        <?php
        while ( have_posts() ) : the_post();
            // If it's a page/post and we have a custom "Single" template, use it
            $type = is_singular( 'product' ) ? 'single_product' : 'single_post';
            $single_template = mh_plug_get_active_template( $type );

            if ( $single_template && !is_singular('mh_templates') ) {
                mh_plug_render_template( $single_template );
            } else {
                the_content();
            }
        endwhile;
        ?>
    </main>

    <?php if ( $footer ) : ?>
        <footer class="mh-custom-footer">
            <?php mh_plug_render_template( $footer ); ?>
        </footer>
    <?php else : 
        get_footer(); 
    endif; ?>

    <?php wp_footer(); ?>
</body>
</html>