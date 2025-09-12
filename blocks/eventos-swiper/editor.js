/**
 * Eventos Swiper Block - Editor JavaScript
 * Bloque para crear un swiper fullscreen con eventos
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, Button, ToggleControl, RangeControl, CheckboxControl, TextControl } = wp.components;
    const { useSelect } = wp.data;
    const { createElement: e, Fragment } = wp.element;

    registerBlockType('amentum/eventos-swiper', {
        title: 'Eventos Swiper',
        icon: 'images-alt2',
        category: 'amentum-blocks',
        attributes: {
            textoInicial: {
                type: 'string',
                default: 'Historias en imágenes'
            },
            descripcionInicial: {
                type: 'string',
                default: 'Ver más'
            },
            mostrarEventos: {
                type: 'boolean',
                default: true
            },
            numeroEventos: {
                type: 'number',
                default: 5
            },
            eventosSeleccionados: {
                type: 'array',
                default: []
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { textoInicial, descripcionInicial, mostrarEventos, numeroEventos, eventosSeleccionados } = attributes;

            // Obtener lista de eventos disponibles
            const eventos = useSelect(function(select) {
                return select('core').getEntityRecords('postType', 'eventos', {
                    per_page: -1,
                    status: 'publish'
                }) || [];
            }, []);


            function toggleEventoSeleccionado(eventoId) {
                const nuevosEventos = eventosSeleccionados.includes(eventoId)
                    ? eventosSeleccionados.filter(id => id !== eventoId)
                    : [...eventosSeleccionados, eventoId];
                setAttributes({ eventosSeleccionados: nuevosEventos });
            }

            function moverEvento(eventoId, direccion) {
                const currentIndex = eventosSeleccionados.indexOf(eventoId);
                if (currentIndex === -1) return;
                
                const newIndex = direccion === 'up' ? currentIndex - 1 : currentIndex + 1;
                if (newIndex < 0 || newIndex >= eventosSeleccionados.length) return;
                
                const nuevosEventos = [...eventosSeleccionados];
                [nuevosEventos[currentIndex], nuevosEventos[newIndex]] = [nuevosEventos[newIndex], nuevosEventos[currentIndex]];
                setAttributes({ eventosSeleccionados: nuevosEventos });
            }

            // Función para obtener evento por ID
            function getEventoById(id) {
                return eventos.find(evento => evento.id === id);
            }

            return e(Fragment, {}, [
                // Inspector Controls (Sidebar)
                e(InspectorControls, {}, [
                    e(PanelBody, { title: 'Configuración del Swiper', initialOpen: true }, [
                        e(ToggleControl, {
                            label: 'Mostrar eventos automáticamente',
                            checked: mostrarEventos,
                            onChange: function(value) { setAttributes({ mostrarEventos: value }); }
                        }),
                        
                        mostrarEventos && e(RangeControl, {
                            label: 'Número de eventos a mostrar',
                            value: numeroEventos,
                            onChange: function(value) { setAttributes({ numeroEventos: value }); },
                            min: 1,
                            max: 10
                        }),

                        // Panel para selección manual de eventos
                        !mostrarEventos && eventos.length > 0 && e('div', { style: { marginTop: '20px' } }, [
                            e('h4', { style: { margin: '10px 0' } }, 'Seleccionar eventos específicos'),
                            e('div', { 
                                style: { 
                                    maxHeight: '200px', 
                                    overflowY: 'auto',
                                    border: '1px solid #ddd',
                                    padding: '10px',
                                    borderRadius: '4px'
                                }
                            }, eventos.map(function(evento) {
                                return e(CheckboxControl, {
                                    key: evento.id,
                                    label: evento.title.rendered,
                                    checked: eventosSeleccionados.includes(evento.id),
                                    onChange: function() { toggleEventoSeleccionado(evento.id); }
                                });
                            }))
                        ]),

                        // Panel para reordenar eventos seleccionados
                        !mostrarEventos && eventosSeleccionados.length > 1 && e('div', { style: { marginTop: '20px' } }, [
                            e('h4', { style: { margin: '10px 0' } }, 'Ordenar eventos seleccionados'),
                            e('div', {
                                style: {
                                    border: '1px solid #ddd',
                                    borderRadius: '4px',
                                    padding: '10px',
                                    maxHeight: '250px',
                                    overflowY: 'auto'
                                }
                            }, eventosSeleccionados.map(function(eventoId, index) {
                                const evento = getEventoById(eventoId);
                                if (!evento) return null;
                                
                                return e('div', {
                                    key: eventoId,
                                    style: {
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'space-between',
                                        padding: '8px',
                                        margin: '4px 0',
                                        backgroundColor: '#f9f9f9',
                                        borderRadius: '4px',
                                        border: '1px solid #e0e0e0'
                                    }
                                }, [
                                    e('span', {
                                        style: {
                                            flex: 1,
                                            fontSize: '13px',
                                            fontWeight: '500'
                                        }
                                    }, `${index + 1}. ${evento.title.rendered}`),
                                    
                                    e('div', {
                                        style: {
                                            display: 'flex',
                                            gap: '4px'
                                        }
                                    }, [
                                        // Botón subir
                                        index > 0 && e(Button, {
                                            isSmall: true,
                                            onClick: function() { moverEvento(eventoId, 'up'); },
                                            style: {
                                                minWidth: '30px',
                                                height: '30px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center'
                                            }
                                        }, '↑'),
                                        
                                        // Botón bajar
                                        index < eventosSeleccionados.length - 1 && e(Button, {
                                            isSmall: true,
                                            onClick: function() { moverEvento(eventoId, 'down'); },
                                            style: {
                                                minWidth: '30px',
                                                height: '30px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center'
                                            }
                                        }, '↓')
                                    ])
                                ]);
                            }))
                        ]),

                        !mostrarEventos && eventos.length === 0 && e('p', {
                            style: { 
                                fontStyle: 'italic', 
                                color: '#666',
                                marginTop: '20px'
                            }
                        }, 'No hay eventos disponibles para seleccionar.')
                    ])
                ]),

                // Block Content Preview
                e('section', {
                    className: 'block-eventos-swiper',
                    style: { 
                        position: 'relative',
                        minHeight: '400px',
                        background: eventos.length > 0 && eventos[0].featured_media_src_url ? 
                            `url(${eventos[0].featured_media_src_url}) center/cover no-repeat` : 
                            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                    },
                    key: 'eventos-preview'
                }, [
                    e('div', {
                        className: 'swiper eventos-swiper-preview'
                    }, [
                        e('div', {
                            className: 'swiper-wrapper'
                        }, [
                            e('div', {
                                className: 'swiper-slide slide-inicial'
                            }, [
                                e('div', {
                                    className: 'slide-background'
                                }),
                                e('div', {
                                    className: 'slide-overlay'
                                }),
                                e('div', {
                                    className: 'slide-content'
                                }, [
                                    e('div', {
                                        className: 'slide-inner'
                                    }, [
                                        // Título editable directamente
                                        e(RichText, {
                                            tagName: 'h2',
                                            className: 'slide-title',
                                            value: textoInicial,
                                            onChange: function(value) { 
                                                setAttributes({ textoInicial: value }); 
                                            },
                                            placeholder: 'Título del slide inicial...',
                                            style: { color: '#fff' }
                                        }),
                                        
                                        // Descripción editable directamente  
                                        e(RichText, {
                                            tagName: 'p',
                                            className: 'slide-description',
                                            value: descripcionInicial,
                                            onChange: function(value) { 
                                                setAttributes({ descripcionInicial: value }); 
                                            },
                                            placeholder: 'Descripción del slide inicial...',
                                            style: { color: '#fff' }
                                        }),

                                        // Información adicional
                                        mostrarEventos && e('div', {
                                            style: {
                                                marginTop: '20px',
                                                padding: '10px 20px',
                                                backgroundColor: 'rgba(255,255,255,0.2)',
                                                borderRadius: '4px',
                                                fontSize: '14px',
                                                color: '#fff'
                                            }
                                        }, `+ ${numeroEventos} eventos automáticos`),

                                        // Información de eventos seleccionados manualmente
                                        !mostrarEventos && eventosSeleccionados.length > 0 && e('div', {
                                            style: {
                                                marginTop: '20px',
                                                padding: '10px 20px',
                                                backgroundColor: 'rgba(255,255,255,0.2)',
                                                borderRadius: '4px',
                                                fontSize: '14px',
                                                color: '#fff'
                                            }
                                        }, `+ ${eventosSeleccionados.length} eventos seleccionados`),

                                        // Mensaje cuando no hay eventos seleccionados
                                        !mostrarEventos && eventosSeleccionados.length === 0 && e('div', {
                                            style: {
                                                marginTop: '20px',
                                                padding: '10px 20px',
                                                backgroundColor: 'rgba(255,255,255,0.15)',
                                                borderRadius: '4px',
                                                fontSize: '14px',
                                                border: '1px dashed rgba(255,255,255,0.3)',
                                                color: '#fff'
                                            }
                                        }, 'Selecciona eventos específicos en el panel lateral →')
                                    ])
                                ])
                            ])
                        ])
                    ]),
                    // Navegación del swiper
                    e('div', { className: 'swiper-pagination' }),
                    e('div', { className: 'swiper-button-next' }),
                    e('div', { className: 'swiper-button-prev' })
                ])
            ]);
        },

        save: function() {
            // Renderizado server-side
            return null;
        }
    });
})();