<?php
/**
 * Template Name: Showcase - Elementos Nativos
 * 
 * Página para mostrar todos los elementos del theme de forma nativa
 *
 * @package Amentum
 */

get_header(); ?>

<main id="primary" class="site-main">
	<div class="container">
		<header class="page-header">
			<h1 class="page-title"><?php _e('Showcase - Elementos Nativos de Amentum', 'amentum'); ?></h1>
			<p class="page-description"><?php _e('Esta página muestra todos los elementos del theme funcionando de forma nativa, sin dependencias de ACF.', 'amentum'); ?></p>
		</header>

		<!-- Sección Hero -->
		<section class="showcase-section" id="hero-section">
			<h2 class="section-title"><?php _e('Sección Hero', 'amentum'); ?></h2>
			<div class="hero-home">
				<div class="hero-home__content">
					<h1 class="hero-home__title"><?php _e('Hola, somos Amentum', 'amentum'); ?></h1>
					<p class="hero-home__subtitle"><?php _e('Agencia creativa especializada en diseño, branding y desarrollo web', 'amentum'); ?></p>
					<a href="#proyectos-section" class="btn btn-primary"><?php _e('Ver Proyectos', 'amentum'); ?></a>
				</div>
			</div>
		</section>

		<!-- Sección Proyectos -->
		<section class="showcase-section" id="proyectos-section">
			<h2 class="section-title"><?php _e('Proyectos Destacados (Nativo)', 'amentum'); ?></h2>
			<?php
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
								<?php else : ?>
									<div class="project-placeholder">
										<span><?php _e('Imagen del proyecto', 'amentum'); ?></span>
									</div>
								<?php endif; ?>
							</div>
							<div class="project-card__content">
								<h3 class="project-card__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>
								<?php
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
			<?php else : ?>
				<div class="no-content">
					<p><?php _e('No hay proyectos disponibles. Crea algunos proyectos en el admin de WordPress.', 'amentum'); ?></p>
					<p><strong><?php _e('Tip:', 'amentum'); ?></strong> <?php _e('Ve a Admin → Proyectos → Añadir nuevo', 'amentum'); ?></p>
				</div>
			<?php endif; 
			wp_reset_postdata(); ?>
		</section>

		<!-- Sección Magazine -->
		<section class="showcase-section" id="magazine-section">
			<h2 class="section-title"><?php _e('Magazine (Nativo)', 'amentum'); ?></h2>
			<?php
			$magazine_query = new WP_Query(array(
				'post_type' => 'magazine',
				'posts_per_page' => 4,
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'DESC'
			));
			
			if ($magazine_query->have_posts()) : ?>
				<div class="magazine-grid">
					<?php while ($magazine_query->have_posts()) : $magazine_query->the_post(); ?>
						<article class="magazine-card">
							<div class="magazine-card__image">
								<?php if (has_post_thumbnail()) : ?>
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail('card_image', ['alt' => get_the_title()]); ?>
									</a>
								<?php endif; ?>
							</div>
							<div class="magazine-card__content">
								<div class="magazine-card__date">
									<?php echo get_the_date(); ?>
								</div>
								<h3 class="magazine-card__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>
								<?php if (has_excerpt()) : ?>
									<div class="magazine-card__excerpt">
										<?php the_excerpt(); ?>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="no-content">
					<p><?php _e('No hay artículos de magazine disponibles. Crea algunos en el admin de WordPress.', 'amentum'); ?></p>
					<p><strong><?php _e('Tip:', 'amentum'); ?></strong> <?php _e('Ve a Admin → Magazine → Añadir nuevo', 'amentum'); ?></p>
				</div>
			<?php endif; 
			wp_reset_postdata(); ?>
		</section>

		<!-- Sección Merch -->
		<section class="showcase-section" id="merch-section">
			<h2 class="section-title"><?php _e('Merch (Nativo)', 'amentum'); ?></h2>
			<?php
			$merch_query = new WP_Query(array(
				'post_type' => 'merch',
				'posts_per_page' => 4,
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'DESC'
			));
			
			if ($merch_query->have_posts()) : ?>
				<div class="merch-grid">
					<?php while ($merch_query->have_posts()) : $merch_query->the_post(); ?>
						<article class="merch-card">
							<div class="merch-card__image">
								<?php if (has_post_thumbnail()) : ?>
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail('card_image', ['alt' => get_the_title()]); ?>
									</a>
								<?php endif; ?>
							</div>
							<div class="merch-card__content">
								<h3 class="merch-card__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>
								<?php if (has_excerpt()) : ?>
									<div class="merch-card__excerpt">
										<?php the_excerpt(); ?>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="no-content">
					<p><?php _e('No hay productos merch disponibles. Crea algunos en el admin de WordPress.', 'amentum'); ?></p>
					<p><strong><?php _e('Tip:', 'amentum'); ?></strong> <?php _e('Ve a Admin → Merch → Añadir nuevo', 'amentum'); ?></p>
				</div>
			<?php endif; 
			wp_reset_postdata(); ?>
		</section>

		<!-- Sección Formulario de Contacto Nativo -->
		<section class="showcase-section" id="contact-section">
			<h2 class="section-title"><?php _e('Formulario de Contacto (Nativo)', 'amentum'); ?></h2>
			<div class="contact-form-native">
				<form id="amentum-contact-form" class="contact-form" method="post">
					<div class="form-row">
						<div class="form-group form-group--half">
							<label for="contact-name"><?php _e('Nombre *', 'amentum'); ?></label>
							<input type="text" id="contact-name" name="nombre" required>
						</div>
						<div class="form-group form-group--half">
							<label for="contact-email"><?php _e('Email *', 'amentum'); ?></label>
							<input type="email" id="contact-email" name="email" required>
						</div>
					</div>
					
					<div class="form-group">
						<label for="contact-services"><?php _e('Servicios de interés', 'amentum'); ?></label>
						<div class="form-tags">
							<label class="tag-option">
								<input type="checkbox" name="servicios[]" value="diseno">
								<span>Diseño</span>
							</label>
							<label class="tag-option">
								<input type="checkbox" name="servicios[]" value="branding">
								<span>Branding</span>
							</label>
							<label class="tag-option">
								<input type="checkbox" name="servicios[]" value="web">
								<span>Diseño Web</span>
							</label>
							<label class="tag-option">
								<input type="checkbox" name="servicios[]" value="fotografia">
								<span>Fotografía</span>
							</label>
						</div>
					</div>
					
					<div class="form-group">
						<label for="contact-message"><?php _e('Tu mensaje *', 'amentum'); ?></label>
						<textarea id="contact-message" name="mensaje" rows="5" required></textarea>
					</div>
					
					<div class="form-group">
						<label class="checkbox-label">
							<input type="checkbox" name="newsletter" value="1">
							<span><?php _e('Quiero recibir el newsletter', 'amentum'); ?></span>
						</label>
					</div>
					
					<div class="form-group">
						<button type="submit" class="btn btn-primary">
							<?php _e('Enviar Mensaje', 'amentum'); ?>
						</button>
					</div>
					
					<input type="hidden" name="action" value="amentum_ajax_frm_contact">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('secret-key-form'); ?>">
				</form>
				
				<div id="form-response" class="form-response" style="display: none;"></div>
			</div>
		</section>

		<!-- Información de Custom Post Types -->
		<section class="showcase-section" id="info-section">
			<h2 class="section-title"><?php _e('Información del Sistema', 'amentum'); ?></h2>
			<div class="info-grid">
				<div class="info-card">
					<h3><?php _e('Custom Post Types Activos', 'amentum'); ?></h3>
					<ul>
						<li><strong>Projects:</strong> <?php echo wp_count_posts('projects')->publish; ?> <?php _e('publicados', 'amentum'); ?></li>
						<li><strong>Magazine:</strong> <?php echo wp_count_posts('magazine')->publish; ?> <?php _e('publicados', 'amentum'); ?></li>
						<li><strong>Merch:</strong> <?php echo wp_count_posts('merch')->publish; ?> <?php _e('publicados', 'amentum'); ?></li>
					</ul>
				</div>
				
				<div class="info-card">
					<h3><?php _e('Funcionalidades Nativas', 'amentum'); ?></h3>
					<ul>
						<li>✅ <?php _e('Custom Post Types', 'amentum'); ?></li>
						<li>✅ <?php _e('Taxonomías personalizadas', 'amentum'); ?></li>
						<li>✅ <?php _e('Formulario de contacto AJAX', 'amentum'); ?></li>
						<li>✅ <?php _e('Customizer integrado', 'amentum'); ?></li>
						<li>✅ <?php _e('Imágenes destacadas', 'amentum'); ?></li>
						<li>✅ <?php _e('Soporte SVG', 'amentum'); ?></li>
					</ul>
				</div>
				
				<div class="info-card">
					<h3><?php _e('Enlaces Útiles', 'amentum'); ?></h3>
					<ul>
						<li><a href="<?php echo admin_url('post-new.php?post_type=projects'); ?>"><?php _e('Crear Proyecto', 'amentum'); ?></a></li>
						<li><a href="<?php echo admin_url('post-new.php?post_type=magazine'); ?>"><?php _e('Crear Artículo', 'amentum'); ?></a></li>
						<li><a href="<?php echo admin_url('post-new.php?post_type=merch'); ?>"><?php _e('Crear Merch', 'amentum'); ?></a></li>
						<li><a href="<?php echo admin_url('customize.php'); ?>"><?php _e('Personalizar Theme', 'amentum'); ?></a></li>
					</ul>
				</div>
			</div>
		</section>
	</div>
