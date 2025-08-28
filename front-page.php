<?php
/**
 * Página de Inicio - Front Page Template
 * 
 * Template para la portada usando bloques nativos de WordPress
 * 
 * @package Amentum
 */

get_header(); ?>

<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__('Páginas:', 'amentum'),
						'after'  => '</div>',
					)
				);
				?>
			</div><!-- .entry-content -->
		</article><!-- #post-<?php the_ID(); ?> -->
		<?php
	endwhile; // End of the loop.
	?>
</main><!-- #main -->

<?php get_footer(); ?>