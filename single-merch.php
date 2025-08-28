<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Amentum
 */

get_header();

$contenido_proyectos_cabecara = get_field('contenido_proyectos_cabecera');
$contenido_proyectos_cabecara_mobile = get_field('contenido_proyectos_cabecera_mobile');
$contenido_proyectos_contenido = get_field('contenido_proyectos_contenido');
$contenido_proyectos_titulo_superior = get_field('contenido_proyectos_titulo_superior');
?>
<main id="primary" class="site-main">


	<?php
	while (have_posts()) :
		the_post();
	?>
		<?php
		$terms = get_the_terms(get_the_ID(), 'tipo');
		?>



		<article class="merch container">

			<section class="proj-hero">

				<div class="proj-hero__container container">

					<div class="proj-hero__intro">
					<div class="proj-hero__small">
						<?php if ($contenido_proyectos_titulo_superior): ?>
							<?= $contenido_proyectos_titulo_superior; ?>
						<?php endif; ?>
					</div>
						<h1 class="proj-hero__h1"><?= the_title(); ?></h1>
						<div class="proj-hero__copy">
							<?php the_field('contenido_proyectos_descripcion'); ?>
						</div>
					</div>
					<div class="proj-hero__img">

						<?php if (!empty($contenido_proyectos_cabecara)) { 
							$file_type = $contenido_proyectos_cabecara['mime_type'];
							
							if (strpos($file_type, 'video/') === 0) {
								echo '<video class="tablet" autoplay muted loop playsinline>';
								echo '<source src="' . esc_url($contenido_proyectos_cabecara['url']) . '" type="' . esc_attr($file_type) . '">';
								echo 'Tu navegador no soporta el elemento video.';
								echo '</video>';
							} else {
								echo '<img class="tablet" src="' . esc_url($contenido_proyectos_cabecara['url']) . '" alt="' . esc_attr($contenido_proyectos_cabecara['alt']) . '">';
							}
						} else if (!empty($contenido_proyectos_cabecara_mobile)) { 
							$mobile_file_type = $contenido_proyectos_cabecara_mobile['mime_type'];
							
							if (strpos($mobile_file_type, 'video/') === 0) {
								echo '<video class="tablet" autoplay muted loop playsinline>';
								echo '<source src="' . esc_url($contenido_proyectos_cabecara_mobile['url']) . '" type="' . esc_attr($mobile_file_type) . '">';
								echo 'Tu navegador no soporta el elemento video.';
								echo '</video>';
							} else {
								echo '<img class="tablet" src="' . esc_url($contenido_proyectos_cabecara_mobile['url']) . '" alt="' . esc_attr($contenido_proyectos_cabecara_mobile['alt']) . '">';
							}
						} ?>

					</div>
				</div>

				<?php if (!empty($contenido_proyectos_cabecara_mobile)) { 
					$mobile_file_type = $contenido_proyectos_cabecara_mobile['mime_type'];
					
					if (strpos($mobile_file_type, 'video/') === 0) {
						echo '<video class="mobile" autoplay muted loop playsinline>';
						echo '<source src="' . esc_url($contenido_proyectos_cabecara_mobile['url']) . '" type="' . esc_attr($mobile_file_type) . '">';
						echo 'Tu navegador no soporta el elemento video.';
						echo '</video>';
					} else {
						echo '<img class="mobile" src="' . esc_url($contenido_proyectos_cabecara_mobile['url']) . '" alt="' . esc_attr($contenido_proyectos_cabecara_mobile['alt']) . '">';
					}
				} else if (!empty($contenido_proyectos_cabecara)) { 
					$file_type = $contenido_proyectos_cabecara['mime_type'];
					
					if (strpos($file_type, 'video/') === 0) {
						echo '<video class="mobile" autoplay muted loop playsinline>';
						echo '<source src="' . esc_url($contenido_proyectos_cabecara['url']) . '" type="' . esc_attr($file_type) . '">';
						echo 'Tu navegador no soporta el elemento video.';
						echo '</video>';
					} else {
						echo '<img class="mobile" src="' . esc_url($contenido_proyectos_cabecara['url']) . '" alt="' . esc_attr($contenido_proyectos_cabecara['alt']) . '">';
					}
				} ?>
			</section>

			<?php if (!empty($contenido_proyectos_contenido)) {
				$total_elements = count($contenido_proyectos_contenido);
			?>

				<section class="prod-imgs">
					<?php

					foreach ($contenido_proyectos_contenido  as $key => $contenido) {
						$seccion_mobile = $contenido['contenido_proyectos_contenido_seccion_mobile'];
						$seccion_desktop = $contenido['contenido_proyectos_contenido_seccion_desktop'];
						$mobile = false;


						if (!empty($seccion_desktop)) {

					?>
							<div class="prod-imgs__img<?php echo $key !== $total_elements - 1 ? ' effect' : ' last' ?>">
								<?php
								if (!empty($seccion_mobile)) {
									foreach ($seccion_mobile as $i => $archivo) {
										$mobile = $archivo['contenido_proyectos_contenido_seccion_mobile_archivo'];

										if (!empty($mobile)) {
											if ($mobile['type'] !==  'video') {
								?>
												<div class="prod-imgs__col<?php echo count($seccion_mobile) >= 2 ? ' prod-imgs__col--2' : '' ?> mobile">
													<img src="<?php echo $mobile['url'] ?>" alt="<?php echo $mobile['alt'] ?>">
												</div>
											<?php
											} else {
											?>
												<div class="prod-imgs__col<?php echo count($seccion_mobile) >= 2 ? ' prod-imgs__col--2' : '' ?> mobile">
													<video class="lazy" autoplay muted loop playsinline src="<?php echo $mobile['url'] ?>">
														<source src="<?php echo $mobile['url']; ?>" type="<?php echo $mobile['mime_type'] ?>">
													</video>
												</div>
											<?php
											}
										}
									}
									$mobile = true;
								}
								foreach ($seccion_desktop as $i => $archivo) {
									$desktop = $archivo['contenido_proyectos_contenido_seccion_desktop_archivo'];

									if (!empty($desktop)) {
										if (!empty($archivo) && $desktop['type'] !==  'video') { ?>
											<div class="prod-imgs__col<?php echo count($seccion_desktop) >= 2 ? ' prod-imgs__col--2' : '' ?><?php echo $mobile ? ' tablet' : ''; ?>">
												<img src="<?php echo $desktop['url']; ?>" alt="<?php echo $desktop['alt']; ?>">
											</div>
										<?php
										} elseif (!empty($archivo)) { ?>
											<div class="prod-imgs__col<?php echo count($seccion_desktop) >= 2 ? ' prod-imgs__col--2' : '' ?><?php echo $mobile ? ' tablet' : ''; ?>">
												<video class="lazy" autoplay muted loop playsinline src="<?php echo $desktop['url'] ?>">
													<source src="<?php echo $desktop['url']; ?>" type="<?php echo $desktop['mime_type'] ?>">
												</video>
											</div>
							<?php }
									}
								}
							} ?>
							</div>
						<?php

						$mobile = false;
					}
						?>


				</section>
			<?php } ?>

		</article>
		

		<?php get_template_part('template-parts/merch', 'list'); ?>
	<?php


	endwhile; // End of the loop.
	?>

