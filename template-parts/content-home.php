<?php
/**
 * Template part para mostrar contenido en home.php (nativo)
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Amentum
 */

?>

<section class="hero-home">
	<div class="hero-home__content">
		<h1 class="hero-home__title"><?php _e('Hola, somos Amentum', 'amentum'); ?></h1>
		<p class="hero-home__subtitle"><?php _e('Agencia creativa especializada en diseño, branding y desarrollo web', 'amentum'); ?></p>
	</div>
</section>

<section class="projects-home">
	<div class="container">
		<h2><?php _e('Proyectos Destacados', 'amentum'); ?></h2>
		
		<?php
		// Query para obtener proyectos destacados
		$projects_query = new WP_Query(array(
			'post_type' => 'projects',
			'posts_per_page' => 6,
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC'
		));
		
		if ($projects_query->have_posts()) : ?>
			<div class="projects-grid">
				<?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
					<article class="project-card">
						<div class="project-card__image">
							<?php if (has_post_thumbnail()) : ?>
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail('card_image', ['alt' => get_the_title()]); ?>
								</a>
							<?php endif; ?>
						</div>
						<div class="project-card__content">
							<h3 class="project-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<?php
							// Mostrar taxonomías si existen
							$terms = get_the_terms(get_the_ID(), 'tipo');
							if ($terms && !is_wp_error($terms)) :
							?>
								<div class="project-card__tags">
									<?php foreach ($terms as $term) : ?>
										<span class="tag"><?php echo esc_html($term->name); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			
			<div class="projects-home__more">
				<a href="<?php echo get_post_type_archive_link('projects'); ?>" class="btn btn-primary">
					<?php _e('Ver Todos los Proyectos', 'amentum'); ?>
				</a>
			</div>
		<?php endif; 
		wp_reset_postdata(); ?>
	</div>
</section>