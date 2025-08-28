<?php
/**
 * Template part para mostrar lista de proyectos (nativo)
 *
 * @package Amentum
 */

if (have_posts()) : ?>
	<div class="projects-list">
		<?php while (have_posts()) : the_post(); ?>
			<article class="project-item" id="project-<?php the_ID(); ?>">
				<div class="project-item__image">
					<?php if (has_post_thumbnail()) : ?>
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail('card_image', [
								'alt' => get_the_title(),
								'loading' => 'lazy'
							]); ?>
						</a>
					<?php else : ?>
						<div class="project-item__placeholder">
							<span><?php _e('Sin imagen', 'amentum'); ?></span>
						</div>
					<?php endif; ?>
				</div>
				
				<div class="project-item__content">
					<h3 class="project-item__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					
					<?php
					// Mostrar taxonomías
					$terms = get_the_terms(get_the_ID(), 'tipo');
					if ($terms && !is_wp_error($terms)) :
					?>
						<div class="project-item__meta">
							<?php foreach ($terms as $term) : ?>
								<span class="project-tag"><?php echo esc_html($term->name); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					
					<?php if (has_excerpt()) : ?>
						<div class="project-item__excerpt">
							<?php the_excerpt(); ?>
						</div>
					<?php endif; ?>
					
					<div class="project-item__link">
						<a href="<?php the_permalink(); ?>" class="btn btn-outline">
							<?php _e('Ver Proyecto', 'amentum'); ?>
						</a>
					</div>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
	
	<?php
	// Paginación
	the_posts_pagination(array(
		'mid_size'  => 2,
		'prev_text' => __('« Anterior', 'amentum'),
		'next_text' => __('Siguiente »', 'amentum'),
	));
	?>
	
<?php else : ?>
	<div class="no-projects">
		<h3><?php _e('No hay proyectos disponibles', 'amentum'); ?></h3>
		<p><?php _e('Vuelve pronto para ver nuestros últimos trabajos.', 'amentum'); ?></p>
	</div>
<?php endif; ?>