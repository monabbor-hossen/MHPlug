<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * ===================================================================
 * MH Plug - Menu Icon Picker
 * ===================================================================
 *
 * This file handles:
 * 1. Enqueuing assets (CSS/JS/Font Awesome) on the nav-menus.php page.
 * 2. Adding the "Select Icon" button to each menu item.
 * 3. Saving the icon meta data.
 * 4. Displaying the icon on the frontend.
 * 5. Injecting the Icon Picker Modal HTML into the footer.
 */


// --- 1. Enqueue Scripts & Styles ---
add_action( 'admin_enqueue_scripts', 'mh_plug_enqueue_menu_picker_assets' );
/**
 * Enqueue assets only on the nav-menus.php admin page.
 */
function mh_plug_enqueue_menu_picker_assets( $hook ) {
    // Only load on the 'Appearance > Menus' page
    if ( 'nav-menus.php' !== $hook ) {
        return;
    }

    $plugin_url = plugin_dir_url( __FILE__ ); // Gets URL of /includes/ folder
    $assets_url = $plugin_url . '../admin/assets/'; // Moves up to /admin/assets/

    // Enqueue our custom modal styles and button styles
    wp_enqueue_style(
        'mh-plug-menu-picker-css',
        $assets_url . 'menu-icon-picker.css',
        [],
        '1.1.0' // <-- Update version if you change CSS
    );

    // --- Enqueue Font Awesome 7 CSS ---
    wp_enqueue_style(
        'mh-plug-fontawesome-7',
        $assets_url . 'fontawesome/css/all.min.css', // <-- Make sure path is correct
        [],
        '7.1.0' // <-- Match your FA version
    );

    // Enqueue our modal JavaScript
    wp_enqueue_script(
        'mh-plug-menu-picker-js',
        $assets_url . 'menu-icon-picker.js',
        [ 'jquery' ],
        '1.1.0', // <-- Update version if you change JS
        true
    );
}


// --- 2. Add Custom Fields to Menu Item ---
add_action( 'wp_nav_menu_item_custom_fields', 'mh_plug_add_menu_item_icon_field', 10, 5 );
/**
 * Add the "Select Icon" button and "Hide Label" switch to each menu item.
 */
