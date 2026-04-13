<?php
// Command: Exit if accessed directly.
// This is a standard security measure in WordPress to prevent the file from being accessed directly via a URL.
if (!defined('ABSPATH')) {
    exit;
}

// Command: Define the final class MH_Elementor_Loader.
// The 'final' keyword means this class cannot be extended by another class.
final class MH_Elementor_Loader {

    // Command: Create a private static property to hold the single instance of this class.
    // This is part of a "Singleton" design pattern, which ensures that this class is only loaded once.
    private static $_instance = null;

    /**
     * Command: Create a public static method to get the single instance of the class.
     * This is the only way to access the class. If an instance doesn't exist, it creates one.
     * If it already exists, it returns the existing one.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Command: Make the constructor private to prevent creating new instances directly.
     * The constructor runs when the class is first instantiated. It hooks our main 'init' method
     * into WordPress's 'plugins_loaded' action, which is a safe point to start interacting with other plugins like Elementor.
     */
    private function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Command: Initialize all Elementor-related hooks.
     * This method is the central point for all our Elementor integrations.
     */
    public function init() {
        // Command: Check if Elementor is loaded and active.
        // If not, our plugin does nothing to avoid causing errors.
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Command: Add our custom functions to specific Elementor action hooks.
        // This tells Elementor when to run our code.
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'print_inline_editor_styles']);
    }

    /**
     * Command: Register our custom widget category in the Elementor editor.
     * This creates the "MH Plug" section in the Elementor widget panel.
     */
    public function register_widget_category($elements_manager) {
        $elements_manager->add_category(
            'mh-plug-widgets', // Unique ID for the category
            [
                'title' => esc_html__('MH Plug', 'mh-plug'), // Display name of the category
                'icon' => 'eicon-plug', // Icon for the category
            ]
        );
    }

    /**
     * Command: Print inline CSS into the Elementor editor's <head> tag.
     * This is used to add the custom "MH" badge to our widgets for branding.
     * We use this method because it's highly reliable and avoids caching issues.
     */
    public function print_inline_editor_styles() {
        ?>
        <style id="mh-plug-editor-badge-styles">
            /*
             * This CSS selector targets the widget wrappers inside our specific category panel.
             * This makes the rule very specific and ensures it only applies to our widgets.
            */
           
            .elementor-element-wrapper [class^="mhi-"], .elementor-element-wrapper [class*=" mhi-"] {
                position: relative !important; /* Make the container a positioning reference */
            }

            /*
             * This creates the "MH" badge using a ::after pseudo-element.
             * It's positioned absolutely within the wrapper.
            */
            .elementor-element-wrapper [class^="mhi-"]::after, .elementor-element-wrapper [class*=" mhi-"]::after {
                content: 'MH';
                position: absolute;
                top: -10px;
                right: -45px;
                z-index: 10;
                background-color: #2293e9ff; /* The background color of the badge */
                color: #ffffff;
                padding: 2px 6px;
                font-size: 10px;
                line-height: 1;
                font-weight: 600;
                border-radius: 4px;
                text-transform: uppercase;
                box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }

        </style>
        <?php
    }

    /**
     * Command: Register our custom widgets with Elementor.
     * This function checks which widgets are enabled in the admin dashboard and loads them.
     */
    public function register_widgets($widgets_manager) {
        // Command: Get the saved on/off settings from the WordPress options table.
        $widget_options = get_option('mh_plug_widgets_settings', []);

        // Command: Create a map of our widgets, linking the setting ID to the file and class name.
        $widget_map = [
            'mh_heading'      => ['file' => 'mh-heading-widget.php', 'class' => 'MH_Heading_Widget'],
            'mh_site_logo'    => ['file' => 'mh-site-logo-widget.php', 'class' => 'MH_Site_Logo_Widget'], // <-- ADD THIS LINE
            'mh_site_title'   => ['file' => 'mh-site-title-widget.php', 'class' => 'MH_Site_Title_Widget'], // <-- ADD THIS LINE
            'mh_brush_text'   => ['file' => 'mh-brush-text-widget.php', 'class' => 'MH_Brush_Text_Widget'], // <-- ADD THIS LINE
            // --- ADD THIS LINE ---
            'mh_brush_slider' => ['file' => 'mh-brush-slider-widget.php', 'class' => 'MH_Brush_Slider_Widget'],
            'mh_image_circle' => ['file' => 'mh-image-circle-widget.php', 'class' => 'MH_Image_Circle_Widget'],
            'mh_image_circle_slider' => ['file' => 'mh-image-circle-slider-widget.php', 'class' => 'MH_Image_Circle_Slider_Widget'],
            'mh_feature_card' => ['file' => 'mh-feature-card-widget.php', 'class' => 'MH_Feature_Card_Widget'],
            'mh_post_carousel' => ['file' => 'mh-post-carousel-widget.php', 'class' => 'MH_Post_Carousel_Widget'],
            'mh_synced_slider' => ['file'  => 'mh-synced-slider-widget.php','class' => 'MH_Synced_Slider_Widget'],
            'mh_button' => ['file'  => 'mh-button-widget.php','class' => 'MH_Button_Widget'],
            'mh_stacked_carousel' => [ 'file' => 'mh-stacked-carousel-widget.php', 'class' => 'MH_Stacked_Carousel_Widget' ],
        ];

        // Command: Loop through each widget in our map.
        foreach ($widget_map as $option_key => $widget_data) {
            
            // Command: Check if the widget's switch is turned on in the dashboard settings.
            // If the setting doesn't exist yet, it defaults to 'true' (on).
            $is_enabled = isset($widget_options[$option_key]) ? (bool)$widget_options[$option_key] : true;
            
            // Command: If the widget is enabled, then proceed to load it.
            if ($is_enabled) {
                $file_path = MH_PLUG_PATH . 'elementor/widgets/' . $widget_data['file'];
                
                // Command: Check if the widget file actually exists before trying to include it.
                if (is_readable($file_path)) {
                    require_once $file_path; // Include the widget's PHP file.
                    
                    // Command: Check if the widget's class exists after including the file.
                    if (class_exists($widget_data['class'])) {
                        // Command: Register the widget with Elementor.
                        $widgets_manager->register(new $widget_data['class']());
                    }
                }
            }
        }
    }

    
}

