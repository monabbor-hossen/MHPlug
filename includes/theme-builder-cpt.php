<?php
/**
 * MH Plug - Theme Builder CPT Registration & AJAX Handlers
 *
 * Meta-key convention (authoritative):
 *   _mh_template_type   — private meta, strict slugs:
 *                           header | footer
 *                           single_post | single_product
 *                           archive_post | archive_product
 *   _mh_template_active — 'yes' | 'no'
 *
 * Legacy read-compat: old code stored `mh_template_type` (no underscore prefix).
 * mh_plug_get_active_template() reads both keys via an OR meta_query so older
 * templates continue to work until an admin saves them again.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// Allowed template type slugs (single source of truth)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Returns the whitelisted template type slugs.
 *
 * Everything that touches template types MUST validate against this list.
 *
 * @return string[]
 */
function mh_plug_allowed_template_types() {
    return [ 'header', 'footer', 'single_post', 'single_product', 'archive_post', 'archive_product' ];
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
        'show_ui'             => false,
        'show_in_menu'        => false,
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => false,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => true, // required for Elementor editor iframe
        'capability_type'     => 'post',
    ] );
}
add_action( 'init', 'mh_plug_register_template_cpt', 0 );

// ─────────────────────────────────────────────────────────────────────────────
// Self-healing: keep mh_templates in Elementor's supported post types list
// ─────────────────────────────────────────────────────────────────────────────

function mh_plug_add_cpt_elementor_support() {
    $cpt_support = get_option( 'elementor_cpt_support', [ 'page', 'post' ] );
    if ( ! in_array( 'mh_templates', $cpt_support, true ) ) {
        $cpt_support[] = 'mh_templates';
        update_option( 'elementor_cpt_support', $cpt_support );
    }
    // Explicitly add Elementor support
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

    // Strict whitelist — reject anything not in our allowed list
    if ( ! in_array( $template_type, mh_plug_allowed_template_types(), true ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid template type.', 'mh-plug' ) ] );
    }

    // ── Insert the post ────────────────────────────────────────────────────
    $post_id = wp_insert_post( [
        'post_title'  => $template_name,
        'post_status' => 'publish',
        'post_type'   => 'mh_templates',
    ] );

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( [ 'message' => $post_id->get_error_message() ] );
    }

    // ── Save authoritative private meta (new key) ──────────────────────────
    update_post_meta( $post_id, '_mh_template_type',   $template_type );
    update_post_meta( $post_id, '_mh_template_active', 'yes' );

    // ── Map to Elementor's internal template type ──────────────────────────
    // Elementor Free uses 'wp-post' as a generic post template.
    // We store our own meta for the real distinction.
    $elementor_type_map = [
        'header'          => 'header',
        'footer'          => 'footer',
        'single_post'     => 'wp-post',
        'single_product'  => 'single-product',
        'archive_post'    => 'archive',
        'archive_product' => 'archive',
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
    $is_active   = ( isset( $_POST['is_active'] ) && $_POST['is_active'] === 'true' ) ? 'yes' : 'no';

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
// single_template: Force our canvas for all mh_templates singular views
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Routes every singular mh_templates request through mh-canvas.php.
 * Priority 999 overrides any theme-level template filters.
 */
function mh_force_elementor_canvas( $template ) {
    if ( is_singular( 'mh_templates' ) ) {
        $canvas = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
        if ( file_exists( $canvas ) ) {
            return $canvas;
        }
    }
    return $template;
}
add_filter( 'single_template', 'mh_force_elementor_canvas', 999 );


// ─────────────────────────────────────────────────────────────────────────────
// Helper: read template type meta with backward-compat fallback
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Returns the template type for a given post, preferring the new private meta
 * key `_mh_template_type` and falling back to the old `mh_template_type`.
 *
 * Legacy slug normalisation is applied so old templates work transparently:
 *   single        → single_post
 *   product_single → single_product
 *   archive        → archive_post
 *   product_archive → archive_product
 *
 * @param  int    $post_id
 * @return string Authoritative slug, or '' if not set.
 */
function mh_plug_get_template_type_meta( $post_id ) {
    $type = get_post_meta( $post_id, '_mh_template_type', true );

    // Fall back to legacy key if new key is empty
    if ( empty( $type ) ) {
        $type = get_post_meta( $post_id, 'mh_template_type', true );
    }

    // Normalise legacy slugs to new canonical slugs
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

// ─────────────────────────────────────────────────────────────────────────────
// Helper: read active status meta with backward-compat fallback
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Returns 'yes' or 'no' for the active status of a template post.
 *
 * @param  int    $post_id
 * @return string 'yes' | 'no'
 */
function mh_plug_get_template_active_meta( $post_id ) {
    $active = get_post_meta( $post_id, '_mh_template_active', true );
    if ( empty( $active ) ) {
        $active = get_post_meta( $post_id, 'mh_template_active', true );
    }
    return ( $active === 'yes' ) ? 'yes' : 'no';
}
