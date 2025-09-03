// Variables globales
let lenis;

// Mantener control manual del scroll
if ('scrollRestoration' in history) {
	history.scrollRestoration = 'manual';
}

document.addEventListener('DOMContentLoaded', function () {
	// Verificar si ya se vio la intro para ocultarla inmediatamente si es necesario
	const hasSeenIntro = sessionStorage.getItem('hasSeenIntro') === 'true';
	const introHome = document.querySelector('.intro-home');
	
	if (hasSeenIntro && introHome) {
		// Si ya se vio la intro, ocultarla inmediatamente sin transición
		introHome.style.display = 'none';
	}
	
	// Cursor eliminado para simplificar el tema
	
	// Solo inicializar Barba.js si el usuario NO está logueado
	if (typeof ajax_forms !== 'undefined' && ajax_forms.isUserLoggedIn === 'false') {
		barbaJsInit();
	} else {
		// Usuario logueado: inicializar todo directamente sin Barba
		initSmoothScroll();
		marquee();
		contentAnimation();
		
		// Inicializar imágenes de magazine (para usuarios logueados)
		if (typeof initMagazineImages === 'function') {
			initMagazineImages();
		}
		
		// Asegurar que la intro esté oculta para usuarios logueados
		if (introHome) {
			introHome.style.display = 'none';
		}
		
		// Reproducir videos
		if (document.querySelectorAll('video').length) {
			document.querySelectorAll('video').forEach((video) => {
				video.play();
			});
		}
	}
});

function delay(n) {
	n = n || 2000;
	return new Promise((done) => {
		setTimeout(() => {
			done();
		}, n);
	});
}

function contentAnimation() {
	scrollElementos();
	menu();
	formulariosAjax();
	inputFilledStates();
	initCustomValidation();
	
	// Cursor eliminado para simplificar
	
	if (document.querySelectorAll('video').length) {
		document.querySelectorAll('video').forEach((video) => {
			video.play();
		});
	}
}

/* ------------------ GSAP ------------------ */

function sectionProyecto() {
	if (document.querySelectorAll('.prod-imgs__img.effect').length) {
		var lazyVideos = [].slice.call(document.querySelectorAll('video.lazy'));

		if ('IntersectionObserver' in window) {
			var lazyVideoObserver = new IntersectionObserver(function (entries, observer) {
				entries.forEach(function (video) {
					if (video.isIntersecting) {
						for (var source in video.target.children) {
							var videoSource = video.target.children[source];
							if (typeof videoSource.tagName === 'string' && videoSource.tagName === 'SOURCE') {
								videoSource.src = videoSource.dataset.src;
							}
						}

						video.target.load();
						video.target.classList.remove('lazy');
						lazyVideoObserver.unobserve(video.target);
					}
				});
			});

			lazyVideos.forEach(function (lazyVideo) {
				lazyVideoObserver.observe(lazyVideo);
			});
		}

		gsap.utils.toArray('.prod-imgs__img.effect').forEach((section) => {
			ScrollTrigger.create({
				trigger: section,
				start: 'top top',
				pin: true,
				pinSpacing: false,
				markers: false,
				onEnter: () => {
					// if (section.querySelectorAll('video').length) {
					// 	section.querySelectorAll('video').forEach((video) => {
					// 		video.play();
					// 	});
					// }
				},
				onEnterBack: () => {
					// if (section.querySelectorAll('video').length) {
					// 	section.querySelectorAll('video').forEach((video) => {
					// 		video.play();
					// 	});
					// }
				},
				onLeave: () => {
					// if (section.querySelectorAll('video').length) {
					// 	section.querySelectorAll('video').forEach((video) => {
					// 		video.play();
					// 	});
					// }
				},
				onLeaveBack: () => {
					// if (section.querySelectorAll('video').length) {
					// 	section
					// 		.querySelectorAll('video')
					// 		.forEach((video) => {
					// 			video.pause();
					// 		});
					// }
				},
			});
		});
	}
}

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

