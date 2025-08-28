/**
 * Hero Full Block - Editor JavaScript
 * Bloque de hero a pantalla completa con imagen de fondo
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, MediaUpload, RichText, ColorPalette } = wp.blockEditor;
    const { PanelBody, Button, RangeControl, SelectControl } = wp.components;
    const { createElement: e } = wp.element;

    registerBlockType('amentum/hero-full', {
        title: 'Hero Full Screen',
        icon: 'format-image',
        category: 'amentum-blocks',
        attributes: {
            superTitle: {
                type: 'string',
                default: 'En'
            },
            title: {
                type: 'string',
                default: 'Amentum Events'
            },
            subtitle: {
                type: 'string',
                default: 'creamos experiencias que dejan huella.'
            },
            backgroundImage: {
                type: 'string',
                default: '/wp-content/themes/amentum/assets/images/amentum-events-bg.png'
            },
            overlayOpacity: {
                type: 'number',
                default: 0.5
            },
            textColor: {
                type: 'string',
                default: 'white'
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { superTitle, title, subtitle, backgroundImage, overlayOpacity, textColor } = attributes;

            const onSelectImage = function(media) {
                setAttributes({
                    backgroundImage: media.url
                });
            };

            const colorOptions = [
                { name: 'Blanco', color: '#ffffff' },
                { name: 'Negro', color: '#000000' },
                { name: 'Gris', color: '#666666' },
                { name: 'Azul', color: '#007cba' },
                { name: 'Verde', color: '#46b450' }
            ];

            return e('div', { className: 'amentum-hero-full-editor' }, [
                // Inspector Controls (Sidebar)
                e(InspectorControls, {}, [
                    e(PanelBody, { title: 'Configuración de Fondo', initialOpen: true }, [
                        e('p', { style: { marginBottom: '10px' } }, 'Imagen de fondo:'),
                        e(MediaUpload, {
                            onSelect: onSelectImage,
                            allowedTypes: ['image'],
                            value: backgroundImage,
                            render: function(obj) {
                                return e(Button, {
                                    onClick: obj.open,
                                    className: backgroundImage ? 'button' : 'button button-large'
                                }, backgroundImage ? 'Cambiar imagen' : 'Seleccionar imagen');
                            }
                        }),
                        
                        backgroundImage && e('div', { style: { marginTop: '10px' } }, [
                            e('img', {
                                src: backgroundImage,
                                style: { maxWidth: '100%', height: 'auto' }
                            })
                        ]),

                        e(RangeControl, {
                            label: 'Opacidad del overlay',
                            value: overlayOpacity,
                            onChange: function(value) {
                                setAttributes({ overlayOpacity: value });
                            },
                            min: 0,
                            max: 1,
                            step: 0.1
                        })
                    ]),

                    e(PanelBody, { title: 'Configuración de Texto', initialOpen: false }, [
                        e('p', { style: { marginBottom: '10px' } }, 'Color del texto:'),
                        e(ColorPalette, {
                            colors: colorOptions,
                            value: textColor,
                            onChange: function(value) {
                                setAttributes({ textColor: value || 'white' });
                            }
                        })
                    ])
                ]),

                // Block Content
                e('div', { 
                    className: 'hero-full-preview',
                    style: {
                        minHeight: '400px',
                        position: 'relative',
                        backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        color: textColor,
                        padding: '40px 20px'
                    }
                }, [
                    // Overlay
                    e('div', {
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            background: `rgba(0, 0, 0, ${overlayOpacity})`,
                            zIndex: 1
                        }
                    }),

                    // Content
                    e('div', {
                        style: {
                            position: 'relative',
                            zIndex: 2,
                            textAlign: 'center',
                            maxWidth: '800px'
                        }
                    }, [
                        // Super Title
                        e(RichText, {
                            tagName: 'div',
                            className: 'hero-full-super-title',
                            style: {
                                fontSize: '64px',
                                fontWeight: '400',
                                lineHeight: '120%',
                                margin: '0',
                                fontFamily: "'Adios Script Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
                                textShadow: '2px 2px 4px rgba(0, 0, 0, 0.5)'
                            },
                            value: superTitle,
                            onChange: function(value) {
                                setAttributes({ superTitle: value });
                            },
                            placeholder: 'Super título...'
                        }),

                        // Title
                        e(RichText, {
                            tagName: 'h1',
                            className: 'hero-full-title',
                            style: {
                                fontSize: '64px',
                                fontWeight: '400',
                                lineHeight: '120%',
                                margin: '0',
                                fontFamily: "'Adios Script Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
                                textTransform: 'uppercase',
                                textShadow: '2px 2px 4px rgba(0, 0, 0, 0.5)'
                            },
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            },
                            placeholder: 'Título principal...'
                        }),

                        // Subtitle
                        e(RichText, {
                            tagName: 'div',
                            className: 'hero-full-subtitle',
                            style: {
                                fontSize: '64px',
                                fontWeight: '400',
                                lineHeight: '120%',
                                margin: '0',
                                fontFamily: "'Adios Script Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
                                textShadow: '1px 1px 2px rgba(0, 0, 0, 0.5)'
                            },
                            value: subtitle,
                            onChange: function(value) {
                                setAttributes({ subtitle: value });
                            },
                            placeholder: 'Subtítulo...'
                        })
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