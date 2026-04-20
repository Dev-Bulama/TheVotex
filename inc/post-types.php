<?php
/**
 * Custom post type and taxonomy registration.
 *
 * All CPTs use a 'thevotex_' prefix to avoid collisions with
 * plugins or other themes. Registered on 'init' at priority 0
 * so rewrite rules are available to flush_rewrite_rules() calls.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all custom post types.
 */
function thevotex_register_post_types(): void {

	/* ── Events ──────────────────────────────────────────── */
	register_post_type(
		'thevotex_event',
		array(
			'labels'        => array(
				'name'               => __( 'Events', 'thevotex' ),
				'singular_name'      => __( 'Event', 'thevotex' ),
				'add_new_item'       => __( 'Add New Event', 'thevotex' ),
				'edit_item'          => __( 'Edit Event', 'thevotex' ),
				'new_item'           => __( 'New Event', 'thevotex' ),
				'view_item'          => __( 'View Event', 'thevotex' ),
				'search_items'       => __( 'Search Events', 'thevotex' ),
				'not_found'          => __( 'No events found', 'thevotex' ),
				'not_found_in_trash' => __( 'No events found in trash', 'thevotex' ),
				'menu_name'          => __( 'Events', 'thevotex' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 5,
			'supports'      => array( 'title', 'thumbnail', 'editor', 'custom-fields', 'page-attributes' ),
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'events', 'with_front' => false ),
		)
	);

	/* ── DJs / Artists ───────────────────────────────────── */
	register_post_type(
		'thevotex_dj',
		array(
			'labels'        => array(
				'name'               => __( 'Artists', 'thevotex' ),
				'singular_name'      => __( 'Artist', 'thevotex' ),
				'add_new_item'       => __( 'Add New Artist', 'thevotex' ),
				'edit_item'          => __( 'Edit Artist', 'thevotex' ),
				'menu_name'          => __( 'Artists', 'thevotex' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-format-audio',
			'menu_position' => 6,
			'supports'      => array( 'title', 'thumbnail', 'editor', 'custom-fields', 'page-attributes' ),
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'artists', 'with_front' => false ),
		)
	);

	/* ── Bottle Service Packages ─────────────────────────── */
	register_post_type(
		'thevotex_package',
		array(
			'labels'        => array(
				'name'               => __( 'Packages', 'thevotex' ),
				'singular_name'      => __( 'Package', 'thevotex' ),
				'add_new_item'       => __( 'Add New Package', 'thevotex' ),
				'edit_item'          => __( 'Edit Package', 'thevotex' ),
				'menu_name'          => __( 'Packages', 'thevotex' ),
			),
			'public'        => false,
			'publicly_queryable' => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-star-filled',
			'menu_position' => 7,
			'supports'      => array( 'title', 'editor', 'custom-fields', 'page-attributes' ),
			'has_archive'   => false,
		)
	);

	/* ── Food Menu Items ─────────────────────────────────── */
	register_post_type(
		'thevotex_food_item',
		array(
			'labels'        => array(
				'name'               => __( 'Food Items', 'thevotex' ),
				'singular_name'      => __( 'Food Item', 'thevotex' ),
				'add_new_item'       => __( 'Add New Food Item', 'thevotex' ),
				'edit_item'          => __( 'Edit Food Item', 'thevotex' ),
				'menu_name'          => __( 'Food Menu', 'thevotex' ),
			),
			'public'        => false,
			'publicly_queryable' => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-food',
			'menu_position' => 8,
			'supports'      => array( 'title', 'custom-fields', 'page-attributes' ),
			'has_archive'   => false,
		)
	);

	/* ── Reservations (private log) ──────────────────────── */
	register_post_type(
		'thevotex_reservation',
		array(
			'labels'        => array(
				'name'               => __( 'Reservations', 'thevotex' ),
				'singular_name'      => __( 'Reservation', 'thevotex' ),
				'menu_name'          => __( 'Reservations', 'thevotex' ),
			),
			'public'        => false,
			'publicly_queryable' => false,
			'show_ui'       => true,
			'show_in_rest'  => false, // Never expose reservation data via REST.
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-clipboard',
			'menu_position' => 9,
			'supports'      => array( 'title', 'custom-fields' ),
			'has_archive'   => false,
			'capabilities'  => array(
				'create_posts' => 'do_not_allow', // Only created programmatically.
			),
			'map_meta_cap'  => true,
		)
	);
}
add_action( 'init', 'thevotex_register_post_types', 0 );

/**
 * Register custom taxonomies.
 */
function thevotex_register_taxonomies(): void {

	/* ── Event Genre ─────────────────────────────────────── */
	register_taxonomy(
		'event_genre',
		array( 'thevotex_event' ),
		array(
			'labels'        => array(
				'name'              => __( 'Genres', 'thevotex' ),
				'singular_name'     => __( 'Genre', 'thevotex' ),
				'search_items'      => __( 'Search Genres', 'thevotex' ),
				'all_items'         => __( 'All Genres', 'thevotex' ),
				'edit_item'         => __( 'Edit Genre', 'thevotex' ),
				'update_item'       => __( 'Update Genre', 'thevotex' ),
				'add_new_item'      => __( 'Add New Genre', 'thevotex' ),
				'new_item_name'     => __( 'New Genre Name', 'thevotex' ),
				'menu_name'         => __( 'Genres', 'thevotex' ),
			),
			'hierarchical'  => false,
			'public'        => true,
			'show_in_rest'  => true,
			'rewrite'       => array( 'slug' => 'genre' ),
		)
	);

	/* ── Food Category ───────────────────────────────────── */
	register_taxonomy(
		'food_category',
		array( 'thevotex_food_item' ),
		array(
			'labels'        => array(
				'name'              => __( 'Food Categories', 'thevotex' ),
				'singular_name'     => __( 'Food Category', 'thevotex' ),
				'menu_name'         => __( 'Categories', 'thevotex' ),
			),
			'hierarchical'  => true,
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'rewrite'       => false,
		)
	);
}
add_action( 'init', 'thevotex_register_taxonomies', 0 );

/**
 * Flush rewrite rules on theme activation only.
 * Never flush on every request — severe performance impact.
 */
function thevotex_flush_rewrite_rules(): void {
	thevotex_register_post_types();
	thevotex_register_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'thevotex_flush_rewrite_rules' );
