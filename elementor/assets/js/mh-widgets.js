/**
 * MH Plug - Consolidated Widget JavaScript
 * Handles: Off-Canvas Cart, Wishlist Toggle, Quick View, Add to Cart, Accordion, Tabs.
 * Requires: mh_plug_ajax (ajax_url, wishlist_nonce, login_url) localized in elementor-loader.php
 */
(function ($) {
    'use strict';

    // ─────────────────────────────────────────────────────────────────
    // 0. TOAST NOTIFICATION (SNACK ALERT)
    // ─────────────────────────────────────────────────────────────────
    window.mhShowToast = function (message, type) {
        if ($('.mh-toast-container').length === 0) {
            $('body').append('<div class="mh-toast-container"></div>');
        }
        var icon = type === 'success' ? '<i class="fas fa-check-circle mh-toast-icon"></i>' : 
                   type === 'error' ? '<i class="fas fa-exclamation-circle mh-toast-icon"></i>' : 
                   '<i class="fas fa-info-circle mh-toast-icon"></i>';
        var toastClass = type === 'success' ? 'mh-toast-success' : type === 'error' ? 'mh-toast-error' : 'mh-toast-info';
        
        var $toast = $('<div class="mh-toast ' + toastClass + '">' +
            '<div style="display: flex; align-items: center;">' + icon + '<span>' + message + '</span></div>' +
            '</div>');
        
        $('.mh-toast-container').append($toast);
        
        // Trigger reflow to apply CSS transition
        $toast[0].offsetHeight;
        $toast.addClass('mh-toast-show');
        
        setTimeout(function () {
            $toast.removeClass('mh-toast-show');
            setTimeout(function () { $toast.remove(); }, 300);
        }, 3000);
    };

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
    // 4. WISHLIST – ALL .mh-advanced-wishlist-btn (Widget + Slider)
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click.mhSmartWishlist', '.mh-advanced-wishlist-btn', function (e) {
        var $btn = $(this);
        var behavior = $btn.data('behavior');
        if (behavior === 'browse' && $btn.hasClass('added')) { return; }
        e.preventDefault();

        // Get ajax config from either mh_plug_ajax or mhWishlist
        var ajaxUrl = '', nonce = '';
        if (typeof mh_plug_ajax !== 'undefined') {
            ajaxUrl = mh_plug_ajax.ajax_url;
            nonce   = mh_plug_ajax.wishlist_nonce;
        } else if (typeof mhWishlist !== 'undefined') {
            ajaxUrl = mhWishlist.ajaxUrl;
            nonce   = mhWishlist.nonce;
        }
        if (!ajaxUrl) { console.warn('MH Wishlist: No AJAX URL available'); return; }

        var pid = $btn.data('product-id');
        $btn.css({ opacity: '0.5', pointerEvents: 'none' });

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action:     'mh_wishlist_toggle',
                product_id: pid,
                security:   nonce
            },
            success: function (response) {
                if (response && response.success) {
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
                        $btn.find('.mh-icon-normal').hide();
                        $btn.find('.mh-icon-added').show();
                    } else {
                        $btn.removeClass('added');
                        if ($label.length) $label.text($btn.data('add-text'));
                        $btn.attr('href', '#');
                        $btn.find('.mh-icon-added').hide();
                        $btn.find('.mh-icon-normal').show();
                    }
                    $(document).trigger('mh_wishlist_updated', [status]);
                    if (window.mhShowToast) {
                        if (status === 'added') window.mhShowToast('Product added to wishlist', 'success');
                        else window.mhShowToast('Product removed from wishlist', 'info');
                    }
                } else {
                    var msg = (response && response.data && response.data.message) ? response.data.message : 'Please log in to add to wishlist.';
                    if (window.mhShowToast) window.mhShowToast(msg, 'error');
                }
            },
            error: function () {
                if (window.mhShowToast) window.mhShowToast('Server error. Please try again.', 'error');
            },
            complete: function () {
                $btn.css({ opacity: '1', pointerEvents: 'auto' });
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────
    // 5. PRODUCT GRID – WISHLIST TOGGLE (.mh-wishlist-btn)
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click.mhGridWishlist', '.mh-product-grid .mh-wishlist-btn', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation(); // Prevent mh-wishlist.js from double-firing

        // Get ajax config from either mh_plug_ajax or mhWishlist
        var ajaxUrl = '', nonce = '';
        if (typeof mh_plug_ajax !== 'undefined') {
            ajaxUrl = mh_plug_ajax.ajax_url;
            nonce   = mh_plug_ajax.wishlist_nonce;
        } else if (typeof mhWishlist !== 'undefined') {
            ajaxUrl = mhWishlist.ajaxUrl;
            nonce   = mhWishlist.nonce;
        }
        if (!ajaxUrl) { console.warn('MH Wishlist: No AJAX URL available'); return; }

        var $btn = $(this);
        $btn.css({ opacity: '0.5', pointerEvents: 'none' });

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action:     'mh_wishlist_toggle',
                product_id: $btn.data('product-id'),
                security:   nonce
            },
            success: function (response) {
                if (response && response.success) {
                    var status = response.data.status;
                    if (status === 'added') {
                        $btn.addClass('mh-added').find('.mh-icon-normal').hide();
                        $btn.find('.mh-icon-added').show();
                    } else {
                        $btn.removeClass('mh-added').find('.mh-icon-added').hide();
                        $btn.find('.mh-icon-normal').show();
                    }
                    $(document).trigger('mh_wishlist_updated', [status]);
                    if (window.mhShowToast) {
                        if (status === 'added') window.mhShowToast('Product added to wishlist', 'success');
                        else window.mhShowToast('Product removed from wishlist', 'info');
                    }
                } else {
                    var msg = (response && response.data && response.data.message) ? response.data.message : 'Failed to update wishlist';
                    if (window.mhShowToast) window.mhShowToast(msg, 'error');
                }
            },
            error: function () {
                if (window.mhShowToast) window.mhShowToast('Server error. Please try again.', 'error');
            },
            complete: function () {
                $btn.css({ opacity: '1', pointerEvents: 'auto' });
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
        var $form       = $btn.closest('form.cart');
        var qty         = $wrap.find('.mh-qty-input').val() || 1;
        var pid         = $btn.data('product-id');
        var checkoutUrl = $btn.data('checkout-url');

        $btn.css({ opacity: '0.6', pointerEvents: 'none' }).text('Processing...');

        if ($form.length) {
            // Update the form's action to send the user straight to checkout
            $form.attr('action', checkoutUrl);
            
            // Remove any existing add-to-cart hidden inputs to prevent duplicates
            $form.find('input[name="add-to-cart"]').remove();
            $form.find('input[name="mh_buy_now"]').remove();
            
            $form.append('<input type="hidden" name="add-to-cart" value="' + pid + '">');
            
            // Flag this request as a Buy Now request
            $form.append('<input type="hidden" name="mh_buy_now" value="1">');
            
            // Because attribute widgets might live OUTSIDE the form, and programmatic
            // submission can bypass jQuery submit hooks, we manually inject the attributes here.
            $('.mh-woo-attribute-select, .mh-custom-attr-select').each(function() {
                var $sel = $(this);
                var val  = $sel.val();
                if (!val || val === '') return;
                
                var rawName = $sel.attr('name') || '';
                
                // If it's already mh_custom_attr format from Combo frontend
                if (rawName.indexOf('mh_custom_attr') === 0) {
                    $form.find('input[name="' + rawName + '"]').remove();
                    $form.append('<input type="hidden" name="' + rawName + '" value="' + val + '">');
                } else {
                    // It's from Elementor attributes widget
                    var attrKey = rawName.replace(/^attribute_/, '');
                    if (!attrKey) attrKey = rawName;
                    
                    $form.find('input[name="mh_custom_attr[' + attrKey + ']"]').remove();
                    $form.append('<input type="hidden" name="mh_custom_attr[' + attrKey + ']" value="' + val + '">');
                }
            });
            
            // Submit the form
            $form.submit();
        } else {
            var separator   = checkoutUrl.indexOf('?') !== -1 ? '&' : '?';
            var directUrl   = checkoutUrl + separator + 'add-to-cart=' + pid + '&quantity=' + qty;
            window.location.href = directUrl;
        }
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
// MH Plug - Cache-Proof Compare Engine & Dynamic Table
// =======================================================
jQuery(document).ready(function($) {
    
    // 1. Sync UI (Header Badge & Buttons)
    function mhSyncCompareUI() {
        let compareList = JSON.parse(localStorage.getItem('mh_compare_list')) || [];
        compareList = compareList.map(id => parseInt(id, 10)); // Ensure all are numbers
        $('.mh-compare-count').text(compareList.length);
        $('.mh-compare-btn').each(function() {
            let pid = parseInt($(this).data('product-id'), 10);
            if (compareList.includes(pid)) {
                $(this).addClass('added');
                $(this).find('.mh-compare-text').text('Added to Compare');
            } else {
                $(this).removeClass('added');
                $(this).find('.mh-compare-text').text('Add to Compare');
            }
        });
    }
    mhSyncCompareUI();

    // 2. Click to Add/Remove from Compare Button
    $(document).on('click', '.mh-compare-btn', function(e) {
        e.preventDefault();
        let productId = parseInt($(this).data('product-id'), 10);
        if (!productId) return;

        let compareList = JSON.parse(localStorage.getItem('mh_compare_list')) || [];
        compareList = compareList.map(id => parseInt(id, 10)); // Ensure all are numbers

        if (compareList.includes(productId)) {
            compareList = compareList.filter(id => id !== productId);
            if (window.mhShowToast) window.mhShowToast('Removed from compare list', 'info');
        } else {
            if (compareList.length >= 4) {
                if (window.mhShowToast) window.mhShowToast('Failed: You can only compare up to 4 products at a time!', 'error');
                return;
            }
            compareList.push(productId);
            if (window.mhShowToast) window.mhShowToast('Product successfully added to compare', 'success');
        }
        localStorage.setItem('mh_compare_list', JSON.stringify(compareList));
        mhSyncCompareUI();
    });

    // 3. 🚀 Fetch and Render the Compare Table via AJAX
    function mhRenderCompareTable() {
        let tableWrapper = $('.mh-compare-table-wrapper');
        if (tableWrapper.length === 0) return; // Only run if the table widget is on the page!

        let compareList = JSON.parse(localStorage.getItem('mh_compare_list')) || [];
        
        if (compareList.length === 0) {
            tableWrapper.html('<div class="mh-compare-empty"><h3>No products to compare</h3><p>Return to the shop to add products.</p></div>');
            return;
        }

        // Send AJAX request to our elementor-loader.php hook
        $.ajax({
            url: mh_plug_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mh_get_compare_table',
                product_ids: compareList
            },
            success: function(response) {
                if (response.success) {
                    tableWrapper.html(response.data.html);
                } else {
                    tableWrapper.html(response.data.html);
                }
            }
        });
    }
    mhRenderCompareTable(); // Trigger the table render on page load

    // 4. Click the 'X' remove button INSIDE the table
    $(document).on('click', '.mh-remove-compare', function(e) {
        e.preventDefault();
        let productId = $(this).data('product-id');
        let compareList = JSON.parse(localStorage.getItem('mh_compare_list')) || [];
        
        compareList = compareList.filter(id => id !== productId);
        localStorage.setItem('mh_compare_list', JSON.stringify(compareList));
        
        mhSyncCompareUI(); // Update badges
        mhRenderCompareTable(); // Refresh the table layout
    });

});

