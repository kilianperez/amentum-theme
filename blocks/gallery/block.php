<?php
/**
 * Gallery Block - Galería con Layout Asimétrico
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Gallery
 */
function amentum_register_gallery_block() {
    error_log('GALLERY BLOCK: Registrando bloque gallery');
    register_block_type('amentum/gallery', array(
        'render_callback' => 'amentum_render_gallery_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Galería'
            ),
            'images' => array(
                'type' => 'array',
                'default' => array()
            )
        )
    ));
}
add_action('init', 'amentum_register_gallery_block');

/**
 * Obtener imágenes predefinidas para galería horizontal
 */
function amentum_get_default_gallery_images() {
    $template_uri = get_template_directory_uri();
    return array(
        array(
            'id' => 'gallery-1',
            'url' => $template_uri . '/assets/img/template/gallery/wedding-venue-1.jpg',
            'alt' => 'Espacio elegante para eventos'
        ),
        array(
            'id' => 'gallery-2',
            'url' => $template_uri . '/assets/img/template/gallery/wedding-table-1.jpg',
            'alt' => 'Mesa elegantemente decorada'
        ),
        array(
            'id' => 'gallery-3',
            'url' => $template_uri . '/assets/img/template/gallery/wedding-couple-1.jpg',
            'alt' => 'Momentos especiales capturados'
        ),
        array(
            'id' => 'gallery-4',
            'url' => $template_uri . '/assets/img/template/gallery/wedding-ceremony-1.jpg',
            'alt' => 'Ceremonia al aire libre'
        ),
        array(
            'id' => 'gallery-5',
            'url' => $template_uri . '/assets/img/template/gallery/wedding-decoration-1.jpg',
            'alt' => 'Decoración artesanal'
        ),
        array(
            'id' => 'gallery-6',
            'url' => $template_uri . '/assets/img/template/gallery/wedding-outdoor-1.jpg',
            'alt' => 'Evento al aire libre'
        )
    );
}

/**
 * Render callback para el bloque Gallery
 */
