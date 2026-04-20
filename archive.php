<?php
/**
 * THEvotex — archive.php
 *
 * Handles all archive views: date, author, category, tag, and CPT archives
 * (thevotex_event, thevotex_dj, thevotex_food_item, thevotex_package).
 *
 * The posts-grid modifier class is driven by post type so CSS can style
 * event cards differently from standard post cards without JS.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main archive-template" role="main">
	<div class="container">

		<?php if ( have_posts() ) : ?>

			<header class="archive-header page-header">
				<?php
				the_archive_title( '<h1 class="archive-title page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .archive-header -->

			<div class="posts-grid posts-grid--<?php echo esc_attr( sanitize_html_class( get_post_type() ?: 'post' ) ); ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', 'archive' );
				endwhile;
				?>
			</div><!-- .posts-grid -->

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => sprintf(
						'<span class="screen-reader-text">%s</span><span aria-hidden="true">&laquo;</span>',
						esc_html__( 'Previous page', 'thevotex' )
					),
					'next_text' => sprintf(
						'<span class="screen-reader-text">%s</span><span aria-hidden="true">&raquo;</span>',
						esc_html__( 'Next page', 'thevotex' )
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
get_footer();
