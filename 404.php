<?php
/**
 * THEvotex — 404.php
 *
 * "Page not found" error template.
 * Displayed whenever WordPress cannot match the URL to any content.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main error-404" role="main">
	<div class="container">

		<section class="not-found" aria-labelledby="error-heading">

			<header class="page-header not-found__header">
				<span class="not-found__code" aria-hidden="true">404</span>
				<h1 class="page-title not-found__title" id="error-heading">
					<?php esc_html_e( 'Page Not Found', 'thevotex' ); ?>
				</h1>
				<p class="not-found__message">
					<?php esc_html_e( "The page you're looking for has left the building.", 'thevotex' ); ?>
				</p>
			</header><!-- .page-header -->

			<div class="not-found__actions">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--gold">
					<?php esc_html_e( 'Return Home', 'thevotex' ); ?>
				</a>
			</div>

			<div class="not-found__search">
				<p class="not-found__search-label">
					<?php esc_html_e( 'Or search for what you need:', 'thevotex' ); ?>
				</p>
				<?php get_search_form(); ?>
			</div>

		</section><!-- .not-found -->

	</div><!-- .container -->
</main><!-- .error-404 -->

<?php
get_footer();
