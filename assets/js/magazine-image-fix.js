/**
 * Solución para border-radius con object-fit: contain
 * Crea un elemento overlay que se adapta exactamente al tamaño de la imagen
 */

// Función global para inicializar imágenes de magazine
function initMagazineImages() {
    // Solo ejecutar si estamos en una página con elementos magazine
    if (!document.querySelector('.magazine-images, .magazine-swiper')) {
        return;
    }
    
    console.log('Initializing magazine images...');
    
    function processImages() {
        // Seleccionar todas las imágenes dentro de .magazine-images y .magazine-swiper (más específico)
        const magazineImages = document.querySelectorAll('.magazine-images .magazine-image img, .magazine-images .magazine-image video, .magazine-swiper .swiper-slide img, .magazine-swiper .swiper-slide video');
        console.log('Found magazine images:', magazineImages.length);
        
        magazineImages.forEach(function(img) {
            // Encontrar el contenedor (.magazine-image o .swiper-slide)
            const container = img.closest('.magazine-image') || img.closest('.swiper-slide');
            
            if (container && !container.querySelector('[data-magazine-overlay]')) {
                createBorderOverlay(container, img);
            }
        });
    }
    
    function createBorderOverlay(magazineContainer, img) {        
        // Crear el elemento overlay
        const overlay = document.createElement('div');
        overlay.className = 'border-overlay';
        overlay.setAttribute('data-magazine-overlay', 'true');
        
        // Función para calcular las dimensiones reales de la imagen con object-fit: contain
        function calculateImageDimensions() {
            const containerWidth = img.offsetWidth;
            const containerHeight = img.offsetHeight;
            const imageAspectRatio = img.naturalWidth / img.naturalHeight;
            const containerAspectRatio = containerWidth / containerHeight;
            
            let renderedWidth, renderedHeight, offsetX, offsetY;
            
            if (imageAspectRatio > containerAspectRatio) {
                // La imagen es más ancha proporcionalmente, se ajusta al ancho
                renderedWidth = containerWidth;
                renderedHeight = containerWidth / imageAspectRatio;
                offsetX = 0;
                offsetY = (containerHeight - renderedHeight) / 2;
            } else {
                // La imagen es más alta proporcionalmente, se ajusta a la altura
                renderedWidth = containerHeight * imageAspectRatio;
                renderedHeight = containerHeight;
                offsetX = (containerWidth - renderedWidth) / 2;
                offsetY = 0;
            }
            
            return { renderedWidth, renderedHeight, offsetX, offsetY };
        }
        
        // Función para actualizar el overlay basado en el tamaño real de la imagen
        function updateOverlay() {
            // Calcular las dimensiones reales de la imagen con object-fit: contain
            const { renderedWidth, renderedHeight, offsetX, offsetY } = calculateImageDimensions();
            
            // Ocultar la imagen original
            img.style.opacity = '0';
            
            // Crear un elemento que muestre solo la parte recortada de la imagen
            overlay.style.cssText = `
                position: absolute;
                left: ${offsetX}px;
                top: ${offsetY}px;
                width: ${renderedWidth}px;
                height: ${renderedHeight}px;
                background-image: url('${img.src}');
                background-size: ${renderedWidth}px ${renderedHeight}px;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 0.5rem;
                overflow: hidden;
                pointer-events: none;
                z-index: 10;
            `;
            
            console.log('Overlay updated:', {
                container: { width: img.offsetWidth, height: img.offsetHeight },
                image: { width: img.naturalWidth, height: img.naturalHeight },
                rendered: { width: renderedWidth, height: renderedHeight },
                position: { x: offsetX, y: offsetY }
            });
        }
        
        // Añadir el overlay al contenedor
        magazineContainer.appendChild(overlay);
        
        // Actualizar inicialmente
        if (img.complete || img.tagName === 'VIDEO') {
            updateOverlay();
        } else {
            img.addEventListener('load', updateOverlay);
        }
        
        // Actualizar en resize para mantener la sincronización
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(updateOverlay, 100);
        });
        
        // También observar cambios en el tamaño de la imagen
        if (typeof ResizeObserver !== 'undefined') {
            const resizeObserver = new ResizeObserver(function(entries) {
                for (let entry of entries) {
                    if (entry.target === img) {
                        updateOverlay();
                        break;
                    }
                }
            });
            resizeObserver.observe(img);
        }
        
        console.log('Border overlay created for:', magazineContainer);
    }
    
    // Procesar imágenes existentes
    processImages();
    
    // Reinicializar si se cargan nuevas imágenes dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                processImages();
            }
        });
    });
    
    // Observar cambios en el contenedor de imágenes magazine
    const magazineContainer = document.querySelector('.magazine-images');
    if (magazineContainer) {
        observer.observe(magazineContainer, {
            childList: true,
            subtree: true
        });
    }
    
    // También observar cambios en el swiper
    const swiperContainer = document.querySelector('.magazine-swiper');
    if (swiperContainer) {
        observer.observe(swiperContainer, {
            childList: true,
            subtree: true
        });
    }
}

// Ejecutar en la carga inicial del DOM
document.addEventListener('DOMContentLoaded', function() {
    initMagazineImages();
});