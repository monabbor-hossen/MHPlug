/**
 * MH Plug — WooCommerce Wishlist JS
 *
 * Handles Add / Remove toggle interactions via AJAX.
 *
 * Localised object `mhWishlist` (from PHP wp_localize_script):
 *   ajaxUrl  — WordPress admin-ajax.php endpoint
 *   nonce    — Fallback nonce (each button also carries data-nonce)
 *   i18n     — { addLabel, removeLabel, emptyMessage }
 */
(function ($) {
    'use strict';

    /* ══════════════════════════════════════════════════════════
       Helpers
       ══════════════════════════════════════════════════════════ */

    /**
     * Set or clear the loading state on a wishlist button.
     * While loading: icon pulses, double-clicks are blocked.
     *
     * @param {jQuery} $btn
     * @param {boolean} isLoading
     */
    function setLoading($btn, isLoading) {
        $btn.toggleClass('mh-adding', isLoading);
    }

    /**
     * Visually toggle the "in wishlist" state on a button.
     * Swaps the FontAwesome icon class and updates aria labels.
     *
     * @param {jQuery} $btn
     * @param {boolean} inWishlist
     */
    function setAdded($btn, inWishlist) {
        $btn.toggleClass('mh-added', inWishlist);

        var $icon = $btn.find('i');

        if (inWishlist) {
            // Solid (filled) heart  ──  remove outline variant first
            $icon.removeClass('fa-regular').addClass('fa-solid');
            $btn.attr({
                title:        mhWishlist.i18n.removeLabel,
                'aria-label': mhWishlist.i18n.removeLabel
            });
        } else {
            // Outline heart  ──  remove solid variant first
            $icon.removeClass('fa-solid').addClass('fa-regular');
            $btn.attr({
                title:        mhWishlist.i18n.addLabel,
                'aria-label': mhWishlist.i18n.addLabel
            });
        }
    }

    /**
     * Update any wishlist count badges scattered around the page.
     *
     * @param {number} count
     */
    function updateCount(count) {
        $('.mh-wishlist-count').text(count);
    }

    /* ══════════════════════════════════════════════════════════
       Main toggle click handler
       Attached to document so it works on AJAX-loaded product grids.
       ══════════════════════════════════════════════════════════ */

    $(document).on('click keydown', '.mh-wishlist-btn', function (e) {
        // Allow Space / Enter for keyboard users; ignore other keys.
        if (e.type === 'keydown' && e.which !== 13 && e.which !== 32) {
            return;
        }
        e.preventDefault();

        var $btn       = $(this);
        var productId  = $btn.data('product-id');
        var nonce      = $btn.data('nonce') || mhWishlist.nonce;
        var inWishlist = $btn.hasClass('mh-added');

        // ── Guard: block double-click while AJAX is in flight ──
        if ($btn.hasClass('mh-adding')) {
            return;
        }

        setLoading($btn, true);

        $.ajax({
            url:    mhWishlist.ajaxUrl,
            method: 'POST',
            data: {
                action:     'mh_toggle_wishlist',   // unified toggle action
                nonce:      nonce,
                product_id: productId
            },
            success: function (res) {
                if (!res.success) {
                    return; // server returned an error — leave button state intact
                }

                var wasAdded = res.data.added; // true = just added, false = just removed

                // ── Update the clicked button ───────────────────
                setAdded($btn, wasAdded);

                // ── Sync all other buttons for the same product ─
                // (e.g. a loop card button + a single product button on the same page)
                $('.mh-wishlist-btn[data-product-id="' + productId + '"]').not($btn).each(function () {
                    setAdded($(this), wasAdded);
                });

                // ── Update counter badges ───────────────────────
                updateCount(res.data.count);

                // ── If removed from within the wishlist table, animate row out ─
                if (!wasAdded) {
                    var $row = $btn.closest('tr[data-product-id="' + productId + '"]');
                    if ($row.length) {
                        $row.fadeOut(300, function () {
                            $row.remove();
                            if ($('table.mh-wishlist-table tbody tr:visible').length === 0) {
                                mhWishlistShowEmpty();
                            }
                        });
                    }
                }
            },
            error: function () {
                // Silent fail — the button state will match server state on next page load.
            },
            complete: function () {
                setLoading($btn, false);
            }
        });
    });

    /* ══════════════════════════════════════════════════════════
       Dedicated Remove button inside the wishlist table
       (uses the smaller circular ×-icon next to each row)
       ══════════════════════════════════════════════════════════ */

    $(document).on('click', '.mh-wl-remove-btn', function (e) {
        e.preventDefault();

        var $btn      = $(this);
        var productId = $btn.data('product-id');
        var nonce     = $btn.data('nonce') || mhWishlist.nonce;

        if ($btn.hasClass('mh-adding')) {
            return;
        }

        $btn.addClass('mh-adding');

        $.ajax({
            url:    mhWishlist.ajaxUrl,
            method: 'POST',
            data: {
                action:     'mh_wishlist_remove',
                nonce:      nonce,
                product_id: productId
            },
            success: function (res) {
                if (!res.success) {
                    return;
                }

                // Fade & remove the table row
                var $row = $btn.closest('tr');
                $row.fadeOut(300, function () {
                    $row.remove();
                    updateCount(res.data.count);

                    if ($('table.mh-wishlist-table tbody tr:visible').length === 0) {
                        mhWishlistShowEmpty();
                    }
                });

                // Sync any page-level wishlist buttons for this product
                $('.mh-wishlist-btn[data-product-id="' + productId + '"]').each(function () {
                    setAdded($(this), false);
                });
            },
            complete: function () {
                $btn.removeClass('mh-adding');
            }
        });
    });

    /* ══════════════════════════════════════════════════════════
       Empty state helper
       ══════════════════════════════════════════════════════════ */

    function mhWishlistShowEmpty() {
        var $wrapper = $('.mh-wishlist-table-wrapper');
        $wrapper.html(
            '<div class="mh-wishlist-empty">' +
                '<i class="fa-regular fa-heart" aria-hidden="true"></i>' +
                '<p>' + mhWishlist.i18n.emptyMessage + '</p>' +
            '</div>'
        );
    }

})(jQuery);
