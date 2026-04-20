<?php
/**
 * The front page template.
 *
 * Used when the site front page is set to a static page (or the "Your latest posts"
 * reading setting). Renders full-width sections specific to the THEvotex nightclub theme.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main front-page" role="main">

	<?php
	/*
	 * If the front page is also the posts page (blog as front page), display
	 * the standard post loop so the reading setting is respected.
	 */
	if ( is_front_page() && is_home() ) :
		?>

		<div class="container">

			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Latest Posts', 'thevotex' ); ?></h1>
			</header>

			<?php if ( have_posts() ) : ?>

				<div class="posts-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content/content', get_post_type() );
					endwhile;
					?>
				</div><!-- .posts-grid -->

				<?php
				the_posts_navigation(
					array(
						'prev_text' => sprintf(
							'<span class="nav-subtitle" aria-hidden="true">&larr;</span> <span class="nav-label">%s</span>',
							esc_html__( 'Older posts', 'thevotex' )
						),
						'next_text' => sprintf(
							'<span class="nav-label">%s</span> <span class="nav-subtitle" aria-hidden="true">&rarr;</span>',
							esc_html__( 'Newer posts', 'thevotex' )
						),
					)
				);
				?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content/content', 'none' ); ?>

			<?php endif; ?>

		</div><!-- .container -->

	<?php else : ?>

		<?php get_template_part( 'template-parts/sections/hero' ); ?>

		<?php get_template_part( 'template-parts/sections/events' ); ?>

		<?php get_template_part( 'template-parts/sections/djs' ); ?>

		<?php get_template_part( 'template-parts/sections/about' ); ?>

		<?php get_template_part( 'template-parts/sections/packages' ); ?>

		<?php get_template_part( 'template-parts/sections/food' ); ?>

		<?php get_template_part( 'template-parts/sections/reservation-cta' ); ?>

	<?php endif; ?>

</main><!-- #main-content -->

<?php
get_footer();
