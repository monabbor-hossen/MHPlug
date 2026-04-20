/**
 * MH Nav Menu Widget - JavaScript
 * Handles: Mobile toggle, mobile caret, desktop click submenu, smooth scroll.
 * Uses event delegation so it works after Elementor re-renders.
 */
(function ($) {
    'use strict';

    // 1. Mobile Menu Toggle
    $(document).on('click', '.mh-nav-mobile-toggle', function () {
        var $wrapper = $(this).closest('[class*="mh-nav-wrapper-"]');
        $wrapper.find('.mh-nav-mobile-panel').slideToggle(300);
        $(this).find('i').toggleClass('fa-bars fa-times');
    });

    // 2. Inject Mobile Carets (once per widget render)
    $(document).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/mh_nav_menu.default', function ($scope) {
                $scope.find('.mh-nav-mobile-panel .menu-item-has-children').each(function () {
                    if ($(this).children('.mh-mobile-caret').length === 0) {
                        $(this).prepend('<div class="mh-mobile-caret"><i class="fas fa-chevron-down"></i></div>');
                    }
                });
            });
        }
    });

    // Fallback inject for non-Elementor contexts
    $(document).ready(function () {
        $('[class*="mh-nav-wrapper-"] .mh-nav-mobile-panel .menu-item-has-children').each(function () {
            if ($(this).children('.mh-mobile-caret').length === 0) {
                $(this).prepend('<div class="mh-mobile-caret"><i class="fas fa-chevron-down"></i></div>');
            }
        });
    });

    // 3. Mobile Caret Click
    $(document).on('click', '.mh-mobile-caret', function (e) {
        e.preventDefault();
        $(this).parent('li').children('.sub-menu').slideToggle(300);
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    // 4. Desktop "Click" Submenu Display
    $(document).on('click', '.mh-nav-desktop .mh-submenu-click .menu-item-has-children > a', function (e) {
        if ($(this).attr('href') === '#' || $(this).attr('href') === '') {
            e.preventDefault();
        }
        var $submenu = $(this).siblings('.sub-menu');
        if ($submenu.css('opacity') === '0') {
            $submenu.css({ opacity: '1', visibility: 'visible', pointerEvents: 'auto', transform: 'translateY(0)' });
        } else {
            $submenu.css({ opacity: '0', visibility: 'hidden', pointerEvents: 'none', transform: 'translateY(15px)' });
        }
    });

    // 5. Smooth Scroll for Anchor Links
    $(document).on('click', '[class*="mh-nav-wrapper-"] a[href^="#"]', function (e) {
        var href = this.getAttribute('href');
        if (href === '#' || href === '') return;
        var target = $(href);
        if (target.length) {
            e.preventDefault();
            var $wrapper = $(this).closest('[class*="mh-nav-wrapper-"]');
            var scrollSpeed = parseInt($wrapper.data('speed')) || 500;
            $wrapper.find('.mh-nav-mobile-panel').slideUp(300);
            $wrapper.find('.mh-nav-mobile-toggle i').removeClass('fa-times').addClass('fa-bars');
            $('html, body').stop().animate({ scrollTop: target.offset().top - 80 }, scrollSpeed);
        }
    });

}(jQuery));
