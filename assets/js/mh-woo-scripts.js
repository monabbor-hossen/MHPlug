jQuery(document).ready(function($) {

    // Guard: PHP must inject the rules array before this script can function
    if (typeof mhVariationRules === 'undefined') {
        console.warn('MH-Plug: mhVariationRules not found on this page. Variation price updates will not work.');
        return;
    }

    // Locate the Elementor price widget container (with fallback)
    var $priceWidget = $('.mh-product-price .elementor-widget-container');
    if ($priceWidget.length === 0) {
        $priceWidget = $('.mh-product-price');
    }

    // Cache the original price HTML so we can revert when a dropdown is cleared
    var basePriceHtml = $priceWidget.html();

    // Disable the Add to Cart button on page load when custom dropdowns exist —
    // the user must complete all selections before purchasing.
    if ($('.mh-custom-attr-select').length > 0) {
        $('.single_add_to_cart_button').prop('disabled', true).addClass('disabled');
    }

    // Delegated change listener — fires for any .mh-custom-attr-select on the page,
    // including those injected after DOM ready (e.g. via Elementor dynamic templates).
    $(document).on('change', '.mh-custom-attr-select', function() {

        var selectedValues = [];
        var allSelected    = true;

        // Collect the current value from every custom variation dropdown
        $('.mh-custom-attr-select').each(function() {
            var val = $(this).val();
            if (!val || val === '') {
                allSelected = false;
            } else {
                // Trim + lowercase for bulletproof matching against PHP-generated keys
                selectedValues.push(String(val).trim().toLowerCase());
            }
        });

        // If not all dropdowns have a value, revert to the base price and block checkout
        if (!allSelected) {
            $priceWidget.html(basePriceHtml);
            $('.single_add_to_cart_button').prop('disabled', true).addClass('disabled');
            return;
        }

        // Sort alphabetically to mirror PHP ksort() used when building combination_string
        selectedValues.sort();
        var comboString = selectedValues.join('|');

        // Search the injected rules array for an exact match.
        // Supports both key names for forward-compatibility:
        //   combination_string — current PHP output key
        //   combination        — requested spec / potential future key
        var matchedRule = mhVariationRules.find(function(rule) {
            var rawKey = rule.combination_string || rule.combination;
            if (!rawKey) return false;
            return String(rawKey).trim().toLowerCase() === comboString;
        });

        // Re-resolve the target each time (handles Elementor late-rendering edge cases)
        var $target = $('.mh-product-price .elementor-widget-container');
        if ($target.length === 0) {
            $target = $('.mh-product-price');
        }

        // Scrape the currency symbol from any existing WooCommerce price on the page
        var currencySymbol = $('.woocommerce-Price-currencySymbol').first().text() || '$';

        if (matchedRule) {

            var newPriceHtml = '';

            if (matchedRule.sale_price && parseFloat(matchedRule.sale_price) > 0) {
                // Sale: struck-through regular price + highlighted sale price
                newPriceHtml =
                    '<del aria-hidden="true">' +
                        '<span class="woocommerce-Price-amount amount">' +
                            '<bdi>' +
                                '<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' +
                                parseFloat(matchedRule.regular_price).toFixed(2) +
                            '</bdi>' +
                        '</span>' +
                    '</del> ' +
                    '<ins>' +
                        '<span class="woocommerce-Price-amount amount">' +
                            '<bdi>' +
                                '<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' +
                                parseFloat(matchedRule.sale_price).toFixed(2) +
                            '</bdi>' +
                        '</span>' +
                    '</ins>';
            } else {
                // Regular price only
                newPriceHtml =
                    '<span class="woocommerce-Price-amount amount">' +
                        '<bdi>' +
                            '<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' +
                            parseFloat(matchedRule.regular_price).toFixed(2) +
                        '</bdi>' +
                    '</span>';
            }

            // Animate the price change and unlock the Add to Cart button
            $target.hide().html(newPriceHtml).fadeIn(200);
            $('.single_add_to_cart_button').prop('disabled', false).removeClass('disabled');

        } else {

            // Valid dropdown selections, but no rule exists for this combination
            $target.hide().html(
                '<span class="mh-unavailable" style="color:#b32d2e; font-weight:bold;">Selection Unavailable</span>'
            ).fadeIn(200);
            $('.single_add_to_cart_button').prop('disabled', true).addClass('disabled');
        }
    });
});
