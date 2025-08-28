/**
 * TESTIMONIOS BLOCK - Editor JavaScript
 * Bloque nativo para mostrar testimonios de clientes
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody } = wp.components;
	const { createElement: el } = wp.element;

	// Testimonios de ejemplo
	const exampleTestimonials = [
		{
			text: 'El trabajo realizado por Amentum superó nuestras expectativas. Su enfoque profesional y creatividad nos ayudó a destacar en el mercado.',
			author: 'María García',
			position: 'CEO, TechStart'
		},
		{
			text: 'Profesionalismo y calidad en cada detalle. Recomiendo totalmente sus servicios para cualquier proyecto de branding.',
			author: 'Carlos Rodríguez',
			position: 'Director Creativo, DesignLab'
		},
		{
			text: 'La comunicación fue excelente durante todo el proceso. El resultado final fue exactamente lo que necesitábamos.',
			author: 'Ana López',
			position: 'Fundadora, EcoVerde'
		}
	];

	registerBlockType('amentum/testimonios', {
		title: 'Testimonios Amentum',
		description: 'Grid de testimonios de clientes con autor y cargo.',
		icon: 'format-quote',
		category: 'amentum-blocks',
		keywords: ['testimonios', 'testimonials', 'opiniones', 'clientes', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: 'Lo que Dicen Nuestros Clientes'
			},
			subtitle: {
				type: 'string',
				default: 'Testimonios reales de clientes satisfechos'
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { title, subtitle } = attributes;

			return el('div', { className: 'amentum-testimonios-block-editor' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Configuración de Testimonios',
						initialOpen: true,
						key: 'testimonios-settings'
					}, [
						el('p', { 
							key: 'note'
						}, 'Los testimonios se muestran desde el código del tema. Puedes personalizar el contenido editando las funciones de render.')
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
							placeholder: 'Título de testimonios...',
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
					
					// Preview Grid
					el('div', { 
						className: 'testimonios-grid',
						key: 'testimonios-grid'
					}, exampleTestimonials.map(function(testimonial, index) {
						return el('div', { 
							className: 'testimonio-card',
							key: 'testimonio-' + index
						}, [
							el('div', { 
								className: 'testimonio-content',
								key: 'content-' + index
							}, [
								el('p', { 
									className: 'testimonio-text',
									key: 'text-' + index
								}, '"' + testimonial.text + '"')
							]),
							
							el('div', { 
								className: 'testimonio-autor',
								key: 'autor-' + index
							}, [
								el('div', { 
									className: 'autor-info',
									key: 'autor-info-' + index
								}, [
									el('h4', { 
										className: 'autor-name',
										key: 'name-' + index
									}, testimonial.author),
									
									el('p', { 
										className: 'autor-cargo',
										key: 'cargo-' + index
									}, testimonial.position)
								])
							])
						]);
					})),
					
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
					}, 'Los testimonios se renderizarán desde el backend con contenido personalizable.')
				])
			]);
		},

		save: function(props) {
			// Los bloques server-side rendered devuelven null
			return null;
		}
	});

})();