function scrollElementos() {
	if (document.querySelectorAll('.animation-scroll').length) {
		gsap.registerPlugin(ScrollTrigger);

		// Usar la misma técnica: SplitType con words y chars, pero excluir contenedores con enlaces
		const animationElements = document.querySelectorAll('.animation-scroll');
		const textElements = Array.from(animationElements).filter(el => !el.querySelector('.link'));
		
		let textos3 = new SplitType(textElements, { 
			types: 'words, chars',
			// Configuración para prevenir saltos
			lineClass: 'line',
			wordClass: 'word', 
			charClass: 'char',
			preserveWhitespace: true
		});

		let tl = gsap.timeline({
			scrollTrigger: {
				trigger: '.section',
				start: 'top center',
				toggleClass: 'animado',
				toggleActions: 'play none none none',
			},
		});

		// Hacer visibles tanto las palabras como los elementos directos
		tl.set('.animation-scroll', { opacity: 1 }) // Para elementos sin words
		  .set('.animation-scroll .word', { opacity: 1 }) // Para palabras
		  .to('.animation-scroll .char', {
			y: 0,
			stagger: 0.05,
			delay: 0,
			duration: 0.1,
		})
		  // Animar enlaces por separado con el mismo timing que las letras
		  .to('.animation-scroll .link.char', {
			y: 0,
			delay: 0.1, // Pequeño delay adicional para que aparezca después del texto
			duration: 0.1, // Misma duración que las letras
			// Sin ease definido - usar el por defecto como las letras
		}, "-=0.05"); // Ajustado para sincronizar mejor
	}
}

function menu() {
	const burger = document.querySelector('.burger');
	const header = document.querySelector('header');

	burger.addEventListener('click', () => {
		header.classList.toggle('active-menu');
	});
}

