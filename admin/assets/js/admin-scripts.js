// Use a safe wrapper for jQuery to avoid conflicts with other libraries.
jQuery(document).ready(function($) {
    // Init color pickers globally for admin pages
    if ( $.fn.wpColorPicker ) {
        $('.mh-color-picker').wpColorPicker();
    }

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
                action:      'mh_tb_toggle_status',
                template_id: id,
                is_active:   isActive,
                _ajax_nonce: typeof mhTbToggleNonce !== 'undefined' ? mhTbToggleNonce : ''
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

    // ── AJAX Save Custom Variations ─────────────────────────────────────
    $(document).on('click', '.mh-save-variations-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $spinner = $btn.next('.mh-save-spinner');

        // Gather all inputs inside our custom panel
        var formData = $('#mh_custom_variations_panel :input').serialize();
        var productId = $('#post_ID').val();

        $btn.prop('disabled', true);
        $spinner.addClass('is-active');

        $.post(ajaxurl, {
            action: 'mh_save_custom_variations',
            product_id: productId,
            form_data: formData
        }, function(response) {
            $spinner.removeClass('is-active');
            if (response.success) {
                $btn.text('Saved!').removeClass('button-primary').addClass('button-secondary');
                setTimeout(function() {
                    $btn.text('Save Variations').removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
                }, 2000);
            } else {
                alert('Error saving variations.');
                $btn.prop('disabled', false);
            }
        });
    });

    // ── Preloader Page Logic ──────────────────────────────────────────────
    $('.mh-loader-type').change(function() {
        if ($(this).val() === 'css') {
            $('.mh-image-settings').hide();
            $('.mh-css-settings').fadeIn();
        } else {
            $('.mh-css-settings').hide();
            $('.mh-image-settings').fadeIn();
        }
    });

    $('.mh-tab').click(function(){
        var type = $(this).data('type');
        var target = $(this).data('target');
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $('#' + target + '_type').val(type);
        if(type === 'gradient') {
            $(this).closest('.mh-elementor-color-group').find('.mh-gradient-controls').slideDown();
        } else {
            $(this).closest('.mh-elementor-color-group').find('.mh-gradient-controls').slideUp();
        }
        updateLivePreview();
    });

    $('input[type="range"]').on('input', function(){
        $(this).siblings('.mh-range-badge').find('span').text($(this).val());
    });

    function updateLivePreview() {
        var type = $('.mh-loader-type:checked').val();
        
        var bgType = $('#bg_type').val(), bgC1 = $('#bg_c1').val(), bgC2 = $('#bg_c2').val(), bgAngle = $('#bg_angle').val();
        var bgStyle = (bgType === 'gradient') ? 'linear-gradient(' + bgAngle + 'deg, ' + bgC1 + ', ' + bgC2 + ')' : bgC1;
        $('#mh-live-preview-box').css('background', bgStyle);

        var effect = $('#mh_css_effect_select').val();
        var effType = $('#loader_type').val(), effC1 = $('#loader_c1').val(), effC2 = $('#loader_c2').val(), effAngle = $('#loader_angle').val();
        var effBgStyle = (effType === 'gradient') ? 'linear-gradient(' + effAngle + 'deg, ' + effC1 + ', ' + effC2 + ')' : effC1;
        var effectScale = $('#mh_effect_size_range').val();
        var svgStroke = (effType === 'gradient') ? 'url(#mh-svg-gradient)' : effC1;

        var customText = $('#mh_custom_text_input').val(), textAnim = $('#mh_text_anim_select').val(), textSize = $('#mh_text_size_range').val();
        var textType = $('#text_type').val(), textC1 = $('#text_c1').val(), textC2 = $('#text_c2').val(), textAngle = $('#text_angle').val();
        var textBgStyle = (textType === 'gradient') ? 'linear-gradient(' + textAngle + 'deg, ' + textC1 + ', ' + textC2 + ')' : textC1;

        $('#mh_loader_color_picker').siblings('.mh-color-overlay').css('background-color', effC1);
        $('#mh_bg_color_picker').siblings('.mh-color-overlay').css('background-color', bgC1);
        $('#mh_text_color_picker').siblings('.mh-color-overlay').css('background-color', textC1);
        $('#mh-svg-c1').attr('stop-color', effC1); $('#mh-svg-c2').attr('stop-color', effC2);

        if(type === 'image') {
            var imgUrl = $('#mh-preloader-image-url').val();
            var imgWidth = $('#mh_img_width_range').val();
            if(imgUrl) {
                $('#mh-loader-visual').html('<img src="'+imgUrl+'" style="width:'+imgWidth+'px; height:auto;"/>');
            } else {
                $('#mh-loader-visual').html('<i class="dashicons dashicons-format-image" style="font-size:40px; color:#888; width:40px; height:40px;"></i>');
            }
        } else {
            var html = '';
            if (effect === '1') html = '<div class="mh-loader-1"></div>';
            if (effect === '2') html = '<div class="mh-loader-2"><div></div><div></div><div></div></div>';
            if (effect === '3') html = '<div class="mh-loader-3"></div>';
            if (effect === '4') html = '<div class="mh-loader-4"></div>';
            if (effect === '5') html = '<div class="mh-loader-5"><div></div><div></div></div>';
            if (effect === '6') html = '<div class="mh-loader-6"><div></div><div></div><div></div><div></div><div></div></div>';
            if (effect === '7') html = '<div class="mh-loader-7"></div>';
            if (effect === '8') html = '<div class="mh-loader-8"></div>';
            if (effect === '9') html = '<div class="mh-loader-9"><div></div><div></div></div>';
            if (effect === '10') html = '<div class="mh-loader-10"><div></div><div></div></div>';
            if (effect === '11') html = '<div class="mh-loader-11"><div></div></div>';
            if (effect === '12') html = '<div class="mh-loader-12"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
            if (effect === '13') html = '<div class="mh-loader-13"><div></div><div></div></div>';
            if (effect === '14') html = '<div class="mh-loader-14"><div></div><div></div><div></div></div>';
            if (effect === '15') html = '<div class="mh-loader-15"><div></div><div></div><div></div><div></div></div>';
            if (effect === '16') html = '<div class="mh-loader-16"></div>';
            if (effect === '17') html = '<div class="mh-loader-17"><div></div></div>';
            if (effect === '18') html = '<div class="mh-loader-18"><div></div><div></div><div></div><div></div></div>';
            if (effect === '19') html = '<div class="mh-loader-19"></div>';
            if (effect === '20') html = '<div class="mh-loader-20"><div></div></div>';
            if (effect === '21') html = '<div class="mh-loader-21 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg></div>';
            if (effect === '22') html = '<div class="mh-loader-22 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></div>';
            if (effect === '23') html = '<div class="mh-loader-23 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></div>';
            if (effect === '24') html = '<div class="mh-loader-24 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></div>';
            if (effect === '25') html = '<div class="mh-loader-25 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg></div>';
            if (effect === '26') html = '<div class="mh-loader-26 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>';
            if (effect === '27') html = '<div class="mh-loader-27 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 5v14M8 5v14M12 5v14M17 5v14M21 5v14"></path></svg><div class="mh-scanline"></div></div>';
            if (effect === '28') html = '<div class="mh-loader-28 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v8"></path><path d="M9 12h6"></path></svg></div>';
            if (effect === '29') html = '<div class="mh-loader-29 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></div>';
            if (effect === '30') html = '<div class="mh-loader-30 mh-ecommerce-icon"><svg class="mh-svg-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>';

            $('#mh-loader-visual').html('<div style="width: calc(100px * '+effectScale+'); height: calc(100px * '+effectScale+'); display: flex; align-items: center; justify-content: center;"><div style="transform: scale('+effectScale+'); --mh-c1: '+effC1+'; --mh-bg-style: '+effBgStyle+'; --mh-svg-stroke: '+svgStroke+';">' + html + '</div></div>');
        }

        if(customText && customText.trim() !== '') {
            var textStyle = (textType === 'gradient') ? 'background: ' + textBgStyle + '; -webkit-background-clip: text; -webkit-text-fill-color: transparent;' : 'color: ' + textC1 + ';';
            var typingVars = '--mh-bg-style: ' + bgStyle + '; --mh-c1: ' + textC1 + ';';
            
            $('#mh-loader-text-display').html('<div class="mh-text-anim-'+textAnim+'" style="' + typingVars + ' font-size: '+textSize+'px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; text-align: center; margin-top: 15px;"><span style="'+textStyle+'">' + customText + '</span></div>').show();
        } else {
            $('#mh-loader-text-display').hide();
        }
    }

    if ($('.mh-live-trigger').length) {
        $('.mh-live-trigger').on('change input', updateLivePreview);
        updateLivePreview(); // Init
    }

    var mediaUploader;
    $('#mh-upload-preloader-btn').click(function(e) {
        e.preventDefault();
        if (mediaUploader) { mediaUploader.open(); return; }
        mediaUploader = wp.media.frames.file_frame = wp.media({ title: 'Select Asset', button: { text: 'Inject Asset' }, multiple: false });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mh-preloader-image-url').val(attachment.url);
            updateLivePreview();
            $('#mh-remove-preloader-btn').css('display', 'inline-flex');
        });
        mediaUploader.open();
    });

    $('#mh-remove-preloader-btn').click(function(e){
        e.preventDefault();
        $('#mh-preloader-image-url').val('');
        updateLivePreview();
        $(this).hide();
    });

});