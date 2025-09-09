/**
 * Módulo Core - Punto de entrada principal del theme Amentum
 * Gestiona la inicialización de todos los módulos
 * (Compilado a JavaScript vanilla para compatibilidad)
 */

function initTheme() {
	console.log('Amentum Theme - JavaScript cargado correctamente');
	
	// Debug de librerías disponibles
	debugLibraries();
	
	// Inicializar control de scroll
	initScrollRestoration();
	
	// Verificar si ya se vio la intro para ocultarla inmediatamente si es necesario
	const hasSeenIntro = sessionStorage.getItem('hasSeenIntro') === 'true';
	const introHome = document.querySelector('.intro-home');
	
	if (hasSeenIntro && introHome) {
		// Si ya se vio la intro, ocultarla inmediatamente sin transición
		introHome.style.display = 'none';
	}
	
	// Inicializar sistema de validación de formularios siempre
	initValidationSystem();
	
	// Solo inicializar Barba.js si el usuario NO está logueado
	if (typeof ajax_forms !== 'undefined' && ajax_forms.isUserLoggedIn === 'false') {
		barbaJsInit();
	} else {
		// Usuario logueado: inicializar todo directamente sin Barba
		initUserLoggedMode();
	}
}

function initUserLoggedMode() {
	initSmoothScroll();
	marquee();
	contentAnimation();
	
	// Inicializar Swipers de eventos para usuarios logueados
	callIfExists('inicializarEventosSwiper');
	
	// Inicializar imágenes de magazine (para usuarios logueados)
	// callIfExists('initMagazineImages');
	
	// Asegurar que la intro esté oculta para usuarios logueados
	const introHome = document.querySelector('.intro-home');
	if (introHome) {
		introHome.style.display = 'none';
	}
	
	// Reproducir videos
	autoPlayVideos();
}

// Inicialización automática cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initTheme);