/* ------------------ VANILLA ANIMACIONES ------------------ */
function marquee() {
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

/*-------------- Validaciones Personalizadas ------------------- */

function initCustomValidation() {
	console.log('🔧 Inicializando sistema de validación personalizado');
	
	// Agregar patrón estricto a todos los campos de email
	const emailInputs = document.querySelectorAll('input[type="email"]');
	emailInputs.forEach(input => {
		if (!input.hasAttribute('pattern')) {
			input.setAttribute('pattern', '[a-zA-Z0-9._%\\+\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}');
		}
	});
	
	// Crear sistema de notificaciones globales
	initGlobalNotifications();
	
	// Función para obtener mensaje de error específico
	function getErrorMessage(field) {
		// Para checkboxes de grupo (tags), siempre devolver mensaje específico
		if (field.type === 'checkbox' && field.hasAttribute('data-option') && field.getAttribute('data-option') === 'tags') {
			return 'Selecciona al menos una opción';
		}
		
		if (field.validity.valueMissing) {
			if (field.type === 'email') {
				return 'Por favor, introduce tu email';
			} else if (field.type === 'checkbox') {
				return 'Debes aceptar los términos para continuar';
			} else {
				return `Por favor, completa el campo "${field.placeholder || field.name || 'requerido'}"`;
			}
		} else if (field.validity.typeMismatch || field.validity.patternMismatch) {
			if (field.type === 'email') {
				return 'Introduce un email válido';
			} else {
				return 'El formato no es válido';
			}
		} else if (field.validity.tooShort) {
			return `El texto es demasiado corto (mínimo ${field.minLength} caracteres)`;
		}
		return 'Este campo no es válido';
	}
	
	// Función para validar grupo de checkboxes (tags)
	function validateCheckboxGroup(container) {
		const checkboxes = container.querySelectorAll('input[type="checkbox"][data-option="tags"]');
		
		// Verificar si algún checkbox del grupo es requerido
		const hasRequiredCheckbox = Array.from(checkboxes).some(cb => cb.hasAttribute('data-required') && cb.getAttribute('data-required') === 'required');
		
		// Solo validar si hay al menos un checkbox requerido en el grupo
		if (!hasRequiredCheckbox) {
			// Limpiar validación si no es requerido
			checkboxes.forEach(cb => cb.setCustomValidity(''));
			return null;
		}
		
		const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);
		
		if (!isAnyChecked) {
			// Marcar como inválido el primer checkbox del grupo para mostrar el error
			const firstCheckbox = checkboxes[0];
			if (firstCheckbox) {
				firstCheckbox.setCustomValidity('Selecciona al menos una opción');
				return firstCheckbox;
			}
		} else {
			// Limpiar validación personalizada de todos los checkboxes del grupo
			checkboxes.forEach(cb => cb.setCustomValidity(''));
		}
		return null;
	}
	
	// Función para mostrar mensaje de error en un campo específico
	function showFieldError(field, message) {
		// Buscar wrapper (input-wrapper, check o checkboxs para grupos)
		let wrapper = field.closest('.input-wrapper') || field.closest('.check');
		
		// Para checkboxes de grupo (tags), usar el .field-wrapper.checkboxs
		if (!wrapper && field.hasAttribute('data-option') && field.getAttribute('data-option') === 'tags') {
			wrapper = field.closest('.field-wrapper.checkboxs');
		}
		
		if (!wrapper) return;
		
		// Buscar mensaje existente o crear uno nuevo
		let errorElement = wrapper.querySelector('.field-error-message');
		if (!errorElement) {
			errorElement = document.createElement('div');
			errorElement.className = 'field-error-message';
			// Insertar el mensaje al final del wrapper para que aparezca debajo de todo
			wrapper.appendChild(errorElement);
		}
		
		errorElement.textContent = message;
		wrapper.classList.add('input-error');
	}
	
	// Función para limpiar error de un campo específico
	function clearFieldError(field) {
		// Buscar wrapper (input-wrapper, check o checkboxs para grupos)
		let wrapper = field.closest('.input-wrapper') || field.closest('.check');
		
		// Para checkboxes de grupo (tags), usar el .field-wrapper.checkboxs
		if (!wrapper && field.hasAttribute('data-option') && field.getAttribute('data-option') === 'tags') {
			wrapper = field.closest('.field-wrapper.checkboxs');
		}
		
		if (!wrapper) return;
		
		wrapper.classList.remove('input-error');
		const errorElement = wrapper.querySelector('.field-error-message');
		if (errorElement) {
			errorElement.textContent = '';
		}
	}
	
	// Interceptar envíos de formularios para validación personalizada
	// EXCLUIR formularios que tienen lógica AJAX específica (newsletter y contact)
	const forms = document.querySelectorAll('form:not([data-form="newsletter"]):not([data-form="contact"])');
	console.log(`📝 Encontrados ${forms.length} formularios para validación estándar`);
	
	forms.forEach(form => {
		form.addEventListener('submit', function(e) {
			// Limpiar errores previos
			clearAllFieldErrors(form);
			
			// Validar grupos de checkboxes antes de la validación HTML5
			const checkboxContainers = form.querySelectorAll('.field-wrapper.checkboxs');
			let hasCheckboxErrors = false;
			
			checkboxContainers.forEach(container => {
				const invalidCheckbox = validateCheckboxGroup(container);
				if (invalidCheckbox) {
					hasCheckboxErrors = true;
					const message = getErrorMessage(invalidCheckbox);
					showFieldError(invalidCheckbox, message);
				}
			});
			
			// Verificar validez del formulario HTML5
			const isFormValid = form.checkValidity();
			
			if (!isFormValid || hasCheckboxErrors) {
				e.preventDefault();
				
				// Mostrar mensajes de error individuales para cada campo inválido
				const invalidFields = form.querySelectorAll(':invalid');
				invalidFields.forEach(field => {
					// Solo mostrar error si no es un checkbox de grupo (ya procesado arriba)
					if (!(field.hasAttribute('data-option') && field.getAttribute('data-option') === 'tags')) {
						const message = getErrorMessage(field);
						showFieldError(field, message);
					}
				});
				
				// Hacer focus en el primer campo inválido
				const firstInvalidField = form.querySelector(':invalid') || form.querySelector('.field-wrapper.checkboxs.input-error input[type="checkbox"]');
				if (firstInvalidField) {
					firstInvalidField.focus();
				}
			} else {
				// Si el formulario es válido, limpiar cualquier error previo
				clearAllFieldErrors(form);
			}
		});
		
		// Validación en tiempo real cuando el usuario escribe
		const inputs = form.querySelectorAll('input, textarea');
		inputs.forEach(input => {
			// Limpiar errores cuando el usuario empiece a escribir
			input.addEventListener('input', function() {
				if (input.checkValidity()) {
					clearFieldError(input);
				} else {
					// Mostrar error en tiempo real solo si ya había un error previo
					const wrapper = input.closest('.input-wrapper');
					if (wrapper && wrapper.classList.contains('input-error')) {
						const message = getErrorMessage(input);
						showFieldError(input, message);
					}
				}
			});
			
			// Validar al salir del campo (blur)
			input.addEventListener('blur', function() {
				if (input.value.trim() !== '' && !input.checkValidity()) {
					const message = getErrorMessage(input);
					showFieldError(input, message);
				}
			});
		});
		
		// Validación en tiempo real para grupos de checkboxes
		const checkboxGroups = form.querySelectorAll('input[type="checkbox"][data-option="tags"]');
		checkboxGroups.forEach(checkbox => {
			checkbox.addEventListener('change', function() {
				const container = checkbox.closest('.field-wrapper.checkboxs');
				if (container) {
					// Validar el grupo completo cuando cambie cualquier checkbox
					const invalidCheckbox = validateCheckboxGroup(container);
					if (invalidCheckbox) {
						// Mantener el error si todavía no hay ninguno seleccionado
						const message = getErrorMessage(invalidCheckbox);
						showFieldError(invalidCheckbox, message);
					} else {
						// Limpiar el error cuando se seleccione al menos uno
						const firstCheckbox = container.querySelector('input[type="checkbox"][data-option="tags"]');
						if (firstCheckbox) {
							clearFieldError(firstCheckbox);
						}
					}
				}
			});
		});
		
		// Deshabilitar validación HTML nativa
		form.setAttribute('novalidate', 'novalidate');
	});
	
	// Agregar validación personalizada COMPLETA a formularios AJAX 
	const ajaxForms = document.querySelectorAll('form[data-form="newsletter"], form[data-form="contact"]');
	console.log(`📧 Encontrados ${ajaxForms.length} formularios AJAX para validación personalizada`);
	
	ajaxForms.forEach(form => {
		// INTERCEPTAR ENVÍO para validación personalizada
		form.addEventListener('submit', function(e) {
			// Limpiar errores previos
			clearAllFieldErrors(form);
			
			// Validar grupos de checkboxes antes de la validación HTML5
			const checkboxContainers = form.querySelectorAll('.field-wrapper.checkboxs');
			let hasCheckboxErrors = false;
			
			checkboxContainers.forEach(container => {
				const invalidCheckbox = validateCheckboxGroup(container);
				if (invalidCheckbox) {
					hasCheckboxErrors = true;
					const message = getErrorMessage(invalidCheckbox);
					showFieldError(invalidCheckbox, message);
				}
			});
			
			// Verificar validez del formulario HTML5
			const isFormValid = form.checkValidity();
			
			if (!isFormValid || hasCheckboxErrors) {
				e.preventDefault();
				e.stopPropagation();
				
				// Mostrar mensajes de error individuales para cada campo inválido
				const invalidFields = form.querySelectorAll(':invalid');
				invalidFields.forEach(field => {
					// Solo mostrar error si no es un checkbox de grupo (ya procesado arriba)
					if (!(field.hasAttribute('data-option') && field.getAttribute('data-option') === 'tags')) {
						const message = getErrorMessage(field);
						showFieldError(field, message);
					}
				});
				
				// Hacer focus en el primer campo inválido
				const firstInvalidField = form.querySelector(':invalid') || form.querySelector('.field-wrapper.checkboxs.input-error input[type="checkbox"]');
				if (firstInvalidField) {
					firstInvalidField.focus();
				}
				
				return false; // Evitar que se envíe
			}
			// Si el formulario es válido, permitir que el AJAX lo maneje
		});
		
		// Validación en tiempo real
		const inputs = form.querySelectorAll('input, textarea');
		inputs.forEach(input => {
			// Validación en tiempo real cuando el usuario escribe
			input.addEventListener('input', function() {
				if (input.checkValidity()) {
					clearFieldError(input);
				} else {
					// Mostrar error en tiempo real solo si ya había un error previo
					const wrapper = input.closest('.input-wrapper');
					if (wrapper && wrapper.classList.contains('input-error')) {
						const message = getErrorMessage(input);
						showFieldError(input, message);
					}
				}
			});
			
			// Validar al salir del campo (blur)
			input.addEventListener('blur', function() {
				if (input.value.trim() !== '' && !input.checkValidity()) {
					const message = getErrorMessage(input);
					showFieldError(input, message);
				}
			});
		});
		
		// Validación en tiempo real para grupos de checkboxes
		const checkboxGroups = form.querySelectorAll('input[type="checkbox"][data-option="tags"]');
		checkboxGroups.forEach(checkbox => {
			checkbox.addEventListener('change', function() {
				const container = checkbox.closest('.field-wrapper.checkboxs');
				if (container) {
					// Validar el grupo completo cuando cambie cualquier checkbox
					const invalidCheckbox = validateCheckboxGroup(container);
					if (invalidCheckbox) {
						// Mantener el error si todavía no hay ninguno seleccionado
						const message = getErrorMessage(invalidCheckbox);
						showFieldError(invalidCheckbox, message);
					} else {
						// Limpiar el error cuando se seleccione al menos uno
						const firstCheckbox = container.querySelector('input[type="checkbox"][data-option="tags"]');
						if (firstCheckbox) {
							clearFieldError(firstCheckbox);
						}
					}
				}
			});
		});
		
		// Deshabilitar validación HTML nativa
		form.setAttribute('novalidate', 'novalidate');
	});
	
	// Función para limpiar todos los errores de un formulario
	function clearAllFieldErrors(form) {
		const inputs = form.querySelectorAll('input, textarea');
		inputs.forEach(input => {
			clearFieldError(input);
		});
	}
}