</main>

<style>
/* Estilos específicos para la página showcase */
.showcase-section {
	margin: 60px 0;
	padding: 40px 0;
	border-bottom: 1px solid #eee;
}

.showcase-section:last-child {
	border-bottom: none;
}

.section-title {
	font-size: 2.5em;
	margin-bottom: 30px;
	text-align: center;
	color: #333;
}

.projects-grid,
.magazine-grid,
.merch-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 30px;
	margin-top: 40px;
}

.project-card,
.magazine-card,
.merch-card {
	background: #fff;
	border-radius: 10px;
	overflow: hidden;
	box-shadow: 0 4px 15px rgba(0,0,0,0.1);
	transition: transform 0.3s ease;
}

.project-card:hover,
.magazine-card:hover,
.merch-card:hover {
	transform: translateY(-5px);
}

.project-card__image,
.magazine-card__image,
.merch-card__image {
	height: 200px;
	overflow: hidden;
}

.project-card__image img,
.magazine-card__image img,
.merch-card__image img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.project-placeholder {
	height: 100%;
	background: #f5f5f5;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #999;
}

.project-card__content,
.magazine-card__content,
.merch-card__content {
	padding: 20px;
}

.project-card__title a,
.magazine-card__title a,
.merch-card__title a {
	text-decoration: none;
	color: #333;
	font-size: 1.2em;
	font-weight: bold;
}

