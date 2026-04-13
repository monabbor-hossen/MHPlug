// Use a safe wrapper for jQuery to avoid conflicts with other libraries.
jQuery(document).ready(function($) {

// Accordion Toggle Logic
    $('.mh-accordion-header').on('click', function(e) {

        var $item = $(this).closest('.mh-accordion-item');
       

        // *** IMPORTANT: Ignore clicks if they came from the Enable/Disable buttons ***
        if ($(e.target).closest('.mh-widget-controls').length) {
            return; // Stop if the click was on the buttons or their container
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

    // --- ADD THIS NEW CODE BLOCK ---
// Handle "Enable All" / "Disable All" button clicks
    $('.mh-toggle-all').on('click', function(e) { // Add 'e' for event object
        e.stopPropagation(); // *** IMPORTANT: Stop the click from bubbling up to the header ***
// --- CHANGED: START ---
        // Check if the button itself is disabled.
        // The 'disabled' attribute is added by settings-page.php if Elementor is inactive.
        if ($(this).is(':disabled')) {
            return; // Do nothing if the button is disabled
        }
        // --- CHANGED: END ---

        var action = $(this).data('action');
        // Find the accordion ITEM, then find the content area within it
        var $accordionItem = $(this).closest('.mh-accordion-item');
        var $contentArea = $accordionItem.find('.mh-accordion-content');
        var $checkboxes = $contentArea.find('.mh-widget-card input[type="checkbox"]');

        if (action === 'enable') {
            $checkboxes.prop('checked', true);
        } else if (action === 'disable') {
            $checkboxes.prop('checked', false);
        }
    });

// --- END OF NEW CODE BLOCK ---
});