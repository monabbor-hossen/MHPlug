/**
 * MH Plug - WooCommerce Wishlist JS
 *
 * Handles Add / Remove interactions via AJAX.
 * mhWishlist object (localized from PHP):
 *   - ajaxUrl  : WordPress AJAX endpoint
 *   - nonce    : Primary nonce (per-page; each button also carries its own data-nonce)
 */
(function ($) {
    'use strict';

    /* ── Helpers ─────────────────────────────────────────── */

    function setLoading($btn, isLoading) {
        $btn.toggleClass('mh-loading', isLoading);
    }

    function setInWishlist($btn, inWishlist) {
        $btn.toggleClass('mh-in-wishlist', inWishlist);

        var $icon = $btn.find('i');
        var $text = $btn.find('.mh-wishlist-btn-text');

        if (inWishlist) {
            $icon.removeClass('fa-regular').addClass('fa-solid');
            $text.text(mhWishlist.i18n.removeLabel);
            $btn.attr('title', mhWishlist.i18n.removeLabel)
                .attr('aria-label', mhWishlist.i18n.removeLabel);
        } else {
            $icon.removeClass('fa-solid').addClass('fa-regular');
            $text.text(mhWishlist.i18n.addLabel);
            $btn.attr('title', mhWishlist.i18n.addLabel)
                .attr('aria-label', mhWishlist.i18n.addLabel);
        }
    }

    function updateCount(count) {
        // Update any wishlist count indicator on the page (e.g. in nav/header)
        $('.mh-wishlist-count').text(count);
    }

    /* ── Toggle (Add / Remove from product buttons) ─────── */

    $(document).on('click keydown', '.mh-wishlist-btn', function (e) {
        // Allow keyboard activation
        if (e.type === 'keydown' && e.which !== 13 && e.which !== 32) {
            return;
        }
        e.preventDefault();

        var $btn       = $(this);
        var productId  = $btn.data('product-id');
        var nonce      = $btn.data('nonce') || mhWishlist.nonce;
        var inWishlist = $btn.hasClass('mh-in-wishlist');
        var action     = inWishlist ? 'mh_wishlist_remove' : 'mh_wishlist_add';

        if ($btn.hasClass('mh-loading')) {
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url:    mhWishlist.ajaxUrl,
            method: 'POST',
            data: {
                action:     action,
                nonce:      nonce,
                product_id: productId
            },
            success: function (res) {
                if (res.success) {
                    setInWishlist($btn, !inWishlist);
                    updateCount(res.data.count);

                    // If removing from within the wishlist table, animate row out
                    if (inWishlist) {
                        var $row = $btn.closest('tr[data-product-id="' + productId + '"]');
                        if ($row.length) {
                            $row.fadeOut(320, function () {
                                $row.remove();
                                // Show empty state if no rows left
                                if ($('table.mh-wishlist-table tbody tr:visible').length === 0) {
                                    mhWishlistShowEmpty();
                                }
                            });
                        }

                        // Also un-highlight any page-level wishlist buttons for this product
                        $('.mh-wishlist-btn[data-product-id="' + productId + '"]').not($btn).each(function () {
                            setInWishlist($(this), false);
                        });
                    }
                }
            },
            error: function () {
                // Silent fail — button reverts state visually on reload
            },
            complete: function () {
                setLoading($btn, false);
            }
        });
    });

    /* ── Remove button inside wishlist table ─────────────── */

    $(document).on('click', '.mh-wl-remove-btn', function (e) {
        e.preventDefault();
        var $btn      = $(this);
        var productId = $btn.data('product-id');
        var nonce     = $btn.data('nonce') || mhWishlist.nonce;

        $btn.addClass('mh-loading');

        $.ajax({
            url:    mhWishlist.ajaxUrl,
            method: 'POST',
            data: {
                action:     'mh_wishlist_remove',
                nonce:      nonce,
                product_id: productId
            },
            success: function (res) {
                if (res.success) {
                    var $row = $btn.closest('tr');
                    $row.fadeOut(320, function () {
                        $row.remove();
                        updateCount(res.data.count);

                        if ($('table.mh-wishlist-table tbody tr:visible').length === 0) {
                            mhWishlistShowEmpty();
                        }
                    });

                    // Update any page-level buttons for this product
                    $('.mh-wishlist-btn[data-product-id="' + productId + '"]').each(function () {
                        setInWishlist($(this), false);
                    });
                }
            },
            complete: function () {
                $btn.removeClass('mh-loading');
            }
        });
    });

    /* ── Empty state helper ──────────────────────────────── */

    function mhWishlistShowEmpty() {
        var $wrapper = $('.mh-wishlist-table-wrapper');
        $wrapper.html(
            '<div class="mh-wishlist-empty">' +
                '<i class="fa-regular fa-heart"></i>' +
                '<p>' + mhWishlist.i18n.emptyMessage + '</p>' +
            '</div>'
        );
    }

})(jQuery);
