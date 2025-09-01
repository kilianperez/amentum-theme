/**
 * Eventos Swiper Block - Editor JavaScript
 * Bloque para crear un swiper fullscreen con eventos
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, RichText, MediaUpload } = wp.blockEditor;
    const { PanelBody, Button, ToggleControl, RangeControl } = wp.components;
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
            imagenInicial: {
                type: 'string',
                default: '/wp-content/themes/amentum/assets/images/evento-inicial.jpg'
            },
            mostrarEventos: {
                type: 'boolean',
                default: true
            },
            numeroEventos: {
                type: 'number',
                default: 5
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { textoInicial, descripcionInicial, imagenInicial, mostrarEventos, numeroEventos } = attributes;

            function onSelectImage(media) {
                setAttributes({ imagenInicial: media.url });
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

                        e('h4', { style: { margin: '20px 0 10px 0' } }, 'Imagen inicial'),
                        e(MediaUpload, {
                            onSelect: onSelectImage,
                            allowedTypes: ['image'],
                            value: imagenInicial,
                            render: function(obj) {
                                return e(Button, {
                                    className: imagenInicial ? 'image-button' : 'button button-large',
                                    onClick: obj.open
                                }, 
                                imagenInicial ? 'Cambiar imagen inicial' : 'Seleccionar imagen inicial'
                                );
                            }
                        })
                    ])
                ]),

                // Block Content Preview
                e('div', {
                    className: 'eventos-swiper-preview',
                    style: { 
                        border: '2px dashed #ddd',
                        padding: '40px 20px',
                        backgroundColor: 'transparent',
                        borderRadius: '8px',
                        position: 'relative',
                        minHeight: '400px',
                        background: imagenInicial ? `url(${imagenInicial}) center/cover no-repeat` : '#f0f0f0'
                    },
                    key: 'eventos-preview'
                }, [
                    // Overlay
                    e('div', {
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            backgroundColor: 'rgba(0,0,0,0.4)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            flexDirection: 'column',
                            color: '#fff',
                            textAlign: 'center'
                        }
                    }, [
                        // Título editable directamente
                        e(RichText, {
                            tagName: 'h2',
                            className: 'swiper-titulo-editor',
                            value: textoInicial,
                            onChange: function(value) { 
                                setAttributes({ textoInicial: value }); 
                            },
                            placeholder: 'Título del slide inicial...'
                        }),
                        
                        // Descripción editable directamente
                        e(RichText, {
                            tagName: 'p',
                            className: 'swiper-descripcion-editor',
                            value: descripcionInicial,
                            onChange: function(value) { 
                                setAttributes({ descripcionInicial: value }); 
                            },
                            placeholder: 'Descripción del slide inicial...'
                        }),

                        // Información adicional
                        mostrarEventos && e('div', {
                            style: {
                                marginTop: '20px',
                                padding: '10px 20px',
                                backgroundColor: 'rgba(255,255,255,0.2)',
                                borderRadius: '4px',
                                fontSize: '14px'
                            }
                        }, `+ ${numeroEventos} eventos automáticos`)
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