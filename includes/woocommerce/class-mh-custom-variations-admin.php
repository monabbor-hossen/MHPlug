<?php
/**
 * MH_Custom_Variations_Admin
 *
 * @package MH_Plug
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MH_Plug_Custom_Variations_Admin {

    public static function init() {
        add_filter( 'woocommerce_product_data_tabs', [ __CLASS__, 'add_product_data_tab' ] );
        add_action( 'woocommerce_product_data_panels', [ __CLASS__, 'render_panel' ] );
        add_action( 'woocommerce_process_product_meta', [ __CLASS__, 'save_custom_variations' ] );
        add_action( 'wp_ajax_mh_save_custom_variations', [ __CLASS__, 'ajax_save_variations' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_assets' ] );
    }

    public static function admin_assets() {
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->id, [ 'product', 'edit-product' ], true ) ) {
            return;
        }

        // CSS
        $css  = '.mh-custom-variation-row select { margin-right: 5px; margin-bottom: 5px; }';
        $css .= '.mh-custom-variation-row .select2-container { margin-right: 5px; margin-bottom: 5px; min-width: 170px !important; text-align: left; }';
        $css .= '.mh-remove-variation-row:hover { color: #800 !important; }';
        $css .= '.mh-select-dropdown-fix { min-width: 170px !important; border: 1px solid #8c8f94 !important; box-sizing: border-box !important; }';
        $css .= '.mh-select-dropdown-fix .select2-search--dropdown { padding: 6px !important; box-sizing: border-box !important; width: 100% !important; }';
        $css .= '.mh-select-dropdown-fix .select2-search__field { box-sizing: border-box !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 4px 6px !important; border: 1px solid #8c8f94 !important; }';
        $css .= '.enable_variation.show_if_variable { display: inline-block !important; }';
        wp_add_inline_style( 'woocommerce_admin_styles', $css );

        // JS
        $nonce = wp_create_nonce( 'mh_save_custom_variations_nonce' );
        $js = 'jQuery(document).ready(function($){function initMHSelect(){var opts={minimumResultsForSearch:1,width:\'120px\',dropdownCssClass:\'mh-select-dropdown-fix\'};if($.fn.selectWoo){$(\'.mh-enhanced-select\').selectWoo(opts);}else if($.fn.select2){$(\'.mh-enhanced-select\').select2(opts);}}initMHSelect();$(document).on(\'click\',\'#mh-add-custom-var-rule\',function(e){e.preventDefault();var template=$(\'#mh-variation-row-template\').html();if(!template){alert(\'Template missing!\');return;}var uniqueIndex=Date.now();var newRow=template.replace(/{index}/g,uniqueIndex);$(\'#mh-custom-variations-tbody\').append(newRow);initMHSelect();});$(document).on(\'click\',\'.mh-remove-variation-row\',function(e){e.preventDefault();if(confirm(\'Remove this rule?\')){$(this).closest(\'tr\').remove();}});$(document).on(\'click\',\'.mh-save-variations-btn\',function(e){e.preventDefault();var $btn=$(this);var $spinner=$btn.next(\'.mh-save-spinner\');var formData=$(\'#mh_custom_variations_panel :input\').serialize();var productId=$(\'#post_ID\').val();$btn.prop(\'disabled\',true);$spinner.addClass(\'is-active\');$.post(ajaxurl,{action:\'mh_save_custom_variations\',product_id:productId,form_data:formData,nonce:\'' . esc_js( $nonce ) . '\'},function(response){$spinner.removeClass(\'is-active\');if(response.success){$btn.text(\'Saved!\').removeClass(\'button-primary\').addClass(\'button-secondary\');setTimeout(function(){$btn.text(\'Save Variations\').removeClass(\'button-secondary\').addClass(\'button-primary\').prop(\'disabled\',false);},2000);}else{alert(\'Error saving variations.\');$btn.prop(\'disabled\',false);}});});});';

        wp_add_inline_script( 'jquery', $js );
    }

    public static function add_product_data_tab( $tabs ) {
        $tabs['mh_custom_variations'] = [
            'label'    => __( 'Custom Variations', 'mh-plug-ecommerce-builder-widgets' ),
            'target'   => 'mh_custom_variations_panel',
            'class'    => [ 'show_if_simple', 'show_if_combo' ],
            'priority' => 70,
        ];
        return $tabs;
    }

    public static function render_panel() {
        global $post;
        $product = wc_get_product( $post->ID );
        
        echo '<div id="mh_custom_variations_panel" class="panel woocommerce_options_panel hidden show_if_simple show_if_combo">';
        
        if ( ! $product ) {
            echo '</div>';
            return;
        }

        // Only for simple and combo
        if ( ! in_array( $product->get_type(), [ 'simple', 'combo' ] ) ) {
            echo '</div>';
            return;
        }

        $all_attributes = $product->get_attributes();
        $attributes     = [];
        
        foreach ( $all_attributes as $attr ) {
            if ( $attr->get_variation() ) {
                $attributes[] = $attr;
            }
        }

        if ( empty( $attributes ) ) {
            echo '<p style="padding:15px;">' . __( 'Please add attributes and check "Used for variations" in the "Attributes" tab first.', 'mh-plug-ecommerce-builder-widgets' ) . '</p>';
            echo '</div>';
            return;
        }

        $saved_rules = get_post_meta( $post->ID, '_mh_custom_variation_rules', true );
        if ( ! is_array( $saved_rules ) ) {
            $saved_rules = [];
        }

        // Generate the Repeater UI
        ?>
        <div id="mh-custom-variations-repeater" style="padding: 15px;">
            <table class="widefat" style="margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Attributes', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Regular Price', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                        <th><?php esc_html_e( 'Sale Price', 'mh-plug-ecommerce-builder-widgets' ); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="mh-custom-variations-tbody">
                    <?php
                    if ( ! empty( $saved_rules ) ) {
                        foreach ( $saved_rules as $index => $rule ) {
                            self::render_row( $index, $attributes, $rule );
                        }
                    }
                    ?>
                </tbody>
            </table>
            <button type="button" class="button button-primary" id="mh-add-custom-var-rule"><?php esc_html_e( 'Add New Rule', 'mh-plug-ecommerce-builder-widgets' ); ?></button>
            <button type="button" class="button button-primary mh-save-variations-btn" style="margin-left: 10px;"><?php esc_html_e( 'Save Variations', 'mh-plug-ecommerce-builder-widgets' ); ?></button>
            <span class="mh-save-spinner spinner" style="float: none; margin-top: 4px;"></span>
        </div>

        <!-- Hidden Template -->
        <script type="text/template" id="mh-variation-row-template">
            <?php self::render_row( '{index}', $attributes, [] ); ?>
        </script>






        <?php
        echo '</div>';
    }

    private static function render_row( $index, $attributes, $data ) {
        $regular_price = isset( $data['regular_price'] ) ? $data['regular_price'] : '';
        $sale_price    = isset( $data['sale_price'] ) ? $data['sale_price'] : '';
        ?>
        <tr class="mh-custom-variation-row">
            <td>
                <?php foreach ( $attributes as $attribute ) : 
                    $attribute_name = $attribute->get_name();
                    $options        = $attribute->get_options();
                    
                    $selected_val = isset( $data['attributes'][ $attribute_name ] ) ? $data['attributes'][ $attribute_name ] : '';
                    ?>
                    <select class="mh-enhanced-select" style="width: 150px;" name="mh_custom_vars[<?php echo esc_attr( $index ); ?>][attributes][<?php echo esc_attr( $attribute_name ); ?>]" data-placeholder="<?php echo esc_attr( wc_attribute_label( $attribute_name ) ); ?>">
                        <option value=""><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></option>
                        <?php
                        if ( $attribute->is_taxonomy() ) {
                            foreach ( $options as $term_id ) {
                                $term = get_term( $term_id, $attribute_name );
                                if ( $term ) {
                                    // Case-insensitive match: saved value may be lowercased from older saves.
                                    $is_selected = ( strcasecmp( $selected_val, $term->slug ) === 0 );
                                    echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $is_selected, true, false ) . '>' . esc_html( $term->name ) . '</option>';
                                }
                            }
                        } else {
                            foreach ( $options as $option ) {
                                // Case-insensitive match: saved value may be lowercased from older saves.
                                $is_selected = ( strcasecmp( $selected_val, $option ) === 0 );
                                echo '<option value="' . esc_attr( $option ) . '" ' . selected( $is_selected, true, false ) . '>' . esc_html( $option ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                <?php endforeach; ?>
            </td>
            <td>
                <input type="text" name="mh_custom_vars[<?php echo esc_attr( $index ); ?>][regular_price]" placeholder="0.00" value="<?php echo esc_attr( $regular_price ); ?>">
            </td>
            <td>
                <input type="text" name="mh_custom_vars[<?php echo esc_attr( $index ); ?>][sale_price]" placeholder="0.00" value="<?php echo esc_attr( $sale_price ); ?>">
            </td>
            <td>
                <button type="button" class="button mh-remove-variation-row" style="color: #b32d2e;">Remove</button>
            </td>
        </tr>
        <?php
    }

    /**
     * AJAX handler: save custom variation rules without a full page reload.
     */
    public static function ajax_save_variations() {
        check_ajax_referer( 'mh_save_custom_variations_nonce', 'nonce' );

        if ( ! current_user_can( 'edit_products' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        if ( ! $product_id ) {
            wp_send_json_error( 'Invalid Product ID' );
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        parse_str( wp_unslash( $_POST['form_data'] ), $parsed_data );

        if ( isset( $parsed_data['mh_custom_vars'] ) && is_array( $parsed_data['mh_custom_vars'] ) ) {
            // Re-use the same processing logic as the normal save.
            $rules_to_save = [];

            foreach ( $parsed_data['mh_custom_vars'] as $row ) {
                if ( empty( $row['attributes'] ) || ! is_array( $row['attributes'] ) ) {
                    continue;
                }

                ksort( $row['attributes'] );

                // Sanitize keys before processing to prevent untrusted keys reaching the database.
                $sanitized_row_attributes = [];
                foreach ( $row['attributes'] as $attr_key => $attr_val ) {
                    $sanitized_row_attributes[ sanitize_key( $attr_key ) ] = $attr_val;
                }

                // Preserve original case for admin UI selected() matching.
                $clean_attributes = array_map( 'wc_clean', $sanitized_row_attributes );

                // Only lowercase for the combination_string (frontend JS matching).
                $combination_string = implode( '|', array_map( 'strtolower', $clean_attributes ) );

                $rules_to_save[] = [
                    'attributes'         => $clean_attributes,
                    'combination_string' => $combination_string,
                    'regular_price'      => wc_clean( wp_unslash( $row['regular_price'] ) ),
                    'sale_price'         => wc_clean( wp_unslash( $row['sale_price'] ) ),
                ];
            }

            update_post_meta( $product_id, '_mh_custom_variation_rules', array_values( $rules_to_save ) );
        } else {
            delete_post_meta( $product_id, '_mh_custom_variation_rules' );
        }

        wp_send_json_success( 'Saved successfully' );
    }

    public static function save_custom_variations( $post_id ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( ! isset( $_POST['mh_custom_vars'] ) || ! is_array( $_POST['mh_custom_vars'] ) ) {
            delete_post_meta( $post_id, '_mh_custom_variation_rules' );
            return;
        }

        $rules_to_save = [];

        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        foreach ( $_POST['mh_custom_vars'] as $row ) {
            if ( empty( $row['attributes'] ) || ! is_array( $row['attributes'] ) ) {
                continue;
            }

            // CRITICAL: sort the attributes alphabetically by key (mirrors JS selectedValues.sort())
            ksort( $row['attributes'] );

            // Sanitize keys before processing to prevent untrusted keys reaching the database.
            $sanitized_row_attributes = [];
            foreach ( $row['attributes'] as $attr_key => $attr_val ) {
                $sanitized_row_attributes[ sanitize_key( $attr_key ) ] = $attr_val;
            }

            // Preserve original case for admin UI selected() matching.
            $clean_attributes = array_map( 'wc_clean', $sanitized_row_attributes );

            // Only lowercase for the combination_string — the frontend JS
            // always lowercases selected values before building the combo
            // string, so the combination_string must use the same case.
            $combination_string = implode( '|', array_map( 'strtolower', $clean_attributes ) );

            $rules_to_save[] = [
                'attributes'         => $clean_attributes,
                'combination_string' => $combination_string,
                'regular_price'      => wc_clean( wp_unslash( $row['regular_price'] ) ),
                'sale_price'         => wc_clean( wp_unslash( $row['sale_price'] ) ),
            ];
        }

        // array_values() re-indexes to sequential integers (0,1,2…).
        // Without this, deleting a row leaves gaps (0,2,5…) which causes
        // wp_json_encode / json_encode to output a JS object {} instead of
        // an array [], breaking .find() in the frontend JavaScript.
        update_post_meta( $post_id, '_mh_custom_variation_rules', array_values( $rules_to_save ) );
    }


}
