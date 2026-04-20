<?php
/**
 * THEvotex — template-parts/content/content-single.php
 *
 * Renders a full single post: header, meta, featured image, body, footer.
 * Called via get_template_part( 'template-parts/content/content', 'single' ).
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thevotex_cats = get_the_category_list( ', ' );
$thevotex_tags = get_the_tag_list(
	'',
	/* translators: separating tags */
	_x( ', ', 'list item separator', 'thevotex' )
);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>

	<header class="entry__header">

		<?php the_title( '<h1 class="entry__title">', '</h1>' ); ?>

		<div class="entry__meta" aria-label="<?php esc_attr_e( 'Post details', 'thevotex' ); ?>">

			<time class="entry__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>

			<?php if ( get_the_author() ) : ?>
				<span class="entry__author">
					<?php
					printf(
						/* translators: %s: author display name */
						esc_html__( 'By %s', 'thevotex' ),
						'<span>' . esc_html( get_the_author() ) . '</span>'
					);
					?>
				</span>
			<?php endif; ?>

			<?php if ( $thevotex_cats ) : ?>
				<span class="entry__categories"><?php echo wp_kses_post( $thevotex_cats ); ?></span>
			<?php endif; ?>

		</div><!-- .entry__meta -->

	</header><!-- .entry__header -->

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="entry__thumbnail">
			<?php
			the_post_thumbnail(
				'large',
				array(
					'class'   => 'entry__thumbnail-img',
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		</div><!-- .entry__thumbnail -->
	<?php endif; ?>

	<div class="entry__content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'thevotex' ),
					array( 'span' => array( 'class' => array() ) )
				),
				get_the_title()
			)
		);

		wp_link_pages(
			array(
				'before'      => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'thevotex' ) . '">',
				'after'       => '</nav>',
				'link_before' => '<span class="page-links__item">',
				'link_after'  => '</span>',
				'pagelink'    => '%',
			)
		);
		?>
	</div><!-- .entry__content -->

	<?php if ( $thevotex_tags ) : ?>
		<footer class="entry__footer">
			<div class="entry__tags">
				<span class="entry__tags-label"><?php esc_html_e( 'Tags:', 'thevotex' ); ?></span>
				<?php echo wp_kses_post( $thevotex_tags ); ?>
			</div>
		</footer><!-- .entry__footer -->
	<?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->
