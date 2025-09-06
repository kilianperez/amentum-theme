/* 
 * Punto de entrada principal - Theme Amentum
 * 
 * Todas las librerías se concatenan automáticamente mediante vite.config.js:
 * 1. jQuery 3.7.1 
 * 2. Swiper 8.0.6 
 * 3. Lenis 1.1.20
 * 4. GSAP 3.13.0 + ScrollTrigger 
 * 5. Split-Type 0.3.4
 * 6. Barba.js 2.10.3
 * 7. jQuery Validation 1.21.0
 * 8. main.js (este archivo)
 * 9. general.js
 * 10. blocks script.js
 * 
 * Sin CDNs externos - todo unificado en all.js
 */

console.log('Amentum Theme - JavaScript cargado correctamente');

// Verificar que todas las librerías estén disponibles tras la concatenación
if (typeof window !== 'undefined') {
    setTimeout(function() {
        var libsStatus = {
            jQuery: !!(window.jQuery || window.$),
            gsap: !!window.gsap,
            ScrollTrigger: !!window.ScrollTrigger,
            SplitType: !!window.SplitType,
            barba: !!window.barba,
            Swiper: !!window.Swiper,
            Lenis: !!window.Lenis
        };
        
        console.log('Estado de librerías cargadas:', libsStatus);
        
        // Verificar si hay alguna librería faltante
        var missing = [];
        for (var name in libsStatus) {
            if (!libsStatus[name]) {
                missing.push(name);
            }
        }
        
        if (missing.length > 0) {
            console.warn('Librerías no detectadas:', missing);
        } else {
            console.log('Todas las librerías cargadas correctamente');
        }
    }, 100);
}