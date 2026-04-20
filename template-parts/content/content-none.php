<?php
/**
 * THEvotex — template-parts/content/content-none.php
 *
 * Displayed when the current query returns no posts.
 * Covers: empty archives, failed searches, and missing blog posts.
 *
 * Called via get_template_part( 'template-parts/content/content', 'none' ).
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="no-results">

	<header class="no-results__header">
		<h1 class="no-results__title"><?php esc_html_e( 'Nothing Found', 'thevotex' ); ?></h1>
	</header>

	<div class="no-results__content">

		<?php if ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, nothing matched your search terms. Please try again with different keywords.', 'thevotex' ); ?></p>
			<?php get_search_form(); ?>

		<?php elseif ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p>
				<?php
				printf(
					wp_kses(
						/* translators: %s: URL to new post screen */
						__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'thevotex' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
			</p>

		<?php else : ?>

			<p><?php esc_html_e( "It seems we can't find what you're looking for. Perhaps searching can help.", 'thevotex' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>

	</div><!-- .no-results__content -->

</section><!-- .no-results -->
