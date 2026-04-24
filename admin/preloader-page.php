<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Fetch existing settings
$settings     = get_option('mh_plug_preloader_settings', []);
$enable       = isset($settings['enable']) ? $settings['enable'] : 'no';
$type         = isset($settings['type']) ? $settings['type'] : 'css'; // 'image' or 'css'
$css_effect   = isset($settings['css_effect']) ? $settings['css_effect'] : '1';
$loader_color = isset($settings['loader_color']) ? $settings['loader_color'] : '#d63638';
$image        = isset($settings['image']) ? $settings['image'] : '';
$bg_color     = isset($settings['bg_color']) ? $settings['bg_color'] : '#ffffff';
$img_width    = isset($settings['img_width']) ? $settings['img_width'] : '150';
$delay        = isset($settings['delay']) ? $settings['delay'] : '500';
$transition   = isset($settings['transition']) ? $settings['transition'] : '500';
?>

<div class="wrap mh-plug-admin-wrap mh-main-settings-wrap">
    <h1><?php esc_html_e('Preloader Settings', 'mh-plug'); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('mh_plug_preloader_group'); ?>

        <div class="mh-accordion">
            <div class="mh-accordion-item">
                <div class="mh-accordion-header">
                    <span class="mh-accordion-title"><?php esc_html_e('Preloader Customization', 'mh-plug'); ?></span>
                </div>
                
                <div class="mh-accordion-content" style="display: block; padding: 30px;">
                    <table class="form-table">
                        
                        <tr>
                            <th scope="row"><?php esc_html_e('Enable Preloader', 'mh-plug'); ?></th>
                            <td>
                                <label class="switch">
                                    <input class="cb" type="checkbox" name="mh_plug_preloader_settings[enable]" value="yes" <?php checked($enable, 'yes'); ?> />
                                    <span class="toggle"><span class="left">off</span><span class="right">on</span></span>
                                </label>
                                <p class="description">Turn the global loading screen on or off.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Preloader Type', 'mh-plug'); ?></th>
                            <td>
                                <label style="margin-right: 15px;">
                                    <input type="radio" name="mh_plug_preloader_settings[type]" value="css" class="mh-loader-type" <?php checked($type, 'css'); ?>> 
                                    <?php esc_html_e('Default Animations', 'mh-plug'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="mh_plug_preloader_settings[type]" value="image" class="mh-loader-type" <?php checked($type, 'image'); ?>> 
                                    <?php esc_html_e('Custom Image (GIF/PNG)', 'mh-plug'); ?>
                                </label>
                            </td>
                        </tr>

                        <tr class="mh-css-settings" style="display: <?php echo ($type === 'css') ? 'table-row' : 'none'; ?>;">
                            <th scope="row"><?php esc_html_e('Select Animation', 'mh-plug'); ?></th>
                            <td>
                                <select name="mh_plug_preloader_settings[css_effect]">
                                    <option value="1" <?php selected($css_effect, '1'); ?>><?php esc_html_e('1. Classic Spinner', 'mh-plug'); ?></option>
                                    <option value="2" <?php selected($css_effect, '2'); ?>><?php esc_html_e('2. Bouncing Dots', 'mh-plug'); ?></option>
                                    <option value="3" <?php selected($css_effect, '3'); ?>><?php esc_html_e('3. Pulse Circle', 'mh-plug'); ?></option>
                                    <option value="4" <?php selected($css_effect, '4'); ?>><?php esc_html_e('4. Flipping Square', 'mh-plug'); ?></option>
                                    <option value="5" <?php selected($css_effect, '5'); ?>><?php esc_html_e('5. Double Bounce', 'mh-plug'); ?></option>
                                    <option value="6" <?php selected($css_effect, '6'); ?>><?php esc_html_e('6. Bar Wave', 'mh-plug'); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr class="mh-css-settings" style="display: <?php echo ($type === 'css') ? 'table-row' : 'none'; ?>;">
                            <th scope="row"><?php esc_html_e('Animation Color', 'mh-plug'); ?></th>
                            <td>
                                <input type="color" name="mh_plug_preloader_settings[loader_color]" value="<?php echo esc_attr($loader_color); ?>" style="height: 40px; width: 80px; padding: 0; cursor: pointer; border-radius: 4px;" />
                            </td>
                        </tr>

                        <tr class="mh-image-settings" style="display: <?php echo ($type === 'image') ? 'table-row' : 'none'; ?>;">
                            <th scope="row"><?php esc_html_e('Loading Image (GIF/SVG/PNG)', 'mh-plug'); ?></th>
                            <td>
                                <div class="mh-image-preview-wrapper" style="margin-bottom: 15px;">
                                    <img id="mh-preloader-preview" src="<?php echo esc_url($image); ?>" style="max-width: 150px; display: <?php echo empty($image) ? 'none' : 'block'; ?>; border: 2px dashed #ccc; padding: 10px; background: #f9f9f9; border-radius: 8px;" />
                                </div>
                                <input type="hidden" id="mh-preloader-image-url" name="mh_plug_preloader_settings[image]" value="<?php echo esc_attr($image); ?>" />
                                <button type="button" class="button button-primary" id="mh-upload-preloader-btn">
                                    <i class="dashicons dashicons-format-image" style="vertical-align: text-top;"></i> <?php esc_html_e('Select Image', 'mh-plug'); ?>
                                </button>
                                <button type="button" class="button button-link-delete" id="mh-remove-preloader-btn" style="color: #d63638; display: <?php echo empty($image) ? 'none' : 'inline-block'; ?>;">
                                    <?php esc_html_e('Remove Image', 'mh-plug'); ?>
                                </button>
                            </td>
                        </tr>

                        <tr class="mh-image-settings" style="display: <?php echo ($type === 'image') ? 'table-row' : 'none'; ?>;">
                            <th scope="row"><?php esc_html_e('Image Width (px)', 'mh-plug'); ?></th>
                            <td>
                                <input type="number" name="mh_plug_preloader_settings[img_width]" value="<?php echo esc_attr($img_width); ?>" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Background Color', 'mh-plug'); ?></th>
                            <td>
                                <input type="color" name="mh_plug_preloader_settings[bg_color]" value="<?php echo esc_attr($bg_color); ?>" style="height: 40px; width: 80px; padding: 0; cursor: pointer; border-radius: 4px;" />
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Display Time (ms)', 'mh-plug'); ?></th>
                            <td>
                                <input type="number" name="mh_plug_preloader_settings[delay]" value="<?php echo esc_attr($delay); ?>" step="100" />
                                <p class="description">How long the loader displays after page loads (e.g., 500 = 0.5 seconds).</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Fade Speed (ms)', 'mh-plug'); ?></th>
                            <td>
                                <input type="number" name="mh_plug_preloader_settings[transition]" value="<?php echo esc_attr($transition); ?>" step="100" />
                                <p class="description">How smooth the fade-out effect is.</p>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        <?php submit_button('Save Preloader Settings'); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($){
    // Toggle Image vs CSS fields
    $('.mh-loader-type').change(function() {
        if ($(this).val() === 'css') {
            $('.mh-image-settings').hide();
            $('.mh-css-settings').fadeIn();
        } else {
            $('.mh-css-settings').hide();
            $('.mh-image-settings').fadeIn();
        }
    });

    // Media Uploader
    var mediaUploader;
    $('#mh-upload-preloader-btn').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Preloader Image',
            button: { text: 'Choose Image' },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mh-preloader-image-url').val(attachment.url);
            $('#mh-preloader-preview').attr('src', attachment.url).show();
            $('#mh-remove-preloader-btn').show();
        });
        mediaUploader.open();
    });

    $('#mh-remove-preloader-btn').click(function(e){
        e.preventDefault();
        $('#mh-preloader-image-url').val('');
        $('#mh-preloader-preview').hide().attr('src', '');
        $(this).hide();
    });
});
</script>