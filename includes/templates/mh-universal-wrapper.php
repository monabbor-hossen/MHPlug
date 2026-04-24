<?php
/**
 * MH Plug - Universal Theme Wrapper (Frontend Layout Fix)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$header = mh_plug_get_active_template( 'header' );
$footer = mh_plug_get_active_template( 'footer' );

// 🚀 INTELLIGENT ROUTING ENGINE
$active_template = null;
$is_woo_single   = false;
$is_woo_archive  = false;

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
    if ( is_tax( 'product_cat' ) ) $is_woo_archive = true;

} elseif ( class_exists('WooCommerce') && ( is_shop() || is_post_type_archive( 'product' ) ) ) {
    $active_template = mh_plug_get_active_template( 'archive_product' );
    $is_woo_archive = true;
} elseif ( is_archive() || is_home() || is_search() ) {
    $active_template = mh_plug_get_active_template( 'archive_post' );
} elseif ( is_singular( 'product' ) ) {
    $active_template = mh_plug_get_active_template( 'single_product' );
    $is_woo_single = true;
} elseif ( is_singular( 'post' ) || is_page() ) {
    $active_template = mh_plug_get_active_template( 'single_post' );
}
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
        <header class="mh-custom-header elementor-location-header">
            <?php mh_plug_render_template( $header ); ?>
        </header>
    <?php endif; ?>

    <main id="primary" class="site-main mh-universal-content">
        <?php
        if ( is_singular( 'mh_templates' ) ) {
            if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif;
        } elseif ( $active_template ) {
            
            // 🚀 FIX 2: Wrapped the live site content in exact WooCommerce CSS DOM Structures
            if ( class_exists( 'WooCommerce' ) ) {
                if ( $is_woo_single ) {
                    global $product;
                    if ( empty( $product ) ) {
                        $product = wc_get_product( get_the_ID() );
                    }
                    echo '<div class="woocommerce"><div class="product">';
                } elseif ( $is_woo_archive ) {
                    echo '<div class="woocommerce">';
                }
            }

            // Inject the proper Theme Builder Template
            mh_plug_render_template( $active_template );

            // Close WooCommerce Wrappers
            if ( class_exists( 'WooCommerce' ) ) {
                if ( $is_woo_single ) {
                    echo '</div></div>';
                } elseif ( $is_woo_archive ) {
                    echo '</div>';
                }
            }

        } else {
            // Fallback for pages that have NO templates assigned
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    if ( class_exists('WooCommerce') && is_singular('product') ) {
                        echo '<div class="woocommerce"><div class="product">';
                        the_content();
                        echo '</div></div>';
                    } else {
                        the_content();
                    }
                endwhile;
            endif;
        }
        ?>
    </main>

    <?php if ( $footer ) : ?>
        <footer class="mh-custom-footer elementor-location-footer">
            <?php mh_plug_render_template( $footer ); ?>
        </footer>
    <?php endif; ?>

    <?php wp_footer(); ?>
</body>
</html>