// =============================================================
// MH COMBO PRODUCTS WIDGET — Responsive Layout Engine
// =============================================================
(function ($) {
    'use strict';

    /**
     * Boot Slick on a single .mh-layout-carousel element.
     */
    function mhInitComboCarousel($carousel) {
        if (!$carousel.length) { return; }
        if (typeof $.fn.slick === 'undefined') {
            console.warn('MH Combo Carousel: Slick not loaded.');
            return;
        }
        if ($carousel.hasClass('slick-initialized')) {
            $carousel.slick('unslick');
        }

        var cols        = parseInt($carousel.data('columns'), 10)         || 3;
        var colsTablet  = parseInt($carousel.data('columns-tablet'), 10)  || 2;
        var colsMobile  = parseInt($carousel.data('columns-mobile'), 10)  || 1;
        var autoplay    = $carousel.data('autoplay') === true || $carousel.data('autoplay') === 'true';
        var autoplaySpd = parseInt($carousel.data('autoplay-speed'), 10)  || 3000;
        var arrows      = $carousel.data('arrows') !== false && $carousel.data('arrows') !== 'false';
        var dots        = $carousel.data('dots')   !== false && $carousel.data('dots')   !== 'false';

        $carousel.slick({
            slidesToShow   : cols,
            slidesToScroll : 1,
            arrows         : arrows,
            dots           : dots,
            autoplay       : autoplay,
            autoplaySpeed  : autoplaySpd,
            adaptiveHeight : false,
            responsive     : [
                {
                    breakpoint : 1025,
                    settings   : { slidesToShow: colsTablet }
                },
                {
                    breakpoint : 768,
                    settings   : { slidesToShow: colsMobile }
                }
            ]
        });
    }

    /**
     * Dynamically swaps layouts (Grid/List/Carousel) across breakpoints
     */
    function mhHandleResponsiveComboLayouts() {
        var winWidth = $(window).width();
        
        $('.mh-responsive-combo').each(function() {
            var $wrapper = $(this);
            var desktop  = $wrapper.data('layout-desktop') || 'grid';
            var tablet   = $wrapper.data('layout-tablet')  || desktop;
            var mobile   = $wrapper.data('layout-mobile')  || tablet;

            var targetLayout = desktop;
            if (winWidth <= 767) {
                targetLayout = mobile;
            } else if (winWidth <= 1024) {
                targetLayout = tablet;
            }

            var currentLayout = $wrapper.data('current-layout');
            
            if (currentLayout !== targetLayout) {
                // If switching away from carousel, gracefully destroy it first
                if ($wrapper.hasClass('slick-initialized')) {
                    $wrapper.slick('unslick');
                }

                $wrapper.removeClass('mh-layout-grid mh-layout-list mh-layout-carousel mh-slick-carousel');
                
                $wrapper.addClass('mh-layout-' + targetLayout);
                $wrapper.data('current-layout', targetLayout);

                if (targetLayout === 'carousel') {
                    $wrapper.addClass('mh-slick-carousel');
                    // Tiny timeout to allow DOM to paint the block layout before Slick calculates track width
                    setTimeout(function() {
                        mhInitComboCarousel($wrapper);
                    }, 50);
                }
            }
        });
    }

    // ── Elementor frontend hook (editor + live page) ──
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/mh_combo_products.default',
            function ($scope) {
                var $wrapper = $scope.find('.mh-responsive-combo');
                if ($wrapper.length) {
                    mhHandleResponsiveComboLayouts();
                } else {
                    mhInitComboCarousel($scope.find('.mh-layout-carousel'));
                }
            }
        );
    });

    // ── DOM-ready fallback ──
    $(document).ready(function () {
        mhHandleResponsiveComboLayouts();
    });

    // ── Live resize monitor ──
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(mhHandleResponsiveComboLayouts, 150);
    });

    // ─────────────────────────────────────────────────────────────────
    // FAQ / ACCORDION WIDGET
    // Extracted from: mh-faq-widget.php render()
    // ─────────────────────────────────────────────────────────────────
    $(document).on('ready elementor/frontend/init', function() {
        $('.mh-faq-container').each(function() {
            var $container = $(this);
            var behavior = $container.data('behavior'); // 'accordion' or 'toggle'

            $container.find('.mh-faq-header').on('click', function() {
                var $item = $(this).parent('.mh-faq-item');
                var is_active = $item.hasClass('active');

                if (behavior === 'accordion') {
                    // Close all other items
                    $container.find('.mh-faq-item').removeClass('active');
                    $container.find('.mh-faq-header').attr('aria-expanded', 'false');
                }

                // Toggle current item
                if (!is_active) {
                    $item.addClass('active');
                    $(this).attr('aria-expanded', 'true');
                } else {
                    $item.removeClass('active');
                    $(this).attr('aria-expanded', 'false');
                }
            });
        });
    });
    // ─────────────────────────────────────────────────────────────────
    // PRODUCT FILTER WIDGET (DROPDOWN)
    // ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.mh-filter-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $dropdown = $(this).closest('.mh-dropdown-container');
        $('.mh-dropdown-container').not($dropdown).removeClass('mh-dropdown-open'); // Close others
        $dropdown.toggleClass('mh-dropdown-open');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.mh-dropdown-container').length) {
            $('.mh-dropdown-container').removeClass('mh-dropdown-open');
        }
    });

}(jQuery));