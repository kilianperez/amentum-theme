/**
 * Servicios Columnas Block - Editor JavaScript
 * Sistema mejorado: elementos independientes de las columnas de visualización
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, RichText, ColorPalette } = wp.blockEditor;
    const { PanelBody, Button, RangeControl, TextControl } = wp.components;
    const { createElement: e, Fragment } = wp.element;

    registerBlockType('amentum/servicios-columnas', {
        title: 'Servicios con Columnas',
        icon: 'columns',
        category: 'amentum-blocks',
        attributes: {
            elementos: {
                type: 'array',
                default: [
                    {
                        titulo: 'QUÉ HACEMOS',
                        contenido: 'Diseñamos, planificamos y producimos eventos a medida: selección del espacio ideal, diseño y decoración personalizada, experiencias gastronómicas únicas y coordinación integral de proveedores y servicios.'
                    },
                    {
                        titulo: 'CÓMO LO HACEMOS',
                        contenido: 'Diseñamos eventos únicos y personalizados, escuchando tus necesidades y cuidando cada detalle para que los asistentes vivan experiencias memorables.'
                    },
                    {
                        titulo: 'PARA QUIÉN',
                        contenido: 'Organizamos desde reuniones íntimas hasta celebraciones de gran escala (400-500 personas) para empresas, instituciones, organismos oficiales y clientes particulares.'
                    }
                ]
            },
            columnasPorFila: {
                type: 'number',
                default: 3
            },
            ctaTexto: {
                type: 'string',
                default: '¿Evento a la vista? Deja que lo hagamos perfecto.'
            },
            ctaBoton: {
                type: 'string',
                default: 'Cuéntanos tu idea'
            },
            ctaUrl: {
                type: 'string',
                default: '#contacto'
            },
            backgroundColor: {
                type: 'string',
                default: '#f8f6f3'
            },
            textColor: {
                type: 'string',
                default: '#333333'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { elementos, columnasPorFila, ctaTexto, ctaBoton, ctaUrl, backgroundColor, textColor } = attributes;

            // Función para añadir nuevo elemento
            const addElemento = function() {
                const nuevosElementos = [...elementos, { titulo: 'Nuevo Servicio', contenido: 'Descripción del servicio...' }];
                setAttributes({ elementos: nuevosElementos });
            };

            // Función para eliminar elemento
            const removeElemento = function(index) {
                const nuevosElementos = elementos.filter((_, i) => i !== index);
                setAttributes({ elementos: nuevosElementos });
            };

            // Función para actualizar elemento específico
            const updateElemento = function(index, field, value) {
                const nuevosElementos = [...elementos];
                nuevosElementos[index][field] = value;
                setAttributes({ elementos: nuevosElementos });
            };

            const colorOptions = [
                { name: 'Beige Claro', color: '#f8f6f3' },
                { name: 'Blanco', color: '#ffffff' },
                { name: 'Gris Claro', color: '#f5f5f5' },
                { name: 'Azul Suave', color: '#f0f4f8' }
            ];

            const textColorOptions = [
                { name: 'Negro', color: '#333333' },
                { name: 'Gris Oscuro', color: '#666666' },
                { name: 'Marrón', color: '#8b4513' },
                { name: 'Azul Oscuro', color: '#2c3e50' }
            ];

            // Calcular ancho para el preview - TODO el ancho disponible
            const calcularAncho = function(columnas) {
                switch(columnas) {
                    case 1: return '100%';
                    case 2: return 'calc((100% - 30px) / 2)';
                    case 3: return 'calc((100% - 60px) / 3)';
                    case 4: return 'calc((100% - 90px) / 4)';
                    case 5: return 'calc((100% - 120px) / 5)';
                    case 6: return 'calc((100% - 150px) / 6)';
                    default: return 'calc((100% - 60px) / 3)';
                }
            };

            return e(Fragment, {}, [
                // Inspector Controls (Sidebar)
                e(InspectorControls, {}, [
                    e(PanelBody, { title: 'Configuración de Layout', initialOpen: true }, [
                        e(RangeControl, {
                            label: 'Columnas por fila',
                            value: columnasPorFila,
                            onChange: (value) => setAttributes({ columnasPorFila: value }),
                            min: 1,
                            max: 6,
                            help: `Los ${elementos.length} elementos se mostrarán en filas de ${columnasPorFila} columnas cada una.`
                        }),

                        e('hr', { style: { margin: '20px 0' } }),

                        e('p', { style: { marginBottom: '15px', fontWeight: 'bold' } }, 
                            `Elementos actuales: ${elementos.length}`
                        ),
                        
                        e(Button, {
                            isPrimary: true,
                            onClick: addElemento,
                            style: { marginBottom: '15px' }
                        }, '+ Añadir Elemento')
                    ]),

                    e(PanelBody, { title: 'CTA (Call to Action)', initialOpen: false }, [
                        e(TextControl, {
                            label: 'URL del Botón',
                            value: ctaUrl,
                            onChange: (value) => setAttributes({ ctaUrl: value })
                        })
                    ]),

                    e(PanelBody, { title: 'Colores', initialOpen: false }, [
                        e('p', { style: { marginBottom: '10px' } }, 'Color de fondo:'),
                        e(ColorPalette, {
                            colors: colorOptions,
                            value: backgroundColor,
                            onChange: (value) => setAttributes({ backgroundColor: value || '#f8f6f3' })
                        }),

                        e('p', { style: { marginBottom: '10px', marginTop: '20px' } }, 'Color de texto:'),
                        e(ColorPalette, {
                            colors: textColorOptions,
                            value: textColor,
                            onChange: (value) => setAttributes({ textColor: value || '#333333' })
                        })
                    ])
                ]),

                // Block Content Preview
                e('section', { 
                    className: 'block-servicios-columnas',
                    style: {
                        color: textColor
                    }
                }, [
                    e('div', {
                        className: 'container'
                    }, [
                        e('div', {
                            className: 'servicios-columnas-content'
                        }, [
                    // Info de distribución
                    e('div', {
                        style: {
                            textAlign: 'center',
                            marginBottom: '20px',
                            padding: '10px',
                            borderRadius: '4px',
                            fontSize: '13px',
                            color: '#666'
                        }
                    }, `Mostrando ${elementos.length} elementos en filas de ${columnasPorFila} columnas`),

                    // Grid de elementos
                    e('div', {
                        className: 'servicios-grid',
                        'data-columns': columnasPorFila,
                        style: {
                            display: 'flex',
                            flexWrap: 'wrap',
                            gap: '30px',
                            marginBottom: '40px',
                            justifyContent: 'center'
                        }
                    }, elementos.map((elemento, index) => 
                        e('div', {
                            key: index,
                            className: 'servicio-columna',
                            style: {
                                textAlign: 'center',
                                position: 'relative',
                                flex: `0 0 ${calcularAncho(columnasPorFila)}`,
                                padding: '20px',
                                border: '1px solid rgba(0, 0, 0, 0.1)',
                                borderRadius: '8px',
                                minHeight: '200px'
                            }
                        }, [
                            // Botón eliminar elemento
                            elementos.length > 1 && e(Button, {
                                isDestructive: true,
                                isSmall: true,
                                onClick: () => removeElemento(index),
                                style: {
                                    position: 'absolute',
                                    top: '-10px',
                                    right: '-10px',
                                    zIndex: 10,
                                    minWidth: '30px',
                                    height: '30px',
                                    borderRadius: '50%',
                                    padding: '0',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    color: 'white',
                                    border: '2px solid white',
                                },
                                title: `Eliminar elemento ${index + 1}`
                            }, '✕'),

                            // Número de elemento
                            e('div', {
                                style: {
                                    position: 'absolute',
                                    top: '-10px',
                                    left: '-10px',
                                    width: '30px',
                                    height: '30px',
                                    borderRadius: '50%',
                                    color: 'white',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    border: '2px solid white',
                                }
                            }, index + 1),

                            e(RichText, {
                                tagName: 'h3',
                                className: 'servicio-titulo',
                                style: {
                                    fontSize: '18px',
                                    fontWeight: '700',
                                    marginBottom: '15px',
                                    letterSpacing: '1px',
                                    textTransform: 'uppercase'
                                },
                                value: elemento.titulo,
                                onChange: (value) => updateElemento(index, 'titulo', value),
                                placeholder: 'Título del servicio...'
                            }),

                            e(RichText, {
                                tagName: 'div',
                                className: 'servicio-contenido',
                                style: {
                                    fontSize: '16px',
                                    lineHeight: '1.6',
                                    opacity: '0.9'
                                },
                                value: elemento.contenido,
                                onChange: (value) => updateElemento(index, 'contenido', value),
                                placeholder: 'Descripción del servicio...'
                            })
                        ])
                    )),

                    // CTA Section
                    e('div', {
                        className: 'servicios-cta'
                    }, [
                        e('div', {
                            className: 'cta-texto'
                        }, [
                            e(RichText, {
                                tagName: 'p',
                                value: ctaTexto,
                                onChange: (value) => setAttributes({ ctaTexto: value }),
                                placeholder: 'Texto del CTA...'
                            })
                        ]),

                        e('div', {
                            className: 'cta-boton'
                        }, [
                            e(RichText, {
                                tagName: 'a',
                                className: 'btn-cta',
                                value: ctaBoton,
                                onChange: (value) => setAttributes({ ctaBoton: value }),
                                placeholder: 'Texto del botón...'
                            })
                        ])
                    ])
                ])
                ])
                ])
            ]);
        },

        save: function() {
            // El contenido se renderiza server-side, no client-side
            return null;
        }
    });
})();
