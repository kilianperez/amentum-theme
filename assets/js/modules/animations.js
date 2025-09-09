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

function efectoLetrasHome() {
	if (document.querySelectorAll('.animation-intro-2').length || document.querySelectorAll('.animation-intro-3').length) {
		// Procesar texto con SplitType incluyendo words y chars
		const textos2 = new SplitType('.animation-intro-2', { types: 'words, chars' });
		const textos3 = new SplitType('.animation-intro-3', { types: 'words, chars' });

		// Verificar si estamos en la intro o en transición
		const introHome = document.querySelector('.intro-home');
		const isIntro = introHome && introHome.style.display !== 'none' && window.getComputedStyle(introHome).display !== 'none';
		
		// Delay diferente según contexto
		const delay = isIntro ? 2.5 : 0.1; // 2.5s en intro, 0.1s en transiciones
		
		// Crear timeline para animación secuencial
		const tl = gsap.timeline({ delay: delay });

		// Hacer visibles las palabras primero, luego animar las letras
		tl.set(['.animation-intro-2 .word', '.animation-intro-3 .word'], { opacity: 1 })
		  .to('.animation-intro-2 .char', {
			y: 0,
			stagger: 0.05,
			duration: 0.1,
			ease: 'power2.out'
		  })
		  .to('.animation-intro-3 .char', {
			y: 0,
			stagger: 0.05,
			duration: 0.1,
			ease: 'power2.out'
		  }, "-=0.1"); // Empezar ligeramente antes de que termine la primera línea
	}
}

function initWebAnimation() {
	const introHome = document.querySelector('.intro-home');
	if (introHome) {
		// La intro ya está visible por defecto en CSS, solo agregar animación
		
		// Después de un momento, agregar la clase load para la animación
		setTimeout(() => {
			introHome.classList.add('load');
			efectoLetrasHome();
			
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