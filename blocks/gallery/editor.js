(function(blocks, element, blockEditor, components) {
    const el = element.createElement;
    const { registerBlockType } = blocks;
    const { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = blockEditor;
    const { PanelBody, TextControl, Button } = components;
    const { Fragment } = element;

    // Obtener URL del theme de forma segura
    const themeUrl = (window.amentumGalleryData && window.amentumGalleryData.themeUrl) || '/wp-content/themes/amentum';
    
    // Imágenes predefinidas simplificadas
    const defaultImages = [
        {
            id: 'gallery-1',
            url: themeUrl + '/assets/img/template/gallery/wedding-venue-1.jpg',
            alt: 'Espacio elegante para eventos'
        },
        {
            id: 'gallery-2',
            url: themeUrl + '/assets/img/template/gallery/wedding-table-1.jpg',
            alt: 'Mesa elegantemente decorada'
        },
        {
            id: 'gallery-3',
            url: themeUrl + '/assets/img/template/gallery/wedding-couple-1.jpg',
            alt: 'Momentos especiales capturados'
        },
        {
            id: 'gallery-4',
            url: themeUrl + '/assets/img/template/gallery/wedding-ceremony-1.jpg',
            alt: 'Ceremonia al aire libre'
        },
        {
            id: 'gallery-5',
            url: themeUrl + '/assets/img/template/gallery/wedding-decoration-1.jpg',
            alt: 'Decoración artesanal'
        },
        {
            id: 'gallery-6',
            url: themeUrl + '/assets/img/template/gallery/wedding-outdoor-1.jpg',
            alt: 'Evento al aire libre'
        }
    ];

    registerBlockType('amentum/gallery', {
        title: 'Gallery',
        icon: 'format-gallery',
        category: 'amentum-blocks',
        supports: {
            align: ['wide', 'full'],
            className: true
        },
        attributes: {
            title: {
                type: 'string',
                default: 'Galería'
            },
            images: {
                type: 'array',
                default: []
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, images } = attributes;

            const blockProps = useBlockProps({
                className: 'block-gallery-horizontal gallery-editor-preview'
            });

            // Usar imágenes predefinidas si no hay imágenes seleccionadas
            const displayImages = images.length > 0 ? images : defaultImages;

            const onSelectImages = (media) => {
                const newImages = media.map((item) => ({
                    id: item.id,
                    url: item.url,
                    alt: item.alt || ''
                }));
                setAttributes({ images: newImages });
            };

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: 'Configuración de la Galería', initialOpen: true },
                        el(TextControl, {
                            label: 'Título',
                            value: title,
                            onChange: (value) => setAttributes({ title: value })
                        })
                    )
                ),
                el(
                    'section',
                    blockProps,
                    el(
                        'div',
                        { className: 'gallery-header' },
                        el(
                            'h2',
                            { className: 'gallery-title' },
                            title
                        )
                    ),
                    el(
                        'div',
                        { className: 'gallery-container' },
                        el(
                            'div',
                            { className: 'gallery-images-wrapper' },
                            el(
                                MediaUploadCheck,
                                {},
                                el(
                                    MediaUpload,
                                    {
                                        onSelect: onSelectImages,
                                        allowedTypes: ['image'],
                                        multiple: true,
                                        gallery: true,
                                        value: images.map(img => img.id),
                                        render: ({ open }) => el(
                                            'div',
                                            { className: 'gallery-editor-content' },
                                            el(
                                                Button,
                                                {
                                                    onClick: open,
                                                    className: 'button button-large',
                                                    style: { 
                                                        marginBottom: '20px',
                                                        display: 'block',
                                                        width: '100%'
                                                    }
                                                },
                                                images.length === 0 ? 'Seleccionar Imágenes' : 'Cambiar Imágenes'
                                            ),
                                            el(
                                                'div',
                                                { className: 'gallery-preview' },
                                                displayImages.map((image, index) => el(
                                                    'div',
                                                    {
                                                        key: image.id || index,
                                                        className: 'gallery-image',
                                                        'data-index': index + 1
                                                    },
                                                    el('img', {
                                                        src: image.url,
                                                        alt: image.alt,
                                                        loading: 'lazy',
                                                        style: {
                                                            width: '100%',
                                                            height: 'auto',
                                                            borderRadius: '4px',
                                                            margin: '5px',
                                                            maxWidth: '150px'
                                                        }
                                                    })
                                                )),
                                                images.length > 0 && el(
                                                    'p',
                                                    { 
                                                        style: { 
                                                            marginTop: '15px',
                                                            textAlign: 'center',
                                                            color: '#666',
                                                            fontSize: '14px'
                                                        }
                                                    },
                                                    `${images.length} imagen${images.length !== 1 ? 'es' : ''} seleccionada${images.length !== 1 ? 's' : ''}`
                                                )
                                            )
                                        )
                                    }
                                )
                            ),
                            el('div', { className: 'gallery-spacer' })
                        )
                    )
                )
            );
        },

        save: function() {
            // El renderizado se hace en PHP
            return null;
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);