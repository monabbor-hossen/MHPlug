<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$is_wc_active = class_exists( 'WooCommerce' );
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
        <li data-tab="product_archive" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e('Product Archive', 'mh-plug'); ?>
            <?php if (!$is_wc_active) echo '<span class="mh-tb-wc-req">(' . esc_html__('Requires WooCommerce', 'mh-plug') . ')</span>'; ?>
        </li>
        <li data-tab="product_single" class="<?php echo $is_wc_active ? '' : 'mh-disabled-tab'; ?>">
            <?php esc_html_e('Product Single', 'mh-plug'); ?>
            <?php if (!$is_wc_active) echo '<span class="mh-tb-wc-req">(' . esc_html__('Requires WooCommerce', 'mh-plug') . ')</span>'; ?>
        </li>
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

        <?php
        // Dynamically Output Templates via CPT Loop
        $templates = new WP_Query([
            'post_type'      => 'mh_templates',
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'draft'],
        ]);

        if ( $templates->have_posts() ) :
            while ( $templates->have_posts() ) : $templates->the_post();
                $template_id = get_the_ID();
                $template_type = get_post_meta($template_id, 'mh_template_type', true) ?: 'single';
                
                // Elementor Edit Link
                $edit_url = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
                
                // Status Switch
                $is_active = get_post_meta($template_id, 'mh_template_active', true) === 'yes' ? 'checked' : '';
                
                // Define icons based on type
                $icon = 'dashicons-media-document';
                if ($template_type === 'header') $icon = 'dashicons-align-center';
                if ($template_type === 'footer') $icon = 'dashicons-arrow-down-alt2';
                if ($template_type === 'archive') $icon = 'dashicons-portfolio';
                if ($template_type === 'product_archive') $icon = 'dashicons-cart';
                if ($template_type === 'product_single') $icon = 'dashicons-products';
        ?>
        <div class="mh-tb-card mh-tb-template-item" data-type="<?php echo esc_attr($template_type); ?>">
            <div class="mh-tb-card-top">
                <div class="mh-tb-card-icon"><i class="dashicons <?php echo esc_attr($icon); ?>"></i></div>
                <span class="mh-tb-badge"><?php echo esc_html(ucwords(str_replace('_', ' ', $template_type))); ?></span>
            </div>
            
            <div class="mh-tb-card-content">
                <h3 class="mh-tb-card-title"><?php the_title(); ?></h3>
                
                <div class="mh-tb-status-toggle">
                    <span class="mh-tb-status-label"><?php esc_html_e('Active Status', 'mh-plug'); ?></span>
                    <label class="switch">
                        <input class="cb" type="checkbox" <?php echo $is_active; ?> />
                        <span class="toggle">
                            <span class="left">off</span>
                            <span class="right">on</span>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="mh-tb-card-actions">
                <a href="<?php echo esc_url($edit_url); ?>" class="mh-tb-edit-link"><i class="dashicons dashicons-edit"></i> <?php esc_html_e('Edit with Elementor', 'mh-plug'); ?></a>
                <button class="mh-tb-delete-btn"><i class="dashicons dashicons-trash"></i></button>
            </div>
        </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>

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
                        <option value="product_archive" <?php disabled(!$is_wc_active); ?>><?php esc_html_e('Product Archive', 'mh-plug'); ?> <?php if(!$is_wc_active) echo '(' . esc_html__('Requires WooCommerce', 'mh-plug') . ')'; ?></option>
                        <option value="product_single" <?php disabled(!$is_wc_active); ?>><?php esc_html_e('Product Single', 'mh-plug'); ?> <?php if(!$is_wc_active) echo '(' . esc_html__('Requires WooCommerce', 'mh-plug') . ')'; ?></option>
                    </select>
                </div>
                
                <div class="mh-tb-form-actions">
                    <?php wp_nonce_field('mh_tb_create_template', 'mh_tb_nonce'); ?>
                    <button type="submit" class="mh-button mh-tb-submit-btn">
                        <span class="mh-button-content-wrapper">
                            <span class="mh-button-icon mh-button-icon-left"><i class="dashicons dashicons-edit"></i></span>
                            <span class="mh-button-text"><?php esc_html_e('Save & Edit', 'mh-plug'); ?></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div> <!-- .wrap -->

<script>
    var mhTbAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
