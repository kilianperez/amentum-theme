/**
 * SERVICIOS BLOCK - Editor JavaScript
 * Bloque nativo para mostrar servicios en grid
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody } = wp.components;
	const { createElement: el } = wp.element;

	// Iconos SVG predefinidos
	const serviceIcons = {
		design: el('svg', {
			viewBox: '0 0 24 24',
			fill: 'currentColor',
			key: 'design-icon'
		}, el('path', {
			d: 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'
		})),
		
		branding: el('svg', {
			viewBox: '0 0 24 24',
			fill: 'currentColor',
			key: 'branding-icon'
		}, el('path', {
			d: 'M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2z'
		})),
		
		web: el('svg', {
			viewBox: '0 0 24 24',
			fill: 'currentColor',
			key: 'web-icon'
		}, el('path', {
			d: 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-1 12H5V8h14v8z'
		})),
		
		photo: el('svg', {
			viewBox: '0 0 24 24',
			fill: 'currentColor',
			key: 'photo-icon'
		}, el('path', {
			d: 'M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-5H17V4.5C17 3.12 15.88 2 14.5 2h-5C8.12 2 7 3.12 7 4.5V6H4.5C4.22 6 4 6.22 4 6.5S4.22 7 4.5 7H6v10.5c0 1.38 1.12 2.5 2.5 2.5h7c1.38 0 2.5-1.12 2.5-2.5V7h1.5c.28 0 .5-.22.5-.5S19.78 6 19.5 6z'
		}))
	};

	registerBlockType('amentum/servicios', {
		title: 'Servicios Amentum',
		description: 'Grid de servicios con iconos y descripción.',
		icon: 'grid-view',
		category: 'amentum-blocks',
		keywords: ['servicios', 'services', 'grid', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: 'Nuestros Servicios'
			},
			subtitle: {
				type: 'string',
				default: 'Ofrecemos soluciones creativas integrales'
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { title, subtitle } = attributes;

			return el('div', { className: 'amentum-servicios-block-editor' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Configuración de Servicios',
						initialOpen: true,
						key: 'servicios-settings'
					}, [
						el('p', { 
							key: 'note'
						}, 'Los servicios se muestran automáticamente desde el código del tema.')
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
							placeholder: 'Título de servicios...',
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
						className: 'servicios-grid',
						key: 'servicios-grid'
					}, [
						// Servicio 1
						el('div', { 
							className: 'servicio-card',
							key: 'servicio-1'
						}, [
							el('div', { 
								className: 'servicio-icon',
								key: 'icon-1'
							}, serviceIcons.design),
							el('h3', { 
								className: 'servicio-title',
								key: 'title-1'
							}, 'Diseño Gráfico'),
							el('p', { 
								className: 'servicio-description',
								key: 'desc-1'
							}, 'Creamos identidades visuales impactantes')
						]),
						
						// Servicio 2
						el('div', { 
							className: 'servicio-card',
							key: 'servicio-2'
						}, [
							el('div', { 
								className: 'servicio-icon',
								key: 'icon-2'
							}, serviceIcons.branding),
							el('h3', { 
								className: 'servicio-title',
								key: 'title-2'
							}, 'Branding'),
							el('p', { 
								className: 'servicio-description',
								key: 'desc-2'
							}, 'Desarrollamos estrategias de marca')
						]),
						
						// Servicio 3
						el('div', { 
							className: 'servicio-card',
							key: 'servicio-3'
						}, [
							el('div', { 
								className: 'servicio-icon',
								key: 'icon-3'
							}, serviceIcons.web),
							el('h3', { 
								className: 'servicio-title',
								key: 'title-3'
							}, 'Desarrollo Web'),
							el('p', { 
								className: 'servicio-description',
								key: 'desc-3'
							}, 'Sitios web modernos y optimizados')
						]),
						
						// Servicio 4
						el('div', { 
							className: 'servicio-card',
							key: 'servicio-4'
						}, [
							el('div', { 
								className: 'servicio-icon',
								key: 'icon-4'
							}, serviceIcons.photo),
							el('h3', { 
								className: 'servicio-title',
								key: 'title-4'
							}, 'Fotografía'),
							el('p', { 
								className: 'servicio-description',
								key: 'desc-4'
							}, 'Fotografía profesional y creativa')
						])
					])
				])
			]);
		},

		save: function(props) {
			// Los bloques server-side rendered devuelven null
			return null;
		}
	});

})();