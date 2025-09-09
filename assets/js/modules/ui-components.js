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


// Agregar otras funciones de UI aquí según sea necesario
export function contentAnimation() {
	menu();
}