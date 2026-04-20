<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
if ( is_page_template( 'page-templates/template-reservation.php' ) ) {
	get_template_part( 'template-parts/footer/site-footer', 'strip' );
} else {
	get_template_part( 'template-parts/footer/site-footer' );
}
?>
<?php wp_footer(); ?>
</body>
</html>