function amentum_render_gallery_block($attributes) {
    // Extraer atributos con valores por defecto
    $title = isset($attributes['title']) ? esc_html($attributes['title']) : 'Galería';
    $images = isset($attributes['images']) && !empty($attributes['images']) ? $attributes['images'] : amentum_get_default_gallery_images();
    
    // Generar ID único para esta instancia
    $unique_id = 'gallery-' . uniqid();
    
    // Comenzar el output
    ob_start();
    ?>
    
    <section class="block-gallery-horizontal" id="<?php echo esc_attr($unique_id); ?>">
        <!-- Header centrado -->
        <div class="gallery-header">
            <h2 class="gallery-title"><?php echo esc_html($title); ?></h2>
        </div>
        
        <!-- Contenedor principal con altura máxima 100vh -->
        <div class="gallery-container">
            <!-- Contenedor de todas las imágenes en una sola fila horizontal -->
            <div class="gallery-images-wrapper">
                <?php foreach ($images as $index => $image): 
                    $image_url = isset($image['url']) ? $image['url'] : '';
                    $image_alt = isset($image['alt']) ? $image['alt'] : 'Imagen de galería';
                    
                    if (empty($image_url)) continue;
                ?>
                    <!-- Imagen con object-fit cover -->
                    <div class="gallery-image" data-index="<?php echo esc_attr($index + 1); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" 
                             alt="<?php echo esc_attr($image_alt); ?>"
                             loading="lazy">
                    </div>
                <?php endforeach; ?>
                
                <!-- Espacio extra al final -->
                <div class="gallery-spacer"></div>
            </div>
        </div>
    </section>
    
    <script>
    // Función para inicializar galería - compatible con Barba.js
    function initGallery_<?php echo esc_js(str_replace('-', '_', $unique_id)); ?>() {
        const galleryId = '<?php echo esc_js($unique_id); ?>';
        const container = document.getElementById(galleryId);
        
        if (!container) return; // Si no existe el contenedor, salir
        
        const galleryContainer = container.querySelector('.gallery-container');
        const galleryWrapper = container.querySelector('.gallery-images-wrapper');
        const images = container.querySelectorAll('.gallery-image');
        
        if (!container || !galleryContainer || !galleryWrapper || images.length === 0) return;
        
        // Verificar si GSAP está disponible
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            // Registrar ScrollTrigger
            gsap.registerPlugin(ScrollTrigger);
            
            // Imágenes visibles desde el inicio
            
            // Scroll horizontal - fórmula simple que funciona
            gsap.to(galleryWrapper, {
                x: () => -(galleryWrapper.scrollWidth - galleryContainer.offsetWidth),
                ease: "none",
                scrollTrigger: {
                    trigger: galleryContainer,
                    pin: true,
                    scrub: 1,
                    start: "top top",
                    end: () => `+=${galleryWrapper.scrollWidth - galleryContainer.offsetWidth}`,
                    invalidateOnRefresh: true
                }
            });
            
            // Sin efectos hover - solo lightbox
            images.forEach(image => {
                const img = image.querySelector('img');
                
                // Click para lightbox
                image.addEventListener('click', () => {
                    const imgSrc = img.src;
                    const imgAlt = img.alt;
                    
                    // Crear lightbox simple
                    const lightbox = document.createElement('div');
                    lightbox.className = 'gallery-lightbox';
                    lightbox.innerHTML = `
                        <div class="lightbox-overlay">
                            <div class="lightbox-content">
                                <img src="${imgSrc}" alt="${imgAlt}">
                                <button class="lightbox-close">&times;</button>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(lightbox);
                    document.body.style.overflow = 'hidden';
                    
                    // Animación de entrada
                    gsap.fromTo(lightbox, 
                        { opacity: 0 },
                        { opacity: 1, duration: 0.3 }
                    );
                    
                    gsap.fromTo(lightbox.querySelector('.lightbox-content'), 
                        { scale: 0.8, opacity: 0 },
                        { scale: 1, opacity: 1, duration: 0.4, ease: "power2.out" }
                    );
                    
                    // Cerrar lightbox
                    const closeLightbox = () => {
                        gsap.to(lightbox, {
                            opacity: 0,
                            duration: 0.3,
                            onComplete: () => {
                                document.body.removeChild(lightbox);
                                document.body.style.overflow = '';
                            }
                        });
                    };
                    
                    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
                    lightbox.querySelector('.lightbox-overlay').addEventListener('click', (e) => {
                        if (e.target === e.currentTarget) closeLightbox();
                    });
                });
            });
        } else {
            // Fallback sin GSAP - imágenes estáticas
            console.warn('GSAP no está disponible para el scroll horizontal');
        }
    }
    
    // Ejecutar en múltiples eventos para compatibilidad con Barba.js
    document.addEventListener('DOMContentLoaded', initGallery_<?php echo esc_js(str_replace('-', '_', $unique_id)); ?>);
    
    // Ejecutar inmediatamente si DOM ya está listo
    if (document.readyState !== 'loading') {
        initGallery_<?php echo esc_js(str_replace('-', '_', $unique_id)); ?>();
    }
    
    // Compatibilidad con Barba.js
    if (typeof barba !== 'undefined') {
        barba.hooks.after(() => {
            setTimeout(() => initGallery_<?php echo esc_js(str_replace('-', '_', $unique_id)); ?>(), 100);
        });
    }
    
    // Evento personalizado para recargas manuales
    document.addEventListener('amentum:pageLoaded', initGallery_<?php echo esc_js(str_replace('-', '_', $unique_id)); ?>);
    </script>
    
    <?php
    return ob_get_clean();
}

/**
 * Enqueue assets del bloque gallery
 */
function amentum_gallery_block_assets() {
    if (has_block('amentum/gallery')) {
        // Enqueue GSAP y ScrollTrigger desde CDN
        wp_enqueue_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
            array(),
            '3.12.2',
            true
        );
        
        wp_enqueue_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
            array('gsap'),
            '3.12.2',
            true
        );
        
        // Registrar ScrollTrigger
        wp_add_inline_script('gsap-scrolltrigger', 'gsap.registerPlugin(ScrollTrigger);');
    }
}
add_action('wp_enqueue_scripts', 'amentum_gallery_block_assets');

/**
 * Localizar script del editor con datos del theme
 */
function amentum_gallery_editor_assets() {
    wp_localize_script(
        'amentum-gallery-editor',
        'amentumGalleryData',
        array(
            'themeUrl' => get_template_directory_uri()
        )
    );
}
add_action('enqueue_block_editor_assets', 'amentum_gallery_editor_assets', 20);