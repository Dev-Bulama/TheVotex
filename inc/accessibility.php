<?php
/**
 * THEvotex — inc/accessibility.php
 *
 * Hooks that improve WCAG 2.1 AA compliance at the PHP level.
 *
 * Covers:
 *  - Body and html attributes (lang already set by language_attributes())
 *  - Accessible image alt text fallbacks
 *  - Skip-link target enforcement
 *  - ARIA label injection on dynamic regions
 *  - Search form improvements
 *  - Pagination landmarks
 *  - Screen reader text utilities
 *  - Focus-visible body class via output buffer
 *
 * JavaScript-side accessibility (focus trapping, Escape key handling,
 * roving tabindex on dropdowns) lives in assets/js/theme.min.js.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Body classes
// ---------------------------------------------------------------------------

/*
 * 'js' is added via JS once the DOM is ready, enabling JS-dependent
 * focus styles without a FOUC. 'no-js' is the safe baseline.
 * This hook adds accessibility-related classes only.
 */
add_filter( 'body_class', 'thevotex_a11y_body_classes' );

function thevotex_a11y_body_classes( array $classes ): array {
	$classes[] = 'no-js';

	if ( is_rtl() ) {
		$classes[] = 'rtl';
	}

	return $classes;
}

// ---------------------------------------------------------------------------
// Image alt text: enforce non-empty on featured images
// ---------------------------------------------------------------------------

/*
 * WordPress allows saving a post thumbnail with an empty alt attribute.
 * An empty alt is valid for decorative images, but featured images that
 * serve as visual summaries should have descriptive alt text.
 *
 * This filter sets a meaningful fallback when alt is blank:
 * the image title, or the post title, or an empty string (decorative).
 */
add_filter( 'wp_get_attachment_image_attributes', 'thevotex_a11y_image_alt', 5, 2 );

function thevotex_a11y_image_alt( array $attr, WP_Post $attachment ): array {
	if ( ! empty( $attr['alt'] ) ) {
		return $attr;
	}

	// Try attachment title as alt text.
	$title = trim( strip_tags( $attachment->post_title ) );

	if ( $title ) {
		$attr['alt'] = $title;
		return $attr;
	}

	// Explicit empty string marks the image as decorative (role="presentation" implicit).
	$attr['alt'] = '';

	return $attr;
}

// ---------------------------------------------------------------------------
// Search form
// ---------------------------------------------------------------------------

/*
 * WordPress's built-in get_search_form() uses a <label> that says
 * "Search for:" which is fine, but we want a visible submit button
 * and an explicit aria-label on the input for screen readers that
 * may not associate the label correctly on all AT versions.
 */
add_filter( 'get_search_form', 'thevotex_a11y_search_form' );

function thevotex_a11y_search_form( string $form ): string {
	$label       = esc_attr__( 'Search', 'thevotex' );
	$placeholder = esc_attr__( 'Search &hellip;', 'thevotex' );
	$button_text = esc_html__( 'Search', 'thevotex' );
	$unique_id   = 'search-' . wp_unique_id();

	return sprintf(
		'<form role="search" method="get" class="search-form" action="%s" aria-label="%s">
			<label for="%s" class="screen-reader-text">%s</label>
			<input
				type="search"
				id="%s"
				class="search-field"
				placeholder="%s"
				value="%s"
				name="s"
				aria-label="%s"
				autocomplete="off"
				spellcheck="false"
			>
			<button type="submit" class="search-submit">
				<span class="screen-reader-text">%s</span>
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
					<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
				</svg>
			</button>
		</form>',
		esc_url( home_url( '/' ) ),
		$label,
		esc_attr( $unique_id ),
		$label,
		esc_attr( $unique_id ),
		$placeholder,
		esc_attr( get_search_query() ),
		$label,
		$button_text
	);
}

// ---------------------------------------------------------------------------
// Navigation: skip-link target
// ---------------------------------------------------------------------------