/*-------------- Estados de Inputs ------------------- */

function inputFilledStates() {
	const inputWrappers = document.querySelectorAll('.input-wrapper');
	
	inputWrappers.forEach(wrapper => {
		const input = wrapper.querySelector('input, textarea');
		if (!input) return;
		
		// Función para verificar si el input está lleno
		const checkFilled = () => {
			if (input.value.trim() !== '') {
				wrapper.classList.add('input-filled');
			} else {
				wrapper.classList.remove('input-filled');
			}
		};
		
		// Verificar estado inicial
		checkFilled();
		
		// Event listeners para cambios
		input.addEventListener('input', checkFilled);
		input.addEventListener('change', checkFilled);
		input.addEventListener('blur', checkFilled);
	});
}

/*-------------- Formularios ------------------- */

function formulariosAjax() {
	const btnNews = document.querySelector('.newsletter-link'),
		formsContainer = document.querySelector('.newsletter'),
		formsExit = document.querySelector('.newsletter__exit'),
		formNews = document.querySelector('.newsletter #frm-contact');

	// Verificar que los elementos existen antes de agregar event listeners
	if (!btnNews || !formsContainer || !formsExit || !formNews) {
		console.warn('Newsletter elements not found:', {
			btnNews: !!btnNews,
			formsContainer: !!formsContainer,
			formsExit: !!formsExit,
			formNews: !!formNews
		});
		return;
	}

	// Cuando hago click en la x de la newsletter
	formsExit.addEventListener('click', () => {
		formsContainer.classList.remove('ver');
	});
	// Cuando hago click en el enlace del footer
	btnNews.addEventListener('click', () => {
		formsContainer.classList.toggle('ver');
	});
	// REMOVIDO: El newsletter ya no se cierra automáticamente con scroll
	// para mejorar la UX del usuario
	// Cuando hago click fuera del elemento elimino la clase ver
	formsContainer.addEventListener('click', function (event) {
		if (!formNews.contains(event.target)) {
			formsContainer.classList.remove('ver');
		}
	});

	$('#frm-contact').on('submit', function (e) {
		e.preventDefault();

		// VALIDAR FORMULARIO ANTES DE ENVIAR AJAX
		const form = this;
		const isFormValid = form.checkValidity();
		
		if (!isFormValid) {
			console.log('❌ Newsletter form validation failed');
			// Mostrar errores de validación - SIMPLIFICADO
			const invalidFields = form.querySelectorAll(':invalid');
			invalidFields.forEach(field => {
				let message = 'Este campo es requerido';
				if (field.type === 'email') {
					message = field.validity.valueMissing ? 'Por favor, introduce tu email' : 'Introduce un email válido';
				} else if (field.type === 'checkbox') {
					message = 'Debes aceptar los términos para continuar';
				}
				
				// Mostrar error simplificado
				let wrapper = field.closest('.input-wrapper') || field.closest('.check');
				if (wrapper) {
					wrapper.classList.add('input-error');
					let errorElement = wrapper.querySelector('.field-error-message');
					if (!errorElement) {
						errorElement = document.createElement('div');
						errorElement.className = 'field-error-message';
						wrapper.appendChild(errorElement);
					}
					errorElement.textContent = message;
				}
			});
			// Hacer focus en el primer campo inválido
			const firstInvalidField = form.querySelector(':invalid');
			if (firstInvalidField) {
				firstInvalidField.focus();
			}
			return false; // NO enviar AJAX si hay errores
		}
		
		console.log('✅ Newsletter form validation passed');
		var formData = $(this).serializeArray();
		console.log('📧 Newsletter form data:', formData);
		formData.push({name: 'action', value: 'amentum_ajax_frm_contact'});
		formData.push({name: 'nonce', value: ajax_forms.frmNonce});
		$.ajax({
			url: ajax_forms.ajaxUrl,
			type: 'post',
			dataType: 'json',
			data: formData,
			beforeSend: function () {
				$('#frm-contact .newsletter__svgs ').removeClass(['init', 'success']).addClass(['load']);
				$('#frm-contact .frm-message').removeClass('error').text(''); // Limpiar mensaje previo
				$('#frm-contact #submit').prop('disabled', true);
			},
		})
			.done(function (res) {
				console.log('📧 Newsletter response:', res);
				if (res.status === 1) {
					$('#frm-contact .newsletter__svgs ').removeClass(['init', 'load']).addClass(['success']);
					$('#frm-contact .frm-message').removeClass('error').text(''); // No mostrar mensaje en newsletter success
					$('#frm-contact')[0].reset();
				} else {
					$('#frm-contact .newsletter__svgs ').removeClass(['init', 'load']).addClass(['error']);
					// NO mostrar NINGÚN error en .frm-message del newsletter
					// Todos los errores del newsletter (validación y wp_mail) se manejan externamente
					$('#frm-contact .frm-message').removeClass('error').text('');
					console.log('📧 Newsletter error, but NOT showing in .frm-message:', res.message);
				}
			})
			.always(function () {
				$('#frm-contact #submit').prop('disabled', false);
			});
	});

	$('#formulario-contacto').on('submit', function (e) {
		e.preventDefault();

		// VALIDAR FORMULARIO ANTES DE ENVIAR AJAX
		const form = this;
		
		// Limpiar errores previos - SIMPLIFICADO para evitar problemas de scope
		const inputs = form.querySelectorAll('input, textarea');
		inputs.forEach(input => {
			// Limpiar validación personalizada
			input.setCustomValidity('');
			// Limpiar clases de error
			let wrapper = input.closest('.input-wrapper') || input.closest('.check') || input.closest('.field-wrapper.checkboxs');
			if (wrapper) {
				wrapper.classList.remove('input-error');
				const errorMsg = wrapper.querySelector('.field-error-message');
				if (errorMsg) errorMsg.remove();
			}
		});
		
		// Validar grupos de checkboxes - SIMPLIFICADO
		const checkboxContainers = form.querySelectorAll('.field-wrapper.checkboxs');
		let hasCheckboxErrors = false;
		
		checkboxContainers.forEach(container => {
			const checkboxes = container.querySelectorAll('input[type="checkbox"][data-option="tags"]');
			
			// Verificar si algún checkbox del grupo es requerido
			const hasRequiredCheckbox = Array.from(checkboxes).some(cb => cb.hasAttribute('data-required') && cb.getAttribute('data-required') === 'required');
			
			// Solo validar si hay al menos un checkbox requerido en el grupo
			if (!hasRequiredCheckbox) {
				return; // Saltar validación si no es requerido
			}
			
			const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);
			
			if (!isAnyChecked) {
				hasCheckboxErrors = true;
				const firstCheckbox = checkboxes[0];
				if (firstCheckbox) {
					firstCheckbox.setCustomValidity('Selecciona al menos una opción');
					// Mostrar error en el container
					container.classList.add('input-error');
					let errorElement = container.querySelector('.field-error-message');
					if (!errorElement) {
						errorElement = document.createElement('div');
						errorElement.className = 'field-error-message';
						container.appendChild(errorElement);
					}
					errorElement.textContent = 'Selecciona al menos una opción';
				}
			}
		});
		
		// Verificar validez del formulario HTML5
		const isFormValid = form.checkValidity();
		
		if (!isFormValid || hasCheckboxErrors) {
			console.log('❌ Contact form validation failed');
			// Mostrar mensajes de error individuales para cada campo inválido - SIMPLIFICADO
			const invalidFields = form.querySelectorAll(':invalid');
			invalidFields.forEach(field => {
				// Solo mostrar error si no es un checkbox de grupo (ya procesado arriba)
				if (!(field.hasAttribute('data-option') && field.getAttribute('data-option') === 'tags')) {
					// Mensaje de error simplificado
					let message = 'Este campo es requerido';
					if (field.type === 'email') {
						message = field.validity.valueMissing ? 'Por favor, introduce tu email' : 'Introduce un email válido';
					} else if (field.type === 'checkbox') {
						message = 'Debes aceptar los términos para continuar';
					}
					
					// Mostrar error simplificado
					let wrapper = field.closest('.input-wrapper') || field.closest('.check');
					if (wrapper) {
						wrapper.classList.add('input-error');
						let errorElement = wrapper.querySelector('.field-error-message');
						if (!errorElement) {
							errorElement = document.createElement('div');
							errorElement.className = 'field-error-message';
							wrapper.appendChild(errorElement);
						}
						errorElement.textContent = message;
					}
				}
			});
			
			// Hacer focus en el primer campo inválido
			const firstInvalidField = form.querySelector(':invalid') || form.querySelector('.field-wrapper.checkboxs.input-error input[type="checkbox"]');
			if (firstInvalidField) {
				firstInvalidField.focus();
			}
			
			return false; // NO enviar AJAX si hay errores
		}
		
		console.log('✅ Contact form validation passed');
		var formData = $(this).serializeArray();
		console.log(formData);

		// Obtener checkboxes marcados
		var checkboxes = $(this).find('input[type="checkbox"]');
		checkboxes.each(function () {
			var checkbox = $(this);
			if (checkbox.is(':checked')) {
				formData.push({
					name: checkbox.attr('name'),
					value: checkbox.val(),
				});
			}
		});

		// Agregar acción y nonce a los datos del formulario
		formData.push({name: 'action', value: 'amentum_ajax_frm_contact'});
		formData.push({name: 'nonce', value: ajax_forms.frmNonce});

		$.ajax({
			url: ajax_forms.ajaxUrl,
			type: 'post',
			dataType: 'json',
			data: formData,
			beforeSend: function () {
				// Solo manejar el formulario de contacto, NO tocar newsletter
				$('#formulario-contacto button[type="submit"]').prop('disabled', true).text('Enviando...');
			},
		})
			.done(function (res) {
				console.log('📝 Contact form response:', res);
				// NO tocar elementos del newsletter - Las notificaciones globales se manejan automáticamente
				if (res.status === 1) {
					$('#formulario-contacto')[0].reset();
					// El éxito se muestra en notificación global automáticamente
				} else {
					// Los errores se muestran en notificación global automáticamente
					// NO mostrar en .frm-message del newsletter
				}
			})
			.always(function () {
				$('#formulario-contacto button[type="submit"]').prop('disabled', false).text('Enviar →');
			});
	});
}

