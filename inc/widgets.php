<?php
/**
 * Widget Areas and Custom Widgets
 *
 * Covers two responsibilities:
 *
 *  1. Sidebar / widget-area registration — three named areas that
 *     map to the theme's layout regions (footer column, off-canvas,
 *     blog sidebar).
 *
 *  2. Custom WP_Widget classes — three venue-specific widgets that
 *     surface Customizer-backed data in a widgetised, editor-friendly
 *     form:
 *
 *       THEvotex_Contact_Widget  — address, phone, email, optional map link
 *       THEvotex_Hours_Widget    — per-day opening hours table
 *       THEvotex_Social_Widget   — social platform links with SVG icons
 *
 * Design decisions:
 *  - All three widgets fall back to Customizer-stored values when their
 *    own fields are left blank, so editors can deploy them site-wide
 *    without re-entering data.
 *  - widget() output is fully escaped — no raw HTML stored in widget meta.
 *  - update() sanitises every field individually rather than running a
 *    blanket sanitize_text_field() over the whole $new_instance array.
 *  - Widget IDs follow the 'thevotex_{name}' convention to avoid
 *    collisions with third-party plugins.
 *
 * @package THEvotex
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Widget area (sidebar) registration
// ---------------------------------------------------------------------------

/**
 * Registers all widget areas.
 *
 * Hooked to 'widgets_init' which fires after 'after_setup_theme',
 * so WooCommerce and other plugins that also fire on 'widgets_init'
 * will see the areas at the correct time.
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_register_widget_areas(): void {

	/*
	 * Shared wrapper markup used by all sidebars.
	 * The %1$s placeholder receives the widget ID attribute.
	 * The %2$s placeholder receives the widget class list.
	 */
	$shared = array(
		'before_widget' => '<div id="%1$s" class="thevotex-widget %2$s">',
		'after_widget'  => '</div><!-- .thevotex-widget -->',
		'before_title'  => '<h4 class="thevotex-widget__title">',
		'after_title'   => '</h4>',
	);

	// ── Footer column ─────────────────────────────────────────────
	// Rendered inside the footer's 4-column grid as the fifth column
	// when a widget is placed. Intended for newsletter sign-ups or
	// custom text blocks.
	register_sidebar( array_merge( $shared, array(
		'id'          => 'footer-col',
		'name'        => __( 'Footer Column', 'thevotex' ),
		'description' => __( 'Widgets appear in the footer grid. Accepts one widget.', 'thevotex' ),
	) ) );

	// ── Off-canvas / popup ────────────────────────────────────────
	// Injected via get_sidebar('offcanvas') inside footer.php,
	// immediately before </body>. Intended for GDPR banners, cookie
	// notices, or newsletter popup plugins.
	register_sidebar( array_merge( $shared, array(
		'id'          => 'offcanvas',
		'name'        => __( 'Off-Canvas / Popup', 'thevotex' ),
		'description' => __( 'Rendered before </body>. Use for GDPR / newsletter overlays.', 'thevotex' ),
	) ) );

	// ── Blog sidebar ─────────────────────────────────────────────
	// Rendered via get_sidebar() on blog archive and single-post
	// templates. Not used on any custom CPT templates.
	register_sidebar( array_merge( $shared, array(
		'id'          => 'blog-sidebar',
		'name'        => __( 'Blog Sidebar', 'thevotex' ),
		'description' => __( 'Appears on blog archive and single post pages.', 'thevotex' ),
	) ) );
}
add_action( 'widgets_init', 'thevotex_register_widget_areas' );

// ---------------------------------------------------------------------------
// Widget registration
// ---------------------------------------------------------------------------

/**
 * Instantiates and registers all custom theme widgets.
 *
 * Hooked to 'widgets_init' at priority 20 so the sidebar areas
 * registered at the default priority (10) exist first.
 *
 * @since  1.0.0
 * @return void
 */
function thevotex_register_widgets(): void {
	register_widget( 'THEvotex_Contact_Widget' );
	register_widget( 'THEvotex_Hours_Widget' );
	register_widget( 'THEvotex_Social_Widget' );
}
add_action( 'widgets_init', 'thevotex_register_widgets', 20 );

// ---------------------------------------------------------------------------
// Widget 1 — Contact Info
// ---------------------------------------------------------------------------

/**
 * Displays venue contact details (address, phone, email).
 *
 * Fields fall back to values stored in the Customizer when left
 * blank, making it safe to drop into any sidebar without re-entering
 * data that is already managed centrally.
 *
 * @since 1.0.0
 */
