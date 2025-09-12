<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Amentum
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-barba="wrapper">
<?php wp_body_open(); ?>

<?php if ( ! is_user_logged_in() ): ?>
<div class="intro-home">
	<div class="animation-palp">
		<svg xmlns="http://www.w3.org/2000/svg" id="Capa_1" data-name="Capa 1" viewBox="0 0 580.4 267.84">
			<defs>
				<style>
				.cls-1 {
					stroke-width: 0px;
				}
				</style>
			</defs>
			<path class="cls-1" d="M0,262.08L101.52,5.76h34.92l103.05,256.32h-32.31l-69.03-175.68c-2.88-7.38-5.78-14.83-8.69-22.36-2.91-7.53-5.73-14.95-8.46-22.27-2.73-7.32-5.54-15-8.42-23.04h11.97c-2.7,7.98-5.34,15.6-7.92,22.86-2.58,7.26-5.3,14.67-8.15,22.23-2.85,7.56-5.75,15.09-8.69,22.59L31.95,262.08H0ZM55.35,190.17v-26.82h128.79v26.82H55.35Z"/>
			<path class="cls-1" d="M247.23,173.43v-25.83h102.24v25.83h-102.24Z"/>
			<path class="cls-1" d="M483.74,267.84c-21.6,0-40.26-3.17-55.98-9.5-15.72-6.33-27.93-16-36.63-29.02-8.7-13.02-13.23-29.46-13.59-49.32h31.32c.3,14.82,3.61,26.73,9.94,35.73,6.33,9,14.95,15.47,25.88,19.39,10.92,3.93,23.91,5.9,38.97,5.9,14.04,0,25.93-1.85,35.68-5.54,9.75-3.69,17.13-9.1,22.14-16.25,5.01-7.14,7.52-15.84,7.52-26.1,0-8.28-1.98-15.31-5.94-21.1-3.96-5.79-10.39-10.72-19.3-14.8s-21.02-7.68-36.32-10.8l-26.55-5.4c-15.6-3.18-28.93-7.74-40-13.68s-19.7-13.84-25.88-23.71c-6.18-9.87-9.27-21.88-9.27-36.05s3.52-25.35,10.58-35.55c7.05-10.2,17.25-18.09,30.6-23.67,13.35-5.58,29.14-8.37,47.38-8.37,23.1,0,41.97,3.46,56.61,10.4,14.64,6.93,25.3,16.26,31.99,27.99,6.69,11.73,10.22,25.15,10.58,40.27h-30.42c-.9-11.28-3.66-20.71-8.28-28.31-4.62-7.59-11.8-13.41-21.55-17.46-9.75-4.05-22.63-6.07-38.66-6.07-12.54,0-23.1,1.65-31.68,4.95-8.58,3.3-15.02,8-19.3,14.08-4.29,6.09-6.44,13.25-6.44,21.47s1.69,15.68,5.08,21.46c3.39,5.79,9.19,10.74,17.42,14.85,8.22,4.11,19.59,7.64,34.11,10.57l26.37,5.4c19.68,3.96,35.42,9.24,47.21,15.84,11.79,6.6,20.25,14.57,25.38,23.89,5.13,9.33,7.7,20.39,7.7,33.17,0,15.36-3.75,28.68-11.25,39.96-7.5,11.28-18.51,20-33.03,26.15-14.52,6.15-31.98,9.22-52.38,9.22Z"/>
		</svg>
	</div>
</div>
<?php endif; ?>
<div class="page-transition"></div>
<div id="page" class="site"	data-barba="container" data-barba-namespace="<?php echo is_archive() ? get_post_type() :  get_page_uri() ?>">


	<header>

			<div class="header">

				<div class="burger">
					<span></span>
				</div>
				<div class="header__logo">
					<?php the_custom_logo(); ?>
				</div>
				<nav class="header__nav">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								// 'menu_id'        => 'primary-menu',
								'menu_class'=> 'header__ul',     
							)
						);
						?>
				</nav>
				<div class="language-selector">
					<div class="language-current">
						<span class="lang-code">ES</span>
						<svg class="lang-arrow" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none">
							<path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</div>
					<div class="language-dropdown">
						<a href="#" class="lang-option" data-lang="es">
							<span class="lang-code">ES</span>
							<span class="lang-name">Español</span>
						</a>
						<a href="#" class="lang-option" data-lang="en">
							<span class="lang-code">EN</span>
							<span class="lang-name">English</span>
						</a>
						<a href="#" class="lang-option" data-lang="ca">
							<span class="lang-code">CA</span>
							<span class="lang-name">Català</span>
						</a>
					</div>
				</div>
			</div>
			<div class="menu-mobile">
				<nav class="menu-mobile__nav">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								// 'menu_id'        => 'primary-menu',
								'menu_class'=> 'menu-mobile__ul',     
								'link_before' => '<span class="animation-text">',
								'link_after' => '</span>',
							)
						);
					?>
									<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-social',
							// 'menu_id'        => 'primary-menu',
							'container_class'=> 'social__li',     
							'link_before' => '<span class="animation-text">',
							'link_after' => '</span>',
						)
					);
				?>
				</nav>
			</div>
	</header>
	<script>
				// const burger = document.querySelector('.burger');
				// const header = document.querySelector('header');

				// burger.addEventListener('click', () => {
				// 	header.classList.toggle('active-menu');
				// });

			</script>


