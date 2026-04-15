/**
 * MH Woo Scripts
 *
 * Handles the custom ± quantity buttons for the
 * MH Custom Add to Cart widget. Scoped to .mh-atc-wrap
 * to avoid touching any native WooCommerce quantity inputs.
 */
(function ($) {
    'use strict';

    /**
     * Initialise quantity controls inside a given container.
     * Called once on DOMReady and again after Elementor re-renders widgets
     * inside the editor (elementor/frontend/init).
     *
     * @param {jQuery} $scope - The widget or page scope to search within.
     */
    function initMhATC($scope) {
        var $context = $scope || $(document);

/* ── Minus button ──────────────────────────────── */
$context.find('.mh-qty-minus').off('click.mhAtc').on('click.mhAtc', function () {
    // Command: Search the parent wrapper to find the input safely
    var $input = $(this).closest('.mh-qty-wrapper').find('.mh-qty-input');
    var current = parseInt($input.val(), 10) || 1;
    var min     = parseInt($input.attr('min'), 10) || 1;

    if (current > min) {
        $input.val(current - 1).trigger('change');
    }
});

/* ── Plus button ───────────────────────────────── */
$context.find('.mh-qty-plus').off('click.mhAtc').on('click.mhAtc', function () {
    // Command: Search the parent wrapper to find the input safely
    var $input = $(this).closest('.mh-qty-wrapper').find('.mh-qty-input');
    var current = parseInt($input.val(), 10) || 1;
    var max     = parseInt($input.attr('max'), 10);

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

    /* ── Fire on page load ─────────────────────────────── */
    $(document).ready(function () {
        initMhATC(null);
    });

    /* ── Fire inside Elementor editor when a widget is rendered ── */
    $(window).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/mh_woo_add_to_cart.default',
                function ($scope) {
                    initMhATC($scope);
                }
            );
        }
    });

})(jQuery);
