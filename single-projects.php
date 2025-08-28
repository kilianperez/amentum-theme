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
?>
<main id="primary" class="site-main">

	<?php
        while (have_posts()) :
            the_post();
            ?>
	<article class="project">
			<section class="proj-hero">

				<div class="proj-hero__container container">
					<div class="proj-hero__intro">
						<h1 class="proj-hero__h1"><?php the_title() ?></h1>

						<?php
$terms = get_the_terms( get_the_ID(), 'tipo' );
?>
<ul class="proj-hero__tags">
<?php if ( $terms && ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) { ?>
		<li><?php echo esc_html( $term->name ); ?></li>
	<?php }
} ?>
</ul>

					</div>
					<div class="proj-hero__copy"><?php the_field('contenido_proyectos_descripcion'); ?></div>
				</div>
				<?php if(!empty($contenido_proyectos_cabecara)){
					?> 
						<div class="proj-hero__img container">
							<?php 
							// Cabecera Desktop/Tablet - mantener lógica original
							$file_url = $contenido_proyectos_cabecara['url'];
							$file_type = $contenido_proyectos_cabecara['mime_type'] ?? '';
							// Solo agregar clase tablet si hay imagen móvil configurada
							$class_attr = !empty($contenido_proyectos_cabecara_mobile) ? 'class="tablet"' : '';
							
							// Detectar tipo de archivo por extensión si mime_type no está disponible
							$file_extension = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
							$video_extensions = ['mp4', 'mov', 'avi', 'webm'];
							
							$is_video = (strpos($file_type, 'video/') === 0) || in_array($file_extension, $video_extensions);
							
							if ($is_video) {
								echo '<video ' . $class_attr . ' autoplay muted loop playsinline>';
								echo '<source src="' . esc_url($file_url) . '" type="video/' . $file_extension . '">';
								echo 'Tu navegador no soporta el elemento video.';
								echo '</video>';
							} else {
								echo '<img ' . $class_attr . ' src="' . esc_url($file_url) . '" alt="' . esc_attr($contenido_proyectos_cabecara['alt']) . '" srcset="">';
							}
							?>
							
							<?php if (!empty($contenido_proyectos_cabecara_mobile)) { 
								$mobile_file_url = $contenido_proyectos_cabecara_mobile['url'];
								$mobile_file_type = $contenido_proyectos_cabecara_mobile['mime_type'] ?? '';
								$mobile_file_extension = strtolower(pathinfo($mobile_file_url, PATHINFO_EXTENSION));
								$mobile_is_video = (strpos($mobile_file_type, 'video/') === 0) || in_array($mobile_file_extension, $video_extensions);
								
								if ($mobile_is_video) {
									echo '<video class="mobile" autoplay muted loop playsinline>';
									echo '<source src="' . esc_url($mobile_file_url) . '" type="video/' . $mobile_file_extension . '">';
									echo 'Tu navegador no soporta el elemento video.';
									echo '</video>';
								} else {
									echo '<img class="mobile" src="' . esc_url($mobile_file_url) . '" alt="' . esc_attr($contenido_proyectos_cabecara_mobile['alt']) . '" srcset="">';
								}
							} ?>
						</div>
					
					<?php
				} ?>
			</section>
		<?php if (!empty($contenido_proyectos_contenido)) { 
			$total_elements = count($contenido_proyectos_contenido);
			?>
			
			<section class="prod-imgs container">
				<?php 
				
				foreach ($contenido_proyectos_contenido  as $key => $contenido) {
					$seccion_mobile = $contenido['contenido_proyectos_contenido_seccion_mobile'];
					$seccion_desktop = $contenido['contenido_proyectos_contenido_seccion_desktop'];
					$mobile = false;
			

						if (!empty($seccion_desktop)){
									
					?> 
					<div class="prod-imgs__img<?php echo $key !== $total_elements - 1 ? ' effect' : ' last' ?>"> <?php
							if (!empty($seccion_mobile)) {
								foreach ($seccion_mobile as $i => $archivo) {
									$mobile = $archivo['contenido_proyectos_contenido_seccion_mobile_archivo'];
		
									if(!empty($mobile)){
										if($mobile['type'] !==  'video' ){
											?>
												<div class="prod-imgs__col<?php echo count($seccion_mobile) >= 2 ? ' prod-imgs__col--2' : '' ?> mobile">
													<img src="<?php echo $mobile['url'] ?>" alt="<?php echo $mobile['alt'] ?>">
												</div>
											<?php
										}else{
											?> 
											<div class="prod-imgs__col<?php echo count($seccion_mobile) >= 2 ? ' prod-imgs__col--2' : '' ?> mobile">
												<video class="lazy" autoplay muted loop playsinline src="<?php echo $mobile['url'] ?>">
													<source src="<?php echo $mobile['url'];?>" type="<?php echo $mobile['mime_type']?>">
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

								if(!empty($desktop)){
									if(!empty($archivo) && $desktop['type'] !==  'video' ){?>
										<div class="prod-imgs__col<?php echo count($seccion_desktop) >= 2 ? ' prod-imgs__col--2' : '' ?><?php echo $mobile ? ' tablet' : '' ;?>">
											<img src="<?php echo $desktop['url'];?>" alt="<?php echo $desktop['alt']; ?>">
										</div>
									<?php
									}elseif(!empty($archivo)){ ?> 
										<div class="prod-imgs__col<?php echo count($seccion_desktop) >= 2 ? ' prod-imgs__col--2' : '' ?><?php echo $mobile ? ' tablet' : '' ;?>">
											<video class="lazy" autoplay muted loop playsinline src="<?php echo $desktop['url'] ?>">
												<source src="<?php echo $desktop['url'];?>" type="<?php echo $desktop['mime_type']?>">
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
		<section class="projects">
			<div class="projects__title container">
				<h2 class="title">more projects</h2>
				<!-- <a href="./works.html" title="Ver projects" class="btn-svg">
					<svg width="63" height="63" viewBox="0 0 63 63" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M30.423 54.1974C31.016 54.7903 31.9815 54.7929 32.577 54.1974L54.1974 32.577C54.2607 32.5136 54.319 32.4401 54.3748 32.359L54.4026 32.321C54.4356 32.2729 54.4761 32.1918 54.5166 32.1056L54.5344 32.0626C54.5572 32.0043 54.5851 31.9155 54.6053 31.8193L54.6155 31.7787C54.6256 31.7281 54.6332 31.6698 54.6357 31.6064L54.5344 31.3074L54.613 31.2289C54.613 31.2289 54.6104 31.2111 54.6053 31.201C54.58 31.0844 54.5521 30.9957 54.5167 30.9146C54.471 30.8082 54.4254 30.7271 54.3773 30.6536C54.3139 30.5598 54.2531 30.4889 54.1898 30.4256L32.5694 8.80515C31.9764 8.21218 31.0109 8.20965 30.4154 8.80515C29.8199 9.40065 29.8225 10.3661 30.4154 10.9591L49.4384 29.9821L9.87198 29.9821C9.03068 29.9821 8.34902 30.6638 8.34902 31.5051C8.34902 31.9105 8.50867 32.2932 8.79501 32.5795C9.08136 32.8658 9.46399 33.0255 9.86944 33.0255L49.4384 33.0229L30.4154 52.046C29.8225 52.6389 29.8224 53.6069 30.4154 54.1999L30.423 54.1974Z" fill="black" />
						<path d="M53.7843 9.21566C41.4968 -3.0719 21.5032 -3.07188 9.21567 9.21567C-3.07188 21.5032 -3.0719 41.4968 9.21566 53.7843C21.5032 66.0719 41.4968 66.0719 53.7843 53.7843C66.0719 41.4968 66.0719 21.5032 53.7843 9.21566ZM10.9337 52.0662C-0.406087 40.7264 -0.406095 22.2736 10.9337 10.9337C22.2736 -0.406095 40.7264 -0.406089 52.0662 10.9337C63.4061 22.2736 63.4061 40.7264 52.0663 52.0663C40.7264 63.4061 22.2736 63.4061 10.9337 52.0662Z" fill="black" />
					</svg>
				</a> -->
			</div>

			<?php 

			$args = array(
				'post_type' => 'projects',
				'posts_per_page' => -1, // Número de publicaciones a mostrar
				'orderby' => 'date', // Ordenar por fecha
				'order' => 'DESC', // Orden descendente (de más nuevo a más antiguo)
				'post_status' => 'publish'
			);

			$id = get_the_ID();
			$projects = get_posts( $args );
			$activador = false;
			$nextProjects = [];
			$othersProj = [];
			foreach ($projects as $i => $value) {

				if($value->ID === $id){
					$activador = true;
				}
				if($activador){
					$nextProjects[] = $value;
				}elseif($value->ID !== $id){
					$othersProj[] = $value;
				}
				
			}

			// Eliminamos el primer elemento del array

			if(count($nextProjects) > 0){

				array_shift($nextProjects);


				// Array 1

				// Combinamos ambos arrays y convertimos los objetos en strings
				$mergedArr = array_map('serialize', array_merge($nextProjects, $othersProj));

				// Obtenemos valores únicos
				$uniqueArr = array_unique($mergedArr);

				// Volvemos a convertir los strings en objetos
				$unserializedArr = array_map('unserialize', $uniqueArr);
				
				$arrayFinal = array_slice($unserializedArr, 0, 4);



				get_template_part('template-parts/projects', 'list', $arrayFinal);
			}
			?>

		</section>
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