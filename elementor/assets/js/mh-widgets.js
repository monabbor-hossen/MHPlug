/**
 * MH Plug - Consolidated Widget JavaScript
 * Handles: Off-Canvas Cart, Wishlist Toggle, Quick View, Add to Cart, Accordion, Tabs.
 * Requires: mh_plug_ajax (ajax_url, wishlist_nonce, login_url) localized in elementor-loader.php
 */
(function ($) {
    'use strict';

    // ─────────────────────────────────────────────────────────────────
    // 1. OFF-CANVAS MINI CART
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-open-mini-cart', function (e) {
        e.preventDefault();
        var $panel = $('.mh-mini-cart-panel');
        var $overlay = $('.mh-cart-overlay');
        $panel.addClass('mh-open');
        $overlay.addClass('mh-open');
        $('body').css('overflow', 'hidden');
    });

    $(document).on('click', '.mh-cart-close, .mh-cart-overlay', function () {
        $('.mh-mini-cart-panel').removeClass('mh-open');
        $('.mh-cart-overlay').removeClass('mh-open');
        $('body').css('overflow', 'auto');
    });

    $('body').on('added_to_cart', function (event, fragments) {
        if (fragments && fragments['div.widget_shopping_cart_content']) {
            $('.mh-offcanvas-content .widget_shopping_cart_content')
                .html(fragments['div.widget_shopping_cart_content']);
        }
        $('.mh-mini-cart-panel').addClass('mh-open');
        $('.mh-cart-overlay').addClass('mh-open');
        $('body').css('overflow', 'hidden');
    });

    // ─────────────────────────────────────────────────────────────────
    // 2. HEADER CART COUNT UPDATE
    // ─────────────────────────────────────────────────────────────────
    $('body').on('added_to_cart removed_from_cart updated_cart_totals', function () {
        if (typeof mh_plug_ajax === 'undefined') return;
        $.post(mh_plug_ajax.ajax_url, { action: 'mh_get_cart_count' }, function (response) {
            if (response.success) {
                $('.mh-cart-count').text(response.data).css('transform', 'scale(1.3)');
                setTimeout(function () { $('.mh-cart-count').css('transform', 'scale(1)'); }, 200);
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────
    // 3. HEADER WISHLIST COUNT UPDATE
    // ─────────────────────────────────────────────────────────────────
    $(document).on('mh_wishlist_updated', function (e, status) {
        var $counter = $('.mh-wishlist-count');
        var currentCount = parseInt($counter.text()) || 0;
        if (status === 'added') { currentCount++; }
        else if (status === 'removed' && currentCount > 0) { currentCount--; }
        $counter.text(currentCount).css('transform', 'scale(1.3)');
        setTimeout(function () { $counter.css('transform', 'scale(1)'); }, 200);
    });

    // ─────────────────────────────────────────────────────────────────
    // 4. WISHLIST BUTTON WIDGET – TOGGLE
    // ─────────────────────────────────────────────────────────────────
    // Scoped to the dedicated wishlist button widget (not product grid)
    $(document).on('click.mhSmartWishlist', '.mh-advanced-wishlist-wrap .mh-advanced-wishlist-btn', function (e) {
        var $btn = $(this);
        var behavior = $btn.data('behavior');
        if (behavior === 'browse' && $btn.hasClass('added')) { return; }
        e.preventDefault();
        if (typeof mh_plug_ajax === 'undefined') return;

        var pid = $btn.data('product-id');
        $btn.css({ opacity: '0.5', pointerEvents: 'none' });

        $.post(mh_plug_ajax.ajax_url, {
            action:     'mh_wishlist_toggle',
            product_id: pid,
            security:   mh_plug_ajax.wishlist_nonce
        }, function (response) {
            $btn.css({ opacity: '1', pointerEvents: 'auto' });
            if (response.success) {
                var status = response.data.status;
                var $label = $btn.find('.mh-wishlist-label');
                if (status === 'added') {
                    $btn.addClass('added');
                    if (behavior === 'browse') {
                        if ($label.length) $label.text($btn.data('browse-text'));
                        $btn.attr('href', $btn.data('wishlist-url'));
                    } else {
                        if ($label.length) $label.text($btn.data('remove-text'));
                    }
                } else {
                    $btn.removeClass('added');
                    if ($label.length) $label.text($btn.data('add-text'));
                    $btn.attr('href', '#');
                }
                $(document).trigger('mh_wishlist_updated', [status]);
            } else {
                alert(response.data ? response.data.message : 'Please log in to add to wishlist.');
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────
    // 5. PRODUCT GRID – WISHLIST TOGGLE
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-product-grid .mh-advanced-wishlist-btn', function (e) {
        e.preventDefault();
        if (typeof mh_plug_ajax === 'undefined') return;
        var $btn = $(this);
        $btn.css({ opacity: '0.5', pointerEvents: 'none' });
        $.post(mh_plug_ajax.ajax_url, {
            action:     'mh_wishlist_toggle',
            product_id: $btn.data('product-id'),
            security:   mh_plug_ajax.wishlist_nonce
        }, function (response) {
            $btn.css({ opacity: '1', pointerEvents: 'auto' });
            if (response.success) {
                var status = response.data.status;
                if (status === 'added') {
                    $btn.addClass('added').find('.mh-icon-normal').hide();
                    $btn.find('.mh-icon-added').show();
                } else {
                    $btn.removeClass('added').find('.mh-icon-added').hide();
                    $btn.find('.mh-icon-normal').show();
                }
                $(document).trigger('mh_wishlist_updated', [status]);
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────
    // 6. QUICK VIEW MODAL – INIT & OPEN
    // ─────────────────────────────────────────────────────────────────
    if ($('#mh-quick-view-modal').length === 0) {
        $('body').append(
            '<div id="mh-quick-view-modal" class="mh-qv-overlay">' +
            '<div class="mh-qv-content">' +
            '<span class="mh-qv-close"><i class="fas fa-times"></i></span>' +
            '<div class="mh-qv-body"></div>' +
            '</div></div>'
        );
    }

    $(document).on('click', '.mh-quick-view-trigger', function (e) {
        e.preventDefault();
        if (typeof mh_plug_ajax === 'undefined') return;

        var product_id  = $(this).attr('data-product-id');
        var template_id = $(this).attr('data-template-id');
        var $modal = $('#mh-quick-view-modal');
        var $body  = $modal.find('.mh-qv-body');

        $body.html('<div style="text-align:center;padding:50px;"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
        $modal.addClass('mh-open');

        $.post(mh_plug_ajax.ajax_url, {
            action:      'mh_quick_view_load',
            product_id:  product_id,
            template_id: template_id
        }, function (response) {
            if (!response.success) return;
            $body.html(response.data);

            // Re-init WooCommerce variation form
            if (typeof $.fn.wc_variation_form !== 'undefined') {
                $body.find('.variations_form').each(function () { $(this).wc_variation_form(); });
            }
            // Re-init Elementor handlers
            if (typeof window.elementorFrontend !== 'undefined') {
                window.elementorFrontend.elementsHandler.runReadyTrigger($body);
            }
            // Inject ± qty buttons
            $body.find('.quantity').each(function () {
                var $qw = $(this);
                if ($qw.find('.mh-qty-btn').length === 0) {
                    $qw.prepend('<span class="mh-qty-btn mh-minus">-</span>');
                    $qw.append('<span class="mh-qty-btn mh-plus">+</span>');
                }
            });
            // Boot Slick gallery in popup
            setTimeout(function () {
                if (!$.fn.slick) return;
                var $main  = $body.find('.mh-gallery-main-viewport');
                var $thumb = $body.find('.mh-gallery-thumb-slider');
                if ($main.length && !$main.hasClass('slick-initialized')) {
                    $main.slick({
                        slidesToShow: 1, slidesToScroll: 1, arrows: true, fade: true,
                        prevArrow: $body.find('.mh-main-prev'),
                        nextArrow: $body.find('.mh-main-next'),
                        asNavFor: $thumb.length ? $thumb : null
                    });
                }
                if ($thumb.length && !$thumb.hasClass('slick-initialized')) {
                    $thumb.slick({
                        slidesToShow: 4, slidesToScroll: 1, arrows: true,
                        focusOnSelect: true,
                        asNavFor: $main.length ? $main : null,
                        prevArrow: $body.find('.mh-thumb-prev'),
                        nextArrow: $body.find('.mh-thumb-next')
                    });
                }
            }, 150);
        });
    });

    // Close modal
    $(document).on('click', '.mh-qv-close, .mh-qv-close *', function () {
        $('#mh-quick-view-modal').removeClass('mh-open');
    });
    $(document).on('click', '.mh-qv-overlay', function (e) {
        if ($(e.target).hasClass('mh-qv-overlay')) {
            $('#mh-quick-view-modal').removeClass('mh-open');
        }
    });

    // ─────────────────────────────────────────────────────────────────
    // 7. QTY ± BUTTONS (Quick View & Add to Cart widget)
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-qty-btn', function () {
        var $qtyInput = $(this).siblings('.qty');
        var currentVal = parseFloat($qtyInput.val()) || 0;
        if ($(this).hasClass('mh-plus')) {
            var max = parseFloat($qtyInput.attr('max'));
            if (!isNaN(max) && currentVal >= max) {
                $qtyInput.val(max);
            } else {
                $qtyInput.val(currentVal + 1);
            }
        } else if (currentVal > 1) {
            $qtyInput.val(currentVal - 1);
        }
        $qtyInput.trigger('change');
    });

    // ─────────────────────────────────────────────────────────────────
    // 8. CUSTOM AJAX ADD TO CART (Quick View form)
    // ─────────────────────────────────────────────────────────────────
    $(document).off('submit', '.mh-qv-add-to-cart-wrap form.cart')
               .on('submit',  '.mh-qv-add-to-cart-wrap form.cart', function (e) {
        e.preventDefault();
        if (typeof mh_plug_ajax === 'undefined') return;

        var $form = $(this);
        var $btn  = $form.find('button[type="submit"]');
        var $wrap = $form.closest('.mh-qv-add-to-cart-wrap');
        var productId = $wrap.attr('data-product-id') ||
                        $form.find('input[name="product_id"]').val() ||
                        $btn.attr('value') || $btn.val();

        if (!productId) { $btn.text('ID Error'); return; }

        var missingAttributes = false;
        $form.find('.mh-qv-attr-select').each(function () {
            if ($(this).val() === '') { missingAttributes = true; }
        });
        if (missingAttributes) {
            $btn.text('Please select options');
            setTimeout(function () { $btn.text('Add to cart'); }, 2000);
            return;
        }

        var formData = $form.serialize() + '&action=mh_qv_add_to_cart&product_id=' + productId;
        $btn.addClass('loading').text('Adding...');

        $.post(mh_plug_ajax.ajax_url, formData, function (response) {
            if (response && response.fragments) {
                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);
                $btn.text('Added to Cart!');
                setTimeout(function () {
                    $('#mh-quick-view-modal').removeClass('mh-open');
                    $btn.removeClass('loading').text('Add to cart');
                }, 1500);
            } else if (response && response.success === false) {
                var msg = response.data && response.data.message ? response.data.message : 'Cart Error';
                $btn.removeClass('loading').text(msg);
                setTimeout(function () { $btn.text('Add to cart'); }, 3000);
            } else {
                $btn.removeClass('loading').text('Cart Error');
                setTimeout(function () { $btn.text('Add to cart'); }, 3000);
            }
        }).fail(function () {
            $btn.removeClass('loading').text('Server Error');
            setTimeout(function () { $btn.text('Add to cart'); }, 3000);
        });
    });

    // ─────────────────────────────────────────────────────────────────
    // 9. WISHLIST TABLE – REMOVE BUTTON
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-wl-remove-btn', function (e) {
        e.preventDefault();
        if (typeof mh_plug_ajax === 'undefined') return;
        var $btn = $(this);
        var $row = $btn.closest('tr');
        var productId = $btn.data('product-id');
        $btn.css({ opacity: '0.5', pointerEvents: 'none' });
        $.post(mh_plug_ajax.ajax_url, {
            action:     'mh_wishlist_toggle',
            product_id: productId,
            security:   mh_plug_ajax.wishlist_nonce
        }, function (response) {
            if (response.success) {
                $row.fadeOut(300, function () {
                    $(this).remove();
                    if ($('.mh-wishlist-table tbody tr').length === 0) {
                        $('.mh-wishlist-table').fadeOut(200, function () {
                            $('.mh-wishlist-empty').fadeIn(300);
                        });
                    }
                });
                $(document).trigger('mh_wishlist_updated', ['removed']);
            } else {
                $btn.css({ opacity: '1', pointerEvents: 'auto' });
                alert('Error removing item.');
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────
    // 10. ADD TO CART WIDGET – QTY + BUY NOW
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-atc-form .mh-qty-plus, .mh-atc-form .mh-qty-minus', function () {
        var $qty = $(this).closest('.mh-qty-wrapper').find('.qty');
        var currentVal = parseFloat($qty.val());
        var max        = parseFloat($qty.attr('max'));
        var min        = parseFloat($qty.attr('min')) || 0;
        var step       = parseFloat($qty.attr('step')) || 1;
        if (isNaN(currentVal)) currentVal = 0;

        if ($(this).is('.mh-qty-plus')) {
            if (!isNaN(max) && currentVal >= max) { $qty.val(max); }
            else { $qty.val(currentVal + step); }
        } else {
            if (currentVal > min) { $qty.val(currentVal - step); }
        }
        $qty.trigger('change');
    });

    $(document).on('click', '.mh-buy-now-btn', function (e) {
        e.preventDefault();
        var $btn        = $(this);
        var $wrap       = $btn.closest('.mh-atc-wrap');
        var qty         = $wrap.find('.mh-qty-input').val() || 1;
        var pid         = $btn.data('product-id');
        var checkoutUrl = $btn.data('checkout-url');
        var separator   = checkoutUrl.indexOf('?') !== -1 ? '&' : '?';
        var directUrl   = checkoutUrl + separator + 'add-to-cart=' + pid + '&quantity=' + qty;
        $btn.css({ opacity: '0.6', pointerEvents: 'none' }).text('Processing...');
        window.location.href = directUrl;
    });

    // ─────────────────────────────────────────────────────────────────
    // 11. PRODUCT DATA ACCORDION
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-accordion-container .mh-accordion-header', function () {
        var $item      = $(this).parent('.mh-accordion-item');
        var $content   = $(this).next('.mh-accordion-content');
        var $container = $(this).closest('.mh-accordion-container');

        if ($item.hasClass('active')) {
            $item.removeClass('active');
            $content.slideUp(300);
        } else {
            $container.find('.mh-accordion-item').removeClass('active');
            $container.find('.mh-accordion-content').slideUp(300);
            $item.addClass('active');
            $content.slideDown(300);
        }
    });

    // ─────────────────────────────────────────────────────────────────
    // 12. PRODUCT DATA TABS
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-product-data-tabs .mh-tab-btn', function () {
        var $this     = $(this);
        var targetID  = $this.data('target');
        var $container = $this.closest('[class*="mh-tabs-container-"]');

        $container.find('.mh-tab-btn').removeClass('mh-active-tab');
        $container.find('.mh-tab-content-panel').removeClass('mh-active-content');
        $this.addClass('mh-active-tab');
        $('#' + targetID).addClass('mh-active-content');
    });

}(jQuery));

// =======================================================
// MH Plug - Cache-Proof Compare Engine (Local Storage)
// =======================================================
jQuery(document).ready(function($) {
    
    // 1. Function to sync the UI with Local Storage
    function mhSyncCompareUI() {
        let compareList = JSON.parse(localStorage.getItem('mh_compare_list')) || [];
        
        // Update all Header Counter badges
        $('.mh-compare-count').text(compareList.length);

        // Update all Compare Buttons (Grid & Single Product)
        $('.mh-compare-btn').each(function() {
            let pid = $(this).data('product-id');
            if (compareList.includes(pid)) {
                $(this).addClass('added');
                $(this).find('.mh-compare-text').text('Added to Compare'); // For single product text
            } else {
                $(this).removeClass('added');
                $(this).find('.mh-compare-text').text('Add to Compare');
            }
        });
    }

    // Initialize on page load
    mhSyncCompareUI();

    // 2. Handle Click Events
    $(document).on('click', '.mh-compare-btn', function(e) {
        e.preventDefault();
        
        let productId = $(this).data('product-id');
        if (!productId) return;

        let compareList = JSON.parse(localStorage.getItem('mh_compare_list')) || [];

        // If already in list, remove it
        if (compareList.includes(productId)) {
            compareList = compareList.filter(id => id !== productId);
        } 
        // If not in list, add it (Limit to 4 products to prevent UI breaking)
        else {
            if (compareList.length >= 4) {
                alert('You can only compare up to 4 products at a time!');
                return;
            }
            compareList.push(productId);
        }

        // Save back to browser storage and instantly update the UI
        localStorage.setItem('mh_compare_list', JSON.stringify(compareList));
        mhSyncCompareUI();
    });
});