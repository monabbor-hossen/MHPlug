<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap mh-plug-wrap">
    
    <!-- Header Area -->
    <div class="mh-tb-header">
        <div class="mh-tb-header-text">
            <h1 class="mh-plug-title"><?php esc_html_e('Theme Builder', 'mh-plug'); ?></h1>
            <p class="mh-tb-description"><?php esc_html_e('Create and manage custom Elementor templates for headers, footers, single posts, and archives.', 'mh-plug'); ?></p>
        </div>
        <div class="mh-tb-header-actions">
            <!-- MH-Plug button style -->
            <button class="mh-button mh-tb-action-create" id="mh-tb-create-btn">
                <span class="mh-button-content-wrapper">
                    <span class="mh-button-icon mh-button-icon-left"><i class="dashicons dashicons-plus-alt2"></i></span>
                    <span class="mh-button-text"><?php esc_html_e('Create Template', 'mh-plug'); ?></span>
                </span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="mh-tb-tabs">
        <li data-tab="all" class="active"><?php esc_html_e('All', 'mh-plug'); ?></li>
        <li data-tab="header"><?php esc_html_e('Header', 'mh-plug'); ?></li>
        <li data-tab="footer"><?php esc_html_e('Footer', 'mh-plug'); ?></li>
        <li data-tab="single"><?php esc_html_e('Single', 'mh-plug'); ?></li>
        <li data-tab="archive"><?php esc_html_e('Archive', 'mh-plug'); ?></li>
    </ul>

    <!-- Template Grid -->
    <div class="mh-tb-grid">
        
        <!-- "Add New" Card Placeholder -->
        <div class="mh-tb-card mh-tb-card-add-new" id="mh-tb-card-add-new" data-type="all">
            <div class="mh-tb-add-icon">
                <i class="dashicons dashicons-plus"></i>
            </div>
            <h3><?php esc_html_e('Add New', 'mh-plug'); ?></h3>
        </div>

        <!-- Example Grid Item 1: Main Header -->
        <div class="mh-tb-card mh-tb-template-item" data-type="header">
            <div class="mh-tb-card-top">
                <div class="mh-tb-card-icon"><i class="dashicons dashicons-align-center"></i></div>
                <span class="mh-tb-badge">Header</span>
            </div>
            
            <div class="mh-tb-card-content">
                <h3 class="mh-tb-card-title">Main Header</h3>
                
                <!-- Use MH 3D Toggle UI for status -->
                <div class="mh-tb-status-toggle">
                    <span class="mh-tb-status-label">Active Status</span>
                    <label class="switch">
                        <input class="cb" type="checkbox" checked />
                        <span class="toggle">
                            <span class="left">off</span>
                            <span class="right">on</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="mh-tb-card-actions">
                <a href="#" class="mh-tb-edit-link"><i class="dashicons dashicons-edit"></i> Edit with Elementor</a>
                <button class="mh-tb-delete-btn"><i class="dashicons dashicons-trash"></i></button>
            </div>
        </div>
        
        <!-- Example Grid Item 2: Footer -->
        <div class="mh-tb-card mh-tb-template-item" data-type="footer">
            <div class="mh-tb-card-top">
                <div class="mh-tb-card-icon"><i class="dashicons dashicons-arrow-down-alt2"></i></div>
                <span class="mh-tb-badge">Footer</span>
            </div>
            
            <div class="mh-tb-card-content">
                <h3 class="mh-tb-card-title">Global Footer</h3>
                
                <div class="mh-tb-status-toggle">
                    <span class="mh-tb-status-label">Active Status</span>
                    <label class="switch">
                        <input class="cb" type="checkbox" />
                        <span class="toggle">
                            <span class="left">off</span>
                            <span class="right">on</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="mh-tb-card-actions">
                <a href="#" class="mh-tb-edit-link"><i class="dashicons dashicons-edit"></i> Edit with Elementor</a>
                <button class="mh-tb-delete-btn"><i class="dashicons dashicons-trash"></i></button>
            </div>
        </div>

    </div><!-- .mh-tb-grid -->

    <!-- Theme Builder Modal -->
    <div class="mh-tb-modal" id="mh-tb-modal">
        <div class="mh-tb-modal-content">
            <div class="mh-tb-modal-header">
                <h2><?php esc_html_e('Create New Template', 'mh-plug'); ?></h2>
                <span class="mh-tb-modal-close" id="mh-tb-modal-close"><i class="dashicons dashicons-no-alt"></i></span>
            </div>
            
            <form id="mh-tb-create-form" class="mh-tb-form">
                <div class="mh-tb-form-group">
                    <label for="mh_tb_template_name"><?php esc_html_e('Template Name', 'mh-plug'); ?></label>
                    <input type="text" id="mh_tb_template_name" name="mh_tb_template_name" required placeholder="<?php esc_attr_e('e.g., Main Header', 'mh-plug'); ?>" />
                </div>
                
                <div class="mh-tb-form-group">
                    <label for="mh_tb_template_type"><?php esc_html_e('Template Type', 'mh-plug'); ?></label>
                    <select id="mh_tb_template_type" name="mh_tb_template_type" required>
                        <option value="header"><?php esc_html_e('Header', 'mh-plug'); ?></option>
                        <option value="footer"><?php esc_html_e('Footer', 'mh-plug'); ?></option>
                        <option value="single"><?php esc_html_e('Single', 'mh-plug'); ?></option>
                        <option value="archive"><?php esc_html_e('Archive', 'mh-plug'); ?></option>
                    </select>
                </div>
                
                <div class="mh-tb-form-actions">
                    <button type="submit" class="mh-button mh-tb-submit-btn"><?php esc_html_e('Save & Edit', 'mh-plug'); ?></button>
                </div>
            </form>
        </div>
    </div>

</div> <!-- .wrap -->
