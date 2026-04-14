// Use a safe wrapper for jQuery to avoid conflicts with other libraries.
jQuery(document).ready(function($) {

    // ── Accordion Toggle Logic ──────────────────────────────────────────
    $('.mh-accordion-header').on('click', function(e) {

        var $item = $(this).closest('.mh-accordion-item');

        // Ignore clicks that came from the Enable/Disable buttons
        if ($(e.target).closest('.mh-widget-controls').length) {
            return;
        }

        var $content = $item.find('.mh-accordion-content');

        // Close others
        $('.mh-accordion-content').not($content).slideUp();
        $('.mh-accordion-item').not($item).removeClass('mh-active').find('.mh-accordion-icon').text('+');

        // Toggle current
        $item.toggleClass('mh-active');
        $content.slideToggle();
        var $icon = $(this).find('.mh-accordion-icon');
        $icon.text($icon.text() === '+' ? '-' : '+');
    });

    // ── "Enable All" / "Disable All" button logic ───────────────────────
    $('.mh-toggle-all').on('click', function(e) {
        e.stopPropagation(); // Prevent accordion from toggling

        var $btn = $(this);

        // Respect the disabled attribute — do nothing if disabled.
        // This guards the WooCommerce section when WC is inactive,
        // and also guards the Elementor section when Elementor is inactive.
        if ($btn.is(':disabled')) {
            return;
        }

        var action        = $btn.data('action');
        var $accordionItem = $btn.closest('.mh-accordion-item');
        var $contentArea  = $accordionItem.find('.mh-accordion-content');

        // Only target non-disabled checkboxes within THIS accordion item
        var $checkboxes = $contentArea.find('.mh-widget-card input[type="checkbox"]:not(:disabled)');

        if (action === 'enable') {
            $checkboxes.prop('checked', true);
        } else if (action === 'disable') {
            $checkboxes.prop('checked', false);
        }
    });

});