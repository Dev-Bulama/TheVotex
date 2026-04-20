<?php
/**
 * Theme Setup
 *
 * Declares all add_theme_support() flags, registers image sizes,
 * navigation menus, widget areas, editor styles, and block patterns.
 *
 * Everything in this file is hooked to 'after_setup_theme' or
 * 'widgets_init' — the two safe registration points WordPress
 * guarantees run before any output.
 *
 * @package THEvotex
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Content width — caps oEmbed / media at 1400 px.
// Declared outside the hook so it is available during 'template_redirect'.
// ---------------------------------------------------------------------------
if ( ! isset( $content_width ) ) {
	$content_width = 1400; // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals
}

// ---------------------------------------------------------------------------
// Core theme setup
// ---------------------------------------------------------------------------

/**
 * Bootstraps all add_theme_support() declarations, image sizes,
 * navigation menus, and editor styles.
 *
 * @since 1.0.0
 * @return void
 */
function thevotex_setup(): void {

	// ── Translations ────────────────────────────────────────────
	load_theme_textdomain(
		'thevotex',
		get_template_directory() . '/languages'
	);

	// ── WordPress core feature flags ────────────────────────────

	/*
	 * Let WordPress manage the <title> tag.
	 * Required by ThemeForest: themes must NOT hard-code <title>.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable featured images (post thumbnails) on all post types.
	 * Individual post types can opt in/out via add_post_type_support().
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Output RSS/Atom feed <link> tags automatically in <head>.
	 */
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Use semantic HTML5 markup for core-generated forms and media.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );

	/*
	 * Allow blocks to use the "wide" and "full" alignment classes.
	 * Required for Elementor full-width sections to render correctly.
	 */
	add_theme_support( 'align-wide' );

	/*
	 * Responsive iframes — WordPress wraps oEmbeds in a container
	 * and adds the necessary CSS for aspect-ratio preservation.
	 */
	add_theme_support( 'responsive-embeds' );

	/*
	 * Enable live preview / selective refresh for widgets in the
	 * Customizer without a full page reload.
	 */
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * Register a wp_body_open() hook so theme parts and plugins
	 * (e.g. Google Tag Manager, OneSignal) can inject markup
	 * immediately after <body> without editing header.php.
	 */
	add_theme_support( 'body-open' );

	// ── Custom logo ──────────────────────────────────────────────
	add_theme_support( 'custom-logo', array(
		'height'               => 80,
		'width'                => 240,
		'flex-height'          => true,
		'flex-width'           => true,
		'unlink-homepage-logo' => true,
		'header-text'          => array( 'site-title', 'site-description' ),
	) );

	// ── Block editor: colour palette ─────────────────────────────
	/*
	 * Expose the theme's design token colours in the block editor
	 * colour picker. Editors can pick brand colours without entering
	 * hex values manually.
	 */
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
			'name'  => __( 'Gold Dark', 'thevotex' ),
			'slug'  => 'thevotex-gold-dk',
			'color' => '#a87830',
		),
		array(
			'name'  => __( 'Black', 'thevotex' ),
			'slug'  => 'thevotex-black',
			'color' => '#020204',
		),
		array(
			'name'  => __( 'Surface Dark', 'thevotex' ),
			'slug'  => 'thevotex-dark',
			'color' => '#07070f',
		),
		array(
			'name'  => __( 'Card', 'thevotex' ),
			'slug'  => 'thevotex-card',
			'color' => '#0b0b14',
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

	// ── Block editor: font sizes ──────────────────────────────────
	add_theme_support( 'editor-font-sizes', array(
		array(
			'name' => __( 'Small', 'thevotex' ),
			'slug' => 'small',
			'size' => 12,
		),
		array(
			'name' => __( 'Normal', 'thevotex' ),
			'slug' => 'normal',
			'size' => 14,
		),
		array(
			'name' => __( 'Large', 'thevotex' ),
			'slug' => 'large',
			'size' => 18,
		),
		array(
			'name' => __( 'Display', 'thevotex' ),
			'slug' => 'display',
			'size' => 42,
		),
	) );

	/*
	 * Lock editors to the declared font sizes above.
	 * Prevents off-brand arbitrary sizes from being entered.
	 */
	add_theme_support( 'disable-custom-font-sizes' );

	/*
	 * Lock editors to the declared colour palette above.
	 */
	add_theme_support( 'disable-custom-colors' );

	// ── Block editor: gradient palette ───────────────────────────
	add_theme_support( 'editor-gradient-presets', array(
		array(
			'name'     => __( 'Gold Gradient', 'thevotex' ),
			'slug'     => 'thevotex-gold-gradient',
			'gradient' => 'linear-gradient(135deg, #a87830 0%, #f0d080 50%, #c9a84c 100%)',
		),
		array(
			'name'     => __( 'Dark to Card', 'thevotex' ),
			'slug'     => 'thevotex-dark-gradient',
			'gradient' => 'linear-gradient(180deg, #07070f 0%, #0b0b14 100%)',
		),
	) );

	// ── Block editor styles ───────────────────────────────────────
	/*
	 * Loads editor.min.css inside the block editor iframe so the
	 * editor canvas visually matches the front-end. Paths are
	 * relative to the theme root.
	 */
	add_editor_style( 'assets/css/editor.min.css' );

	// ── WooCommerce ───────────────────────────────────────────────
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 400,
		'single_image_width'    => 800,
		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 3,
			'min_columns'     => 1,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// ── Elementor ─────────────────────────────────────────────────
	/*
	 * 'elementor' — signals to Elementor that the theme is
	 * compatible, enabling the full-width page layout option.
	 *
	 * 'header-footer-elementor' — used by Elementor Theme Builder
	 * and the Header Footer Elementor plugin to replace the theme's
	 * header and footer with Elementor-built templates.
	 */
	add_theme_support( 'elementor' );
	add_theme_support( 'header-footer-elementor' );

	// ── Image sizes ───────────────────────────────────────────────
	/*
	 * All sizes hard-crop so images are predictably framed.
	 * Naming follows the 'thevotex-{context}' convention to avoid
	 * collisions with core or plugin sizes.
	 */
	add_image_size( 'thevotex-hero',    1920, 1080, true ); // Full-screen hero slides.
	add_image_size( 'thevotex-hero-md',  960,  540, true ); // Hero on tablet/mobile.
	add_image_size( 'thevotex-event',    900,  600, true ); // Event card featured image.
	add_image_size( 'thevotex-card',     600,  800, true ); // Portrait card (DJ tease).
	add_image_size( 'thevotex-dj',       500,  360, true ); // DJ roster grid card.
	add_image_size( 'thevotex-thumb',    400,  400, true ); // Square thumbnail.
	add_image_size( 'thevotex-og',      1200,  630, true ); // Open Graph social share.

	// ── Navigation menus ──────────────────────────────────────────
	register_nav_menus( array(
		'primary'          => __( 'Primary Navigation', 'thevotex' ),
		'mobile'           => __( 'Mobile Navigation', 'thevotex' ),
		'footer-navigate'  => __( 'Footer — Navigate', 'thevotex' ),
		'footer-legal'     => __( 'Footer — Legal', 'thevotex' ),
	) );
}
add_action( 'after_setup_theme', 'thevotex_setup' );

