/**
 * Sistema de manejo de formularios nativos de Amentum
 * Validación y envío AJAX
 */

(function($) {
    'use strict';

    class AmentumFormHandler {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            // Event delegation para formularios dinámicos
            $(document).on('submit', '.amentum-formulario', (e) => {
                e.preventDefault();
                this.handleFormSubmit(e);
            });

            // Validación en tiempo real
            $(document).on('input blur', '.amentum-campo-input, .amentum-campo-textarea, .amentum-campo-select', (e) => {
                this.validateField($(e.target));
            });

            // Validación especial para checkboxes de tags
            $(document).on('change', '.amentum-campo-checkbox', (e) => {
                this.validateTagsField($(e.target));
            });
        }

        async handleFormSubmit(e) {
            const $form = $(e.target);
            const $submitButton = $form.find('.amentum-boton-enviar');
            const $loadingText = $submitButton.find('.amentum-boton-loading');
            const $buttonText = $submitButton.find('.amentum-boton-texto');
            const $messageContainer = $form.find('.amentum-formulario-mensaje');
            
            // Limpiar errores previos
            this.clearErrors($form);

            // Validar formulario
            if (!this.validateForm($form)) {
                this.showMessage($messageContainer, 'Por favor corrige los errores marcados en rojo.', 'error');
                return;
            }

            // Mostrar estado de carga
            $submitButton.prop('disabled', true);
            $buttonText.hide();
            $loadingText.show();

            try {
                const formData = this.collectFormData($form);
                const response = await this.submitForm(formData);

                if (response.success) {
                    this.showMessage($messageContainer, response.data.mensaje || 'Tu mensaje ha sido enviado correctamente.', 'exito');
                    $form[0].reset();
                    this.clearErrors($form);
                } else {
                    this.showMessage($messageContainer, response.data.mensaje || 'Hubo un error al enviar el formulario.', 'error');
                }
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                this.showMessage($messageContainer, 'Error de conexión. Por favor intenta de nuevo.', 'error');
            } finally {
                // Restaurar botón
                $submitButton.prop('disabled', false);
                $loadingText.hide();
                $buttonText.show();
            }
        }

        validateForm($form) {
            let isValid = true;
            
            // Validar campos requeridos
            $form.find('[required]').each((index, element) => {
                if (!this.validateField($(element))) {
                    isValid = false;
                }
            });

            // Validar campos de tags requeridos
            $form.find('.amentum-campo-tags').each((index, container) => {
                const $container = $(container);
                const $checkboxes = $container.find('input[data-required="required"]');
                if ($checkboxes.length > 0) {
                    if (!this.validateTagsField($checkboxes.first())) {
                        isValid = false;
                    }
                }
            });

            return isValid;
        }

        validateField($field) {
            const value = $field.val().trim();
            const fieldType = $field.attr('type') || 'text';
            const $wrapper = $field.closest('.amentum-campo-wrapper');
            const $error = $wrapper.find('.amentum-campo-error');
            
            let isValid = true;
            let errorMessage = '';

            // Validar campos requeridos
            if ($field.prop('required') && !value) {
                isValid = false;
                errorMessage = 'Este campo es obligatorio';
            }
            
            // Validaciones específicas por tipo
            if (value && isValid) {
                switch (fieldType) {
                    case 'email':
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            isValid = false;
                            errorMessage = 'Ingresa un email válido';
                        }
                        break;
                    
                    case 'tel':
                        const phoneRegex = /^[\+]?[\d\s\-\(\)]+$/;
                        if (!phoneRegex.test(value) || value.length < 9) {
                            isValid = false;
                            errorMessage = 'Ingresa un teléfono válido';
                        }
                        break;
                    
                    case 'url':
                        try {
                            new URL(value);
                        } catch (e) {
                            isValid = false;
                            errorMessage = 'Ingresa una URL válida';
                        }
                        break;
                }
            }

            // Mostrar/ocultar error
            if (!isValid) {
                this.showFieldError($field, errorMessage);
            } else {
                this.clearFieldError($field);
            }

            return isValid;
        }

        validateTagsField($field) {
            const $tagsContainer = $field.closest('.amentum-campo-tags');
            const $wrapper = $tagsContainer.closest('.amentum-campo-wrapper');
            const $error = $wrapper.find('.amentum-campo-error');
            const $checkboxes = $tagsContainer.find('input[data-required="required"]');
            
            if ($checkboxes.length === 0) return true;

            const hasSelection = $tagsContainer.find('input:checked').length > 0;
            
            if (!hasSelection) {
                this.showFieldError($tagsContainer, 'Selecciona al menos una opción');
                return false;
            } else {
                this.clearFieldError($tagsContainer);
                return true;
            }
        }

        showFieldError($field, message) {
            const $wrapper = $field.closest('.amentum-campo-wrapper');
            const $error = $wrapper.find('.amentum-campo-error');
            
            // Añadir clase de error al campo
            if ($field.hasClass('amentum-campo-tags')) {
                $field.addClass('campo-error');
            } else {
                $field.addClass('campo-error');
            }
            
            // Mostrar mensaje de error
            $error.text(message).show();
        }

        clearFieldError($field) {
            const $wrapper = $field.closest('.amentum-campo-wrapper');
            const $error = $wrapper.find('.amentum-campo-error');
            
            // Remover clase de error
            if ($field.hasClass('amentum-campo-tags')) {
                $field.removeClass('campo-error');
            } else {
                $field.removeClass('campo-error');
            }
            
            // Ocultar mensaje de error
            $error.hide().text('');
        }

        clearErrors($form) {
            $form.find('.campo-error').removeClass('campo-error');
            $form.find('.amentum-campo-error').hide().text('');
        }

        collectFormData($form) {
            const formData = new FormData();
            
            // Añadir datos básicos del formulario
            $form.find('input[type="hidden"]').each((index, input) => {
                formData.append(input.name, input.value);
            });

            // Añadir campos de texto, email, tel, url, textarea, select
            $form.find('.amentum-campo-input, .amentum-campo-textarea, .amentum-campo-select').each((index, field) => {
                const $field = $(field);
                if ($field.val().trim()) {
                    formData.append(field.name, $field.val().trim());
                }
            });

            // Añadir checkboxes individuales
            $form.find('.amentum-campo-checkbox-wrapper input:checked').each((index, checkbox) => {
                formData.append(checkbox.name, checkbox.value);
            });

            // Añadir tags (checkboxes múltiples)
            const tagGroups = {};
            $form.find('.amentum-campo-tags input:checked').each((index, checkbox) => {
                const name = checkbox.name.replace('[]', '');
                if (!tagGroups[name]) {
                    tagGroups[name] = [];
                }
                tagGroups[name].push(checkbox.value);
            });

            // Añadir grupos de tags al FormData
            Object.keys(tagGroups).forEach(key => {
                tagGroups[key].forEach(value => {
                    formData.append(key + '[]', value);
                });
            });

            return formData;
        }

        async submitForm(formData) {
            const response = await fetch(amentum_ajax.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }

        showMessage($container, message, type) {
            $container
                .removeClass('exito error')
                .addClass(type)
                .text(message)
                .show();

            // Auto-ocultar mensajes de éxito después de 5 segundos
            if (type === 'exito') {
                setTimeout(() => {
                    $container.fadeOut();
                }, 5000);
            }
        }
    }

    // Inicializar cuando el DOM esté listo
    $(document).ready(() => {
        new AmentumFormHandler();
    });

})(jQuery);