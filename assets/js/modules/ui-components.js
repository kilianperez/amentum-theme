/**
 * Componentes de UI: Menu, marquee y otros elementos interactivos
 */

export function menu() {
	const burger = document.querySelector('.burger');
	const header = document.querySelector('header');

	if (burger && header) {
		burger.addEventListener('click', () => {
			header.classList.toggle('active-menu');
		});
	}
}

export function marquee() {
	const marquees = document.querySelectorAll('div.marquee');
	let currentScrollSpeedMultiplier = 1; // Velocidad actual (interpolada)
	let scrollDirection = 1; // 1 = hacia abajo, -1 = hacia arriba
	let lastScrollY = window.scrollY;
	let lastScrollTime = performance.now();
	let scrollVelocity = 0;
	
	// Sistema de ease-out progresivo como Lenis (más suave)
	const lenisEasing = 0.05; // Interpolación más lenta para suavidad
	const maxMultiplier = 15; // Multiplicador reducido para menos brusquedad
	const velocityThreshold = 0.3; // Menos sensible para evitar cambios bruscos

	// Detectar scroll y calcular velocidad y dirección
	window.addEventListener('scroll', () => {
		const currentScrollY = window.scrollY;
		const currentTime = performance.now();
		const timeDelta = currentTime - lastScrollTime;
		const scrollDelta = Math.abs(currentScrollY - lastScrollY);
		
		// Detectar dirección del scroll
		if (currentScrollY > lastScrollY) {
			scrollDirection = 1; // Scroll hacia abajo
		} else if (currentScrollY < lastScrollY) {
			scrollDirection = -1; // Scroll hacia arriba
		}
		
		// Calcular velocidad de scroll (píxeles por ms)
		if (timeDelta > 0) {
			scrollVelocity = scrollDelta / timeDelta;
		}
		
		lastScrollY = currentScrollY;
		lastScrollTime = currentTime;
	});

	marquees.forEach(function (element) {
		let tick = 1;
		let value = element.dataset.speed;
		element.innerHTML += element.innerHTML;
		element.innerHTML += element.innerHTML;

		const innerTags = element.querySelectorAll('div.inner');
		innerTags.forEach((inner, index) => {
			inner.style.left = inner.offsetWidth * index + 'px';
		});

		const ticker = function () {
			// Calcular objetivo basado en velocidad actual de scroll (más suave)
			const targetMultiplier = scrollVelocity > velocityThreshold ? 
				1 + (scrollVelocity * 1.5) : 1; // Reducido a 1.5 para transiciones más suaves
			
			// Limitar el máximo
			const clampedTarget = Math.min(targetMultiplier, maxMultiplier);
			
			// Interpolación continua hacia el objetivo calculado
			currentScrollSpeedMultiplier += (clampedTarget - currentScrollSpeedMultiplier) * lenisEasing;
			
			// Decay natural más progresivo (como Lenis)
			scrollVelocity *= 0.92;
			
			tick += parseInt(value) * 0.2 * currentScrollSpeedMultiplier * scrollDirection; // Aplicar dirección y velocidad
			//element.innerHTML = tick;
			//element.style.left = tick + "px";
			innerTags.forEach((inner, index) => {
				let width = inner.offsetWidth;
				let normalizedMarqueeX = ((tick % width) + width) % width;
				let pos = width * (index - 1) + normalizedMarqueeX;

				inner.style.left = pos + 'px';
			});
			requestAnimationFrame(ticker);
		};
		ticker();
	});
}

// Agregar otras funciones de UI aquí según sea necesario
export function contentAnimation() {
	menu();
}