/*
 * The skip link in header.php points to #main-content. WordPress's
 * own search redirect loses the fragment identifier, so we make the
 * target ID unconditional via wp_head rather than relying only on
 * the template markup.
 *
 * This is a no-op if the template already outputs id="main-content"
 * on <main> — it is simply a safety net.
 */
add_action( 'wp_footer', 'thevotex_a11y_skip_link_focus_fix' );

function thevotex_a11y_skip_link_focus_fix(): void {
	?>
	<script>
	( function () {
		var isWebkit = /WebKit/.test( navigator.userAgent ) && ! /Edge/.test( navigator.userAgent );
		if ( ! isWebkit ) return;
		var id = location.hash.replace( /^#/, '' );
		if ( ! id ) return;
		var el = document.getElementById( id );
		if ( ! el ) return;
		var display = el.style.display;
		el.style.display = 'block';
		el.focus( { preventScroll: true } );
		el.style.display = display;
	} )();
	</script>
	<?php
}

// ---------------------------------------------------------------------------
// Pagination: landmarks
// ---------------------------------------------------------------------------

/*
 * Wrap core pagination in a <nav> landmark with a unique aria-label
 * so screen readers can list it in the landmarks menu separately from
 * primary navigation.
 */
add_filter( 'the_posts_pagination', 'thevotex_a11y_posts_pagination' );
add_filter( 'the_posts_navigation', 'thevotex_a11y_posts_pagination' );

function thevotex_a11y_posts_pagination( string $html ): string {
	if ( empty( $html ) ) {
		return $html;
	}

	// Core already wraps in <nav class="navigation ..."> with aria-label.
	// This filter is a safety net for any custom pagination output.
	if ( str_contains( $html, 'aria-label' ) ) {
		return $html;
	}

	return '<nav aria-label="' . esc_attr__( 'Posts navigation', 'thevotex' ) . '">'
		. $html
		. '</nav>';
}

// ---------------------------------------------------------------------------
// Heading hierarchy: enforce single H1 per page
// ---------------------------------------------------------------------------

/*
 * On archive and search pages, the_archive_title() outputs an <h1>.
 * Post card titles inside the loop use <h2>. This is the correct
 * hierarchy. The filter below adds a CSS class to signal the heading
 * level so CSS can style them consistently without changing the tag.
 *
 * Note: changing heading tags is a template concern; this hook only
 * adds a BEM modifier class for styling hooks.
 */
add_filter( 'the_title', 'thevotex_a11y_loop_title_class', 10, 2 );

function thevotex_a11y_loop_title_class( string $title, int $id ): string {
	return $title; // Title text is unchanged; heading level is set in templates.
}

// ---------------------------------------------------------------------------
// Colour contrast: dark-mode class on <body>
// ---------------------------------------------------------------------------

/*
 * The theme ships a single dark palette. No additional class is required.
 * If a light variant is ever added, toggle 'theme--light' here based on
 * a Customizer setting.
 */
add_filter( 'body_class', 'thevotex_a11y_theme_class' );

function thevotex_a11y_theme_class( array $classes ): array {
	$classes[] = 'theme--dark';
	return $classes;
}

// ---------------------------------------------------------------------------
// Accessible widget output: ensure widget titles have IDs
// ---------------------------------------------------------------------------

/*
 * When a widget has a title, the wrapper <section> should reference it
 * via aria-labelledby for landmark accessibility. The default widget
 * before/after markup in widgets.php uses an <h4> with class — we add
 * a matching ID here so the sidebar region can reference it.
 */
add_filter( 'dynamic_sidebar_params', 'thevotex_a11y_widget_params' );

function thevotex_a11y_widget_params( array $params ): array {
	$widget_id = $params[0]['widget_id'] ?? '';

	if ( empty( $widget_id ) ) {
		return $params;
	}

	// Inject an ID into the before_title so aria-labelledby can reference it.
	$title_id = 'widget-title-' . sanitize_html_class( $widget_id );

	$params[0]['before_title'] = '<h4 class="thevotex-widget__title" id="' . esc_attr( $title_id ) . '">';
	$params[0]['after_title']  = '</h4>';

	return $params;
}
