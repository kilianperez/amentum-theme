/* 
 * Punto de entrada principal para Vite
 * 
 * NOTA: Este archivo es solo un punto de entrada dummy para Vite.
 * La concatenación real de JavaScript se hace mediante el plugin 
 * concatenateJavaScript() en vite.config.js que replica exactamente
 * la funcionalidad de filesToAllJs de gulpfile.js:
 * 
 * 1. node_modules/jquery/dist/jquery.min.js
 * 2. node_modules/swiper/swiper-bundle.min.js  
 * 3. node_modules/lenis/dist/lenis.min.js
 * 4. assets/js (todos los archivos .js incluyendo este archivo)
 * 5. blocks (todos los script.js de bloques)
 */

// Este archivo se incluirá automáticamente en la concatenación
console.log('Amentum Theme - JavaScript entry point (main.js)');