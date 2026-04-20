<?php
/**
 * Theme helper functions — pure utilities used across template
 * files. No side effects, no hooks registered here.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a Customizer theme mod value with a typed fallback.
 *
 * @param string $key      Customizer setting key (without 'thevotex_' prefix).
 * @param mixed  $fallback Default value if setting is not saved.
 * @return mixed
 */
function thevotex_option( string $key, mixed $fallback = '' ): mixed {
	return get_theme_mod( 'thevotex_' . $key, $fallback );
}

/**
 * Outputs the gold horizontal divider element.
 *
 * @return void
 */
function thevotex_divider(): void {
	echo '<div class="thevotex-gold-divider" aria-hidden="true"></div>' . "\n";
}

/**
 * Returns the versioned URI of a theme asset.
 *
 * @param string $path Relative path from theme root (e.g. 'assets/images/logo.svg').
 * @return string Full URL with version query string.
 */
function thevotex_asset( string $path ): string {
	$abs = get_template_directory() . '/' . ltrim( $path, '/' );
	$ver = file_exists( $abs ) ? filemtime( $abs ) : THEVOTEX_VERSION;

	return get_template_directory_uri() . '/' . ltrim( $path, '/' ) . '?v=' . $ver;
}

/**
 * Returns an array of social platform data from Customizer settings.
 * Only includes platforms where a URL has been saved.
 *
 * @return array<int, array{platform: string, url: string, label: string}>
 */
function thevotex_social_links(): array {
	$platforms = array(
		array(
			'platform' => 'instagram',
			'key'      => 'social_instagram',
			'label'    => __( 'Instagram', 'thevotex' ),
		),
		array(
			'platform' => 'facebook',
			'key'      => 'social_facebook',
			'label'    => __( 'Facebook', 'thevotex' ),
		),
		array(
			'platform' => 'tiktok',
			'key'      => 'social_tiktok',
			'label'    => __( 'TikTok', 'thevotex' ),
		),
		array(
			'platform' => 'twitter',
			'key'      => 'social_twitter',
			'label'    => __( 'Twitter / X', 'thevotex' ),
		),
	);

	$links = array();

	foreach ( $platforms as $item ) {
		$url = esc_url( thevotex_option( $item['key'] ) );

		if ( ! empty( $url ) ) {
			$links[] = array(
				'platform' => $item['platform'],
				'url'      => $url,
				'label'    => $item['label'],
			);
		}
	}

	return $links;
}

/**
 * Returns the operating hours as a structured array.
 * Keys are lowercase day names; values are open/close strings.
 * Days with no hours saved are excluded from the array.
 *
 * @return array<string, array{open: string, close: string}>
 */
function thevotex_hours(): array {
	$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
	$hours = array();

	foreach ( $days as $day ) {
		$open  = sanitize_text_field( thevotex_option( 'hours_' . $day . '_open', '' ) );
		$close = sanitize_text_field( thevotex_option( 'hours_' . $day . '_close', '' ) );

		if ( ! empty( $open ) && ! empty( $close ) ) {
			$hours[ $day ] = array(
				'open'  => $open,
				'close' => $close,
			);
		}
	}

	return $hours;
}

/**
 * Returns the venue contact details from Customizer.
 *
 * @return array{address: string, city: string, email: string, phone: string}
 */
function thevotex_contact_info(): array {
	return array(
		'address' => sanitize_text_field( thevotex_option( 'contact_address', '219 17 Avenue SW' ) ),
		'city'    => sanitize_text_field( thevotex_option( 'contact_city', 'Calgary, AB' ) ),
		'email'   => sanitize_email( thevotex_option( 'contact_email', 'info@thevotex.com' ) ),
		'phone'   => sanitize_text_field( thevotex_option( 'contact_phone', '+1 (587) 439-1168' ) ),
	);
}

/**
 * Runs a WP_Query for the thevotex_package CPT, ordered by menu_order.
 * Used by bottle-grid.php and reservation/pkg-tabs.php.
 *
 * @param int $limit Max packages to return. -1 returns all.
 * @return WP_Query
 */
function thevotex_get_packages( int $limit = -1 ): WP_Query {
	return new WP_Query( array(
		'post_type'      => 'thevotex_package',
		'posts_per_page' => $limit,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	) );
}

/**
 * Runs a WP_Query for the thevotex_event CPT.
 * Returns upcoming events ordered by ACF event_date field.
 *
 * @param int $limit Max events to return.
 * @return WP_Query
 */
function thevotex_get_upcoming_events( int $limit = 6 ): WP_Query {
	return new WP_Query( array(
		'post_type'      => 'thevotex_event',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'meta_key'       => 'event_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => 'event_date',
				'value'   => current_time( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	) );
}

/**
 * Runs a WP_Query for the thevotex_dj CPT.
 *
 * @param int $limit Max DJs to return.
 * @return WP_Query
 */
function thevotex_get_djs( int $limit = -1 ): WP_Query {
	return new WP_Query( array(
		'post_type'      => 'thevotex_dj',
		'posts_per_page' => $limit,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	) );
}

/**
 * Runs a WP_Query for the thevotex_food_item CPT,
 * optionally filtered by food_category taxonomy term.
 *
 * @param string $category_slug Optional taxonomy term slug.
 * @return WP_Query
 */
function thevotex_get_food_items( string $category_slug = '' ): WP_Query {
	$args = array(
		'post_type'      => 'thevotex_food_item',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	);

	if ( ! empty( $category_slug ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'food_category',
				'field'    => 'slug',
				'terms'    => sanitize_title( $category_slug ),
			),
		);
	}

	return new WP_Query( $args );
}

/**
 * Formats a numeric price for display.
 * Returns 'POA' if value is empty or zero.
 *
 * @param int|float|string $amount Raw price value.
 * @param string           $symbol Currency symbol.
 * @return string Formatted price string.
 */
function thevotex_format_price( int|float|string $amount, string $symbol = '$' ): string {
	$amount = (float) $amount;

	if ( $amount <= 0 ) {
		return __( 'POA', 'thevotex' );
	}

	return esc_html( $symbol . number_format( $amount, 0 ) );
}

/**
 * Outputs a section label element — gold uppercase small caps
 * with a trailing line, used above every section heading.
 *
 * @param string $text Label text.
 * @return void
 */
function thevotex_section_label( string $text ): void {
	echo '<div class="thevotex-section-label">' . esc_html( $text ) . '</div>' . "\n";
}

/**
 * Returns true if Advanced Custom Fields is active.
 * Gates all get_field() / have_rows() calls site-wide.
 *
 * @return bool
 */
function thevotex_has_acf(): bool {
	return function_exists( 'get_field' );
}

/**
 * Safe wrapper around ACF get_field().
 * Returns $fallback if ACF is not active or the field is empty.
 *
 * @param string           $key      ACF field key or name.
 * @param int|false        $post_id  Post ID or false for current post.
 * @param mixed            $fallback Value if field is empty.
 * @return mixed
 */
function thevotex_field( string $key, int|false $post_id = false, mixed $fallback = '' ): mixed {
	if ( ! thevotex_has_acf() ) {
		return $fallback;
	}

	$value = get_field( $key, $post_id );

	return ( ! empty( $value ) ) ? $value : $fallback;
}
