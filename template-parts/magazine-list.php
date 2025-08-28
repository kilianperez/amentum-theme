<?php
/**
 * Template part para mostrar lista de magazine (nativo)
 *
 * @package Amentum
 */

if (have_posts()) : ?>
	<div class="magazine-list">
		<?php while (have_posts()) : the_post(); ?>
			<article class="magazine-item" id="magazine-<?php the_ID(); ?>">
				<div class="magazine-item__image">
					<?php if (has_post_thumbnail()) : ?>
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail('card_image', [
								'alt' => get_the_title(),
								'loading' => 'lazy'
							]); ?>
						</a>
					<?php endif; ?>
				</div>
				
				<div class="magazine-item__content">
					<div class="magazine-item__date">
						<?php echo get_the_date(); ?>
					</div>
					
					<h3 class="magazine-item__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					
					<?php if (has_excerpt()) : ?>
						<div class="magazine-item__excerpt">
							<?php the_excerpt(); ?>
						</div>
					<?php endif; ?>
					
					<div class="magazine-item__link">
						<a href="<?php the_permalink(); ?>" class="btn btn-outline">
							<?php _e('Leer Más', 'amentum'); ?>
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
	<div class="no-magazine">
		<h3><?php _e('No hay artículos disponibles', 'amentum'); ?></h3>
		<p><?php _e('Vuelve pronto para leer nuestros últimos artículos.', 'amentum'); ?></p>
	</div>
<?php endif; ?>