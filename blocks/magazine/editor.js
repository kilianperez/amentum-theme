/**
 * MAGAZINE BLOCK - Editor JavaScript
 * Bloque nativo para mostrar artículos de magazine
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody, RangeControl } = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType('amentum/magazine', {
		title: 'Magazine Amentum',
		description: 'Grid de artículos de magazine con fecha y extracto.',
		icon: 'admin-post',
		category: 'amentum-blocks',
		keywords: ['magazine', 'artículos', 'blog', 'noticias', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: 'Últimos Artículos'
			},
			subtitle: {
				type: 'string',
				default: 'Mantente al día con nuestras últimas publicaciones'
			},
			postsToShow: {
				type: 'number',
				default: 3
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { title, subtitle, postsToShow } = attributes;

			return el('div', { className: 'amentum-magazine-block-editor' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Configuración de Magazine',
						initialOpen: true,
						key: 'magazine-settings'
					}, [
						el(RangeControl, {
							label: 'Número de artículos a mostrar',
							value: postsToShow,
							onChange: function(value) {
								setAttributes({ postsToShow: value });
							},
							min: 1,
							max: 9,
							key: 'posts-to-show'
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
							placeholder: 'Título de magazine...',
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
						className: 'magazine-grid',
						key: 'magazine-grid'
					}, Array.from({ length: Math.min(postsToShow, 3) }, function(_, index) {
						return el('div', { 
							className: 'magazine-card',
							key: 'magazine-' + index
						}, [
							el('div', { 
								className: 'magazine-image',
								key: 'image-' + index
							}, [
								el('div', { 
									style: {
										width: '100%',
										height: '100%',
										background: 'var(--amentum-bg-light)',
										display: 'flex',
										alignItems: 'center',
										justifyContent: 'center',
										color: 'var(--amentum-text-light)',
										fontSize: '0.9rem'
									},
									key: 'placeholder-' + index
								}, 'Imagen del artículo')
							]),
							
							el('div', { 
								className: 'magazine-content',
								key: 'content-' + index
							}, [
								el('div', { 
									className: 'magazine-meta',
									key: 'meta-' + index
								}, new Date().toLocaleDateString('es-ES')),
								
								el('h3', { 
									className: 'magazine-title',
									key: 'title-' + index
								}, el('a', { 
									href: '#',
									key: 'link-' + index
								}, 'Artículo de Ejemplo ' + (index + 1))),
								
								el('p', { 
									className: 'magazine-excerpt',
									key: 'excerpt-' + index
								}, 'Este es un extracto de ejemplo del artículo que muestra cómo se verá el contenido.'),
								
								el('a', { 
									href: '#',
									className: 'magazine-link',
									key: 'read-more-' + index
								}, 'Leer más')
							])
						]);
					})),
					
					// View All Button
					el('div', { 
						className: 'block-actions',
						key: 'block-actions'
					}, [
						el('div', { 
							className: 'btn btn-outline',
							key: 'view-all-btn'
						}, 'Ver Todos los Artículos')
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
					}, 'Los artículos se cargarán automáticamente desde el post type "magazine". Mostrando ' + postsToShow + ' artículos.')
				])
			]);
		},

		save: function(props) {
			// Los bloques server-side rendered devuelven null
			return null;
		}
	});

})();