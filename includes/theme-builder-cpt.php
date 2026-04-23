<?php
/**
 * MH Plug - Theme Builder CPT Registration & AJAX Handlers
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// Allowed template type slugs
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_allowed_template_types() {
    // 🚀 NEW: Added 'product_category' and 'post_category'
    return [ 'header', 'footer', 'single_post', 'single_product', 'archive_post', 'archive_product', 'quick_view', 'product_category', 'post_category' ];
}

// ─────────────────────────────────────────────────────────────────────────────
// Register the mh_templates Custom Post Type
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_register_template_cpt() {
    $labels = [
        'name'          => _x( 'MH Templates', 'Post Type General Name', 'mh-plug' ),
        'singular_name' => _x( 'MH Template',  'Post Type Singular Name', 'mh-plug' ),
        'menu_name'     => __( 'MH Templates', 'mh-plug' ),
        'add_new_item'  => __( 'Add New Template', 'mh-plug' ),
        'edit_item'     => __( 'Edit Template', 'mh-plug' ),
        'view_item'     => __( 'View Template', 'mh-plug' ),
    ];

    register_post_type( 'mh_templates', [
        'label'               => __( 'MH Template', 'mh-plug' ),
        'labels'              => $labels,
        'supports'            => [ 'title', 'editor', 'elementor' ],
        'hierarchical'        => false,
        'public'              => true,
        // 🚀 THE FIX: show_ui MUST be true for Elementor to load the editor!
        'show_ui'             => true, 
        // We keep this false so it doesn't clutter the main WP sidebar
        'show_in_menu'        => false,
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => false,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => true, 
        // 🚀 THE FIX: Added an explicit rewrite rule for clean URLs
        'rewrite'             => [ 'slug' => 'mh-template' ], 
        'capability_type'     => 'post',
    ] );
}
add_action( 'init', 'mh_plug_register_template_cpt', 0 );

function mh_plug_add_cpt_elementor_support() {
    $cpt_support = get_option( 'elementor_cpt_support', [ 'page', 'post' ] );
    if ( ! in_array( 'mh_templates', $cpt_support, true ) ) {
        $cpt_support[] = 'mh_templates';
        update_option( 'elementor_cpt_support', $cpt_support );
    }
    add_post_type_support( 'mh_templates', 'elementor' );
}
add_action( 'init', 'mh_plug_add_cpt_elementor_support' );

// ─────────────────────────────────────────────────────────────────────────────
// AJAX: Create Template
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_ajax_create_template() {
    check_ajax_referer( 'mh_tb_create_template', '_ajax_nonce' );

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( [ 'message' => __( 'You do not have permission to do this.', 'mh-plug' ) ] );
    }

    $template_name = isset( $_POST['template_name'] ) ? sanitize_text_field( $_POST['template_name'] ) : '';
    $template_type = isset( $_POST['template_type'] ) ? sanitize_key( $_POST['template_type'] )        : '';

    if ( empty( $template_name ) ) {
        wp_send_json_error( [ 'message' => __( 'Template name is required.', 'mh-plug' ) ] );
    }

    if ( ! in_array( $template_type, mh_plug_allowed_template_types(), true ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid template type.', 'mh-plug' ) ] );
    }

    $post_id = wp_insert_post( [
        'post_title'  => $template_name,
        'post_status' => 'publish',
        'post_type'   => 'mh_templates',
    ] );

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( [ 'message' => $post_id->get_error_message() ] );
    }

    update_post_meta( $post_id, '_mh_template_type',   $template_type );
    update_post_meta( $post_id, '_mh_template_active', 'yes' );

$elementor_type_map = [
        'header'           => 'header',
        'footer'           => 'footer',
        'single_post'      => 'wp-post',
        'single_product'   => 'single-product',
        'archive_post'     => 'archive',
        'archive_product'  => 'archive',
        'product_category' => 'archive', // 🚀 NEW
        'post_category'    => 'archive', // 🚀 NEW
        'quick_view'       => 'single-product', 
    ];
    $elementor_type = $elementor_type_map[ $template_type ] ?? 'wp-post';

    update_post_meta( $post_id, '_elementor_template_type', $elementor_type );
    update_post_meta( $post_id, '_elementor_edit_mode',     'builder' );

    wp_send_json_success( [
        'message'  => __( 'Template created successfully.', 'mh-plug' ),
        'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=elementor' ),
    ] );
}
add_action( 'wp_ajax_mh_tb_create_template', 'mh_plug_ajax_create_template' );

// ─────────────────────────────────────────────────────────────────────────────
// AJAX: Toggle Active Status
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_ajax_toggle_status() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( [ 'message' => __( 'You do not have permission to do this.', 'mh-plug' ) ] );
    }

    $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
    
    $is_active_raw = isset( $_POST['is_active'] ) ? $_POST['is_active'] : false;
    $is_active = ( filter_var( $is_active_raw, FILTER_VALIDATE_BOOLEAN ) ) ? 'yes' : 'no';

    if ( ! $template_id ) {
        wp_send_json_error( [ 'message' => __( 'Invalid ID.', 'mh-plug' ) ] );
    }

    update_post_meta( $template_id, '_mh_template_active', $is_active );
    wp_send_json_success( [ 'message' => __( 'Status updated.', 'mh-plug' ) ] );
}
add_action( 'wp_ajax_mh_tb_toggle_status', 'mh_plug_ajax_toggle_status' );

// ─────────────────────────────────────────────────────────────────────────────
// AJAX: Delete Template
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_ajax_delete_template() {
    check_ajax_referer( 'mh_tb_delete_template', '_ajax_nonce' );

    if ( ! current_user_can( 'delete_posts' ) ) {
        wp_send_json_error( [ 'message' => __( 'You do not have permission to do this.', 'mh-plug' ) ] );
    }

    $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;

    if ( ! $template_id || get_post_type( $template_id ) !== 'mh_templates' ) {
        wp_send_json_error( [ 'message' => __( 'Invalid template.', 'mh-plug' ) ] );
    }

    $result = wp_trash_post( $template_id );

    if ( ! $result ) {
        wp_send_json_error( [ 'message' => __( 'Could not delete template. Please try again.', 'mh-plug' ) ] );
    }

    wp_send_json_success( [ 'message' => __( 'Template deleted.', 'mh-plug' ) ] );
}
add_action( 'wp_ajax_mh_tb_delete_template', 'mh_plug_ajax_delete_template' );


// ─────────────────────────────────────────────────────────────────────────────
// 🚀 THE FIX: Force Elementor's Native Canvas for all mh_templates!
// ─────────────────────────────────────────────────────────────────────────────
function mh_force_elementor_canvas( $template ) {
    if ( is_singular( 'mh_templates' ) ) {
        // Borrow Elementor's official canvas template so we NEVER get the 'the_content' error!
        if ( defined( 'ELEMENTOR_PATH' ) ) {
            $canvas = ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';
            if ( file_exists( $canvas ) ) {
                return $canvas;
            }
        }
    }
    return $template;
}
// Note: We changed this from 'single_template' to 'template_include' for better overrides!
add_filter( 'template_include', 'mh_force_elementor_canvas', 999 );
// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_get_template_type_meta( $post_id ) {
    $type = get_post_meta( $post_id, '_mh_template_type', true );
    if ( empty( $type ) ) {
        $type = get_post_meta( $post_id, 'mh_template_type', true );
    }
    $legacy_map = [
        'single'          => 'single_post',
        'product_single'  => 'single_product',
        'archive'         => 'archive_post',
        'product_archive' => 'archive_product',
    ];
    if ( isset( $legacy_map[ $type ] ) ) {
        $type = $legacy_map[ $type ];
    }
    return (string) $type;
}

function mh_plug_get_template_active_meta( $post_id ) {
    $active = get_post_meta( $post_id, '_mh_template_active', true );
    if ( empty( $active ) ) {
        $active = get_post_meta( $post_id, 'mh_template_active', true );
    }
    return ( $active === 'yes' ) ? 'yes' : 'no';
}

// ─────────────────────────────────────────────────────────────────────────────
// 🚀 PRO FEATURE: INDIVIDUAL CATEGORY TEMPLATE SELECTOR
// ─────────────────────────────────────────────────────────────────────────────

// 1. Add Dropdown to "Create New Category" screen
function mh_plug_category_template_add_field() {
    $templates = get_posts([ 'post_type' => 'mh_templates', 'posts_per_page' => -1, 'post_status' => 'publish' ]);
    ?>
    <div class="form-field">
        <label for="mh_category_template"><?php _e( 'MH Custom Template (Optional)', 'mh-plug' ); ?></label>
        <select name="mh_category_template" id="mh_category_template">
            <option value=""><?php _e( '— Use Default Archive Template —', 'mh-plug' ); ?></option>
            <?php foreach ( $templates as $tpl ) : ?>
                <option value="<?php echo esc_attr( $tpl->ID ); ?>"><?php echo esc_html( $tpl->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
        <p><?php _e( 'Select a specific Elementor template for this category. This will override the global archive template.', 'mh-plug' ); ?></p>
    </div>
    <?php
}

// 2. Add Dropdown to "Edit Category" screen
function mh_plug_category_template_edit_field( $term ) {
    $templates = get_posts([ 'post_type' => 'mh_templates', 'posts_per_page' => -1, 'post_status' => 'publish' ]);
    $current   = get_term_meta( $term->term_id, '_mh_category_template', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="mh_category_template"><?php _e( 'MH Custom Template', 'mh-plug' ); ?></label></th>
        <td>
            <select name="mh_category_template" id="mh_category_template">
                <option value=""><?php _e( '— Use Default Archive Template —', 'mh-plug' ); ?></option>
                <?php foreach ( $templates as $tpl ) : ?>
                    <option value="<?php echo esc_attr( $tpl->ID ); ?>" <?php selected( $current, $tpl->ID ); ?>>
                        <?php echo esc_html( $tpl->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php _e( 'Select a specific Elementor template for this category. This will override the global archive template.', 'mh-plug' ); ?></p>
        </td>
    </tr>
    <?php
}

// 3. Save the Data
function mh_plug_save_category_template( $term_id ) {
    if ( isset( $_POST['mh_category_template'] ) ) {
        update_term_meta( $term_id, '_mh_category_template', sanitize_text_field( $_POST['mh_category_template'] ) );
    }
}

// Hook into WooCommerce Product Categories
add_action( 'product_cat_add_form_fields', 'mh_plug_category_template_add_field' );
add_action( 'product_cat_edit_form_fields', 'mh_plug_category_template_edit_field' );
add_action( 'created_product_cat', 'mh_plug_save_category_template' );
add_action( 'edited_product_cat', 'mh_plug_save_category_template' );

// Hook into Standard WordPress Post Categories
add_action( 'category_add_form_fields', 'mh_plug_category_template_add_field' );
add_action( 'category_edit_form_fields', 'mh_plug_category_template_edit_field' );
add_action( 'created_category', 'mh_plug_save_category_template' );
add_action( 'edited_category', 'mh_plug_save_category_template' );