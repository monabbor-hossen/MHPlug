<?php
/**
 * MH Wishlist Table Widget
 *
 * Renders the current user/session's wishlist as a data table.
 * Columns: Image, Product Name, Price, Stock Status, Add to Cart, Remove.
 * Provides style controls for headers, rows, and action buttons.
 *
 * @package MH_Plug
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class MH_Wishlist_Table_Widget extends Widget_Base {

    public function get_name()        { return 'mh_wishlist_table'; }
    public function get_title()       { return __( 'MH Wishlist Table', 'mh-plug' ); }
    public function get_icon()        { return 'eicon-table'; }
    public function get_categories()  { return [ 'mh-plug-widgets' ]; }
    public function get_keywords()    { return [ 'wishlist', 'table', 'woocommerce', 'mh' ]; }

    protected function register_controls() {

        /* ── CONTENT: Column Visibility ── */
        $this->start_controls_section( 'columns_section', [
            'label' => __( 'Columns', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_image', [
            'label'        => __( 'Show Product Image', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug' ),
            'label_off'    => __( 'No', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_price', [
            'label'        => __( 'Show Price', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug' ),
            'label_off'    => __( 'No', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_stock', [
            'label'        => __( 'Show Stock Status', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug' ),
            'label_off'    => __( 'No', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_add_to_cart', [
            'label'        => __( 'Show Add to Cart', 'mh-plug' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'mh-plug' ),
            'label_off'    => __( 'No', 'mh-plug' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'empty_message', [
            'label'   => __( 'Empty Wishlist Message', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Your wishlist is empty.', 'mh-plug' ),
        ] );

        $this->end_controls_section();

        /* ── STYLE: Table Header ── */
        $this->start_controls_section( 'style_header', [
            'label' => __( 'Table Header', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'header_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} table.mh-wishlist-table thead' => 'background: {{VALUE}};' ],
        ] );

        $this->add_control( 'header_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} table.mh-wishlist-table thead th' => 'color: {{VALUE}};' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'header_typography',
            'selector' => '{{WRAPPER}} table.mh-wishlist-table thead th',
        ] );

        $this->end_controls_section();

        /* ── STYLE: Table Rows ── */
        $this->start_controls_section( 'style_rows', [
            'label' => __( 'Table Rows', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'row_bg', [
            'label'     => __( 'Row Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} table.mh-wishlist-table tbody tr' => 'background: {{VALUE}};' ],
        ] );

        $this->add_control( 'row_hover_bg', [
            'label'     => __( 'Row Hover Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f9f9f9',
            'selectors' => [ '{{WRAPPER}} table.mh-wishlist-table tbody tr:hover' => 'background: {{VALUE}};' ],
        ] );

        $this->add_control( 'row_text_color', [
            'label'     => __( 'Row Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} table.mh-wishlist-table tbody td' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'row_border_color', [
            'label'     => __( 'Row Divider Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f0f0f1',
            'selectors' => [ '{{WRAPPER}} table.mh-wishlist-table tbody tr' => 'border-bottom-color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: Add to Cart Button ── */
        $this->start_controls_section( 'style_cart_btn', [
            'label'     => __( 'Add to Cart Button', 'mh-plug' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_add_to_cart' => 'yes' ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'cart_btn_typography',
            'selector' => '{{WRAPPER}} .mh-wl-col-cart .button',
        ] );

        $this->add_control( 'cart_btn_bg', [
            'label'     => __( 'Background Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1d2327',
            'selectors' => [ '{{WRAPPER}} .mh-wl-col-cart .button' => 'background: {{VALUE}};' ],
        ] );

        $this->add_control( 'cart_btn_color', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .mh-wl-col-cart .button' => 'color: {{VALUE}};' ],
        ] );

        $this->add_control( 'cart_btn_bg_hover', [
            'label'     => __( 'Hover Background', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-wl-col-cart .button:hover' => 'background: {{VALUE}};' ],
        ] );

        $this->add_control( 'cart_btn_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 6, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .mh-wl-col-cart .button' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->add_responsive_control( 'cart_btn_padding', [
            'label'      => __( 'Padding', 'mh-plug' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .mh-wl-col-cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE: Table Container ── */
        $this->start_controls_section( 'style_table', [
            'label' => __( 'Table Container', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'table_border',
            'selector' => '{{WRAPPER}} table.mh-wishlist-table',
        ] );

        $this->add_control( 'table_border_radius', [
            'label'      => __( 'Border Radius', 'mh-plug' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} table.mh-wishlist-table' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( ! function_exists( 'mh_wishlist_get_items' ) ) {
            echo '<p>' . esc_html__( 'Wishlist feature is not enabled.', 'mh-plug' ) . '</p>';
            return;
        }

        $product_ids  = mh_wishlist_get_items();
        $nonce        = wp_create_nonce( 'mh_wishlist_nonce' );
        $show_image   = 'yes' === $settings['show_image'];
        $show_price   = 'yes' === $settings['show_price'];
        $show_stock   = 'yes' === $settings['show_stock'];
        $show_cart    = 'yes' === $settings['show_add_to_cart'];
        $empty_msg    = ! empty($settings['empty_message']) ? $settings['empty_message'] : __( 'Your wishlist is empty.', 'mh-plug' );

        ?>
        <div class="mh-wishlist-table-wrapper">
            
            <div class="mh-wishlist-empty" style="<?php echo empty( $product_ids ) ? 'display:block;' : 'display:none;'; ?> text-align: center; padding: 40px;">
                <i class="far fa-heart" style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
                <p style="font-size: 18px; color: #777;"><?php echo esc_html( $empty_msg ); ?></p>
            </div>

            <?php if ( ! empty( $product_ids ) ) : ?>
                <table class="mh-wishlist-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr>
                            <th class="mh-wl-col-remove" style="padding: 15px;"></th>
                            <?php if ( $show_image ) : ?>
                                <th class="mh-wl-col-image" style="padding: 15px;"><?php esc_html_e( 'Product', 'mh-plug' ); ?></th>
                            <?php endif; ?>
                            <th class="mh-wl-col-name" style="padding: 15px;"><?php esc_html_e( 'Name', 'mh-plug' ); ?></th>
                            <?php if ( $show_price ) : ?>
                                <th class="mh-wl-col-price" style="padding: 15px;"><?php esc_html_e( 'Price', 'mh-plug' ); ?></th>
                            <?php endif; ?>
                            <?php if ( $show_stock ) : ?>
                                <th class="mh-wl-col-stock" style="padding: 15px;"><?php esc_html_e( 'Stock', 'mh-plug' ); ?></th>
                            <?php endif; ?>
                            <?php if ( $show_cart ) : ?>
                                <th class="mh-wl-col-cart" style="padding: 15px;"><?php esc_html_e( 'Action', 'mh-plug' ); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $product_ids as $product_id ) :
                            $product = wc_get_product( $product_id );
                            if ( ! $product ) continue;

                            $stock      = $product->get_stock_status();
                            $badge_map  = [
                                'instock'    => [ 'class' => 'mh-wl-stock-instock',    'label' => __( 'In Stock', 'mh-plug' ), 'color' => '#0f834d' ],
                                'outofstock' => [ 'class' => 'mh-wl-stock-outofstock', 'label' => __( 'Out of Stock', 'mh-plug' ), 'color' => '#d63638' ],
                                'onbackorder'=> [ 'class' => 'mh-wl-stock-onbackorder','label' => __( 'On Backorder', 'mh-plug' ), 'color' => '#e27730' ],
                            ];
                            $badge = $badge_map[ $stock ] ?? $badge_map['instock'];
                        ?>
                        <tr data-product-id="<?php echo esc_attr( $product_id ); ?>" style="border-bottom: 1px solid #eee;">
                            <td class="mh-wl-col-remove" style="padding: 15px;">
                                <span class="mh-wl-remove-btn" data-product-id="<?php echo esc_attr( $product_id ); ?>" style="cursor: pointer; color: #d63638; font-size: 18px;">
                                    <i class="fas fa-times"></i>
                                </span>
                            </td>
                            <?php if ( $show_image ) : ?>
                            <td class="mh-wl-col-image" style="padding: 15px;">
                                <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
                                    <?php echo $product->get_image( [ 60, 60 ] ); ?>
                                </a>
                            </td>
                            <?php endif; ?>
                            <td class="mh-wl-col-name" style="padding: 15px;">
                                <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" style="text-decoration: none; color: inherit; font-weight: 500;">
                                    <?php echo esc_html( $product->get_name() ); ?>
                                </a>
                            </td>
                            <?php if ( $show_price ) : ?>
                            <td class="mh-wl-col-price" style="padding: 15px;">
                                <?php echo $product->get_price_html(); ?>
                            </td>
                            <?php endif; ?>
                            <?php if ( $show_stock ) : ?>
                            <td class="mh-wl-col-stock" style="padding: 15px;">
                                <span style="color: <?php echo esc_attr($badge['color']); ?>; font-weight: 600;">
                                    <?php echo esc_html( $badge['label'] ); ?>
                                </span>
                            </td>
                            <?php endif; ?>
                            <?php if ( $show_cart ) : ?>
                            <td class="mh-wl-col-cart" style="padding: 15px;">
                                <?php
                                if ( $product->is_in_stock() ) {
                                    echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                                        sprintf(
                                            '<a href="%s" data-product_id="%d" data-product_sku="%s" class="button add_to_cart_button ajax_add_to_cart">%s</a>',
                                            esc_url( $product->add_to_cart_url() ),
                                            esc_attr( $product_id ),
                                            esc_attr( $product->get_sku() ),
                                            esc_html( $product->add_to_cart_text() )
                                        ),
                                        $product
                                    );
                                } else {
                                    echo '<span style="color: #d63638;">' . esc_html__( 'Out of Stock', 'mh-plug' ) . '</span>';
                                }
                                ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <script>
            jQuery(document).ready(function($) {
                var mhAjaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
                var mhNonce   = '<?php echo esc_attr( $nonce ); ?>';

                $('.mh-wl-remove-btn').on('click', function(e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var $row = $btn.closest('tr');
                    var productId = $btn.data('product-id');

                    $btn.css({'opacity': '0.5', 'pointer-events': 'none'});

                    // Send AJAX request to toggle (remove) the item
                    $.post(mhAjaxUrl, {
                        action: 'mh_wishlist_toggle',
                        product_id: productId,
                        security: mhNonce
                    }, function(response) {
                        if(response.success) {
                            // Fade out and remove the row
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                
                                // Check if table is empty now
                                if ($('.mh-wishlist-table tbody tr').length === 0) {
                                    $('.mh-wishlist-table').fadeOut(200, function() {
                                        $('.mh-wishlist-empty').fadeIn(300);
                                    });
                                }
                            });
                            
                            // Trigger event to update the header counters
                            $(document).trigger('mh_wishlist_updated', ['removed']);
                        } else {
                            $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                            alert('Error removing item.');
                        }
                    });
                });
            });
        </script>
        <?php
    }

}
