<?php
/**
 * MH Wishlist Button Widget (Premium Text Swap Style)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class MH_Wishlist_Button_Widget extends Widget_Base {

    public function get_name() { return 'mh_wishlist_button'; }
    public function get_title() { return __( 'MH Wishlist Button', 'mh-plug' ); }
    public function get_icon() { return 'eicon-heart'; }
    public function get_categories() { return [ 'mh-plug-widgets' ]; }

    protected function register_controls() {

        /* ── CONTENT ── */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Wishlist Texts & Links', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'add_text', [
            'label'   => __( '"Add" Text', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Add to wishlist', 'mh-plug' ),
        ] );

        $this->add_control( 'browse_text', [
            'label'   => __( '"Browse" Text (After Added)', 'mh-plug' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Browse Wishlist', 'mh-plug' ),
        ] );

        $this->add_control( 'wishlist_page', [
            'label'       => __( 'Wishlist Page URL', 'mh-plug' ),
            'type'        => Controls_Manager::URL,
            'placeholder' => __( 'https://your-site.com/wishlist', 'mh-plug' ),
            'description' => __( 'Where should "Browse Wishlist" take the user?', 'mh-plug' ),
        ] );

        $this->add_responsive_control( 'align', [
            'label'   => __( 'Alignment', 'mh-plug' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => __( 'Left', 'mh-plug' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'mh-plug' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'mh-plug' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .mh-text-wishlist-wrapper' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /* ── STYLE ── */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Link Style', 'mh-plug' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'typography',
            'selector' => '{{WRAPPER}} .mh-text-wishlist-wrapper a',
        ] );

        $this->start_controls_tabs( 'style_tabs' );

        $this->start_controls_tab( 'tab_normal', [ 'label' => __( 'Normal', 'mh-plug' ) ] );
        $this->add_control( 'color_normal', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .mh-text-wishlist-wrapper a' => 'color: {{VALUE}}; text-decoration: none;' ],
        ] );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_hover', [ 'label' => __( 'Hover', 'mh-plug' ) ] );
        $this->add_control( 'color_hover', [
            'label'     => __( 'Text Color', 'mh-plug' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#d63638',
            'selectors' => [ '{{WRAPPER}} .mh-text-wishlist-wrapper a:hover' => 'color: {{VALUE}}; text-decoration: underline;' ],
        ] );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product();
        }

        if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 10px; color: #d63638;">Please create a product to preview the wishlist button.</div>';
            }
            return;
        }

        $product_id  = $product->get_id();
        $in_wishlist = function_exists('mh_wishlist_has_product') ? mh_wishlist_has_product($product_id) : false;
        
        $add_text    = ! empty( $settings['add_text'] ) ? $settings['add_text'] : 'Add to wishlist';
        $browse_text = ! empty( $settings['browse_text'] ) ? $settings['browse_text'] : 'Browse Wishlist';
        $browse_url  = ! empty( $settings['wishlist_page']['url'] ) ? $settings['wishlist_page']['url'] : '#';

        ?>
        <div class="mh-text-wishlist-wrapper" style="margin-top: 10px;">
            
            <a href="#" class="mh-text-wishlist-add" data-product-id="<?php echo esc_attr( $product_id ); ?>" style="display: <?php echo $in_wishlist ? 'none' : 'inline-block'; ?>; cursor: pointer; transition: 0.3s;">
                <?php echo esc_html( $add_text ); ?>
            </a>

            <a href="<?php echo esc_url( $browse_url ); ?>" class="mh-text-wishlist-browse" style="display: <?php echo $in_wishlist ? 'inline-block' : 'none'; ?>; transition: 0.3s;">
                <?php echo esc_html( $browse_text ); ?>
            </a>

        </div>

        <script>
            jQuery(document).ready(function($){
                // Ensure we only bind this once per widget
                $('.mh-text-wishlist-add').off('click.mhTextWishlist').on('click.mhTextWishlist', function(e){
                    e.preventDefault();
                    var $btn = $(this);
                    var $wrapper = $btn.closest('.mh-text-wishlist-wrapper');
                    var pid = $btn.data('product-id');

                    // If global wishlist config is missing, abort.
                    if(typeof mhWishlist === 'undefined') return;

                    // Add a tiny loading visual effect
                    $btn.css({'opacity': '0.5', 'pointer-events': 'none'});

                    $.post(mhWishlist.ajaxUrl, {
                        action: 'mh_wishlist_toggle',
                        product_id: pid,
                        security: mhWishlist.nonce
                    }, function(response) {
                        if(response.success && response.data.status === 'added') {
                            // The Magic Swap!
                            $btn.hide();
                            $wrapper.find('.mh-text-wishlist-browse').fadeIn(300);
                        } else {
                            // Revert if failed
                            $btn.css({'opacity': '1', 'pointer-events': 'auto'});
                        }
                    });
                });
            });
        </script>
        <?php
    }
}