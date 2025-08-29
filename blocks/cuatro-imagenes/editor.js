/**
 * Cuatro Imágenes Block - Editor JavaScript
 * Bloque para mostrar 4 imágenes en diseño responsive
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, RichText, MediaUpload } = wp.blockEditor;
    const { PanelBody, Button } = wp.components;
    const { createElement: e, Fragment } = wp.element;

    registerBlockType('amentum/cuatro-imagenes', {
        title: 'Cuatro Imágenes',
        icon: 'format-gallery',
        category: 'amentum-blocks',
        attributes: {
            imagen1: {
                type: 'string',
                default: '/wp-content/themes/amentum/assets/images/galeria-proyecto-1.png'
            },
            imagen2: {
                type: 'string',
                default: '/wp-content/themes/amentum/assets/images/galeria-proyecto-2.png'
            },
            imagen3: {
                type: 'string',
                default: '/wp-content/themes/amentum/assets/images/galeria-proyecto-3.png'
            },
            imagen4: {
                type: 'string',
                default: '/wp-content/themes/amentum/assets/images/galeria-proyecto-4.png'
            },
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { imagen1, imagen2, imagen3, imagen4 } = attributes;

            function onSelectImage(imageNumber) {
                return function(media) {
                    const attrName = 'imagen' + imageNumber;
                    setAttributes({ [attrName]: media.url });
                };
            }

            return e(Fragment, {}, [
                // Inspector Controls (Sidebar)
                e(InspectorControls, {}, [
                    e(PanelBody, { title: 'Configuración de Imágenes', initialOpen: true }, [
                        // Imagen 1
                        e('h4', { style: { margin: '20px 0 10px 0' } }, 'Proyecto 1'),
                        e(MediaUpload, {
                            onSelect: onSelectImage(1),
                            allowedTypes: ['image'],
                            value: imagen1,
                            render: function(obj) {
                                return e(Button, {
                                    className: imagen1 ? 'image-button' : 'button button-large',
                                    onClick: obj.open
                                }, 
                                imagen1 ? 'Cambiar Proyecto 1' : 'Seleccionar Proyecto 1'
                                );
                            }
                        }),

                        // Imagen 2
                        e('h4', { style: { margin: '20px 0 10px 0' } }, 'Proyecto 2'),
                        e(MediaUpload, {
                            onSelect: onSelectImage(2),
                            allowedTypes: ['image'],
                            value: imagen2,
                            render: function(obj) {
                                return e(Button, {
                                    className: imagen2 ? 'image-button' : 'button button-large',
                                    onClick: obj.open
                                }, 
                                imagen2 ? 'Cambiar Proyecto 2' : 'Seleccionar Proyecto 2'
                                );
                            }
                        }),

                        // Imagen 3
                        e('h4', { style: { margin: '20px 0 10px 0' } }, 'Proyecto 3'),
                        e(MediaUpload, {
                            onSelect: onSelectImage(3),
                            allowedTypes: ['image'],
                            value: imagen3,
                            render: function(obj) {
                                return e(Button, {
                                    className: imagen3 ? 'image-button' : 'button button-large',
                                    onClick: obj.open
                                }, 
                                imagen3 ? 'Cambiar Proyecto 3' : 'Seleccionar Proyecto 3'
                                );
                            }
                        }),

                        // Imagen 4
                        e('h4', { style: { margin: '20px 0 10px 0' } }, 'Proyecto 4'),
                        e(MediaUpload, {
                            onSelect: onSelectImage(4),
                            allowedTypes: ['image'],
                            value: imagen4,
                            render: function(obj) {
                                return e(Button, {
                                    className: imagen4 ? 'image-button' : 'button button-large',
                                    onClick: obj.open
                                }, 
                                imagen4 ? 'Cambiar Proyecto 4' : 'Seleccionar Proyecto 4'
                                );
                            }
                        })
                    ])
                ]),

                // Block Content Preview
                e('div', {
                    className: 'cuatro-imagenes-preview',
                    style: { 
                        border: '2px dashed #ddd',
                        padding: '20px',
                        backgroundColor: '#f8f6f3'
                    }
                }, [
                    // Grid de imágenes preview
                    e('div', {
                        style: {
                            display: 'grid',
                            gridTemplateColumns: 'repeat(4, 1fr)',
                            gap: '15px',
                            maxWidth: '800px',
                            margin: '0 auto'
                        }
                    }, [
                        // Imagen 1
                        e('div', {
                            style: {
                                aspectRatio: '3/4',
                                backgroundColor: '#e0e0e0',
                                overflow: 'hidden'
                            }
                        }, [
                            imagen1 ? e('img', {
                                src: imagen1,
                                style: {
                                    width: '100%',
                                    height: '100%',
                                    objectFit: 'cover'
                                }
                            }) : e('div', {
                                style: {
                                    height: '100%',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '12px',
                                    color: '#999'
                                }
                            }, 'Proyecto 1')
                        ]),

                        // Imagen 2
                        e('div', {
                            style: {
                                aspectRatio: '3/4',
                                backgroundColor: '#e0e0e0',
                                overflow: 'hidden'
                            }
                        }, [
                            imagen2 ? e('img', {
                                src: imagen2,
                                style: {
                                    width: '100%',
                                    height: '100%',
                                    objectFit: 'cover'
                                }
                            }) : e('div', {
                                style: {
                                    height: '100%',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '12px',
                                    color: '#999'
                                }
                            }, 'Proyecto 2')
                        ]),

                        // Imagen 3
                        e('div', {
                            style: {
                                aspectRatio: '3/4',
                                backgroundColor: '#e0e0e0',
                                overflow: 'hidden'
                            }
                        }, [
                            imagen3 ? e('img', {
                                src: imagen3,
                                style: {
                                    width: '100%',
                                    height: '100%',
                                    objectFit: 'cover'
                                }
                            }) : e('div', {
                                style: {
                                    height: '100%',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '12px',
                                    color: '#999'
                                }
                            }, 'Proyecto 3')
                        ]),

                        // Imagen 4
                        e('div', {
                            style: {
                                aspectRatio: '3/4',
                                backgroundColor: '#e0e0e0',
                                overflow: 'hidden'
                            }
                        }, [
                            imagen4 ? e('img', {
                                src: imagen4,
                                style: {
                                    width: '100%',
                                    height: '100%',
                                    objectFit: 'cover'
                                }
                            }) : e('div', {
                                style: {
                                    height: '100%',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '12px',
                                    color: '#999'
                                }
                            }, 'Proyecto 4')
                        ])
                    ]),

                ])
            ]);
        },

        save: function() {
            // Renderizado server-side
            return null;
        }
    });
})();