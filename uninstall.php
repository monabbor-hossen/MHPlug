<?php
/**
 * MH Plug Uninstall Script
 *
 * This file is automatically executed when the user deletes the plugin
 * from the WordPress Dashboard. It handles cleaning up custom tables
 * and options to ensure the plugin leaves no orphaned data.
 *
 * @package MH_Plug
 */

// Exit if accessed directly or if uninstall not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// 1. Drop the custom Wishlist table.
$table_name = $wpdb->prefix . 'mh_woocommerce_wishlist';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

// 2. Delete all plugin settings/options.
$options_to_delete = [
    'mh_plug_widgets_settings',
    'mh_plug_preloader_settings',
    'mh_plug_mini_cart_settings',
    'mh_plug_woo_pages_settings',
    'mh_age_gate_options',
    // Legacy/fallback names observed in codebase
    'mh_plug_settings',
    'mh_plug_version'
];

foreach ( $options_to_delete as $option ) {
    delete_option( $option );
}
