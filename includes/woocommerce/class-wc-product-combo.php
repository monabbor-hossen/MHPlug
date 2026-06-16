<?php
/**
 * MH_Plug_Product_Combo — Custom WooCommerce Product Type: Combo
 *
 * Registers "combo" as a native WooCommerce product type by extending
 * WC_Product. This file must be loaded AFTER WooCommerce is initialised
 * (use the `woocommerce_loaded` action, not `plugins_loaded`).
 *
 * @package MH_Plug
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class MH_Plug_Product_Combo
 *
 * Represents a "Combo" product: a curated bundle of individual products
 * that can be sold together at a custom price.
 */
class MH_Plug_Product_Combo extends WC_Product {

    /**
     * WooCommerce product type slug.
     * Must match the term slug registered in the product_type taxonomy.
     *
     * @var string
     */
    protected $product_type = 'combo';

    /**
     * Constructor.
     *
     * Passes the product ID / post object to the parent, then forces the
     * internal product_type property so WooCommerce always recognises this
     * object as a combo, regardless of how it was instantiated.
     *
     * @param int|WC_Product|WP_Post $product Product ID, post object, or WC_Product instance.
     */
    public function __construct( $product = 0 ) {
        $this->product_type = 'combo';
        parent::__construct( $product );
    }

    /**
     * Returns the product type slug.
     *
     * Used internally by WooCommerce (e.g. `$product->is_type('combo')`,
     * `wc_get_product_type()`, template loaders, etc.).
     *
     * @return string
     */
    public function get_type() {
        return 'combo';
    }

    /**
     * Returns the IDs of the products that make up this combo.
     *
     * The value is stored as a JSON-encoded array in post meta under
     * `_mh_combo_products`. Falls back to an empty array if nothing is saved.
     *
     * @return int[] Array of product IDs.
     */
    public function get_combo_product_ids() {
        $raw = get_post_meta( $this->get_id(), '_mh_combo_products', true );

        if ( empty( $raw ) ) {
            return [];
        }

        // Support array directly (new behavior)
        if ( is_array( $raw ) ) {
            return array_filter( array_map( 'absint', $raw ) );
        }

        // Handle JSON arrays (previous behavior)
        $decoded = json_decode( $raw, true );

        if ( is_array( $decoded ) ) {
            return array_filter( array_map( 'absint', $decoded ) );
        }

        // Legacy: comma-separated IDs
        return array_filter( array_map( 'absint', explode( ',', $raw ) ) );
    }

    /**
     * Convenience wrapper: return fully-loaded WC_Product objects for the combo items.
     *
     * @return WC_Product[] Associative array keyed by product ID.
     */
    public function get_combo_products() {
        $products = [];

        foreach ( $this->get_combo_product_ids() as $id ) {
            $product = wc_get_product( $id );
            if ( $product instanceof WC_Product ) {
                $products[ $id ] = $product;
            }
        }

        return $products;
    }

    /**
     * Mark combo product as purchasable.
     *
     * @return bool
     */
    public function is_purchasable() {
        return true;
    }
}
