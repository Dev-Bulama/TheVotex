<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
$thevotex_logo_id    = get_theme_mod( 'custom_logo' );
$thevotex_socials    = thevotex_social_links();

$thevotex_svg_icons = array(
	'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><defs><linearGradient id="ig-grad" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" stop-color="#c9a84c"/><stop offset="100%" stop-color="#e8c97a"/></linearGradient></defs><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="url(#ig-grad)" stroke-width="2"/><circle cx="12" cy="12" r="5" fill="none" stroke="url(#ig-grad)" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="url(#ig-grad)"/></svg>',
	'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><defs><linearGradient id="fb-grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#c9a84c"/><stop offset="100%" stop-color="#e8c97a"/></linearGradient></defs><path fill="url(#fb-grad)" d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.99 3.66 9.12 8.44 9.88V14.89H7.9V12h2.54V9.8c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99C18.34 21.12 22 16.99 22 12z"/></svg>',
	'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><defs><linearGradient id="tt-grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#c9a84c"/><stop offset="100%" stop-color="#e8c97a"/></linearGradient></defs><path fill="url(#tt-grad)" d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.79 1.54V6.78a4.85 4.85 0 01-1.02-.09z"/></svg>',
	'twitter'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><defs><linearGradient id="tw-grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#c9a84c"/><stop offset="100%" stop-color="#e8c97a"/></linearGradient></defs><path fill="url(#tw-grad)" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
);
?>
<footer id="site-footer" class="site-footer" role="contentinfo">

	<div class="footer-top">

		<div class="footer-brand">

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-brand__logo-link" rel="home">
				<?php if ( $thevotex_logo_id ) : ?>
					<?php echo wp_get_attachment_image( $thevotex_logo_id, 'full', false, array( 'class' => 'footer-logo-img', 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<span class="footer-logo__text"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>

			<p class="footer-tagline"><?php echo esc_html( thevotex_option( 'footer_tagline', get_bloginfo( 'description' ) ) ); ?></p>

			<?php if ( ! empty( $thevotex_socials ) ) : ?>
				<div class="footer-socials">
					<?php foreach ( $thevotex_socials as $thevotex_platform => $thevotex_url ) : ?>
						<?php
						$thevotex_platform_label = ucfirst( $thevotex_platform );
						$thevotex_icon           = isset( $thevotex_svg_icons[ $thevotex_platform ] ) ? $thevotex_svg_icons[ $thevotex_platform ] : '';
						?>
						<a
							href="<?php echo esc_url( $thevotex_url ); ?>"
							class="social-btn"
							target="_blank"
							rel="noopener noreferrer me"
							aria-label="<?php echo esc_attr( sprintf( __( 'Follow us on %s', 'thevotex' ), $thevotex_platform_label ) ); ?>"
						>
							<?php if ( $thevotex_icon ) : ?>
								<?php echo $thevotex_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG defined above ?>
							<?php else : ?>
								<?php echo esc_html( $thevotex_platform_label ); ?>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>

		<div class="footer-cols">
			<?php if ( is_active_sidebar( 'thevotex_footer_1' ) ) : ?>
				<div class="footer-col">
					<?php dynamic_sidebar( 'thevotex_footer_1' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'thevotex_footer_2' ) ) : ?>
				<div class="footer-col">
					<?php dynamic_sidebar( 'thevotex_footer_2' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'thevotex_footer_3' ) ) : ?>
				<div class="footer-col">
					<?php dynamic_sidebar( 'thevotex_footer_3' ); ?>
				</div>
			<?php endif; ?>
		</div>

	</div>

	<div class="footer-bottom">
		<span class="footer-copyright">
			&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'thevotex' ); ?>
		</span>
		<?php thevotex_footer_nav_menu( array( 'menu_class' => 'footer-legal-links' ) ); ?>
	</div>

</footer>