class THEvotex_Contact_Widget extends WP_Widget {

	/**
	 * Registers the widget with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'thevotex_contact',
			__( 'THEvotex — Contact Info', 'thevotex' ),
			array(
				'description'                 => __( 'Displays venue address, phone, and email. Falls back to Customizer values.', 'thevotex' ),
				'customize_selective_refresh' => true,
				'classname'                   => 'thevotex-widget--contact',
			)
		);
	}

	/**
	 * Renders the widget on the front end.
	 *
	 * @since  1.0.0
	 * @param  array $args     Sidebar wrapper markup from register_sidebar().
	 * @param  array $instance Saved widget field values.
	 * @return void
	 */
	public function widget( $args, $instance ): void {
		// Resolve each field: widget value → Customizer fallback → hard default.
		$title     = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );
		$address_1 = ! empty( $instance['address_1'] )
			? $instance['address_1']
			: sanitize_text_field( get_theme_mod( 'thevotex_contact_address', '219 17 Avenue SW' ) );
		$address_2 = ! empty( $instance['address_2'] )
			? $instance['address_2']
			: sanitize_text_field( get_theme_mod( 'thevotex_contact_city', 'Calgary, AB' ) );
		$phone     = ! empty( $instance['phone'] )
			? $instance['phone']
			: sanitize_text_field( get_theme_mod( 'thevotex_contact_phone', '+1 (587) 439-1168' ) );
		$email     = ! empty( $instance['email'] )
			? $instance['email']
			: sanitize_email( get_theme_mod( 'thevotex_contact_email', 'info@thevotex.com' ) );
		$show_map  = ! empty( $instance['show_map'] );
		$map_url   = esc_url_raw( $instance['map_url'] ?? '' );

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		echo '<address class="thevotex-contact-widget" translate="no">';

		if ( ! empty( $address_1 ) ) {
			echo '<p class="thevotex-contact-widget__address">';
			echo esc_html( $address_1 );
			if ( ! empty( $address_2 ) ) {
				echo '<br>' . esc_html( $address_2 );
			}
			echo '</p>';
		}

		if ( ! empty( $phone ) ) {
			$phone_clean = preg_replace( '/[^+\d]/', '', $phone );
			printf(
				'<p class="thevotex-contact-widget__phone"><a href="tel:%s">%s</a></p>',
				esc_attr( $phone_clean ),
				esc_html( $phone )
			);
		}

		if ( ! empty( $email ) ) {
			printf(
				'<p class="thevotex-contact-widget__email"><a href="mailto:%s">%s</a></p>',
				esc_attr( $email ),
				esc_html( $email )
			);
		}

		if ( $show_map && ! empty( $map_url ) ) {
			printf(
				'<p class="thevotex-contact-widget__map"><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
				esc_url( $map_url ),
				esc_html__( 'View on map →', 'thevotex' )
			);
		}

		echo '</address>';
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Renders the widget settings form in the Widgets admin screen.
	 *
	 * @since  1.0.0
	 * @param  array $instance Saved widget field values.
	 * @return void
	 */
	public function form( $instance ): void {
		$fields = array(
			'title'     => __( 'Title', 'thevotex' ),
			'address_1' => __( 'Address Line 1', 'thevotex' ),
			'address_2' => __( 'Address Line 2 / City', 'thevotex' ),
			'phone'     => __( 'Phone Number', 'thevotex' ),
			'email'     => __( 'Email Address', 'thevotex' ),
			'map_url'   => __( 'Map Link URL', 'thevotex' ),
		);

		foreach ( $fields as $key => $label ) {
			$value = esc_attr( $instance[ $key ] ?? '' );
			printf(
				'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>',
				esc_attr( $this->get_field_id( $key ) ),
				esc_html( $label ),
				esc_attr( $this->get_field_name( $key ) ),
				$value
			);
		}

		// Checkbox for map link visibility.
		$show_map = ! empty( $instance['show_map'] );
		printf(
			'<p><input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s> <label for="%1$s">%4$s</label></p>',
			esc_attr( $this->get_field_id( 'show_map' ) ),
			esc_attr( $this->get_field_name( 'show_map' ) ),
			checked( $show_map, true, false ),
			esc_html__( 'Show map link', 'thevotex' )
		);
	}

