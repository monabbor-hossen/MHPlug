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
        if ( 'settings_page_mh-age-gate' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_add_inline_script( 'wp-color-picker', 'jQuery(document).ready(function($){$(".mh-color-picker").wpColorPicker();});' );
    }

    public function add_plugin_page() {
        add_options_page(
            __( 'MH Age Gate', 'mh-plug' ),
            __( 'MH Age Gate', 'mh-plug' ),
            'manage_options',
            'mh-age-gate',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'MH Age Gate Settings', 'mh-plug' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'mh_age_gate_option_group' );
                do_settings_sections( 'mh-age-gate-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'mh_age_gate_option_group',
            'mh_age_gate_options',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'mh_age_gate_setting_section',
            __( 'General Settings', 'mh-plug' ),
            array( $this, 'print_section_info' ),
            'mh-age-gate-admin'
        );

        add_settings_field(
            'enable_global',
            __( 'Enable Global Age Gate', 'mh-plug' ),
            array( $this, 'enable_global_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'minimum_age',
            __( 'Minimum Age Required', 'mh-plug' ),
            array( $this, 'minimum_age_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'modal_heading',
            __( 'Modal Heading Text', 'mh-plug' ),
            array( $this, 'modal_heading_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'modal_subheading',
            __( 'Modal Subheading/Disclaimer', 'mh-plug' ),
            array( $this, 'modal_subheading_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'btn_yes_text',
            __( '"Yes" Button Text', 'mh-plug' ),
            array( $this, 'btn_yes_text_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'btn_no_text',
            __( '"No" Button Text', 'mh-plug' ),
            array( $this, 'btn_no_text_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'redirect_url',
            __( 'Redirect URL (for "No" clicks)', 'mh-plug' ),
            array( $this, 'redirect_url_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'bg_color',
            __( 'Background Color', 'mh-plug' ),
            array( $this, 'bg_color_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );

        add_settings_field(
            'text_color',
            __( 'Text Color', 'mh-plug' ),
            array( $this, 'text_color_cb' ),
            'mh-age-gate-admin',
            'mh_age_gate_setting_section'
        );
    }

    public function sanitize( $input ) {
        $sanitary_values = array();
        
        $sanitary_values['enable_global'] = isset( $input['enable_global'] ) ? true : false;
        
        if ( isset( $input['minimum_age'] ) ) {
            $sanitary_values['minimum_age'] = absint( $input['minimum_age'] );
        }

        if ( isset( $input['modal_heading'] ) ) {
            $sanitary_values['modal_heading'] = sanitize_text_field( $input['modal_heading'] );
        }

        if ( isset( $input['modal_subheading'] ) ) {
            $sanitary_values['modal_subheading'] = sanitize_textarea_field( $input['modal_subheading'] );
        }

        if ( isset( $input['btn_yes_text'] ) ) {
            $sanitary_values['btn_yes_text'] = sanitize_text_field( $input['btn_yes_text'] );
        }

        if ( isset( $input['btn_no_text'] ) ) {
            $sanitary_values['btn_no_text'] = sanitize_text_field( $input['btn_no_text'] );
        }

        if ( isset( $input['redirect_url'] ) ) {
            $sanitary_values['redirect_url'] = esc_url_raw( $input['redirect_url'] );
        }

        if ( isset( $input['bg_color'] ) ) {
            $sanitary_values['bg_color'] = sanitize_hex_color( $input['bg_color'] );
        }

        if ( isset( $input['text_color'] ) ) {
            $sanitary_values['text_color'] = sanitize_hex_color( $input['text_color'] );
        }

        return $sanitary_values;
    }

    public function print_section_info() {
        esc_html_e( 'Configure your Age Gate settings below:', 'mh-plug' );
    }

    public function enable_global_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $checked = isset( $options['enable_global'] ) && $options['enable_global'] ? 'checked' : '';
        printf(
            '<input type="checkbox" id="enable_global" name="mh_age_gate_options[enable_global]" value="1" %s />',
            esc_attr( $checked )
        );
    }

    public function minimum_age_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['minimum_age'] ) ? $options['minimum_age'] : 18;
        printf(
            '<input type="number" id="minimum_age" name="mh_age_gate_options[minimum_age]" value="%s" />',
            esc_attr( $val )
        );
    }

    public function modal_heading_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['modal_heading'] ) ? $options['modal_heading'] : '';
        printf(
            '<input type="text" id="modal_heading" name="mh_age_gate_options[modal_heading]" value="%s" class="regular-text" />',
            esc_attr( $val )
        );
    }

    public function modal_subheading_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['modal_subheading'] ) ? $options['modal_subheading'] : '';
        printf(
            '<textarea id="modal_subheading" name="mh_age_gate_options[modal_subheading]" rows="4" class="large-text code">%s</textarea>',
            esc_html( $val )
        );
    }

    public function btn_yes_text_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['btn_yes_text'] ) ? $options['btn_yes_text'] : 'Yes, I am older';
        printf(
            '<input type="text" id="btn_yes_text" name="mh_age_gate_options[btn_yes_text]" value="%s" class="regular-text" />',
            esc_attr( $val )
        );
    }

    public function btn_no_text_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['btn_no_text'] ) ? $options['btn_no_text'] : 'No, I am not';
        printf(
            '<input type="text" id="btn_no_text" name="mh_age_gate_options[btn_no_text]" value="%s" class="regular-text" />',
            esc_attr( $val )
        );
    }

    public function redirect_url_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['redirect_url'] ) ? $options['redirect_url'] : '';
        printf(
            '<input type="url" id="redirect_url" name="mh_age_gate_options[redirect_url]" value="%s" class="regular-text" />',
            esc_url( $val )
        );
    }

    public function bg_color_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['bg_color'] ) ? $options['bg_color'] : '#ffffff';
        printf(
            '<input type="text" id="bg_color" name="mh_age_gate_options[bg_color]" value="%s" class="mh-color-picker" />',
            esc_attr( $val )
        );
    }

    public function text_color_cb() {
        $options = get_option( 'mh_age_gate_options' );
        $val     = isset( $options['text_color'] ) ? $options['text_color'] : '#000000';
        printf(
            '<input type="text" id="text_color" name="mh_age_gate_options[text_color]" value="%s" class="mh-color-picker" />',
            esc_attr( $val )
        );
    }
}

new MH_Plug_Age_Gate_Admin();
