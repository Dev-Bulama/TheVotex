<?php
/**
 * THEvotex — inc/compatibility.php
 *
 * Thin integration shims for commonly-used plugins.
 * Every block is guarded by function_exists() / class_exists() / defined()
 * so no code runs when the plugin is absent.
 *
 * Plugins covered:
 *  - Advanced Custom Fields (ACF / ACF Pro)
 *  - Contact Form 7
 *  - WPForms
 *  - Yoast SEO
 *  - Rank Math SEO
 *  - Elementor / Elementor Pro
 *  - General body-class additions
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Advanced Custom Fields
// ---------------------------------------------------------------------------

if ( function_exists( 'acf' ) || class_exists( 'ACF' ) ) {
	/*
	 * Prevent ACF from removing the core WordPress Custom Fields metabox.
	 * Keeping it visible avoids confusion when editors switch between
	 * ACF fields and native post meta.
	 */
	add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );

	/*
	 * Exclude the internal Reservation CPT from ACF's field-group UI.
	 * Reservations are managed programmatically — no editor-facing fields.
	 */
	add_filter( 'acf/get_post_types', 'thevotex_acf_exclude_cpts' );

	function thevotex_acf_exclude_cpts( array $post_types ): array {
		return array_diff( $post_types, array( 'thevotex_reservation' ) );
	}
}

// ---------------------------------------------------------------------------
// Contact Form 7
// ---------------------------------------------------------------------------

if ( defined( 'WPCF7_VERSION' ) ) {
	/*
	 * Disable CF7's own scripts/styles. The theme bundles any required
	 * form resets inside assets/css/theme.min.css.
	 * Re-enqueue selectively on pages that actually contain a CF7 shortcode
	 * so forms render correctly without loading assets everywhere.
	 */
	add_filter( 'wpcf7_load_js',  '__return_false' );
	add_filter( 'wpcf7_load_css', '__return_false' );

	add_action( 'wp_enqueue_scripts', 'thevotex_cf7_selective_enqueue', 20 );

	function thevotex_cf7_selective_enqueue(): void {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_post();

		if ( $post && has_shortcode( $post->post_content, 'contact-form-7' ) ) {
			wpcf7_enqueue_scripts();
			wpcf7_enqueue_styles();
		}
	}
}

// ---------------------------------------------------------------------------
// WPForms
// ---------------------------------------------------------------------------

if ( function_exists( 'wpforms' ) ) {
	/*
	 * WPForms loads its scripts on every page by default.
	 * Disable global loading; the plugin enqueues what it needs when a
	 * form shortcode is detected on the current page.
	 */
	add_filter( 'wpforms_global_assets', '__return_false' );
}

// ---------------------------------------------------------------------------
// Gravity Forms
// ---------------------------------------------------------------------------

if ( class_exists( 'GFForms' ) ) {
	/*
	 * Disable Gravity Forms' CSS on all pages.
	 * The theme provides base form styles via its own stylesheet.
	 */
	add_filter( 'pre_option_rg_gforms_disable_css', '__return_true' );
}

// ---------------------------------------------------------------------------
// Yoast SEO
// ---------------------------------------------------------------------------

if ( defined( 'WPSEO_VERSION' ) ) {
	/*
	 * Remove the Yoast metabox from the Reservation CPT.
	 * Reservation records are internal; they should not be indexed.
	 */
	add_action( 'add_meta_boxes', 'thevotex_yoast_remove_from_private_cpts', 100 );

	function thevotex_yoast_remove_from_private_cpts(): void {
		remove_meta_box( 'wpseo_meta', 'thevotex_reservation', 'normal' );
	}

	/*
	 * Tell Yoast to treat the Reservation CPT as non-public so it is
	 * excluded from the sitemap automatically.
	 */
	add_filter( 'wpseo_accessible_post_types', 'thevotex_yoast_exclude_cpts' );

	function thevotex_yoast_exclude_cpts( array $post_types ): array {
		unset( $post_types['thevotex_reservation'] );
		return $post_types;
	}
}

// ---------------------------------------------------------------------------
// Rank Math SEO
// ---------------------------------------------------------------------------

if ( defined( 'RANK_MATH_VERSION' ) ) {
	/*
	 * Exclude the Reservation CPT from Rank Math's analysis and sitemap.
	 */
	add_filter( 'rank_math/excluded_post_types', 'thevotex_rankmath_exclude_cpts' );

	function thevotex_rankmath_exclude_cpts( array $post_types ): array {
		$post_types[] = 'thevotex_reservation';
		return $post_types;
	}
}

// ---------------------------------------------------------------------------
// Elementor
// ---------------------------------------------------------------------------

if ( defined( 'ELEMENTOR_VERSION' ) ) {
	/*
	 * When Elementor's Theme Builder renders a custom header/footer,
	 * it fires before/after_body hooks that may output duplicate cursor
	 * elements. Prevent the theme's cursor partial from duplicating.
	 *
	 * Elementor Pro manages its own canvas wrapper; on canvas-template
	 * pages the theme wrapper classes are unnecessary.
	 */
	add_action( 'elementor/theme/register_conditions', 'thevotex_elementor_conditions', 10, 1 );

	function thevotex_elementor_conditions( $conditions_manager ): void {
		// Reserved for custom Elementor display conditions if needed.
	}

	/*
	 * Suppress the theme's page title on Elementor-edited pages where
	 * the editor controls the heading through a widget instead.
	 */
	add_filter( 'thevotex_show_page_title', 'thevotex_elementor_hide_page_title' );

	function thevotex_elementor_hide_page_title( bool $show ): bool {
		if (
			is_singular()
			&& \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() )
		) {
			return false;
		}

		return $show;
	}
}

// ---------------------------------------------------------------------------
// Body class additions
// ---------------------------------------------------------------------------

add_filter( 'body_class', 'thevotex_plugin_body_classes' );

function thevotex_plugin_body_classes( array $classes ): array {
	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'woocommerce-active';
	}

	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$classes[] = 'elementor-active';
	}

	if ( function_exists( 'acf' ) || class_exists( 'ACF' ) ) {
		$classes[] = 'acf-active';
	}

	return $classes;
}
