/**
 * Sistema de scroll suave con Lenis - Implementación optimizada
 * (Compilado a JavaScript vanilla para compatibilidad)
 */

let lenis;

function initSmoothScroll() {
	if (typeof Lenis !== 'undefined') {
		// Solo inicializar si no existe ya
		if (window.lenis) {
			window.lenis.destroy();
		}
		
		// Inicializar Lenis con configuración optimizada
		lenis = new Lenis({
			duration: 1.2,
			easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
			orientation: 'vertical',
			gestureOrientation: 'vertical',
			smoothWheel: true,
			wheelMultiplier: 1,
			touchMultiplier: 2,
			infinite: false,
			autoResize: true
		});

		// RAF loop optimizado
		function raf(time) {
			if (lenis) {
				lenis.raf(time);
				requestAnimationFrame(raf);
			}
		}
		requestAnimationFrame(raf);

		// Hacer Lenis disponible globalmente
		window.lenis = lenis;

		console.log('✅ Lenis smooth scroll initialized');
	} else {
		console.warn('⚠️ Lenis library not found');
	}
}

// Mantener control manual del scroll
function initScrollRestoration() {
	if ('scrollRestoration' in history) {
		history.scrollRestoration = 'manual';
	}
}

// Obtener la instancia de Lenis
function getLenis() {
	return lenis;
}