<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php $thevotex_logo_id = get_theme_mod( 'custom_logo' ); ?>
<footer class="footer-strip site-footer" role="contentinfo">

	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-strip-logo" rel="home">
		<?php if ( $thevotex_logo_id ) : ?>
			<?php echo wp_get_attachment_image( $thevotex_logo_id, 'full', false, array( 'class' => 'footer-logo-img', 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<span class="footer-logo__text"><?php bloginfo( 'name' ); ?></span>
		<?php endif; ?>
	</a>

	<?php thevotex_footer_nav_menu( array( 'menu_class' => 'footer-strip-links', 'depth' => 1 ) ); ?>

	<span class="footer-strip-copy">
		&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'thevotex' ); ?>
	</span>

</footer>