/* ------------------ BARBAJS ------------------ */
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
						marquee();
					} else {
						// Ya visitó: iniciar todo normal
						initSmoothScroll();
						marquee();
						contentAnimation();
					}
					
					// Inicializar imágenes de magazine (para primera carga)
					if (typeof initMagazineImages === 'function') {
						initMagazineImages();
					}
					
					// Reproducir videos
					if (document.querySelectorAll('video').length) {
						document.querySelectorAll('video').forEach((video) => {
							video.play();
						});
					}
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
					const done = this.async();
					
					// Cursor eliminado
					
					// Mostrar transición
					await pageTransition();
					await delay(200);
					done();
				},
				
				// Antes de entrar a la nueva página
				beforeEnter(data) {
					// Cursor eliminado
					
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
						efectoLetrasHome(); // Solo para transiciones entre páginas
						contentAnimation();
						
						// Inicializar imágenes de magazine (para navegación con Barba.js)
						if (typeof initMagazineImages === 'function') {
							initMagazineImages();
						}
						
						// Reproducir videos
						if (document.querySelectorAll('video').length) {
							document.querySelectorAll('video').forEach((video) => {
								video.play();
							});
						}
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
		
		// El cursor ya no necesita reinicialización porque está fuera del contenedor de Barba.js
	});
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
				contentAnimation();
			}, 3500); // 3.5 segundos para que termine toda la transición
		}, 100);
	}

	// Limpiar sessionStorage solo en recarga de página
	window.addEventListener('beforeunload', function () {
		sessionStorage.removeItem('hasSeenIntro');
	});
}

