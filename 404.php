<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Amentum
 */

get_header();
?>

<main id="primary" class="page-404" data-barba="container" data-barba-namespace="not-found">

	<section class="error-404 not-found">

		<div class="page-content">
			<div class="container">
				<h1>404</h1>
				<p>Oops! La página que estás buscando no existe.</p>
				<a href="<?php echo get_home_url() ?>">Ir a la página de Inicio</a>
			</div>
			
		</div><!-- .page-content -->
	</section><!-- .error-404 -->

</main><!-- #main -->

<?php
get_footer();
