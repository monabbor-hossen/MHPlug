<?php
/**
 * Age Gate Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class MH_Plug_Age_Gate_Front {

    private $should_render = false;
    private $options = array();

    public function __construct() {
        add_action( 'wp', array( $this, 'determine_display' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_footer', array( $this, 'render_modal' ) );
    }

    public function determine_display() {
        if ( is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
            return;
        }

        $this->options = get_option( 'mh_age_gate_options', array() );

        // If cookie is present, no need to show
        if ( isset( $_COOKIE['mh_plug_age_verified'] ) ) {
            return;
        }

        $global_enable = !empty( $this->options['enable_global'] );

        $override = 'global';
        if ( is_singular() ) {
            $post_id  = get_the_ID();
            $meta_val = get_post_meta( $post_id, '_mh_age_gate_override', true );
            if ( ! empty( $meta_val ) ) {
                $override = $meta_val;
            }
        }

        if ( 'force_disable' === $override ) {
            $this->should_render = false;
        } elseif ( 'force_enable' === $override ) {
            $this->should_render = true;
        } else {
            // 'global'
            $this->should_render = $global_enable;
        }
    }

    public function enqueue_scripts() {
        if ( ! $this->should_render ) {
            return;
        }

        wp_enqueue_style(
            'mh-age-gate-public',
            plugins_url( '../../assets/css/age-gate-public.css', __FILE__ ),
            array(),
            '1.0.0'
        );

        // Inject dynamic CSS based on settings
        $overlay_bg = $this->options['overlay_bg_color'] ?? 'rgba(0,0,0,0.8)';
        $modal_bg   = $this->options['bg_color'] ?? '#ffffff';
        $shadow     = isset( $this->options['modal_shadow'] ) && $this->options['modal_shadow'] == '1' ? '0 15px 50px rgba(0,0,0,0.3)' : 'none';
        
        $title_color = $this->options['title_color'] ?? '#333333';
        $title_size  = $this->options['title_font_size'] ?? '32px';
        
        $text_color = $this->options['text_color'] ?? '#666666';
        $text_size  = $this->options['text_font_size'] ?? '16px';
        
        $btn_bg    = $this->options['btn_bg_color'] ?? '#007cba';
        $btn_hover = $this->options['btn_hover_color'] ?? '#005a87';
        $btn_text  = $this->options['btn_text_color'] ?? '#ffffff';
        $btn_size  = $this->options['btn_font_size'] ?? '18px';

        $custom_css = "
            :root {
                --mh-ag-overlay-bg: {$overlay_bg};
                --mh-ag-modal-bg: {$modal_bg};
                --mh-ag-shadow: {$shadow};
                
                --mh-ag-title-color: {$title_color};
                --mh-ag-title-size: {$title_size};
                
                --mh-ag-text-color: {$text_color};
                --mh-ag-text-size: {$text_size};
                
                --mh-ag-btn-bg: {$btn_bg};
                --mh-ag-btn-hover: {$btn_hover};
                --mh-ag-btn-color: {$btn_text};
                --mh-ag-btn-size: {$btn_size};
            }
        ";
        wp_add_inline_style( 'mh-age-gate-public', $custom_css );

        wp_enqueue_script(
            'mh-age-gate-public',
            plugins_url( '../../assets/js/age-gate-public.js', __FILE__ ),
            array(),
            '1.0.0',
            true
        );

        $redirect_url = $this->options['redirect_url'] ?? '';
        $cookie_days  = $this->options['cookie_duration'] ?? 30;

        wp_localize_script( 'mh-age-gate-public', 'mhAgeGate', array(
            'redirectUrl' => esc_url_raw( $redirect_url ),
            'cookieDays'  => absint( $cookie_days )
        ) );
    }

    public function render_modal() {
        if ( ! $this->should_render ) {
            return;
        }

        $modal_heading    = $this->options['modal_heading'] ?? __( 'Age Verification', 'mh-plug' );
        $modal_subheading = $this->options['modal_subheading'] ?? __( 'Please confirm your age to continue.', 'mh-plug' );
        $btn_yes          = $this->options['btn_yes_text'] ?? __( 'Yes, I am older', 'mh-plug' );
        $btn_no           = $this->options['btn_no_text'] ?? __( 'No, I am not', 'mh-plug' );
        $min_age          = $this->options['minimum_age'] ?? 18;

        // Replace any potential [Age] placeholder
        $btn_yes = str_replace( '[Age]', $min_age, $btn_yes );

        ?>
        <div id="mh-age-gate-overlay">
            <div class="mh-age-gate-content">
                <h2><?php echo esc_html( $modal_heading ); ?></h2>
                <div class="mh-age-gate-desc">
                    <p><?php echo nl2br( esc_html( $modal_subheading ) ); ?></p>
                </div>
                <div class="mh-age-gate-actions">
                    <button id="mh-age-gate-yes" class="mh-age-gate-btn mh-btn-yes"><?php echo esc_html( $btn_yes ); ?></button>
                    <button id="mh-age-gate-no" class="mh-age-gate-btn mh-btn-no"><?php echo esc_html( $btn_no ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
}

new MH_Plug_Age_Gate_Front();
