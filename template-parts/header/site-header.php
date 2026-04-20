<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
$thevotex_logo_id = get_theme_mod( 'custom_logo' );
?>
<header id="site-header" class="site-header" role="banner">

	<nav id="mainNav" class="main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'thevotex' ); ?>">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo" rel="home">
			<?php if ( $thevotex_logo_id ) : ?>
				<?php echo wp_get_attachment_image( $thevotex_logo_id, 'full', false, array( 'class' => 'nav-logo__img', 'loading' => 'eager' ) ); ?>
			<?php else : ?>
				<span class="nav-logo__text"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>

		<div class="main-nav__desktop">
			<?php thevotex_nav_menu( 'primary', array( 'menu_class' => 'nav-links', 'menu_id' => 'primary-menu' ) ); ?>
		</div>

		<button
			class="hamburger"
			id="hamburger"
			aria-label="<?php esc_attr_e( 'Open navigation menu', 'thevotex' ); ?>"
			aria-controls="mobileMenu"
			aria-expanded="false"
		>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</button>

	</nav>

	<div
		class="mobile-menu"
		id="mobileMenu"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Mobile navigation', 'thevotex' ); ?>"
		hidden
	>
		<button class="mobile-close" id="mobileClose" aria-label="<?php esc_attr_e( 'Close navigation menu', 'thevotex' ); ?>">&#10005;</button>

		<div class="mobile-menu-logo" aria-hidden="true">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" tabindex="-1">
				<?php if ( $thevotex_logo_id ) : ?>
					<?php echo wp_get_attachment_image( $thevotex_logo_id, 'full', false, array( 'class' => 'nav-logo__img', 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<span class="nav-logo__text"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>
		</div>

		<?php thevotex_mobile_nav_menu( array( 'menu_id' => 'mobile-menu-list' ) ); ?>

	</div>

</header>
