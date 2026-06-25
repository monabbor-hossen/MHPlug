<?php
/**
 * MH_Combo_Frontend — WooCommerce Frontend Integration for the Combo Product Type
 *
 * Handles rendering the Add to Cart template on the single product page
 * for the "combo" product type.
 *
 * @package MH_Plug
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class MH_Combo_Frontend
 */
class MH_Plug_Combo_Frontend {

    /**
     * Boot the class: register frontend hooks.
     *
     * @return void
     */
    public static function init() {
        // Hook into WooCommerce's dynamic add-to-cart action for the 'combo' type.
        add_action( 'woocommerce_combo_add_to_cart', [ __CLASS__, 'add_to_cart_template' ] );

        // Hook for Custom Variations dropdowns
        add_action( 'woocommerce_before_add_to_cart_button', [ __CLASS__, 'render_custom_variations_dropdowns' ] );
    }

    /**
     * Render the Add to Cart template for Combo products.
     *
     * Displays a list of included products, then renders the standard
     * WooCommerce simple add to cart button/form.
     *
     * @return void
     */
    public static function add_to_cart_template() {
        global $product;

        // Ensure we are dealing with a valid product and it is a combo.
        if ( ! is_a( $product, 'WC_Product' ) || ! $product->is_type( 'combo' ) ) {
            return;
        }

        if ( ! $product->is_purchasable() ) {
            return;
        }

        $combo_product_ids = get_post_meta( $product->get_id(), '_mh_combo_products', true );
        
        if ( ! is_array( $combo_product_ids ) || empty( $combo_product_ids ) ) {
            echo '<p class="mh-combo-empty">' . esc_html__( 'No items are included in this combo yet.', 'mh-plug-ecommerce-builder-widgets' ) . '</p>';
            return;
        }

        do_action( 'woocommerce_before_add_to_cart_form' );

        echo '<form class="cart" action="' . esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ) . '" method="post" enctype="multipart/form-data">';

        // Render the list of included products
        echo '<div class="mh-combo-includes-wrapper" style="margin-bottom: 25px; padding: 15px; background: #f9f9f9; border-radius: 5px; border: 1px solid #eee;">';
        echo '<h4 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">' . esc_html__( 'This Combo Includes:', 'mh-plug-ecommerce-builder-widgets' ) . '</h4>';
        echo '<ul class="mh-combo-includes-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">';
        
        foreach ( $combo_product_ids as $id ) {
            $bundled_product = wc_get_product( $id );
            
            if ( $bundled_product && $bundled_product->is_visible() ) {
                $product_url = esc_url( $bundled_product->get_permalink() );
                $thumbnail   = $bundled_product->get_image( [50, 50], [ 'style' => 'border-radius: 4px;' ] );
                $title       = wp_kses_post( $bundled_product->get_title() );
                
                echo '<li style="display: flex; align-items: center; gap: 15px;">';
                echo '<div class="mh-combo-item-thumb" style="width: 50px; height: 50px; flex-shrink: 0;">';
                echo '<a href="' . $product_url . '">' . $thumbnail . '</a>';
                echo '</div>';
                echo '<div class="mh-combo-item-details">';
                echo '<strong style="display: block; font-size: 14px;"><a href="' . $product_url . '" style="text-decoration: none; color: inherit;">' . $title . '</a></strong>';
                echo '</div>';
                echo '</li>';
            }
        }
        
        echo '</ul>';
        echo '</div>';

        // The Add to Cart Button
        echo '<input type="hidden" name="product_id" value="' . esc_attr( $product->get_id() ) . '" />';
        echo '<button type="submit" name="add-to-cart" value="' . esc_attr( $product->get_id() ) . '" class="single_add_to_cart_button button alt mh-combo-btn">' . esc_html__( 'Add Combo to Cart', 'mh-plug-ecommerce-builder-widgets' ) . '</button>';

        echo '</form>';

        do_action( 'woocommerce_after_add_to_cart_form' );
    }

    /**
     * Render Custom Variations dropdowns and inject JSON rules.
     */
    public static function render_custom_variations_dropdowns() {
        global $product;

        if ( ! is_a( $product, 'WC_Product' ) ) {
            return;
        }

        $rules = get_post_meta( $product->get_id(), '_mh_custom_variation_rules', true );
        
        if ( empty( $rules ) || ! is_array( $rules ) ) {
            return;
        }

        $all_attributes = $product->get_attributes();
        
        if ( empty( $all_attributes ) ) {
            return;
        }

        echo '<div class="mh-custom-variations-wrapper" style="margin-bottom: 20px;">';
        
        foreach ( $all_attributes as $attribute ) {
            $attribute_name = $attribute->get_name();
            $options        = $attribute->get_options();
            $is_variation   = $attribute->get_variation() ? 'true' : 'false';
            
            echo '<div class="mh-custom-attr-row" style="margin-bottom: 10px;">';
            echo '<label style="display:block; margin-bottom: 5px;">' . esc_html( wc_attribute_label( $attribute_name ) ) . '</label>';
            echo '<select name="mh_custom_attr[' . esc_attr( $attribute_name ) . ']" class="mh-custom-attr-select" data-attribute="' . esc_attr( $attribute_name ) . '" data-is-variation="' . esc_attr( $is_variation ) . '">';
            echo '<option value="">' . esc_html__( 'Choose an option', 'mh-plug-ecommerce-builder-widgets' ) . '</option>';
            
            if ( $attribute->is_taxonomy() ) {
                foreach ( $options as $term_id ) {
                    $term = get_term( $term_id, $attribute_name );
                    if ( $term ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                    }
                }
            } else {
                foreach ( $options as $option ) {
                    echo '<option value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
                }
            }
            
            echo '</select>';
            echo '</div>';
        }
        
        echo '</div>';

        // Localize the rules for JS — always output a sequential JS array ([]),
        // never a JS object ({}). array_values() re-indexes any gaps left by
        // deleted repeater rows so json_encode always emits array syntax.
        echo '<script>var mhVariationRules = ' . wp_json_encode( array_values( $rules ) ) . ';</script>';

    }
}
