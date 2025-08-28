/**
 * HERO BLOCK - Editor JavaScript
 * Bloque nativo para sección principal de la página
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody, ToggleControl } = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType('amentum/hero', {
		title: 'Hero Amentum',
		description: 'Sección principal con título, subtítulo y botones de acción.',
		icon: 'format-image',
		category: 'amentum-blocks',
		keywords: ['hero', 'principal', 'inicio', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: 'Bienvenido a Amentum'
			},
			subtitle: {
				type: 'string',
				default: 'Agencia creativa especializada en diseño, branding y desarrollo web'
			},
			showButtons: {
				type: 'boolean',
				default: true
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { title, subtitle, showButtons } = attributes;

			return el('div', { className: 'amentum-hero-block-editor' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Configuración del Hero',
						initialOpen: true,
						key: 'hero-settings'
					}, [
						el(ToggleControl, {
							label: 'Mostrar botones de acción',
							checked: showButtons,
							onChange: function(value) {
								setAttributes({ showButtons: value });
							},
							key: 'show-buttons'
						})
					])
				]),

				// Block Content
				el('div', { 
					className: 'hero-content',
					key: 'hero-content'
				}, [
					el('div', { 
						className: 'hero-text',
						key: 'hero-text'
					}, [
						el(RichText, {
							tagName: 'h1',
							className: 'hero-title',
							value: title,
							onChange: function(value) {
								setAttributes({ title: value });
							},
							placeholder: 'Título principal...',
							allowedFormats: ['core/bold', 'core/italic'],
							key: 'hero-title'
						}),
						
						el(RichText, {
							tagName: 'p',
							className: 'hero-subtitle',
							value: subtitle,
							onChange: function(value) {
								setAttributes({ subtitle: value });
							},
							placeholder: 'Subtítulo descriptivo...',
							allowedFormats: ['core/bold', 'core/italic'],
							key: 'hero-subtitle'
						}),
						
						showButtons && el('div', { 
							className: 'hero-actions',
							key: 'hero-actions'
						}, [
							el('div', { 
								className: 'btn btn-primary',
								key: 'btn-primary'
							}, 'Ver Proyectos'),
							el('div', { 
								className: 'btn btn-outline',
								key: 'btn-outline' 
							}, 'Contactar')
						])
					]),
					
					el('div', { 
						className: 'hero-visual',
						key: 'hero-visual'
					}, [
						el('div', { 
							className: 'hero-shape',
							key: 'hero-shape'
						}, [
							el('svg', {
								viewBox: '0 0 200 200',
								className: 'shape-svg',
								key: 'shape-svg'
							}, [
								el('circle', {
									cx: '100',
									cy: '100',
									r: '80',
									fill: 'var(--amentum-primary)',
									opacity: '0.2',
									key: 'circle-1'
								}),
								el('circle', {
									cx: '100',
									cy: '100',
									r: '60',
									fill: 'var(--amentum-secondary)',
									opacity: '0.3',
									key: 'circle-2'
								}),
								el('circle', {
									cx: '100',
									cy: '100',
									r: '40',
									fill: 'var(--amentum-accent)',
									opacity: '0.4',
									key: 'circle-3'
								})
							])
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