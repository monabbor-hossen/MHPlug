<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Fetch existing settings
$settings     = get_option('mh_plug_preloader_settings', []);
$enable       = isset($settings['enable']) ? $settings['enable'] : 'no';
$type         = isset($settings['type']) ? $settings['type'] : 'css';
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
                    
                    <div class="mh-settings-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">

                        <div class="mh-widget-card">
                            <div class="mh-widget-card-header">
                                <div class="mh-widget-title"><?php esc_html_e('Enable Preloader', 'mh-plug'); ?></div>
                                <label class="switch">
                                    <input class="cb" type="checkbox" name="mh_plug_preloader_settings[enable]" value="yes" <?php checked($enable, 'yes'); ?> />
                                    <span class="toggle"><span class="left">off</span><span class="right">on</span></span>
                                </label>
                            </div>
                        </div>

                        <div class="mh-widget-card">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Preloader Type', 'mh-plug'); ?></div>
                                <select name="mh_plug_preloader_settings[type]" class="mh-loader-type" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #c3c4c7;">
                                    <option value="css" <?php selected($type, 'css'); ?>><?php esc_html_e('Default Animations', 'mh-plug'); ?></option>
                                    <option value="image" <?php selected($type, 'image'); ?>><?php esc_html_e('Custom Image (GIF/PNG)', 'mh-plug'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mh-widget-card mh-css-settings" style="grid-column: 1 / -1; display: <?php echo ($type === 'css') ? 'block' : 'none'; ?>;">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: center; gap: 15px; padding: 30px;">
                                <div class="mh-widget-title" style="margin-bottom: 10px;"><?php esc_html_e('Live Animation Preview', 'mh-plug'); ?></div>
                                <div id="mh-css-preloader-preview-box" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: <?php echo esc_attr($bg_color); ?>; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e4e7;">
                                    </div>
                            </div>
                        </div>

                        <div class="mh-widget-card mh-image-settings" style="grid-column: 1 / -1; display: <?php echo ($type === 'image') ? 'block' : 'none'; ?>;">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: center; gap: 15px; padding: 30px;">
                                <div class="mh-widget-title" style="margin-bottom: 10px;"><?php esc_html_e('Live Image Preview', 'mh-plug'); ?></div>
                                <div class="mh-image-preview-wrapper" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: <?php echo esc_attr($bg_color); ?>; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e4e7;">
                                    <img id="mh-preloader-preview" src="<?php echo esc_url($image); ?>" style="max-width: 100px; max-height: 100px; display: <?php echo empty($image) ? 'none' : 'block'; ?>;" />
                                </div>
                                <input type="hidden" id="mh-preloader-image-url" name="mh_plug_preloader_settings[image]" value="<?php echo esc_attr($image); ?>" />
                                <div style="display:flex; gap: 10px; margin-top: 10px;">
                                    <button type="button" class="button button-primary" id="mh-upload-preloader-btn"><i class="dashicons dashicons-format-image" style="vertical-align: middle;"></i> <?php esc_html_e('Choose Image', 'mh-plug'); ?></button>
                                    <button type="button" class="button" id="mh-remove-preloader-btn" style="color: #d63638; border-color: #d63638; display: <?php echo empty($image) ? 'none' : 'inline-block'; ?>;"><?php esc_html_e('Remove', 'mh-plug'); ?></button>
                                </div>
                            </div>
                        </div>

                        <div class="mh-widget-card mh-css-settings" style="display: <?php echo ($type === 'css') ? 'block' : 'none'; ?>;">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Select Animation', 'mh-plug'); ?></div>
                                <select name="mh_plug_preloader_settings[css_effect]" id="mh_css_effect_select" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #c3c4c7;">
                                    <option value="1" <?php selected($css_effect, '1'); ?>><?php esc_html_e('1. Classic Spinner', 'mh-plug'); ?></option>
                                    <option value="2" <?php selected($css_effect, '2'); ?>><?php esc_html_e('2. Bouncing Dots', 'mh-plug'); ?></option>
                                    <option value="3" <?php selected($css_effect, '3'); ?>><?php esc_html_e('3. Pulse Circle', 'mh-plug'); ?></option>
                                    <option value="4" <?php selected($css_effect, '4'); ?>><?php esc_html_e('4. Flipping Square', 'mh-plug'); ?></option>
                                    <option value="5" <?php selected($css_effect, '5'); ?>><?php esc_html_e('5. Double Bounce', 'mh-plug'); ?></option>
                                    <option value="6" <?php selected($css_effect, '6'); ?>><?php esc_html_e('6. Bar Wave', 'mh-plug'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mh-widget-card mh-css-settings" style="display: <?php echo ($type === 'css') ? 'block' : 'none'; ?>;">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Animation Color', 'mh-plug'); ?></div>
                                <input type="color" id="mh_loader_color_picker" name="mh_plug_preloader_settings[loader_color]" value="<?php echo esc_attr($loader_color); ?>" style="width: 100%; height: 40px; border-radius: 6px; border: 1px solid #c3c4c7; cursor: pointer; padding: 2px;" />
                            </div>
                        </div>

                        <div class="mh-widget-card mh-image-settings" style="display: <?php echo ($type === 'image') ? 'block' : 'none'; ?>;">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Image Width (px)', 'mh-plug'); ?></div>
                                <input type="number" name="mh_plug_preloader_settings[img_width]" value="<?php echo esc_attr($img_width); ?>" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #c3c4c7;" />
                            </div>
                        </div>

                        <div class="mh-widget-card">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Background Color', 'mh-plug'); ?></div>
                                <input type="color" id="mh_bg_color_picker" name="mh_plug_preloader_settings[bg_color]" value="<?php echo esc_attr($bg_color); ?>" style="width: 100%; height: 40px; border-radius: 6px; border: 1px solid #c3c4c7; cursor: pointer; padding: 2px;" />
                            </div>
                        </div>

                        <div class="mh-widget-card">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Display Time (ms)', 'mh-plug'); ?> <span style="font-weight:normal; color:#888; font-size:12px;">(e.g., 500 = 0.5s)</span></div>
                                <input type="number" name="mh_plug_preloader_settings[delay]" value="<?php echo esc_attr($delay); ?>" step="100" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #c3c4c7;" />
                            </div>
                        </div>

                        <div class="mh-widget-card">
                            <div class="mh-widget-card-header" style="flex-direction: column; align-items: stretch; gap: 10px;">
                                <div class="mh-widget-title"><?php esc_html_e('Fade Speed (ms)', 'mh-plug'); ?> <span style="font-weight:normal; color:#888; font-size:12px;">(Smoothness)</span></div>
                                <input type="number" name="mh_plug_preloader_settings[transition]" value="<?php echo esc_attr($transition); ?>" step="100" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #c3c4c7;" />
                            </div>
                        </div>

                    </div> </div>
            </div>
        </div>

        <div style="padding: 20px 0;">
            <?php submit_button('Save Preloader Settings', 'primary', 'submit', false); ?>
        </div>
    </form>
</div>

<style>
    #mh-css-preloader-preview-box { --mh-loader-color: <?php echo esc_attr($loader_color); ?>; }
    .mh-loader-1 { width: 50px; height: 50px; border: 5px solid rgba(0,0,0,0.1); border-top: 5px solid var(--mh-loader-color); border-radius: 50%; animation: mh-spin 1s linear infinite; }
    @keyframes mh-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .mh-loader-2 { display: flex; gap: 8px; }
    .mh-loader-2 div { width: 16px; height: 16px; background-color: var(--mh-loader-color); border-radius: 50%; animation: mh-bounce 1.4s infinite ease-in-out both; }
    .mh-loader-2 div:nth-child(1) { animation-delay: -0.32s; }
    .mh-loader-2 div:nth-child(2) { animation-delay: -0.16s; }
    @keyframes mh-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
    .mh-loader-3 { width: 50px; height: 50px; background-color: var(--mh-loader-color); border-radius: 50%; animation: mh-pulse 1.2s infinite cubic-bezier(0.2, 0.6, 0.2, 1); }
    @keyframes mh-pulse { 0% { transform: scale(0); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
    .mh-loader-4 { width: 40px; height: 40px; background-color: var(--mh-loader-color); animation: mh-flip 1.2s infinite ease-in-out; }
    @keyframes mh-flip { 0% { transform: perspective(120px) rotateX(0deg) rotateY(0deg); } 50% { transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg); } 100% { transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg); } }
    .mh-loader-5 { width: 50px; height: 50px; position: relative; }
    .mh-loader-5 div { width: 100%; height: 100%; border-radius: 50%; background-color: var(--mh-loader-color); opacity: 0.6; position: absolute; top: 0; left: 0; animation: mh-bounce2 2s infinite ease-in-out; }
    .mh-loader-5 div:nth-child(2) { animation-delay: -1.0s; }
    @keyframes mh-bounce2 { 0%, 100% { transform: scale(0); } 50% { transform: scale(1); } }
    .mh-loader-6 { display: flex; gap: 5px; height: 40px; align-items: center; }
    .mh-loader-6 div { width: 6px; height: 100%; background-color: var(--mh-loader-color); animation: mh-wave 1.2s infinite ease-in-out; }
    .mh-loader-6 div:nth-child(2) { animation-delay: -1.1s; }
    .mh-loader-6 div:nth-child(3) { animation-delay: -1.0s; }
    .mh-loader-6 div:nth-child(4) { animation-delay: -0.9s; }
    .mh-loader-6 div:nth-child(5) { animation-delay: -0.8s; }
    @keyframes mh-wave { 0%, 40%, 100% { transform: scaleY(0.4); } 20% { transform: scaleY(1); } }
</style>

<script>
jQuery(document).ready(function($){
    $('.mh-loader-type').change(function() {
        if ($(this).val() === 'css') {
            $('.mh-image-settings').hide();
            $('.mh-css-settings').fadeIn();
        } else {
            $('.mh-css-settings').hide();
            $('.mh-image-settings').fadeIn();
        }
    });

    function updateCssPreview() {
        var effect = $('#mh_css_effect_select').val();
        var loaderColor = $('#mh_loader_color_picker').val();
        var bgColor = $('#mh_bg_color_picker').val();
        
        var html = '';
        if (effect === '1') html = '<div class="mh-loader-1"></div>';
        if (effect === '2') html = '<div class="mh-loader-2"><div></div><div></div><div></div></div>';
        if (effect === '3') html = '<div class="mh-loader-3"></div>';
        if (effect === '4') html = '<div class="mh-loader-4"></div>';
        if (effect === '5') html = '<div class="mh-loader-5"><div></div><div></div></div>';
        if (effect === '6') html = '<div class="mh-loader-6"><div></div><div></div><div></div><div></div><div></div></div>';

        $('#mh-css-preloader-preview-box').html(html).css({ '--mh-loader-color': loaderColor, 'background-color': bgColor });
        $('.mh-image-preview-wrapper').css('background-color', bgColor);
    }

    $('#mh_css_effect_select, #mh_loader_color_picker, #mh_bg_color_picker').on('change input', updateCssPreview);
    updateCssPreview();

    var mediaUploader;
    $('#mh-upload-preloader-btn').click(function(e) {
        e.preventDefault();
        if (mediaUploader) { mediaUploader.open(); return; }
        mediaUploader = wp.media.frames.file_frame = wp.media({ title: 'Choose Preloader Image', button: { text: 'Choose Image' }, multiple: false });
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