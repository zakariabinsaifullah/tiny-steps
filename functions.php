<?php
/**
 * Tiny Steps — Theme Functions
 *
 * This file is intentionally kept as a loader only.
 * All feature logic lives in the files under inc/.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 * @package Tiny_Steps
 * @since   1.0
 */

// ── Theme setup ────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/setup.php' );      // Editor style

// ── Asset enqueueing ───────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/enqueue.php' );    // Frontend & block styles

// ── Uploads ────────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/svg-upload.php' );   // Sanitised SVG uploads

// ── Blocks ─────────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/blocks.php' );        // Custom block registration
require_once get_theme_file_path( 'inc/block-styles.php' );  // Core block style variations
require_once get_theme_file_path( 'inc/extensions.php' );    // Block editor extensions

// ── Icon library ───────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/my-icons.php' );      // Site-wide saved SVG icons

// ── Editor taxonomy ────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/categories.php' ); // Block & pattern categories

// ── Form panel ─────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/form.php' );       // Slide-in form panel & settings
require_once get_theme_file_path( 'inc/office-hours.php' ); // Office hours settings & {{placeholders}}

// ── Shortcodes ─────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/shortcode.php' );         // Posts grid, tabs, featured & related posts
require_once get_theme_file_path( 'inc/shortcodes-page.php' );  // Appearance → Tiny Steps reference page


/**
 * Set WordPress excerpt length to 25 words.
 */
function tnys_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'tnys_excerpt_length' );

/**
 * Customize excerpt more text.
 */
function tnys_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'tnys_excerpt_more' );