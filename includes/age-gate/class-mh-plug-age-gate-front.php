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

        $global_enable = isset( $this->options['enable_global'] ) ? $this->options['enable_global'] : false;

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

        wp_enqueue_script(
            'mh-age-gate-public',
            plugins_url( '../../assets/js/age-gate-public.js', __FILE__ ),
            array(),
            '1.0.0',
            true
        );

        $redirect_url = isset( $this->options['redirect_url'] ) ? $this->options['redirect_url'] : '';
        wp_localize_script( 'mh-age-gate-public', 'mhAgeGate', array(
            'redirectUrl' => esc_url_raw( $redirect_url )
        ) );
    }

    public function render_modal() {
        if ( ! $this->should_render ) {
            return;
        }

        $modal_heading    = isset( $this->options['modal_heading'] ) ? $this->options['modal_heading'] : __( 'Age Verification', 'mh-plug' );
        $modal_subheading = isset( $this->options['modal_subheading'] ) ? $this->options['modal_subheading'] : __( 'Please confirm your age to continue.', 'mh-plug' );
        $btn_yes          = isset( $this->options['btn_yes_text'] ) ? $this->options['btn_yes_text'] : __( 'Yes, I am older', 'mh-plug' );
        $btn_no           = isset( $this->options['btn_no_text'] ) ? $this->options['btn_no_text'] : __( 'No, I am not', 'mh-plug' );
        $bg_color         = isset( $this->options['bg_color'] ) ? $this->options['bg_color'] : '#ffffff';
        $text_color       = isset( $this->options['text_color'] ) ? $this->options['text_color'] : '#000000';
        $min_age          = isset( $this->options['minimum_age'] ) ? $this->options['minimum_age'] : 18;

        // Replace any potential [Age] placeholder if necessary
        $btn_yes = str_replace( '[Age]', $min_age, $btn_yes );

        ?>
        <div id="mh-age-gate-overlay" style="background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>;">
            <div class="mh-age-gate-content">
                <h2><?php echo esc_html( $modal_heading ); ?></h2>
                <p><?php echo nl2br( esc_html( $modal_subheading ) ); ?></p>
                <div class="mh-age-gate-actions">
                    <button id="mh-age-gate-yes" class="mh-age-gate-btn"><?php echo esc_html( $btn_yes ); ?></button>
                    <button id="mh-age-gate-no" class="mh-age-gate-btn mh-btn-secondary"><?php echo esc_html( $btn_no ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
}

new MH_Plug_Age_Gate_Front();