// ---------------------------------------------------------------------------
// Widget areas (sidebars)
// ---------------------------------------------------------------------------

/**
 * Registers all widget areas used by the theme.
 *
 * The main site layout does not use traditional sidebars, but widget
 * areas are provided for the footer columns and any plugin that needs
 * a widgetised region (e.g. Elementor popups, GDPR banners).
 *
 * @since 1.0.0
 * @return void
 */
function thevotex_register_widget_areas(): void {

	$shared_args = array(
		'before_widget' => '<div id="%1$s" class="thevotex-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="thevotex-widget__title">',
		'after_title'   => '</h4>',
	);

	// Footer column — displayed inside the footer's 4-column grid.
	register_sidebar( array_merge( $shared_args, array(
		'id'          => 'footer-col',
		'name'        => __( 'Footer Column', 'thevotex' ),
		'description' => __( 'Widgets placed here appear in the footer. The footer layout will accommodate up to one widget area.', 'thevotex' ),
	) ) );

	// Off-canvas / popup area — for GDPR, newsletter overlays, etc.
	register_sidebar( array_merge( $shared_args, array(
		'id'          => 'offcanvas',
		'name'        => __( 'Off-Canvas / Popup', 'thevotex' ),
		'description' => __( 'Widgets placed here are injected before </body>. Useful for GDPR banners or newsletter pop-ups.', 'thevotex' ),
	) ) );

	// Blog sidebar — only rendered on archive / single-post templates.
	register_sidebar( array_merge( $shared_args, array(
		'id'          => 'blog-sidebar',
		'name'        => __( 'Blog Sidebar', 'thevotex' ),
		'description' => __( 'Appears on blog archive and single post pages.', 'thevotex' ),
	) ) );
}
add_action( 'widgets_init', 'thevotex_register_widget_areas' );

// ---------------------------------------------------------------------------
// Block patterns
// ---------------------------------------------------------------------------

/**
 * Registers custom block patterns for the Gutenberg inserter.
 *
 * Patterns give editors one-click access to pre-built layouts that
 * match the theme's design language. Each pattern is a PHP string
 * of serialised block markup — stored here rather than in a
 * /patterns directory to keep the /inc structure self-contained.
 *
 * @since 1.0.0
 * @return void
 */
