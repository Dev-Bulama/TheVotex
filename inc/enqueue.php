<?php
/**
 * Asset Enqueueing
 *
 * Handles all script and stylesheet registration, enqueueing,
 * versioning, and conditional loading for front-end, block editor,
 * and WP admin contexts.
 *
 * Responsibilities:
 *  - Front-end styles and scripts (global + per-template)
 *  - Google Fonts with proper preconnect resource hints
 *  - Inline Customizer CSS injected via wp_add_inline_style()
 *  - Block editor stylesheet
 *  - Admin stylesheet (scoped to CPT screens)
 *  - Conditional chatbot widget script
 *  - defer / async attributes on non-critical scripts
 *  - wp_localize_script() data bridge (PHP → JS)
 *
 * @package THEvotex
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Asset helpers
// ---------------------------------------------------------------------------

/**
 * Returns a cache-busting version string for a theme file.
 *
 * Uses the file's mtime during development so any save triggers
 * a fresh cache. Falls back to THEVOTEX_VERSION on build servers
 * where the file may not yet exist.
 *
 * @since  1.0.0
 * @param  string $relative_path Path relative to the theme root (e.g. 'assets/css/theme.min.css').
 * @return string                Version string safe for use in wp_enqueue_*.
 */
function thevotex_asset_version( string $relative_path ): string {
	$abs = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	return file_exists( $abs )
		? (string) filemtime( $abs )
		: THEVOTEX_VERSION;
}

/**
 * Returns the full URL of a theme asset.
 *
 * @since  1.0.0
 * @param  string $relative_path Path relative to the theme root.
 * @return string                Absolute URL.
 */
function thevotex_asset_url( string $relative_path ): string {
	return get_template_directory_uri() . '/' . ltrim( $relative_path, '/' );
}

// ---------------------------------------------------------------------------
// Resource hints — preconnect for Google Fonts
// ---------------------------------------------------------------------------

/**
 * Adds preconnect resource hints for Google Fonts origins.
 *
 * Uses the 'wp_resource_hints' filter (the correct WP API) rather
 * than directly echoing <link> tags in wp_head. This lets other
 * plugins participate in hint deduplication.
 *
 * @since  1.0.0
 * @param  array  $urls          Existing hint URLs for this $relation_type.
 * @param  string $relation_type The hint type: 'preconnect', 'dns-prefetch', etc.
 * @return array                 Merged hint URLs.
 */
function thevotex_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'thevotex_resource_hints', 10, 2 );

// ---------------------------------------------------------------------------
// Front-end styles
// ---------------------------------------------------------------------------

