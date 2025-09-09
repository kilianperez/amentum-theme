/* 
 * Punto de entrada principal - Theme Amentum (Modularizado)
 * 
 * Este archivo ahora usa módulos organizados para una mejor estructura:
 * - modules/core.js - Inicialización principal
 * - modules/barba-transitions.js - Barba.js y transiciones
 * - modules/smooth-scroll.js - Lenis smooth scroll
 * - modules/animations.js - GSAP y efectos visuales
 * - modules/ui-components.js - Menu y UI
 * - modules/form-validation.js - Sistema de validación
 * - modules/utils.js - Funciones auxiliares
 * 
 * Los módulos se concatenan automáticamente mediante vite.config.js
 */

// Test de watch mode
console.log('🔥 TEST WATCH MODE - CAMBIO:', Date.now());

// La función initTheme() se define en modules/core.js y se ejecuta automáticamente
// debido a la concatenación. El DOMContentLoaded se maneja en el módulo core.
console.log('Amentum Theme - JavaScript principal cargado correctamente');