<?php
/**
 * WordPress Customizer registration — panels, sections,
 * settings, and controls for all theme options.
 *
 * Settings use 'sanitize_callback' on every control.
 * No unsanitized data is ever written to the database.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all Customizer panels, sections, settings, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function thevotex_customizer_register( WP_Customize_Manager $wp_customize ): void {

	/* ── Panel ───────────────────────────────────────────── */
	$wp_customize->add_panel( 'thevotex_options', array(
		'title'       => __( 'THEvotex Theme Options', 'thevotex' ),
		'description' => __( 'Configure all site-wide settings for THEvotex.', 'thevotex' ),
		'priority'    => 130,
	) );

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Contact & Location
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_contact', array(
		'title' => __( 'Contact & Location', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$contact_fields = array(
		'thevotex_contact_address' => array(
			'label'    => __( 'Street Address', 'thevotex' ),
			'default'  => '219 17 Avenue SW',
			'sanitize' => 'sanitize_text_field',
		),
		'thevotex_contact_city'    => array(
			'label'    => __( 'City & Province', 'thevotex' ),
			'default'  => 'Calgary, AB',
			'sanitize' => 'sanitize_text_field',
		),
		'thevotex_contact_email'   => array(
			'label'    => __( 'Email Address', 'thevotex' ),
			'default'  => 'info@thevotex.com',
			'sanitize' => 'sanitize_email',
		),
		'thevotex_contact_phone'   => array(
			'label'    => __( 'Phone Number', 'thevotex' ),
			'default'  => '+1 (587) 439-1168',
			'sanitize' => 'sanitize_text_field',
		),
	);

	foreach ( $contact_fields as $id => $field ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize'],
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $field['label'],
			'section' => 'thevotex_contact',
			'type'    => 'text',
		) );
	}

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Operating Hours
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_hours', array(
		'title' => __( 'Operating Hours', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$days = array(
		'monday'    => __( 'Monday', 'thevotex' ),
		'tuesday'   => __( 'Tuesday', 'thevotex' ),
		'wednesday' => __( 'Wednesday', 'thevotex' ),
		'thursday'  => __( 'Thursday', 'thevotex' ),
		'friday'    => __( 'Friday', 'thevotex' ),
		'saturday'  => __( 'Saturday', 'thevotex' ),
		'sunday'    => __( 'Sunday', 'thevotex' ),
	);

	foreach ( $days as $slug => $label ) {
		foreach ( array( 'open', 'close' ) as $type ) {
			$id = "thevotex_hours_{$slug}_{$type}";
			$wp_customize->add_setting( $id, array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			) );
			$wp_customize->add_control( $id, array(
				/* translators: 1: Day name, 2: "Open" or "Close". */
				'label'   => sprintf( __( '%1$s — %2$s', 'thevotex' ), $label, ucfirst( $type ) ),
				'section' => 'thevotex_hours',
				'type'    => 'text',
			) );
		}
	}

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Social Media
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_social', array(
		'title' => __( 'Social Media', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$social_platforms = array(
		'thevotex_social_instagram' => __( 'Instagram URL', 'thevotex' ),
		'thevotex_social_facebook'  => __( 'Facebook URL', 'thevotex' ),
		'thevotex_social_tiktok'    => __( 'TikTok URL', 'thevotex' ),
		'thevotex_social_twitter'   => __( 'Twitter / X URL', 'thevotex' ),
	);

	foreach ( $social_platforms as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'thevotex_social',
			'type'    => 'url',
		) );
	}

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Hero Slider
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_hero', array(
		'title' => __( 'Hero Slider', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$wp_customize->add_setting( 'thevotex_slider_speed', array(
		'default'           => 5500,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'thevotex_slider_speed', array(
		'label'       => __( 'Slide Interval (milliseconds)', 'thevotex' ),
		'description' => __( 'Default: 5500 (5.5 seconds).', 'thevotex' ),
		'section'     => 'thevotex_hero',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 2000, 'max' => 15000, 'step' => 500 ),
	) );

	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( "thevotex_hero_slide_{$i}_image", array(
			'default'           => '',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Media_Control(
			$wp_customize,
			"thevotex_hero_slide_{$i}_image",
			array(
				/* translators: %d: Slide number. */
				'label'     => sprintf( __( 'Slide %d Background Image', 'thevotex' ), $i ),
				'section'   => 'thevotex_hero',
				'mime_type' => 'image',
			)
		) );
	}

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Homepage Settings
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_homepage', array(
		'title' => __( 'Homepage', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$wp_customize->add_setting( 'thevotex_show_dj_tease', array(
		'default'           => false,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'thevotex_show_dj_tease', array(
		'label'   => __( 'Show Resident DJs teaser section', 'thevotex' ),
		'section' => 'thevotex_homepage',
		'type'    => 'checkbox',
	) );

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Chatbot Widget
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_chatbot', array(
		'title' => __( 'Chatbot Widget', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$wp_customize->add_setting( 'thevotex_chatbot_enabled', array(
		'default'           => false,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'thevotex_chatbot_enabled', array(
		'label'   => __( 'Enable chatbot widget', 'thevotex' ),
		'section' => 'thevotex_chatbot',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'thevotex_chatbot_id', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'thevotex_chatbot_id', array(
		'label'   => __( 'Chatbot ID', 'thevotex' ),
		'section' => 'thevotex_chatbot',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'thevotex_chatbot_api', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'thevotex_chatbot_api', array(
		'label'   => __( 'Chatbot API URL', 'thevotex' ),
		'section' => 'thevotex_chatbot',
		'type'    => 'url',
	) );

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Footer
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_footer', array(
		'title' => __( 'Footer', 'thevotex' ),
		'panel' => 'thevotex_options',
	) );

	$wp_customize->add_setting( 'thevotex_footer_tagline', array(
		'default'           => __( 'Premium nightlife at the heart of Calgary. Every Friday & Saturday from 10PM.', 'thevotex' ),
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage', // Live preview without reload.
	) );
	$wp_customize->add_control( 'thevotex_footer_tagline', array(
		'label'   => __( 'Footer Tagline', 'thevotex' ),
		'section' => 'thevotex_footer',
		'type'    => 'text',
	) );
}
add_action( 'customize_register', 'thevotex_customizer_register' );

/**
 * Output inline CSS custom property overrides for Customizer
 * live-preview. This allows the Customizer postMessage transport
 * to update CSS tokens without a full page reload.
 */
function thevotex_customizer_live_preview_css(): void {
	$gold = sanitize_hex_color( get_theme_mod( 'thevotex_gold_override', '' ) );

	if ( empty( $gold ) ) {
		return;
	}

	echo '<style id="thevotex-customizer-overrides">:root{--thevotex-gold:' . esc_attr( $gold ) . ';}</style>' . "\n";
}
add_action( 'wp_head', 'thevotex_customizer_live_preview_css', 99 );
