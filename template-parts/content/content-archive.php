<?php
/**
 * THEvotex — template-parts/content/content-archive.php
 *
 * Card layout used in all archive loops: blog index, date/author/tax archives,
 * and CPT archives (events, DJs, food items, packages).
 *
 * Called via get_template_part( 'template-parts/content/content', 'archive' ).
 *
 * The image link is aria-hidden so screen readers reach the title link first
 * and don't encounter two consecutive identical links to the same target.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<a
			href="<?php the_permalink(); ?>"
			class="post-card__image-link"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php
			the_post_thumbnail(
				'thevotex-card',
				array(
					'class'   => 'post-card__image',
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		</a>
	<?php endif; ?>

	<div class="post-card__body">

		<header class="post-card__header">

			<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>

			<?php
			the_title(
				sprintf(
					'<h2 class="post-card__title"><a href="%s" rel="bookmark">',
					esc_url( get_permalink() )
				),
				'</a></h2>'
			);
			?>

		</header><!-- .post-card__header -->

		<div class="post-card__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<a href="<?php the_permalink(); ?>" class="btn btn--outline post-card__cta">
			<?php esc_html_e( 'Read More', 'thevotex' ); ?>
			<span class="screen-reader-text">
				<?php
				printf(
					/* translators: %s: post title */
					esc_html__( 'about %s', 'thevotex' ),
					get_the_title()
				);
				?>
			</span>
		</a>

	</div><!-- .post-card__body -->

</article><!-- #post-<?php the_ID(); ?> -->
