<?php
/**
 * The main template file.
 *
 * Fallback template used when no other template matches the current query.
 * WordPress requires this file to be present in every theme.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main" role="main">
	<div class="container">

		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Latest', 'thevotex' ); ?></h1>
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
</main><!-- #main-content -->

<?php
get_sidebar();
get_footer();
