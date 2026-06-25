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

$loader_color_type = isset($settings['loader_color_type']) ? $settings['loader_color_type'] : 'solid';
$loader_c1         = isset($settings['loader_c1']) ? $settings['loader_c1'] : '#2293e9';
$loader_c2         = isset($settings['loader_c2']) ? $settings['loader_c2'] : '#004265';
$loader_angle      = isset($settings['loader_angle']) ? $settings['loader_angle'] : '90';

$bg_color_type = isset($settings['bg_color_type']) ? $settings['bg_color_type'] : 'solid';
$bg_c1         = isset($settings['bg_c1']) ? $settings['bg_c1'] : '#0f172a';
$bg_c2         = isset($settings['bg_c2']) ? $settings['bg_c2'] : '#1e293b';
$bg_angle      = isset($settings['bg_angle']) ? $settings['bg_angle'] : '90';

$custom_text   = isset($settings['custom_text']) ? $settings['custom_text'] : 'SYSTEM LOADING...';
$text_size     = isset($settings['text_size']) ? $settings['text_size'] : '16';
$text_anim     = isset($settings['text_anim']) ? $settings['text_anim'] : 'pulse';
$text_color_type = isset($settings['text_color_type']) ? $settings['text_color_type'] : 'solid';
$text_c1         = isset($settings['text_c1']) ? $settings['text_c1'] : '#2293e9';
$text_c2         = isset($settings['text_c2']) ? $settings['text_c2'] : '#00ffd5';
$text_angle      = isset($settings['text_angle']) ? $settings['text_angle'] : '90';

$delay      = isset($settings['delay']) ? $settings['delay'] : '500';
$transition = isset($settings['transition']) ? $settings['transition'] : '500';

