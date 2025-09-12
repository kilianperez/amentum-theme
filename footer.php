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
		<nav class="footer__nav">
			<div class="footer__ul">
				<!-- Orden para móvil: Logo, Newsletter, Social, Legal, Copyright -->
				<?php 
				$footer_logo = get_theme_mod('amentum_footer_logo');
				if ($footer_logo) : ?>
				<div class="footer__li footer__logo mobile-only">
					<?php echo wp_get_attachment_image($footer_logo, 'full', false, array('class' => 'footer__logo-img', 'alt' => get_bloginfo('name'))); ?>
				</div>
				<?php endif; ?>
				
				
				<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-social',
							'container_class'=> 'footer__li social',     
						)
					);
				?>
				
				<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-legal',
							'container_class'=> 'footer__li legal',     
						)
					);
				?>
				
				<div class="footer__li copyright">
					<p>© <?php echo date("Y") ?> <?php echo get_theme_mod('amentum_copyright'); ?></p>
				</div>
			</div>
		</nav>
	</footer>
	<script>

</script>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