/**
 * Enqueues all front-end stylesheets.
 *
 * Load order:
 *   1. Google Fonts (external)
 *   2. theme.min.css (global compiled CSS)
 *   3. reservation.min.css (conditional — reservation template only)
 *   4. woocommerce.min.css (conditional — WooCommerce pages only)
 *   5. Inline Customizer overrides (appended to theme.min.css handle)
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_enqueue_styles(): void {

	// ── Google Fonts ─────────────────────────────────────────────
	wp_enqueue_style(
		'thevotex-google-fonts',
		'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap',
		array(),
		null  // Never version external stylesheets — the URL is its own cache key.
	);

	// ── Main compiled stylesheet ──────────────────────────────────
	wp_enqueue_style(
		'thevotex-main',
		thevotex_asset_url( 'assets/css/theme.min.css' ),
		array( 'thevotex-google-fonts' ),
		thevotex_asset_version( 'assets/css/theme.min.css' )
	);

	// ── RTL stylesheet ────────────────────────────────────────────
	// WordPress automatically switches to this file when is_rtl() returns true.
	wp_style_add_data( 'thevotex-main', 'rtl', 'replace' );

	// ── Customizer live CSS overrides ─────────────────────────────
	// Appended as an inline <style> block after thevotex-main.
	// Avoids an extra HTTP request and enables live-preview postMessage.
	$customizer_css = thevotex_build_customizer_css();
	if ( ! empty( $customizer_css ) ) {
		wp_add_inline_style( 'thevotex-main', $customizer_css );
	}

	// ── Reservation page ──────────────────────────────────────────
	// Loaded only on the reservation template — the multi-step wizard
	// CSS is ~30 KB and irrelevant on every other page.
	if ( thevotex_is_page_template( 'template-reservation' ) ) {
		wp_enqueue_style(
			'thevotex-reservation',
			thevotex_asset_url( 'assets/css/reservation.min.css' ),
			array( 'thevotex-main' ),
			thevotex_asset_version( 'assets/css/reservation.min.css' )
		);
	}

	// ── WooCommerce ───────────────────────────────────────────────
	if ( thevotex_is_woocommerce_page() ) {
		wp_enqueue_style(
			'thevotex-woocommerce',
			thevotex_asset_url( 'assets/css/woocommerce.min.css' ),
			array( 'thevotex-main' ),
			thevotex_asset_version( 'assets/css/woocommerce.min.css' )
		);
	}

	// ── Block styles ──────────────────────────────────────────────
	// wp-block-library is already enqueued by core when blocks are
	// present; we do not need to call it again. However, we can
	// dequeue the default block-library CSS and replace it with our
	// own compiled version that strips unwanted defaults.
	if ( ! has_blocks() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-blocks-style' ); // WooCommerce blocks.
	}
}
add_action( 'wp_enqueue_scripts', 'thevotex_enqueue_styles' );

// ---------------------------------------------------------------------------
// Front-end scripts
// ---------------------------------------------------------------------------

/**
 * Enqueues all front-end scripts.
 *
 * Load order:
 *   1. thevotex-main (cursor, nav, hero slider, scroll-reveal)
 *   2. thevotex-reservation (conditional — reservation template only)
 *   3. comment-reply (conditional — singular pages with comments open)
 *   4. thevotex-chatbot (conditional — Customizer toggle + credentials set)
 *
 * All theme scripts are loaded in the footer (true = in_footer).
 * No jQuery dependency — the theme uses vanilla JS throughout.
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_enqueue_scripts(): void {

	// ── Core theme script ─────────────────────────────────────────
	wp_enqueue_script(
		'thevotex-main',
		thevotex_asset_url( 'assets/js/theme.min.js' ),
		array(),  // No jQuery dependency.
		thevotex_asset_version( 'assets/js/theme.min.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',  // Requires WP 6.3+. Non-blocking, executes after DOM parse.
		)
	);

	// ── PHP → JS data bridge ──────────────────────────────────────
	/*
	 * Keep this minimal. Only pass values JS cannot obtain itself
	 * (server-side settings, nonces, AJAX URLs). Never pass
	 * sensitive data (user passwords, private keys, etc.).
	 */
	wp_localize_script(
		'thevotex-main',
		'thevotexData',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'thevotex_nonce' ),
			'siteName'    => esc_js( get_bloginfo( 'name' ) ),
			'currency'    => esc_js( thevotex_get_currency_symbol() ),
			'sliderSpeed' => absint( get_theme_mod( 'thevotex_slider_speed', 5500 ) ),
			'isRtl'       => is_rtl(),
			'homeUrl'     => esc_url( home_url( '/' ) ),
		)
	);

	// ── Reservation wizard ────────────────────────────────────────
	if ( thevotex_is_page_template( 'template-reservation' ) ) {
		wp_enqueue_script(
			'thevotex-reservation',
			thevotex_asset_url( 'assets/js/reservation.min.js' ),
			array( 'thevotex-main' ),
			thevotex_asset_version( 'assets/js/reservation.min.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// Pass reservation-specific data separately so it is only
		// output on pages that actually load this script.
		wp_localize_script(
			'thevotex-reservation',
			'thevotexReservation',
			array(
				'nonce'              => wp_create_nonce( 'thevotex_reservation_nonce' ),
				'confirmationMsg'    => esc_js( get_theme_mod(
					'thevotex_reservation_confirm_msg',
					__( 'Your reservation request has been received. Our team will confirm everything within 24 hours.', 'thevotex' )
				) ),
				'validationRequired' => esc_js( __( 'This field is required.', 'thevotex' ) ),
				'validationDate'     => esc_js( __( 'Please select a date in the future.', 'thevotex' ) ),
			)
		);
	}

	// ── Comment reply ─────────────────────────────────────────────
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// ── Chatbot widget ────────────────────────────────────────────
	thevotex_maybe_enqueue_chatbot();
}
add_action( 'wp_enqueue_scripts', 'thevotex_enqueue_scripts' );

// ---------------------------------------------------------------------------
// defer / async attributes on script tags
// ---------------------------------------------------------------------------

/**
 * Adds a 'defer' attribute to non-critical third-party scripts.
 *
 * Using WP 6.3's 'strategy' parameter (see thevotex_enqueue_scripts)
 * is the preferred approach. This filter is a fallback for scripts
 * registered by plugins that do not use the new API.
 *
 * Handles only script handles explicitly listed in the allow-list
 * below — never blindly defers all scripts (breaks inline handlers
 * that depend on the script being ready synchronously).
 *
 * @since  1.0.0
 * @param  string $tag    HTML <script> tag.
 * @param  string $handle Registered script handle.
 * @param  string $src    Script src URL.
 * @return string         Modified <script> tag.
 */
function thevotex_script_attributes( string $tag, string $handle, string $src ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$defer_handles = array(
		'thevotex-chatbot',
	);

	if ( in_array( $handle, $defer_handles, true ) ) {
		// Avoid double-adding if WP core already applied the attribute.
		if ( ! str_contains( $tag, 'defer' ) ) {
			$tag = str_replace( ' src=', ' defer src=', $tag );
		}
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'thevotex_script_attributes', 10, 3 );

// ---------------------------------------------------------------------------
// Block editor assets
// ---------------------------------------------------------------------------

/**
 * Enqueues the editor stylesheet inside the block editor iframe.
 *
 * This is distinct from add_editor_style() (registered in setup.php):
 * the latter handles the classic editor and some block editor contexts,
 * while enqueue_block_editor_assets covers the full-site-editing frame.
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_enqueue_block_editor_assets(): void {
	wp_enqueue_style(
		'thevotex-block-editor',
		thevotex_asset_url( 'assets/css/editor.min.css' ),
		array(),
		thevotex_asset_version( 'assets/css/editor.min.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'thevotex_enqueue_block_editor_assets' );

// ---------------------------------------------------------------------------
// Admin assets
// ---------------------------------------------------------------------------

/**
 * Enqueues the admin stylesheet scoped to relevant screens.
 *
 * Avoids loading on every admin page — only where the theme's CPTs
 * are edited. The $hook parameter matches the screen base ID.
 *
 * @since  1.0.0
 * @param  string $hook Current admin page hook suffix.
 * @return void
 */
function thevotex_enqueue_admin_assets( string $hook ): void {
	$allowed_hooks = array( 'post.php', 'post-new.php', 'edit.php' );

	if ( ! in_array( $hook, $allowed_hooks, true ) ) {
		return;
	}

	wp_enqueue_style(
		'thevotex-admin',
		thevotex_asset_url( 'assets/css/admin.css' ),
		array(),
		thevotex_asset_version( 'assets/css/admin.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'thevotex_enqueue_admin_assets' );

// ---------------------------------------------------------------------------
// Chatbot widget
// ---------------------------------------------------------------------------

/**
 * Enqueues the third-party chatbot widget script.
 *
 * Only loads when all three conditions are met:
 *   1. The Customizer toggle is enabled.
 *   2. A chatbot ID has been saved.
 *   3. An API URL has been saved.
 *
 * Uses a named callback for the script_loader_tag filter so it can
 * be removed cleanly by child themes or plugins.
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_maybe_enqueue_chatbot(): void {
	$enabled    = (bool) get_theme_mod( 'thevotex_chatbot_enabled', false );
	$chatbot_id = sanitize_text_field( get_theme_mod( 'thevotex_chatbot_id', '' ) );
	$api_url    = esc_url_raw( get_theme_mod( 'thevotex_chatbot_api', '' ) );

	if ( ! $enabled || empty( $chatbot_id ) || empty( $api_url ) ) {
		return;
	}

	$widget_src = trailingslashit( $api_url ) . 'widget.js';

	wp_enqueue_script(
		'thevotex-chatbot',
		esc_url( $widget_src ),
		array(),
		null,   // External versioned script — no mtime needed.
		true    // Footer.
	);

	// Store in a global so the named tag filter can access the values.
	// This avoids a closure (closures cannot be removed with remove_filter).
	$GLOBALS['thevotex_chatbot_data'] = array(
		'id'      => $chatbot_id,
		'api_url' => $api_url,
	);

	add_filter( 'script_loader_tag', 'thevotex_chatbot_script_tag', 20, 2 );
}

/**
 * Injects data attributes on the chatbot <script> tag.
 *
 * Named function — can be unhooked by child themes:
 *   remove_filter( 'script_loader_tag', 'thevotex_chatbot_script_tag', 20 );
 *
 * @since  1.0.0
 * @param  string $tag    Full <script> HTML tag.
 * @param  string $handle Script handle.
 * @return string         Modified tag, or original tag if not the chatbot handle.
 */
function thevotex_chatbot_script_tag( string $tag, string $handle ): string {
	if ( 'thevotex-chatbot' !== $handle ) {
		return $tag;
	}

	$data = $GLOBALS['thevotex_chatbot_data'] ?? array();

	if ( empty( $data ) ) {
		return $tag;
	}

	$attrs = sprintf(
		' data-chatbot-id="%s" data-api-url="%s"',
		esc_attr( $data['id'] ),
		esc_url( $data['api_url'] )
	);

	return str_replace( ' src=', $attrs . ' src=', $tag );
}

// ---------------------------------------------------------------------------
// Customizer live CSS
// ---------------------------------------------------------------------------

/**
 * Builds a CSS string of :root custom property overrides driven by
 * Customizer settings. Output is appended inline after thevotex-main
 * via wp_add_inline_style() — no extra HTTP request.
 *
 * Each property maps a Customizer setting key to a CSS variable name.
 * Add new overridable tokens to the $overrides array; the loop
 * handles validation and output.
 *
 * @since  1.0.0
 * @return string Inline CSS block, or empty string if nothing is overridden.
 */
function thevotex_build_customizer_css(): string {
	/*
	 * Map: Customizer setting key => CSS custom property name.
	 * 'type' determines the sanitization applied before output.
	 * Supported types: 'color', 'url', 'text'.
	 */
	$overrides = array(
		array(
			'mod'      => 'thevotex_gold_override',
			'property' => '--thevotex-gold',
			'type'     => 'color',
			'default'  => '',
		),
		array(
			'mod'      => 'thevotex_black_override',
			'property' => '--thevotex-black',
			'type'     => 'color',
			'default'  => '',
		),
	);

	$declarations = array();

	foreach ( $overrides as $override ) {
		$value = get_theme_mod( $override['mod'], $override['default'] );

		if ( empty( $value ) || $value === $override['default'] ) {
			continue;
		}

		switch ( $override['type'] ) {
			case 'color':
				$safe = sanitize_hex_color( $value );
				break;
			case 'url':
				$safe = esc_url( $value );
				break;
			default:
				$safe = sanitize_text_field( $value );
				break;
		}

		if ( ! empty( $safe ) ) {
			$declarations[] = sprintf( '%s:%s', $override['property'], $safe );
		}
	}

	if ( empty( $declarations ) ) {
		return '';
	}

	return ':root{' . implode( ';', $declarations ) . '}';
}

// ---------------------------------------------------------------------------
// Utility helpers (enqueue-scope)
// ---------------------------------------------------------------------------

/**
 * Checks whether the current page uses a named page template.
 *
 * Accepts the slug without path prefix or .php extension for brevity.
 * Internally resolves both 'template-reservation' and
 * 'page-templates/template-reservation.php'.
 *
 * @since  1.0.0
 * @param  string $slug Template slug (e.g. 'template-reservation').
 * @return bool
 */
function thevotex_is_page_template( string $slug ): bool {
	// Try with path prefix first (matches the theme's folder structure).
	if ( is_page_template( 'page-templates/' . $slug . '.php' ) ) {
		return true;
	}

	// Fallback: slug without a path (handles ad-hoc placements).
	return is_page_template( $slug . '.php' );
}

/**
 * Returns true if the current request is any WooCommerce context.
 * Returns false safely when WooCommerce is not installed — no fatal.
 *
 * @since  1.0.0
 * @return bool
 */
function thevotex_is_woocommerce_page(): bool {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}

	return is_woocommerce()
		|| is_cart()
		|| is_checkout()
		|| is_account_page()
		|| is_wc_endpoint_url();
}

/**
 * Returns the active WooCommerce currency symbol.
 * Falls back to '$' when WooCommerce is not installed.
 *
 * Using get_woocommerce_currency_symbol() directly in
 * wp_localize_script() would throw a fatal error on sites
 * without WooCommerce — this wrapper prevents that.
 *
 * @since  1.0.0
 * @return string Currency symbol.
 */
function thevotex_get_currency_symbol(): string {
	if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
		return get_woocommerce_currency_symbol();
	}

	return '$';
}