function thevotex_register_block_patterns(): void {

	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	// Register a custom pattern category for the theme.
	register_block_pattern_category(
		'thevotex',
		array( 'label' => __( 'THEvotex', 'thevotex' ) )
	);

	// ── CTA Banner pattern ────────────────────────────────────────
	register_block_pattern(
		'thevotex/cta-banner',
		array(
			'title'         => __( 'Call-to-Action Banner', 'thevotex' ),
			'description'   => __( 'Full-width dark banner with a gold heading and button, matching the About page CTA.', 'thevotex' ),
			'categories'    => array( 'thevotex', 'call-to-action' ),
			'keywords'      => array( 'cta', 'banner', 'button', 'gold' ),
			'viewportWidth' => 1400,
			'content'       => '<!-- wp:group {"className":"thevotex-cta-banner","layout":{"type":"constrained"}} -->
<div class="wp-block-group thevotex-cta-banner">
<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">' . esc_html__( 'READY TO EXPERIENCE IT?', 'thevotex' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","className":"thevotex-cta-banner__sub"} -->
<p class="has-text-align-center thevotex-cta-banner__sub">' . esc_html__( 'Reserve your table or VIP package tonight.', 'thevotex' ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-thevotex-gold"} -->
<div class="wp-block-button is-style-thevotex-gold"><a class="wp-block-button__link wp-element-button" href="/contact">' . esc_html__( 'Book Your Night →', 'thevotex' ) . '</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->',
		)
	);

	// ── Section label + heading pattern ──────────────────────────
	register_block_pattern(
		'thevotex/section-header',
		array(
			'title'       => __( 'Section Label + Heading', 'thevotex' ),
			'description' => __( 'Gold uppercase section label with a large Bebas Neue heading below it.', 'thevotex' ),
			'categories'  => array( 'thevotex', 'text' ),
			'keywords'    => array( 'heading', 'label', 'section', 'title' ),
			'content'     => '<!-- wp:paragraph {"className":"thevotex-section-label"} -->
<p class="thevotex-section-label">' . esc_html__( 'Section Label', 'thevotex' ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2,"className":"thevotex-section-title"} -->
<h2 class="wp-block-heading thevotex-section-title">' . esc_html__( 'SECTION HEADING', 'thevotex' ) . '</h2>
<!-- /wp:heading -->',
		)
	);
}
add_action( 'init', 'thevotex_register_block_patterns' );

// ---------------------------------------------------------------------------
// Body classes
// ---------------------------------------------------------------------------

/**
 * Appends theme-specific body classes.
 *
 * Added classes are used by CSS and JS to scope styles and
 * behaviours per template without inline conditionals in markup.
 *
 * @since  1.0.0
 * @param  string[] $classes Existing body classes from WordPress.
 * @return string[]          Merged body class list.
 */
function thevotex_body_classes( array $classes ): array {

	// Flag pages using the reservation template for scoped CSS/JS.
	if ( is_page_template( 'page-templates/template-reservation.php' ) ) {
		$classes[] = 'is-reservation-page';
	}

	// Flag when a custom logo is set so the fallback text-logo is hidden.
	if ( has_custom_logo() ) {
		$classes[] = 'has-custom-logo';
	}

	// Flag for Elementor full-width canvas pages (removes default padding).
	if ( defined( '\Elementor\Plugin::VERSION' ) ) {
		$classes[] = 'elementor-enabled';
	}

	// Remove 'no-sidebar' added by some plugins — not relevant to this theme.
	$classes = array_diff( $classes, array( 'no-sidebar' ) );

	return array_values( $classes );
}
add_filter( 'body_class', 'thevotex_body_classes' );

// ---------------------------------------------------------------------------
// Image size labels (Media Library)
// ---------------------------------------------------------------------------

/**
 * Adds human-readable labels for the custom image sizes in the
 * "Image Size" dropdown of the block editor attachment inspector.
 *
 * @since  1.0.0
 * @param  string[] $sizes Existing size labels keyed by size slug.
 * @return string[]        Merged size labels.
 */
function thevotex_image_size_names( array $sizes ): array {
	return array_merge( $sizes, array(
		'thevotex-hero'    => __( 'THEvotex — Hero (1920×1080)', 'thevotex' ),
		'thevotex-hero-md' => __( 'THEvotex — Hero Medium (960×540)', 'thevotex' ),
		'thevotex-event'   => __( 'THEvotex — Event Card (900×600)', 'thevotex' ),
		'thevotex-card'    => __( 'THEvotex — Portrait Card (600×800)', 'thevotex' ),
		'thevotex-dj'      => __( 'THEvotex — DJ Card (500×360)', 'thevotex' ),
		'thevotex-thumb'   => __( 'THEvotex — Square Thumb (400×400)', 'thevotex' ),
		'thevotex-og'      => __( 'THEvotex — Open Graph (1200×630)', 'thevotex' ),
	) );
}
add_filter( 'image_size_names_choose', 'thevotex_image_size_names' );

// ---------------------------------------------------------------------------
// Excerpt
// ---------------------------------------------------------------------------

/**
 * Sets the auto-generated excerpt word limit.
 *
 * @since  1.0.0
 * @param  int $length Default word count (55).
 * @return int         Theme word count.
 */
function thevotex_excerpt_length( int $length ): int {
	return is_admin() ? $length : 20;
}
add_filter( 'excerpt_length', 'thevotex_excerpt_length' );

/**
 * Replaces the default […] excerpt suffix with a styled ellipsis.
 *
 * @since  1.0.0
 * @param  string $more Default suffix.
 * @return string       Themed suffix.
 */
function thevotex_excerpt_more( string $more ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	return ' &hellip;';
}
add_filter( 'excerpt_more', 'thevotex_excerpt_more' );
