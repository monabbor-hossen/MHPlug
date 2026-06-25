<?php
/**
 * MH Plug - Theme Builder Admin Page
 *
 * Template type slugs (authoritative):
 * header | footer | single_post | single_product | archive_post | archive_product | quick_view | post_category | product_category | product_tag | product_brand | custom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_wc_active = class_exists( 'WooCommerce' );
?>

<div class="wrap mh-plug-admin-wrap">


    <div class="mh-tb-header">
        <div class="mh-tb-header-text">
            <h1 class="mh-plug-title"><?php esc_html_e( 'Theme Builder', 'mh-plug-ecommerce-builder-widgets' ); ?></h1>
            <p class="mh-tb-description"><?php esc_html_e( 'Create and manage custom Elementor templates for headers, footers, single posts, products, archives, categories, and generic layouts.', 'mh-plug-ecommerce-builder-widgets' ); ?></p>
        </div>
        <div class="mh-tb-header-actions">
            <button class="mh-button mh-tb-action-create" id="mh-tb-create-btn">
                <span class="mh-button-content-wrapper">
                    <span class="mh-button-icon mh-button-icon-left"><i class="dashicons dashicons-plus-alt2"></i></span>
                    <span class="mh-button-text"><?php esc_html_e( 'Create Template', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                </span>
            </button>
        </div>
    </div>

    <ul class="mh-tb-tabs">
        <li data-tab="all" class="active"><?php esc_html_e( 'All', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        <li data-tab="header"><?php esc_html_e( 'Header', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        <li data-tab="footer"><?php esc_html_e( 'Footer', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        <li data-tab="single_post"><?php esc_html_e( 'Single Post', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        <li data-tab="archive_post"><?php esc_html_e( 'Archive', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        <li data-tab="post_category"><?php esc_html_e( 'Post Category', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        <li data-tab="custom"><?php esc_html_e( 'Custom', 'mh-plug-ecommerce-builder-widgets' ); ?></li>
        
        <li data-tab="archive_product" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Product Archive', 'mh-plug-ecommerce-builder-widgets' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug-ecommerce-builder-widgets' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="product_category" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Product Category', 'mh-plug-ecommerce-builder-widgets' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug-ecommerce-builder-widgets' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="product_tag" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Product Tag', 'mh-plug-ecommerce-builder-widgets' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug-ecommerce-builder-widgets' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="product_brand" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Product Brand', 'mh-plug-ecommerce-builder-widgets' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug-ecommerce-builder-widgets' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="single_product" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Single Product', 'mh-plug-ecommerce-builder-widgets' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug-ecommerce-builder-widgets' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="quick_view" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Quick View', 'mh-plug-ecommerce-builder-widgets' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug-ecommerce-builder-widgets' ); ?>)</span>
            <?php endif; ?>
        </li>
    </ul>

    <div class="mh-tb-grid">

        <div class="mh-tb-card mh-tb-card-add-new" id="mh-tb-card-add-new" data-type="all">
            <div class="mh-tb-add-icon"><i class="dashicons dashicons-plus"></i></div>
            <h3><?php esc_html_e( 'Add New', 'mh-plug-ecommerce-builder-widgets' ); ?></h3>
        </div>

        <?php
        $templates = new WP_Query( [
            'post_type'      => 'mh_templates',
            'posts_per_page' => -1,
            'post_status'    => [ 'publish', 'draft' ],
            'no_found_rows'  => true,
        ] );

        $legacy_map = [
            'single'          => 'single_post',
            'product_single'  => 'single_product',
            'archive'         => 'archive_post',
            'product_archive' => 'archive_product',
        ];

        $icon_map = [
            'header'           => 'dashicons-align-center',
            'footer'           => 'dashicons-arrow-down-alt2',
            'single_post'      => 'dashicons-media-document',
            'single_product'   => 'dashicons-products',
            'archive_post'     => 'dashicons-portfolio',
            'archive_product'  => 'dashicons-cart',
            'post_category'    => 'dashicons-category', 
            'product_category' => 'dashicons-store', 
            'product_tag'      => 'dashicons-tag',
            'product_brand'    => 'dashicons-awards',
            'quick_view'       => 'dashicons-visibility', 
            'custom'           => 'dashicons-layout',
        ];

        $label_map = [
            'header'           => __( 'Header', 'mh-plug-ecommerce-builder-widgets' ),
            'footer'           => __( 'Footer', 'mh-plug-ecommerce-builder-widgets' ),
            'single_post'      => __( 'Single Post', 'mh-plug-ecommerce-builder-widgets' ),
            'single_product'   => __( 'Single Product', 'mh-plug-ecommerce-builder-widgets' ),
            'archive_post'     => __( 'Archive', 'mh-plug-ecommerce-builder-widgets' ),
            'archive_product'  => __( 'Product Archive', 'mh-plug-ecommerce-builder-widgets' ),
            'post_category'    => __( 'Post Category', 'mh-plug-ecommerce-builder-widgets' ), 
            'product_category' => __( 'Product Category', 'mh-plug-ecommerce-builder-widgets' ), 
            'product_tag'      => __( 'Product Tag', 'mh-plug-ecommerce-builder-widgets' ),
            'product_brand'    => __( 'Product Brand', 'mh-plug-ecommerce-builder-widgets' ),
            'quick_view'       => __( 'Quick View', 'mh-plug-ecommerce-builder-widgets' ),
            'custom'           => __( 'Custom Template', 'mh-plug-ecommerce-builder-widgets' ),
        ];

        if ( $templates->have_posts() ) :
            while ( $templates->have_posts() ) :
                $templates->the_post();
                $template_id = get_the_ID();

                $raw_type = get_post_meta( $template_id, '_mh_template_type', true );
                if ( empty( $raw_type ) ) {
                    $raw_type = get_post_meta( $template_id, 'mh_template_type', true );
                }
                
                $template_type = isset( $legacy_map[ $raw_type ] ) ? $legacy_map[ $raw_type ] : $raw_type;
                if ( empty( $template_type ) ) {
                    $template_type = 'single_post'; 
                }

                $edit_url  = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
                $is_active = ( mh_plug_get_template_active_meta( $template_id ) === 'yes' ) ? 'checked' : '';
                $icon      = $icon_map[ $template_type ]  ?? 'dashicons-media-document';
                $badge     = $label_map[ $template_type ] ?? ucwords( str_replace( '_', ' ', $template_type ) );
                ?>
        <div class="mh-tb-card mh-tb-template-item" data-type="<?php echo esc_attr( $template_type ); ?>">
            <div class="mh-tb-card-top">
                <div class="mh-tb-card-icon"><i class="dashicons <?php echo esc_attr( $icon ); ?>"></i></div>
                <span class="mh-tb-badge"><?php echo esc_html( $badge ); ?></span>
            </div>

            <div class="mh-tb-card-content">
                <h3 class="mh-tb-card-title"><?php the_title(); ?></h3>

                <div class="mh-tb-status-toggle">
                    <span class="mh-tb-status-label"><?php esc_html_e( 'Active Status', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                    <label class="switch">
                        <input class="cb mh-tb-status-cb" type="checkbox"
                               data-id="<?php echo esc_attr( $template_id ); ?>"
                               <?php echo esc_attr( $is_active ); ?> />
                        <span class="toggle">
                            <span class="left">off</span>
                            <span class="right">on</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="mh-tb-card-actions">
                <a href="<?php echo esc_url( $edit_url ); ?>" class="mh-tb-edit-link">
                    <i class="dashicons dashicons-edit"></i>
                    <?php esc_html_e( 'Edit', 'mh-plug-ecommerce-builder-widgets' ); ?>
                </a>
                <button class="mh-tb-delete-btn" data-id="<?php echo esc_attr( $template_id ); ?>">
                    <i class="dashicons dashicons-trash"></i>
                </button>
            </div>
        </div>
                <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>

    </div>
    
    <div class="mh-tb-modal" id="mh-tb-modal">
        <div class="mh-tb-modal-content">
            <div class="mh-tb-modal-header">
                <h2><?php esc_html_e( 'Create New Template', 'mh-plug-ecommerce-builder-widgets' ); ?></h2>
                <span class="mh-tb-modal-close" id="mh-tb-modal-close">
                    <i class="dashicons dashicons-no-alt"></i>
                </span>
            </div>

            <form id="mh-tb-create-form" class="mh-tb-form">
                <div class="mh-tb-form-group">
                    <label for="mh_tb_template_name"><?php esc_html_e( 'Template Name', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                    <input type="text" id="mh_tb_template_name" name="mh_tb_template_name" required
                           placeholder="<?php esc_attr_e( 'e.g., Main Header', 'mh-plug-ecommerce-builder-widgets' ); ?>" />
                </div>

                <div class="mh-tb-form-group">
                    <label for="mh_tb_template_type"><?php esc_html_e( 'Template Type', 'mh-plug-ecommerce-builder-widgets' ); ?></label>
                    <select id="mh_tb_template_type" name="mh_tb_template_type" required>
                        <option value="header"><?php esc_html_e( 'Header', 'mh-plug-ecommerce-builder-widgets' ); ?></option>
                        <option value="footer"><?php esc_html_e( 'Footer', 'mh-plug-ecommerce-builder-widgets' ); ?></option>
                        <option value="single_post"><?php esc_html_e( 'Single Post', 'mh-plug-ecommerce-builder-widgets' ); ?></option>
                        <option value="archive_post"><?php esc_html_e( 'Archive', 'mh-plug-ecommerce-builder-widgets' ); ?></option>
                        
                        <option value="post_category"><?php esc_html_e( 'Post Category', 'mh-plug-ecommerce-builder-widgets' ); ?></option>
                        <option value="product_category" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Product Category', 'mh-plug-ecommerce-builder-widgets' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug-ecommerce-builder-widgets' ) . ')'; ?>
                        </option>
                        <option value="product_tag" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Product Tag', 'mh-plug-ecommerce-builder-widgets' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug-ecommerce-builder-widgets' ) . ')'; ?>
                        </option>
                        <option value="product_brand" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Product Brand', 'mh-plug-ecommerce-builder-widgets' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug-ecommerce-builder-widgets' ) . ')'; ?>
                        </option>

                        <option value="archive_product" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Product Archive', 'mh-plug-ecommerce-builder-widgets' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug-ecommerce-builder-widgets' ) . ')'; ?>
                        </option>
                        <option value="single_product" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Single Product', 'mh-plug-ecommerce-builder-widgets' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug-ecommerce-builder-widgets' ) . ')'; ?>
                        </option>
                        <option value="quick_view" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Quick View Popup', 'mh-plug-ecommerce-builder-widgets' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug-ecommerce-builder-widgets' ) . ')'; ?>
                        </option>
                        
                        <option value="custom"><?php esc_html_e( 'Custom (Use Anywhere)', 'mh-plug-ecommerce-builder-widgets' ); ?></option>
                    </select>
                </div>

                <div class="mh-tb-form-actions">
                    <?php wp_nonce_field( 'mh_tb_create_template', 'mh_tb_nonce' ); ?>
                    <button type="submit" class="mh-button mh-tb-submit-btn">
                        <span class="mh-button-content-wrapper">
                            <span class="mh-button-icon mh-button-icon-left"><i class="dashicons dashicons-edit"></i></span>
                            <span class="mh-button-text"><?php esc_html_e( 'Save & Edit', 'mh-plug-ecommerce-builder-widgets' ); ?></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>