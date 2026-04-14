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

    /* =========================================================
     * THEME BUILDER LOGIC
     * ========================================================= */

    // Tab Filtering
    $('.mh-tb-tabs li').on('click', function() {
        var $tab = $(this);
        var type = $tab.data('tab');

        // UI Active State
        $('.mh-tb-tabs li').removeClass('active');
        $tab.addClass('active');

        // Filter Cards
        if (type === 'all') {
            $('.mh-tb-template-item').fadeIn(200);
        } else {
            $('.mh-tb-template-item').hide();
            $('.mh-tb-template-item[data-type="' + type + '"]').fadeIn(200);
        }
    });

    // Modal Open
    $('#mh-tb-create-btn, #mh-tb-card-add-new').on('click', function() {
        $('#mh-tb-modal').css('display', 'flex').hide().fadeIn(300);
    });

    // Modal Close (X button, cancel, or clicking outside area)
    $('#mh-tb-modal-close').on('click', function() {
        $('#mh-tb-modal').fadeOut(300);
    });

    $('#mh-tb-modal').on('click', function(e) {
        // If clicking exactly on the dark overlay, close it
        if ($(e.target).is('#mh-tb-modal')) {
            $(this).fadeOut(300);
        }
    });

    // Form Submit (Mock logic for now)
    $('#mh-tb-create-form').on('submit', function(e) {
        e.preventDefault();
        var templateName = $('#mh_tb_template_name').val();
        var templateType = $('#mh_tb_template_type').val();
        
        // In a real app, send AJAX here. For now, close modal.
        $('#mh-tb-modal').fadeOut(300);
        
        // Reset form
        $(this)[0].reset();
        
        // Optional: Trigger a notification plugin here
    });

});