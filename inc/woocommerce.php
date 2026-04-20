<?php
/**
 * THEvotex — inc/woocommerce.php
 *
 * WooCommerce integration hooks. Loaded only when WooCommerce is active.
 *
 * Responsibilities:
 *  - Remove WooCommerce's default content wrappers (theme owns the shell).
 *  - Filter loop columns and products-per-page via Customizer settings.
 *  - Adjust breadcrumb markup to match theme conventions.
 *  - Limit related products to 3 (matches the 3-column shop grid).
 *  - Add WooCommerce-aware body classes.
 *
 * add_theme_support( 'woocommerce' ), gallery-zoom, lightbox, and slider
 * are declared in inc/setup.php inside thevotex_setup().
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

// ---------------------------------------------------------------------------
// Content wrappers
// ---------------------------------------------------------------------------

/*
 * WooCommerce ships open/close wrappers via these hooks. Since woocommerce.php
 * already wraps the content in .container, the defaults would produce
 * a double-wrapped layout. Remove them.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper',     10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar',             'woocommerce_get_sidebar',                10 );

// ---------------------------------------------------------------------------
// Shop grid
// ---------------------------------------------------------------------------

add_filter( 'loop_shop_columns',  'thevotex_woo_loop_columns' );
add_filter( 'loop_shop_per_page', 'thevotex_woo_products_per_page', 20 );

function thevotex_woo_loop_columns(): int {
	return max( 1, (int) get_theme_mod( 'thevotex_woo_columns', 3 ) );
}

function thevotex_woo_products_per_page(): int {
	return max( 1, (int) get_theme_mod( 'thevotex_woo_per_page', 12 ) );
}

// ---------------------------------------------------------------------------
// Related products
// ---------------------------------------------------------------------------

add_filter( 'woocommerce_output_related_products_args', 'thevotex_woo_related_args' );

function thevotex_woo_related_args( array $args ): array {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}

// ---------------------------------------------------------------------------
// Breadcrumbs
// ---------------------------------------------------------------------------

add_filter( 'woocommerce_breadcrumb_defaults', 'thevotex_woo_breadcrumb_args' );

function thevotex_woo_breadcrumb_args( array $args ): array {
	$args['delimiter']   = '<span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb thevotex-breadcrumb" aria-label="'
		. esc_attr__( 'Breadcrumb', 'thevotex' ) . '">';
	$args['wrap_after']  = '</nav>';
	return $args;
}

// ---------------------------------------------------------------------------
// Body classes
// ---------------------------------------------------------------------------

add_filter( 'body_class', 'thevotex_woo_body_classes' );

function thevotex_woo_body_classes( array $classes ): array {
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		$classes[] = 'thevotex-woo-page';
	}
	return $classes;
}

// ---------------------------------------------------------------------------
// Product thumbnail columns (gallery strip)
// ---------------------------------------------------------------------------

add_filter( 'woocommerce_product_thumbnails_columns', function (): int {
	return 4;
} );

// ---------------------------------------------------------------------------
// Cart fragments — keep AJAX cart working
// ---------------------------------------------------------------------------

add_filter( 'woocommerce_add_to_cart_fragments', 'thevotex_woo_cart_count_fragment' );

function thevotex_woo_cart_count_fragment( array $fragments ): array {
	$fragments['.thevotex-cart-count'] = sprintf(
		'<span class="thevotex-cart-count">%d</span>',
		WC()->cart ? WC()->cart->get_cart_contents_count() : 0
	);
	return $fragments;
}
