/**
 * TEXTO + FORMULARIO BLOCK - Editor JavaScript
 * Bloque con texto a la izquierda (4/12) y formulario a la derecha (6/12)
 */

(function() {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls } = wp.blockEditor;
	const { PanelBody, SelectControl } = wp.components;
	const { createElement: el } = wp.element;

	registerBlockType('amentum/texto-formulario', {
		title: 'Texto + Formulario',
		description: 'Bloque con texto a la izquierda (4/12) y formulario a la derecha (6/12)',
		icon: 'align-pull-left',
		category: 'amentum-blocks',
		keywords: ['texto', 'formulario', 'layout', 'columnas'],
		supports: {
			align: ['wide', 'full'],
			anchor: true
		},
		attributes: {
			titulo: {
				type: 'string',
				default: 'Soy Beatriz Llamas'
			},
			contenido: {
				type: 'string',
				default: 'Crecí en Madrid, rodeada de pasión por la gastronomía y el cuidado de los detalles.'
			},
			formularioId: {
				type: 'number',
				default: 0
			},
			espacioEntreCols: {
				type: 'string',
				default: '2rem'
			}
		},

		edit: function(props) {
			const { attributes, setAttributes } = props;
			const { titulo, contenido, formularioId, espacioEntreCols } = attributes;

			// Preparar opciones de formularios
			const formularioOptions = [
				{ value: 0, label: 'Selecciona un formulario...' }
			];

			if (window.amentumFormularios && window.amentumFormularios.formularios) {
				window.amentumFormularios.formularios.forEach(formulario => {
					formularioOptions.push({
						value: formulario.value,
						label: formulario.label
					});
				});
			}

			// Opciones de espacio entre columnas
			const espacioOptions = [
				{ value: '1rem', label: '1rem (Pequeño)' },
				{ value: '1.5rem', label: '1.5rem' },
				{ value: '2rem', label: '2rem (Medio)' },
				{ value: '3rem', label: '3rem (Grande)' },
				{ value: '4rem', label: '4rem (Extra Grande)' }
			];

			const editorStyles = {
				container: {
					padding: '40px 20px',
					backgroundColor: '#f8f9fa',
					border: '1px solid #e9ecef',
					borderRadius: '8px'
				},
				content: {
					display: 'grid',
					gridTemplateColumns: '1fr 1.5fr',
					gap: espacioEntreCols,
					alignItems: 'start',
					maxWidth: '100%',
					backgroundColor: '#ffffff',
					padding: '30px',
					borderRadius: '8px',
					boxShadow: '0 2px 8px rgba(0,0,0,0.1)'
				},
				textoColumn: {
					paddingRight: '1rem'
				},
				titulo: {
					margin: '0 0 1.5rem 0',
					fontSize: '2.5rem',
					fontWeight: '600',
					lineHeight: '1.2',
					color: '#2c3e50',
					fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
				},
				contenido: {
					fontSize: '1.125rem',
					lineHeight: '1.7',
					color: '#4a5568',
					margin: '0',
					fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
				},
				formularioColumn: {
					minHeight: '300px'
				},
				preview: {
					background: '#ffffff',
					border: '1px solid #e9ecef',
					borderRadius: '8px',
					padding: '2rem',
					boxShadow: '0 2px 4px rgba(0,0,0,0.05)',
					minHeight: '280px',
					textAlign: 'center',
					display: 'flex',
					flexDirection: 'column',
					alignItems: 'center',
					justifyContent: 'center',
					color: '#2c3e50',
					position: 'relative'
				},
				placeholder: {
					background: '#f8f9fa',
					border: '2px dashed #dee2e6',
					borderRadius: '12px',
					padding: '3rem 2rem',
					textAlign: 'center',
					minHeight: '280px',
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'center'
				}
			};

			return el('div', {
				className: 'amentum-texto-formulario-editor',
				style: editorStyles.container
			}, [
				// Inspector Controls
				el(InspectorControls, { key: 'inspector' }, [
					el(PanelBody, {
						title: 'Configuración del Formulario',
						initialOpen: true,
						key: 'form-settings'
					}, [
						el(SelectControl, {
							label: 'Formulario a Mostrar',
							value: formularioId,
							options: formularioOptions,
							onChange: function(value) {
								setAttributes({ formularioId: parseInt(value) });
							},
							help: 'Selecciona el formulario que deseas mostrar',
							key: 'form-select'
						})
					]),

					el(PanelBody, {
						title: 'Layout',
						initialOpen: false,
						key: 'layout-settings'
					}, [
						el(SelectControl, {
							label: 'Espacio Entre Columnas',
							value: espacioEntreCols,
							options: espacioOptions,
							onChange: function(value) {
								setAttributes({ espacioEntreCols: value });
							},
							help: 'Ajusta el espacio entre el texto y el formulario',
							key: 'spacing-select'
						})
					])
				]),

				// Block Content
				el('div', {
					className: 'texto-formulario-content',
					key: 'content',
					style: editorStyles.content
				}, [
					// Texto Column
					el('div', {
						className: 'texto-column',
						key: 'texto-column',
						style: editorStyles.textoColumn
					}, [
						el(RichText, {
							tagName: 'h2',
							className: 'texto-title',
							value: titulo,
							onChange: function(value) {
								setAttributes({ titulo: value });
							},
							placeholder: 'Título principal...',
							allowedFormats: ['core/bold', 'core/italic'],
							key: 'titulo',
							style: editorStyles.titulo
						}),

						el(RichText, {
							tagName: 'p',
							className: 'texto-description',
							value: contenido,
							onChange: function(value) {
								setAttributes({ contenido: value });
							},
							placeholder: 'Descripción del contenido...',
							allowedFormats: ['core/bold', 'core/italic'],
							key: 'contenido',
							style: editorStyles.contenido
						})
					]),

					// Formulario Column
					el('div', {
						className: 'formulario-column',
						key: 'formulario-column',
						style: editorStyles.formularioColumn
					}, [
						formularioId > 0 ?
							// Vista del formulario seleccionado
							el('div', {
								className: 'formulario-preview',
								key: 'form-preview',
								style: editorStyles.preview
							}, [
								el('div', {
									style: { fontSize: '3rem', marginBottom: '1.5rem', opacity: '0.9' }
								}, '📝'),
								el('h3', {
									style: {
										margin: '0 0 1rem 0',
										color: '#2c3e50',
										fontSize: '1.5rem',
										fontWeight: '600',
										fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
									}
								}, 'Formulario ID: ' + formularioId),
								el('p', {
									style: {
										margin: '0',
										color: '#4a5568',
										fontSize: '1rem',
										fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
									}
								}, 'Formulario estilo Avellana'),
								// Simular línea animada de Avellana
								el('div', {
									style: {
										position: 'absolute',
										bottom: '20px',
										left: '20px',
										right: '20px',
										height: '1px',
										backgroundColor: '#2c3e50',
										opacity: '0.3'
									}
								}),
								el('div', {
									style: {
										position: 'absolute',
										bottom: '20px',
										left: '20px',
										width: '60%',
										height: '2px',
										backgroundColor: '#667eea'
									}
								})
							]) :
							// Placeholder cuando no hay formulario
							el('div', {
								className: 'formulario-placeholder',
								key: 'form-placeholder',
								style: editorStyles.placeholder
							}, [
								el('div', { style: { textAlign: 'center' } }, [
									el('div', {
										style: {
											fontSize: '4rem',
											marginBottom: '1.5rem',
											opacity: '0.6'
										}
									}, '📝'),
									el('h3', {
										style: {
											margin: '0 0 1rem 0',
											color: '#495057',
											fontSize: '1.5rem',
											fontWeight: '600'
										}
									}, 'Selecciona un Formulario'),
									el('p', {
										style: {
											margin: '0',
											color: '#6c757d',
											fontSize: '1rem'
										}
									}, 'Usa la configuración del bloque →')
								])
							])
					])
				])
			]);
		},

		save: function() {
			// Los bloques server-side rendered devuelven null
			return null;
		}
	});

})();