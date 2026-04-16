/**
 * MH Product Gallery Slider Initialization
 */
(function($) {
    'use strict';

    const initProductGallery = function() {
        
        $('.mh-product-gallery-container').each(function() {
            const $wrapper         = $(this);
            const $mainSlider      = $wrapper.find('.mh-gallery-main-viewport');
            const $thumbSlider     = $wrapper.find('.mh-gallery-thumb-slider');

            if ( ! $mainSlider.length ) return;

            const hasThumbs = $thumbSlider.length > 0;

            // 1. MAIN IMAGE SLIDER
            $mainSlider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: false, // Forces the smooth Slide animation from the video
                infinite: true,
                arrows: false, // Handled by our custom external arrows
                asNavFor: hasThumbs ? $thumbSlider : null,
                adaptiveHeight: true
            });

            // 2. THUMBNAILS SLIDER
            if ( hasThumbs ) {
                $thumbSlider.slick({
                    slidesToShow: 4, 
                    slidesToScroll: 1,
                    asNavFor: $mainSlider,
                    focusOnSelect: true,
                    infinite: true,
                    arrows: false, // Handled by our custom external arrows
                    responsive: [
                        { breakpoint: 1024, settings: { slidesToShow: 3 } },
                        { breakpoint: 767,  settings: { slidesToShow: 3 } }
                    ]
                });
            }

            // 3. MAIN SLIDER NAVIGATION CLICKS
            $wrapper.find('.mh-main-prev').on('click', function() {
                $mainSlider.slick('slickPrev');
            });
            $wrapper.find('.mh-main-next').on('click', function() {
                $mainSlider.slick('slickNext');
            });

            // 4. THUMBNAIL SLIDER NAVIGATION CLICKS
            $wrapper.find('.mh-thumb-prev').on('click', function() {
                $thumbSlider.slick('slickPrev');
            });
            $wrapper.find('.mh-thumb-next').on('click', function() {
                $thumbSlider.slick('slickNext');
            });

        });
    };

    $(window).on('load', function() {
        initProductGallery();
    });

})(jQuery);