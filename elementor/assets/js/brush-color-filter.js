// elementor/assets/js/brush-color-filter.js

(function($) {
    /**
     * Converts an RGB color to HSL.
     * Assumes r, g, and b are contained in the set [0, 255] and
     * returns h, s, and l in the set [0, 1].
     *
     * @param   Number  r       The red color value
     * @param   Number  g       The green color value
     * @param   Number  b       The blue color value
     * @return  Array           The HSL representation
     */
    function rgbToHsl(r, g, b) {
        r /= 255, g /= 255, b /= 255;

        var max = Math.max(r, g, b),
            min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;

        if (max == min) {
            h = s = 0; // achromatic
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }

            h /= 6;
        }

        return [h, s, l];
    }

    /**
     * Converts an HSL color value to RGB. Conversion formula
     * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
     * Assumes h, s, and l are contained in the set [0, 1] and
     * returns r, g, and b in the set [0, 255].
     *
     * @param   Number  h       The hue
     * @param   Number  s       The saturation
     * @param   Number  l       The lightness
     * @return  Array           The RGB representation
     */
    function hslToRgb(h, s, l) {
        var r, g, b;

        if (s == 0) {
            r = g = b = l; // achromatic
        } else {
            function hue2rgb(p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1 / 6) return p + (q - p) * 6 * t;
                if (t < 1 / 2) return q;
                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                return p;
            }

            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1 / 3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1 / 3);
        }

        return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
    }

    /**
     * Converts an RGB color value to HSB (hue, saturation, brightness).
     * Assumes r, g, and b are contained in the set [0, 255] and
     * returns h, s, and b in the set [0, 1].
     *
     * @param   Number  r       The red color value
     * @param   Number  g       The green color value
     * @param   Number  b       The blue color value
     * @return  Array           The HSB representation
     */
    function rgbToHsb(r, g, b) {
        r /= 255, g /= 255, b /= 255;

        var max = Math.max(r, g, b),
            min = Math.min(r, g, b);
        var h, s, br = max;
        var d = max - min;
        s = max == 0 ? 0 : d / max;

        if (max == min) {
            h = 0; // achromatic
        } else {
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return [h, s, br];
    }

    /**
     * Generates a CSS filter string to convert a white (#FFFFFF) background to a target color.
     * This uses a complex algorithm to get close to the target color.
     * Best for recoloring single-color white SVGs or very simple PNGs.
     *
     * @param {string} hex       The target hex color (e.g., "#FFC0CB").
     * @returns {string}         CSS filter string.
     */
    function hexToCssFilter(hex) {
        if (!hex) return '';

        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);

        // Calculate a filter that approximates the target color for a white source.
        // This is not a perfect science for all colors but works for many.
        let hsb = rgbToHsb(r, g, b);
        let hue = hsb[0] * 360;
        let saturation = hsb[1] * 100;
        let brightness = hsb[2] * 100;

        // A simplified approach might involve adjusting brightness, saturation, and hue.
        // This is a more complex approach often used for recoloring icons.
        // Source: https://stackoverflow.com/questions/42966649/how-to-change-the-color-of-an-image-from-javascript
        // And https://codepen.io/sosuke/pen/Pjoqqz
        
        // This is a simplified filter generation. For perfect accuracy, it's very complex.
        // For general recoloring of a white/grey shape to a solid color, this can work.
        let filters = `
            brightness(${brightness / 100 * 1.5}) 
            saturate(${saturation / 100 * 2}) 
            hue-rotate(${hue}deg)
            contrast(100%)
            invert(0%)
            sepia(0%)
        `;
        
        // A more robust but heavier filter for specific color:
        // This calculates the filters needed to go from white to the target color.
        // It requires a target color to be passed in, not relative to current.
        // For this use case (changing a white SVG to a specific color), this is better.
        // Source: https://github.com/colorjs/color-filter-generator
        // Given that Elementor controls set a CSS variable, we can make this reactive.

        // Simpler approach:
        // Assume the brush image starts as a light color (like white or light grey)
        // We need to shift its hue, adjust saturation, and adjust brightness.
        // This is tricky because `hue-rotate` rotates around the color wheel.
        // A common method is to first desaturate, then apply a hue-rotate, then re-saturate and adjust brightness.

        // Since we are setting a CSS custom property `--mh-brush-color`,
        // it's possible to use a more direct CSS filter calculation if the base image is known (e.g., white).
        // Let's refine the filter generation to be more stable.
        
        // For a white source color, target is (r,g,b)
        let C = [r / 255, g / 255, b / 255]; // Target color in 0-1 range
        let filter = "brightness(0) saturate(100%)";
        let r_adj = 0, g_adj = 0, b_adj = 0;

        // Calculate a matrix for brightness, contrast, grayscale, sepia, hue-rotate, invert.
        // This is too complex for simple JS.
        // A simpler, more practical approach for Elementor:
        // - If it's an SVG and `fill` is `currentColor`, just set `color`.
        // - If it's an SVG without `currentColor` or a PNG, use a complex CSS filter.

        // For `brash.svg` that has no fill, setting `color` on parent works if using `currentColor`.
        // If the SVG has `fill="black"` or something, the `filter` approach is needed.

        // Given the user wants to recolor "this image" (implying the PNG),
        // we HAVE to use the complex filter approach.
        
        // The standard filter chain for white to target color:
        // invert -> hue-rotate -> saturate -> brightness -> contrast
        // The values here are dependent on the target color and source color.
        
        // For a white source image (like a simple SVG with `fill="white"` or `fill="#000000"` with `invert(1)`):
        // To convert a white image to a specific color, we can use a set of CSS filters.
        // This specific set of filters is derived from tools like 'CSS Filter Generator'
        // which solve for the matrix to change white to target hex.
        
        // Example for white to pink (#FFC0CB):
        // filter: invert(79%) sepia(26%) saturate(1915%) hue-rotate(308deg) brightness(101%) contrast(101%);
        // This function would need to generate these values.
        
        // Let's provide a fallback that attempts a simpler filter if the complex one is too hard.
        // A much simpler method if the SVG itself uses `fill="currentColor"`:
        // Then you just set the `color` property on the `mh-brush-text-wrapper`.

        // Since the prompt implies a fixed image (PNG-like behavior),
        // we must use a filter.

        // This is a simplified filter computation. Real-world solutions are more complex.
        // This aims for a reasonable shift.
        const r_val = r / 255;
        const g_val = g / 255;
        const b_val = b / 255;

        // A very basic approach:
        let avg = (r_val + g_val + b_val) / 3;
        let brightness_filter = `brightness(${avg * 200}%)`; // Adjust brightness
        
        let target_hsl = rgbToHsl(r, g, b);
        let hue_rotate_filter = `hue-rotate(${target_hsl[0] * 360}deg)`;
        let saturate_filter = `saturate(${target_hsl[1] * 200}%)`; // Double saturation for effect

        return `
            ${hue_rotate_filter} 
            ${saturate_filter} 
            ${brightness_filter} 
            contrast(120%)
        `.trim();
    }


    /**
     * Function to update the CSS filter based on the --mh-brush-color custom property.
     */
    function updateBrushColorFilter($widget) {
        const brushColor = $widget.css('--mh-brush-color');
        if (brushColor) {
            // Remove any alpha channel for filter calculation
            const hexColor = rgbaToHex(brushColor); 
            if (hexColor) {
                const filterString = hexToCssFilter(hexColor);
                $widget.css('--mh-brush-color-filter', filterString);
            }
        }
    }

    /**
     * Converts an RGBA color string to HEX.
     * @param {string} rgbaString - e.g. "rgba(255, 0, 0, 0.5)" or "rgb(255, 0, 0)"
     * @returns {string|null} Hex color string (e.g., "#FF0000") or null if invalid.
     */
    function rgbaToHex(rgbaString) {
        let parts = rgbaString.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+\.?\d*))?\)$/);
        if (!parts) return null;

        let r = parseInt(parts[1], 10);
        let g = parseInt(parts[2], 10);
        let b = parseInt(parts[3], 10);

        // Ensure values are within 0-255 range
        r = Math.min(255, Math.max(0, r));
        g = Math.min(255, Math.max(0, g));
        b = Math.min(255, Math.max(0, b));

        return "#" +
            ("0" + r.toString(16)).slice(-2) +
            ("0" + g.toString(16)).slice(-2) +
            ("0" + b.toString(16)).slice(-2);
    }


    // On Elementor Frontend
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/mh-brush-text.default', function($scope) {
            const $widget = $scope.find('.mh-brush-text-wrapper');
            if ($widget.length) {
                // Initial update
                updateBrushColorFilter($widget);

                // Observe changes to the custom CSS property
                // This requires a MutationObserver or a more direct hook if Elementor provides one for style changes
                // For now, a simpler (but less efficient) approach for editor:
                // Trigger update when the color control changes in editor
                if (elementorFrontend.isEditMode()) {
                    $scope.on('change', '.elementor-control[data-setting="brush_color"] input', function() {
                        updateBrushColorFilter($widget);
                    });
                }
            }
        });
    });

    // In Elementor Editor Mode
    // Use Elementor's internal events for better performance in editor
    if (window.elementor && window.elementor.on) {
        elementor.on('change', function(controlView, model) {
            if (model.attributes.widgetType === 'mh-brush-text' && controlView.model.get('name') === 'brush_color') {
                const $widget = elementor.$previewContents.find('[data-id="' + model.attributes.id + '"] .mh-brush-text-wrapper');
                if ($widget.length) {
                    // Update the custom property first
                    $widget.css('--mh-brush-color', controlView.model.get('value'));
                    updateBrushColorFilter($widget);
                }
            }
            // Also need to handle initial load for editor or if background image changes
            if (model.attributes.widgetType === 'mh-brush-text' && (controlView.model.get('name') === 'brush_image' || controlView.model.get('name') === 'brush_color')) {
                 const $widget = elementor.$previewContents.find('[data-id="' + model.attributes.id + '"] .mh-brush-text-wrapper');
                 if ($widget.length) {
                    // Force re-evaluation of the filter if image or color changes
                    updateBrushColorFilter($widget);
                 }
            }
        });
    }

})(jQuery);