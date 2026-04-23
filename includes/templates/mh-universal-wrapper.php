<?php
/**
 * MH Plug - Universal Theme Wrapper (Pro Version with Category Routing)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$header = mh_plug_get_active_template( 'header' );
$footer = mh_plug_get_active_template( 'footer' );

// 🚀 INTELLIGENT ROUTING ENGINE
$active_template = null;

if ( is_tax( 'product_cat' ) || is_category() ) {
    
    // 1. Check if THIS specific category has a custom template assigned
    $term_id = get_queried_object_id();
    $custom_cat_template_id = get_term_meta( $term_id, '_mh_category_template', true );
    
    if ( ! empty( $custom_cat_template_id ) ) {
        $active_template = get_post( $custom_cat_template_id );
    } else {
        // 2. If no specific template is chosen, fallback to the Global Archive template
        $archive_type = is_tax( 'product_cat' ) ? 'archive_product' : 'archive_post';
        $active_template = mh_plug_get_active_template( $archive_type );
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
        // 🚀 THE FIX: Tell WordPress what to render!
        if ( is_singular( 'mh_templates' ) ) {
            // Let Elementor Editor do its native preview logic
            if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif;
        } elseif ( $active_template ) {
            // Inject the proper Theme Builder Template
            mh_plug_render_template( $active_template );
        } else {
            // Fallback for pages that have NO templates assigned
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            endif;
        }
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