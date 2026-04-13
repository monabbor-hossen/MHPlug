<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MH_Admin_Menu Class
 * Final Hybrid Version: Injects menu styles and enqueues page styles.
 */

class MH_Admin_Menu {

    // --- CHANGE THIS PROPERTY ---
    // Use an associative array for better management.
    // Key => Internal Name, Value => Display Name
    private $widgets = [
        'mh_heading'        => 'Advanced Heading',
        'mh_site_logo'      => 'Site Logo', 
        'mh_site_title'     => 'Site Title',
        
        // Add Global Settings here for now
        'enable_menu_icons' => 'Menu Icons',
        
        'mh_brush_text'     => 'Brush Text',
        
        'mh_brush_slider'   => 'Brush Slider',
        'mh_image_circle'   => 'Image Circle',
        'mh_image_circle_slider' => 'Image Circle Slider',
        'mh_feature_card'   => 'Feature Card',
        'mh_post_carousel'  => 'Post',
        'mh_synced_slider' => 'Synced Slider',
        'mh_button' => 'Button',
        'mh_stacked_carousel' =>'Stacked Carousel',
    ];

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Hook to inject critical menu styles on ALL admin pages.
        add_action('admin_head', [$this, 'add_menu_inline_styles']);

        // Hook to load page-specific assets (CSS for accordion, JS).
        add_action('admin_enqueue_scripts', [$this, 'enqueue_page_assets']);
    }

    public function register_menu() {
        add_menu_page(
            esc_html__('MH Plug Settings', 'mh-plug'),
            'MH Plug',
            'manage_options',
            'mh-plug-settings',
            [$this, 'render_settings_page'],
            'dashicons-admin-generic', // Placeholder
            58
        );
    }

    /**
     * Injects ONLY the menu icon and background styles into the <head>.
     */
    public function add_menu_inline_styles() {
        ?>
        <style id="mh-plug-menu-styles">
        /* Default (Inactive) State Icon */
        #adminmenu #toplevel_page_mh-plug-settings .wp-menu-image {
            background-image: url('<?php echo esc_url(MH_PLUG_URL . 'admin/assets/images/MH-icon.png'); ?>') !important;
            background-repeat: no-repeat !important;
            background-position: center center !important;
            background-size: 20px auto !important;
        }

        /* Hide placeholder Dashicon */
        #adminmenu #toplevel_page_mh-plug-settings .wp-menu-image::before {
            content: '' !important;
        }

        /* Active & Hover State Background */
        #adminmenu li#toplevel_page_mh-plug-settings:hover a,
        #adminmenu li.current#toplevel_page_mh-plug-settings a,
        #adminmenu li.wp-has-current-submenu#toplevel_page_mh-plug-settings a {
            background: #004265 !important;
            color: #fff !important;
        }
        </style>
<?php
    }