function mh_render_color_group($name_prefix, $label, $type_val, $c1_val, $c2_val, $angle_val) {
    ?>
    <div class="mh-field-group">
        <label><?php echo esc_html($label); ?></label>
        <div class="mh-elementor-color-group">
            <div class="mh-color-tabs">
                <div class="mh-tab <?php echo $type_val === 'solid' ? 'active' : ''; ?>" data-type="solid" data-target="<?php echo esc_attr($name_prefix); ?>"><i class="fas fa-paint-brush"></i> Classic</div>
                <div class="mh-tab <?php echo $type_val === 'gradient' ? 'active' : ''; ?>" data-type="gradient" data-target="<?php echo esc_attr($name_prefix); ?>"><i class="fas fa-fill-drip"></i> Gradient</div>
            </div>
            <input type="hidden" id="<?php echo esc_attr($name_prefix); ?>_type" name="mh_plug_preloader_settings[<?php echo esc_attr($name_prefix); ?>_type]" value="<?php echo esc_attr($type_val); ?>">
            
            <div class="mh-color-body">
                <div class="mh-color-picker-wrap">
                    <span class="mh-picker-label">Color 1</span>
                    <input type="color" id="<?php echo esc_attr($name_prefix); ?>_c1" name="mh_plug_preloader_settings[<?php echo esc_attr($name_prefix); ?>_c1]" value="<?php echo esc_attr($c1_val); ?>" class="mh-live-trigger" />
                </div>
                <div class="mh-gradient-controls" style="display: <?php echo $type_val === 'gradient' ? 'block' : 'none'; ?>;">
                    <div class="mh-color-picker-wrap" style="margin-top: 10px;">
                        <span class="mh-picker-label">Color 2</span>
                        <input type="color" id="<?php echo esc_attr($name_prefix); ?>_c2" name="mh_plug_preloader_settings[<?php echo esc_attr($name_prefix); ?>_c2]" value="<?php echo esc_attr($c2_val); ?>" class="mh-live-trigger" />
                    </div>
                    <div class="mh-range-slider" style="margin-top: 10px; background: transparent; padding: 0; border: none;">
                        <span class="mh-picker-label" style="min-width: 50px;">Angle</span>
                        <input type="range" id="<?php echo esc_attr($name_prefix); ?>_angle" name="mh_plug_preloader_settings[<?php echo esc_attr($name_prefix); ?>_angle]" min="0" max="360" value="<?php echo esc_attr($angle_val); ?>" class="mh-live-trigger" />
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
        <h1><i class="fas fa-space-shuttle"></i> <?php esc_html_e('Preloader Engine', 'mh-plug-ecommerce-builder-widgets'); ?></h1>
        <p><?php esc_html_e('Advanced holographic gradient engine deployed.', 'mh-plug-ecommerce-builder-widgets'); ?></p>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields('mh_plug_preloader_group'); ?>
        <div class="mh-settings-grid">
            <div class="mh-futuristic-card mh-card-wide">
                <div class="mh-card-inner">
                    <div class="mh-setting-row">
                        <div class="mh-setting-info">
                            <h3><?php esc_html_e('System Power', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                            <p><?php esc_html_e('Activate the global preloader on your website.', 'mh-plug-ecommerce-builder-widgets'); ?></p>
                        </div>
                        <label class="switch">
                            <input class="cb mh-live-trigger" type="checkbox" name="mh_plug_preloader_settings[enable]" value="yes" <?php checked($enable, 'yes'); ?> />
                            <span class="toggle"><span class="left">off</span><span class="right">on</span></span>
                        </label>
                    </div>
                    <div class="mh-divider"></div>
                    <div class="mh-setting-row">
                        <div class="mh-setting-info">
                            <h3><?php esc_html_e('Render Engine Type', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                            <p><?php esc_html_e('Choose between CSS animations or custom media.', 'mh-plug-ecommerce-builder-widgets'); ?></p>
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
                    <h3><i class="fas fa-eye"></i> <?php esc_html_e('Live Hologram Preview', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                    <div class="mh-hud-container">
                        <div class="mh-hud-overlay"></div>
                        <div id="mh-live-preview-box" class="mh-preview-box">
                            <svg style="width:0;height:0;position:absolute;" aria-hidden="true" focusable="false">
                              <linearGradient id="mh-svg-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop id="mh-svg-c1" offset="0%" stop-color="#2293e9" />
                                <stop id="mh-svg-c2" offset="100%" stop-color="#00ffd5" />
                              </linearGradient>
                            </svg>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 35px; z-index: 5;">
                                <div id="mh-loader-visual"></div>
                                <div id="mh-loader-text-display"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card mh-css-settings" style="display: <?php echo ($type === 'css') ? 'block' : 'none'; ?>;">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-magic"></i> <?php esc_html_e('Animation Protocol', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Select Sequence (30 Total)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
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
                        <label><?php esc_html_e('Animation Scale (Size)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
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
                    <h3><i class="fas fa-file-upload"></i> <?php esc_html_e('Media Protocol', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Upload Asset (GIF/PNG)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
                        <input type="hidden" id="mh-preloader-image-url" name="mh_plug_preloader_settings[image]" value="<?php echo esc_attr($image); ?>" class="mh-live-trigger" />
                        <div class="mh-action-buttons">
                            <button type="button" class="mh-cyber-btn mh-btn-blue" id="mh-upload-preloader-btn"><i class="fas fa-cloud-upload-alt"></i> <?php esc_html_e('Inject Media', 'mh-plug-ecommerce-builder-widgets'); ?></button>
                            <button type="button" class="mh-cyber-btn mh-btn-red" id="mh-remove-preloader-btn" style="display: <?php echo empty($image) ? 'none' : 'inline-flex'; ?>;"><i class="fas fa-trash"></i> <?php esc_html_e('Purge', 'mh-plug-ecommerce-builder-widgets'); ?></button>
                        </div>
                    </div>
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Asset Scale (Width px)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" id="mh_img_width_range" name="mh_plug_preloader_settings[img_width]" min="20" max="300" value="<?php echo esc_attr($img_width); ?>" class="mh-live-trigger" />
                            <span class="mh-range-badge"><span class="mh-width-val"><?php echo esc_attr($img_width); ?></span>px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mh-futuristic-card">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-font"></i> <?php esc_html_e('Holographic Text Protocol', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                    
                    <div class="mh-field-group">
                        <label><?php esc_html_e('Custom Loading Text', 'mh-plug-ecommerce-builder-widgets'); ?></label>
                        <input type="text" id="mh_custom_text_input" name="mh_plug_preloader_settings[custom_text]" value="<?php echo esc_attr($custom_text); ?>" class="mh-cyber-input mh-live-trigger" placeholder="e.g. SYSTEM LOADING..." />
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Text Animation Effect', 'mh-plug-ecommerce-builder-widgets'); ?></label>
                        <select name="mh_plug_preloader_settings[text_anim]" id="mh_text_anim_select" class="mh-cyber-select mh-live-trigger">
                            <option value="none" <?php selected($text_anim, 'none'); ?>>None</option>
                            <option value="blink" <?php selected($text_anim, 'blink'); ?>>Blink</option>
                            <option value="pulse" <?php selected($text_anim, 'pulse'); ?>>Pulse Grow</option>
                            <option value="float" <?php selected($text_anim, 'float'); ?>>Floating</option>
                            <option value="tracking" <?php selected($text_anim, 'tracking'); ?>>Tracking (Spacing Expand)</option>
                            <option value="bounce" <?php selected($text_anim, 'bounce'); ?>>Bounce</option>
                            <option value="blur" <?php selected($text_anim, 'blur'); ?>>Blur Pulse</option>
                            <option value="neon" <?php selected($text_anim, 'neon'); ?>>Neon Glow</option>
                            <option value="shake" <?php selected($text_anim, 'shake'); ?>>Shake</option>
                            <option value="flip-y" <?php selected($text_anim, 'flip-y'); ?>>Flip 3D</option>
                            <option value="slide-fade" <?php selected($text_anim, 'slide-fade'); ?>>Slide & Fade</option>
                            <option value="zoom" <?php selected($text_anim, 'zoom'); ?>>Zoom In/Out</option>
                            <option value="jello" <?php selected($text_anim, 'jello'); ?>>Jello Wobble</option>
                            <option value="swing" <?php selected($text_anim, 'swing'); ?>>Pendulum Swing</option>
                            <option value="glitch" <?php selected($text_anim, 'glitch'); ?>>Cyber Glitch</option>
                            <option value="typing" <?php selected($text_anim, 'typing'); ?>>Typewriter (Typing Effect)</option>
                        </select>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Text Size (px)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" id="mh_text_size_range" name="mh_plug_preloader_settings[text_size]" min="10" max="60" value="<?php echo esc_attr($text_size); ?>" class="mh-live-trigger" />
                            <span class="mh-range-badge"><span class="mh-text-size-val"><?php echo esc_attr($text_size); ?></span>px</span>
                        </div>
                    </div>

                    <?php mh_render_color_group('text', 'Text Color / Gradient', $text_color_type, $text_c1, $text_c2, $text_angle); ?>
                </div>
            </div>

            <div class="mh-futuristic-card">
                <div class="mh-card-inner">
                    <h3><i class="fas fa-globe"></i> <?php esc_html_e('Environment Variables', 'mh-plug-ecommerce-builder-widgets'); ?></h3>
                    
                    <?php mh_render_color_group('bg', 'Backdrop Color / Gradient', $bg_color_type, $bg_c1, $bg_c2, $bg_angle); ?>

                    <div class="mh-field-group" style="margin-top:20px;">
                        <label><?php esc_html_e('Retention Time (Delay ms)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
                        <div class="mh-range-slider">
                            <input type="range" name="mh_plug_preloader_settings[delay]" min="0" max="3000" step="100" value="<?php echo esc_attr($delay); ?>" oninput="this.nextElementSibling.querySelector('span').innerText = this.value" />
                            <span class="mh-range-badge"><span><?php echo esc_attr($delay); ?></span>ms</span>
                        </div>
                    </div>

                    <div class="mh-field-group">
                        <label><?php esc_html_e('Dissolve Speed (Fade ms)', 'mh-plug-ecommerce-builder-widgets'); ?></label>
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
                <i class="fas fa-save"></i> <?php esc_html_e('Initialize Settings', 'mh-plug-ecommerce-builder-widgets'); ?>
            </button>
        </div>
    </form>
</div>
