<?php
/**
 * THEvotex — woocommerce.php
 *
 * WooCommerce checks for this file before falling back to page.php.
 * Its presence tells WooCommerce that the theme owns the page shell,
 * so WooCommerce only renders the inner shop/product content.
 *
 * @package THEvotex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main woocommerce-page" role="main">
	<div class="container">
		<?php woocommerce_content(); ?>
	</div><!-- .container -->
</main><!-- #main-content -->

<?php
get_footer();
