<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) { exit; }

// Fetch existing settings
$settings = get_option('mh_plug_preloader_settings', []);
$enable       = isset($settings['enable']) ? $settings['enable'] : 'no';
$type         = isset($settings['type']) ? $settings['type'] : 'css';
$css_effect   = isset($settings['css_effect']) ? $settings['css_effect'] : '1';
$image        = isset($settings['image']) ? $settings['image'] : '';
$img_width    = isset($settings['img_width']) ? $settings['img_width'] : '100';
$effect_size  = isset($settings['effect_size']) ? $settings['effect_size'] : '1.0';

// Loader Gradient Settings
$loader_color_type = isset($settings['loader_color_type']) ? $settings['loader_color_type'] : 'solid';
$loader_c1         = isset($settings['loader_c1']) ? $settings['loader_c1'] : '#2293e9';
$loader_c2         = isset($settings['loader_c2']) ? $settings['loader_c2'] : '#004265';
$loader_angle      = isset($settings['loader_angle']) ? $settings['loader_angle'] : '90';

// Background Gradient Settings
$bg_color_type = isset($settings['bg_color_type']) ? $settings['bg_color_type'] : 'solid';
$bg_c1         = isset($settings['bg_c1']) ? $settings['bg_c1'] : '#0f172a';
$bg_c2         = isset($settings['bg_c2']) ? $settings['bg_c2'] : '#1e293b';
$bg_angle      = isset($settings['bg_angle']) ? $settings['bg_angle'] : '90';

// Text Gradient Settings
$custom_text   = isset($settings['custom_text']) ? $settings['custom_text'] : 'SYSTEM LOADING...';
$text_size     = isset($settings['text_size']) ? $settings['text_size'] : '16';
$text_anim     = isset($settings['text_anim']) ? $settings['text_anim'] : 'pulse';
$text_color_type = isset($settings['text_color_type']) ? $settings['text_color_type'] : 'solid';
$text_c1         = isset($settings['text_c1']) ? $settings['text_c1'] : '#2293e9';
$text_c2         = isset($settings['text_c2']) ? $settings['text_c2'] : '#00ffd5';
$text_angle      = isset($settings['text_angle']) ? $settings['text_angle'] : '90';

// Timers
$delay      = isset($settings['delay']) ? $settings['delay'] : '500';
$transition = isset($settings['transition']) ? $settings['transition'] : '500';

