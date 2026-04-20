<?php
/**
 * THEvotex — template-parts/content/content-page.php
 *
 * Renders the content of a single WordPress page.
 * Called via get_template_part( 'template-parts/content/content', 'page' ).
 *
 * The featured image is suppressed on the Full Width and Canvas templates
 * because those layouts give editors full control via Elementor.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thevotex_show_thumbnail = has_post_thumbnail()
	&& ! is_page_template( 'page-templates/template-fullwidth.php' )
	&& ! is_page_template( 'page-templates/template-canvas.php' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>

	<?php if ( $thevotex_show_thumbnail ) : ?>
		<div class="entry__thumbnail">
			<?php
			the_post_thumbnail(
				'full',
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
		the_content();

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

</article><!-- #post-<?php the_ID(); ?> -->
