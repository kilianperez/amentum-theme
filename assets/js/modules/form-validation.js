/**
 * Sistema completo de validación de formularios
 */

export function initValidationSystem() {
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
	
	// Función para limpiar todos los errores de un formulario
	function clearAllFieldErrors(form) {
		const inputs = form.querySelectorAll('input, textarea');
		inputs.forEach(input => {
			clearFieldError(input);
		});
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
}

// Función placeholder para el sistema de notificaciones globales
function initGlobalNotifications() {
	// Esta función puede implementarse más adelante si se necesita un sistema de notificaciones global
	console.log('📢 Sistema de notificaciones globales inicializado');
}