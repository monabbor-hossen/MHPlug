/**
 * MH Woo Scripts
 * Handles Add to Cart quantity, Wishlist AJAX, and Quick View Popups.
 */
(function ($) {
    'use strict';

    // ─────────────────────────────────────────────────────────────────────────────
    // 1. ADD TO CART QUANTITY CONTROLS
    // ─────────────────────────────────────────────────────────────────────────────
    function initMhATC($scope) {
        var $context = $scope || $(document);
        $context.find('.mh-qty-minus').off('click.mhAtc').on('click.mhAtc', function () {
            var $input = $(this).closest('.mh-qty-wrapper').find('.mh-qty-input');
            var current = parseInt($input.val(), 10) || 1;
            var min     = parseInt($input.attr('min'), 10) || 1;
            if (current > min) { $input.val(current - 1).trigger('change'); }
        });
        $context.find('.mh-qty-plus').off('click.mhAtc').on('click.mhAtc', function () {
            var $input = $(this).closest('.mh-qty-wrapper').find('.mh-qty-input');
            var current = parseInt($input.val(), 10) || 1;
            var max     = parseInt($input.attr('max'), 10);
            if (isNaN(max) || max < 0 || current < max) { $input.val(current + 1).trigger('change'); }
        });
        $context.find('.mh-qty-input').off('change.mhAtc').on('change.mhAtc', function () {
            var $input  = $(this);
            var val     = parseInt($input.val(), 10);
            var min     = parseInt($input.attr('min'), 10) || 1;
            var max     = parseInt($input.attr('max'), 10);
            if (isNaN(val) || val < min) { $input.val(min); } 
            else if (!isNaN(max) && max > 0 && val > max) { $input.val(max); }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 2. WISHLIST AJAX HANDLER (Fixed for Both Grids & Single Products)
    // ─────────────────────────────────────────────────────────────────────────────
    function initMhWishlist() {
        // Target BOTH the single product button and the product grid button
        $(document).off('click.mhWishlist', '.mh-wishlist-btn, .mh-advanced-wishlist-btn').on('click.mhWishlist', '.mh-wishlist-btn, .mh-advanced-wishlist-btn', function(e) {
            e.preventDefault();
            
            var $btn       = $(this);
            var productId  = $btn.data('product-id');
            // Safely get nonce from button OR the global ajax object we injected
            var nonce      = $btn.data('nonce') || (typeof mh_plug_ajax !== 'undefined' ? mh_plug_ajax.wishlist_nonce : '');
            var isLoggedIn = $btn.data('logged-in'); 
            
            if ( isLoggedIn === false || isLoggedIn === 'false' ) {
                alert("Please log in to add items to your wishlist.");
                if ( typeof mh_plug_ajax !== 'undefined' && mh_plug_ajax.login_url ) { window.location.href = mh_plug_ajax.login_url; }
                return;
            }

            if ( $btn.hasClass('mh-loading') ) return;
            $btn.addClass('mh-loading').css('opacity', '0.5');

            $.ajax({
                url: typeof mh_plug_ajax !== 'undefined' ? mh_plug_ajax.ajax_url : '',
                type: 'POST',
                data: { action: 'mh_wishlist_toggle', product_id: productId, security: nonce },
                success: function(response) {
                    $btn.removeClass('mh-loading').css('opacity', '1');
                    
                    if ( response.success ) {
                        var isAdded = (response.data.status === 'added');

                        // 1. Update Single Product Button UI
                        if ($btn.hasClass('mh-wishlist-btn')) {
                            if (isAdded) {
                                $btn.addClass('mh-in-wishlist');
                                $btn.find('.mh-wishlist-btn-text').text($btn.data('text-added') || 'Added to Wishlist');
                            } else {
                                $btn.removeClass('mh-in-wishlist');
                                $btn.find('.mh-wishlist-btn-text').text($btn.data('text-normal') || 'Add to Wishlist');
                            }
                        }

                        // 2. Update Product Grid Button UI
                        if ($btn.hasClass('mh-advanced-wishlist-btn')) {
                            if (isAdded) {
                                $btn.addClass('added');
                                $btn.find('.mh-icon-normal').hide();
                                $btn.find('.mh-icon-added').show();
                            } else {
                                $btn.removeClass('added');
                                $btn.find('.mh-icon-normal').show();
                                $btn.find('.mh-icon-added').hide();
                            }
                        }

                        // 3. Globally Update All Header Wishlist Counters on the page
                        if (response.data.count !== undefined) {
                            $('.mh-wishlist-count').text(response.data.count);
                        }

                    } else {
                        // Server rejected it (e.g., security check failed)
                        alert( response.data.message || "Failed to update wishlist." );
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        }
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
    // 3. QUICK VIEW AJAX MODAL ENGINE
    // ─────────────────────────────────────────────────────────────────────────────
    function initMhQuickView() {
        if ($('#mh-quick-view-modal').length === 0) {
            $('body').append(`
                <div id="mh-quick-view-modal" class="mh-qv-overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.85); z-index:99999; justify-content:center; align-items:center;">
                    <div class="mh-qv-content" style="background:#fff; width:90%; max-width:950px; max-height:90vh; overflow-y:auto; position:relative; border-radius:12px; padding:30px; box-shadow:0 10px 40px rgba(0,0,0,0.3);">
                        <button class="mh-qv-close" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; color:#888; transition:0.3s;"><i class="fas fa-times"></i></button>
                        <div class="mh-qv-body" style="width:100%;"></div>
                        <div class="mh-qv-loader" style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin fa-3x" style="color:#004265;"></i></div>
                    </div>
                </div>
            `);
        }

        $(document).on('click', '.mh-quick-view-trigger', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var productId = $btn.data('product-id');
            var templateId = $btn.data('template-id');

            var $modal = $('#mh-quick-view-modal');
            var $body = $modal.find('.mh-qv-body');
            var $loader = $modal.find('.mh-qv-loader');

            $body.empty().hide();
            $loader.show();
            $modal.css('display', 'flex').hide().fadeIn(300);

            $.ajax({
                url: typeof mh_plug_ajax !== 'undefined' ? mh_plug_ajax.ajax_url : '',
                type: 'POST',
                data: { action: 'mh_quick_view', product_id: productId, template_id: templateId },
                success: function(response) {
                    $loader.hide();
                    if (response.success) {
                        $body.html(response.data.html).fadeIn(300);
                        initMhATC($body);
                        if (typeof wc_add_to_cart_variation_params !== 'undefined') {
                            $body.find('.variations_form').each(function() { $(this).wc_variation_form(); });
                        }
                    } else {
                        $body.html('<p>Error loading product details.</p>').fadeIn();
                    }
                },
                error: function() {
                    $loader.hide();
                    $body.html('<p>Server error. Please try again.</p>').fadeIn();
                }
            });
        });

        $(document).on('click', '.mh-qv-close, .mh-qv-overlay', function(e) {
            if ($(e.target).hasClass('mh-qv-overlay') || $(e.target).closest('.mh-qv-close').length) {
                $('#mh-quick-view-modal').fadeOut(300);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // 4. INITIALIZATION TRIGGERS
    // ─────────────────────────────────────────────────────────────────────────────
    $(document).ready(function () {
        initMhATC(null);
        initMhWishlist();
        initMhQuickView(); 
    });

    $(window).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/mh_woo_add_to_cart.default', function ($scope) { initMhATC($scope); });
        }
    });

})(jQuery);