<?php
/**
 * Archivo de plantilla principal
 *
 * Este es el archivo de plantilla genérico en los temas de WordPress.
 * Es uno de los dos archivos requeridos para un tema (el otro es style.css).
 * Se utiliza para mostrar una página cuando no coincide con una consulta específica.
 * Por ejemplo, se usa para construir la página principal "home" cuando no existe el archivo home.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Amentum
 */

// Incluir el header de la página (navegación, barra lateral, etc.)
get_header();
?>

	<main id="primary" class="site-main" data-barba="container" data-barba-namespace="index">

		<?php
		if ( have_posts() ) : // Si hay publicaciones disponibles

			if ( is_home() && ! is_front_page() ) : // Si estamos en la página principal y no es la página inicial
				?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1> <!-- El título de la publicación -->
				</header>
				<?php
			endif;

			/* Empieza el bucle */
			while ( have_posts() ) :
				the_post();

				/*
				 * Incluye la plantilla específica del tipo de publicación para su contenido.
				 * Si desea anular esto con un tema hijo, incluya un archivo llamado content-___.php (donde ___ es el nombre del tipo de publicación) y este se utilizará en su lugar.
				 */
				get_template_part( 'template-parts/content', get_post_type() );

			endwhile;

			the_posts_navigation(); // Mostrar la navegación de las publicaciones

		else : // Si no hay publicaciones disponibles

			get_template_part( 'template-parts/content', 'none' ); // Incluye una plantilla para cuando no hay resultados

		endif;
		?>

	</main><!-- #main -->

<?php
// get_sidebar();
get_footer(); // Incluir el footer (pie de página)
