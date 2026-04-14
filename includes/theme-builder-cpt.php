<?php
/**
 * Registers the Custom Post Type for Theme Builder Templates.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the mh_templates Custom Post Type
 */
function mh_plug_register_template_cpt() {
    $labels = [
        'name'                  => _x('MH Templates', 'Post Type General Name', 'mh-plug'),
        'singular_name'         => _x('MH Template', 'Post Type Singular Name', 'mh-plug'),
        'menu_name'             => __('MH Templates', 'mh-plug'),
        'name_admin_bar'        => __('MH Template', 'mh-plug'),
        'add_new'               => __('Add New', 'mh-plug'),
        'add_new_item'          => __('Add New Template', 'mh-plug'),
        'edit_item'             => __('Edit Template', 'mh-plug'),
        'view_item'             => __('View Template', 'mh-plug'),
    ];

    $args = [
        'label'                 => __('MH Template', 'mh-plug'),
        'labels'                => $labels,
        'supports'              => ['title', 'editor', 'elementor'], // Elementor support is crucial
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => false, // Hide from standard menu, we have our own UI
        'show_in_menu'          => false,
        'menu_position'         => 50,
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true, // Needed for Elementor rendering
        'capability_type'       => 'post',
    ];

    register_post_type('mh_templates', $args);
}
add_action('init', 'mh_plug_register_template_cpt', 0);

/**
 * Ensure `mh_templates` is added to Elementor's supported post types 
 * upon plugin activation (or just passively hook it).
 */
function mh_plug_add_cpt_elementor_support() {
    $cpt_support = get_option('elementor_cpt_support', ['page', 'post']);
    
    if ( !in_array('mh_templates', $cpt_support) ) {
        $cpt_support[] = 'mh_templates';
        update_option('elementor_cpt_support', $cpt_support);
    }
}
// Run this on init to ensure it continuously supports Elementor, acting like a self-healing fix.
add_action('init', 'mh_plug_add_cpt_elementor_support');

/**
 * Handle Theme Builder AJAX Template Creation
 */
function mh_plug_ajax_create_template() {
    check_ajax_referer('mh_tb_create_template', '_ajax_nonce');

    if ( !current_user_can('edit_posts') ) {
        wp_send_json_error(['message' => __('You do not have permission to do this.', 'mh-plug')]);
    }

    $template_name = isset($_POST['template_name']) ? sanitize_text_field($_POST['template_name']) : '';
    $template_type = isset($_POST['template_type']) ? sanitize_text_field($_POST['template_type']) : 'single';

    if ( empty($template_name) ) {
        wp_send_json_error(['message' => __('Template name is required.', 'mh-plug')]);
    }

    // Insert Post
    $post_data = [
        'post_title'  => $template_name,
        'post_status' => 'publish',
        'post_type'   => 'mh_templates',
    ];

    $post_id = wp_insert_post($post_data);

    if ( is_wp_error($post_id) ) {
        wp_send_json_error(['message' => $post_id->get_error_message()]);
    }

    // Save Template Type Meta
    update_post_meta($post_id, 'mh_template_type', $template_type);
    
    // Elementor Specific Metadata to identify template type
    $elementor_type = 'wp-post'; // Default
    if ( $template_type === 'header' ) $elementor_type = 'header';
    if ( $template_type === 'footer' ) $elementor_type = 'footer';
    if ( $template_type === 'archive' || $template_type === 'product_archive' ) $elementor_type = 'archive';
    if ( $template_type === 'product_single' ) $elementor_type = 'single-product';
    
    update_post_meta($post_id, '_elementor_template_type', $elementor_type);
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    
    // Set default status to active if you want newly created to be active
    update_post_meta($post_id, 'mh_template_active', 'yes');

    // Return exact Elementor edit URL
    $edit_url = admin_url( 'post.php?post=' . $post_id . '&action=elementor' );

    wp_send_json_success([
        'message'  => __('Template created successfully.', 'mh-plug'),
        'edit_url' => $edit_url
    ]);
}
add_action('wp_ajax_mh_tb_create_template', 'mh_plug_ajax_create_template');

/**
 * Handle Theme Builder AJAX Toggle Status
 */
function mh_plug_ajax_toggle_status() {
    // Note: In your admin setup, might want to use a more specific nonce check if needed
    if ( !current_user_can('edit_posts') ) {
        wp_send_json_error(['message' => __('You do not have permission to do this.', 'mh-plug')]);
    }

    $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
    $is_active   = isset($_POST['is_active']) && $_POST['is_active'] === 'true' ? 'yes' : 'no';

    if ( !$template_id ) {
        wp_send_json_error(['message' => __('Invalid ID.', 'mh-plug')]);
    }

    update_post_meta($template_id, 'mh_template_active', $is_active);

    wp_send_json_success(['message' => __('Status updated.', 'mh-plug')]);
}
add_action('wp_ajax_mh_tb_toggle_status', 'mh_plug_ajax_toggle_status');