/**
 * Sistema de scroll suave con Lenis - Implementación optimizada
 */
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

/*-------------- Sistema de Notificaciones Globales ------------------- */

function initGlobalNotifications() {
	// Crear el elemento de notificación global si no existe
	let globalNotification = document.querySelector('.global-notification');
	if (!globalNotification) {
		globalNotification = document.createElement('div');
		globalNotification.className = 'global-notification';
		document.body.appendChild(globalNotification);
	}
	
	// Función global para mostrar notificaciones
	window.showGlobalNotification = function(message, type = 'error', duration = 6000) {
		console.log(`🔔 showGlobalNotification called: "${message}", type: ${type}`);
		globalNotification.textContent = message;
		globalNotification.className = `global-notification ${type}`;
		
		// DEBUG: Verificar posición en pantalla
		console.log('📍 Element position before show:', globalNotification.getBoundingClientRect());
		console.log('📍 Element computed styles before show:', {
			position: window.getComputedStyle(globalNotification).position,
			top: window.getComputedStyle(globalNotification).top,
			right: window.getComputedStyle(globalNotification).right,
			zIndex: window.getComputedStyle(globalNotification).zIndex,
			opacity: window.getComputedStyle(globalNotification).opacity,
			visibility: window.getComputedStyle(globalNotification).visibility,
			display: window.getComputedStyle(globalNotification).display
		});
		
		// FORZAR la clase show con setTimeout para evitar problemas de timing
		setTimeout(() => {
			globalNotification.classList.add('show');
			console.log('📍 Classes after forced show:', globalNotification.className);
			console.log('📍 Element position after show:', globalNotification.getBoundingClientRect());
			console.log('📍 Element computed styles after show:', {
				opacity: window.getComputedStyle(globalNotification).opacity,
				visibility: window.getComputedStyle(globalNotification).visibility,
				transform: window.getComputedStyle(globalNotification).transform
			});
		}, 10);
		
		// Auto-ocultar después del tiempo especificado
		setTimeout(() => {
			globalNotification.classList.remove('show');
			console.log('⏰ Auto-hiding notification after', duration, 'ms');
		}, duration);
	};
	
	// TEST MANUAL: Agregar función de test global
	window.testNotification = function() {
		console.log('🧪 Testing notification manually...');
		showGlobalNotification('Test notification - should be visible!', 'error', 10000);
	};
	
	// Función global para ocultar notificaciones
	window.hideGlobalNotification = function() {
		globalNotification.classList.remove('show');
	};
	
	// Interceptar errores AJAX de WordPress (wp_mail, etc.)
	$(document).ajaxComplete(function(event, xhr, settings) {
		// Solo procesar respuestas JSON de nuestros formularios
		if (settings.data && settings.data.indexOf('amentum_ajax_frm_contact') !== -1) {
			try {
				const response = JSON.parse(xhr.responseText);
				
				// Identificar qué formulario envió la petición
				let formType = 'unknown';
				if (settings.data.indexOf('tipo=newsletter') !== -1) {
					formType = 'newsletter';
				} else if (settings.data.indexOf('tipo=contacto') !== -1) {
					formType = 'contact';
				}
				
				console.log(`📧 AJAX respuesta de formulario: ${formType}`, response);
				
				if (response.status === 0 && response.message) {
					// Error del servidor (wp_mail, etc.) - Solo mostrar notificación global para contacto
					if (formType === 'contact') {
						showGlobalNotification(response.message, 'error');
						console.log('🔔 Mostrando notificación global para error en contacto');
					} else {
						// Ahora SIEMPRE mostramos notificación global
					showGlobalNotification(response.message, 'error');
					}
				} else if (response.status === 1 && response.message) {
					// Éxito - Solo mostrar notificación global para contacto
					if (formType === 'contact') {
						showGlobalNotification(response.message, 'success', 4000);
						console.log('🔔 Mostrando notificación global para éxito en contacto');
					} else {
						// Ahora SIEMPRE mostramos notificación global  
					showGlobalNotification(response.message, 'success', 4000);
					}
				}
			} catch (e) {
				// Si no es JSON válido, ignorar
				console.warn('Error parsing AJAX response:', e);
			}
		}
	});

	// Inicializar Swiper para Magazine
	const magazineSwiper = document.querySelector('.magazine-swiper .swiper-wrapper');
	
	if (magazineSwiper) {
		const swiper = new Swiper('.magazine-swiper', {
			slidesPerView: 1,
			spaceBetween: 20,
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			},
			breakpoints: {
				768: {
					slidesPerView: 1.2,
					spaceBetween: 30,
				}
			}
		});
	}
}