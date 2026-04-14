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

    // Form Submit logic
    $('#mh-tb-create-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $btnText = $btn.find('.mh-button-text');
        var originalText = $btnText.text();
        
        var templateName = $('#mh_tb_template_name').val();
        var templateType = $('#mh_tb_template_type').val();
        var nonce = $('#mh_tb_nonce').val();
        
        $btn.prop('disabled', true);
        $btnText.text('Creating...');
        
        $.ajax({
            url: typeof mhTbAjaxUrl !== 'undefined' ? mhTbAjaxUrl : ajaxurl,
            type: 'POST',
            data: {
                action: 'mh_tb_create_template',
                template_name: templateName,
                template_type: templateType,
                _ajax_nonce: nonce
            },
            success: function(response) {
                if (response.success && response.data.edit_url) {
                    window.location.href = response.data.edit_url;
                } else {
                    alert(response.data.message || 'Error creating template.');
                    $btn.prop('disabled', false);
                    $btnText.text(originalText);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $btn.prop('disabled', false);
                $btnText.text(originalText);
            }
        });
    });

    // Template Card Status Toggle AJAX
    $(document).on('change', '.mh-tb-status-cb', function() {
        var $cb = $(this);
        var id = $cb.data('id');
        var isActive = $cb.prop('checked');
        
        $cb.prop('disabled', true); // Temporarily disable to prevent double clicks
        
        $.ajax({
            url: typeof mhTbAjaxUrl !== 'undefined' ? mhTbAjaxUrl : ajaxurl,
            type: 'POST',
            data: {
                action: 'mh_tb_toggle_status',
                template_id: id,
                is_active: isActive
            },
            success: function(response) {
                if (!response.success) {
                    alert(response.data.message || 'Error updating status.');
                    $cb.prop('checked', !isActive); // Revert checkbox
                }
                $cb.prop('disabled', false);
            },
            error: function() {
                alert('An error occurred. Status not saved.');
                $cb.prop('checked', !isActive); // Revert
                $cb.prop('disabled', false);
            }
        });
    });

    // ── Delete Template Button ──────────────────────────────────────────────
    // Uses event delegation so it works for any dynamically rendered card.
    $(document).on('click', '.mh-tb-delete-btn', function(e) {
        e.preventDefault();

        var $btn  = $(this);
        var id    = $btn.data('id');
        var $card = $btn.closest('.mh-tb-card');

        if ( !id ) {
            alert('Invalid template ID.');
            return;
        }

        if ( !confirm('Are you sure you want to delete this template? This cannot be undone.') ) {
            return;
        }

        // Visual feedback while waiting
        $btn.prop('disabled', true).css('opacity', '0.5');

        $.ajax({
            url: typeof mhTbAjaxUrl !== 'undefined' ? mhTbAjaxUrl : ajaxurl,
            type: 'POST',
            data: {
                action:      'mh_tb_delete_template',
                template_id:  id,
                _ajax_nonce:  typeof mhTbDeleteNonce !== 'undefined' ? mhTbDeleteNonce : ''
            },
            success: function(response) {
                if (response.success) {
                    // Shrink and fade the card, then remove it from the DOM
                    $card.animate(
                        { opacity: 0, height: 0, marginBottom: 0, paddingTop: 0, paddingBottom: 0 },
                        400,
                        function() { $card.remove(); }
                    );
                } else {
                    alert(response.data.message || 'Error deleting template.');
                    $btn.prop('disabled', false).css('opacity', '1');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $btn.prop('disabled', false).css('opacity', '1');
            }
        });
    });

});