// Command: Start the entire process by calling the instance method.
MH_Elementor_Loader::instance();


/**
 * Enqueue Custom Icon Font for Elementor Editor.
 */
function mh_plug_enqueue_editor_icons() {
    wp_enqueue_style(
        'mhi-icons', // Handle name
        MH_PLUG_URL . './elementor/assets/css/style.css', // Correct path
        [],
        MH_PLUG_VERSION
    );
    wp_enqueue_style(
        'style', // Handle name
        MH_PLUG_URL . './elementor/assets/css/widget-style.css', // Correct path
        [],
        MH_PLUG_VERSION
    );

    // --- NEW: Enqueue the brush color filter script ---
    wp_enqueue_script(
        'mh-brush-color-filter-script',
        MH_PLUG_URL . 'elementor/assets/js/brush-color-filter.js',
        ['jquery', 'elementor-frontend'], // Ensure Elementor frontend is loaded
        MH_PLUG_VERSION,
        true // In footer
    );
    
    wp_enqueue_script('slick-js', MH_PLUG_URL . 'assets/slick/slick.min.js', ['jquery'], MH_PLUG_VERSION, true);
    // --- END NEW BLOCK ---
}
add_action( 'elementor/editor/before_enqueue_scripts', 'mh_plug_enqueue_editor_icons' );
// This action is important for frontend as well, if we want this script on the front.
// For editor only: add_action( 'elementor/editor/after_enqueue_scripts', 'mh_plug_enqueue_editor_icons' );
// For both:
add_action( 'elementor/frontend/after_register_scripts', 'mh_plug_enqueue_editor_icons' ); // Enqueue on frontend for runtime effects
