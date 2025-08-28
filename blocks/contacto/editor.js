/**
 * CONTACTO BLOCK - Editor JavaScript
 * Bloque nativo para formulario de contacto
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl } = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType('amentum/contacto', {
		title: 'Contacto Amentum',
		description: 'Formulario de contacto con información de empresa.',
		icon: 'email-alt',
		category: 'amentum-blocks',
		keywords: ['contacto', 'contact', 'formulario', 'email', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: '¿Listo para comenzar tu proyecto?'
			},
			subtitle: {
				type: 'string',
				default: 'Cuéntanos sobre tu idea y hagámosla realidad'
			},
			email: {
				type: 'string',
				default: 'hello@amentum.com'
			},
			responseTime: {
				type: 'string',
				default: 'Respondemos en menos de 24 horas'
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { title, subtitle, email, responseTime } = attributes;

			return el('div', { className: 'amentum-contacto-block-editor' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Información de Contacto',
						initialOpen: true,
						key: 'contacto-settings'
					}, [
						el(TextControl, {
							label: 'Email de contacto',
							value: email,
							onChange: function(value) {
								setAttributes({ email: value });
							},
							type: 'email',
							key: 'email-field'
						}),
						
						el(TextControl, {
							label: 'Tiempo de respuesta',
							value: responseTime,
							onChange: function(value) {
								setAttributes({ responseTime: value });
							},
							key: 'response-time'
						})
					])
				]),

				// Block Content
				el('div', { key: 'block-content' }, [
					// Header
					el('div', { 
						className: 'block-header',
						key: 'block-header'
					}, [
						el(RichText, {
							tagName: 'h2',
							className: 'block-title',
							value: title,
							onChange: function(value) {
								setAttributes({ title: value });
							},
							placeholder: 'Título de contacto...',
							key: 'title'
						}),
						
						el(RichText, {
							tagName: 'p',
							className: 'block-subtitle',
							value: subtitle,
							onChange: function(value) {
								setAttributes({ subtitle: value });
							},
							placeholder: 'Subtítulo descriptivo...',
							key: 'subtitle'
						})
					]),
					
					// Contacto Content Preview
					el('div', { 
						className: 'contacto-content',
						key: 'contacto-content'
					}, [
						// Form Preview
						el('div', { 
							className: 'contact-form',
							key: 'contact-form'
						}, [
							el('div', { 
								className: 'form-row',
								key: 'form-row-1'
							}, [
								el('div', { 
									className: 'form-group',
									key: 'nombre-group'
								}, [
									el('label', { key: 'nombre-label' }, 'Nombre *'),
									el('input', { 
										type: 'text',
										placeholder: 'Tu nombre...',
										disabled: true,
										key: 'nombre-input'
									})
								]),
								
								el('div', { 
									className: 'form-group',
									key: 'email-group'
								}, [
									el('label', { key: 'email-label' }, 'Email *'),
									el('input', { 
										type: 'email',
										placeholder: 'tu@email.com',
										disabled: true,
										key: 'email-input'
									})
								])
							]),
							
							el('div', { 
								className: 'form-group',
								key: 'servicios-group'
							}, [
								el('label', { key: 'servicios-label' }, 'Servicios de interés'),
								el('div', { 
									className: 'form-tags',
									key: 'form-tags'
								}, [
									el('label', { 
										className: 'tag-option',
										key: 'tag-1'
									}, [
										el('input', { 
											type: 'checkbox',
											disabled: true,
											key: 'checkbox-1'
										}),
										el('span', { key: 'span-1' }, 'Diseño')
									]),
									el('label', { 
										className: 'tag-option',
										key: 'tag-2'
									}, [
										el('input', { 
											type: 'checkbox',
											disabled: true,
											key: 'checkbox-2'
										}),
										el('span', { key: 'span-2' }, 'Branding')
									]),
									el('label', { 
										className: 'tag-option',
										key: 'tag-3'
									}, [
										el('input', { 
											type: 'checkbox',
											disabled: true,
											key: 'checkbox-3'
										}),
										el('span', { key: 'span-3' }, 'Web')
									])
								])
							]),
							
							el('div', { 
								className: 'form-group',
								key: 'mensaje-group'
							}, [
								el('label', { key: 'mensaje-label' }, 'Cuéntanos sobre tu proyecto *'),
								el('textarea', { 
									placeholder: 'Describe tu idea, objetivos y detalles...',
									disabled: true,
									key: 'mensaje-textarea'
								})
							]),
							
							el('div', { 
								className: 'form-group',
								key: 'submit-group'
							}, [
								el('div', { 
									className: 'btn btn-primary btn-large',
									key: 'submit-btn'
								}, 'Enviar Mensaje')
							])
						]),
						
						// Info Preview
						el('div', { 
							className: 'contacto-info',
							key: 'contacto-info'
						}, [
							el('div', { 
								className: 'info-item',
								key: 'info-1'
							}, [
								el('h3', { key: 'info-title' }, 'Información de Contacto'),
								el('p', { key: 'info-desc' }, 'Estamos aquí para ayudarte con tu próximo proyecto.')
							]),
							
							el('div', { 
								className: 'info-item',
								key: 'info-2'
							}, [
								el('h4', { key: 'email-title' }, 'Email'),
								el('p', { key: 'email-value' }, [
									el('a', { 
										href: 'mailto:' + email,
										key: 'email-link'
									}, email)
								])
							]),
							
							el('div', { 
								className: 'info-item',
								key: 'info-3'
							}, [
								el('h4', { key: 'response-title' }, 'Tiempo de Respuesta'),
								el('p', { key: 'response-value' }, responseTime)
							])
						])
					]),
					
					// Info Note
					el('div', { 
						className: 'editor-note',
						style: {
							background: '#f0f0f1',
							padding: '15px',
							margin: '20px 0',
							borderRadius: '4px',
							fontSize: '14px',
							color: '#646970'
						},
						key: 'editor-note'
					}, 'El formulario es completamente funcional en el frontend. Los campos se procesan via AJAX.')
				])
			]);
		},

		save: function(props) {
			// Los bloques server-side rendered devuelven null
			return null;
		}
	});

})();