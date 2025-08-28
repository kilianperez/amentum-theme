<?php 
    /**
    * Template Name: About
    */
    get_header(); ?>
 <main id="primary" class="projects">
     <?php
    //            get_template_part('template-parts/projects', 'list');

	$integrantes_equipo = get_field('integrantes_equipo');



    ?> 
    		<section class="section projects">
				<div class="projects__title container">
					<div class="title animation-intro-2"><span class="clip"><?php the_title(); ?></span></div>
				</div>
				<?php if ($integrantes_equipo) {
					?> 
					<div class="projects__list container about">
						<?php 
							foreach ($integrantes_equipo as $key => $integrante) {
								?> 
								<div class="project-card">
									<div class="project-card__img">
										<img src="<?php echo $integrante["integrantes_equipo_imagen"]['url'] ?>" alt="<?php echo $integrante["integrantes_equipo_imagen"]['alt'] ?>" />
									</div>
									<div class="project-card__body" style="--color-prod: #060606">
										<h3 class="project-card__title"><?php echo $integrante["integrantes_equipo_nombre"] ?></h3>
										<p class="project-card__tag"><?php echo $integrante["integrantes_equipo_cargo"] ?></p>
									</div>
								</div>


								<?php
							}

						?>
					</div>
					<?php
				} ?>
			</section>
    
    
    
    <?php
            

?>

 </main><!-- #main -->

 <?php
// get_sidebar();
get_footer(); // Incluir el footer (pie de página)
?>