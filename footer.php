<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Amentum
 */

?>





	<footer class="footer">
		<div class="footer__content">
			<!-- Sección superior con 3 columnas de información -->
			<div class="footer__info">
				<div class="footer__info-column">
					<h3 class="footer__info-title">EMAIL</h3>
					<?php if (get_theme_mod('amentum_email')) : ?>
						<a href="mailto:<?php echo get_theme_mod('amentum_email'); ?>" class="footer__info-text">
							<?php echo get_theme_mod('amentum_email'); ?>
						</a>
					<?php endif; ?>
					<?php if (get_theme_mod('amentum_address')) : ?>
						<p class="footer__info-text">
							<?php echo nl2br(get_theme_mod('amentum_address')); ?>
						</p>
					<?php endif; ?>
				</div>
				
				<div class="footer__info-column">
					<h3 class="footer__info-title">TELÉFONO</h3>
					<?php if (get_theme_mod('amentum_phone')) : ?>
						<a href="tel:<?php echo str_replace(' ', '', get_theme_mod('amentum_phone')); ?>" class="footer__info-text">
							<?php echo get_theme_mod('amentum_phone'); ?>
						</a>
					<?php endif; ?>
				</div>
				
				<div class="footer__info-column">
					<h3 class="footer__info-title">REDES SOCIALES</h3>
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-social',
								'container' => false,
								'menu_class' => 'footer__social-list',
								'fallback_cb' => false,
							)
						);
					?>
				</div>
			</div>
			
			<!-- Sección inferior con logo, copyright y links legales -->
			<div class="footer__bottom">
				<div class="footer__bottom-left">
					<p class="footer__copyright">© Amentum Events 2025</p>
				</div>
				
				<div class="footer__bottom-center">
					<?php 
					$footer_logo = get_theme_mod('amentum_footer_logo');
					if ($footer_logo) : ?>
						<?php echo wp_get_attachment_image($footer_logo, 'full', false, array('class' => 'footer__logo-img', 'alt' => get_bloginfo('name'))); ?>
					<?php endif; ?>
				</div>
				
				<div class="footer__bottom-right">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-legal',
								'container' => false,
								'menu_class' => 'footer__legal-list',
								'fallback_cb' => false,
							)
						);
					?>
				</div>
			</div>
		</div>
	</footer>
	<script>

</script>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