.project-card__tags {
	margin-top: 10px;
}

.tag {
	background: #007cba;
	color: white;
	padding: 4px 8px;
	border-radius: 4px;
	font-size: 0.8em;
	margin-right: 5px;
}

.magazine-card__date {
	color: #666;
	font-size: 0.9em;
	margin-bottom: 10px;
}

.hero-home {
	text-align: center;
	padding: 80px 20px;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	border-radius: 15px;
}

.hero-home__title {
	font-size: 3em;
	margin-bottom: 20px;
}

.hero-home__subtitle {
	font-size: 1.3em;
	margin-bottom: 30px;
	opacity: 0.9;
}

.btn {
	display: inline-block;
	padding: 12px 24px;
	text-decoration: none;
	border-radius: 5px;
	font-weight: bold;
	transition: all 0.3s ease;
	border: none;
	cursor: pointer;
}

.btn-primary {
	background: #007cba;
	color: white;
}

.btn-primary:hover {
	background: #005a87;
}

.contact-form {
	max-width: 600px;
	margin: 0 auto;
}

.form-row {
	display: flex;
	gap: 20px;
	margin-bottom: 20px;
}

.form-group {
	margin-bottom: 20px;
}

.form-group--half {
	flex: 1;
}

.form-group label {
	display: block;
	margin-bottom: 5px;
	font-weight: bold;
}

.form-group input,
.form-group textarea {
	width: 100%;
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 5px;
}

.form-tags {
	display: flex;
	flex-wrap: wrap;
	gap: 10px;
	margin-top: 10px;
}

.tag-option {
	display: flex;
	align-items: center;
	cursor: pointer;
}

.tag-option input {
	width: auto;
	margin-right: 5px;
}

.info-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 30px;
	margin-top: 30px;
}

.info-card {
	background: #f9f9f9;
	padding: 30px;
	border-radius: 10px;
}

.info-card h3 {
	margin-bottom: 20px;
	color: #333;
}

.info-card ul {
	list-style: none;
	padding: 0;
}

.info-card li {
	padding: 5px 0;
}

.no-content {
	text-align: center;
	padding: 40px;
	background: #f9f9f9;
	border-radius: 10px;
	margin-top: 20px;
}

.form-response {
	margin-top: 20px;
	padding: 15px;
	border-radius: 5px;
}

.form-response.success {
	background: #d4edda;
	color: #155724;
	border: 1px solid #c3e6cb;
}

.form-response.error {
	background: #f8d7da;
	color: #721c24;
	border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
	.form-row {
		flex-direction: column;
	}
	
	.hero-home__title {
		font-size: 2em;
	}
	
	.section-title {
		font-size: 2em;
	}
}
</style>

<?php get_footer(); ?>