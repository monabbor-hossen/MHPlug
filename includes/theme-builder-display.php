<?php
/**
 * MH Plug - Theme Builder Display Logic
 *
 * Handles frontend injection of active Header / Footer templates AND
 * strict single-post vs single-product content overrides.
 *
 * Template type slugs (authoritative, stored in `_mh_template_type`):
 * header | footer | single_post | single_product | archive_post | archive_product
 *
 * KEY SAFETY RULE — Elementor Edit Mode:
 * When Elementor's editor is active (is_edit_mode() === true) ALL overrides
 * are completely skipped. The editor renders inside our canvas (mh-canvas.php)
 * which already contains the_content(), so no interference is needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// Helper: is the current request inside the Elementor visual editor?
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Returns true when Elementor's editor iframe is active.
 *
 * Wrapped so callers never repeat the null-safe chain or risk fatals on
 * sites without Elementor.
 *
 * @return bool
 */
function mh_plug_is_elementor_edit_mode() {
    return (
        did_action( 'elementor/loaded' ) &&
        class_exists( '\Elementor\Plugin' ) &&
        isset( \Elementor\Plugin::$instance->editor ) &&
        \Elementor\Plugin::$instance->editor->is_edit_mode()
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// Helper: fetch the active template post of a given type
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Retrieves the first published, active `mh_templates` post of a given type.
 *
 * Searches the new `_mh_template_type` meta key. Falls back to the old
 * `mh_template_type` key via an OR relation so legacy templates still match.
 *
 * @param  string       $type  Canonical slug: header | footer | single_post |
 * single_product | archive_post | archive_product
 * @return WP_Post|null        The matching post, or null if none found.
 */
function mh_plug_get_active_template( $type ) {
    $type = sanitize_key( $type );

    // Legacy slug this new slug maps back to (for backward compat)
    $legacy_reverse_map = [
        'single_post'     => 'single',
        'single_product'  => 'product_single',
        'archive_post'    => 'archive',
        'archive_product' => 'product_archive',
    ];

    $meta_query = [
        'relation' => 'AND',
        // Active status: check both meta keys
        [
            'relation' => 'OR',
            [
                'key'   => '_mh_template_active',
                'value' => 'yes',
            ],
            [
                'key'   => 'mh_template_active',
                'value' => 'yes',
            ],
        ],
        // Template type: check both new and legacy keys
        [
            'relation' => 'OR',
            [
                'key'   => '_mh_template_type',
                'value' => $type,
            ],
        ],
    ];

    // Add legacy key clause only if a mapping exists
    if ( isset( $legacy_reverse_map[ $type ] ) ) {
        $meta_query[2][] = [
            'key'   => 'mh_template_type',
            'value' => $legacy_reverse_map[ $type ],
        ];
        // Also support the new slug stored in the old key (migration in progress)
        $meta_query[2][] = [
            'key'   => 'mh_template_type',
            'value' => $type,
        ];
    } else {
        // header / footer never had different slugs, just add old-key check
        $meta_query[2][] = [
            'key'   => 'mh_template_type',
            'value' => $type,
        ];
    }

    $posts = get_posts( [
        'post_type'      => 'mh_templates',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'no_found_rows'  => true,     // skip COUNT(*) — we only need 1 row
        'meta_query'     => $meta_query,
    ] );

    return ! empty( $posts ) ? $posts[0] : null;
}

// ─────────────────────────────────────────────────────────────────────────────
// Helper: render a template post's Elementor content safely
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Outputs the Elementor-built content of an mh_templates post.
 *
 * Falls back to `apply_filters('the_content', …)` if Elementor is absent.
 *
 * @param WP_Post $template_post
 * @return void
 */
function mh_plug_render_template( $template_post ) {
    if (
        did_action( 'elementor/loaded' ) &&
        class_exists( '\Elementor\Plugin' ) &&
        isset( \Elementor\Plugin::$instance->frontend )
    ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display(
            $template_post->ID,
            true  // with_css: include Elementor's inline styles
        );
    } else {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo apply_filters( 'the_content', $template_post->post_content );
    }
}
// ─────────────────────────────────────────────────────────────────────────────
// Header injection (Corrected Override)
// ─────────────────────────────────────────────────────────────────────────────
function mh_plug_inject_header( $name ) {
    // 1. Force the red debug message to prove the hook is alive
    echo '<div style="background:red; color:white; padding:20px; z-index:99999; position:relative; text-align:center;">
        <strong>MH PLUG DEBUG:</strong> get_header is firing!
    </div>';

    // 2. Hard-fetch the first header regardless of active status
    $header_posts = get_posts([
        'post_type' => 'mh_templates',
        'meta_key' => '_mh_template_type',
        'meta_value' => 'header',
        'numberposts' => 1
    ]);

    if ( !empty($header_posts) ) {
        echo '<header class="mh-custom-header" style="border: 5px solid blue;">';
        mh_plug_render_template( $header_posts[0] );
        echo '</header>';
    } else {
        echo '<div style="background:orange; padding:20px;">No header templates found in database at all!</div>';
    }
    
    // 3. Kill the theme's default header
    $templates = [];
    $name = (string) $name;
    if ( '' !== $name ) { $templates[] = "header-{$name}.php"; }
    $templates[] = 'header.php';
    ob_start(); locate_template( $templates, true ); ob_get_clean();
}
// Ensure priority is 0 to fire before the theme
add_action( 'get_header', 'mh_plug_inject_header', 0 );

// ─────────────────────────────────────────────────────────────────────────────
// Footer injection (Corrected Override)
// ─────────────────────────────────────────────────────────────────────────────

function mh_plug_inject_footer( $name ) {
    if ( mh_plug_is_elementor_edit_mode() ) {
        return;
    }

    $footer = mh_plug_get_active_template( 'footer' );
    if ( ! $footer ) {
        return;
    }
    
    // Command: Render our custom Elementor footer
    ?>
<footer class="mh-custom-footer" id="mh-site-footer">
    <?php mh_plug_render_template( $footer ); ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
    <?php
    
    // STRICT RULE: Prevent the theme's original footer.php from loading.
    $templates = [];
    $name = (string) $name;
    if ( '' !== $name ) {
        $templates[] = "footer-{$name}.php";
    }
    $templates[] = 'footer.php';
    
    ob_start();
    locate_template( $templates, true ); // true = require_once
    ob_get_clean(); // Discard the original theme footer
}
add_action( 'get_footer', 'mh_plug_inject_footer', 0 );
// ─────────────────────────────────────────────────────────────────────────────
// Content override — Single Post (standard WordPress blog post)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Replaces the_content on standard singular blog posts with the active
 * `single_post` template, if one exists.
 *
 * Deliberate constraints:
 * - Only fires on `post` post type (NOT 'page', NOT 'product').
 * - Skips mh_templates posts to prevent infinite recursion.
 * - Skips Elementor edit mode.
 * - Must be in the main WP loop (in_the_loop()).
 *
 * @param  string $content  Original post content.
 * @return string           Template HTML or original content.
 */
function mh_plug_override_single_post_content( $content ) {
    if ( ! in_the_loop() )                    return $content;
    if ( ! is_singular( 'post' ) )            return $content;
    if ( is_singular( 'mh_templates' ) )      return $content;
    if ( mh_plug_is_elementor_edit_mode() )   return $content;

    $template = mh_plug_get_active_template( 'single_post' );
    if ( ! $template ) {
        return $content;
    }

    ob_start();
    mh_plug_render_template( $template );
    return ob_get_clean();
}
add_filter( 'the_content', 'mh_plug_override_single_post_content', 20 );

// ─────────────────────────────────────────────────────────────────────────────
// Template override — Single Product (WooCommerce)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Intercepts the WordPress template loading pipeline for WooCommerce single
 * product pages and returns our dedicated frontend wrapper instead.
 *
 * WHY template_include and NOT the_content filter:
 * WooCommerce loads its own template (single-product.php) which calls
 * wc_setup_product_data() to populate the global $product object BEFORE
 * the_content ever fires. If we intercept via the_content we arrive after
 * WC's context setup has already run for the default template — but our
 * Elementor widgets expect $product to be available from the start of the
 * page render. Using template_include lets us own the entire page lifecycle:
 * we set up the product context ourselves and then render the Elementor
 * template inside it.
 *
 * The queried template post ID is stored in a plugin-namespaced global so the
 * wrapper file (mh-single-product-frontend.php) can retrieve it without a
 * second database query.
 *
 * Skipped when Elementor's editor is active — the editor renders inside
 * mh-canvas.php which handles the product context setup separately.
 *
 * @param  string $template  Path to the template WordPress / WooCommerce chose.
 * @return string            Our wrapper path, or original $template.
 */
function mh_plug_single_product_template_include( $template ) {
    // Safety checks — bail early if this isn't a WooCommerce product page
    if ( ! class_exists( 'WooCommerce' ) )   return $template;
    if ( ! function_exists( 'is_product' ) ) return $template;
    if ( ! is_product() )                    return $template;

    // Never interfere while Elementor editor is open
    if ( mh_plug_is_elementor_edit_mode() )  return $template;

    // Look for an active single_product template
    $mh_template = mh_plug_get_active_template( 'single_product' );
    if ( ! $mh_template ) {
        return $template; // No active template — let WooCommerce handle it
    }

    // Store the template post ID in a global so the wrapper file can read it
    // without running a second query.
    global $mh_plug_product_template_id;
    $mh_plug_product_template_id = $mh_template->ID;

    // Point WordPress at our wrapper file
    $wrapper = MH_PLUG_PATH . 'includes/templates/mh-single-product-frontend.php';
    if ( file_exists( $wrapper ) ) {
        return $wrapper;
    }

    return $template;
}
add_filter( 'template_include', 'mh_plug_single_product_template_include', 99 );

// ─────────────────────────────────────────────────────────────────────────────
// Mandatory Canvas Routing for Elementor Editor
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Intercepts the template loading specifically for our Custom Post Type.
 * Forces WordPress to use mh-canvas.php so Elementor has a clean environment
 * without triggering the "the_content" missing error.
 *
 * @param  string $template Path to the template WordPress chose.
 * @return string           Our canvas path, or original $template.
 */
function mh_plug_force_canvas_template( $template ) {
    // Command: Check if we are viewing a single Theme Builder template
    if ( is_singular( 'mh_templates' ) ) {
        $canvas_template = MH_PLUG_PATH . 'includes/templates/mh-canvas.php';
        
        // Strict Rule: Ensure the file exists before forcing it
        if ( file_exists( $canvas_template ) ) {
            return $canvas_template;
        }
    }
    return $template;
}
// Command: Priority 99 ensures this overrides standard theme templates.
// Command: Change priority to 99999 to guarantee nothing overrides your custom canvas.
add_filter( 'template_include', 'mh_plug_force_canvas_template', 99999 );