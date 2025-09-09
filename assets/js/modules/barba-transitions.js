/**
 * Configuración y manejo de transiciones con Barba.js
 * (Compilado a JavaScript vanilla para compatibilidad)
 */

/**
 * Inicializa todos los módulos de página después de navegación
 * Centraliza la lógica que se ejecuta tanto en once() como en enter()
 */
function initPageModules() {
	// Inicializar scripts básicos
	contentAnimation();
	
	// Inicializar Swipers de eventos
	callIfExists('inicializarEventosSwiper');
	
	// Reproducir videos
	autoPlayVideos();
}

/**
 * Cleanup simple y efectivo para prevenir memory leaks
 * Basado en patrones de proyectos reales WordPress + Barba.js
 */
function cleanupPageModules() {
	// Swiper cleanup
	if (window.swiperInstances) {
		window.swiperInstances.forEach(swiper => {
			try { swiper.destroy(); } catch(e) {}
		});
		window.swiperInstances = [];
	}
	
	// Video cleanup
	document.querySelectorAll('video').forEach(video => {
		try { 
			video.pause(); 
			video.currentTime = 0; 
		} catch(e) {}
	});
}

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
					
					// Inicializar todos los módulos de página
					initPageModules();
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
					
					// ✅ CLEANUP: Ahora se maneja en hooks globales (más simple)
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
					
					// ⏱️ DELAY NECESARIO: Asegurar que el DOM esté completamente listo
					// Barba.js swapea el contenido del container, pero algunos elementos
					// pueden necesitar un frame adicional para estar disponibles en selectores
					// 50ms garantiza que jQuery/vanilla selectors encuentren elementos correctamente
					setTimeout(() => {
						// Reinicializar todos los módulos de página
						initPageModules();
					}, 50);
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
	
	// ✅ HOOKS GLOBALES SIMPLES: Patrón de proyectos reales
	barba.hooks.before(() => {
		cleanupPageModules();
	});

	barba.hooks.after(() => {
		initPageModules();
	});
	
	// ✅ WORDPRESS BODY CLASS UPDATE: Esencial para themes WordPress
	barba.hooks.after((data) => {
		const bodyMatch = data.next.html.match(/<body[^>]*class="([^"]*)"[^>]*>/);
		if (bodyMatch && bodyMatch[1]) {
			document.body.className = bodyMatch[1];
		}
	});
}