	/**
	 * Sanitises and saves the widget settings.
	 *
	 * @since  1.0.0
	 * @param  array $new_instance Values submitted from the form.
	 * @param  array $old_instance Previously saved values.
	 * @return array               Sanitised values to store.
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance              = array();
		$instance['title']     = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['address_1'] = sanitize_text_field( $new_instance['address_1'] ?? '' );
		$instance['address_2'] = sanitize_text_field( $new_instance['address_2'] ?? '' );
		$instance['phone']     = sanitize_text_field( $new_instance['phone'] ?? '' );
		$instance['email']     = sanitize_email( $new_instance['email'] ?? '' );
		$instance['map_url']   = esc_url_raw( $new_instance['map_url'] ?? '' );
		$instance['show_map']  = ! empty( $new_instance['show_map'] ) ? 1 : 0;

		return $instance;
	}
}

// ---------------------------------------------------------------------------
// Widget 2 — Operating Hours
// ---------------------------------------------------------------------------

/**
 * Displays the venue's operating hours as a day-by-day table.
 *
 * Each day has a label (editable), an hours string (e.g. "7PM – 2AM"),
 * and a closed toggle. Days marked as closed display a localised
 * "Closed" string instead of hours. Falls back to Customizer values.
 *
 * @since 1.0.0
 */
class THEvotex_Hours_Widget extends WP_Widget {

	/**
	 * Ordered day configuration. Keys are field name suffixes.
	 *
	 * @var array<string, string>
	 */
	private const DAYS = array(
		'monday'    => 'Monday',
		'tuesday'   => 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday'  => 'Thursday',
		'friday'    => 'Friday',
		'saturday'  => 'Saturday',
		'sunday'    => 'Sunday',
	);

