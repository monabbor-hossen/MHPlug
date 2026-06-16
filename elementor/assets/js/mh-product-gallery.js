/**
 * MH Product Gallery Slider Initialization
 */
(function($) {
    'use strict';

    var WidgetGalleryHandler = function($scope, $) {
        var $wrapper = $scope.find('.mh-premium-gallery-container');
        if ( ! $wrapper.length ) return;

        var $mainSlider  = $wrapper.find('.mh-gallery-main-viewport');
        var $thumbSlider = $wrapper.find('.mh-gallery-thumb-slider');

        if ( ! $mainSlider.length ) return;

        var hasThumbs = $thumbSlider.length > 0;

        // Destroy existing sliders if Elementor reloads the widget while editing
        if ( $mainSlider.hasClass('slick-initialized') ) {
            $mainSlider.slick('unslick');
        }
        if ( hasThumbs && $thumbSlider.hasClass('slick-initialized') ) {
            $thumbSlider.slick('unslick');
        }

        // 1. MAIN IMAGE SLIDER
        $mainSlider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: false, 
            infinite: true,
            arrows: false, 
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
                arrows: false, 
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 3 } },
                    { breakpoint: 767,  settings: { slidesToShow: 3 } }
                ]
            });
        }

        // 3. MAIN SLIDER ARROWS
        $wrapper.find('.mh-main-prev').off('click').on('click', function() {
            $mainSlider.slick('slickPrev');
        });
        $wrapper.find('.mh-main-next').off('click').on('click', function() {
            $mainSlider.slick('slickNext');
        });

        // 4. THUMBNAIL SLIDER ARROWS
        $wrapper.find('.mh-thumb-prev').off('click').on('click', function() {
            $thumbSlider.slick('slickPrev');
        });
        $wrapper.find('.mh-thumb-next').off('click').on('click', function() {
            $thumbSlider.slick('slickNext');
        });
    };

    // 🚀 Hook directly into Elementor's widget loading process
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/mh_product_gallery.default', WidgetGalleryHandler);
    });

})(jQuery);