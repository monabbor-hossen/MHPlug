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
        $text_color = $this->options['text_color'] ?? '#666666';
        
        $btn_yes_bg    = $this->options['btn_yes_bg_color'] ?? '#007cba';
        $btn_yes_hover = $this->options['btn_yes_hover_color'] ?? '#005a87';
        $btn_yes_text  = $this->options['btn_yes_text_color'] ?? '#ffffff';

        $btn_no_bg    = $this->options['btn_no_bg_color'] ?? '#f0f0f1';
        $btn_no_hover = $this->options['btn_no_hover_color'] ?? '#dcdcdc';
        $btn_no_text  = $this->options['btn_no_text_color'] ?? '#3c434a';

        $custom_css = "
            :root {
                --mh-ag-overlay-bg: {$overlay_bg};
                --mh-ag-modal-bg: {$modal_bg};
                --mh-ag-shadow: {$shadow};
                
                --mh-ag-title-color: {$title_color};
                
                --mh-ag-text-color: {$text_color};
                
                --mh-ag-btn-yes-bg: {$btn_yes_bg};
                --mh-ag-btn-yes-hover: {$btn_yes_hover};
                --mh-ag-btn-yes-color: {$btn_yes_text};

                --mh-ag-btn-no-bg: {$btn_no_bg};
                --mh-ag-btn-no-hover: {$btn_no_hover};
                --mh-ag-btn-no-color: {$btn_no_text};
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

        // 🚀 THE FIX: Replaced wp_localize_script with wp_add_inline_script
        $age_gate_data = array(
            'redirectUrl' => esc_url_raw( $redirect_url ),
            'cookieDays'  => absint( $cookie_days )
        );
        wp_add_inline_script( 'mh-age-gate-public', 'var mhAgeGate = ' . wp_json_encode( $age_gate_data ) . ';', 'before' );
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
        $show_logo        = !empty( $this->options['show_logo'] );

        // Replace any potential [Age] placeholder
        $btn_yes = str_replace( '[Age]', $min_age, $btn_yes );

        ?>
        <div id="mh-age-gate-overlay">
            <div class="mh-age-gate-content">
                <?php if ( $show_logo ) : ?>
                    <div class="mh-age-gate-logo">
                        <?php 
                        if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                            the_custom_logo();
                        } else {
                            echo '<span class="mh-age-gate-fallback-logo">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
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