	/**
	 * Registers the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'thevotex_hours',
			__( 'THEvotex — Operating Hours', 'thevotex' ),
			array(
				'description'                 => __( 'Displays opening hours for each day of the week.', 'thevotex' ),
				'customize_selective_refresh' => true,
				'classname'                   => 'thevotex-widget--hours',
			)
		);
	}

	/**
	 * Renders the widget on the front end.
	 *
	 * @since  1.0.0
	 * @param  array $args     Sidebar wrapper markup.
	 * @param  array $instance Saved widget field values.
	 * @return void
	 */
	public function widget( $args, $instance ): void {
		$title = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		echo '<dl class="thevotex-hours-widget">';

		foreach ( self::DAYS as $slug => $default_label ) {
			// Field keys: {slug}_label, {slug}_hours, {slug}_closed.
			$label  = ! empty( $instance[ $slug . '_label' ] )
				? $instance[ $slug . '_label' ]
				: __( $default_label, 'thevotex' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			$hours  = ! empty( $instance[ $slug . '_hours' ] )
				? $instance[ $slug . '_hours' ]
				: sanitize_text_field( get_theme_mod( 'thevotex_hours_' . $slug . '_open', '' ) );
			$closed = ! empty( $instance[ $slug . '_closed' ] );

			// Skip days where both hours and closed are unset (not configured).
			if ( ! $closed && empty( $hours ) ) {
				continue;
			}

			$hours_display = $closed
				? '<span class="thevotex-hours-widget__closed">' . esc_html__( 'Closed', 'thevotex' ) . '</span>'
				: '<span class="thevotex-hours-widget__time">' . esc_html( $hours ) . '</span>';

			printf(
				'<div class="thevotex-hours-widget__row%s"><dt class="thevotex-hours-widget__day">%s</dt><dd class="thevotex-hours-widget__hours">%s</dd></div>',
				$closed ? ' is-closed' : '',
				esc_html( $label ),
				$hours_display // Already escaped above.
			);
		}

		echo '</dl>';
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Renders the admin form.
	 *
	 * @since  1.0.0
	 * @param  array $instance Saved values.
	 * @return void
	 */
	public function form( $instance ): void {
		// Title.
		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s"></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html__( 'Title', 'thevotex' ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $instance['title'] ?? '' )
		);

		echo '<hr>';

		foreach ( self::DAYS as $slug => $default_label ) {
			$label  = esc_attr( $instance[ $slug . '_label' ]  ?? '' );
			$hours  = esc_attr( $instance[ $slug . '_hours' ]  ?? '' );
			$closed = ! empty( $instance[ $slug . '_closed' ] );

			echo '<fieldset style="border:1px solid #ddd;padding:8px;margin:6px 0;">';
			echo '<legend style="font-weight:600;padding:0 4px;">' . esc_html( $default_label ) . '</legend>';

			// Day label (editable).
			printf(
				'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" placeholder="%5$s"></p>',
				esc_attr( $this->get_field_id( $slug . '_label' ) ),
				esc_html__( 'Day label', 'thevotex' ),
				esc_attr( $this->get_field_name( $slug . '_label' ) ),
				$label,
				esc_attr( $default_label )
			);

			// Hours string.
			printf(
				'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" placeholder="%5$s"></p>',
				esc_attr( $this->get_field_id( $slug . '_hours' ) ),
				esc_html__( 'Hours (e.g. 7PM – 2AM)', 'thevotex' ),
				esc_attr( $this->get_field_name( $slug . '_hours' ) ),
				$hours,
				esc_attr__( 'e.g. 7PM – 2AM', 'thevotex' )
			);

			// Closed toggle.
			printf(
				'<p><input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s> <label for="%1$s">%4$s</label></p>',
				esc_attr( $this->get_field_id( $slug . '_closed' ) ),
				esc_attr( $this->get_field_name( $slug . '_closed' ) ),
				checked( $closed, true, false ),
				esc_html__( 'Closed', 'thevotex' )
			);

			echo '</fieldset>';
		}
	}

	/**
	 * Sanitises and saves the widget settings.
	 *
	 * @since  1.0.0
	 * @param  array $new_instance Values submitted from the form.
	 * @param  array $old_instance Previously saved values.
	 * @return array               Sanitised values to store.
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] ?? '' );

		foreach ( array_keys( self::DAYS ) as $slug ) {
			$instance[ $slug . '_label' ]  = sanitize_text_field( $new_instance[ $slug . '_label' ]  ?? '' );
			$instance[ $slug . '_hours' ]  = sanitize_text_field( $new_instance[ $slug . '_hours' ]  ?? '' );
			$instance[ $slug . '_closed' ] = ! empty( $new_instance[ $slug . '_closed' ] ) ? 1 : 0;
		}

		return $instance;
	}
}

// ---------------------------------------------------------------------------
// Widget 3 — Social Links
// ---------------------------------------------------------------------------

/**
 * Displays social media platform links with inline SVG icons.
 *
 * Only platforms with a URL entered (widget field or Customizer) are
 * rendered. Editors can choose whether to show text labels alongside
 * icons via a checkbox. Falls back to Customizer social URL settings.
 *
 * @since 1.0.0
 */
class THEvotex_Social_Widget extends WP_Widget {

	/**
	 * Supported platforms and their Customizer key mappings.
	 *
	 * @var array<string, array{label: string, mod: string}>
	 */
	private const PLATFORMS = array(
		'instagram' => array(
			'label' => 'Instagram',
			'mod'   => 'thevotex_social_instagram',
		),
		'facebook'  => array(
			'label' => 'Facebook',
			'mod'   => 'thevotex_social_facebook',
		),
		'tiktok'    => array(
			'label' => 'TikTok',
			'mod'   => 'thevotex_social_tiktok',
		),
		'twitter'   => array(
			'label' => 'Twitter / X',
			'mod'   => 'thevotex_social_twitter',
		),
	);

	/**
	 * Registers the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'thevotex_social',
			__( 'THEvotex — Social Links', 'thevotex' ),
			array(
				'description'                 => __( 'Displays social media links with SVG icons. Falls back to Customizer URLs.', 'thevotex' ),
				'customize_selective_refresh' => true,
				'classname'                   => 'thevotex-widget--social',
			)
		);
	}

	/**
	 * Renders the widget on the front end.
	 *
	 * @since  1.0.0
	 * @param  array $args     Sidebar wrapper markup.
	 * @param  array $instance Saved widget field values.
	 * @return void
	 */
	public function widget( $args, $instance ): void {
		$title       = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );
		$show_labels = ! empty( $instance['show_labels'] );

		// Build the list of active links.
		$links = array();
		foreach ( self::PLATFORMS as $platform => $config ) {
			$url = ! empty( $instance[ $platform . '_url' ] )
				? esc_url_raw( $instance[ $platform . '_url' ] )
				: esc_url_raw( get_theme_mod( $config['mod'], '' ) );

			if ( ! empty( $url ) ) {
				$links[ $platform ] = array(
					'url'   => $url,
					'label' => $config['label'],
				);
			}
		}

		if ( empty( $links ) ) {
			return;
		}

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}

		echo '<ul class="thevotex-social-widget" role="list" aria-label="' . esc_attr__( 'Social media links', 'thevotex' ) . '">';

		foreach ( $links as $platform => $data ) {
			$label_html = $show_labels
				? '<span class="thevotex-social-widget__label">' . esc_html( $data['label'] ) . '</span>'
				: '<span class="screen-reader-text">' . esc_html( $data['label'] ) . '</span>';

			printf(
				'<li class="thevotex-social-widget__item thevotex-social-widget__item--%1$s">'
				. '<a href="%2$s" class="thevotex-social-widget__link" target="_blank" rel="noopener noreferrer me">'
				. '%3$s%4$s'
				. '</a></li>',
				esc_attr( $platform ),
				esc_url( $data['url'] ),
				self::get_platform_icon( $platform ), // Already-safe SVG markup.
				$label_html // Already escaped above.
			);
		}

		echo '</ul>';
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Renders the admin form.
	 *
	 * @since  1.0.0
	 * @param  array $instance Saved values.
	 * @return void
	 */
	public function form( $instance ): void {
		// Title.
		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="url" value="%4$s"></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html__( 'Title', 'thevotex' ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $instance['title'] ?? '' )
		);

		// One URL field per platform.
		foreach ( self::PLATFORMS as $platform => $config ) {
			printf(
				'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="url" value="%4$s" placeholder="%5$s"></p>',
				esc_attr( $this->get_field_id( $platform . '_url' ) ),
				esc_html( $config['label'] . ' URL' ),
				esc_attr( $this->get_field_name( $platform . '_url' ) ),
				esc_attr( $instance[ $platform . '_url' ] ?? '' ),
				esc_attr__( 'Leave blank to use Customizer value', 'thevotex' )
			);
		}

		// Show labels toggle.
		$show_labels = ! empty( $instance['show_labels'] );
		printf(
			'<p><input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s> <label for="%1$s">%4$s</label></p>',
			esc_attr( $this->get_field_id( 'show_labels' ) ),
			esc_attr( $this->get_field_name( 'show_labels' ) ),
			checked( $show_labels, true, false ),
			esc_html__( 'Show platform name labels', 'thevotex' )
		);
	}

