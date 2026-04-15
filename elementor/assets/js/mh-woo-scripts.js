/**
 * MH Woo Scripts
 *
 * Handles the custom ± quantity buttons for the MH Custom Add to Cart widget,
 * and the AJAX functionality for the MH Wishlist Button widget.
 */
(function ($) {
    'use strict';

    // ─────────────────────────────────────────────────────────────────────────────
    // 1. ADD TO CART QUANTITY CONTROLS
    // ─────────────────────────────────────────────────────────────────────────────
    
    /**
     * Initialise quantity controls inside a given container.
     * Called once on DOMReady and again after Elementor re-renders widgets
     * inside the editor (elementor/frontend/init).
     */
    function initMhATC($scope) {
        var $context = $scope || $(document);

        /* ── Minus button ──────────────────────────────── */
        $context.find('.mh-qty-minus').off('click.mhAtc').on('click.mhAtc', function () {
            // Target parent wrapper for safety against theme wrappers
            var $input = $(this).closest('.mh-qty-wrapper').find('.mh-qty-input');
            var current = parseInt($input.val(), 10) || 1;
            var min     = parseInt($input.attr('min'), 10) || 1;

            if (current > min) {
                $input.val(current - 1).trigger('change');
            }
        });

        /* ── Plus button ───────────────────────────────── */
        $context.find('.mh-qty-plus').off('click.mhAtc').on('click.mhAtc', function () {
            // Target parent wrapper for safety against theme wrappers
            var $input = $(this).closest('.mh-qty-wrapper').find('.mh-qty-input');
            var current = parseInt($input.val(), 10) || 1;
            var max     = parseInt($input.attr('max'), 10);

            // Respect stock limit; if max is NaN or less than 0 (unlimited), skip the check
            if (isNaN(max) || max < 0 || current < max) {
                $input.val(current + 1).trigger('change');
            }
        });

        /* ── Sanitise direct input ─────────────────────── */
        $context.find('.mh-qty-input').off('change.mhAtc').on('change.mhAtc', function () {
            var $input  = $(this);
            var val     = parseInt($input.val(), 10);
            var min     = parseInt($input.attr('min'), 10) || 1;
            var max     = parseInt($input.attr('max'), 10);

            if (isNaN(val) || val < min) {
                $input.val(min);
            } else if (!isNaN(max) && max > 0 && val > max) {
                $input.val(max);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 2. WISHLIST AJAX HANDLER
    // ─────────────────────────────────────────────────────────────────────────────
    
    /**
     * Initialise the Wishlist click event using Event Delegation.
     * Event delegation ($(document).on) ensures it works even if Elementor 
     * reloads the widget dynamically in the editor.
     */
    function initMhWishlist() {
        $(document).off('click.mhWishlist', '.mh-wishlist-btn').on('click.mhWishlist', '.mh-wishlist-btn', function(e) {
            e.preventDefault();
            
            var $btn       = $(this);
            var productId  = $btn.data('product-id');
            var nonce      = $btn.data('nonce');
            var isLoggedIn = $btn.data('logged-in');
            
            // 1. Account Check: If not logged in, redirect to login page
            if ( isLoggedIn !== true && isLoggedIn !== 'true' ) {
                alert("Please log in to add items to your wishlist.");
                // Ensure the localized variable exists before redirecting
                if ( typeof mh_plug_ajax !== 'undefined' && mh_plug_ajax.login_url ) {
                    window.location.href = mh_plug_ajax.login_url;
                }
                return;
            }

            // Prevent double-clicking while waiting for server response
            if ( $btn.hasClass('mh-loading') ) return;
            
            // Add a visual loading state
            $btn.addClass('mh-loading').css('opacity', '0.5');

            // 2. Send the data to the server
            $.ajax({
                url: typeof mh_plug_ajax !== 'undefined' ? mh_plug_ajax.ajax_url : '',
                type: 'POST',
                data: {
                    action: 'mh_wishlist_toggle',
                    product_id: productId,
                    security: nonce
                },
                success: function(response) {
                    $btn.removeClass('mh-loading').css('opacity', '1');
                    
                    if ( response.success ) {
                        // Toggle the visual state based on server response
                        if ( response.data.status === 'added' ) {
                            $btn.addClass('mh-in-wishlist');
                            $btn.find('.mh-wishlist-btn-text').text( $btn.data('text-added') );
                        } else {
                            $btn.removeClass('mh-in-wishlist');
                            $btn.find('.mh-wishlist-btn-text').text( $btn.data('text-normal') );
                        }
                    } else {
                        alert( response.data.message );
                    }
                },
                error: function() {
                    $btn.removeClass('mh-loading').css('opacity', '1');
                    alert("Server error. Please try again.");
                }
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 3. INITIALIZATION TRIGGERS
    // ─────────────────────────────────────────────────────────────────────────────

    /* ── Fire on standard page load ─────────────────────────────── */
    $(document).ready(function () {
        initMhATC(null);
        initMhWishlist();
    });

    /* ── Fire inside Elementor editor when a widget is rendered ── */
    $(window).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            
            // Re-init Add to Cart quantity buttons when its specific widget is dragged/dropped
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/mh_woo_add_to_cart.default',
                function ($scope) {
                    initMhATC($scope);
                }
            );
            
            // Wishlist doesn't strictly require a specific element_ready hook because 
            // we used $(document).on() event delegation, but placing it here ensures 
            // it binds correctly in edge cases.
        }
    });

})(jQuery);