/**
     * Enqueues assets for the plugin's settings page OR the Nav Menus page.
     * CORRECTED LOGIC
     */
    public function enqueue_page_assets($hook) {

        // Check if we are on the main plugin settings page
        if ('toplevel_page_mh-plug-settings' === $hook) {
            // Enqueue the external stylesheet for accordion and widget cards.
            $css_path = MH_PLUG_PATH . 'admin/assets/css/admin-styles.css';
            if (file_exists($css_path)) {
                $css_version = filemtime($css_path);
                wp_enqueue_style('mh-plug-admin-styles', MH_PLUG_URL . 'admin/assets/css/admin-styles.css', [], $css_version);
            }

            // Enqueue the JavaScript for the accordion.
            $js_path = MH_PLUG_PATH . 'admin/assets/js/admin-scripts.js';
             if (file_exists($js_path)) {
                $js_version = filemtime($js_path);
                wp_enqueue_script('mh-plug-admin-scripts', MH_PLUG_URL . 'admin/assets/js/admin-scripts.js', ['jquery'], $js_version, true);
             }
        }
        // Check if we are on the Nav Menus admin page
        elseif ( 'nav-menus.php' === $hook ) { // Use elseif here
             // Enqueue CSS for the icon picker button and modal styles
            $picker_css_path = MH_PLUG_PATH . 'admin/assets/css/menu-icon-picker.css';
             if (file_exists($picker_css_path)) {
                $picker_css_version = filemtime($picker_css_path);
                wp_enqueue_style('mh-plug-menu-icon-picker-styles', MH_PLUG_URL . 'admin/assets/css/menu-icon-picker.css', [], $picker_css_version);
             }

             // Enqueue JS for the icon picker functionality
            $picker_js_path = MH_PLUG_PATH . 'admin/assets/js/menu-icon-picker.js';
             if (file_exists($picker_js_path)) {
                 $picker_js_version = filemtime($picker_js_path);
                wp_enqueue_script('mh-plug-menu-icon-picker-script', MH_PLUG_URL . 'admin/assets/js/menu-icon-picker.js', ['jquery'], $picker_js_version, true);
             }

            // Enqueue your actual icon font CSS here too!
            $icon_font_css_path = MH_PLUG_PATH . 'elementor/assets/css/style.css'; // Adjust path if needed
            if (file_exists($icon_font_css_path)) {
                $icon_font_css_version = filemtime($icon_font_css_path);
                wp_enqueue_style('mh-icons-for-picker', MH_PLUG_URL . 'elementor/assets/css/style.css', [], $icon_font_css_version);
            }

            // --- MODIFIED BLOCK ---
            // Enqueue your local Font Awesome 7.1.0 CSS
            $fa_css_path = MH_PLUG_PATH . 'assets/fontawesome-7/css/all.min.css';
            if (file_exists($fa_css_path)) {
                wp_enqueue_style('mh-fontawesome-7', MH_PLUG_URL . 'assets/fontawesome-7/css/all.min.css', [], '7.1.0'); // Use the FA version
            }
            // --- END MODIFIED BLOCK ---
        }
    }
    
    // --- The rest of the functions are unchanged ---
    public function render_settings_page() { require_once MH_PLUG_PATH . 'admin/settings-page.php'; }
    public function register_settings() {
        register_setting(
            'mh_plug_settings_group',
            'mh_plug_widgets_settings',
            [$this, 'sanitize_widgets_settings'] // <-- Add this sanitize callback
        );
        add_settings_section('mh_plug_widgets_section', null, null, 'mh-plug-settings-page');
// --- ADD THIS NEW SECTION ---
        add_settings_section(
            'mh_plug_global_settings_section', // New unique ID for global settings
            null, 
            null, 
            'mh-plug-settings-page'
        );
// Loop through all registered settings
        foreach ($this->widgets as $key => $label) {
            
            // --- ADD THIS LOGIC ---
            // Decide which section this setting belongs to
            $section_id = 'mh_plug_widgets_section'; // Default to widgets section
            if ($key === 'enable_menu_icons') {
                $section_id = 'mh_plug_global_settings_section'; // Assign this one to the global section
            }
            // --- END LOGIC ---

            add_settings_field(
                $key, 
                $label, 
                [$this, 'render_widget_toggle_field'],
                'mh-plug-settings-page', 
                $section_id, // <-- USE THE DYNAMIC SECTION ID
                ['id' => $key, 'label' => $label]
            );
        }
    }
   
    /**
     * Render the HTML for the 3D toggle switch.
     * @param array $args Arguments passed from add_settings_field().
     */
    public function render_widget_toggle_field($args) {
        $options = get_option('mh_plug_widgets_settings');
        $id = esc_attr($args['id']);
        // Check if the option is saved and set to 1 (checked). Default is true (checked) if not set.
        $is_checked = isset($options[$id]) ? (bool)$options[$id] : true;
        $checked_attr = $is_checked ? ' checked' : '';

        // Get the current value (1 or 0)
    $current_value = $is_checked ? '1' : '0';
// --- CHANGE #1: Get the 'disabled' string we passed from settings-page.php ---
    $disabled_string = isset($args['disabled']) ? $args['disabled'] : '';
        // Output the new HTML structure for the 3D switch
        echo "<div class='mh-widget-card'>";
        echo "  <div class='mh-widget-card-header'>";
        echo "      <div class='mh-widget-title'>" . esc_html($args['label']) . "</div>";
        // Start of new switch HTML
        echo "      <label class='switch'>";
        echo "          <input class='cb' type='checkbox' name='mh_plug_widgets_settings[{$id}]' value='1' {$checked_attr} {$disabled_string}/>";
        echo "          <span class='toggle'>";
        echo "              <span class='left'>off</span>";
        echo "              <span class='right'>on</span>";
        echo "          </span>";
        echo "      </label>";
        // End of new switch HTML
        echo "  </div>";
        // --- THIS IS THE "MEMORY" ---
    // If the switch is disabled, a disabled input's value is NOT sent.
    // So, we add a HIDDEN input with the SAME name and the REAL value.
    // This hidden input WILL be sent, preserving the setting.
    if ( $disabled_string !== '' ) {
        echo "<input type='hidden' name='mh_plug_widgets_settings[{$id}]' value='{$current_value}' />";
    }
    // --- END OF "MEMORY" ---
        echo "</div>";
    }

    /**
     * --- ADD THIS ENTIRE NEW FUNCTION ---
     * Sanitize Callback for Widget Settings.
     * This function ensures that unchecked boxes are saved as '0' (off).
     * @param array $input The raw data submitted from the form.
     * @return array The cleaned data to be saved.
     */
    public function sanitize_widgets_settings($input) {
        // --- MODIFY THIS FUNCTION ---
        $sanitized_data = [];

        // Get all the valid keys from our new 'widgets' property.
        $widget_keys = array_keys($this->widgets);

        foreach ($widget_keys as $widget_key) {
            if (isset($input[$widget_key]) && $input[$widget_key] == '1') {
                $sanitized_data[$widget_key] = 1;
            } else {
                $sanitized_data[$widget_key] = 0;
            }
        }
        return $sanitized_data;
    }
}

new MH_Admin_Menu();