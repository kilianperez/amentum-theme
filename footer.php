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



	<div class="newsletter">
		<div class="contenedor">
			<form id="frm-contact" action="" data-form="newsletter">
				<div class="newsletter__title"> 
					<p>Newsletter</p>
					<div class="newsletter__svgs init">
						<img src="<?php echo get_template_directory_uri() ?>/assets/dist/img/template/home/Smile.svg" alt="" class="newsletter__svg init">
						<img src="<?php echo get_template_directory_uri() ?>/assets/dist/img/template/home/Vector-1.svg" alt="" class="newsletter__svg load">
						<img src="<?php echo get_template_directory_uri() ?>/assets/dist/img/template/home/Heart.svg" alt="" class="newsletter__svg success">
					</div>
					<svg class="newsletter__exit link" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M22 1L1 22M1 1L22 22" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>

				</div>
				<div class="input-wrapper">
					<label for="news_email" class="sr-only">email</label>
					<input type="email" name="email" id="news_email" placeholder="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" required>
				</div>
				<br>
				<p class="check">
					<input type="checkbox" id="news_terms" name="terms" required>
					<label id="lbl-terms" for="news_terms" >He leído y acepto la <a href="<?php echo get_privacy_policy_url() ?>">política de privacidad y protección de datos</a></label>
				</p>
				<p class="frm-message"></p>
				<div class="newsletter__submit">
					<input type="url" id="news_pagina" name="pagina" value="<?php echo get_permalink() ?>"  hidden />
					<input type="text" id="news_tipo" name="tipo" value="newsletter"  hidden />
					<button class="link" id="news_submit" type="submit" name="submit">Suscribirme <span class="btn-arrow">→</span></button>
				</div>
			</form>
		</div>
	</div>


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
				
				<div class="footer__li newsletter-item">
					<ul>
						<li>
							<a class="newsletter-link" href="javascript: void(0)"><?php echo get_theme_mod('amentum_newsletter'); ?></a>
						</li>
					</ul>
				</div>
				
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
