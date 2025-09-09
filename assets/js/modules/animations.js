/**
 * Animaciones GSAP y efectos visuales
 * (Compilado a JavaScript vanilla para compatibilidad)
 */

function pageTransition() {
	return new Promise((resolve) => {
		const transition = document.querySelector('.page-transition');
		
		if (transition) {
			// Mostrar el fundido blanco
			transition.classList.add('active');
			
			// Esperar a que la transición termine y resolver la promesa
			setTimeout(() => {
				resolve();
			}, 700); // Esperar un poco más que la duración de la transición CSS (0.6s)
		} else {
			resolve();
		}
	});
}

function initWebAnimation() {
	const introHome = document.querySelector('.intro-home');
	if (introHome) {
		// La intro ya está visible por defecto en CSS, solo agregar animación
		console.log('🎬 Iniciando animación de intro v2');
		
		// Después de un momento, agregar la clase load para la animación
		setTimeout(() => {
			introHome.classList.add('load');
			
			// Esperar a que termine la transición CSS (opacity 1s + delay 2s = 3s)
			setTimeout(() => {
				introHome.style.display = 'none';
			}, 3500); // 3.5 segundos para que termine toda la transición
		}, 100);
	}

	// Limpiar sessionStorage solo en recarga de página
	window.addEventListener('beforeunload', function () {
		sessionStorage.removeItem('hasSeenIntro');
	});
}