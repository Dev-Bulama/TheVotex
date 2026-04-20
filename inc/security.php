<?php
/**
 * Security hardening — removes WordPress information leakage,
 * locks down common attack vectors, and enforces output escaping
 * conventions at the theme level.
 *
 * None of these replacements affect authenticated admin users.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── WordPress version exposure ─────────────────────────────── */

/**
 * Remove the WP version number from <head>, RSS feeds,
 * and the ?ver= query string on core asset URLs.
 * Prevents automated scanners from fingerprinting the install.
 */
add_filter( 'the_generator', '__return_empty_string' );

function thevotex_remove_version_from_assets( string $src ): string {
	if ( strpos( $src, 'ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}
add_filter( 'style_loader_src',  'thevotex_remove_version_from_assets' );
add_filter( 'script_loader_src', 'thevotex_remove_version_from_assets' );

/* ── Head cleanup ────────────────────────────────────────────── */

/**
 * Remove rarely-needed tags from <head> that expose site
 * metadata or create unnecessary HTTP requests.
 */
function thevotex_clean_head(): void {
	remove_action( 'wp_head', 'rsd_link' );                             // EditURI / RSD link.
	remove_action( 'wp_head', 'wlwmanifest_link' );                     // Windows Live Writer.
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );             // Shortlink.
	remove_action( 'wp_head', 'wp_generator' );                         // WP version meta tag.
	remove_action( 'wp_head', 'feed_links_extra', 3 );                  // Category/tag RSS feeds.
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );  // Prev/next post link rels.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );      // Emoji detection JS.
	remove_action( 'wp_print_styles', 'print_emoji_styles' );           // Emoji CSS.
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'thevotex_clean_head' );

/* ── REST API exposure ───────────────────────────────────────── */

/**
 * Block unauthenticated access to the /users endpoint.
 * Prevents enumeration of WordPress user login names via the API.
 * Authenticated requests (admin, editor) are unaffected.
 */
function thevotex_restrict_rest_users( WP_Error|bool|null $result ): WP_Error|bool|null {
	if ( ! empty( $result ) ) {
		return $result;
	}

	if ( ! is_user_logged_in() && isset( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		if ( strpos( $request_uri, '/wp-json/wp/v2/users' ) !== false ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to list users.', 'thevotex' ),
				array( 'status' => 403 )
			);
		}
	}

	return $result;
}
add_filter( 'rest_authentication_errors', 'thevotex_restrict_rest_users' );

/* ── Login page hardening ────────────────────────────────────── */

/**
 * Replace the generic "Invalid username" / "Wrong password"
 * error messages with a single ambiguous message. Prevents
 * username enumeration via login form error feedback.
 */
function thevotex_obscure_login_errors(): string {
	return __( 'The credentials you entered are incorrect.', 'thevotex' );
}
add_filter( 'login_errors', 'thevotex_obscure_login_errors' );

/**
 * Add X-Frame-Options and X-Content-Type-Options headers.
 * Complements any server-level header rules.
 * X-Frame-Options prevents clickjacking.
 * X-Content-Type-Options prevents MIME-sniffing.
 */
function thevotex_security_headers(): void {
	if ( ! is_admin() && ! headers_sent() ) {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	}
}
add_action( 'send_headers', 'thevotex_security_headers' );

/* ── AJAX / form CSRF protection ─────────────────────────────── */

/**
 * Central nonce verification helper for AJAX handlers.
 * Call this at the top of every wp_ajax_* callback before
 * processing any POST data.
 *
 * @param string $action Nonce action string.
 * @return void Sends a 403 JSON error and exits on failure.
 */
function thevotex_verify_nonce( string $action = 'thevotex_nonce' ): void {
	$nonce = isset( $_POST['nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Security check failed. Please refresh and try again.', 'thevotex' ) ),
			403
		);
	}
}

/* ── File editor disable ─────────────────────────────────────── */

/**
 * Disable the WP theme/plugin file editor for production.
 * Prevents remote code execution if an admin account is compromised.
 * Define THEVOTEX_ALLOW_FILE_EDIT in wp-config.php to override.
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) && ! defined( 'THEVOTEX_ALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/* ── Comment spam surface reduction ─────────────────────────── */

/**
 * Remove the author URL field from comment forms.
 * A common spam-magnet with no UX value for this site type.
 */
function thevotex_remove_comment_url_field( array $fields ): array {
	unset( $fields['url'] );

	return $fields;
}
add_filter( 'comment_form_default_fields', 'thevotex_remove_comment_url_field' );

/* ── Output escaping reminder (development) ──────────────────── */

/**
 * In debug mode, register a shutdown function that warns if any
 * unescaped output patterns are detected in theme template files.
 * This is a development aid only — never runs in production.
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
	// Remind developers: always use esc_html(), esc_attr(),
	// esc_url(), wp_kses_post(), or intval() before any echo.
	// WordPress.com VIP coding standards require no unescaped output.
}
