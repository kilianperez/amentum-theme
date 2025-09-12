/**
 * Eventos Destacados Block - Editor JavaScript
 * Bloque para mostrar 4 eventos destacados en diseño responsive
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { RichText, MediaUpload } = wp.blockEditor;
    const { Button } = wp.components;
    const { createElement: e, Fragment } = wp.element;

    registerBlockType('amentum/eventos-destacados', {
        title: 'Eventos Destacados',
        icon: 'format-gallery',
        category: 'amentum-blocks',
        attributes: {
            titulo: {
                type: 'string',
                default: 'Sobre nosotros'
            },
            descripcion: {
                type: 'string',
                default: 'Amentum nace de la pasión por la gastronomía, la excelencia en los detalles y el arte de reunir a las personas.'
            },
            imagen1: {
                type: 'string',
                default: (typeof wpGlobalSettings !== 'undefined' && wpGlobalSettings.templateDirectoryUri) ? 
                    wpGlobalSettings.templateDirectoryUri + '/assets/images/galeria-proyecto-1.png' : 
                    '/wp-content/themes/amentum/assets/images/galeria-proyecto-1.png'
            },
            imagen2: {
                type: 'string',
                default: (typeof wpGlobalSettings !== 'undefined' && wpGlobalSettings.templateDirectoryUri) ? 
                    wpGlobalSettings.templateDirectoryUri + '/assets/images/galeria-proyecto-2.png' : 
                    '/wp-content/themes/amentum/assets/images/galeria-proyecto-2.png'
            },
            imagen3: {
                type: 'string',
                default: (typeof wpGlobalSettings !== 'undefined' && wpGlobalSettings.templateDirectoryUri) ? 
                    wpGlobalSettings.templateDirectoryUri + '/assets/images/galeria-proyecto-3.png' : 
                    '/wp-content/themes/amentum/assets/images/galeria-proyecto-3.png'
            },
            imagen4: {
                type: 'string',
                default: (typeof wpGlobalSettings !== 'undefined' && wpGlobalSettings.templateDirectoryUri) ? 
                    wpGlobalSettings.templateDirectoryUri + '/assets/images/galeria-proyecto-4.png' : 
                    '/wp-content/themes/amentum/assets/images/galeria-proyecto-4.png'
            },
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { titulo, descripcion, imagen1, imagen2, imagen3, imagen4 } = attributes;

            function onSelectImage(imageNumber) {
                return function(media) {
                    const attrName = 'imagen' + imageNumber;
                    setAttributes({ [attrName]: media.url });
                };
            }

            return e('section', {
                className: 'block-eventos-destacados',
                key: 'eventos-preview'
            }, [
                e('div', {
                    className: 'container'
                }, [
                // Header editable directamente
                e('div', {
                    className: 'eventos-destacados-header'
                }, [
                    // Título editable directamente
                    e(RichText, {
                        tagName: 'h2',
                        className: 'eventos-titulo-editor',
                        value: titulo,
                        onChange: function(value) { 
                            setAttributes({ titulo: value }); 
                        },
                        placeholder: 'Título de la sección...'
                    }),
                    // Descripción editable directamente
                    e(RichText, {
                        tagName: 'p',
                        className: 'eventos-descripcion-editor',
                        value: descripcion,
                        onChange: function(value) { 
                            setAttributes({ descripcion: value }); 
                        },
                        placeholder: 'Descripción de la sección...'
                    }),
                    // Enlace estático
                    e('div', {
                        className: 'eventos-link'
                    }, [
                        e('a', {
                            href: '#eventos',
                        }, 'Nuestros eventos')
                    ])
                ]),

                // Grid de imágenes con controles directos
                e('div', {
                    className: 'eventos-destacados-grid'
                }, [
                    // Imagen 1 - Click para cambiar
                    e(MediaUpload, {
                        onSelect: onSelectImage(1),
                        allowedTypes: ['image'],
                        value: imagen1,
                        render: function(obj) {
                            return e('div', {
                                className: 'imagen-item',
                                onClick: obj.open,
                                style: { cursor: 'pointer' }
                            }, [
                                imagen1 ? e('img', {
                                    src: imagen1,
                                    alt: 'Evento destacado 1',
                                    loading: 'lazy'
                                }) : e('div', {
                                    style: {
                                        height: '100%',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontSize: '12px',
                                        color: '#999',
                                        flexDirection: 'column',
                                        backgroundColor: '#e0e0e0'
                                    }
                                }, [
                                    e('div', {}, 'Click para'),
                                    e('div', {}, 'seleccionar'),
                                    e('div', {}, 'Evento 1')
                                ])
                            ]);
                        }
                    }),

                    // Imagen 2 - Click para cambiar
                    e(MediaUpload, {
                        onSelect: onSelectImage(2),
                        allowedTypes: ['image'],
                        value: imagen2,
                        render: function(obj) {
                            return e('div', {
                                className: 'imagen-item',
                                onClick: obj.open,
                                style: { cursor: 'pointer' }
                            }, [
                                imagen2 ? e('img', {
                                    src: imagen2,
                                    alt: 'Evento destacado 2',
                                    loading: 'lazy'
                                }) : e('div', {
                                    style: {
                                        height: '100%',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontSize: '12px',
                                        color: '#999',
                                        flexDirection: 'column',
                                        backgroundColor: '#e0e0e0'
                                    }
                                }, [
                                    e('div', {}, 'Click para'),
                                    e('div', {}, 'seleccionar'),
                                    e('div', {}, 'Evento 2')
                                ])
                            ]);
                        }
                    }),

                    // Imagen 3 - Click para cambiar
                    e(MediaUpload, {
                        onSelect: onSelectImage(3),
                        allowedTypes: ['image'],
                        value: imagen3,
                        render: function(obj) {
                            return e('div', {
                                className: 'imagen-item',
                                onClick: obj.open,
                                style: { cursor: 'pointer' }
                            }, [
                                imagen3 ? e('img', {
                                    src: imagen3,
                                    alt: 'Evento destacado 3',
                                    loading: 'lazy'
                                }) : e('div', {
                                    style: {
                                        height: '100%',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontSize: '12px',
                                        color: '#999',
                                        flexDirection: 'column',
                                        backgroundColor: '#e0e0e0'
                                    }
                                }, [
                                    e('div', {}, 'Click para'),
                                    e('div', {}, 'seleccionar'),
                                    e('div', {}, 'Evento 3')
                                ])
                            ]);
                        }
                    }),

                    // Imagen 4 - Click para cambiar
                    e(MediaUpload, {
                        onSelect: onSelectImage(4),
                        allowedTypes: ['image'],
                        value: imagen4,
                        render: function(obj) {
                            return e('div', {
                                className: 'imagen-item',
                                onClick: obj.open,
                                style: { cursor: 'pointer' }
                            }, [
                                imagen4 ? e('img', {
                                    src: imagen4,
                                    alt: 'Evento destacado 4',
                                    loading: 'lazy'
                                }) : e('div', {
                                    style: {
                                        height: '100%',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontSize: '12px',
                                        color: '#999',
                                        flexDirection: 'column',
                                        backgroundColor: '#e0e0e0'
                                    }
                                }, [
                                    e('div', {}, 'Click para'),
                                    e('div', {}, 'seleccionar'),
                                    e('div', {}, 'Evento 4')
                                ])
                            ]);
                        }
                    })
                ])
                ])
            ]);
        },

        save: function() {
            // Renderizado server-side
            return null;
        }
    });
})();