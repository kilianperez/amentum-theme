/**
 * Editor de Bloque Selector de Formulario - Sistema Nativo
 */

const { registerBlockType } = wp.blocks;
const { createElement: el, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl, ServerSideRender } = wp.components;
const { __ } = wp.i18n;

registerBlockType('amentum/formulario-selector', {
    title: __('Selector de Formulario', 'amentum'),
    description: __('Selecciona y muestra un formulario personalizado', 'amentum'),
    category: 'amentum-blocks',
    icon: 'feedback',
    keywords: ['formulario', 'contacto', 'form', 'selector'],
    supports: {
        align: ['wide', 'full'],
        anchor: true,
    },
    attributes: {
        formularioId: {
            type: 'number',
            default: 0
        },
        mostrarTitulo: {
            type: 'boolean',
            default: true
        },
        mostrarDescripcion: {
            type: 'boolean',
            default: true
        },
        clasePersonalizada: {
            type: 'string',
            default: ''
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { formularioId, mostrarTitulo, mostrarDescripcion, clasePersonalizada } = attributes;

        // Preparar opciones de formularios
        const formularioOptions = [
            { value: 0, label: __('Selecciona un formulario...', 'amentum') }
        ];
        
        if (window.amentumFormularios && window.amentumFormularios.formularios) {
            window.amentumFormularios.formularios.forEach(formulario => {
                formularioOptions.push({
                    value: formulario.value,
                    label: formulario.label
                });
            });
        }

        return el(Fragment, {},
            // Inspector Controls (Sidebar)
            el(InspectorControls, {},
                el(PanelBody, {
                    title: __('Configuración del Formulario', 'amentum'),
                    initialOpen: true
                },
                    el(SelectControl, {
                        label: __('Formulario a Mostrar', 'amentum'),
                        value: formularioId,
                        options: formularioOptions,
                        onChange: (value) => setAttributes({ formularioId: parseInt(value) }),
                        help: __('Selecciona el formulario que deseas mostrar en esta página', 'amentum')
                    }),
                    
                    formularioId > 0 && el(ToggleControl, {
                        label: __('Mostrar Título del Formulario', 'amentum'),
                        checked: mostrarTitulo,
                        onChange: (value) => setAttributes({ mostrarTitulo: value }),
                        help: __('Mostrar el título configurado en el formulario', 'amentum')
                    }),
                    
                    formularioId > 0 && el(ToggleControl, {
                        label: __('Mostrar Descripción del Formulario', 'amentum'),
                        checked: mostrarDescripcion,
                        onChange: (value) => setAttributes({ mostrarDescripcion: value }),
                        help: __('Mostrar la descripción configurada en el formulario', 'amentum')
                    }),
                    
                    formularioId > 0 && el(TextControl, {
                        label: __('Clase CSS Personalizada', 'amentum'),
                        value: clasePersonalizada,
                        onChange: (value) => setAttributes({ clasePersonalizada: value }),
                        help: __('Clase CSS opcional para personalizar el estilo del formulario', 'amentum'),
                        placeholder: __('mi-clase-personalizada', 'amentum')
                    })
                )
            ),
            
            // Preview del bloque
            formularioId > 0 ? 
                el(ServerSideRender, {
                    block: 'amentum/formulario-selector',
                    attributes: attributes,
                    EmptyResponsePlaceholder: () => el('div', {
                        style: {
                            padding: '2rem',
                            border: '2px solid #f0ad4e',
                            background: '#fcf8e3',
                            textAlign: 'center',
                            borderRadius: '4px'
                        }
                    },
                        el('p', { style: { fontWeight: 'bold' } }, __('Formulario Seleccionado', 'amentum')),
                        el('p', {}, __('Este formulario no tiene campos configurados o no se puede mostrar.', 'amentum'))
                    ),
                    ErrorResponsePlaceholder: ({ response }) => el('div', {
                        style: {
                            padding: '2rem',
                            border: '2px solid #dc3545',
                            background: '#f8d7da',
                            textAlign: 'center',
                            borderRadius: '4px'
                        }
                    },
                        el('p', { style: { fontWeight: 'bold', color: '#721c24' } }, __('Error al cargar el formulario', 'amentum')),
                        el('p', { style: { color: '#721c24' } }, response && response.message ? response.message : __('Error desconocido', 'amentum'))
                    )
                }) :
                // Placeholder cuando no hay formulario seleccionado
                el('div', {
                    style: {
                        padding: '2rem',
                        border: '2px dashed #ccc',
                        textAlign: 'center',
                        color: '#666',
                        borderRadius: '4px',
                        backgroundColor: '#f9f9f9'
                    }
                },
                    el('div', {
                        style: {
                            fontSize: '24px',
                            marginBottom: '1rem'
                        }
                    }, '📝'),
                    el('p', { 
                        style: { 
                            fontWeight: 'bold', 
                            marginBottom: '0.5rem',
                            fontSize: '16px'
                        } 
                    }, __('Selector de Formulario', 'amentum')),
                    el('p', {
                        style: { 
                            margin: '0',
                            fontSize: '14px'
                        }
                    }, __('Selecciona un formulario en la configuración del bloque para mostrarlo aquí.', 'amentum'))
                )
        );
    },

    save: function() {
        // El renderizado se maneja en el servidor
        return null;
    }
});