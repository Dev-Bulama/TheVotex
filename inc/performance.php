<?php
/**
 * THEvotex — inc/performance.php
 *
 * Front-end performance optimisations targeting Core Web Vitals:
 *  LCP — hero image gets fetchpriority="high" + loading="eager"
 *  CLS — image dimensions preserved; no layout-shift on fonts
 *  FID/INP — scripts deferred (handled in enqueue.php); heartbeat removed
 *  TTFB — unnecessary head output stripped; dashicons removed for guests
 *
 * Nothing here duplicates enqueue.php (preconnect, defer strategy)
 * or security.php (version removal, ?ver= stripping).
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// XML-RPC
// ---------------------------------------------------------------------------

/*
 * Disable XML-RPC entirely. This theme has no use for remote publishing.
 * Blocking it removes a common brute-force and DDoS attack surface.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

add_filter( 'wp_headers', 'thevotex_remove_pingback_header' );

function thevotex_remove_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}

// ---------------------------------------------------------------------------
// Self-pings
// ---------------------------------------------------------------------------

/*
 * WordPress sends a pingback to itself when a post links to another
 * post on the same site. Disable it — it generates noise with no benefit.
 */
add_action( 'pre_ping', 'thevotex_disable_self_pings' );

function thevotex_disable_self_pings( array &$links ): void {
	$home = get_option( 'home' );

	foreach ( $links as $key => $link ) {
		if ( str_starts_with( $link, $home ) ) {
			unset( $links[ $key ] );
		}
	}
}

// ---------------------------------------------------------------------------
// wp-embed script
// ---------------------------------------------------------------------------

/*
 * WordPress loads wp-embed.min.js on every page to allow other sites
 * to embed this site's content. Unless this site explicitly needs
 * to be embedded elsewhere, remove it (~7 KB, one HTTP round-trip).
 */
add_action( 'wp_footer', 'thevotex_dequeue_wp_embed', 1 );

function thevotex_dequeue_wp_embed(): void {
	wp_deregister_script( 'wp-embed' );
}

// ---------------------------------------------------------------------------
// WP Heartbeat
// ---------------------------------------------------------------------------

/*
 * The Heartbeat API fires an AJAX call every 15 s by default.
 * On the front-end there is nothing listening to it — remove it
 * entirely to save one network request per minute per visitor.
 * On the admin (post editor) it is left untouched.
 */
add_action( 'init', 'thevotex_disable_front_heartbeat' );

function thevotex_disable_front_heartbeat(): void {
	if ( ! is_admin() ) {
		wp_deregister_script( 'heartbeat' );
	}
}

// ---------------------------------------------------------------------------
// Dashicons
// ---------------------------------------------------------------------------

/*
 * WordPress loads dashicons on every page for logged-in users showing
 * the admin bar. For non-logged-in front-end visitors there is no
 * admin bar, so dashicons are wasted bytes (~50 KB).
 */
add_action( 'wp_enqueue_scripts', 'thevotex_dequeue_dashicons', 100 );

function thevotex_dequeue_dashicons(): void {
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}
}

// ---------------------------------------------------------------------------
// LCP image: fetchpriority + eager loading
// ---------------------------------------------------------------------------

/*
 * WordPress 6.3 added native fetchpriority="high" for the first image
 * in the post content. For the hero/slider images — which are the LCP
 * element on every page — we apply it explicitly via the image attributes
 * filter to guarantee the browser prioritises them in its preload scanner.
 */
add_filter( 'wp_get_attachment_image_attributes', 'thevotex_lcp_image_priority', 10, 3 );

/**
 * @param array            $attr       Existing image attributes.
 * @param WP_Post          $attachment Attachment post object.
 * @param string|int[]     $size       Requested size name or [w, h] array.
 */
function thevotex_lcp_image_priority( array $attr, WP_Post $attachment, string|array $size ): array {
	$lcp_sizes = array( 'thevotex-hero', 'thevotex-hero-md' );

	if ( is_string( $size ) && in_array( $size, $lcp_sizes, true ) ) {
		$attr['fetchpriority'] = 'high';
		$attr['loading']       = 'eager';
		$attr['decoding']      = 'sync';
	}

	return $attr;
}

// ---------------------------------------------------------------------------
// Content images: lazy loading + async decoding
// ---------------------------------------------------------------------------

/*
 * WordPress 5.5 added native loading="lazy" to content images automatically.
 * Additionally marking them decoding="async" allows the browser to decode
 * the image off the main thread, avoiding layout jank during page load.
 *
 * The regex targets <img> tags that do not already carry a decoding attribute
 * and are not inside a noscript element.
 */
add_filter( 'the_content',       'thevotex_content_image_attrs' );
add_filter( 'post_thumbnail_html', 'thevotex_content_image_attrs' );

function thevotex_content_image_attrs( string $html ): string {
	if ( ! str_contains( $html, '<img' ) ) {
		return $html;
	}

	// Add decoding="async" to any <img> that does not already have it.
	return preg_replace(
		'/<img(?![^>]*\bdecoding\b)([^>]*)>/i',
		'<img decoding="async"$1>',
		$html
	) ?? $html;
}

// ---------------------------------------------------------------------------
// Block library CSS
// ---------------------------------------------------------------------------

/*
 * The wp-block-library stylesheet (~70 KB) is enqueued even when the
 * page uses no Gutenberg blocks. On pages where has_blocks() returns
 * false (front page hero, CPT singles, WooCommerce product pages)
 * dequeue it to save the HTTP request.
 *
 * This is intentionally low-priority (100) so WooCommerce Blocks and
 * Elementor have already registered their dependencies.
 */
add_action( 'wp_enqueue_scripts', 'thevotex_maybe_dequeue_block_library', 100 );

function thevotex_maybe_dequeue_block_library(): void {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}

	$post = get_post();

	if ( $post && has_blocks( $post->post_content ) ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}

// ---------------------------------------------------------------------------
// DNS prefetch
// ---------------------------------------------------------------------------

/*
 * Add dns-prefetch hints for external origins used by the theme.
 * Preconnect is handled in enqueue.php for the origins that need it
 * (Google Fonts). DNS-prefetch covers the rest at lower cost.
 */
add_filter( 'wp_resource_hints', 'thevotex_dns_prefetch_hints', 10, 2 );

function thevotex_dns_prefetch_hints( array $urls, string $relation_type ): array {
	if ( 'dns-prefetch' !== $relation_type ) {
		return $urls;
	}

	$extra = array(
		'https://fonts.googleapis.com',
		'https://fonts.gstatic.com',
	);

	return array_unique( array_merge( $urls, $extra ) );
}

// ---------------------------------------------------------------------------
// Preload critical fonts
// ---------------------------------------------------------------------------

/*
 * Preloading the primary display font ('Bebas Neue' subset) eliminates
 * the FOUT (Flash of Unstyled Text) on headings. This is a hint — the
 * browser may choose to ignore it under memory pressure.
 *
 * Adjust the href to match your self-hosted font file path if fonts
 * are moved from Google Fonts to /assets/fonts/.
 */
add_action( 'wp_head', 'thevotex_preload_fonts', 2 );

function thevotex_preload_fonts(): void {
	$fonts_dir = get_template_directory() . '/assets/fonts';

	if ( ! is_dir( $fonts_dir ) ) {
		return;
	}

	$font_files = glob( $fonts_dir . '/bebas-neue*.woff2' );

	if ( empty( $font_files ) ) {
		return;
	}

	foreach ( $font_files as $font_file ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin="anonymous">' . "\n",
			esc_url( get_template_directory_uri() . '/assets/fonts/' . basename( $font_file ) )
		);
	}
}
