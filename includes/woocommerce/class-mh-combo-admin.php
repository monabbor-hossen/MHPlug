<?php
/**
 * MH_Combo_Admin — WooCommerce Admin Integration for the Combo Product Type
 *
 * Handles everything needed to make the "Combo" product type fully functional
 * inside the WooCommerce Product Data meta-box:
 *
 *  • Adds "Combo Product" to the product-type dropdown.
 *  • Injects a dedicated "Combo Items" tab (visible only for combo products).
 *  • Renders a native wc_product_search Select2 field for picking child products.
 *  • Saves selected product IDs to `_mh_combo_products` post meta.
 *
 * @package MH_Plug
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class MH_Combo_Admin
 */
class MH_Combo_Admin {

    /**
     * Boot the class: register all hooks.
     *
     * Call this once from the main plugin loader (after WooCommerce is ready).
     *
     * @return void
     */
    public static function init() {
        $instance = new self();

        // 1. Add "Combo Product" option to the Product Type dropdown.
        add_filter( 'product_type_selector', [ $instance, 'add_combo_product_type' ] );

        // 2. Add the "Combo Items" tab to the Product Data meta-box.
        add_filter( 'woocommerce_product_data_tabs', [ $instance, 'add_combo_product_tab' ] );

        // 3. Render the tab panel HTML.
        add_action( 'woocommerce_product_data_panels', [ $instance, 'render_combo_panel' ] );

        // 4. Save posted combo data when the product is saved.
        add_action( 'woocommerce_process_product_meta_combo', [ $instance, 'save_combo_meta' ] );

        // 5. Enqueue inline CSS/JS tweaks for the admin editor.
        add_action( 'admin_enqueue_scripts', [ $instance, 'admin_inline_styles' ] );
        add_action( 'admin_enqueue_scripts', [ $instance, 'admin_footer_scripts' ] );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1.  PRODUCT TYPE DROPDOWN
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Add "Combo Product" to the WooCommerce product-type selector.
     *
     * @param array $types Existing product types [ slug => label ].
     * @return array
     */
    public function add_combo_product_type( array $types ) {
        $types['combo'] = __( 'Combo Product', 'mh-plug' );
        return $types;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2.  PRODUCT DATA TAB
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Register the "Combo Items" tab in the WooCommerce Product Data meta-box.
     *
     * The `class` key controls visibility. WooCommerce automatically shows/
     * hides tabs whose class matches the selected product type, following the
     * convention `show_if_{type}`.
     *
     * @param array $tabs Existing product data tabs.
     * @return array
     */
    public function add_combo_product_tab( array $tabs ) {
        $tabs['mh_combo'] = [
            'label'    => __( 'Combo Items', 'mh-plug' ),
            'target'   => 'combo_product_data',   // ID of the panel <div>.
            'class'    => [ 'show_if_combo' ],        // Show only for combo type.
            'priority' => 65,
        ];

        return $tabs;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3.  PANEL HTML
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Output the HTML for the Combo Items product-data panel.
     *
     * Uses WooCommerce's native `wc_product_search` field (which renders a
     * Select2 AJAX product-search widget) to let the admin pick which products
     * are bundled inside this combo.
     *
     * @return void
     */
    public function render_combo_panel() {
        global $post;

        // Retrieve currently saved combo products (ensure it defaults to an empty array)
        $combo_product_ids = get_post_meta( $post->ID, '_mh_combo_products', true );
        if ( ! is_array( $combo_product_ids ) ) {
            $combo_product_ids = [];
        }
        ?>
        <div id="combo_product_data" class="panel woocommerce_options_panel show_if_combo">
            <div class="options_group">
                <p class="form-field">
                    <label for="_mh_combo_products"><?php esc_html_e( 'Combo Products', 'mh-plug' ); ?></label>
                    <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="_mh_combo_products" name="_mh_combo_products[]" data-placeholder="<?php esc_attr_e( 'Search for products...', 'mh-plug' ); ?>" data-action="woocommerce_json_search_products_and_variations">
                        <?php
                        foreach ( $combo_product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </p>

                <?php
                // Read-only display: auto-calculated sum of bundled product prices.
                $current_regular_price = get_post_meta( $post->ID, '_regular_price', true );
                woocommerce_wp_text_input( [
                    'id'                => '_mh_combo_regular_price_display',
                    'label'             => __( 'Total Regular Price', 'mh-plug' ),
                    'value'             => $current_regular_price,
                    'description'       => __( 'This is automatically calculated by summing the prices of the bundled products. Save the product to update this value.', 'mh-plug' ),
                    'desc_tip'          => true,
                    'custom_attributes' => [
                        'readonly' => 'readonly',
                        'style'    => 'background-color: #f0f0f1; cursor: not-allowed;',
                    ],
                ] );

                // Custom Combo Sale Price field — populate from saved meta.
                $saved_combo_sale_price = get_post_meta( $post->ID, '_mh_combo_sale_price', true );
                woocommerce_wp_text_input( [
                    'id'          => '_mh_combo_sale_price',
                    'label'       => __( 'Combo Sale Price ($)', 'mh-plug' ),
                    'description' => __( 'Leave blank to charge the full combined price of all products. Enter a number to offer a discounted combo price.', 'mh-plug' ),
                    'desc_tip'    => true,
                    'data_type'   => 'price',
                    'value'       => $saved_combo_sale_price,
                ] );
                ?>
            </div>
        </div>
        <?php
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4.  SAVE META
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Save the selected combo-product IDs when the product is published/updated.
     *
     * WooCommerce fires `woocommerce_process_product_meta_{type}` for the active
     * product type, so this fires only when saving a combo product.
     *
     * @param int $post_id The product post ID.
     * @return void
     */
    public function save_combo_meta( $post_id ) {
        // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verified by WooCommerce.
        
        $selected_products = [];
        if ( isset( $_POST['_mh_combo_products'] ) ) {
            $selected_products = array_map( 'absint', (array) wp_unslash( $_POST['_mh_combo_products'] ) );
            update_post_meta( $post_id, '_mh_combo_products', $selected_products );
        } else {
            // No products selected — clear the meta entirely.
            delete_post_meta( $post_id, '_mh_combo_products' );
        }

        // Calculate auto-sum of bundled products
        $calculated_regular_price = 0;
        if ( ! empty( $selected_products ) ) {
            foreach ( $selected_products as $id ) {
                $product = wc_get_product( $id );
                if ( $product ) {
                    // Get raw price and cast to float
                    $price = (float) $product->get_price();
                    $calculated_regular_price += $price;
                }
            }
        }

        update_post_meta( $post_id, '_regular_price', $calculated_regular_price );

        // Handle Combo Sale Price
        if ( isset( $_POST['_mh_combo_sale_price'] ) && '' !== $_POST['_mh_combo_sale_price'] ) {
            $sale_price = wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['_mh_combo_sale_price'] ) ) );
            update_post_meta( $post_id, '_mh_combo_sale_price', $sale_price );
            update_post_meta( $post_id, '_sale_price', $sale_price );
            update_post_meta( $post_id, '_price', $sale_price );
        } else {
            delete_post_meta( $post_id, '_mh_combo_sale_price' );
            delete_post_meta( $post_id, '_sale_price' );
            update_post_meta( $post_id, '_price', $calculated_regular_price );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5.  ADMIN STYLES
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Output lightweight inline styles for the Combo admin panel.
     * Only runs on the WooCommerce product edit screen.
     *
     * @return void
     */
    public function admin_inline_styles() {
        $screen = get_current_screen();
        if ( ! $screen || 'product' !== $screen->id ) {
            return;
        }
        $css  = '#woocommerce-product-data ul.wc-tabs li.mh_combo_options.mh_combo_tab a::before {';
        $css .= "content: '\\f509'; font-family: Dashicons; font-size: 17px; line-height: 1; }";
        $css .= '.mh-combo-panel { padding: 12px 0 !important; }';
        $css .= '.mh-combo-options-group { padding: 0 !important; }';
        $css .= '.mh-combo-field label { display: block; margin-bottom: 6px; }';
        $css .= '.mh-combo-preview { padding: 10px 16px; background: #f8f9fa; border-left: 4px solid #7c3aed; margin: 0 24px 16px; border-radius: 3px; }';
        $css .= '.mh-combo-preview-heading { margin: 0 0 6px; color: #3c434a; }';
        $css .= '.mh-combo-preview-list { margin: 0; padding: 0; list-style: disc inside; }';
        $css .= '.mh-combo-preview-list li { margin: 4px 0; font-size: 13px; color: #50575e; }';
        wp_add_inline_style( 'woocommerce_admin_styles', $css );
    }

    /**
     * Inject JS in the admin footer to hide native WooCommerce pricing fields
     * (Regular Price / Sale Price in the General tab) whenever the Combo
     * product type is selected. Our Combo Items tab owns pricing for this type.
     */
    public function admin_footer_scripts() {
        $screen = get_current_screen();
        if ( ! $screen || 'product' !== $screen->id ) {
            return;
        }
        $js  = 'jQuery(document).ready(function($){';
        $js .= 'function mhTogglePricingFields(){';
        $js .= 'var type=$("#product-type").val();';
        $js .= 'if("combo"===type){$(".options_group.pricing").hide();}';
        $js .= 'else{$(".options_group.pricing").show();}';
        $js .= '}';
        $js .= 'mhTogglePricingFields();';
        $js .= '$("#product-type").on("change",mhTogglePricingFields);';
        $js .= '});';
        wp_add_inline_script( 'jquery', $js );
    }
}
