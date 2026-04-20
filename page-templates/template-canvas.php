<?php
/**
 * Template Name: Canvas (Elementor)
 * Template Post Type: page
 *
 * Blank canvas — Elementor renders the entire page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'elementor-template-canvas' ); ?>>
<?php wp_body_open(); ?>
<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content(); ?>
<?php endwhile; ?>
<?php wp_footer(); ?>
</body>
</html>
