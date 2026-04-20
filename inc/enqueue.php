<?php
/**
 * Asset enqueueing — styles and scripts with proper
 * dependency chains, versioning, and conditional loading.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a cache-busted version string for a theme asset.
 * Uses filemtime() in development; falls back to theme version
 * if the file does not exist (e.g. during CI or first deploy).
 *
 * @param string $relative_path Path relative to the theme root.
 * @return string Version string.
 */
function thevotex_asset_version( string $relative_path ): string {
	$abs = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $abs ) ) {
		return (string) filemtime( $abs );
	}

	return THEVOTEX_VERSION;
}

/**
 * Returns the full URI for a theme asset.
 *
 * @param string $relative_path Path relative to the theme root.
 * @return string Full URI.
 */
function thevotex_asset_uri( string $relative_path ): string {
	return get_template_directory_uri() . '/' . ltrim( $relative_path, '/' );
}

/**
 * Enqueue front-end styles.
 */
function thevotex_enqueue_styles(): void {

	/* Google Fonts — preconnect declared in wp_head via separate hook */
	wp_enqueue_style(
		'thevotex-google-fonts',
		'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap',
		array(),
		null // External — no version hash needed.
	);

	/* Main compiled stylesheet */
	wp_enqueue_style(
		'thevotex-main',
		thevotex_asset_uri( 'assets/css/theme.min.css' ),
		array( 'thevotex-google-fonts' ),
		thevotex_asset_version( 'assets/css/theme.min.css' )
	);

	/* Reservation page — loaded only when needed */
	if ( thevotex_is_template( 'template-reservation' ) ) {
		wp_enqueue_style(
			'thevotex-reservation',
			thevotex_asset_uri( 'assets/css/reservation.min.css' ),
			array( 'thevotex-main' ),
			thevotex_asset_version( 'assets/css/reservation.min.css' )
		);
	}

	/* WooCommerce compatibility layer */
	if ( thevotex_is_woocommerce_page() ) {
		wp_enqueue_style(
			'thevotex-woocommerce',
			thevotex_asset_uri( 'assets/css/woocommerce.min.css' ),
			array( 'thevotex-main' ),
			thevotex_asset_version( 'assets/css/woocommerce.min.css' )
		);
	}

	/* WordPress core block styles — only on pages using blocks */
	if ( has_blocks() ) {
		wp_enqueue_style( 'wp-block-library' );
	}
}
add_action( 'wp_enqueue_scripts', 'thevotex_enqueue_styles' );

/**
 * Enqueue front-end scripts.
 */
function thevotex_enqueue_scripts(): void {

	/* Deregister jQuery from footer; re-register in footer */
	wp_deregister_script( 'jquery' );
	wp_register_script(
		'jquery',
		includes_url( '/js/jquery/jquery.min.js' ),
		array(),
		null,
		true // Load in footer.
	);

	/* Core theme JS — cursor, nav scroll, hero slider, reveal */
	wp_enqueue_script(
		'thevotex-main',
		thevotex_asset_uri( 'assets/js/theme.min.js' ),
		array(),
		thevotex_asset_version( 'assets/js/theme.min.js' ),
		true
	);

	/*
	 * Pass PHP data to JS.
	 * Kept minimal — only what JS cannot determine on its own.
	 */
	wp_localize_script(
		'thevotex-main',
		'thevotexData',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'thevotex_nonce' ),
			'siteName'     => get_bloginfo( 'name' ),
			'currency'     => get_woocommerce_currency_symbol(),
			'sliderSpeed'  => (int) get_theme_mod( 'thevotex_slider_speed', 5500 ),
			'chatbotId'    => sanitize_text_field( get_theme_mod( 'thevotex_chatbot_id', '' ) ),
			'chatbotApi'   => esc_url( get_theme_mod( 'thevotex_chatbot_api', '' ) ),
		)
	);

	/* Reservation page — multi-step wizard JS */
	if ( thevotex_is_template( 'template-reservation' ) ) {
		wp_enqueue_script(
			'thevotex-reservation',
			thevotex_asset_uri( 'assets/js/reservation.min.js' ),
			array( 'thevotex-main' ),
			thevotex_asset_version( 'assets/js/reservation.min.js' ),
			true
		);
	}

	/* Comment reply script — only where needed */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'thevotex_enqueue_scripts' );

/**
 * Add <link rel="preconnect"> for Google Fonts in <head>.
 * Must fire before thevotex-google-fonts stylesheet.
 */
function thevotex_preconnect_fonts(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'thevotex_preconnect_fonts', 1 );

/**
 * Enqueue block editor styles so the editor matches the front-end.
 */
function thevotex_enqueue_editor_styles(): void {
	wp_enqueue_style(
		'thevotex-editor',
		thevotex_asset_uri( 'assets/css/editor.min.css' ),
		array(),
		thevotex_asset_version( 'assets/css/editor.min.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'thevotex_enqueue_editor_styles' );

/**
 * Enqueue WP admin styles for custom columns and ACF UX.
 */
function thevotex_enqueue_admin_styles( string $hook ): void {
	$cpt_screens = array( 'post.php', 'post-new.php', 'edit.php' );

	if ( ! in_array( $hook, $cpt_screens, true ) ) {
		return;
	}

	wp_enqueue_style(
		'thevotex-admin',
		thevotex_asset_uri( 'assets/css/admin.css' ),
		array(),
		thevotex_asset_version( 'assets/css/admin.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'thevotex_enqueue_admin_styles' );

/**
 * Conditionally load the Skilly chatbot widget.
 * Only injected when the Customizer ID and API URL are both set
 * and the toggle is enabled.
 */
function thevotex_maybe_enqueue_chatbot(): void {
	$chatbot_enabled = (bool) get_theme_mod( 'thevotex_chatbot_enabled', false );
	$chatbot_id      = sanitize_text_field( get_theme_mod( 'thevotex_chatbot_id', '' ) );
	$chatbot_api     = esc_url( get_theme_mod( 'thevotex_chatbot_api', '' ) );

	if ( ! $chatbot_enabled || empty( $chatbot_id ) || empty( $chatbot_api ) ) {
		return;
	}

	// Register without src — the src is set via the data attributes pattern.
	wp_enqueue_script(
		'thevotex-chatbot',
		esc_url( $chatbot_api . '/widget.js' ),
		array(),
		null,
		true
	);

	// Append data attributes after the script tag is output.
	add_filter( 'script_loader_tag', function( string $tag, string $handle ) use ( $chatbot_id, $chatbot_api ): string {
		if ( 'thevotex-chatbot' !== $handle ) {
			return $tag;
		}

		return str_replace(
			'<script ',
			'<script data-chatbot-id="' . esc_attr( $chatbot_id ) . '" data-api-url="' . esc_url( $chatbot_api ) . '" ',
			$tag
		);
	}, 10, 2 );
}
add_action( 'wp_enqueue_scripts', 'thevotex_maybe_enqueue_chatbot' );

/* ── Helpers ────────────────────────────────────────────────── */

/**
 * Checks whether the current page uses a specific page template.
 *
 * @param string $template_slug Slug without '.php' extension.
 * @return bool
 */
function thevotex_is_template( string $template_slug ): bool {
	return is_page_template( $template_slug . '.php' );
}

/**
 * Safely checks for an active WooCommerce page.
 * Returns false if WooCommerce is not installed.
 *
 * @return bool
 */
function thevotex_is_woocommerce_page(): bool {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}

	return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}