</main><!-- #main -->

<?php
// get_sidebar();
get_footer();
?>


<?php
/*

							
echo '<pre>';
var_dump($desktop);
echo '<pre>';
if($desktop['type'] !==  'video' ){
	?> 
		<div class="prod-imgs__img">
			<img <?php echo !empty($mobile) ? 'class="tablet"' : '' ;?> src="<?php echo $desktop['url'];?>" alt="<?php echo $desktop['alt']; ?>" srcset="">
			<?php if (!empty($mobile)) { ?>
				<img class="mobile" src="<?php echo $mobile['url'] ?>" alt="<?php echo $mobile['alt'] ?>" srcset="">
			<?php } ?>
		</div>
	<?php
}else{
	?> 
		<div class="prod-imgs__img">
			<video autoplay  loop controls <?php echo !empty($mobile) ? 'class="tablet"' : '' ;?> >
				<source src="<?php echo $desktop['url'];?>" type="<?php echo $desktop['mime_type']?>">
			</video>
			<?php if (!empty($mobile)) { ?>
				<video autoplay  loop  class="mobile" src="<?php echo $mobile['url'] ?>">
					<source src="<?php echo $mobile['url'];?>" type="<?php echo $mobile['mime_type']?>">
				</video>
			<?php } ?>
		</div>
	<?php
}
*/

?>

<div class="bloque">
	<div class="columnas"></div>
	<div class="columnas"></div>
</div>