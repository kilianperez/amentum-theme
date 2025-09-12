/**
 * Componentes de UI: Menu y otros elementos interactivos
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

export function languageSelector() {
	const selector = document.querySelector('.language-selector');
	const current = document.querySelector('.language-current');
	const dropdown = document.querySelector('.language-dropdown');
	const options = document.querySelectorAll('.lang-option');

	if (selector && current && dropdown) {
		// Toggle dropdown al hacer click
		current.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			selector.classList.toggle('active');
		});

		// Cerrar dropdown al hacer click fuera
		document.addEventListener('click', (e) => {
			if (!selector.contains(e.target)) {
				selector.classList.remove('active');
			}
		});

		// Seleccionar idioma
		options.forEach(option => {
			option.addEventListener('click', (e) => {
				e.preventDefault();
				const langCode = option.getAttribute('data-lang');
				const langText = option.querySelector('.lang-code').textContent;
				
				// Actualizar idioma actual
				document.querySelector('.language-current .lang-code').textContent = langText;
				
				// Cerrar dropdown
				selector.classList.remove('active');
				
				// Aquí puedes agregar la lógica para cambiar realmente el idioma
				console.log('Idioma seleccionado:', langCode);
				
				// Ejemplo: redireccionar a URL con idioma
				// window.location.href = `/${langCode}/`;
			});
		});

		// Cerrar dropdown al presionar Escape
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				selector.classList.remove('active');
			}
		});
	}
}

// Agregar otras funciones de UI aquí según sea necesario
export function contentAnimation() {
	menu();
	languageSelector();
}