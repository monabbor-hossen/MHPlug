/*
 * MH Plug - Menu Icon Picker JavaScript
 * This file is loaded only on nav-menus.php
 */
jQuery(document).ready(function($) {

    // --- Variables ---
    var modal = $('#mh-menu-icon-modal'); // Try to find modal on load
    var currentItemID = null;
    var currentInput = null;
    var currentButtonIcon = null;
    var faSearchInitialized = false; // Flag to ensure search is only initialized once

    // --- Function to Initialize Font Awesome Search ---
    function initializeFaSearch() {
        if (!faSearchInitialized && modal.length > 0) { // Only run if modal exists and not already initialized
            $('#mh-fa-icon-search').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                // Use the specific grid ID for FA icons
                $('#mh-fa-icon-grid .mh-icon-item').each(function() {
                    // Make sure 'data-filter' exists before calling includes
                    var iconName = $(this).data('filter');
                    if (iconName && typeof iconName === 'string' && iconName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            faSearchInitialized = true; // Set flag
            console.log('MH Plug: FA Search Initialized.'); // For debugging
        } else if (modal.length === 0) {
             console.warn('MH Plug: Modal not found on DOM ready for search init.'); // For debugging
        }
    }

    // --- Try initializing search on document ready ---
    // This might work if the footer hook runs before $(document).ready
     if(modal.length === 0) {
         // If modal isn't there yet, re-select it just in case
         modal = $('#mh-menu-icon-modal');
     }
    initializeFaSearch();

    // --- 1. Open the Modal ---
    $('#menu-management').on('click', '.mh-menu-icon-picker-button', function() {
        var $button = $(this);
        currentItemID = $button.data('itemid');
        currentInput = $($button.data('target-input'));
        currentButtonIcon = $button.find('.mh-plug-button-icon');

        // Re-find modal in case it was added after initial load
        if(modal.length === 0) {
             modal = $('#mh-menu-icon-modal');
        }

        if(modal.length === 0) {
            console.error('MH Plug: Modal HTML not found when button clicked.');
            return;
        }

        // --- Initialize search here too, as a fallback ---
        // This guarantees search works even if modal wasn't ready on initial load
        initializeFaSearch();

        // Open the modal
        modal.addClass('mh-modal-show');
    });

    // --- 2. Close the Modal ---
    $('body').on('click', '.mh-menu-icon-modal-close', function() {
        if (modal.length > 0) modal.removeClass('mh-modal-show');
    });
    $(window).on('click', function(event) {
        if (modal.length > 0 && $(event.target).is(modal)) {
            modal.removeClass('mh-modal-show');
        }
    });

    // --- 3. Select an Icon in the Modal ---
    $('body').on('click', '.mh-icon-item', function() {
        var iconClass = $(this).find('i').attr('class');

        if (currentInput && currentButtonIcon) {
            currentInput.val(iconClass);
            currentButtonIcon.attr('class', 'mh-plug-button-icon ' + iconClass);
            var removeLink = $('.mh-menu-icon-remove[data-itemid="' + currentItemID + '"]');
            removeLink.show();
        }
        if (modal.length > 0) modal.removeClass('mh-modal-show');
    });

    // --- 4. "Remove" Icon Link ---
    $('#menu-management').on('click', '.mh-menu-icon-remove', function() {
        var $removeLink = $(this);
        currentItemID = $removeLink.data('itemid');
        var $button = $('#mh-menu-icon-picker-button-' + currentItemID);
        currentInput = $($button.data('target-input'));
        currentButtonIcon = $button.find('.mh-plug-button-icon');

        currentInput.val('');
        currentButtonIcon.attr('class', 'mh-plug-button-icon mhi-add-plus');
        $removeLink.hide();
    });

    // --- 5. Modal Tab Switching ---
    $('body').on('click', '.mh-menu-icon-modal-tabs button', function() {
        var tab_id = $(this).data('tab');
        $('.mh-menu-icon-modal-tabs button').removeClass('active');
        $('.mh-menu-icon-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tab_id).addClass('active');
    });

});