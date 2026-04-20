/* global wp */
/**
 * THEvotex — Customizer live-preview bindings.
 *
 * Every setting with transport:'postMessage' needs a binding here so
 * the Customizer preview iframe updates without a full page reload.
 * The pattern: wp.customize( settingId, fn ) → value.bind( fn ).
 *
 * CSS token overrides use document.documentElement.style.setProperty()
 * so the change propagates to every rule that references the token.
 */
( function () {
	'use strict';

	var root = document.documentElement;

	/* ── Color tokens → CSS custom properties ───────────────────── */

	var colorMap = {
		thevotex_color_primary : '--thevotex-gold',
		thevotex_color_dark    : '--thevotex-black',
		thevotex_color_card    : '--thevotex-card',
		thevotex_color_text    : '--thevotex-white',
		thevotex_color_muted   : '--thevotex-muted',
		thevotex_footer_bg     : '--thevotex-footer-bg',
	};

	Object.keys( colorMap ).forEach( function ( setting ) {
		wp.customize( setting, function ( value ) {
			value.bind( function ( newval ) {
				if ( newval ) {
					root.style.setProperty( colorMap[ setting ], newval );
				} else {
					root.style.removeProperty( colorMap[ setting ] );
				}
			} );
		} );
	} );

	/* ── Header height ──────────────────────────────────────────── */

	wp.customize( 'thevotex_header_height', function ( value ) {
		value.bind( function ( newval ) {
			root.style.setProperty( '--thevotex-header-height', newval + 'px' );
		} );
	} );

	/* ── Header sticky ──────────────────────────────────────────── */

	wp.customize( 'thevotex_header_sticky', function ( value ) {
		value.bind( function ( newval ) {
			var header = document.getElementById( 'site-header' );
			if ( header ) {
				header.classList.toggle( 'is-sticky', !! newval );
			}
		} );
	} );

	/* ── Header transparent ─────────────────────────────────────── */

	wp.customize( 'thevotex_header_transparent', function ( value ) {
		value.bind( function ( newval ) {
			var header = document.getElementById( 'site-header' );
			if ( header ) {
				header.classList.toggle( 'header--transparent', !! newval );
			}
		} );
	} );

	/* ── Typography: heading font ───────────────────────────────── */

	wp.customize( 'thevotex_font_heading', function ( value ) {
		value.bind( function ( newval ) {
			root.style.setProperty(
				'--thevotex-font-heading',
				"'" + newval + "', serif"
			);
		} );
	} );

	/* ── Typography: body font ──────────────────────────────────── */

	wp.customize( 'thevotex_font_body', function ( value ) {
		value.bind( function ( newval ) {
			root.style.setProperty(
				'--thevotex-font-body',
				"'" + newval + "', sans-serif"
			);
		} );
	} );

	/* ── Typography: base font size ─────────────────────────────── */

	wp.customize( 'thevotex_font_size_base', function ( value ) {
		value.bind( function ( newval ) {
			root.style.setProperty( '--thevotex-font-size-base', newval + 'px' );
		} );
	} );

	/* ── Footer tagline ─────────────────────────────────────────── */

	wp.customize( 'thevotex_footer_tagline', function ( value ) {
		value.bind( function ( newval ) {
			document.querySelectorAll( '.footer-tagline' ).forEach( function ( el ) {
				el.textContent = newval;
			} );
		} );
	} );

	/* ── Footer copyright ───────────────────────────────────────── */

	wp.customize( 'thevotex_footer_copyright', function ( value ) {
		value.bind( function ( newval ) {
			document.querySelectorAll( '.footer-copyright' ).forEach( function ( el ) {
				el.textContent = newval;
			} );
		} );
	} );

} )();
