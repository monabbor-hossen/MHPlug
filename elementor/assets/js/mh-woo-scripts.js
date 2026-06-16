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
    // 2. WISHLIST — Handled by mh-widgets.js (Elementor) & mh-wishlist.js (global)
    //    No handler here to avoid double AJAX toggle conflicts.
    // ─────────────────────────────────────────────────────────────────────────────

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
        initMhQuickView(); 
    });

    $(window).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/mh_woo_add_to_cart.default', function ($scope) { initMhATC($scope); });
        }
    });

})(jQuery);


// =============================================================================
// 5. CUSTOM VARIATION PRICE UPDATER — AGGRESSIVE TARGET DISCOVERY
//    Runs in its own jQuery(document).ready() block, completely independent of
//    the IIFE above, to avoid 'use strict' or scope conflicts.
// =============================================================================
jQuery(document).ready(function($) {

    // Guard: PHP must inject mhVariationRules via the inline <script> tag in
    // render_custom_variations_dropdowns() before this block is useful.
    if (typeof mhVariationRules === 'undefined') return;

    // ── Aggressive Target Discovery ──────────────────────────────────────────
    // Try progressively broader selectors so the price update works even if:
    //   • the Elementor class is missing (caching / regenerate issue)
    //   • the widget is on a custom template without .mh-product-price
    //   • the page uses a native WooCommerce single product template
    var $priceTarget = $('.mh-product-price .elementor-widget-container'); // primary
    if ($priceTarget.length === 0) $priceTarget = $('.mh-product-price');  // wrapper fallback
    if ($priceTarget.length === 0) $priceTarget = $('p.price').first();    // native Woo <p class="price">
    if ($priceTarget.length === 0) {                                        // extreme last resort
        $priceTarget = $('.woocommerce-Price-amount').first().closest('p, div');
    }

    // Store the default price HTML so we can revert when a dropdown is cleared.
    var defaultPriceHtml = $priceTarget.length > 0 ? $priceTarget.html() : '';

    // Disable the Add to Cart button on page load when custom dropdowns exist —
    // the user must complete all selections before they can purchase.
    if ($('.mh-custom-attr-select').length > 0) {
        $('.single_add_to_cart_button').prop('disabled', true).addClass('disabled');
    }

    // Delegated listener — works for dropdowns injected at any time by Elementor.
    $(document).on('change', '.mh-custom-attr-select', function() {

        // Re-verify target in case DOM mutated after initial discovery.
        if ($priceTarget.length === 0) {
            console.error('MH-Plug: Cannot find any price element on the page to update.');
            return;
        }

        var variationPairs = [];
        var allSelected    = true;

        // Collect the current value from every custom variation dropdown.
        $('.mh-custom-attr-select').each(function() {
            var val = $(this).val();
            if (!val || val === '') {
                allSelected = false;
            } else {
                // Only use attributes marked as "Used for variations" to calculate price
                var isVariation = $(this).data('is-variation');
                if (isVariation === true || isVariation === 'true') {
                    var rawName = $(this).data('attribute') || $(this).attr('name') || '';
                    var attrKey = rawName.replace(/^mh_custom_attr\[(.*)\]$/, '$1').replace(/^attribute_/, '');
                    variationPairs.push({
                        key: attrKey.toLowerCase(),
                        val: String(val).trim().toLowerCase()
                    });
                }
            }
        });

        // Not all dropdowns filled: revert to default price and block checkout.
        if (!allSelected) {
            $priceTarget.html(defaultPriceHtml);
            $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', true).addClass('disabled');
            return;
        }

        // Sort by key to mirror PHP ksort() used when building the rule key.
        variationPairs.sort(function(a, b) {
            if (a.key < b.key) return -1;
            if (a.key > b.key) return 1;
            return 0;
        });

        var comboString = variationPairs.map(function(p) { return p.val; }).join('|');
        console.log('MH-Plug Searching for:', comboString);

        // Search the injected rules. Supports both key names:
        //   combination_string — current PHP output key (_mh_custom_variation_rules)
        //   combination        — spec key / potential future rename
        var matchedRule = mhVariationRules.find(function(rule) {
            var rawKey = rule.combination_string || rule.combination;
            if (!rawKey) return false;
            return String(rawKey).trim().toLowerCase() === comboString;
        });

        if (matchedRule) {
            console.log('MH-Plug Match Found! Price:', matchedRule.regular_price);
            // Scrape the currency symbol from an existing price element on the page.
            var currencySymbol = $('.woocommerce-Price-currencySymbol').first().text() || '৳';
            var newPriceHtml   = '';

            if (matchedRule.sale_price && parseFloat(matchedRule.sale_price) > 0) {
                // Sale: struck-through regular price + highlighted sale price.
                newPriceHtml =
                    '<del aria-hidden="true"><span class="woocommerce-Price-amount amount"><bdi>' +
                    '<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' +
                    parseFloat(matchedRule.regular_price).toFixed(2) +
                    '</bdi></span></del> ' +
                    '<ins><span class="woocommerce-Price-amount amount"><bdi>' +
                    '<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' +
                    parseFloat(matchedRule.sale_price).toFixed(2) +
                    '</bdi></span></ins>';
            } else {
                // Regular price only.
                newPriceHtml =
                    '<span class="woocommerce-Price-amount amount"><bdi>' +
                    '<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' +
                    parseFloat(matchedRule.regular_price).toFixed(2) +
                    '</bdi></span>';
            }

            // Animate the price change and unlock the Add to Cart button.
            $priceTarget.hide().html(newPriceHtml).fadeIn(200);
            $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', false).removeClass('disabled');

        } else {
            // Dropdowns are filled but no rule exists for this combination.
            $priceTarget.hide().html(
                '<span class="mh-unavailable" style="color:#b32d2e; font-weight:bold; font-size:18px;">Selection Unavailable</span>'
            ).fadeIn(200);
            $('.single_add_to_cart_button, .mh-buy-now-btn').prop('disabled', true).addClass('disabled');
        }
    });
});
