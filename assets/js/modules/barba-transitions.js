/**
 * Configuración y manejo de transiciones con Barba.js
 * (Compilado a JavaScript vanilla para compatibilidad)
 */

function barbaJsInit() {
	barba.init({
		sync: true,
		debug: false,
		transitions: [
			{
				// Primera carga de la página
				once(data) {
					// Verificar si es primera visita
					const hasSeenIntro = sessionStorage.getItem('hasSeenIntro') === 'true';
					
					if (!hasSeenIntro) {
						// Primera visita: mostrar intro
						sessionStorage.setItem('hasSeenIntro', 'true');
						initWebAnimation();
						initSmoothScroll();
					} else {
						// Ya visitó: iniciar todo normal
						initSmoothScroll();
					}
					
					// Inicializar scripts básicos
					marquee();
					contentAnimation();
					
					// Inicializar Swipers de eventos en primera carga
					callIfExists('inicializarEventosSwiper');
					
					// Inicializar imágenes de magazine (para primera carga)
					// callIfExists('initMagazineImages');
					
					// Reproducir videos
					autoPlayVideos();
				},
				
				// Antes de salir de la página - animar cierre visual del menú
				beforeLeave(data) {
					const header = document.querySelector('header');
					const menuOverlay = document.querySelector('.menu-mobile');
					const menuUl = document.querySelector('.menu-mobile ul');
					
					if (header && header.classList.contains('active-menu')) {
						// X → hamburguesa (quitar active-menu del header)
						header.classList.remove('active-menu');
						
						// Mantener overlay visible durante transición
						if (menuOverlay) {
							menuOverlay.classList.add('transitioning');
						}
						
						// Letras → opacity 0 (transición suave)
						if (menuUl) {
							menuUl.style.opacity = '0';
						}
						
						// NO esperar - las transiciones de menú y página ocurren en paralelo
					}
				},
				
				// Salir de la página
				async leave(data) {
					console.log('🚪 Barba leave() ejecutándose');
					const done = this.async();
					
					// Mostrar transición
					await pageTransition();
					await delay(200);
					done();
				},
				
				// Antes de entrar a la nueva página
				beforeEnter(data) {
					// Scroll to top usando Lenis si está disponible
					if (window.lenis) {
						window.lenis.scrollTo(0, { immediate: true });
					} else {
						window.scrollTo(0, 0);
					}
				},
				
				// Entrar a la nueva página
				enter(data) {
					// Limpiar estado de transición del menú
					const menuOverlay = document.querySelector('.menu-mobile');
					const menuUl = document.querySelector('.menu-mobile ul');
					
					if (menuOverlay) {
						menuOverlay.classList.remove('transitioning');
					}
					if (menuUl) {
						menuUl.style.opacity = ''; // Resetear opacity inline
					}
					
					// Ocultar transición
					const transition = document.querySelector('.page-transition');
					if (transition) {
						transition.classList.remove('active');
					}
					
					// Pequeño delay para asegurar que el DOM esté listo
					setTimeout(() => {
						// Inicializar scripts
						marquee();
						contentAnimation();
						
						// Reinicializar Swipers de eventos después de navegación Barba.js
						callIfExists('inicializarEventosSwiper');
						
						// Inicializar imágenes de magazine (para navegación con Barba.js)
						// callIfExists('initMagazineImages');
						
						// Reproducir videos
						autoPlayVideos();
					}, 50); // Delay mínimo de 50ms
				}
			}
		]
	});
	
	// Hooks globales para manejo del scroll
	barba.hooks.enter(() => {
		// Scroll al top en cada navegación
		if (window.lenis) {
			window.lenis.scrollTo(0, { immediate: true });
		}
	});
}