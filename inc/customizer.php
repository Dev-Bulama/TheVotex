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

	$wp_customize->add_setting( 'thevotex_footer_copyright', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_footer_copyright', array(
		'label'       => __( 'Custom Copyright Text', 'thevotex' ),
		'description' => __( 'Leave blank to auto-generate: © Year Site Name.', 'thevotex' ),
		'section'     => 'thevotex_footer',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'thevotex_footer_bg', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control(
		$wp_customize,
		'thevotex_footer_bg',
		array(
			'label'   => __( 'Footer Background Color', 'thevotex' ),
			'section' => 'thevotex_footer',
		)
	) );

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Colors
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_colors', array(
		'title'    => __( 'Colors', 'thevotex' ),
		'panel'    => 'thevotex_options',
		'priority' => 20,
	) );

	$thevotex_color_settings = array(
		'thevotex_color_primary' => array(
			'label'   => __( 'Primary — Gold', 'thevotex' ),
			'default' => '#c9a84c',
		),
		'thevotex_color_dark'    => array(
			'label'   => __( 'Dark Background', 'thevotex' ),
			'default' => '#020204',
		),
		'thevotex_color_card'    => array(
			'label'   => __( 'Card / Surface Background', 'thevotex' ),
			'default' => '#0b0b14',
		),
		'thevotex_color_text'    => array(
			'label'   => __( 'Body Text', 'thevotex' ),
			'default' => '#f0eee8',
		),
		'thevotex_color_muted'   => array(
			'label'   => __( 'Muted / Secondary Text', 'thevotex' ),
			'default' => '#7a7a8a',
		),
	);

	foreach ( $thevotex_color_settings as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control(
			$wp_customize,
			$id,
			array(
				'label'   => $args['label'],
				'section' => 'thevotex_colors',
			)
		) );
	}

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Typography
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_typography', array(
		'title'    => __( 'Typography', 'thevotex' ),
		'panel'    => 'thevotex_options',
		'priority' => 25,
	) );

	$wp_customize->add_setting( 'thevotex_font_heading', array(
		'default'           => 'Bebas Neue',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_font_heading', array(
		'label'   => __( 'Heading Font', 'thevotex' ),
		'section' => 'thevotex_typography',
		'type'    => 'select',
		'choices' => array(
			'Bebas Neue'         => 'Bebas Neue',
			'Cormorant Garamond' => 'Cormorant Garamond',
			'Playfair Display'   => 'Playfair Display',
			'Montserrat'         => 'Montserrat',
			'DM Sans'            => 'DM Sans',
		),
	) );

	$wp_customize->add_setting( 'thevotex_font_body', array(
		'default'           => 'DM Sans',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_font_body', array(
		'label'   => __( 'Body Font', 'thevotex' ),
		'section' => 'thevotex_typography',
		'type'    => 'select',
		'choices' => array(
			'DM Sans'            => 'DM Sans',
			'Cormorant Garamond' => 'Cormorant Garamond',
			'Inter'              => 'Inter',
			'Lato'               => 'Lato',
			'Open Sans'          => 'Open Sans',
		),
	) );

	$wp_customize->add_setting( 'thevotex_font_size_base', array(
		'default'           => 16,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_font_size_base', array(
		'label'       => __( 'Base Font Size (px)', 'thevotex' ),
		'section'     => 'thevotex_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 14,
			'max'  => 20,
			'step' => 1,
		),
	) );

	/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
	   SECTION: Header Layout
	   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
	$wp_customize->add_section( 'thevotex_header_layout', array(
		'title'    => __( 'Header Layout', 'thevotex' ),
		'panel'    => 'thevotex_options',
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'thevotex_header_sticky', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_header_sticky', array(
		'label'   => __( 'Sticky header (fixed on scroll)', 'thevotex' ),
		'section' => 'thevotex_header_layout',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'thevotex_header_transparent', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_header_transparent', array(
		'label'       => __( 'Transparent header on front page', 'thevotex' ),
		'description' => __( 'Header blends into the hero image on the homepage.', 'thevotex' ),
		'section'     => 'thevotex_header_layout',
		'type'        => 'checkbox',
	) );

	$wp_customize->add_setting( 'thevotex_header_height', array(
		'default'           => 80,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'thevotex_header_height', array(
		'label'       => __( 'Header height (px)', 'thevotex' ),
		'section'     => 'thevotex_header_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 60,
			'max'  => 140,
			'step' => 4,
		),
	) );
}
add_action( 'customize_register', 'thevotex_customizer_register' );

/**
 * Enqueues the live-preview JS inside the Customizer preview iframe.
 *
 * The script uses wp.customize() bindings so postMessage transport
 * updates CSS tokens and DOM text instantly without a page reload.
 */
function thevotex_customizer_preview_enqueue(): void {
	wp_enqueue_script(
		'thevotex-customizer-preview',
		THEVOTEX_ASSETS . '/js/customizer-preview.js',
		array( 'customize-preview' ),
		THEVOTEX_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'thevotex_customizer_preview_enqueue' );