function mh_plug_add_menu_item_icon_field( $item_id, $item, $depth, $args, $id = 0 ) {
    // Get the actual DB ID
    $menu_item_db_id = $item->ID;

    // Get Icon Picker Variables
    $icon_input_id = "edit-menu-item-icon-" . esc_attr( $menu_item_db_id );
    $icon_input_name = "menu-item-icon[" . esc_attr( $menu_item_db_id ) . "]";
    $icon_class = get_post_meta( $menu_item_db_id, '_menu_item_icon', true );

    // Get Hide Label Switch Variables
    $hide_label_input_id = "edit-menu-item-hide-label-" . esc_attr( $menu_item_db_id );
    $hide_label_input_name = "menu-item-hide-label[" . esc_attr( $menu_item_db_id ) . "]";
    $hide_label_value = get_post_meta( $menu_item_db_id, '_menu_item_hide_label', true );
    $hide_label_checked = ! empty( $hide_label_value ) ? 'checked="checked"' : '';

    // Get Global Settings to check if hide label is enabled
    $settings = get_option('mh_plug_widgets_settings', []);
    // Ensure you add 'enable_hide_label_option' to your settings page and sanitize function
    $enable_hide_label_toggle = isset($settings['enable_hide_label_option']) ? (bool) $settings['enable_hide_label_option'] : false;
    ?>

    <div class="field-mh-menu-icon-container description-wide">

        <input type="hidden"
               id="<?php echo $icon_input_id; ?>"
               class="widefat edit-menu-item-icon"
               name="<?php echo $icon_input_name; ?>"
               value="<?php echo esc_attr( $icon_class ); ?>" />

        <strong class="mh-menu-icon-label"><?php _e('Icon', 'mh-plug'); ?></strong>

        <div class="mh-menu-icon-button-row">
            <button type="button"
                    id="mh-menu-icon-picker-button-<?php echo $menu_item_db_id; ?>"
                    class="mh-menu-icon-picker-button mh-plug-neumorphic-button"
                    data-itemid="<?php echo $menu_item_db_id; ?>"
                    data-target-input="#<?php echo $icon_input_id; ?>">

                <span class="mh-plug-button-icon <?php echo esc_attr($icon_class ? $icon_class : 'mhi-add-plus'); ?>"></span>
                <span class="mh-plug-button-text"><?php _e('Select Icon', 'mh-plug'); ?></span>
            </button>

            <span class="mh-menu-icon-remove"
                  data-itemid="<?php echo $menu_item_db_id; ?>"
                  <?php echo empty($icon_class) ? 'style="display: none;"' : ''; ?>>
                <?php _e('Remove', 'mh-plug'); ?>
            </span>
        </div>

        <p class="description"><?php _e('Click the button to select an icon.', 'mh-plug'); ?></p>

        <?php // This entire 3D toggle switch section will ONLY appear if its global setting is ON ?>
        <?php if (!empty($icon_class)) : ?>
            <div class="field-mh-menu-icon-hide-label">
                <div class="mh-plug-toggle-switch-container">
                    <div class="mh-plug-toggle-switch">
                        <input type="checkbox"
                               id="<?php echo $hide_label_input_id; ?>"
                               class="edit-menu-item-hide-label"
                               name="<?php echo $hide_label_input_name; ?>"
                               value="1" <?php echo $hide_label_checked; ?> />
                        <label for="<?php echo $hide_label_input_id; ?>"></label>
                    </div>
                    <label for="<?php echo $hide_label_input_id; ?>" class="mh-plug-toggle-label">
                        <?php _e('Hide Navigation Label (Only show icon)', 'mh-plug'); ?>
                    </label>
                </div>
            </div>
        <?php endif; // End conditional check ?>

    </div>
    <?php
}


// --- 3. Save Custom Fields ---
add_action( 'wp_update_nav_menu_item', 'mh_plug_save_menu_item_icon_field', 10, 3 );
/**
 * Save the Icon Class and Hide Label custom fields for a menu item.
 */
function mh_plug_save_menu_item_icon_field( $menu_id, $menu_item_db_id, $args ) {
    // Save Icon Field
    if ( isset( $_POST['menu-item-icon'][ $menu_item_db_id ] ) ) {
        $sanitized_icon_class = sanitize_text_field( trim( $_POST['menu-item-icon'][ $menu_item_db_id ] ) );
        update_post_meta( $menu_item_db_id, '_menu_item_icon', $sanitized_icon_class );
    } else {
        delete_post_meta( $menu_item_db_id, '_menu_item_icon' );
    }

    // Save Hide Label Switch
    if ( isset( $_POST['menu-item-hide-label'][ $menu_item_db_id ] ) && $_POST['menu-item-hide-label'][ $menu_item_db_id ] === '1' ) {
        update_post_meta( $menu_item_db_id, '_menu_item_hide_label', '1' );
    } else {
        delete_post_meta( $menu_item_db_id, '_menu_item_hide_label' );
    }
}


// --- 4. Display Icon on Frontend ---
add_filter( 'nav_menu_item_title', 'mh_plug_display_menu_item_icon', 10, 4 );
/**
 * Prepend the icon HTML to the menu item title on the frontend.
 * Adds screen-reader text if label is hidden.
 */
