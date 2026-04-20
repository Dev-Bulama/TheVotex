<?php
/**
 * Template Name: Full Width
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main-content" class="site-main template-fullwidth" role="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/content/content', 'page' ); ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
