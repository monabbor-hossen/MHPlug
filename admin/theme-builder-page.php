<?php
/**
 * MH Plug - Theme Builder Admin Page
 *
 * Template type slugs (authoritative):
 * header | footer | single_post | single_product | archive_post | archive_product | quick_view | post_category | product_category | custom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_wc_active = class_exists( 'WooCommerce' );
?>

<div class="wrap mh-plug-admin-wrap">

    <style>
        /* 🚀 THE FIX: Bulletproof Buttons */
        .mh-plug-admin-wrap .mh-button {
            background: #004265 !important; /* Forced Plugin Brand Color */
            color: #ffffff !important;
            border: none !important;
            padding: 10px 24px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 10px rgba(0, 66, 101, 0.2) !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            outline: none !important;
        }
        .mh-plug-admin-wrap .mh-button:hover {
            background: #002b42 !important; /* Darker Brand Color */
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(0, 66, 101, 0.3) !important;
            color: #ffffff !important;
        }
        .mh-plug-admin-wrap .mh-button-content-wrapper {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        .mh-plug-admin-wrap .mh-button .dashicons {
            font-size: 18px !important;
            line-height: 1 !important;
            width: 18px !important;
            height: 18px !important;
            transition: transform 0.3s ease !important;
        }
        .mh-plug-admin-wrap .mh-button:hover .dashicons {
            transform: scale(1.1) !important;
        }

        /* Modal Polish */
        .mh-tb-modal-content {
            border-radius: 12px !important;
            overflow: hidden !important;
            background: #ffffff !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
            padding: 0 !important;
            border: 1px solid #e0e0e0 !important;
        }
        .mh-tb-modal-header {
            background: #f8f9fa !important;
            border-bottom: 1px solid #e0e0e0 !important;
            padding: 20px 25px !important;
        }
        .mh-tb-modal-header h2 {
            margin: 0 !important;
            color: #004265 !important;
            font-weight: 700 !important;
        }
        .mh-tb-form {
            padding: 25px !important;
        }

        /* 🚀 THE FIX: Bulletproof Form Inputs (Kills WordPress 3D Shadows) */
        .mh-tb-form-group {
            margin-bottom: 20px !important;
        }
        .mh-tb-form-group label {
            display: block !important;
            margin-bottom: 8px !important;
            font-weight: 600 !important;
            color: #2c3338 !important;
            font-size: 13px !important;
        }
        .mh-tb-form-group input[type="text"],
        .mh-tb-form-group select {
            width: 100% !important;
            height: auto !important;
            padding: 10px 14px !important;
            font-size: 14px !important;
            line-height: 1.5 !important;
            border: 1px solid #8c8f94 !important;
            border-radius: 4px !important;
            background-color: #ffffff !important;
            color: #2c3338 !important;
            box-shadow: none !important; /* 🚀 Kills the ugly puffy shadow */
            transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
            box-sizing: border-box !important;
            appearance: none !important; 
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }
        
        /* Custom SVG Arrow for Dropdown */
        .mh-tb-form-group select {
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%207l5%205%205-5%22%20stroke%3D%22%23555%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 16px !important;
            padding-right: 40px !important;
            cursor: pointer !important;
        }

        /* Focus States (Glowing Brand Color) */
        .mh-tb-form-group input[type="text"]:focus,
        .mh-tb-form-group select:focus {
            border-color: #004265 !important;
            box-shadow: 0 0 0 1px #004265 !important; /* Clean flat focus ring */
            outline: none !important;
        }

        /* Modal 'Save & Edit' Button */
        .mh-tb-form-actions {
            margin-top: 30px !important;
        }
        .mh-tb-modal-content .mh-tb-submit-btn {
            width: 100% !important;
            padding: 14px !important;
            font-size: 15px !important;
        }
    </style>

    <div class="mh-tb-header">
        <div class="mh-tb-header-text">
            <h1 class="mh-plug-title"><?php esc_html_e( 'Theme Builder', 'mh-plug' ); ?></h1>
            <p class="mh-tb-description"><?php esc_html_e( 'Create and manage custom Elementor templates for headers, footers, single posts, products, archives, categories, and generic layouts.', 'mh-plug' ); ?></p>
        </div>
        <div class="mh-tb-header-actions">
            <button class="mh-button mh-tb-action-create" id="mh-tb-create-btn">
                <span class="mh-button-content-wrapper">
                    <span class="mh-button-icon mh-button-icon-left"><i class="dashicons dashicons-plus-alt2"></i></span>
                    <span class="mh-button-text"><?php esc_html_e( 'Create Template', 'mh-plug' ); ?></span>
                </span>
            </button>
        </div>
    </div>

    <ul class="mh-tb-tabs">
        <li data-tab="all" class="active"><?php esc_html_e( 'All', 'mh-plug' ); ?></li>
        <li data-tab="header"><?php esc_html_e( 'Header', 'mh-plug' ); ?></li>
        <li data-tab="footer"><?php esc_html_e( 'Footer', 'mh-plug' ); ?></li>
        <li data-tab="single_post"><?php esc_html_e( 'Single Post', 'mh-plug' ); ?></li>
        <li data-tab="archive_post"><?php esc_html_e( 'Archive', 'mh-plug' ); ?></li>
        <li data-tab="post_category"><?php esc_html_e( 'Post Category', 'mh-plug' ); ?></li>
        <li data-tab="custom"><?php esc_html_e( 'Custom', 'mh-plug' ); ?></li>
        
        <li data-tab="archive_product" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Product Archive', 'mh-plug' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="product_category" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Product Category', 'mh-plug' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="single_product" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Single Product', 'mh-plug' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug' ); ?>)</span>
            <?php endif; ?>
        </li>
        <li data-tab="quick_view" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e( 'Quick View', 'mh-plug' ); ?>
            <?php if ( ! $is_wc_active ) : ?>
                <span class="mh-tb-wc-req">(<?php esc_html_e( 'Requires Woo', 'mh-plug' ); ?>)</span>
            <?php endif; ?>
        </li>
    </ul>

    <div class="mh-tb-grid">

        <div class="mh-tb-card mh-tb-card-add-new" id="mh-tb-card-add-new" data-type="all">
            <div class="mh-tb-add-icon"><i class="dashicons dashicons-plus"></i></div>
            <h3><?php esc_html_e( 'Add New', 'mh-plug' ); ?></h3>
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
            'quick_view'       => 'dashicons-visibility', 
            'custom'           => 'dashicons-layout',
        ];

        $label_map = [
            'header'           => __( 'Header', 'mh-plug' ),
            'footer'           => __( 'Footer', 'mh-plug' ),
            'single_post'      => __( 'Single Post', 'mh-plug' ),
            'single_product'   => __( 'Single Product', 'mh-plug' ),
            'archive_post'     => __( 'Archive', 'mh-plug' ),
            'archive_product'  => __( 'Product Archive', 'mh-plug' ),
            'post_category'    => __( 'Post Category', 'mh-plug' ), 
            'product_category' => __( 'Product Category', 'mh-plug' ), 
            'quick_view'       => __( 'Quick View', 'mh-plug' ),
            'custom'           => __( 'Custom Template', 'mh-plug' ),
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
                    <span class="mh-tb-status-label"><?php esc_html_e( 'Active Status', 'mh-plug' ); ?></span>
                    <label class="switch">
                        <input class="cb mh-tb-status-cb" type="checkbox"
                               data-id="<?php echo esc_attr( $template_id ); ?>"
                               <?php echo $is_active; ?> />
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
                    <?php esc_html_e( 'Edit', 'mh-plug' ); ?>
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
                <h2><?php esc_html_e( 'Create New Template', 'mh-plug' ); ?></h2>
                <span class="mh-tb-modal-close" id="mh-tb-modal-close">
                    <i class="dashicons dashicons-no-alt"></i>
                </span>
            </div>

            <form id="mh-tb-create-form" class="mh-tb-form">
                <div class="mh-tb-form-group">
                    <label for="mh_tb_template_name"><?php esc_html_e( 'Template Name', 'mh-plug' ); ?></label>
                    <input type="text" id="mh_tb_template_name" name="mh_tb_template_name" required
                           placeholder="<?php esc_attr_e( 'e.g., Main Header', 'mh-plug' ); ?>" />
                </div>

                <div class="mh-tb-form-group">
                    <label for="mh_tb_template_type"><?php esc_html_e( 'Template Type', 'mh-plug' ); ?></label>
                    <select id="mh_tb_template_type" name="mh_tb_template_type" required>
                        <option value="header"><?php esc_html_e( 'Header', 'mh-plug' ); ?></option>
                        <option value="footer"><?php esc_html_e( 'Footer', 'mh-plug' ); ?></option>
                        <option value="single_post"><?php esc_html_e( 'Single Post', 'mh-plug' ); ?></option>
                        <option value="archive_post"><?php esc_html_e( 'Archive', 'mh-plug' ); ?></option>
                        
                        <option value="post_category"><?php esc_html_e( 'Post Category', 'mh-plug' ); ?></option>
                        <option value="product_category" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Product Category', 'mh-plug' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug' ) . ')'; ?>
                        </option>

                        <option value="archive_product" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Product Archive', 'mh-plug' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug' ) . ')'; ?>
                        </option>
                        <option value="single_product" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Single Product', 'mh-plug' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug' ) . ')'; ?>
                        </option>
                        <option value="quick_view" <?php disabled( ! $is_wc_active ); ?>>
                            <?php esc_html_e( 'Quick View Popup', 'mh-plug' ); ?>
                            <?php if ( ! $is_wc_active ) echo ' (' . esc_html__( 'Requires WooCommerce', 'mh-plug' ) . ')'; ?>
                        </option>
                        
                        <option value="custom"><?php esc_html_e( 'Custom (Use Anywhere)', 'mh-plug' ); ?></option>
                    </select>
                </div>

                <div class="mh-tb-form-actions">
                    <?php wp_nonce_field( 'mh_tb_create_template', 'mh_tb_nonce' ); ?>
                    <button type="submit" class="mh-button mh-tb-submit-btn">
                        <span class="mh-button-content-wrapper">
                            <span class="mh-button-icon mh-button-icon-left"><i class="dashicons dashicons-edit"></i></span>
                            <span class="mh-button-text"><?php esc_html_e( 'Save & Edit', 'mh-plug' ); ?></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div><script>
    var mhTbAjaxUrl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
</script>