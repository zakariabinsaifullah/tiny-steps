<?php
/**
 * SVG Uploads
 *
 * Lets administrators upload SVG files, sanitised on the way in.
 *
 * @package Tiny_Steps
 */

if ( ! function_exists( 'tnys_allow_svg_upload' ) ) :
	/**
	 * Permits SVG uploads for users who can already publish unfiltered markup.
	 *
	 * WordPress blocks image/svg+xml because an SVG is a script carrier. The
	 * capability check plus the sanitiser below are what make this safe: the
	 * file is rewritten on upload with scripts, event handlers and external
	 * references removed.
	 *
	 * @param array $mimes Allowed mime types.
	 * @return array Filtered mime types.
	 */
	function tnys_allow_svg_upload( $mimes ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $mimes;
		}

		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;
	}
endif;
add_filter( 'upload_mimes', 'tnys_allow_svg_upload' );


if ( ! function_exists( 'tnys_sanitize_svg_upload' ) ) :
	/**
	 * Rewrites an uploaded SVG through the theme's sanitiser before it lands
	 * in the media library.
	 *
	 * Runs on every upload, so an SVG that slips past `upload_mimes` — through
	 * a plugin, say — is still cleaned. Reuses tnys_sanitize_svg_markup() from
	 * inc/my-icons.php, which strips scripts, event handlers and external
	 * references. An unparseable file is rejected outright.
	 *
	 * @param array $file Upload array.
	 * @return array Possibly rejected upload array.
	 */
	function tnys_sanitize_svg_upload( $file ) {
		if ( empty( $file['tmp_name'] ) || empty( $file['type'] ) ) {
			return $file;
		}

		if ( 'image/svg+xml' !== $file['type'] ) {
			return $file;
		}

		if ( ! function_exists( 'tnys_sanitize_svg_markup' ) ) {
			$file['error'] = __( 'SVG uploads are unavailable: the sanitiser is missing.', 'tiny-steps' );
			return $file;
		}

		$raw = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local temp file.

		if ( false === $raw ) {
			$file['error'] = __( 'The SVG could not be read.', 'tiny-steps' );
			return $file;
		}

		$clean = tnys_sanitize_svg_markup( $raw );

		if ( '' === $clean ) {
			$file['error'] = __( 'That SVG could not be sanitised, so it was not uploaded.', 'tiny-steps' );
			return $file;
		}

		file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents -- Local temp file.

		return $file;
	}
endif;
add_filter( 'wp_handle_upload_prefilter', 'tnys_sanitize_svg_upload' );
