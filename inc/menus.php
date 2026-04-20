<?php
/**
 * Navigation Menus
 *
 * Covers three responsibilities:
 *
 *  1. Menu location registration   — four named locations matching the
 *                                     static HTML nav architecture.
 *  2. Walker classes               — THEvotex_Walker_Nav for the desktop
 *                                     header nav and THEvotex_Walker_Mobile_Nav
 *                                     for the full-screen mobile overlay.
 *  3. Template helper functions    — thin wrappers around wp_nav_menu()
 *                                     with per-location defaults pre-applied,
 *                                     so template parts stay readable.
 *
 * Design decisions:
 *  - Walkers extend Walker_Nav_Menu rather than re-implementing Walker,
 *    so core security patches propagate automatically.
 *  - Active-state detection translates WordPress's verbose current-menu-*
 *    class set into the single 'active' class the theme CSS expects.
 *  - nav-cta and any other "link classes" set in Appearance → Menus are
 *    forwarded from the <li> to the <a> tag, matching the static HTML.
 *  - External links get rel="noopener noreferrer" automatically.
 *  - aria-current, aria-haspopup, and aria-expanded are injected for
 *    keyboard and screen-reader accessibility.
 *
 * @package THEvotex
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Menu location registration
// ---------------------------------------------------------------------------

/**
 * Registers all navigation menu locations used by the theme.
 *
 * Hooked to 'after_setup_theme' so menu locations appear in
 * Appearance → Menus before any template-parts request them.
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_register_nav_menus(): void {
	register_nav_menus( array(
		'primary'         => __( 'Primary Navigation', 'thevotex' ),
		'mobile'          => __( 'Mobile Navigation', 'thevotex' ),
		'footer-navigate' => __( 'Footer — Navigate', 'thevotex' ),
		'footer-legal'    => __( 'Footer — Legal', 'thevotex' ),
	) );
}
add_action( 'after_setup_theme', 'thevotex_register_nav_menus' );

// ---------------------------------------------------------------------------
// Desktop nav Walker
// ---------------------------------------------------------------------------

/**
 * Custom walker for the fixed desktop navigation bar.
 *
 * Output structure (depth 0):
 *   <li class="has-dropdown menu-item-{id}">
 *     <a href="..." aria-haspopup="true" aria-expanded="false">Label</a>
 *     <ul class="dropdown" role="menu">…</ul>
 *   </li>
 *
 * Output structure (depth 1 — dropdown items):
 *   <li class="dropdown__item menu-item-{id}">
 *     <a href="...">Label</a>
 *   </li>
 *
 * Notes:
 *  - Dropdowns are intentionally capped at one level (depth > 0 sub-menus
 *    are suppressed). The design has no second-level dropdowns.
 *  - Classes entered in the menu editor "CSS Classes" field that appear in
 *    LINK_CLASSES are forwarded to the <a> tag instead of the <li> tag.
 *
 * @since 1.0.0
 */
class THEvotex_Walker_Nav extends Walker_Nav_Menu {

	/**
	 * CSS classes that belong on the <a> tag rather than the <li> tag.
	 * Editors assign these via Appearance → Menus → "CSS Classes" field.
	 *
	 * 'nav-cta' produces the gold-bordered CTA button in the nav bar.
	 *
	 * @var string[]
	 */
	private const LINK_CLASSES = array( 'nav-cta' );

	/**
	 * WordPress class markers that indicate the current/active page.
	 * Translated to a single 'active' class on the <a> tag.
	 *
	 * @var string[]
	 */
	private const ACTIVE_CLASSES = array(
		'current-menu-item',
		'current_page_item',
		'current-menu-ancestor',
		'current_page_ancestor',
		'current-menu-parent',
		'current_page_parent',
	);

	/**
	 * Opens a dropdown <ul> for a depth-0 item with children.
	 * Second-level and deeper dropdowns are suppressed.
	 *
	 * @since  1.0.0
	 * @param  string   $output  Passed by reference. Used to append additional content.
	 * @param  int      $depth   Depth of menu item. Used for padding.
	 * @param  stdClass $args    An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ): void {
		if ( $depth !== 0 ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth + 1 );
		$output .= "\n{$indent}<ul class=\"dropdown\" role=\"menu\" aria-label=\""
			. esc_attr__( 'Submenu', 'thevotex' ) . "\">\n";
	}

	/**
	 * Closes a dropdown <ul>.
	 *
	 * @since  1.0.0
	 * @param  string   $output Passed by reference.
	 * @param  int      $depth  Depth of menu item.
	 * @param  stdClass $args   An object of wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ): void {
		if ( $depth !== 0 ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth + 1 );
		$output .= "{$indent}</ul>\n";
	}

	/**
	 * Outputs the opening <li> and <a> for a single menu item.
	 *
	 * @since  1.0.0
	 * @param  string   $output           Passed by reference.
	 * @param  object   $data_object      Menu item data object (WP_Post with nav meta).
	 * @param  int      $depth            Depth of menu item.
	 * @param  stdClass $args             wp_nav_menu() arguments object.
	 * @param  int      $current_object_id Current menu item ID.
	 * @return void
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ): void {
		/** @var WP_Post $item */
		$item   = $data_object;
		$indent = str_repeat( "\t", $depth );

