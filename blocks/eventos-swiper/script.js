/**
 * Eventos Swiper - Inicialización de swipers fullscreen
 * Maneja todos los swipers de eventos en la página
 */

// Array para almacenar instancias de Swiper y evitar duplicados
let eventosSwipersInstances = [];

// Función para limpiar swipers existentes
function limpiarEventosSwiper() {
    eventosSwipersInstances.forEach(swiperInstance => {
        if (swiperInstance && typeof swiperInstance.destroy === 'function') {
            swiperInstance.destroy(true, true);
        }
    });
    eventosSwipersInstances = [];
}

// Función para inicializar swipers
function inicializarEventosSwiper() {
    // Limpiar swipers existentes antes de crear nuevos
    limpiarEventosSwiper();
    
    // Buscar todos los swipers de eventos en la página
    const eventosSwippers = document.querySelectorAll('.block-eventos-swiper .swiper');
    
    if (eventosSwippers.length === 0) {
        return; // No hay swipers de eventos, salir
    }

    // Verificar que Swiper esté disponible
    if (typeof Swiper === 'undefined') {
        // Reintentar después de un pequeño delay
        setTimeout(inicializarEventosSwiper, 100);
        return;
    }

    console.log('✅ Swiper disponible, inicializando', eventosSwippers.length, 'swipers de eventos');

    // Inicializar cada swiper de eventos
    eventosSwippers.forEach(function(swiperElement) {
        const swiperContainer = swiperElement;
        const slidesCount = swiperContainer.querySelectorAll('.swiper-slide').length;
        
        // Solo inicializar si hay slides
        if (slidesCount === 0) {
            console.warn('Swiper sin slides encontrado, saltando inicialización.');
            return;
        }

        // Configuración del swiper
        const swiperConfig = {
            // Configuración básica
            slidesPerView: 1,
            spaceBetween: 0,
            loop: slidesCount > 1, // Solo loop si hay más de 1 slide
            speed: 1200,
            
            // Autoplay
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            
            // Paginación
            pagination: {
                el: swiperContainer.querySelector('.swiper-pagination'),
                clickable: true,
                type: 'bullets'
            },
            
            // Navegación
            navigation: {
                nextEl: swiperContainer.querySelector('.swiper-button-next'),
                prevEl: swiperContainer.querySelector('.swiper-button-prev')
            },
            
            // Efecto fade
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            
            // Eventos personalizados
            on: {
                slideChangeTransitionStart: function () {
                    // Reiniciar animaciones en el slide activo
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        const slideInner = activeSlide.querySelector('.slide-inner');
                        if (slideInner) {
                            slideInner.style.animation = 'none';
                            slideInner.offsetHeight; // Force reflow
                            slideInner.style.animation = 'slideInUp 1s ease-out';
                        }
                    }
                },
                init: function() {
                    // Animar el primer slide al inicializar
                    const firstSlide = this.slides[this.activeIndex];
                    if (firstSlide) {
                        const slideInner = firstSlide.querySelector('.slide-inner');
                        if (slideInner) {
                            slideInner.style.animation = 'slideInUp 1s ease-out';
                        }
                    }
                }
            }
        };

        // Inicializar el swiper
        try {
            const swiper = new Swiper(swiperContainer, swiperConfig);
            
            // Almacenar la instancia para poder destruirla después
            eventosSwipersInstances.push(swiper);
            
            // Debug info en desarrollo
            if (typeof WP_DEBUG !== 'undefined' && WP_DEBUG) {
                console.log('Swiper eventos inicializado:', {
                    container: swiperContainer,
                    slides: slidesCount,
                    loop: slidesCount > 1
                });
            }
        } catch (error) {
            console.error('Error inicializando swiper de eventos:', error);
        }
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', inicializarEventosSwiper);

// También inicializar cuando window esté completamente cargado (por si acaso)
window.addEventListener('load', inicializarEventosSwiper);
