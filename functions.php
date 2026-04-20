<?php
/**
 * THEvotex — functions.php
 *
 * This file is the single entry point for all theme PHP.
 * It defines constants and loads /inc files in dependency order.
 * No business logic lives here — delegate everything to /inc.
 *
 * @package THEvotex
 * @version 1.0.0
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Theme constants ─────────────────────────────────────────── */

define( 'THEVOTEX_VERSION',   '1.0.0' );
define( 'THEVOTEX_DIR',       get_template_directory() );
define( 'THEVOTEX_URI',       get_template_directory_uri() );
define( 'THEVOTEX_INC',       THEVOTEX_DIR . '/inc' );
define( 'THEVOTEX_ASSETS',    THEVOTEX_URI . '/assets' );
define( 'THEVOTEX_MIN_PHP',   '8.1' );
define( 'THEVOTEX_MIN_WP',    '6.3' );

/* ── PHP version gate ────────────────────────────────────────── */

if ( version_compare( PHP_VERSION, THEVOTEX_MIN_PHP, '<' ) ) {
	add_action( 'admin_notices', function (): void {
		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			sprintf(
				/* translators: 1: Minimum PHP version, 2: Current PHP version. */
				esc_html__( 'THEvotex requires PHP %1$s or higher. You are running PHP %2$s. Please upgrade your PHP version.', 'thevotex' ),
				esc_html( THEVOTEX_MIN_PHP ),
				esc_html( PHP_VERSION )
			)
		);
	} );

	return; // Stop loading the theme — avoid fatal errors on old PHP.
}

/* ── WordPress version gate ──────────────────────────────────── */

global $wp_version;

if ( version_compare( $wp_version, THEVOTEX_MIN_WP, '<' ) ) {
	add_action( 'admin_notices', function () use ( $wp_version ): void {
		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			sprintf(
				/* translators: 1: Minimum WP version, 2: Current WP version. */
				esc_html__( 'THEvotex requires WordPress %1$s or higher. You are running WordPress %2$s.', 'thevotex' ),
				esc_html( THEVOTEX_MIN_WP ),
				esc_html( $wp_version )
			)
		);
	} );

	return;
}

/* ── Load /inc files ─────────────────────────────────────────── */

/*
 * Load order matters:
 * 1. helpers   — pure utility functions; no hooks; depended on by everything else
 * 2. setup     — add_theme_support, image sizes, nav menus
 * 3. post-types — register CPTs and taxonomies (priority 0 on 'init')
 * 4. enqueue   — scripts and styles (depends on helpers for thevotex_asset_version)
 * 5. security  — hardening hooks
 * 6. customizer — Customizer panels (depends on helpers for thevotex_option)
 */
$thevotex_inc_files = array(
	'/helpers.php',
	'/setup.php',
	'/post-types.php',
	'/menus.php',
	'/widgets.php',
	'/enqueue.php',
	'/security.php',
	'/customizer.php',
	'/woocommerce.php',
	'/compatibility.php',
);

foreach ( $thevotex_inc_files as $file ) {
	$path = THEVOTEX_INC . $file;

	if ( file_exists( $path ) ) {
		require_once $path;
	} else {
		// In debug mode, surface missing /inc files immediately.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			trigger_error(
				sprintf( 'THEvotex: required file not found: %s', esc_html( $path ) ),
				E_USER_WARNING
			);
		}
	}
}

unset( $thevotex_inc_files, $file, $path );

/* ── Optional: ACF fields (loaded only if ACF is active) ─────── */

if ( function_exists( 'acf_add_local_field_group' ) ) {
	$acf_file = THEVOTEX_INC . '/acf-fields.php';

	if ( file_exists( $acf_file ) ) {
		require_once $acf_file;
	}

	unset( $acf_file );
}

/* ── Optional: Reservation AJAX handler ─────────────────────── */

$reservation_file = THEVOTEX_INC . '/reservation-handler.php';

if ( file_exists( $reservation_file ) ) {
	require_once $reservation_file;
}

unset( $reservation_file );

/* ── Optional: Admin enhancements (admin-only) ───────────────── */

if ( is_admin() ) {
	$admin_files = array(
		'/admin/admin-columns.php',
		'/admin/admin-styles.php',
	);

	foreach ( $admin_files as $file ) {
		$path = THEVOTEX_INC . $file;

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}

	unset( $admin_files, $file, $path );
}