	/**
	 * Sanitises and saves the widget settings.
	 *
	 * @since  1.0.0
	 * @param  array $new_instance Values submitted from the form.
	 * @param  array $old_instance Previously saved values.
	 * @return array               Sanitised values to store.
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance              = array();
		$instance['title']     = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['show_labels'] = ! empty( $new_instance['show_labels'] ) ? 1 : 0;

		foreach ( array_keys( self::PLATFORMS ) as $platform ) {
			$instance[ $platform . '_url' ] = esc_url_raw( $new_instance[ $platform . '_url' ] ?? '' );
		}

		return $instance;
	}

	/**
	 * Returns an inline SVG icon string for the given social platform.
	 *
	 * Output is safe to echo without escaping — all attribute values
	 * are hard-coded string literals, not user input.
	 *
	 * @since  1.0.0
	 * @param  string $platform Platform slug (instagram|facebook|tiktok|twitter).
	 * @return string           SVG markup string, or empty string for unknown platforms.
	 */
	private static function get_platform_icon( string $platform ): string {
		$icons = array(

			'instagram' => '<svg class="thevotex-social-widget__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
				. '<path fill="currentColor" d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5zM12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm4.75-2.25a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5z"/>'
				. '</svg>',

			'facebook' => '<svg class="thevotex-social-widget__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
				. '<path fill="currentColor" d="M22 12a10 10 0 1 0-11.6 9.9v-7H8v-3h2.4V9.5c0-2.4 1.4-3.7 3.6-3.7 1 0 2 .2 2 .2v2.3h-1.2c-1.2 0-1.6.8-1.6 1.5V12H16l-.4 3h-2.4v7A10 10 0 0 0 22 12z"/>'
				. '</svg>',

			'tiktok' => '<svg class="thevotex-social-widget__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
				. '<path fill="currentColor" d="M21 8.5c-1.9 0-3.7-.7-5-2V16a5 5 0 1 1-5-5c.3 0 .7 0 1 .1v2.7c-.3-.1-.7-.2-1-.2a2.4 2.4 0 1 0 2.4 2.4V2h2.6c.2 2 1.8 3.5 4 3.5v3z"/>'
				. '</svg>',

			'twitter' => '<svg class="thevotex-social-widget__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
				. '<path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'
				. '</svg>',
		);

		return $icons[ $platform ] ?? '';
	}
}
