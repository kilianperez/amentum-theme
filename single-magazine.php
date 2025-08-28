<?php
/**
 * The template for displaying all single magazine posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Amentum
 */

get_header();

$contenido_magazine_contenido = get_field('contenido_magazine_contenido');
$magazine_descripcion = get_field('magazine_descripcion');
$magazine_numero_paginas = get_field('magazine_numero_paginas');
$magazine_idioma = get_field('magazine_idioma');
$magazine_archivo = get_field('magazine_archivo');

// Valores temporales para testing si los campos están vacíos
if (empty($magazine_numero_paginas)) {
    $magazine_numero_paginas = 14; // Valor de prueba
}
if (empty($magazine_idioma)) {
    $magazine_idioma = 'Español'; // Valor de prueba
}
?>
<main id="primary" class="site-main">
	<?php
	while (have_posts()) :
		the_post();
	?>
		<article class="magazine">
			<?php if (!empty($contenido_magazine_contenido)) { ?>
				<section class="magazine-content">
					<div class="magazine-layout">
						<!-- Bloque 1: Swiper para mobile -->
						<div class="magazine-swiper mobile">
							<div class="swiper-wrapper">
								<?php
								foreach ($contenido_magazine_contenido as $contenido) {
									$archivo_mobile = $contenido['contenido_magazine_contenido_archivo_mobile'];
									$archivo_desktop = $contenido['contenido_magazine_contenido_archivo_desktop'];
									// Priorizar mobile, si no existe usar desktop
									$archivo = !empty($archivo_mobile) ? $archivo_mobile : $archivo_desktop;
									
									if (!empty($archivo)) {
										$is_video = $archivo['type'] === 'video';
								?>
									<div class="swiper-slide">
										<?php if (!$is_video): ?>
											<img src="<?= esc_url($archivo['url']); ?>" alt="<?= esc_attr($archivo['alt']); ?>">
										<?php else: ?>
											<video autoplay muted loop playsinline>
												<source src="<?= esc_url($archivo['url']); ?>" type="<?= esc_attr($archivo['mime_type']); ?>">
											</video>
										<?php endif; ?>
									</div>
								<?php
									}
								}
								?>
							</div>
							<div class="swiper-pagination"></div>
						</div>

						<!-- Bloque 2: Imágenes para desktop (sticky layout) -->
						<div class="magazine-images tablet">
							<?php
							foreach ($contenido_magazine_contenido as $contenido) {
								$archivo_desktop = $contenido['contenido_magazine_contenido_archivo_desktop'];
								
								if (!empty($archivo_desktop)) {
									$is_video = $archivo_desktop['type'] === 'video';
							?>
								<div class="magazine-image">
									<?php if (!$is_video): ?>
										<img src="<?= esc_url($archivo_desktop['url']); ?>" alt="<?= esc_attr($archivo_desktop['alt']); ?>">
									<?php else: ?>
										<video autoplay muted loop playsinline>
											<source src="<?= esc_url($archivo_desktop['url']); ?>" type="<?= esc_attr($archivo_desktop['mime_type']); ?>">
										</video>
									<?php endif; ?>
								</div>
							<?php
								}
							}
							?>
						</div>

						<!-- Bloque 3: Texto compartido (100% mobile, 50% desktop) -->
						<div class="magazine-text">
							<div class="magazine-text-content">
								<h2 class="magazine-title"><?= get_the_title(); ?></h2>
								
								<?php if ($magazine_descripcion): ?>
									<div class="magazine-description">
										<?= $magazine_descripcion; ?>
									</div>
								<?php endif; ?>
								
								<?php if ($magazine_numero_paginas): ?>
									<div class="magazine-pages">
										<?php 
										// Controlar singular/plural
										$paginas_texto = ($magazine_numero_paginas == 1) ? 'página' : 'páginas';
										echo $magazine_numero_paginas . ' ' . $paginas_texto;
										?>
									</div>
								<?php endif; ?>
								
								<?php if ($magazine_idioma): ?>
									<div class="magazine-language">
										Idioma: <?= esc_html($magazine_idioma); ?>
									</div>
								<?php endif; ?>
								
								<?php if ($magazine_archivo): ?>
									<div class="magazine-download">
										<a href="<?= esc_url($magazine_archivo['url']); ?>" 
										   download="<?= esc_attr($magazine_archivo['filename']); ?>"
										   class="magazine-download-link">
											Descargar
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</section>
			<?php } ?>
		</article>

		<?php get_template_part('template-parts/magazine', 'list'); ?>
	<?php
	endwhile; // End of the loop.
	?>
</main><!-- #main -->

<?php
get_footer();
?>