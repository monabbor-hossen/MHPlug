<?php
if (!defined('ABSPATH')) exit;

final class MH_Elementor_Loader {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) { self::$_instance = new self(); }
        return self::$_instance;
    }

    private function __construct() {
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'print_inline_editor_styles']);

        add_action('wp_enqueue_scripts', [$this, 'mh_plug_register_widget_assets']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'mh_plug_register_widget_assets']);
        add_action('wp_enqueue_scripts', [$this, 'mh_plug_enqueue_woo_scripts']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'mh_plug_enqueue_woo_scripts']);

        add_action('wp_ajax_mh_get_compare_table', [$this, 'get_compare_table_ajax']);
        add_action('wp_ajax_nopriv_mh_get_compare_table', [$this, 'get_compare_table_ajax']);
        add_action('wp_ajax_mh_quick_view', [$this, 'quick_view_ajax']);
        add_action('wp_ajax_nopriv_mh_quick_view', [$this, 'quick_view_ajax']);

        // 🚀 Render Preloader globally
        add_action('wp_head', [$this, 'render_preloader_css']);
        add_action('wp_footer', [$this, 'render_preloader_html_js']);
    }

    // 🚀 Output Global CSS with Full Gradient Support
    public function render_preloader_css() {
        if (is_admin()) return;
        if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) return; 

        $settings = get_option('mh_plug_preloader_settings', []);
        if (empty($settings['enable']) || $settings['enable'] !== 'yes') return;

        // Fetch Background
        $bg_type = isset($settings['bg_type']) ? $settings['bg_type'] : 'solid';
        $bg_c1   = isset($settings['bg_c1']) ? $settings['bg_c1'] : '#0f172a';
        $bg_c2   = isset($settings['bg_c2']) ? $settings['bg_c2'] : '#1e293b';
        $bg_ang  = isset($settings['bg_angle']) ? $settings['bg_angle'] : '90';
        $global_bg = ($bg_type === 'gradient') ? "linear-gradient({$bg_ang}deg, {$bg_c1}, {$bg_c2})" : $bg_c1;

        $transition = !empty($settings['transition']) ? intval($settings['transition']) : 500;

        echo '<style>
            #mh-global-preloader { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: ' . esc_attr($global_bg) . '; z-index: 99999999; display: flex; align-items: center; justify-content: center; transition: opacity ' . esc_attr($transition) . 'ms ease, visibility ' . esc_attr($transition) . 'ms ease; }
            #mh-global-preloader.mh-preloader-hidden { opacity: 0; visibility: hidden; }
            
            /* 🚀 TEXT ANIMATIONS */
            .mh-text-anim-blink { animation: mh-text-blink 1.5s infinite; }
            .mh-text-anim-pulse { animation: mh-text-pulse 2s infinite ease-in-out; }
            .mh-text-anim-float { animation: mh-text-float 2s infinite ease-in-out; }
            .mh-text-anim-tracking { animation: mh-text-tracking 2s infinite ease-in-out; }
            @keyframes mh-text-blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
            @keyframes mh-text-pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.7; } }
            @keyframes mh-text-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
            @keyframes mh-text-tracking { 0%, 100% { letter-spacing: 2px; } 50% { letter-spacing: 8px; } }

            /* CSS VISUAL ANIMATIONS */
            .mh-loader-1 { width: 50px; height: 50px; border: 5px solid rgba(255,255,255,0.1); border-top: 5px solid var(--mh-c1); border-radius: 50%; animation: mh-spin 1s linear infinite; }
            @keyframes mh-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            .mh-loader-2 { display: flex; gap: 8px; } .mh-loader-2 div { width: 16px; height: 16px; background: var(--mh-bg-style); border-radius: 50%; animation: mh-bounce 1.4s infinite ease-in-out both; } .mh-loader-2 div:nth-child(1) { animation-delay: -0.32s; } .mh-loader-2 div:nth-child(2) { animation-delay: -0.16s; }
            @keyframes mh-bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
            .mh-loader-3 { width: 50px; height: 50px; background: var(--mh-bg-style); border-radius: 50%; animation: mh-pulse 1.2s infinite cubic-bezier(0.2, 0.6, 0.2, 1); }
            @keyframes mh-pulse { 0% { transform: scale(0); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
            .mh-loader-4 { width: 40px; height: 40px; background: var(--mh-bg-style); animation: mh-flip 1.2s infinite ease-in-out; }
            @keyframes mh-flip { 0% { transform: perspective(120px) rotateX(0deg) rotateY(0deg); } 50% { transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg); } 100% { transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg); } }
            .mh-loader-5 { width: 50px; height: 50px; position: relative; } .mh-loader-5 div { width: 100%; height: 100%; border-radius: 50%; background: var(--mh-bg-style); opacity: 0.6; position: absolute; top: 0; left: 0; animation: mh-bounce2 2s infinite ease-in-out; } .mh-loader-5 div:nth-child(2) { animation-delay: -1.0s; }
            @keyframes mh-bounce2 { 0%, 100% { transform: scale(0); } 50% { transform: scale(1); } }
            .mh-loader-6 { display: flex; gap: 5px; height: 40px; align-items: center; } .mh-loader-6 div { width: 6px; height: 100%; background: var(--mh-bg-style); animation: mh-wave 1.2s infinite ease-in-out; } .mh-loader-6 div:nth-child(2) { animation-delay: -1.1s; } .mh-loader-6 div:nth-child(3) { animation-delay: -1.0s; } .mh-loader-6 div:nth-child(4) { animation-delay: -0.9s; } .mh-loader-6 div:nth-child(5) { animation-delay: -0.8s; }
            @keyframes mh-wave { 0%, 40%, 100% { transform: scaleY(0.4); } 20% { transform: scaleY(1); } }
            .mh-loader-7 { width: 50px; height: 50px; border-radius: 50%; background: conic-gradient(transparent 60%, var(--mh-c1)); animation: mh-spin 1s linear infinite; }
            .mh-loader-8 { width: 40px; height: 40px; background: var(--mh-bg-style); animation: mh-morph 2s infinite ease-in-out; }
            @keyframes mh-morph { 0% { border-radius: 0%; transform: rotate(0deg); } 50% { border-radius: 50%; transform: rotate(180deg); } 100% { border-radius: 0%; transform: rotate(360deg); } }
            .mh-loader-9 { width: 50px; height: 50px; position: relative; animation: mh-spin 2s linear infinite; } .mh-loader-9 div { position: absolute; width: 15px; height: 15px; background: var(--mh-bg-style); border-radius: 50%; top: 0; left: 50%; transform: translateX(-50%); } .mh-loader-9 div:nth-child(2) { top: auto; bottom: 0; }
            .mh-loader-10 { position: relative; width: 60px; height: 60px; } .mh-loader-10 div { position: absolute; border: 4px solid var(--mh-c1); opacity: 1; border-radius: 50%; animation: mh-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite; } .mh-loader-10 div:nth-child(2) { animation-delay: -0.5s; }
            @keyframes mh-ripple { 0% { top: 28px; left: 28px; width: 0; height: 0; opacity: 0; } 5% { opacity: 1; } 100% { top: -1px; left: -1px; width: 58px; height: 58px; opacity: 0; } }
            .mh-loader-11 { width: 50px; height: 50px; border: 3px dashed var(--mh-c1); border-radius: 50%; animation: mh-spin 4s linear infinite; display: flex; align-items: center; justify-content: center; } .mh-loader-11 div { width: 20px; height: 20px; background: var(--mh-bg-style); clip-path: polygon(50% 0%, 0% 100%, 100% 100%); animation: mh-spin-reverse 2s linear infinite; }
            @keyframes mh-spin-reverse { 0% { transform: rotate(360deg); } 100% { transform: rotate(0deg); } }
            .mh-loader-12 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; width: 50px; height: 50px; } .mh-loader-12 div { background: var(--mh-bg-style); border-radius: 50%; animation: mh-grid-pulse 1.2s infinite ease-in-out; } .mh-loader-12 div:nth-child(1), .mh-loader-12 div:nth-child(5), .mh-loader-12 div:nth-child(9) { animation-delay: 0.4s; } .mh-loader-12 div:nth-child(2), .mh-loader-12 div:nth-child(6), .mh-loader-12 div:nth-child(7) { animation-delay: 0.8s; } .mh-loader-12 div:nth-child(3), .mh-loader-12 div:nth-child(4), .mh-loader-12 div:nth-child(8) { animation-delay: 1.2s; }
            @keyframes mh-grid-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.3; transform: scale(0.5); } }
            .mh-loader-13 { position: relative; width: 60px; height: 20px; } .mh-loader-13 div { position: absolute; width: 20px; height: 20px; background: var(--mh-bg-style); border-radius: 50%; animation: mh-infinity 1.5s infinite ease-in-out; } .mh-loader-13 div:nth-child(2) { animation-delay: -0.75s; }
            @keyframes mh-infinity { 0% { left: 0; transform: scale(1); z-index: 1; } 25% { transform: scale(1.5); z-index: 2; } 50% { left: 40px; transform: scale(1); z-index: 1; } 75% { transform: scale(0.5); z-index: 0; } 100% { left: 0; transform: scale(1); z-index: 1; } }
            .mh-loader-14 { position: relative; width: 60px; height: 60px; } .mh-loader-14 div { position: absolute; width: 100%; height: 100%; border: 3px solid transparent; border-top-color: var(--mh-c1); border-radius: 50%; animation: mh-spin 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite; } .mh-loader-14 div:nth-child(2) { width: 80%; height: 80%; top: 10%; left: 10%; border-top-color: transparent; border-right-color: var(--mh-c1); animation-duration: 1.5s; animation-direction: reverse; } .mh-loader-14 div:nth-child(3) { width: 60%; height: 60%; top: 20%; left: 20%; border-top-color: transparent; border-bottom-color: var(--mh-c1); animation-duration: 1s; }
            .mh-loader-15 { width: 40px; height: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 4px; } .mh-loader-15 div { background: var(--mh-bg-style); animation: mh-cube-split 1.5s infinite ease-in-out; } .mh-loader-15 div:nth-child(1) { transform-origin: bottom right; } .mh-loader-15 div:nth-child(2) { transform-origin: bottom left; } .mh-loader-15 div:nth-child(3) { transform-origin: top right; } .mh-loader-15 div:nth-child(4) { transform-origin: top left; }
            @keyframes mh-cube-split { 0%, 100% { transform: scale(1); } 50% { transform: scale(0.5) rotate(90deg); border-radius: 50%; } }
            .mh-loader-16 { width: 20px; height: 20px; background: var(--mh-bg-style); border-radius: 50%; box-shadow: 0 0 20px 5px var(--mh-c1); animation: mh-glow-pulse 1s infinite alternate; }
            @keyframes mh-glow-pulse { 0% { box-shadow: 0 0 10px 2px var(--mh-c1); transform: scale(0.8); } 100% { box-shadow: 0 0 30px 10px var(--mh-c1); transform: scale(1.2); } }
            .mh-loader-17 { width: 50px; height: 50px; border-radius: 50%; border: 4px solid var(--mh-c1); border-color: var(--mh-c1) transparent; animation: mh-spin 1.2s linear infinite; position: relative; } .mh-loader-17 div { position: absolute; top: 4px; left: 4px; right: 4px; bottom: 4px; border-radius: 50%; border: 4px solid var(--mh-c1); border-color: transparent var(--mh-c1); animation: mh-spin-reverse 0.6s linear infinite; }
            .mh-loader-18 { display: flex; gap: 6px; height: 40px; align-items: flex-end; } .mh-loader-18 div { width: 8px; background: var(--mh-bg-style); animation: mh-stairway 1s infinite ease-in-out alternate; } .mh-loader-18 div:nth-child(1) { animation-delay: 0s; } .mh-loader-18 div:nth-child(2) { animation-delay: 0.2s; } .mh-loader-18 div:nth-child(3) { animation-delay: 0.4s; } .mh-loader-18 div:nth-child(4) { animation-delay: 0.6s; }
            @keyframes mh-stairway { 0% { height: 10px; } 100% { height: 40px; } }
            .mh-loader-19 { width: 50px; height: 50px; border-radius: 50%; box-shadow: inset 0 0 0 4px rgba(255,255,255,0.1); position: relative; } .mh-loader-19::after { content: ""; position: absolute; top: -4px; left: -4px; right: -4px; bottom: -4px; border-radius: 50%; border: 4px solid transparent; border-top-color: var(--mh-c1); animation: mh-spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite; }
            .mh-loader-20 { width: 50px; height: 50px; perspective: 150px; } .mh-loader-20 div { width: 100%; height: 100%; border: 6px solid var(--mh-c1); border-radius: 50%; animation: mh-hyper-ring 2s linear infinite; }
            @keyframes mh-hyper-ring { 0% { transform: rotateX(60deg) rotateZ(0deg); } 100% { transform: rotateX(60deg) rotateZ(360deg); } }

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
        </style>';
    }

    // 🚀 NEW: Output SVGs, Text, and Scale Wrappers
    public function render_preloader_html_js() {
        if (is_admin()) return;
        if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) return;

        $settings = get_option('mh_plug_preloader_settings', []);
        if (empty($settings['enable']) || $settings['enable'] !== 'yes') return;

        // Fetch User Vars
        $type        = isset($settings['type']) ? $settings['type'] : 'css';
        $css_effect  = isset($settings['css_effect']) ? (string)$settings['css_effect'] : '1';
        $effect_size = !empty($settings['effect_size']) ? $settings['effect_size'] : '1.0';
        
        $eff_type    = isset($settings['loader_type']) ? $settings['loader_type'] : 'solid';
        $eff_c1      = isset($settings['loader_c1']) ? $settings['loader_c1'] : '#2293e9';
        $eff_c2      = isset($settings['loader_c2']) ? $settings['loader_c2'] : '#00ffd5';
        $eff_angle   = isset($settings['loader_angle']) ? $settings['loader_angle'] : '90';
        $eff_bg      = ($eff_type === 'gradient') ? "linear-gradient({$eff_angle}deg, {$eff_c1}, {$eff_c2})" : $eff_c1;
        $svg_stroke  = ($eff_type === 'gradient') ? 'url(#mh-svg-gradient)' : $eff_c1;

        $image       = !empty($settings['image']) ? $settings['image'] : '';
        $img_width   = !empty($settings['img_width']) ? $settings['img_width'] : '150';
        $delay       = !empty($settings['delay']) ? intval($settings['delay']) : 500;

        $custom_text = isset($settings['custom_text']) ? $settings['custom_text'] : '';
        $text_type   = isset($settings['text_type']) ? $settings['text_type'] : 'solid';
        $text_c1     = !empty($settings['text_c1']) ? $settings['text_c1'] : '#2293e9';
        $text_c2     = !empty($settings['text_c2']) ? $settings['text_c2'] : '#00ffd5';
        $text_angle  = !empty($settings['text_angle']) ? $settings['text_angle'] : '90';
        $text_size   = !empty($settings['text_size']) ? $settings['text_size'] : '16';
        $text_anim   = !empty($settings['text_anim']) ? $settings['text_anim'] : 'pulse';
        $text_bg     = ($text_type === 'gradient') ? "linear-gradient({$text_angle}deg, {$text_c1}, {$text_c2})" : $text_c1;
        $text_style  = ($text_type === 'gradient') ? "background: {$text_bg}; -webkit-background-clip: text; -webkit-text-fill-color: transparent;" : "color: {$text_c1};";

        echo '<div id="mh-global-preloader">';
        
        // Inject SVG Gradient Defs
        if ($eff_type === 'gradient') {
            echo '<svg style="width:0;height:0;position:absolute;" aria-hidden="true" focusable="false"><linearGradient id="mh-svg-gradient" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="' . esc_attr($eff_c1) . '" /><stop offset="100%" stop-color="' . esc_attr($eff_c2) . '" /></linearGradient></svg>';
        }

        echo '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 25px; z-index: 5;">';

        // Render Visual Base
        if ($type === 'image' && !empty($image)) {
            echo '<img src="' . esc_url($image) . '" alt="Loading..." style="width:' . esc_attr($img_width) . 'px; height:auto;" />';
        } else {
            echo '<div style="transform: scale(' . esc_attr($effect_size) . '); --mh-c1: ' . esc_attr($eff_c1) . '; --mh-bg-style: ' . esc_attr($eff_bg) . '; --mh-svg-stroke: ' . esc_attr($svg_stroke) . ';">';
            switch ($css_effect) {
                case '1': echo '<div class="mh-loader-1"></div>'; break;
                case '2': echo '<div class="mh-loader-2"><div></div><div></div><div></div></div>'; break;
                case '3': echo '<div class="mh-loader-3"></div>'; break;
                case '4': echo '<div class="mh-loader-4"></div>'; break;
                case '5': echo '<div class="mh-loader-5"><div></div><div></div></div>'; break;
                case '6': echo '<div class="mh-loader-6"><div></div><div></div><div></div><div></div><div></div></div>'; break;
                case '7': echo '<div class="mh-loader-7"></div>'; break;
                case '8': echo '<div class="mh-loader-8"></div>'; break;
                case '9': echo '<div class="mh-loader-9"><div></div><div></div></div>'; break;
                case '10': echo '<div class="mh-loader-10"><div></div><div></div></div>'; break;
                case '11': echo '<div class="mh-loader-11"><div></div></div>'; break;
                case '12': echo '<div class="mh-loader-12"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'; break;
                case '13': echo '<div class="mh-loader-13"><div></div><div></div></div>'; break;
                case '14': echo '<div class="mh-loader-14"><div></div><div></div><div></div></div>'; break;
                case '15': echo '<div class="mh-loader-15"><div></div><div></div><div></div><div></div></div>'; break;
                case '16': echo '<div class="mh-loader-16"></div>'; break;
                case '17': echo '<div class="mh-loader-17"><div></div></div>'; break;
                case '18': echo '<div class="mh-loader-18"><div></div><div></div><div></div><div></div></div>'; break;
                case '19': echo '<div class="mh-loader-19"></div>'; break;
                case '20': echo '<div class="mh-loader-20"><div></div></div>'; break;
                case '21': echo '<div class="mh-loader-21 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg></div>'; break;
                case '22': echo '<div class="mh-loader-22 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></div>'; break;
                case '23': echo '<div class="mh-loader-23 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></div>'; break;
                case '24': echo '<div class="mh-loader-24 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></div>'; break;
                case '25': echo '<div class="mh-loader-25 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg></div>'; break;
                case '26': echo '<div class="mh-loader-26 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>'; break;
                case '27': echo '<div class="mh-loader-27 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 5v14M8 5v14M12 5v14M17 5v14M21 5v14"></path></svg><div class="mh-scanline"></div></div>'; break;
                case '28': echo '<div class="mh-loader-28 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8"></path><path d="M9 12h6"></path></svg></div>'; break;
                case '29': echo '<div class="mh-loader-29 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></div>'; break;
                case '30': echo '<div class="mh-loader-30 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>'; break;
                default:  echo '<div class="mh-loader-1"></div>'; break;
            }
            echo '</div>'; 
        }

        // Render Custom Loading Text
        if (!empty(trim($custom_text))) {
            echo '<div class="mh-text-anim-' . esc_attr($text_anim) . '" style="' . esc_attr($text_style) . ' font-size: ' . esc_attr($text_size) . 'px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; text-align: center;">' . esc_html($custom_text) . '</div>';
        }

        echo '</div>'; // End flex container
        echo '</div>'; // End global preloader

        echo '<script>
            window.addEventListener("load", function() {
                setTimeout(function() {
                    var preloader = document.getElementById("mh-global-preloader");
                    if (preloader) { preloader.classList.add("mh-preloader-hidden"); }
                }, ' . esc_js($delay) . ');
            });
        </script>';
    }

    public function quick_view_ajax() {
        if (!isset($_POST['product_id'])) { wp_send_json_error(['message' => 'No product ID provided.']); }
        $product_id = intval($_POST['product_id']);
        $template_id = !empty($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        global $post, $product;
        $post = get_post($product_id);
        $product = wc_get_product($product_id);
        setup_postdata($post);
        ob_start();
        if ($template_id && class_exists('\Elementor\Plugin')) {
            if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
                $css_file->enqueue();
            }
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id, true);
        } else {
            echo '<div style="padding:30px; text-align:center; font-family:sans-serif;">';
            echo $product->get_image('woocommerce_single', ['style' => 'max-width:300px; border-radius:10px; margin-bottom:20px;']);
            echo '<h2 style="margin:0 0 10px; color:#111;">' . $product->get_title() . '</h2>';
            echo '<div style="font-size:20px; color:#d63638; font-weight:bold; margin-bottom:20px;">' . $product->get_price_html() . '</div>';
            echo '<div>'; woocommerce_template_single_add_to_cart(); echo '</div></div>';
        }
        $html = ob_get_clean();
        wp_reset_postdata();
        wp_send_json_success(['html' => $html]);
    }

    public function get_compare_table_ajax() {
        if (!isset($_POST['product_ids']) || !is_array($_POST['product_ids'])) { wp_send_json_error(['html' => '<div class="mh-compare-empty"><h3>No products to compare</h3><p>Return to the shop to add products.</p></div>']); }
        $product_ids = array_map('intval', $_POST['product_ids']);
        $products = [];
        $all_attributes = [];
        foreach ($product_ids as $id) {
            $prod = wc_get_product($id);
            if ($prod) {
                $products[] = $prod;
                foreach ($prod->get_attributes() as $attr_name => $attr) {
                    $label = $attr->is_taxonomy() ? wc_attribute_label($attr_name) : $attr->get_name();
                    $all_attributes[$attr_name] = $label;
                }
            }
        }
        if (empty($products)) wp_send_json_error(['html' => '<p>Products not found.</p>']);
        ob_start();
        ?>
        <table class="mh-compare-table">
            <tbody>
                <tr>
                    <th>Product Details</th>
                    <?php foreach($products as $prod_obj): 
                        global $product, $post;
                        $product = $prod_obj;
                        $post = get_post($prod_obj->get_id());
                        setup_postdata($post);
                    ?>
                        <td class="mh-compare-item">
                            <div class="mh-compare-image">
                                <a href="#" class="mh-remove-compare" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="Remove"><i class="fas fa-times"></i></a>
                                <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                            </div>
                            <h3 class="mh-compare-title"><a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a></h3>
                            <div class="mh-compare-price"><?php echo $product->get_price_html(); ?></div>
                            <div class="mh-compare-add-to-cart"><?php woocommerce_template_loop_add_to_cart(); ?></div>
                        </td>
                    <?php endforeach; wp_reset_postdata(); ?>
                </tr>
                <tr>
                    <th>Description</th>
                    <?php foreach($products as $product): ?><td><?php echo wp_trim_words($product->get_short_description(), 15, '...'); ?></td><?php endforeach; ?>
                </tr>
                <tr>
                    <th>Rating</th>
                    <?php foreach($products as $product): ?><td><?php echo wc_get_rating_html($product->get_average_rating()); ?></td><?php endforeach; ?>
                </tr>
                <tr>
                    <th>Availability</th>
                    <?php foreach($products as $product): ?><td><?php echo wc_get_stock_html($product); ?></td><?php endforeach; ?>
                </tr>
                <?php foreach($all_attributes as $attr_key => $attr_label): ?>
                    <tr>
                        <th><?php echo esc_html($attr_label); ?></th>
                        <?php foreach($products as $product): ?>
                            <td><?php $attr_val = $product->get_attribute($attr_key); echo !empty($attr_val) ? wp_kses_post($attr_val) : '-'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        wp_send_json_success(['html' => ob_get_clean()]);
    }

    public function register_widget_category($elements_manager) { $elements_manager->add_category('mh-plug-widgets', ['title' => esc_html__('MH Plug', 'mh-plug'), 'icon' => 'eicon-plug']); }
    public function print_inline_editor_styles() { echo '<style id="mh-plug-editor-badge-styles"> .elementor-element-wrapper [class^="mhi-"] { position: relative !important; } .elementor-element-wrapper [class^="mhi-"]::after { content: "MH"; position: absolute; top: -10px; right: -45px; z-index: 10; background-color: #2293e9ff; color: #ffffff; padding: 2px 6px; font-size: 10px; line-height: 1; font-weight: 600; border-radius: 4px; text-transform: uppercase; box-shadow: 0 1px 2px rgba(0,0,0,0.2); } </style>'; }
    public function register_widgets($widgets_manager) {
        $widget_options = get_option('mh_plug_widgets_settings', []);
        $widget_map = [
            'mh_heading' => ['file' => 'mh-heading-widget.php', 'class' => 'MH_Heading_Widget'],
            'mh_site_logo' => ['file' => 'mh-site-logo-widget.php', 'class' => 'MH_Site_Logo_Widget'],
            'mh_site_title' => ['file' => 'mh-site-title-widget.php', 'class' => 'MH_Site_Title_Widget'],
            'mh_brush_text' => ['file' => 'mh-brush-text-widget.php', 'class' => 'MH_Brush_Text_Widget'],
            'mh_brush_slider' => ['file' => 'mh-brush-slider-widget.php', 'class' => 'MH_Brush_Slider_Widget'],
            'mh_image_circle' => ['file' => 'mh-image-circle-widget.php', 'class' => 'MH_Image_Circle_Widget'],
            'mh_image_circle_slider' => ['file' => 'mh-image-circle-slider-widget.php', 'class' => 'MH_Image_Circle_Slider_Widget'],
            'mh_feature_card' => ['file' => 'mh-feature-card-widget.php', 'class' => 'MH_Feature_Card_Widget'],
            'mh_post_carousel' => ['file' => 'mh-post-carousel-widget.php', 'class' => 'MH_Post_Carousel_Widget'],
            'mh_synced_slider' => ['file' => 'mh-synced-slider-widget.php', 'class' => 'MH_Synced_Slider_Widget'],
            'mh_button' => ['file' => 'mh-button-widget.php', 'class' => 'MH_Button_Widget'],
            'mh_stacked_carousel' => ['file' => 'mh-stacked-carousel-widget.php', 'class' => 'MH_Stacked_Carousel_Widget'],
            'mh_wishlist_button' => ['file' => 'mh-wishlist-button-widget.php', 'class' => 'MH_Wishlist_Button_Widget'],
            'mh_wishlist_table' => ['file' => 'mh-wishlist-table-widget.php', 'class' => 'MH_Wishlist_Table_Widget'],
            'mh_nav_menu' => ['file' => 'mh-nav-menu-widget.php', 'class' => 'MH_Nav_Menu_Widget'],
            'mh_copyright' => ['file' => 'mh-copyright-widget.php', 'class' => 'MH_Copyright_Widget'],
            'mh_taxonomy_card' => ['file' => 'mh-taxonomy-card-widget.php', 'class' => 'MH_Plug_Taxonomy_Card_Widget'],
            'mh_breadcrumb' => [ 'file' => 'mh-breadcrumb-widget.php', 'class' => 'MH_Breadcrumb_Widget' ],
        ];
        if ( class_exists( 'WooCommerce' ) ) {
            $wc_widget_map = [
                'mh_woo_add_to_cart' => [ 'file' => 'mh-woo-add-to-cart-widget.php', 'class' => 'MH_Woo_Add_To_Cart_Widget' ],
                'mh_woo_attributes' => [ 'file' => 'mh-woo-attributes-widget.php', 'class' => 'MH_Woo_Attributes_Widget' ],
                'mh_product_search' => [ 'file' => 'mh-product-search-widget.php', 'class' => 'MH_Plug_Product_Search_Widget' ],
                'mh_product_title' => [ 'file' => 'mh-product-title-widget.php', 'class' => 'MH_Product_Title_Widget' ],
                'mh_product_price' => [ 'file' => 'mh-product-price-widget.php', 'class' => 'MH_Product_Price_Widget' ],
                'mh_product_short_description' => [ 'file' => 'mh-product-short-description-widget.php', 'class' => 'MH_Product_Short_Description_Widget' ],
                'mh_product_category' => [ 'file' => 'mh-product-category-widget.php', 'class' => 'MH_Product_Category_Widget' ],
                'mh_product_tags' => [ 'file' => 'mh-product-tags-widget.php', 'class' => 'MH_Product_Tags_Widget' ],
                'mh_product_brands' => [ 'file' => 'mh-product-brands-widget.php', 'class' => 'MH_Product_Brands_Widget' ],
                'mh_product_rating' => [ 'file' => 'mh-product-rating-widget.php', 'class' => 'MH_Product_Rating_Widget' ],
                'mh_product_gallery' => [ 'file' => 'mh-product-gallery-widget.php', 'class' => 'MH_Product_Gallery_Widget' ],
                'mh_product_share' => [ 'file' => 'mh-product-share-widget.php', 'class' => 'MH_Product_Share_Widget' ],
                'mh_product_data_accordion' => [ 'file' => 'mh-product-data-accordion-widget.php', 'class' => 'MH_Product_Data_Accordion_Widget' ],
                'mh_header_wishlist' => [ 'file' => 'mh-header-wishlist-widget.php', 'class' => 'MH_Header_Wishlist_Widget' ],
                'mh_header_cart' => [ 'file' => 'mh-header-cart-widget.php', 'class' => 'MH_Header_Cart_Widget' ],
                'mh_product_grid' => [ 'file' => 'mh-product-grid-widget.php', 'class' => 'MH_Product_Grid_Widget' ],
                'mh_header_compare' => [ 'file' => 'mh-header-compare-widget.php', 'class' => 'MH_Header_Compare_Widget' ],
                'mh_product_compare_btn' => [ 'file' => 'mh-product-compare-btn-widget.php', 'class' => 'MH_Product_Compare_Btn_Widget' ],
                'mh_compare_table' => [ 'file' => 'mh-compare-table-widget.php', 'class' => 'MH_Compare_Table_Widget' ],
            ];
            $widget_map = array_merge( $widget_map, $wc_widget_map );
        }
        foreach ($widget_map as $option_key => $widget_data) {
            $is_enabled = isset($widget_options[$option_key]) ? (bool)$widget_options[$option_key] : true;
            if ($is_enabled) {
                $file_path = MH_PLUG_PATH . 'elementor/widgets/' . $widget_data['file'];
                if (file_exists($file_path)) {
                    require_once $file_path;
                    $class_name = '\\' . ltrim($widget_data['class'], '\\');
                    if (class_exists($class_name)) { $widgets_manager->register(new $class_name()); }
                }
            }
        }
    }
    public function mh_plug_register_widget_assets() {
        wp_register_style('mh-widgets-css', MH_PLUG_URL . 'elementor/assets/css/mh-widgets.css', [], MH_PLUG_VERSION);
        wp_register_script('mh-widgets-js', MH_PLUG_URL . 'elementor/assets/js/mh-widgets.js', ['jquery'], MH_PLUG_VERSION, true);
        wp_register_style('mh-nav-menu-css', MH_PLUG_URL . 'elementor/assets/css/mh-nav-menu.css', [], MH_PLUG_VERSION);
        wp_register_script('mh-nav-menu-js', MH_PLUG_URL . 'elementor/assets/js/mh-nav-menu.js', ['jquery'], MH_PLUG_VERSION, true);
    }
    public function mh_plug_enqueue_woo_scripts() {
        if (!class_exists('WooCommerce')) return;
        wp_register_script('mh-woo-scripts', MH_PLUG_URL . 'elementor/assets/js/mh-woo-scripts.js', ['jquery'], MH_PLUG_VERSION, true);
        wp_register_script('mh-product-gallery-script', MH_PLUG_URL . 'elementor/assets/js/mh-product-gallery.js', ['jquery', 'mh-slick-js'], MH_PLUG_VERSION, true);
        wp_script_add_data('mh-widgets-js', 'group', 1);
        wp_enqueue_script('mh-woo-scripts');
        if (is_product()) wp_enqueue_script('mh-product-gallery-script');
        $ajax_data = [ 'ajax_url' => admin_url('admin-ajax.php'), 'login_url' => wc_get_page_permalink('myaccount'), 'wishlist_nonce' => wp_create_nonce('mh_wishlist_nonce') ];
        wp_add_inline_script('mh-woo-scripts', 'var mh_plug_ajax = ' . wp_json_encode($ajax_data) . ';', 'before');
    }
}
MH_Elementor_Loader::instance();

function mh_plug_enqueue_editor_icons() {
    wp_enqueue_style('mhi-icons', MH_PLUG_URL . 'elementor/assets/css/style.css', [], MH_PLUG_VERSION);
    wp_enqueue_style('style', MH_PLUG_URL . 'elementor/assets/css/widget-style.css', [], MH_PLUG_VERSION);
    wp_enqueue_script('mh-brush-color-filter-script', MH_PLUG_URL . 'elementor/assets/js/brush-color-filter.js', ['jquery'], MH_PLUG_VERSION, true);
    wp_enqueue_script('slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
}
add_action('elementor/editor/before_enqueue_scripts', 'mh_plug_enqueue_editor_icons');
add_action('elementor/frontend/after_register_scripts', 'mh_plug_enqueue_editor_icons');