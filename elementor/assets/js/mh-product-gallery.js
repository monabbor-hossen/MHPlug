/**
 * MH Product Gallery Slider Initialization
 * * Sets up Slick slider for main image and thumbnail syncing with full responsive control.
 */
(function($) {
    'use strict';

    const initProductGallery = function() {
        
        $('.mh-product-gallery-container').each(function() {
            const $wrapper         = $(this);
            const $mainSlider      = $wrapper.find('.mh-gallery-main-viewport');
            const $thumbSlider     = $wrapper.find('.mh-gallery-thumb-slider');
            const settingsString   = $wrapper.attr('data-mh-settings');

            if ( !settingsString || ! $mainSlider.length ) {
                return;
            }

            const settings = JSON.parse(settingsString);

            // Determine if thumbnails are present
            const hasThumbs = $thumbSlider.length > 0;

            // ─────────────────────────────────────────────────────────────────
            // MAIN IMAGE SLIDER INITIALIZATION
            // ─────────────────────────────────────────────────────────────────
            const slickMainSettings = {
                slidesToShow: 1,
                slidesToScroll: 1,
                // Apply the Transition type (fade vs slide) from Elementor control
                fade: settings.transition_type === 'fade', 
                infinite: settings.loop === 'yes',
                autoplay: settings.autoplay === 'yes',
                autoplaySpeed: parseInt(settings.autoplay_speed) || 5000,
                arrows: false, // We use custom arrows external to the slide content
                asNavFor: hasThumbs ? $thumbSlider : null, // Sync with thumbnails!
                adaptiveHeight: true,
                lazyLoad: 'progressive'
            };

            $mainSlider.slick(slickMainSettings);

            // Apply modern subtle scaling effect during transition
            $mainSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                $mainSlider.find('.slick-slide img').css({
                    'transition': 'transform 0.5s ease',
                    'transform': 'scale(1.03)' // Small scale up to start
                });
            });

            $mainSlider.on('afterChange', function(event, slick, currentSlide) {
                $mainSlider.find('.slick-slide.slick-active img').css({
                    'transition': 'transform 0.5s ease',
                    'transform': 'scale(1)' // Return to normal scale on active slide
                });
            });


            // ─────────────────────────────────────────────────────────────────
            // THUMBNAILS SLIDER INITIALIZATION (if present)
            // ─────────────────────────────────────────────────────────────────
            if ( hasThumbs ) {
                const slickThumbSettings = {
                    slidesToShow: 4, // Default Desktop
                    slidesToScroll: 1,
                    // Apply Vertical setting based on Thumbs Position control
                    vertical: settings.thumbs_position === 'left' || settings.thumbs_position === 'right',
                    verticalSwiping: settings.thumbs_position === 'left' || settings.thumbs_position === 'right',
                    asNavFor: $mainSlider, // Sync with main image!
                    focusOnSelect: true,
                    infinite: settings.loop === 'yes',
                    arrows: false,
                    responsive: [
                        {
                            breakpoint: 1024, // Tablet
                            settings: {
                                slidesToShow: 3,
                                vertical: false, // Standard Desktop Breakpoint logic - set thumbs to bottom on tablet
                                verticalSwiping: false,
                            }
                        },
                        {
                            breakpoint: 767, // Mobile
                            settings: {
                                slidesToShow: 2, // Standard Desktop Breakpoint logic - set thumbs to bottom on mobile
                                vertical: false,
                                verticalSwiping: false,
                            }
                        }
                    ]
                };
                
                $thumbSlider.slick(slickThumbSettings);
            }

            // ─────────────────────────────────────────────────────────────────
            // CUSTOM NAVIGATION ARROWS
            // ─────────────────────────────────────────────────────────────────
            $wrapper.find('.mh-gallery-prev').on('click', function() {
                $mainSlider.slick('slickPrev');
            });

            $wrapper.find('.mh-gallery-next').on('click', function() {
                $mainSlider.slick('slickNext');
            });
            
            // Clean initial scaling state
            $mainSlider.find('.slick-slide img').css('transform', 'scale(1)');

        });
    };

    // Initialize after standard WooCommerce assets and page content are ready
    $(window).on('load', function() {
        initProductGallery();
    });

})(jQuery);