function mh_plug_display_menu_item_icon( $title, $item, $args, $depth ) {
    if ( ! is_object( $item ) || ! isset( $item->ID ) ) {
        return $title;
    }

    $icon_class = get_post_meta( $item->ID, '_menu_item_icon', true );
    $hide_label = get_post_meta( $item->ID, '_menu_item_hide_label', true );
    $icon_html = '';

    if ( ! empty( $icon_class ) ) {
        $sanitized_icon_class = esc_attr( $icon_class );
        $icon_only_class = ! empty( $hide_label ) ? ' mh-menu-icon-only' : '';
        // Add accessibility attribute
        $icon_html = '<i class="' . $sanitized_icon_class . $icon_only_class . '" aria-hidden="true"></i> ';
    }

    if ( ! empty( $hide_label ) ) {
        // If hiding label, also add a screen-reader-only text of the title for accessibility
        // Make sure you have CSS for .screen-reader-text (WordPress includes this by default)
        return $icon_html . '<span class="screen-reader-text">' . esc_html($title) . '</span>';
    } else {
        // Otherwise, return the icon (if any) prepended to the original title
        return $icon_html . $title;
    }
}


// --- 5. Add Modal HTML to Footer ---
add_action( 'admin_footer-nav-menus.php', 'mh_plug_add_icon_picker_modal_html' );
/**
 * Print the (hidden) icon picker modal HTML into the footer of the Menus page.
 */
function mh_plug_add_icon_picker_modal_html() {

    // Load our icon arrays (make sure these files exist in /includes/)
    $mh_icons_path = plugin_dir_path( __FILE__ ) . 'mh-icon-list.php';
    $fa_icons_path = plugin_dir_path( __FILE__ ) . 'fa-7-icon-list.php';

    $mh_icons = file_exists($mh_icons_path) ? include($mh_icons_path) : [];
    $fa_icons = file_exists($fa_icons_path) ? include($fa_icons_path) : [];

    // Sort Font Awesome icons alphabetically
    if (is_array($fa_icons)) {
        sort($fa_icons);
    }

    ?>
    <div id="mh-menu-icon-modal" class="mh-menu-icon-modal">
        <div class="mh-menu-icon-modal-content">

            <div class="mh-menu-icon-modal-header">
                <h2><?php _e('Select an Icon', 'mh-plug'); ?></h2>
                <span class="mh-menu-icon-modal-close">&times;</span>
            </div>

            <div class="mh-menu-icon-modal-tabs">
                <button class="active" data-tab="mh-tab-mh-icons"><?php _e('MH Icons', 'mh-plug'); ?></button>
                <button data-tab="mh-tab-fa-icons"><?php _e('Font Awesome 7', 'mh-plug'); ?></button>
            </div>

            <div class="mh-menu-icon-modal-body">

                <div id="mh-tab-mh-icons" class="mh-menu-icon-tab-content active">
                    <div class="mh-menu-icon-grid">
                        <?php if ( ! empty( $mh_icons ) && is_array($mh_icons) ) : ?>
                            <?php foreach ( $mh_icons as $icon ) : ?>
                                <span class="mh-icon-item" title="<?php echo esc_attr( $icon ); ?>">
                                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                </span>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p><?php _e('No custom MH icons found in includes/mh-icon-list.php', 'mh-plug'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="mh-tab-fa-icons" class="mh-menu-icon-tab-content">
                    <?php // Basic Search Input for Font Awesome ?>
                    <input type="text" id="mh-fa-icon-search" placeholder="<?php _e('Search Font Awesome icons...', 'mh-plug'); ?>" style="width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">

                    <div class="mh-menu-icon-grid" id="mh-fa-icon-grid">
                        <?php if ( ! empty( $fa_icons ) && is_array($fa_icons) ) : ?>
                            <?php foreach ( $fa_icons as $icon ) : ?>
                                <?php // Add data-filter attribute for search ?>
                                <span class="mh-icon-item" title="<?php echo esc_attr( $icon ); ?>" data-filter="<?php echo esc_attr( str_replace(['fas ', 'far ', 'fab '], '', $icon) ); // Remove prefix for search ?>">
                                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                </span>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p><?php _e('No Font Awesome icons found. Please check includes/fa-7-icon-list.php', 'mh-plug'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="mh-menu-icon-modal-footer">
                <p><?php _e('Click an icon to select it.', 'mh-plug'); ?></p>
            </div>

        </div>
    </div>
    <?php
}

?>