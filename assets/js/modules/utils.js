/**
 * Utilidades y funciones auxiliares
 * (Compilado a JavaScript vanilla para compatibilidad)
 */

// Función para crear delays/promesas con timeout
function delay(n) {
	n = n || 2000;
	return new Promise((done) => {
		setTimeout(() => {
			done();
		}, n);
	});
}

// Verificar si una función existe antes de llamarla
function callIfExists(functionName, ...args) {
	if (typeof window[functionName] === 'function') {
		return window[functionName](...args);
	}
	return false;
}

// Verificar disponibilidad de librerías
function checkLibraryAvailability() {
	return {
		jQuery: !!(window.jQuery || window.$),
		gsap: !!window.gsap,
		ScrollTrigger: !!window.ScrollTrigger,
		SplitType: !!window.SplitType,
		barba: !!window.barba,
		Swiper: !!window.Swiper,
		Lenis: !!window.Lenis
	};
}

// Función para reproducir videos automáticamente
function autoPlayVideos() {
	const videos = document.querySelectorAll('video');
	if (videos.length) {
		videos.forEach((video) => {
			video.play();
		});
	}
}

// Debug de librerías disponibles
function debugLibraries() {
	setTimeout(() => {
		const libsStatus = checkLibraryAvailability();
		console.log('Estado de librerías cargadas:', libsStatus);
		
		const missing = [];
		for (const name in libsStatus) {
			if (!libsStatus[name]) {
				missing.push(name);
			}
		}
		
		if (missing.length > 0) {
			console.warn('Librerías no detectadas:', missing);
		} else {
			console.log('Todas las librerías cargadas correctamente');
		}
	}, 100);
}