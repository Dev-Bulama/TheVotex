<?php
/**
 * Theme setup — add_theme_support declarations, image sizes,
 * text domain, and content width.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets the content width global in pixels.
 * Caps embedded media (oEmbed, images) at 1400px.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1400;
}

/**
 * Core theme setup.
 * Fires on 'after_setup_theme' — safe to call add_theme_support() here.
 */
function thevotex_setup(): void {

	/* ── Translations ─────────────────────────────────────── */
	load_theme_textdomain( 'thevotex', get_template_directory() . '/languages' );

	/* ── WordPress core features ─────────────────────────── */
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	/* ── Custom logo ─────────────────────────────────────── */
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );

	/* ── Selective refresh for Customizer widgets ────────── */
	add_theme_support( 'customize-selective-refresh-widgets' );

	/* ── Wide / full-width block alignment ───────────────── */
	add_theme_support( 'align-wide' );

	/* ── Block editor colour palette (matches CSS tokens) ── */
	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Gold', 'thevotex' ),
			'slug'  => 'thevotex-gold',
			'color' => '#c9a84c',
		),
		array(
			'name'  => __( 'Gold Light', 'thevotex' ),
			'slug'  => 'thevotex-gold-lt',
			'color' => '#f0d080',
		),
		array(
			'name'  => __( 'Black', 'thevotex' ),
			'slug'  => 'thevotex-black',
			'color' => '#020204',
		),
		array(
			'name'  => __( 'Off White', 'thevotex' ),
			'slug'  => 'thevotex-white',
			'color' => '#f0eee8',
		),
		array(
			'name'  => __( 'Muted', 'thevotex' ),
			'slug'  => 'thevotex-muted',
			'color' => '#7a7a8a',
		),
	) );

	/* ── Disable default block font sizes (use theme tokens) */
	add_theme_support( 'disable-custom-font-sizes' );

	/* ── WooCommerce ──────────────────────────────────────── */
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	/* ── Elementor ───────────────────────────────────────── */
	// Elementor reads this to enable full-width page layouts.
	add_theme_support( 'elementor' );

	// Expose the theme's header/footer to Elementor Theme Builder.
	add_theme_support( 'header-footer-elementor' );

	/* ── Image sizes ─────────────────────────────────────── */
	add_image_size( 'thevotex-hero',    1920, 1080, true );
	add_image_size( 'thevotex-event',    900,  600, true );
	add_image_size( 'thevotex-card',     600,  800, true );
	add_image_size( 'thevotex-dj',       500,  360, true );
	add_image_size( 'thevotex-thumb',    400,  400, true );

	/* ── Navigation menus ────────────────────────────────── */
	register_nav_menus( array(
		'primary'        => __( 'Primary Navigation', 'thevotex' ),
		'mobile'         => __( 'Mobile Navigation', 'thevotex' ),
		'footer-navigate' => __( 'Footer — Navigate', 'thevotex' ),
		'footer-legal'   => __( 'Footer — Legal', 'thevotex' ),
	) );
}
add_action( 'after_setup_theme', 'thevotex_setup' );
