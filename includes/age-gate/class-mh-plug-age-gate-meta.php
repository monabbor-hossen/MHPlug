<?php
/**
 * Age Gate Meta Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class MH_Plug_Age_Gate_Meta {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
    }

    public function add_meta_box() {
        $screens = array( 'post', 'page', 'product' );
        foreach ( $screens as $screen ) {
            add_meta_box(
                'mh_age_gate_meta_box',
                __( 'Age Gate Settings', 'mh-plug' ),
                array( $this, 'meta_box_html' ),
                $screen,
                'side',
                'default'
            );
        }
    }

    public function meta_box_html( $post ) {
        wp_nonce_field( 'mh_age_gate_save_data', 'mh_age_gate_meta_box_nonce' );

        $value = get_post_meta( $post->ID, '_mh_age_gate_override', true );
        if ( empty( $value ) ) {
            $value = 'global';
        }

        echo '<p><label for="mh_age_gate_override">';
        esc_html_e( 'Select age gate behavior for this post:', 'mh-plug' );
        echo '</label></p>';
        
        echo '<select id="mh_age_gate_override" name="mh_age_gate_override" style="width: 100%;">';
        echo '<option value="global" ' . selected( $value, 'global', false ) . '>' . esc_html__( 'Use Global Setting', 'mh-plug' ) . '</option>';
        echo '<option value="force_enable" ' . selected( $value, 'force_enable', false ) . '>' . esc_html__( 'Force Enable (Override Global)', 'mh-plug' ) . '</option>';
        echo '<option value="force_disable" ' . selected( $value, 'force_disable', false ) . '>' . esc_html__( 'Force Disable (Override Global)', 'mh-plug' ) . '</option>';
        echo '</select>';
    }

    public function save_meta_box_data( $post_id ) {
        if ( ! isset( $_POST['mh_age_gate_meta_box_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( wp_unslash( $_POST['mh_age_gate_meta_box_nonce'] ), 'mh_age_gate_save_data' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        if ( ! isset( $_POST['mh_age_gate_override'] ) ) {
            return;
        }

        $my_data = sanitize_text_field( wp_unslash( $_POST['mh_age_gate_override'] ) );
        if ( in_array( $my_data, array( 'global', 'force_enable', 'force_disable' ), true ) ) {
            update_post_meta( $post_id, '_mh_age_gate_override', $my_data );
        }
    }
}

new MH_Plug_Age_Gate_Meta();
