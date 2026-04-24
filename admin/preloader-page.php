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
$loader_color = isset($settings['loader_color']) ? $settings['loader_color'] : '#2293e9';
$image        = isset($settings['image']) ? $settings['image'] : '';
$bg_color     = isset($settings['bg_color']) ? $settings['bg_color'] : '#0f172a';
$img_width    = isset($settings['img_width']) ? $settings['img_width'] : '100';
$delay        = isset($settings['delay']) ? $settings['delay'] : '500';
$transition   = isset($settings['transition']) ? $settings['transition'] : '500';
?>

<div class="wrap mh-plug-admin-wrap mh-futuristic-dashboard">
    <div class="mh-dashboard-header">
        <div class="mh-header-glow"></div>
        <h1><i class="fas fa-space-shuttle"></i> <?php esc_html_e('Preloader Engine', 'mh-plug'); ?></h1>
        <p><?php esc_html_e('Configure your global loading screen with futuristic precision.', 'mh-plug'); ?></p>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields('mh_plug_preloader_group'); ?>

        <div class="mh-settings-grid">

            <div class="mh-futuristic-card mh-card-wide">
                <div class="mh-card-inner">
                    <div class="mh-setting-row">
                        <div class="mh-setting-info">
                            <h3><?php esc_html_e('System Power', 'mh-plug'); ?></h3>
                            <p><?php esc_html_e('Activate the global preloader on your website.', 'mh-plug'); ?></p>
                        </div>
                        <label class="mh-cyber-switch">
                            <input type="checkbox" name="mh_plug_preloader_settings[enable]" value="yes" <?php checked($enable, 'yes'); ?> />
                            <span class="mh-slider-track"></span>
                        </label>
                    </div>
                    
                    <div class="mh-divider"></div>

                    <div class="mh-setting-row">
                        <div class="mh-setting-info">
                            <h3><?php esc_html_e('Render Engine Type', 'mh-plug'); ?></h3>
                            <p><?php esc_html_e('Choose between CSS animations or custom media.', 'mh-plug'); ?></p>
                        </div>
                        <div class="mh-cyber-radio-group">
                            <label class="mh-cyber-radio">
                                <input type="radio" name="mh_plug_preloader_settings[type]" value="css" class="mh-loader-type" <?php checked($type, 'css'); ?>> 
                                <span><i class="fas fa-code"></i> CSS Animations</span>
                            </label>
                            <label class="mh-cyber-radio">
                                <input type="radio" name="mh_plug_preloader_settings[type]" value="image" class="mh-loader-type" <?php checked($type, 'image'); ?>> 
                                <span><i class="fas fa-image"></i> Custom Media</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card mh-preview-card">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-eye"></i> <?php esc_html_e('Live Hologram Preview', 'mh-plug'); ?></h3>
                    <div class="mh-hud-container">
                        <div class="mh-hud-overlay"></div>
                        <div id="mh-css-preloader-preview-box" class="mh-preview-box mh-css-settings" style="display: <?php echo ($type === 'css') ? 'flex' : 'none'; ?>; background: <?php echo esc_attr($bg_color); ?>;"></div>
                        <div class="mh-preview-box mh-image-settings mh-image-preview-wrapper" style="display: <?php echo ($type === 'image') ? 'flex' : 'none'; ?>; background: <?php echo esc_attr($bg_color); ?>;">
                            <img id="mh-preloader-preview" src="<?php echo esc_url($image); ?>" style="width: <?php echo esc_attr($img_width); ?>px; display: <?php echo empty($image) ? 'none' : 'block'; ?>;" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card mh-css-settings" style="display: <?php echo ($type === 'css') ? 'block' : 'none'; ?>;">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-magic"></i> <?php esc_html_e('Animation Protocol', 'mh-plug'); ?></h3>
                    
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Select Sequence', 'mh-plug'); ?></label>
                        <select name="mh_plug_preloader_settings[css_effect]" id="mh_css_effect_select" class="mh-cyber-select">
                            <option value="1" <?php selected($css_effect, '1'); ?>>1. Classic Spinner</option>
                            <option value="2" <?php selected($css_effect, '2'); ?>>2. Bouncing Dots</option>
                            <option value="3" <?php selected($css_effect, '3'); ?>>3. Pulse Circle</option>
                            <option value="4" <?php selected($css_effect, '4'); ?>>4. Flipping Square</option>
                            <option value="5" <?php selected($css_effect, '5'); ?>>5. Double Bounce</option>
                            <option value="6" <?php selected($css_effect, '6'); ?>>6. Bar Wave</option>
                            <option value="7" <?php selected($css_effect, '7'); ?>>7. Radar Scan</option>
                            <option value="8" <?php selected($css_effect, '8'); ?>>8. Morphing Hex</option>
                            <option value="9" <?php selected($css_effect, '9'); ?>>9. Orbiting Spheres</option>
                            <option value="10" <?php selected($css_effect, '10'); ?>>10. Ripple Rings</option>
                            <option value="11" <?php selected($css_effect, '11'); ?>>11. Quantum Triangle</option>
                            <option value="12" <?php selected($css_effect, '12'); ?>>12. Grid Matrix</option>
                            <option value="13" <?php selected($css_effect, '13'); ?>>13. Infinity Loop</option>
                            <option value="14" <?php selected($css_effect, '14'); ?>>14. Arc Reactor</option>
                            <option value="15" <?php selected($css_effect, '15'); ?>>15. Cube Split</option>
                            <option value="16" <?php selected($css_effect, '16'); ?>>16. Glow Pulse</option>
                            <option value="17" <?php selected($css_effect, '17'); ?>>17. Rotating Portal</option>
                            <option value="18" <?php selected($css_effect, '18'); ?>>18. Stairway</option>
                            <option value="19" <?php selected($css_effect, '19'); ?>>19. Shooting Star</option>
                            <option value="20" <?php selected($css_effect, '20'); ?>>20. Hyper Ring</option>
                        </select>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Energy Color', 'mh-plug'); ?></label>
                        <div class="mh-color-picker-wrap">
                            <input type="color" id="mh_loader_color_picker" name="mh_plug_preloader_settings[loader_color]" value="<?php echo esc_attr($loader_color); ?>" />
                            <div class="mh-color-overlay" style="background-color: <?php echo esc_attr($loader_color); ?>;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card mh-image-settings" style="display: <?php echo ($type === 'image') ? 'block' : 'none'; ?>;">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-file-upload"></i> <?php esc_html_e('Media Protocol', 'mh-plug'); ?></h3>
                    
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Upload Asset (GIF/PNG)', 'mh-plug'); ?></label>
                        <input type="hidden" id="mh-preloader-image-url" name="mh_plug_preloader_settings[image]" value="<?php echo esc_attr($image); ?>" />
                        <div class="mh-action-buttons">
                            <button type="button" class="mh-cyber-btn mh-btn-blue" id="mh-upload-preloader-btn">
                                <i class="fas fa-cloud-upload-alt"></i> <?php esc_html_e('Inject Media', 'mh-plug'); ?>
                            </button>
                            <button type="button" class="mh-cyber-btn mh-btn-red" id="mh-remove-preloader-btn" style="display: <?php echo empty($image) ? 'none' : 'inline-flex'; ?>;">
                                <i class="fas fa-trash"></i> <?php esc_html_e('Purge', 'mh-plug'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Asset Scale (Width px)', 'mh-plug'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" id="mh_img_width_range" name="mh_plug_preloader_settings[img_width]" min="20" max="300" value="<?php echo esc_attr($img_width); ?>" />
                            <span class="mh-range-badge"><span id="mh_img_width_val"><?php echo esc_attr($img_width); ?></span>px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-globe"></i> <?php esc_html_e('Environment Variables', 'mh-plug'); ?></h3>
                    
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Backdrop Atmosphere (Color)', 'mh-plug'); ?></label>
                        <div class="mh-color-picker-wrap">
                            <input type="color" id="mh_bg_color_picker" name="mh_plug_preloader_settings[bg_color]" value="<?php echo esc_attr($bg_color); ?>" />
                            <div class="mh-color-overlay" style="background-color: <?php echo esc_attr($bg_color); ?>;"></div>
                        </div>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Retention Time (Delay ms)', 'mh-plug'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" name="mh_plug_preloader_settings[delay]" min="0" max="3000" step="100" value="<?php echo esc_attr($delay); ?>" oninput="this.nextElementSibling.querySelector('span').innerText = this.value" />
                            <span class="mh-range-badge"><span><?php echo esc_attr($delay); ?></span>ms</span>
                        </div>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Dissolve Speed (Fade ms)', 'mh-plug'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" name="mh_plug_preloader_settings[transition]" min="100" max="2000" step="100" value="<?php echo esc_attr($transition); ?>" oninput="this.nextElementSibling.querySelector('span').innerText = this.value" />
                            <span class="mh-range-badge"><span><?php echo esc_attr($transition); ?></span>ms</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mh-save-dock">
            <button type="submit" class="mh-cyber-btn mh-btn-save">
                <i class="fas fa-save"></i> <?php esc_html_e('Initialize Settings', 'mh-plug'); ?>
            </button>
        </div>
    </form>
</div>

<style>
    /* Base Variables & Dashboard Layout */
    :root {
        --mh-dark: #004265;
        --mh-blue: #2293e9;
        --mh-blue-glow: rgba(34, 147, 233, 0.4);
        --mh-red: #d63638;
        --mh-bg: #f0f4f8;
        --mh-card: #ffffff;
        --mh-text: #334155;
        --mh-border: #e2e8f0;
    }
    .mh-futuristic-dashboard { max-width: 1200px; margin: 20px auto; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .mh-dashboard-header { position: relative; padding: 30px; background: var(--mh-dark); border-radius: 16px; color: white; margin-bottom: 30px; overflow: hidden; box-shadow: 0 15px 35px rgba(0, 66, 101, 0.2); }
    .mh-dashboard-header h1 { color: white; margin: 0 0 10px 0; font-size: 28px; font-weight: 800; position: relative; z-index: 2; }
    .mh-dashboard-header p { margin: 0; font-size: 16px; color: #94a3b8; position: relative; z-index: 2; }
    .mh-header-glow { position: absolute; right: -50px; top: -50px; width: 250px; height: 250px; background: radial-gradient(circle, var(--mh-blue-glow) 0%, transparent 70%); z-index: 1; }
    .mh-settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
    .mh-card-wide { grid-column: 1 / -1; }
    .mh-futuristic-card { background: var(--mh-card); border-radius: 16px; border: 1px solid var(--mh-border); box-shadow: 0 10px 25px rgba(0,0,0,0.03); transition: transform 0.3s, box-shadow 0.3s; position: relative; overflow: hidden; }
    .mh-futuristic-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(34, 147, 233, 0.12); border-color: var(--mh-blue); }
    .mh-futuristic-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--mh-dark), var(--mh-blue)); opacity: 0; transition: opacity 0.3s; }
    .mh-futuristic-card:hover::before { opacity: 1; }
    .mh-card-inner { padding: 25px; }
    .mh-futuristic-card h3 { margin: 0 0 20px 0; color: var(--mh-dark); font-size: 18px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--mh-border); padding-bottom: 15px; }
    .mh-futuristic-card h3 i { color: var(--mh-blue); }
    .mh-setting-row { display: flex; justify-content: space-between; align-items: center; gap: 20px; }
    .mh-setting-info h3 { border: none; padding: 0; margin: 0 0 5px 0; font-size: 16px; }
    .mh-setting-info p { margin: 0; font-size: 13px; color: #64748b; }
    .mh-divider { height: 1px; background: var(--mh-border); margin: 25px 0; }
    .mh-field-group { margin-bottom: 20px; }
    .mh-field-group:last-child { margin-bottom: 0; }
    .mh-field-group label { display: block; font-weight: 600; color: var(--mh-text); margin-bottom: 10px; }
    .mh-cyber-switch { position: relative; display: inline-block; width: 60px; height: 30px; }
    .mh-cyber-switch input { opacity: 0; width: 0; height: 0; }
    .mh-slider-track { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 30px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.1); }
    .mh-slider-track:before { position: absolute; content: ""; height: 22px; width: 22px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .mh-cyber-switch input:checked + .mh-slider-track { background-color: var(--mh-blue); box-shadow: 0 0 15px var(--mh-blue-glow); }
    .mh-cyber-switch input:checked + .mh-slider-track:before { transform: translateX(30px); }
    .mh-cyber-radio-group { display: flex; gap: 15px; }
    .mh-cyber-radio { cursor: pointer; flex: 1; }
    .mh-cyber-radio input { display: none; }
    .mh-cyber-radio span { display: block; text-align: center; padding: 12px; background: #f8fafc; border: 2px solid var(--mh-border); border-radius: 10px; font-weight: 600; color: #64748b; transition: all 0.3s; }
    .mh-cyber-radio input:checked + span { background: rgba(34, 147, 233, 0.05); border-color: var(--mh-blue); color: var(--mh-blue); box-shadow: 0 5px 15px var(--mh-blue-glow); transform: translateY(-2px); }
    .mh-cyber-select { width: 100%; padding: 12px 15px; border-radius: 10px; border: 2px solid var(--mh-border); background: #f8fafc; font-weight: 600; color: var(--mh-dark); outline: none; transition: 0.3s; }
    .mh-cyber-select:focus { border-color: var(--mh-blue); box-shadow: 0 0 0 4px var(--mh-blue-glow); }
    .mh-range-slider { display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 10px 15px; border-radius: 10px; border: 1px solid var(--mh-border); }
    .mh-range-slider input[type="range"] { flex: 1; -webkit-appearance: none; height: 6px; background: var(--mh-border); border-radius: 5px; outline: none; }
    .mh-range-slider input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--mh-blue); cursor: pointer; box-shadow: 0 0 10px var(--mh-blue-glow); transition: 0.2s; }
    .mh-range-slider input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.2); }
    .mh-range-badge { background: var(--mh-dark); color: white; padding: 5px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; min-width: 65px; text-align: center; }
    .mh-color-picker-wrap { position: relative; width: 100%; height: 50px; border-radius: 10px; overflow: hidden; border: 2px solid var(--mh-border); cursor: pointer; transition: 0.3s; }
    .mh-color-picker-wrap:hover { border-color: var(--mh-blue); box-shadow: 0 0 15px var(--mh-blue-glow); }
    .mh-color-picker-wrap input[type="color"] { position: absolute; width: 150%; height: 150%; top: -25%; left: -25%; cursor: pointer; opacity: 0; z-index: 2; }
    .mh-color-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
    .mh-action-buttons { display: flex; gap: 10px; }
    .mh-cyber-btn { border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; transition: 0.3s; }
    .mh-btn-blue { background: var(--mh-blue); color: white; box-shadow: 0 5px 15px var(--mh-blue-glow); }
    .mh-btn-blue:hover { background: #1b7ccc; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(34, 147, 233, 0.6); }
    .mh-btn-red { background: transparent; border: 2px solid var(--mh-red); color: var(--mh-red); }
    .mh-btn-red:hover { background: var(--mh-red); color: white; transform: translateY(-2px); }
    .mh-save-dock { margin-top: 30px; text-align: right; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: 1px solid var(--mh-border); }
    .mh-btn-save { background: var(--mh-dark); color: white; padding: 15px 40px; font-size: 16px; }
    .mh-btn-save:hover { background: var(--mh-blue); transform: translateY(-3px); box-shadow: 0 10px 25px var(--mh-blue-glow); }
    .mh-hud-container { position: relative; width: 100%; height: 250px; border-radius: 12px; overflow: hidden; border: 2px solid var(--mh-blue); box-shadow: inset 0 0 30px var(--mh-blue-glow); background: #000; display: flex; align-items: center; justify-content: center; }
    .mh-hud-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(34,147,233,0.05) 0%, rgba(34,147,233,0.1) 50%, rgba(34,147,233,0.05) 100%); pointer-events: none; z-index: 10; }
    .mh-hud-overlay::after { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.1) 2px, rgba(0,0,0,0.1) 4px); pointer-events: none; }
    .mh-preview-box { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 5; transition: background-color 0.3s; }

    /* ALL 20 PRELOADER CSS ANIMATIONS */
    #mh-css-preloader-preview-box { --mh-loader-color: <?php echo esc_attr($loader_color); ?>; }
    
    /* 1. Spinner */ .mh-loader-1 { width: 50px; height: 50px; border: 5px solid rgba(255,255,255,0.1); border-top: 5px solid var(--mh-loader-color); border-radius: 50%; animation: mh-spin 1s linear infinite; }
    @keyframes mh-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    /* 2. Dots */ .mh-loader-2 { display: flex; gap: 8px; } .mh-loader-2 div { width: 16px; height: 16px; background-color: var(--mh-loader-color); border-radius: 50%; animation: mh-bounce 1.4s infinite ease-in-out both; } .mh-loader-2 div:nth-child(1) { animation-delay: -0.32s; } .mh-loader-2 div:nth-child(2) { animation-delay: -0.16s; }
    @keyframes mh-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
    
    /* 3. Pulse */ .mh-loader-3 { width: 50px; height: 50px; background-color: var(--mh-loader-color); border-radius: 50%; animation: mh-pulse 1.2s infinite cubic-bezier(0.2, 0.6, 0.2, 1); }
    @keyframes mh-pulse { 0% { transform: scale(0); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
    
    /* 4. Flip */ .mh-loader-4 { width: 40px; height: 40px; background-color: var(--mh-loader-color); animation: mh-flip 1.2s infinite ease-in-out; }
    @keyframes mh-flip { 0% { transform: perspective(120px) rotateX(0deg) rotateY(0deg); } 50% { transform: perspective(120px) rotateX(-180deg) rotateY(0deg); } 100% { transform: perspective(120px) rotateX(-180deg) rotateY(-180deg); } }
    
    /* 5. Dbl Bounce */ .mh-loader-5 { width: 50px; height: 50px; position: relative; } .mh-loader-5 div { width: 100%; height: 100%; border-radius: 50%; background-color: var(--mh-loader-color); opacity: 0.6; position: absolute; top: 0; left: 0; animation: mh-bounce2 2s infinite ease-in-out; } .mh-loader-5 div:nth-child(2) { animation-delay: -1.0s; }
    @keyframes mh-bounce2 { 0%, 100% { transform: scale(0); } 50% { transform: scale(1); } }
    
    /* 6. Bar Wave */ .mh-loader-6 { display: flex; gap: 5px; height: 40px; align-items: center; } .mh-loader-6 div { width: 6px; height: 100%; background-color: var(--mh-loader-color); animation: mh-wave 1.2s infinite ease-in-out; } .mh-loader-6 div:nth-child(2) { animation-delay: -1.1s; } .mh-loader-6 div:nth-child(3) { animation-delay: -1.0s; } .mh-loader-6 div:nth-child(4) { animation-delay: -0.9s; } .mh-loader-6 div:nth-child(5) { animation-delay: -0.8s; }
    @keyframes mh-wave { 0%, 40%, 100% { transform: scaleY(0.4); } 20% { transform: scaleY(1); } }

    /* 7. Radar Scan */ .mh-loader-7 { width: 50px; height: 50px; border-radius: 50%; background: conic-gradient(transparent 60%, var(--mh-loader-color)); animation: mh-spin 1s linear infinite; }
    
    /* 8. Morph Hex */ .mh-loader-8 { width: 40px; height: 40px; background: var(--mh-loader-color); animation: mh-morph 2s infinite ease-in-out; }
    @keyframes mh-morph { 0% { border-radius: 0%; transform: rotate(0deg); } 50% { border-radius: 50%; transform: rotate(180deg); } 100% { border-radius: 0%; transform: rotate(360deg); } }
    
    /* 9. Orbiting Spheres */ .mh-loader-9 { width: 50px; height: 50px; position: relative; animation: mh-spin 2s linear infinite; } .mh-loader-9 div { position: absolute; width: 15px; height: 15px; background: var(--mh-loader-color); border-radius: 50%; top: 0; left: 50%; transform: translateX(-50%); } .mh-loader-9 div:nth-child(2) { top: auto; bottom: 0; }
    
    /* 10. Ripple Rings */ .mh-loader-10 { position: relative; width: 60px; height: 60px; } .mh-loader-10 div { position: absolute; border: 4px solid var(--mh-loader-color); opacity: 1; border-radius: 50%; animation: mh-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite; } .mh-loader-10 div:nth-child(2) { animation-delay: -0.5s; }
    @keyframes mh-ripple { 0% { top: 28px; left: 28px; width: 0; height: 0; opacity: 0; } 5% { opacity: 1; } 100% { top: -1px; left: -1px; width: 58px; height: 58px; opacity: 0; } }

    /* 11. Quantum Triangle */ .mh-loader-11 { width: 50px; height: 50px; border: 3px dashed var(--mh-loader-color); border-radius: 50%; animation: mh-spin 4s linear infinite; display: flex; align-items: center; justify-content: center; } .mh-loader-11 div { width: 20px; height: 20px; background: var(--mh-loader-color); clip-path: polygon(50% 0%, 0% 100%, 100% 100%); animation: mh-spin-reverse 2s linear infinite; }
    @keyframes mh-spin-reverse { 0% { transform: rotate(360deg); } 100% { transform: rotate(0deg); } }

    /* 12. Grid Matrix */ .mh-loader-12 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; width: 50px; height: 50px; } .mh-loader-12 div { background: var(--mh-loader-color); border-radius: 50%; animation: mh-grid-pulse 1.2s infinite ease-in-out; } .mh-loader-12 div:nth-child(1), .mh-loader-12 div:nth-child(5), .mh-loader-12 div:nth-child(9) { animation-delay: 0.4s; } .mh-loader-12 div:nth-child(2), .mh-loader-12 div:nth-child(6), .mh-loader-12 div:nth-child(7) { animation-delay: 0.8s; } .mh-loader-12 div:nth-child(3), .mh-loader-12 div:nth-child(4), .mh-loader-12 div:nth-child(8) { animation-delay: 1.2s; }
    @keyframes mh-grid-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.3; transform: scale(0.5); } }

    /* 13. Infinity Loop */ .mh-loader-13 { position: relative; width: 60px; height: 20px; } .mh-loader-13 div { position: absolute; width: 20px; height: 20px; background: var(--mh-loader-color); border-radius: 50%; animation: mh-infinity 1.5s infinite ease-in-out; } .mh-loader-13 div:nth-child(2) { animation-delay: -0.75s; }
    @keyframes mh-infinity { 0% { left: 0; transform: scale(1); z-index: 1; } 25% { transform: scale(1.5); z-index: 2; } 50% { left: 40px; transform: scale(1); z-index: 1; } 75% { transform: scale(0.5); z-index: 0; } 100% { left: 0; transform: scale(1); z-index: 1; } }

    /* 14. Arc Reactor */ .mh-loader-14 { position: relative; width: 60px; height: 60px; } .mh-loader-14 div { position: absolute; width: 100%; height: 100%; border: 3px solid transparent; border-top-color: var(--mh-loader-color); border-radius: 50%; animation: mh-spin 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite; } .mh-loader-14 div:nth-child(2) { width: 80%; height: 80%; top: 10%; left: 10%; border-top-color: transparent; border-right-color: var(--mh-loader-color); animation-duration: 1.5s; animation-direction: reverse; } .mh-loader-14 div:nth-child(3) { width: 60%; height: 60%; top: 20%; left: 20%; border-top-color: transparent; border-bottom-color: var(--mh-loader-color); animation-duration: 1s; }

    /* 15. Cube Split */ .mh-loader-15 { width: 40px; height: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 4px; } .mh-loader-15 div { background: var(--mh-loader-color); animation: mh-cube-split 1.5s infinite ease-in-out; } .mh-loader-15 div:nth-child(1) { transform-origin: bottom right; } .mh-loader-15 div:nth-child(2) { transform-origin: bottom left; } .mh-loader-15 div:nth-child(3) { transform-origin: top right; } .mh-loader-15 div:nth-child(4) { transform-origin: top left; }
    @keyframes mh-cube-split { 0%, 100% { transform: scale(1); } 50% { transform: scale(0.5) rotate(90deg); border-radius: 50%; } }

    /* 16. Glow Pulse */ .mh-loader-16 { width: 20px; height: 20px; background: var(--mh-loader-color); border-radius: 50%; box-shadow: 0 0 20px 5px var(--mh-loader-color); animation: mh-glow-pulse 1s infinite alternate; }
    @keyframes mh-glow-pulse { 0% { box-shadow: 0 0 10px 2px var(--mh-loader-color); transform: scale(0.8); } 100% { box-shadow: 0 0 30px 10px var(--mh-loader-color); transform: scale(1.2); } }

    /* 17. Rotating Portal */ .mh-loader-17 { width: 50px; height: 50px; border-radius: 50%; border: 4px solid var(--mh-loader-color); border-color: var(--mh-loader-color) transparent; animation: mh-spin 1.2s linear infinite; position: relative; } .mh-loader-17 div { position: absolute; top: 4px; left: 4px; right: 4px; bottom: 4px; border-radius: 50%; border: 4px solid var(--mh-loader-color); border-color: transparent var(--mh-loader-color); animation: mh-spin-reverse 0.6s linear infinite; }

    /* 18. Stairway */ .mh-loader-18 { display: flex; gap: 6px; height: 40px; align-items: flex-end; } .mh-loader-18 div { width: 8px; background: var(--mh-loader-color); animation: mh-stairway 1s infinite ease-in-out alternate; } .mh-loader-18 div:nth-child(1) { animation-delay: 0s; } .mh-loader-18 div:nth-child(2) { animation-delay: 0.2s; } .mh-loader-18 div:nth-child(3) { animation-delay: 0.4s; } .mh-loader-18 div:nth-child(4) { animation-delay: 0.6s; }
    @keyframes mh-stairway { 0% { height: 10px; } 100% { height: 40px; } }

    /* 19. Shooting Star */ .mh-loader-19 { width: 50px; height: 50px; border-radius: 50%; box-shadow: inset 0 0 0 4px rgba(255,255,255,0.1); position: relative; } .mh-loader-19::after { content: ''; position: absolute; top: -4px; left: -4px; right: -4px; bottom: -4px; border-radius: 50%; border: 4px solid transparent; border-top-color: var(--mh-loader-color); animation: mh-spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite; }

    /* 20. Hyper Ring */ .mh-loader-20 { width: 50px; height: 50px; perspective: 150px; } .mh-loader-20 div { width: 100%; height: 100%; border: 6px solid var(--mh-loader-color); border-radius: 50%; animation: mh-hyper-ring 2s linear infinite; }
    @keyframes mh-hyper-ring { 0% { transform: rotateX(60deg) rotateZ(0deg); } 100% { transform: rotateX(60deg) rotateZ(360deg); } }
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

    function updateLivePreview() {
        var effect = $('#mh_css_effect_select').val();
        var loaderColor = $('#mh_loader_color_picker').val();
        var bgColor = $('#mh_bg_color_picker').val();
        var imgWidth = $('#mh_img_width_range').val();
        
        $('#mh_loader_color_picker').siblings('.mh-color-overlay').css('background-color', loaderColor);
        $('#mh_bg_color_picker').siblings('.mh-color-overlay').css('background-color', bgColor);
        $('#mh_img_width_val').text(imgWidth);
        $('#mh-preloader-preview').css('width', imgWidth + 'px');

        var html = '';
        if (effect === '1') html = '<div class="mh-loader-1"></div>';
        if (effect === '2') html = '<div class="mh-loader-2"><div></div><div></div><div></div></div>';
        if (effect === '3') html = '<div class="mh-loader-3"></div>';
        if (effect === '4') html = '<div class="mh-loader-4"></div>';
        if (effect === '5') html = '<div class="mh-loader-5"><div></div><div></div></div>';
        if (effect === '6') html = '<div class="mh-loader-6"><div></div><div></div><div></div><div></div><div></div></div>';
        if (effect === '7') html = '<div class="mh-loader-7"></div>';
        if (effect === '8') html = '<div class="mh-loader-8"></div>';
        if (effect === '9') html = '<div class="mh-loader-9"><div></div><div></div></div>';
        if (effect === '10') html = '<div class="mh-loader-10"><div></div><div></div></div>';
        if (effect === '11') html = '<div class="mh-loader-11"><div></div></div>';
        if (effect === '12') html = '<div class="mh-loader-12"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
        if (effect === '13') html = '<div class="mh-loader-13"><div></div><div></div></div>';
        if (effect === '14') html = '<div class="mh-loader-14"><div></div><div></div><div></div></div>';
        if (effect === '15') html = '<div class="mh-loader-15"><div></div><div></div><div></div><div></div></div>';
        if (effect === '16') html = '<div class="mh-loader-16"></div>';
        if (effect === '17') html = '<div class="mh-loader-17"><div></div></div>';
        if (effect === '18') html = '<div class="mh-loader-18"><div></div><div></div><div></div><div></div></div>';
        if (effect === '19') html = '<div class="mh-loader-19"></div>';
        if (effect === '20') html = '<div class="mh-loader-20"><div></div></div>';

        $('#mh-css-preloader-preview-box').html(html).css({ '--mh-loader-color': loaderColor, 'background-color': bgColor });
        $('.mh-image-preview-wrapper').css('background-color', bgColor);
    }

    $('#mh_css_effect_select, #mh_loader_color_picker, #mh_bg_color_picker, #mh_img_width_range').on('change input', updateLivePreview);
    updateLivePreview();

    var mediaUploader;
    $('#mh-upload-preloader-btn').click(function(e) {
        e.preventDefault();
        if (mediaUploader) { mediaUploader.open(); return; }
        mediaUploader = wp.media.frames.file_frame = wp.media({ title: 'Select Asset', button: { text: 'Inject Asset' }, multiple: false });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mh-preloader-image-url').val(attachment.url);
            $('#mh-preloader-preview').attr('src', attachment.url).show();
            $('#mh-remove-preloader-btn').css('display', 'inline-flex');
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