// Reusable Elementor-Style Gradient UI Component
function mh_render_color_group($name_prefix, $label, $type_val, $c1_val, $c2_val, $angle_val) {
    ?>
    <div class="mh-field-group">
        <label><?php echo esc_html($label); ?></label>
        <div class="mh-elementor-color-group">
            <div class="mh-color-tabs">
                <div class="mh-tab <?php echo $type_val === 'solid' ? 'active' : ''; ?>" data-type="solid" data-target="<?php echo $name_prefix; ?>"><i class="fas fa-paint-brush"></i> Classic</div>
                <div class="mh-tab <?php echo $type_val === 'gradient' ? 'active' : ''; ?>" data-type="gradient" data-target="<?php echo $name_prefix; ?>"><i class="fas fa-fill-drip"></i> Gradient</div>
            </div>
            <input type="hidden" id="<?php echo $name_prefix; ?>_type" name="mh_plug_preloader_settings[<?php echo $name_prefix; ?>_type]" value="<?php echo esc_attr($type_val); ?>">
            
            <div class="mh-color-body">
                <div class="mh-color-picker-wrap">
                    <span class="mh-picker-label">Color 1</span>
                    <input type="color" id="<?php echo $name_prefix; ?>_c1" name="mh_plug_preloader_settings[<?php echo $name_prefix; ?>_c1]" value="<?php echo esc_attr($c1_val); ?>" class="mh-live-trigger" />
                </div>
                
                <div class="mh-gradient-controls" style="display: <?php echo $type_val === 'gradient' ? 'block' : 'none'; ?>;">
                    <div class="mh-color-picker-wrap" style="margin-top: 10px;">
                        <span class="mh-picker-label">Color 2</span>
                        <input type="color" id="<?php echo $name_prefix; ?>_c2" name="mh_plug_preloader_settings[<?php echo $name_prefix; ?>_c2]" value="<?php echo esc_attr($c2_val); ?>" class="mh-live-trigger" />
                    </div>
                    <div class="mh-range-slider" style="margin-top: 10px; background: transparent; padding: 0; border: none;">
                        <span class="mh-picker-label" style="min-width: 50px;">Angle</span>
                        <input type="range" id="<?php echo $name_prefix; ?>_angle" name="mh_plug_preloader_settings[<?php echo $name_prefix; ?>_angle]" min="0" max="360" value="<?php echo esc_attr($angle_val); ?>" class="mh-live-trigger" />
                        <span class="mh-range-badge"><span class="mh-angle-val"><?php echo esc_attr($angle_val); ?></span>°</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="wrap mh-plug-admin-wrap mh-futuristic-dashboard">
    <div class="mh-dashboard-header">
        <div class="mh-header-glow"></div>
        <h1><i class="fas fa-space-shuttle"></i> <?php esc_html_e('Preloader Engine', 'mh-plug'); ?></h1>
        <p><?php esc_html_e('Advanced holographic gradient engine deployed.', 'mh-plug'); ?></p>
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
                        
                        <label class="switch">
                            <input class="cb mh-live-trigger" type="checkbox" name="mh_plug_preloader_settings[enable]" value="yes" <?php checked($enable, 'yes'); ?> />
                            <span class="toggle"><span class="left">off</span><span class="right">on</span></span>
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
                        <div id="mh-live-preview-box" class="mh-preview-box">
                            <svg style="width:0;height:0;position:absolute;" aria-hidden="true" focusable="false">
                              <linearGradient id="mh-svg-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop id="mh-svg-c1" offset="0%" stop-color="#2293e9" />
                                <stop id="mh-svg-c2" offset="100%" stop-color="#00ffd5" />
                              </linearGradient>
                            </svg>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 25px; z-index: 5;">
                                <div id="mh-loader-visual"></div>
                                <div id="mh-loader-text-display"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card mh-css-settings" style="display: <?php echo ($type === 'css') ? 'block' : 'none'; ?>;">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-magic"></i> <?php esc_html_e('Animation Protocol', 'mh-plug'); ?></h3>
                    
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Select Sequence (30 Total)', 'mh-plug'); ?></label>
                        <select name="mh_plug_preloader_settings[css_effect]" id="mh_css_effect_select" class="mh-cyber-select mh-live-trigger">
                            <optgroup label="Basic Shapes">
                                <option value="1" <?php selected($css_effect, '1'); ?>>1. Classic Spinner</option>
                                <option value="2" <?php selected($css_effect, '2'); ?>>2. Bouncing Dots</option>
                                <option value="3" <?php selected($css_effect, '3'); ?>>3. Pulse Circle</option>
                                <option value="4" <?php selected($css_effect, '4'); ?>>4. Flipping Square</option>
                                <option value="5" <?php selected($css_effect, '5'); ?>>5. Double Bounce</option>
                                <option value="6" <?php selected($css_effect, '6'); ?>>6. Bar Wave</option>
                            </optgroup>
                            <optgroup label="Futuristic Designs">
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
                            </optgroup>
                            <optgroup label="E-Commerce Specials 🚀">
                                <option value="21" <?php selected($css_effect, '21'); ?>>21. Shopping Cart Dash</option>
                                <option value="22" <?php selected($css_effect, '22'); ?>>22. Shopping Bag Drop</option>
                                <option value="23" <?php selected($css_effect, '23'); ?>>23. Price Tag Flip</option>
                                <option value="24" <?php selected($css_effect, '24'); ?>>24. Delivery Truck</option>
                                <option value="25" <?php selected($css_effect, '25'); ?>>25. Gift Box Shake</option>
                                <option value="26" <?php selected($css_effect, '26'); ?>>26. Credit Card Swipe</option>
                                <option value="27" <?php selected($css_effect, '27'); ?>>27. Barcode Scan</option>
                                <option value="28" <?php selected($css_effect, '28'); ?>>28. Coin Spin</option>
                                <option value="29" <?php selected($css_effect, '29'); ?>>29. Package Pulse</option>
                                <option value="30" <?php selected($css_effect, '30'); ?>>30. Storefront Swing</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Animation Scale (Size)', 'mh-plug'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" id="mh_effect_size_range" name="mh_plug_preloader_settings[effect_size]" min="0.5" max="3.0" step="0.1" value="<?php echo esc_attr($effect_size); ?>" class="mh-live-trigger" />
                            <span class="mh-range-badge"><span class="mh-scale-val"><?php echo esc_attr($effect_size); ?></span>x</span>
                        </div>
                    </div>

                    <?php mh_render_color_group('loader', 'Effect Color / Gradient', $loader_color_type, $loader_c1, $loader_c2, $loader_angle); ?>
                </div>
            </div>

            <div class="mh-futuristic-card mh-image-settings" style="display: <?php echo ($type === 'image') ? 'block' : 'none'; ?>;">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-file-upload"></i> <?php esc_html_e('Media Protocol', 'mh-plug'); ?></h3>
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Upload Asset (GIF/PNG)', 'mh-plug'); ?></label>
                        <input type="hidden" id="mh-preloader-image-url" name="mh_plug_preloader_settings[image]" value="<?php echo esc_attr($image); ?>" class="mh-live-trigger" />
                        <div class="mh-action-buttons">
                            <button type="button" class="mh-cyber-btn mh-btn-blue" id="mh-upload-preloader-btn"><i class="fas fa-cloud-upload-alt"></i> <?php esc_html_e('Inject Media', 'mh-plug'); ?></button>
                            <button type="button" class="mh-cyber-btn mh-btn-red" id="mh-remove-preloader-btn" style="display: <?php echo empty($image) ? 'none' : 'inline-flex'; ?>;"><i class="fas fa-trash"></i> <?php esc_html_e('Purge', 'mh-plug'); ?></button>
                        </div>
                    </div>
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Asset Scale (Width px)', 'mh-plug'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" id="mh_img_width_range" name="mh_plug_preloader_settings[img_width]" min="20" max="300" value="<?php echo esc_attr($img_width); ?>" class="mh-live-trigger" />
                            <span class="mh-range-badge"><span class="mh-width-val"><?php echo esc_attr($img_width); ?></span>px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-font"></i> <?php esc_html_e('Holographic Text Protocol', 'mh-plug'); ?></h3>
                    
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Custom Loading Text', 'mh-plug'); ?></label>
                        <input type="text" id="mh_custom_text_input" name="mh_plug_preloader_settings[custom_text]" value="<?php echo esc_attr($custom_text); ?>" class="mh-cyber-input mh-live-trigger" placeholder="e.g. SYSTEM LOADING..." />
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Text Animation Effect', 'mh-plug'); ?></label>
                        <select name="mh_plug_preloader_settings[text_anim]" id="mh_text_anim_select" class="mh-cyber-select mh-live-trigger">
                            <option value="none" <?php selected($text_anim, 'none'); ?>>None</option>
                            <option value="blink" <?php selected($text_anim, 'blink'); ?>>Blink</option>
                            <option value="pulse" <?php selected($text_anim, 'pulse'); ?>>Pulse Grow</option>
                            <option value="float" <?php selected($text_anim, 'float'); ?>>Floating</option>
                            <option value="tracking" <?php selected($text_anim, 'tracking'); ?>>Tracking (Spacing Expand)</option>
                        </select>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Text Size (px)', 'mh-plug'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" id="mh_text_size_range" name="mh_plug_preloader_settings[text_size]" min="10" max="40" value="<?php echo esc_attr($text_size); ?>" class="mh-live-trigger" />
                            <span class="mh-range-badge"><span class="mh-text-size-val"><?php echo esc_attr($text_size); ?></span>px</span>
                        </div>
                    </div>

                    <?php mh_render_color_group('text', 'Text Color / Gradient', $text_color_type, $text_c1, $text_c2, $text_angle); ?>
                </div>
            </div>

            <div class="mh-futuristic-card">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-globe"></i> <?php esc_html_e('Environment Variables', 'mh-plug'); ?></h3>
                    
                    <?php mh_render_color_group('bg', 'Backdrop Color / Gradient', $bg_color_type, $bg_c1, $bg_c2, $bg_angle); ?>

                    <div class="mh-field-group" style="margin-top:20px;">
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
    :root { --mh-dark: #004265; --mh-blue: #2293e9; --mh-blue-glow: rgba(34, 147, 233, 0.4); --mh-red: #d63638; --mh-border: #e2e8f0; }
    
    /* Dashboard Grid & Cards */
    .mh-futuristic-dashboard { max-width: 1200px; margin: 20px auto; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .mh-dashboard-header { position: relative; padding: 30px; background: var(--mh-dark); border-radius: 16px; color: white; margin-bottom: 30px; overflow: hidden; }
    .mh-dashboard-header h1 { margin: 0 0 10px 0; font-size: 28px; font-weight: 800; color:white; z-index: 2; position: relative; }
    .mh-dashboard-header p { margin: 0; font-size: 16px; color: #94a3b8; z-index: 2; position: relative; }
    .mh-header-glow { position: absolute; right: -50px; top: -50px; width: 250px; height: 250px; background: radial-gradient(circle, var(--mh-blue-glow) 0%, transparent 70%); }
    .mh-settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
    .mh-card-wide { grid-column: 1 / -1; }
    .mh-futuristic-card { background: #fff; border-radius: 16px; border: 1px solid var(--mh-border); box-shadow: 0 10px 25px rgba(0,0,0,0.03); position: relative; }
    .mh-card-inner { padding: 25px; }
    .mh-futuristic-card h3 { margin: 0 0 20px 0; color: var(--mh-dark); font-size: 18px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--mh-border); padding-bottom: 15px; }
    .mh-futuristic-card h3 i { color: var(--mh-blue); }
    .mh-setting-row { display: flex; justify-content: space-between; align-items: center; gap: 20px; }
    .mh-setting-info h3 { border: none; padding: 0; margin: 0 0 5px 0; font-size: 16px; }
    .mh-setting-info p { margin: 0; font-size: 13px; color: #64748b; }
    .mh-divider { height: 1px; background: var(--mh-border); margin: 25px 0; }
    .mh-field-group { margin-bottom: 20px; }
    .mh-field-group label { display: block; font-weight: 600; color: var(--mh-text); margin-bottom: 10px; }
    
    /* Inputs */
    .mh-cyber-radio-group { display: flex; gap: 15px; }
    .mh-cyber-radio { cursor: pointer; flex: 1; }
    .mh-cyber-radio input { display: none; }
    .mh-cyber-radio span { display: block; text-align: center; padding: 12px; background: #f8fafc; border: 2px solid var(--mh-border); border-radius: 10px; font-weight: 600; color: #64748b; transition: 0.3s; }
    .mh-cyber-radio input:checked + span { background: rgba(34, 147, 233, 0.05); border-color: var(--mh-blue); color: var(--mh-blue); }
    .mh-cyber-select, .mh-cyber-input { width: 100%; padding: 12px 15px; border-radius: 10px; border: 2px solid var(--mh-border); background: #f8fafc; font-weight: 600; outline: none; }
    .mh-range-slider { display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 10px 15px; border-radius: 10px; border: 1px solid var(--mh-border); }
    .mh-range-slider input[type="range"] { flex: 1; height: 6px; background: var(--mh-border); border-radius: 5px; outline: none; }
    .mh-range-badge { background: var(--mh-dark); color: white; padding: 5px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; min-width: 65px; text-align: center; }
    .mh-action-buttons { display: flex; gap: 10px; margin-top:10px; }
    .mh-cyber-btn { border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; transition: 0.3s; }
    .mh-btn-blue { background: var(--mh-blue); color: white; }
    .mh-btn-red { background: transparent; border: 2px solid var(--mh-red); color: var(--mh-red); }
    .mh-save-dock { margin-top: 30px; text-align: right; background: white; padding: 20px; border-radius: 16px; border: 1px solid var(--mh-border); }
    .mh-btn-save { background: var(--mh-dark); color: white; padding: 15px 40px; font-size: 16px; cursor: pointer; }

    /* Elementor-Style Gradient UI */
    .mh-elementor-color-group { background: #f8fafc; border: 1px solid var(--mh-border); border-radius: 10px; padding: 15px; }
    .mh-color-tabs { display: flex; background: #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 15px; }
    .mh-tab { flex: 1; text-align: center; padding: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: #64748b; transition: 0.3s; }
    .mh-tab.active { background: var(--mh-blue); color: white; }
    .mh-color-picker-wrap { display: flex; align-items: center; justify-content: space-between; background: white; border: 1px solid var(--mh-border); padding: 5px 15px; border-radius: 8px; }
    .mh-picker-label { font-weight: 600; font-size: 13px; }
    .mh-color-picker-wrap input[type="color"] { width: 40px; height: 30px; padding: 0; border: none; cursor: pointer; background: transparent; }

    /* Live HUD */
    .mh-hud-container { position: relative; width: 100%; height: 350px; border-radius: 12px; overflow: hidden; border: 2px solid var(--mh-blue); box-shadow: inset 0 0 30px var(--mh-blue-glow); background: #000; }
    .mh-hud-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(34,147,233,0.05) 0%, rgba(34,147,233,0.1) 50%, rgba(34,147,233,0.05) 100%); pointer-events: none; z-index: 10; }
    .mh-preview-box { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 5; transition: 0.3s; }

    /* TEXT & VISUAL ANIMATIONS */
    .mh-text-anim-blink { animation: mh-text-blink 1.5s infinite; }
    .mh-text-anim-pulse { animation: mh-text-pulse 2s infinite ease-in-out; }
    .mh-text-anim-float { animation: mh-text-float 2s infinite ease-in-out; }
    .mh-text-anim-tracking { animation: mh-text-tracking 2s infinite ease-in-out; }
    @keyframes mh-text-blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    @keyframes mh-text-pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.7; } }
    @keyframes mh-text-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    @keyframes mh-text-tracking { 0%, 100% { letter-spacing: 2px; } 50% { letter-spacing: 8px; } }

    .mh-loader-1 { width: 50px; height: 50px; border: 5px solid rgba(255,255,255,0.1); border-top: 5px solid var(--mh-c1); border-radius: 50%; animation: mh-spin 1s linear infinite; } @keyframes mh-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .mh-loader-2 { display: flex; gap: 8px; } .mh-loader-2 div { width: 16px; height: 16px; background: var(--mh-bg-style); border-radius: 50%; animation: mh-bounce 1.4s infinite ease-in-out both; } .mh-loader-2 div:nth-child(1) { animation-delay: -0.32s; } .mh-loader-2 div:nth-child(2) { animation-delay: -0.16s; } @keyframes mh-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
    .mh-loader-3 { width: 50px; height: 50px; background: var(--mh-bg-style); border-radius: 50%; animation: mh-pulse 1.2s infinite cubic-bezier(0.2, 0.6, 0.2, 1); } @keyframes mh-pulse { 0% { transform: scale(0); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
    .mh-loader-4 { width: 40px; height: 40px; background: var(--mh-bg-style); animation: mh-flip 1.2s infinite ease-in-out; } @keyframes mh-flip { 0% { transform: perspective(120px) rotateX(0deg) rotateY(0deg); } 50% { transform: perspective(120px) rotateX(-180deg) rotateY(0deg); } 100% { transform: perspective(120px) rotateX(-180deg) rotateY(-180deg); } }
    .mh-loader-5 { width: 50px; height: 50px; position: relative; } .mh-loader-5 div { width: 100%; height: 100%; border-radius: 50%; background: var(--mh-bg-style); opacity: 0.6; position: absolute; top: 0; left: 0; animation: mh-bounce2 2s infinite ease-in-out; } .mh-loader-5 div:nth-child(2) { animation-delay: -1.0s; } @keyframes mh-bounce2 { 0%, 100% { transform: scale(0); } 50% { transform: scale(1); } }
    .mh-loader-6 { display: flex; gap: 5px; height: 40px; align-items: center; } .mh-loader-6 div { width: 6px; height: 100%; background: var(--mh-bg-style); animation: mh-wave 1.2s infinite ease-in-out; } .mh-loader-6 div:nth-child(2) { animation-delay: -1.1s; } .mh-loader-6 div:nth-child(3) { animation-delay: -1.0s; } .mh-loader-6 div:nth-child(4) { animation-delay: -0.9s; } .mh-loader-6 div:nth-child(5) { animation-delay: -0.8s; } @keyframes mh-wave { 0%, 40%, 100% { transform: scaleY(0.4); } 20% { transform: scaleY(1); } }
    .mh-loader-7 { width: 50px; height: 50px; border-radius: 50%; background: conic-gradient(transparent 60%, var(--mh-c1)); animation: mh-spin 1s linear infinite; }
    .mh-loader-8 { width: 40px; height: 40px; background: var(--mh-bg-style); animation: mh-morph 2s infinite ease-in-out; } @keyframes mh-morph { 0% { border-radius: 0%; transform: rotate(0deg); } 50% { border-radius: 50%; transform: rotate(180deg); } 100% { border-radius: 0%; transform: rotate(360deg); } }
    .mh-loader-9 { width: 50px; height: 50px; position: relative; animation: mh-spin 2s linear infinite; } .mh-loader-9 div { position: absolute; width: 15px; height: 15px; background: var(--mh-bg-style); border-radius: 50%; top: 0; left: 50%; transform: translateX(-50%); } .mh-loader-9 div:nth-child(2) { top: auto; bottom: 0; }
    .mh-loader-10 { position: relative; width: 60px; height: 60px; } .mh-loader-10 div { position: absolute; border: 4px solid var(--mh-c1); opacity: 1; border-radius: 50%; animation: mh-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite; } .mh-loader-10 div:nth-child(2) { animation-delay: -0.5s; } @keyframes mh-ripple { 0% { top: 28px; left: 28px; width: 0; height: 0; opacity: 0; } 5% { opacity: 1; } 100% { top: -1px; left: -1px; width: 58px; height: 58px; opacity: 0; } }
    .mh-loader-11 { width: 50px; height: 50px; border: 3px dashed var(--mh-c1); border-radius: 50%; animation: mh-spin 4s linear infinite; display: flex; align-items: center; justify-content: center; } .mh-loader-11 div { width: 20px; height: 20px; background: var(--mh-bg-style); clip-path: polygon(50% 0%, 0% 100%, 100% 100%); animation: mh-spin-reverse 2s linear infinite; } @keyframes mh-spin-reverse { 0% { transform: rotate(360deg); } 100% { transform: rotate(0deg); } }
    .mh-loader-12 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; width: 50px; height: 50px; } .mh-loader-12 div { background: var(--mh-bg-style); border-radius: 50%; animation: mh-grid-pulse 1.2s infinite ease-in-out; } .mh-loader-12 div:nth-child(1), .mh-loader-12 div:nth-child(5), .mh-loader-12 div:nth-child(9) { animation-delay: 0.4s; } .mh-loader-12 div:nth-child(2), .mh-loader-12 div:nth-child(6), .mh-loader-12 div:nth-child(7) { animation-delay: 0.8s; } .mh-loader-12 div:nth-child(3), .mh-loader-12 div:nth-child(4), .mh-loader-12 div:nth-child(8) { animation-delay: 1.2s; } @keyframes mh-grid-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.3; transform: scale(0.5); } }
    .mh-loader-13 { position: relative; width: 60px; height: 20px; } .mh-loader-13 div { position: absolute; width: 20px; height: 20px; background: var(--mh-bg-style); border-radius: 50%; animation: mh-infinity 1.5s infinite ease-in-out; } .mh-loader-13 div:nth-child(2) { animation-delay: -0.75s; } @keyframes mh-infinity { 0% { left: 0; transform: scale(1); z-index: 1; } 25% { transform: scale(1.5); z-index: 2; } 50% { left: 40px; transform: scale(1); z-index: 1; } 75% { transform: scale(0.5); z-index: 0; } 100% { left: 0; transform: scale(1); z-index: 1; } }
    .mh-loader-14 { position: relative; width: 60px; height: 60px; } .mh-loader-14 div { position: absolute; width: 100%; height: 100%; border: 3px solid transparent; border-top-color: var(--mh-c1); border-radius: 50%; animation: mh-spin 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite; } .mh-loader-14 div:nth-child(2) { width: 80%; height: 80%; top: 10%; left: 10%; border-top-color: transparent; border-right-color: var(--mh-c1); animation-duration: 1.5s; animation-direction: reverse; } .mh-loader-14 div:nth-child(3) { width: 60%; height: 60%; top: 20%; left: 20%; border-top-color: transparent; border-bottom-color: var(--mh-c1); animation-duration: 1s; }
    .mh-loader-15 { width: 40px; height: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 4px; } .mh-loader-15 div { background: var(--mh-bg-style); animation: mh-cube-split 1.5s infinite ease-in-out; } .mh-loader-15 div:nth-child(1) { transform-origin: bottom right; } .mh-loader-15 div:nth-child(2) { transform-origin: bottom left; } .mh-loader-15 div:nth-child(3) { transform-origin: top right; } .mh-loader-15 div:nth-child(4) { transform-origin: top left; } @keyframes mh-cube-split { 0%, 100% { transform: scale(1); } 50% { transform: scale(0.5) rotate(90deg); border-radius: 50%; } }
    .mh-loader-16 { width: 20px; height: 20px; background: var(--mh-bg-style); border-radius: 50%; box-shadow: 0 0 20px 5px var(--mh-c1); animation: mh-glow-pulse 1s infinite alternate; } @keyframes mh-glow-pulse { 0% { box-shadow: 0 0 10px 2px var(--mh-c1); transform: scale(0.8); } 100% { box-shadow: 0 0 30px 10px var(--mh-c1); transform: scale(1.2); } }
    .mh-loader-17 { width: 50px; height: 50px; border-radius: 50%; border: 4px solid var(--mh-c1); border-color: var(--mh-c1) transparent; animation: mh-spin 1.2s linear infinite; position: relative; } .mh-loader-17 div { position: absolute; top: 4px; left: 4px; right: 4px; bottom: 4px; border-radius: 50%; border: 4px solid var(--mh-c1); border-color: transparent var(--mh-c1); animation: mh-spin-reverse 0.6s linear infinite; }
    .mh-loader-18 { display: flex; gap: 6px; height: 40px; align-items: flex-end; } .mh-loader-18 div { width: 8px; background: var(--mh-bg-style); animation: mh-stairway 1s infinite ease-in-out alternate; } .mh-loader-18 div:nth-child(1) { animation-delay: 0s; } .mh-loader-18 div:nth-child(2) { animation-delay: 0.2s; } .mh-loader-18 div:nth-child(3) { animation-delay: 0.4s; } .mh-loader-18 div:nth-child(4) { animation-delay: 0.6s; } @keyframes mh-stairway { 0% { height: 10px; } 100% { height: 40px; } }
    .mh-loader-19 { width: 50px; height: 50px; border-radius: 50%; box-shadow: inset 0 0 0 4px rgba(255,255,255,0.1); position: relative; } .mh-loader-19::after { content: ""; position: absolute; top: -4px; left: -4px; right: -4px; bottom: -4px; border-radius: 50%; border: 4px solid transparent; border-top-color: var(--mh-c1); animation: mh-spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite; }
    .mh-loader-20 { width: 50px; height: 50px; perspective: 150px; } .mh-loader-20 div { width: 100%; height: 100%; border: 6px solid var(--mh-c1); border-radius: 50%; animation: mh-hyper-ring 2s linear infinite; } @keyframes mh-hyper-ring { 0% { transform: rotateX(60deg) rotateZ(0deg); } 100% { transform: rotateX(60deg) rotateZ(360deg); } }

    .mh-ecommerce-icon { display: flex; align-items: center; justify-content: center; position: relative; }
    .mh-svg-icon { width: 50px; height: 50px; fill: none; stroke: var(--mh-svg-stroke); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .mh-loader-21 { animation: mh-cart-dash 1.5s infinite ease-in-out; } @keyframes mh-cart-dash { 0% { transform: translateX(-30px) rotate(-15deg); opacity: 0; } 50% { transform: translateX(0) rotate(0deg); opacity: 1; } 100% { transform: translateX(30px) rotate(15deg); opacity: 0; } }
    .mh-loader-22 { animation: mh-bag-drop 1.2s infinite cubic-bezier(0.28, 0.84, 0.42, 1); transform-origin: bottom; } @keyframes mh-bag-drop { 0% { transform: translateY(-30px) scaleY(1.2); opacity: 0; } 50% { transform: translateY(0) scaleY(0.8); opacity: 1; } 70% { transform: translateY(-10px) scaleY(1); } 100% { transform: translateY(0) scaleY(1); opacity: 0; } }
    .mh-loader-23 { animation: mh-tag-flip 1.5s infinite; perspective: 100px; } @keyframes mh-tag-flip { 0% { transform: rotateY(0deg); } 50% { transform: rotateY(180deg); } 100% { transform: rotateY(360deg); } }
    .mh-loader-24 { animation: mh-truck-drive 2s infinite linear; } @keyframes mh-truck-drive { 0% { transform: translateX(-40px); opacity: 0; } 20% { opacity: 1; } 80% { opacity: 1; } 100% { transform: translateX(40px); opacity: 0; } }
    .mh-loader-25 { animation: mh-gift-shake 1.5s infinite; } @keyframes mh-gift-shake { 0%, 100% { transform: rotate(0deg); } 10%, 30%, 50%, 70%, 90% { transform: rotate(-10deg); } 20%, 40%, 60%, 80% { transform: rotate(10deg); } }
    .mh-loader-26 .mh-svg-icon { animation: mh-card-swipe 1.5s infinite ease-in-out; } @keyframes mh-card-swipe { 0% { transform: translateY(-20px); opacity: 0; } 50% { transform: translateY(0); opacity: 1; } 100% { transform: translateY(20px); opacity: 0; } }
    .mh-loader-27 { position: relative; } .mh-scanline { position: absolute; top: 0; left: -10%; width: 120%; height: 3px; background: var(--mh-bg-style); animation: mh-scan 1.5s infinite linear; box-shadow: 0 0 8px var(--mh-c1); } @keyframes mh-scan { 0% { top: 0; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
    .mh-loader-28 { animation: mh-coin-drop 1.5s infinite ease-in-out; } @keyframes mh-coin-drop { 0% { transform: translateY(-30px) rotateY(0deg); opacity: 0; } 50% { transform: translateY(0) rotateY(180deg); opacity: 1; } 100% { transform: translateY(20px) rotateY(360deg); opacity: 0; } }
    .mh-loader-29 { animation: mh-box-pulse 1.2s infinite ease-in-out; } @keyframes mh-box-pulse { 0% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.2); opacity: 1; } 100% { transform: scale(1); opacity: 0.5; } }
    .mh-loader-30 { transform-origin: top center; animation: mh-store-swing 2s infinite ease-in-out; } @keyframes mh-store-swing { 0% { transform: rotate(-15deg); } 50% { transform: rotate(15deg); } 100% { transform: rotate(-15deg); } }
</style>

<script>
jQuery(document).ready(function($){
    
    // Type Toggle
    $('.mh-loader-type').change(function() {
        if ($(this).val() === 'css') {
            $('.mh-image-settings').hide();
            $('.mh-css-settings').fadeIn();
        } else {
            $('.mh-css-settings').hide();
            $('.mh-image-settings').fadeIn();
        }
    });

    // Elementor-Style Tab Switching
    $('.mh-tab').click(function(){
        var type = $(this).data('type');
        var target = $(this).data('target');
        
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $('#' + target + '_type').val(type);
        
        if(type === 'gradient') {
            $(this).closest('.mh-elementor-color-group').find('.mh-gradient-controls').slideDown();
        } else {
            $(this).closest('.mh-elementor-color-group').find('.mh-gradient-controls').slideUp();
        }
        updateLivePreview();
    });

    // Range Badge updates
    $('input[type="range"]').on('input', function(){
        $(this).siblings('.mh-range-badge').find('span').text($(this).val());
    });

    // Master Preview Updater
    function updateLivePreview() {
        var type = $('.mh-loader-type:checked').val();
        
        // Background
        var bgType = $('#bg_type').val();
        var bgC1 = $('#bg_c1').val();
        var bgC2 = $('#bg_c2').val();
        var bgAngle = $('#bg_angle').val();
        var bgStyle = (bgType === 'gradient') ? 'linear-gradient(' + bgAngle + 'deg, ' + bgC1 + ', ' + bgC2 + ')' : bgC1;
        $('#mh-live-preview-box').css('background', bgStyle);

        // Effect Loader
        var effect = $('#mh_css_effect_select').val();
        var effType = $('#loader_type').val();
        var effC1 = $('#loader_c1').val();
        var effC2 = $('#loader_c2').val();
        var effAngle = $('#loader_angle').val();
        var effBgStyle = (effType === 'gradient') ? 'linear-gradient(' + effAngle + 'deg, ' + effC1 + ', ' + effC2 + ')' : effC1;
        var effectScale = $('#mh_effect_size_range').val();
        var svgStroke = (effType === 'gradient') ? 'url(#mh-svg-gradient)' : effC1;

        // Text
        var customText = $('#mh_custom_text_input').val();
        var textAnim = $('#mh_text_anim_select').val();
        var textSize = $('#mh_text_size_range').val();
        var textType = $('#text_type').val();
        var textC1 = $('#text_c1').val();
        var textC2 = $('#text_c2').val();
        var textAngle = $('#text_angle').val();
        var textBgStyle = (textType === 'gradient') ? 'linear-gradient(' + textAngle + 'deg, ' + textC1 + ', ' + textC2 + ')' : textC1;

        // Update SVG Gradient Data
        $('#mh-svg-c1').attr('stop-color', effC1);
        $('#mh-svg-c2').attr('stop-color', effC2);

        // Render Visual Base
        if(type === 'image') {
            var imgUrl = $('#mh-preloader-image-url').val();
            var imgWidth = $('#mh_img_width_range').val();
            if(imgUrl) {
                $('#mh-loader-visual').html('<img src="'+imgUrl+'" style="width:'+imgWidth+'px; height:auto;"/>');
            } else {
                $('#mh-loader-visual').html('<i class="dashicons dashicons-format-image" style="font-size:40px; color:#888; width:40px; height:40px;"></i>');
            }
        } else {
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
            // SVGs
            if (effect === '21') html = '<div class="mh-loader-21 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg></div>';
            if (effect === '22') html = '<div class="mh-loader-22 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></div>';
            if (effect === '23') html = '<div class="mh-loader-23 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></div>';
            if (effect === '24') html = '<div class="mh-loader-24 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></div>';
            if (effect === '25') html = '<div class="mh-loader-25 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg></div>';
            if (effect === '26') html = '<div class="mh-loader-26 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>';
            if (effect === '27') html = '<div class="mh-loader-27 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 5v14M8 5v14M12 5v14M17 5v14M21 5v14"></path></svg><div class="mh-scanline"></div></div>';
            if (effect === '28') html = '<div class="mh-loader-28 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8"></path><path d="M9 12h6"></path></svg></div>';
            if (effect === '29') html = '<div class="mh-loader-29 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></div>';
            if (effect === '30') html = '<div class="mh-loader-30 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>';

            // Wrap in scale block
            $('#mh-loader-visual').html('<div style="transform: scale('+effectScale+'); --mh-c1: '+effC1+'; --mh-bg-style: '+effBgStyle+'; --mh-svg-stroke: '+svgStroke+';">' + html + '</div>');
        }

        // Render Text Base
        if(customText.trim() !== '') {
            var textStyle = (textType === 'gradient') ? 'background: ' + textBgStyle + '; -webkit-background-clip: text; -webkit-text-fill-color: transparent;' : 'color: ' + textC1 + ';';
            $('#mh-loader-text-display').html('<div class="mh-text-anim-'+textAnim+'" style="' + textStyle + ' font-size: '+textSize+'px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; text-align: center;">' + customText + '</div>').show();
        } else {
            $('#mh-loader-text-display').hide();
        }
    }

    $('.mh-live-trigger').on('change input', updateLivePreview);
    updateLivePreview(); // Init

    // WP Media Library Handler
    var mediaUploader;
    $('#mh-upload-preloader-btn').click(function(e) {
        e.preventDefault();
        if (mediaUploader) { mediaUploader.open(); return; }
        mediaUploader = wp.media.frames.file_frame = wp.media({ title: 'Select Asset', button: { text: 'Inject Asset' }, multiple: false });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mh-preloader-image-url').val(attachment.url);
            updateLivePreview();
            $('#mh-remove-preloader-btn').css('display', 'inline-flex');
        });
        mediaUploader.open();
    });

    $('#mh-remove-preloader-btn').click(function(e){
        e.preventDefault();
        $('#mh-preloader-image-url').val('');
        updateLivePreview();
        $(this).hide();
    });
});
</script>