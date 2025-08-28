/**
 * PROYECTOS BLOCK - Editor JavaScript
 * Bloque nativo para mostrar proyectos en grid
 */

(function() {
	'use strict';
	
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody, ToggleControl, RangeControl } = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType('amentum/proyectos', {
		title: 'Proyectos Amentum',
		description: 'Grid de proyectos destacados con imágenes y enlaces.',
		icon: 'portfolio',
		category: 'amentum-blocks',
		keywords: ['proyectos', 'projects', 'portfolio', 'amentum'],
		
		attributes: {
			title: {
				type: 'string',
				default: 'Proyectos Destacados'
			},
			subtitle: {
				type: 'string',
				default: 'Algunos de nuestros trabajos más recientes'
			},
			showViewAll: {
				type: 'boolean',
				default: true
			},
			postsToShow: {
				type: 'number',
				default: 6
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { title, subtitle, showViewAll, postsToShow } = attributes;

			return el('div', { className: 'amentum-proyectos-block-editor' }, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, { 
						title: 'Configuración de Proyectos',
						initialOpen: true,
						key: 'proyectos-settings'
					}, [
						el(RangeControl, {
							label: 'Número de proyectos a mostrar',
							value: postsToShow,
							onChange: function(value) {
								setAttributes({ postsToShow: value });
							},
							min: 3,
							max: 12,
							step: 3,
							key: 'posts-to-show'
						}),
						
						el(ToggleControl, {
							label: 'Mostrar botón "Ver Todos"',
							checked: showViewAll,
							onChange: function(value) {
								setAttributes({ showViewAll: value });
							},
							key: 'show-view-all'
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
							placeholder: 'Título de proyectos...',
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
						className: 'proyectos-grid',
						key: 'proyectos-grid'
					}, Array.from({ length: Math.min(postsToShow, 3) }, function(_, index) {
						return el('div', { 
							className: 'proyecto-card',
							key: 'proyecto-' + index
						}, [
							el('div', { 
								className: 'proyecto-image',
								key: 'image-' + index
							}, [
								el('div', { 
									className: 'proyecto-placeholder',
									key: 'placeholder-' + index
								}, 'Proyecto ' + (index + 1)),
								
								el('div', { 
									className: 'proyecto-overlay',
									key: 'overlay-' + index
								}, [
									el('span', { 
										className: 'proyecto-link',
										key: 'link-' + index
									}, 'Ver Proyecto')
								])
							]),
							
							el('div', { 
								className: 'proyecto-content',
								key: 'content-' + index
							}, [
								el('h3', { 
									className: 'proyecto-title',
									key: 'title-' + index
								}, el('a', { 
									href: '#',
									key: 'link-title-' + index
								}, 'Proyecto Ejemplo ' + (index + 1))),
								
								el('div', { 
									className: 'proyecto-tags',
									key: 'tags-' + index
								}, [
									el('span', { 
										className: 'tag',
										key: 'tag-' + index
									}, 'Diseño')
								])
							])
						]);
					})),
					
					// View All Button
					showViewAll && el('div', { 
						className: 'block-actions',
						key: 'block-actions'
					}, [
						el('div', { 
							className: 'btn btn-primary',
							key: 'view-all-btn'
						}, 'Ver Todos los Proyectos')
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
					}, 'Los proyectos se cargarán automáticamente desde el post type "projects". Mostrando ' + postsToShow + ' proyectos.')
				])
			]);
		},

		save: function(props) {
			// Los bloques server-side rendered devuelven null
			return null;
		}
	});

})();