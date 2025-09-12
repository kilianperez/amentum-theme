/**
 * ABOUT BLOCK - Editor JavaScript
 * Bloque "Sobre Nosotros" con imagen principal, texto y imagen decorativa
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, MediaUpload } = wp.blockEditor;
	const { PanelBody, ToggleControl, Button } = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType('amentum/about', {
		title: 'About Amentum',
		description: 'Sección "Sobre Nosotros" con imagen principal, texto y imagen decorativa.',
		icon: 'admin-users',
		category: 'amentum-blocks',
		keywords: ['about', 'sobre', 'nosotros', 'perfil', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: 'Soy Beatriz Llamas'
			},
			description: {
				type: 'string',
				default: 'Crecí en Madrid, rodeada de pasión por la gastronomía y el cuidado de los detalles. Fundé mi primer catering, La Colineta, durante la universidad y más tarde dirigí la escuela de cocina Alambique, donde organicé eventos exclusivos y comencé a escribir sobre gastronomía. Tras varios años en el extranjero, creé Amentum un proyecto que une mi experiencia en cocina, eventos y relaciones humanas para ofrecer experiencias únicas y personalizadas.'
			},
			mainImage: {
				type: 'object',
				default: {
					url: (typeof wpGlobalSettings !== 'undefined' && wpGlobalSettings.templateDirectoryUri) ? 
						wpGlobalSettings.templateDirectoryUri + '/assets/img/template/about/chef-beatriz.png' : 
						'/wp-content/themes/amentum/assets/img/template/about/chef-beatriz.png',
					alt: 'Beatriz Llamas - Chef'
				}
			},
			decorativeImage: {
				type: 'object', 
				default: {
					url: (typeof wpGlobalSettings !== 'undefined' && wpGlobalSettings.templateDirectoryUri) ? 
						wpGlobalSettings.templateDirectoryUri + '/assets/img/template/about/decorativa-paisaje.png' : 
						'/wp-content/themes/amentum/assets/img/template/about/decorativa-paisaje.png',
					alt: 'Imagen decorativa'
				}
			},
			buttonText: {
				type: 'string',
				default: 'Saber más'
			},
			buttonLink: {
				type: 'string',
				default: '#'
			},
			showDecorative: {
				type: 'boolean',
				default: true
			},
			showButton: {
				type: 'boolean',
				default: true
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { 
				title, 
				description, 
				mainImage, 
				decorativeImage, 
				buttonText, 
				buttonLink,
				showDecorative,
				showButton 
			} = attributes;

			return el('section', { className: 'block-about', id: 'about-block' }, [
				el('div', { className: 'container' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Configuración del Bloque',
						initialOpen: true,
						key: 'about-settings'
					}, [
						el(ToggleControl, {
							label: 'Mostrar imagen decorativa',
							checked: showDecorative,
							onChange: function(value) {
								setAttributes({ showDecorative: value });
							},
							key: 'show-decorative'
						}),
						
						el(ToggleControl, {
							label: 'Mostrar botón de acción',
							checked: showButton,
							onChange: function(value) {
								setAttributes({ showButton: value });
							},
							key: 'show-button'
						})
					]),
					
					el(PanelBody, { 
						title: 'Enlace del Botón',
						initialOpen: false,
						key: 'button-link'
					}, [
						el('input', {
							type: 'url',
							placeholder: 'https://ejemplo.com',
							value: buttonLink,
							onChange: function(event) {
								setAttributes({ buttonLink: event.target.value });
							},
							style: {
								width: '100%',
								padding: '8px',
								borderRadius: '4px',
								border: '1px solid #ddd'
							},
							key: 'button-url'
						})
					])
				]),

				// Block Content
				el('div', { 
					className: 'about-content',
					key: 'about-content'
				}, [
					// About Text
					el('div', { 
						className: 'about-text',
						key: 'about-text'
					}, [
						el(RichText, {
							tagName: 'h2',
							className: 'about-title',
							value: title,
							onChange: function(value) {
								setAttributes({ title: value });
							},
							placeholder: 'Título principal...',
							allowedFormats: ['core/bold', 'core/italic'],
							key: 'about-title'
						}),
						
						el(RichText, {
							tagName: 'p',
							className: 'about-description',
							value: description,
							onChange: function(value) {
								setAttributes({ description: value });
							},
							placeholder: 'Descripción sobre la persona o empresa...',
							allowedFormats: ['core/bold', 'core/italic'],
							key: 'about-description'
						}),
						
						showButton && el('div', { 
							className: 'about-actions',
							key: 'about-actions'
						}, [
							el(RichText, {
								tagName: 'span',
								className: 'btn btn-outline',
								value: buttonText,
								onChange: function(value) {
									setAttributes({ buttonText: value });
								},
								placeholder: 'Texto del botón',
								allowedFormats: [],
								key: 'button-text'
							})
						])
					]),
					
					// Main Image
					el('div', { 
						className: 'about-image-main',
						key: 'about-main-image'
					}, [
						el(MediaUpload, {
							onSelect: function(media) {
								setAttributes({ 
									mainImage: {
										url: media.url,
										alt: media.alt,
										id: media.id
									}
								});
							},
							type: 'image',
							value: mainImage ? mainImage.id : null,
							render: function({ open }) {
								return mainImage ? 
									el('div', { 
										className: 'image-container',
										key: 'main-image-container'
									}, [
										el('img', {
											src: mainImage.url,
											alt: mainImage.alt,
											loading: 'lazy',
											className: 'main-image',
											onClick: open,
											style: { cursor: 'pointer' },
											key: 'main-image'
										}),
										el(Button, {
											className: 'remove-image',
											onClick: function() {
												setAttributes({ mainImage: null });
											},
											isDestructive: true,
											key: 'remove-main'
										}, 'Eliminar imagen')
									]) :
									el(Button, {
										onClick: open,
										className: 'button button-large',
										key: 'upload-main'
									}, 'Subir imagen principal');
							},
							key: 'main-media-upload'
						})
					]),
					
					// Decorative Image
					showDecorative && el('div', { 
						className: 'about-image-decorative',
						key: 'about-decorative-image'
					}, [
						el(MediaUpload, {
							onSelect: function(media) {
								setAttributes({ 
									decorativeImage: {
										url: media.url,
										alt: media.alt,
										id: media.id
									}
								});
							},
							type: 'image',
							value: decorativeImage ? decorativeImage.id : null,
							render: function({ open }) {
								return decorativeImage ? 
									el('div', { 
										className: 'image-container small',
										key: 'decorative-image-container'
									}, [
										el('img', {
											src: decorativeImage.url,
											alt: decorativeImage.alt,
											loading: 'lazy',
											className: 'decorative-image',
											onClick: open,
											style: { cursor: 'pointer', maxWidth: '100px' },
											key: 'decorative-image'
										}),
										el(Button, {
											className: 'remove-image',
											onClick: function() {
												setAttributes({ decorativeImage: null });
											},
											isDestructive: true,
											isSmall: true,
											key: 'remove-decorative'
										}, 'Eliminar')
									]) :
									el(Button, {
										onClick: open,
										className: 'button',
										isSmall: true,
										key: 'upload-decorative'
									}, 'Imagen decorativa');
							},
							key: 'decorative-media-upload'
						})
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