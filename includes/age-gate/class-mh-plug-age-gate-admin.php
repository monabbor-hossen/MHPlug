<?php
/**
 * Age Gate Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class MH_Plug_Age_Gate_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'mh-age-gate' ) === false ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_add_inline_script( 'wp-color-picker', 'jQuery(document).ready(function($){$(".mh-color-picker").wpColorPicker();});' );

        $css_path = MH_PLUG_PATH . 'admin/assets/css/admin-styles.css';
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style( 'mh-plug-admin-styles', MH_PLUG_URL . 'admin/assets/css/admin-styles.css', array(), filemtime( $css_path ) );
        }

        $js_path = MH_PLUG_PATH . 'admin/assets/js/admin-scripts.js';
        if ( file_exists( $js_path ) ) {
            wp_enqueue_script( 'mh-plug-admin-scripts', MH_PLUG_URL . 'admin/assets/js/admin-scripts.js', array( 'jquery' ), filemtime( $js_path ), true );
        }
    }

    public function add_plugin_page() {
        add_submenu_page(
            'mh-plug-settings',
            __( 'MH Age Gate', 'mh-plug' ),
            __( 'Age Gate', 'mh-plug' ),
            'manage_options',
            'mh-age-gate',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page() {
        global $wp_settings_fields;
        ?>
        <div class="wrap mh-plug-admin-wrap">
            <h1><?php esc_html_e( 'MH Age Gate Settings', 'mh-plug' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'mh_age_gate_option_group' ); ?>
                <div class="mh-accordion">
                    
                    <!-- General Settings -->
                    <div class="mh-accordion-item">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php esc_html_e( 'General Settings', 'mh-plug' ); ?></span>
                            <span class="mh-header-controls">
                                <span class="mh-accordion-icon">+</span>
                            </span>
                        </div>
                        <div class="mh-accordion-content">
                            <div class="mh-settings-grid">
                                <?php
                                if ( isset( $wp_settings_fields['mh-age-gate-admin']['mh_age_gate_general_section'] ) ) {
                                    foreach ( (array) $wp_settings_fields['mh-age-gate-admin']['mh_age_gate_general_section'] as $field ) {
                                        call_user_func( $field['callback'], $field['args'] );
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Content Settings -->
                    <div class="mh-accordion-item">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php esc_html_e( 'Content Settings', 'mh-plug' ); ?></span>
                            <span class="mh-header-controls">
                                <span class="mh-accordion-icon">+</span>
                            </span>
                        </div>
                        <div class="mh-accordion-content">
                            <div class="mh-settings-grid">
                                <?php
                                if ( isset( $wp_settings_fields['mh-age-gate-admin']['mh_age_gate_content_section'] ) ) {
                                    foreach ( (array) $wp_settings_fields['mh-age-gate-admin']['mh_age_gate_content_section'] as $field ) {
                                        call_user_func( $field['callback'], $field['args'] );
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Settings -->
                    <div class="mh-accordion-item">
                        <div class="mh-accordion-header">
                            <span class="mh-accordion-title"><?php esc_html_e( 'Appearance Settings', 'mh-plug' ); ?></span>
                            <span class="mh-header-controls">
                                <span class="mh-accordion-icon">+</span>
                            </span>
                        </div>
                        <div class="mh-accordion-content">
                            <div class="mh-settings-grid">
                                <?php
                                if ( isset( $wp_settings_fields['mh-age-gate-admin']['mh_age_gate_appearance_section'] ) ) {
                                    foreach ( (array) $wp_settings_fields['mh-age-gate-admin']['mh_age_gate_appearance_section'] as $field ) {
                                        call_user_func( $field['callback'], $field['args'] );
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
                <br />
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting( 'mh_age_gate_option_group', 'mh_age_gate_options', array( $this, 'sanitize' ) );

        // 1. General Section
        add_settings_section( 'mh_age_gate_general_section', '', null, 'mh-age-gate-admin' );
        
        add_settings_field( 'enable_global', __( 'Enable Global Age Gate', 'mh-plug' ), array( $this, 'enable_global_cb' ), 'mh-age-gate-admin', 'mh_age_gate_general_section' );
        add_settings_field( 'minimum_age', __( 'Minimum Age Required', 'mh-plug' ), array( $this, 'minimum_age_cb' ), 'mh-age-gate-admin', 'mh_age_gate_general_section' );
        add_settings_field( 'cookie_duration', __( 'Cookie Duration (Days)', 'mh-plug' ), array( $this, 'cookie_duration_cb' ), 'mh-age-gate-admin', 'mh_age_gate_general_section' );

        // 2. Content Section
        add_settings_section( 'mh_age_gate_content_section', '', null, 'mh-age-gate-admin' );

        add_settings_field( 'modal_heading', __( 'Modal Heading', 'mh-plug' ), array( $this, 'modal_heading_cb' ), 'mh-age-gate-admin', 'mh_age_gate_content_section' );
        add_settings_field( 'modal_subheading', __( 'Disclaimer / Subheading', 'mh-plug' ), array( $this, 'modal_subheading_cb' ), 'mh-age-gate-admin', 'mh_age_gate_content_section' );
        add_settings_field( 'btn_yes_text', __( '"Yes" Button Text', 'mh-plug' ), array( $this, 'btn_yes_text_cb' ), 'mh-age-gate-admin', 'mh_age_gate_content_section' );
        add_settings_field( 'btn_no_text', __( '"No" Button Text', 'mh-plug' ), array( $this, 'btn_no_text_cb' ), 'mh-age-gate-admin', 'mh_age_gate_content_section' );
        add_settings_field( 'redirect_url', __( 'Redirect URL (On "No")', 'mh-plug' ), array( $this, 'redirect_url_cb' ), 'mh-age-gate-admin', 'mh_age_gate_content_section' );

        // 3. Appearance Section
        add_settings_section( 'mh_age_gate_appearance_section', '', null, 'mh-age-gate-admin' );

        add_settings_field( 'overlay_bg_color', __( 'Overlay Background Color', 'mh-plug' ), array( $this, 'overlay_bg_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'modal_shadow', __( 'Enable Modal Shadow', 'mh-plug' ), array( $this, 'modal_shadow_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'bg_color', __( 'Modal Background Color', 'mh-plug' ), array( $this, 'bg_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        
        add_settings_field( 'title_color', __( 'Title Color', 'mh-plug' ), array( $this, 'title_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'title_font_size', __( 'Title Font Size (e.g. 32px)', 'mh-plug' ), array( $this, 'title_font_size_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        
        add_settings_field( 'text_color', __( 'Text Color', 'mh-plug' ), array( $this, 'text_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'text_font_size', __( 'Text Font Size (e.g. 16px)', 'mh-plug' ), array( $this, 'text_font_size_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );

        add_settings_field( 'btn_bg_color', __( 'Button Background Color', 'mh-plug' ), array( $this, 'btn_bg_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'btn_hover_color', __( 'Button Hover Color', 'mh-plug' ), array( $this, 'btn_hover_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'btn_text_color', __( 'Button Text Color', 'mh-plug' ), array( $this, 'btn_text_color_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
        add_settings_field( 'btn_font_size', __( 'Button Font Size (e.g. 18px)', 'mh-plug' ), array( $this, 'btn_font_size_cb' ), 'mh-age-gate-admin', 'mh_age_gate_appearance_section' );
    }

    public function sanitize( $input ) {
        $sanitary_values = array();
        
        $sanitary_values['enable_global']   = isset( $input['enable_global'] ) && $input['enable_global'] == '1' ? true : false;
        $sanitary_values['modal_shadow']    = isset( $input['modal_shadow'] ) && $input['modal_shadow'] == '1' ? true : false;
        
        $int_fields = array( 'minimum_age', 'cookie_duration' );
        foreach( $int_fields as $f ) {
            if ( isset( $input[$f] ) ) $sanitary_values[$f] = absint( $input[$f] );
        }

        $text_fields = array( 'modal_heading', 'btn_yes_text', 'btn_no_text', 'title_font_size', 'text_font_size', 'btn_font_size' );
        foreach( $text_fields as $f ) {
            if ( isset( $input[$f] ) ) $sanitary_values[$f] = sanitize_text_field( $input[$f] );
        }

        if ( isset( $input['modal_subheading'] ) ) {
            $sanitary_values['modal_subheading'] = sanitize_textarea_field( $input['modal_subheading'] );
        }

        if ( isset( $input['redirect_url'] ) ) {
            $sanitary_values['redirect_url'] = esc_url_raw( $input['redirect_url'] );
        }

        $color_fields = array( 'overlay_bg_color', 'bg_color', 'title_color', 'text_color', 'btn_bg_color', 'btn_hover_color', 'btn_text_color' );
        foreach( $color_fields as $f ) {
            if ( isset( $input[$f] ) ) $sanitary_values[$f] = sanitize_text_field( $input[$f] ); // Color pickers can include rgba(), sanitize_hex_color is too strict
        }

        return $sanitary_values;
    }

    // --- CUSTOM FIELD RENDERERS ---

    private function render_switch( $id, $label, $is_checked ) {
        $checked_attr = $is_checked ? 'checked' : '';
        echo "<div class='mh-widget-card'>";
        echo "  <div class='mh-widget-card-header'>";
        echo "      <div class='mh-widget-title'>" . esc_html( $label ) . "</div>";
        echo "      <label class='switch'>";
        echo "          <input class='cb' type='checkbox' name='mh_age_gate_options[{$id}]' value='1' {$checked_attr} />";
        echo "          <span class='toggle'><span class='left'>off</span><span class='right'>on</span></span>";
        echo "      </label>";
        echo "  </div>";
        echo "</div>";
    }

    private function render_custom_field( $title, $html_input ) {
        echo "<div class='mh-widget-card' style='display: flex; flex-direction: column;'>";
        echo "  <div class='mh-widget-card-header' style='border-bottom: 1px solid #eee; margin-bottom: 15px;'>";
        echo "      <div class='mh-widget-title'>" . esc_html( $title ) . "</div>";
        echo "  </div>";
        echo "  <div class='mh-widget-card-body' style='padding: 0 15px 15px 15px;'>";
        echo        $html_input;
        echo "  </div>";
        echo "</div>";
    }

    // GENERAL
    public function enable_global_cb() {
        $opt = get_option('mh_age_gate_options');
        $this->render_switch('enable_global', __('Global Status', 'mh-plug'), !empty($opt['enable_global']));
    }

    public function minimum_age_cb() {
        $val = get_option('mh_age_gate_options')['minimum_age'] ?? 18;
        $html = sprintf( '<input type="number" name="mh_age_gate_options[minimum_age]" value="%s" style="width: 100%%;" />', esc_attr($val) );
        $this->render_custom_field( __('Minimum Age', 'mh-plug'), $html );
    }

    public function cookie_duration_cb() {
        $val = get_option('mh_age_gate_options')['cookie_duration'] ?? 30;
        $html = sprintf( '<input type="number" name="mh_age_gate_options[cookie_duration]" value="%s" style="width: 100%%;" />', esc_attr($val) );
        $this->render_custom_field( __('Cookie Duration (Days)', 'mh-plug'), $html );
    }

    // CONTENT
    public function modal_heading_cb() {
        $val = get_option('mh_age_gate_options')['modal_heading'] ?? '';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[modal_heading]" value="%s" style="width: 100%%;" />', esc_attr($val) );
        $this->render_custom_field( __('Modal Heading', 'mh-plug'), $html );
    }

    public function modal_subheading_cb() {
        $val = get_option('mh_age_gate_options')['modal_subheading'] ?? '';
        $html = sprintf( '<textarea name="mh_age_gate_options[modal_subheading]" rows="4" style="width: 100%%; resize: vertical;">%s</textarea>', esc_html($val) );
        $this->render_custom_field( __('Disclaimer Text', 'mh-plug'), $html );
    }

    public function btn_yes_text_cb() {
        $val = get_option('mh_age_gate_options')['btn_yes_text'] ?? '';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[btn_yes_text]" value="%s" style="width: 100%%;" placeholder="Yes, I am older" />', esc_attr($val) );
        $this->render_custom_field( __('Yes Text', 'mh-plug'), $html );
    }

    public function btn_no_text_cb() {
        $val = get_option('mh_age_gate_options')['btn_no_text'] ?? '';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[btn_no_text]" value="%s" style="width: 100%%;" placeholder="No, I am not" />', esc_attr($val) );
        $this->render_custom_field( __('No Text', 'mh-plug'), $html );
    }

    public function redirect_url_cb() {
        $val = get_option('mh_age_gate_options')['redirect_url'] ?? '';
        $html = sprintf( '<input type="url" name="mh_age_gate_options[redirect_url]" value="%s" style="width: 100%%;" />', esc_url($val) );
        $this->render_custom_field( __('Redirect URL', 'mh-plug'), $html );
    }

    // APPEARANCE
    public function overlay_bg_color_cb() {
        $val = get_option('mh_age_gate_options')['overlay_bg_color'] ?? 'rgba(0,0,0,0.8)';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[overlay_bg_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Overlay Color', 'mh-plug'), $html );
    }

    public function bg_color_cb() {
        $val = get_option('mh_age_gate_options')['bg_color'] ?? '#ffffff';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[bg_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Modal Background', 'mh-plug'), $html );
    }

    public function modal_shadow_cb() {
        $opt = get_option('mh_age_gate_options');
        $this->render_switch('modal_shadow', __('Enable Modal Shadow', 'mh-plug'), isset($opt['modal_shadow']) ? !empty($opt['modal_shadow']) : true);
    }

    public function title_color_cb() {
        $val = get_option('mh_age_gate_options')['title_color'] ?? '#333333';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[title_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Title Color', 'mh-plug'), $html );
    }

    public function title_font_size_cb() {
        $val = get_option('mh_age_gate_options')['title_font_size'] ?? '32px';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[title_font_size]" value="%s" style="width: 100%%;" placeholder="e.g. 32px or 2em" />', esc_attr($val) );
        $this->render_custom_field( __('Title Font Size', 'mh-plug'), $html );
    }

    public function text_color_cb() {
        $val = get_option('mh_age_gate_options')['text_color'] ?? '#666666';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[text_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Text Color', 'mh-plug'), $html );
    }

    public function text_font_size_cb() {
        $val = get_option('mh_age_gate_options')['text_font_size'] ?? '16px';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[text_font_size]" value="%s" style="width: 100%%;" placeholder="e.g. 16px" />', esc_attr($val) );
        $this->render_custom_field( __('Text Font Size', 'mh-plug'), $html );
    }

    public function btn_bg_color_cb() {
        $val = get_option('mh_age_gate_options')['btn_bg_color'] ?? '#007cba';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[btn_bg_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Button Background', 'mh-plug'), $html );
    }

    public function btn_hover_color_cb() {
        $val = get_option('mh_age_gate_options')['btn_hover_color'] ?? '#005a87';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[btn_hover_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Button Hover Color', 'mh-plug'), $html );
    }

    public function btn_text_color_cb() {
        $val = get_option('mh_age_gate_options')['btn_text_color'] ?? '#ffffff';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[btn_text_color]" value="%s" class="mh-color-picker" />', esc_attr($val) );
        $this->render_custom_field( __('Button Text Color', 'mh-plug'), $html );
    }

    public function btn_font_size_cb() {
        $val = get_option('mh_age_gate_options')['btn_font_size'] ?? '18px';
        $html = sprintf( '<input type="text" name="mh_age_gate_options[btn_font_size]" value="%s" style="width: 100%%;" placeholder="e.g. 18px" />', esc_attr($val) );
        $this->render_custom_field( __('Button Font Size', 'mh-plug'), $html );
    }

}

new MH_Plug_Age_Gate_Admin();