		// ── <li> classes ─────────────────────────────────────────
		$item_classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$item_classes[] = 'menu-item-' . $item->ID;

		$has_children = in_array( 'menu-item-has-children', $item_classes, true );
		if ( $has_children ) {
			$item_classes[] = 'has-dropdown';
		}

		if ( 0 === $depth && $has_children ) {
			// Aria role for the parent <li> — the toggle is the child <a>.
			$li_role = ' role="none"';
		} else {
			$li_role = '';
		}

		// Depth-1 items get a 'dropdown__item' class for individual styling.
		if ( 1 === $depth ) {
			$item_classes[] = 'dropdown__item';
		}

		// Strip link-only classes from the <li>.
		$li_classes = array_diff( $item_classes, self::LINK_CLASSES );

		$li_class_str = implode(
			' ',
			apply_filters(
				'nav_menu_css_class',
				array_filter( array_unique( $li_classes ) ),
				$item,
				$args,
				$depth
			)
		);

		$li_id = apply_filters(
			'nav_menu_item_id',
			'menu-item-' . $item->ID,
			$item,
			$args,
			$depth
		);

		$output .= "{$indent}<li id=\"" . esc_attr( $li_id ) . "\""
			. " class=\"" . esc_attr( $li_class_str ) . "\""
			. $li_role // Already-safe literal string.
			. ">\n";

		// ── <a> attributes ────────────────────────────────────────
		$link_classes = array();

		// Translate WP current-page classes → single 'active' class.
		if ( array_intersect( self::ACTIVE_CLASSES, $item_classes ) ) {
			$link_classes[] = 'active';
		}

		// Forward editor-assigned link classes to the <a>.
		$forwarded    = array_intersect( (array) $item->classes, self::LINK_CLASSES );
		$link_classes = array_merge( $link_classes, array_values( $forwarded ) );

		$atts = array(
			'href'  => ! empty( $item->url )        ? $item->url        : '#',
			'title' => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'class' => implode( ' ', array_filter( $link_classes ) ),
		);

		// Target + rel.
		if ( ! empty( $item->target ) ) {
			$atts['target'] = $item->target;
		}

		if ( '_blank' === ( $item->target ?? '' ) ) {
			// Security: external tabs must not get opener access.
			$existing_rel = ! empty( $item->xfn ) ? trim( $item->xfn ) . ' ' : '';
			$atts['rel']  = $existing_rel . 'noopener noreferrer';
		} elseif ( ! empty( $item->xfn ) ) {
			$atts['rel'] = $item->xfn;
		}

