 <?php
/**
 * Pagina post type projects
 *
 * @package Amentum
 */

get_header();
?>
 <main id="primary" class="projects">

     <?php
            get_template_part('template-parts/merch', 'list');

?>

 </main><!-- #main -->

 <?php
// get_sidebar();
get_footer(); // Incluir el footer (pie de página)

?>