		// Accessibility.
		if ( in_array( 'current-menu-item', $item_classes, true ) ) {
			$atts['aria-current'] = 'page';
		}
		if ( $has_children && 0 === $depth ) {
			$atts['aria-haspopup'] = 'true';
			$atts['aria-expanded'] = 'false';
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attr_str = '';
		foreach ( $atts as $name => $value ) {
			if ( '' !== $value ) {
				$safe       = ( 'href' === $name ) ? esc_url( $value ) : esc_attr( $value );
				$attr_str  .= " {$name}=\"{$safe}\"";
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before ?? '';
		$item_output .= "<a{$attr_str}>";
		$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
		$item_output .= '</a>';
		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Outputs the closing </li>.
	 *
	 * @since  1.0.0
	 * @param  string   $output      Passed by reference.
	 * @param  object   $data_object Menu item data object.
	 * @param  int      $depth       Depth of menu item.
	 * @param  stdClass $args        wp_nav_menu() arguments object.
	 * @return void
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ): void {
		$output .= "</li>\n";
	}
}

// ---------------------------------------------------------------------------
// Mobile overlay Walker
// ---------------------------------------------------------------------------

/**
 * Custom walker for the full-screen mobile navigation overlay.
 *
 * Output structure (depth 0, no children):
 *   <li class="mobile-menu__item menu-item-{id}">
 *     <a href="...">Home</a>
 *   </li>
 *
 * Output structure (depth 0, with children):
 *   <li class="mobile-nav-item menu-item-{id}" id="mobile-toggle-{id}">
 *     <a class="mobile-main-link" aria-expanded="false" href="#">
 *       Menu <span class="arrow" aria-hidden="true">▾</span>
 *     </a>
 *     <ul class="mobile-sub">…</ul>
 *   </li>
 *
 * Output structure (depth 1):
 *   <li class="mobile-sub__item menu-item-{id}">
 *     <a href="...">Bottle Service</a>
 *   </li>
 *
 * @since 1.0.0
 */
class THEvotex_Walker_Mobile_Nav extends Walker_Nav_Menu {

	/** @var string[] WP active page class markers. */
	private const ACTIVE_CLASSES = array(
		'current-menu-item',
		'current_page_item',
		'current-menu-ancestor',
		'current_page_ancestor',
	);

	/**
	 * Opens a mobile sub-menu <ul>.
	 *
	 * @since  1.0.0
	 * @param  string   $output Passed by reference.
	 * @param  int      $depth  Depth of menu item.
	 * @param  stdClass $args   wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ): void {
		if ( $depth !== 0 ) {
			return;
		}

		$output .= "\t\t<ul class=\"mobile-sub\" role=\"menu\">\n";
	}

	/**
	 * Closes a mobile sub-menu <ul>.
	 *
	 * @since  1.0.0
	 * @param  string   $output Passed by reference.
	 * @param  int      $depth  Depth of menu item.
	 * @param  stdClass $args   wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ): void {
		if ( $depth !== 0 ) {
			return;
		}

		$output .= "\t\t</ul>\n";
	}

	/**
	 * Outputs the opening <li> and <a> for a single mobile menu item.
	 *
	 * @since  1.0.0
	 * @param  string   $output           Passed by reference.
	 * @param  object   $data_object      Menu item.
	 * @param  int      $depth            Depth of menu item.
	 * @param  stdClass $args             wp_nav_menu() arguments.
	 * @param  int      $current_object_id Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ): void {
		/** @var WP_Post $item */
		$item         = $data_object;
		$item_classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $item_classes, true );
		$is_active    = (bool) array_intersect( self::ACTIVE_CLASSES, $item_classes );

		// ── <li> ─────────────────────────────────────────────────
		if ( 0 === $depth ) {
			$li_class = $has_children ? 'mobile-nav-item' : 'mobile-menu__item';
		} else {
			$li_class = 'mobile-sub__item';
		}

		if ( $is_active ) {
			$li_class .= ' is-active';
		}

		$li_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

		// Parent items get a distinct ID used by JS accordion toggle.
		$mobile_id = ( 0 === $depth && $has_children )
			? ' id="mobile-toggle-' . esc_attr( (string) $item->ID ) . '"'
			: '';

		$output .= "\t<li id=\"" . esc_attr( $li_id ) . "\""
			. " class=\"" . esc_attr( $li_class ) . "\""
			. $mobile_id // Already safe (string from esc_attr above).
			. ">\n";

		// ── <a> ───────────────────────────────────────────────────
		$link_class = '';
		$extra_html = '';

		if ( 0 === $depth && $has_children ) {
			$link_class = 'mobile-main-link';
			// The arrow span is toggled by JS and rotated via CSS.
			$extra_html = '<span class="arrow" aria-hidden="true">&#9662;</span>';
		}

		$atts = array(
			'href'  => ( 0 === $depth && $has_children )
				? ( ! empty( $item->url ) && '#' !== $item->url ? $item->url : '#' )
				: ( ! empty( $item->url ) ? $item->url : '#' ),
			'class' => $link_class,
		);

		if ( ! empty( $item->attr_title ) ) {
			$atts['title'] = $item->attr_title;
		}
		if ( ! empty( $item->target ) ) {
			$atts['target'] = $item->target;
		}
		if ( '_blank' === ( $item->target ?? '' ) ) {
			$existing_rel = ! empty( $item->xfn ) ? trim( $item->xfn ) . ' ' : '';
			$atts['rel']  = $existing_rel . 'noopener noreferrer';
		} elseif ( ! empty( $item->xfn ) ) {
			$atts['rel'] = $item->xfn;
		}
		if ( $is_active ) {
			$atts['aria-current'] = 'page';
		}
		if ( 0 === $depth && $has_children ) {
			$atts['aria-expanded'] = 'false';
			$atts['aria-controls'] = 'mobile-toggle-' . esc_attr( (string) $item->ID );
		}

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attr_str = '';
		foreach ( $atts as $name => $value ) {
			if ( '' !== $value ) {
				$safe      = ( 'href' === $name ) ? esc_url( $value ) : esc_attr( $value );
				$attr_str .= " {$name}=\"{$safe}\"";
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before ?? '';
		$item_output .= "<a{$attr_str}>";
		$item_output .= ( $args->link_before ?? '' ) . esc_html( $title ) . $extra_html . ( $args->link_after ?? '' );
		$item_output .= '</a>';
		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Outputs the closing </li>.
	 *
	 * @since  1.0.0
	 * @param  string   $output      Passed by reference.
	 * @param  object   $data_object Menu item.
	 * @param  int      $depth       Depth of menu item.
	 * @param  stdClass $args        wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ): void {
		$output .= "\t</li>\n";
	}
}

// ---------------------------------------------------------------------------
// Template helper functions
// ---------------------------------------------------------------------------

/**
 * Renders a navigation menu using the correct Walker and defaults
 * for the given theme location.
 *
 * Usage in template-parts:
 *   thevotex_nav_menu( 'primary' );
 *   thevotex_nav_menu( 'footer-navigate', array( 'items_wrap' => '<ul class="footer-links">%3$s</ul>' ) );
 *
 * @since  1.0.0
 * @param  string  $location Theme menu location slug.
 * @param  array   $args     Optional overrides merged on top of the defaults.
 * @return void
 */
function thevotex_nav_menu( string $location, array $args = array() ): void {
	$defaults = array(
		'theme_location' => $location,
		'container'      => false,
		'echo'           => true,
		'fallback_cb'    => 'thevotex_nav_fallback',
		'walker'         => new THEvotex_Walker_Nav(),
		'items_wrap'     => '<ul class="nav-links" role="list" aria-label="%2$s">%3$s</ul>',
		'depth'          => 2,
	);

	wp_nav_menu( array_merge( $defaults, $args ) );
}

/**
 * Renders the mobile navigation overlay menu.
 *
 * @since  1.0.0
 * @param  array $args Optional overrides.
 * @return void
 */
function thevotex_mobile_nav_menu( array $args = array() ): void {
	$defaults = array(
		'theme_location' => 'mobile',
		'container'      => false,
		'echo'           => true,
		'fallback_cb'    => 'thevotex_nav_fallback',
		'walker'         => new THEvotex_Walker_Mobile_Nav(),
		'items_wrap'     => '<ul class="mobile-menu__list" role="list">%3$s</ul>',
		'depth'          => 2,
	);

	// Fall back to the primary location if no dedicated mobile menu is assigned.
	if ( ! has_nav_menu( 'mobile' ) && has_nav_menu( 'primary' ) ) {
		$defaults['theme_location'] = 'primary';
	}

	wp_nav_menu( array_merge( $defaults, $args ) );
}

/**
 * Renders the footer navigation menu.
 *
 * @since  1.0.0
 * @param  array $args Optional overrides.
 * @return void
 */
function thevotex_footer_nav_menu( array $args = array() ): void {
	$defaults = array(
		'theme_location' => 'footer-navigate',
		'container'      => false,
		'echo'           => true,
		'fallback_cb'    => '__return_false',
		'walker'         => new THEvotex_Walker_Nav(),
		'items_wrap'     => '<ul class="footer-links" role="list">%3$s</ul>',
		'depth'          => 1,
	);

	wp_nav_menu( array_merge( $defaults, $args ) );
}

/**
 * Checks whether a menu location has a menu assigned to it.
 * Thin wrapper kept for brevity in template-parts.
 *
 * @since  1.0.0
 * @param  string $location Theme menu location slug.
 * @return bool
 */
function thevotex_has_nav_menu( string $location ): bool {
	return has_nav_menu( $location );
}

/**
 * Fallback callback for wp_nav_menu() when no menu is assigned
 * to a location. Renders a minimal set of page links so the nav
 * is never visually empty on a fresh install.
 *
 * WordPress calls this function with the same $args array passed
 * to wp_nav_menu(). Output must be echoed (not returned) unless
 * $args['echo'] is false.
 *
 * @since  1.0.0
 * @param  array $args wp_nav_menu() arguments array (not stdClass).
 * @return void
 */
function thevotex_nav_fallback( array $args ): void {
	$pages = get_pages( array(
		'sort_column' => 'menu_order',
		'number'      => 10,
	) );

	if ( empty( $pages ) ) {
		return;
	}

	$items = '';
	foreach ( $pages as $page ) {
		$current = ( is_page( $page->ID ) ) ? ' class="active" aria-current="page"' : '';
		$items  .= '<li><a href="' . esc_url( get_permalink( $page->ID ) ) . '"' . $current . '>'
			. esc_html( $page->post_title )
			. '</a></li>';
	}

	$wrap  = $args['items_wrap'] ?? '<ul>%3$s</ul>';
	$label = $args['menu_class'] ?? '';
	$html  = sprintf( $wrap, '', esc_attr( $label ), $items );

	if ( ! isset( $args['echo'] ) || $args['echo'] ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- content is already escaped above.
	} else {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
