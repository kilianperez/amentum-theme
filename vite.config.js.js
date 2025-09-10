import { defineConfig } from 'vite';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import { glob } from 'glob';
import legacy from '@vitejs/plugin-legacy';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig(() => {

  // Plugin personalizado para concatenar JavaScript
  const concatenateJavaScript = () => {
    const { minify } = require('terser');
    let isWatchMode = false;
    let buildCount = 0;
    
    // Colores ANSI
    const colors = {
      reset: '\x1b[0m',
      bright: '\x1b[1m',
      cyan: '\x1b[36m',
      green: '\x1b[32m',
      yellow: '\x1b[33m',
      gray: '\x1b[90m',
      white: '\x1b[37m'
    };
    
    return {
      name: 'concatenate-javascript',
      // Mostrar cambios detectados de forma simple
      watchChange(id) {
        if (id.endsWith('.js') && (
          id.includes('/assets/js/') || 
          id.includes('/blocks/')
        )) {
          const fileName = id.split('/').slice(-2).join('/');
          const time = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          console.log(`\n${colors.gray}[${time}]${colors.reset} ${colors.yellow}JS:${colors.reset} ${colors.cyan}${fileName}${colors.reset}`);
        }
      },
      buildStart() {
        isWatchMode = process.argv.includes('--watch');
        buildCount++;
        
        if (isWatchMode) {
          const sourceFiles = [
            resolve(__dirname, 'assets/js/main.js'),
            resolve(__dirname, 'assets/js/general.js'),
            resolve(__dirname, 'assets/js/debug-live.js'),
            // Agregar módulos al watch
            ...glob.globSync('assets/js/modules/**/*.js', { cwd: __dirname }).map(file => 
              resolve(__dirname, file)
            ),
            // Agregar partials al watch
            ...glob.globSync('assets/js/partials/**/*.js', { cwd: __dirname }).map(file => 
              resolve(__dirname, file)
            ),
            // Agregar blocks al watch
            ...glob.globSync('blocks/**/script.js', { cwd: __dirname }).map(file => 
              resolve(__dirname, file)
            )
          ];
          
          sourceFiles.forEach(file => {
            if (fs.existsSync(file)) {
              this.addWatchFile(file);
            }
          });
          
          if (buildCount === 1) {
            const jsFiles = sourceFiles.length;
            
            console.log('\n' + colors.yellow + '━'.repeat(60) + colors.reset);
            console.log(colors.yellow + 'SOLO JS WATCH MODE' + colors.reset + colors.gray + ' - Theme Amentum' + colors.reset);
            console.log(colors.yellow + '━'.repeat(60) + colors.reset);
            console.log(colors.gray + 'Monitoreando ' + colors.cyan + jsFiles + colors.gray + ' archivos JS' + colors.reset);
            console.log(colors.gray + 'Esperando cambios JS...' + colors.reset + '\n');
          }
        } else {
          console.log('\n' + colors.yellow + 'Compilando JavaScript...' + colors.reset);
        }
      },
      async generateBundle(options, bundle) {
        const startTime = Date.now();
        
        // Definir archivos a concatenar en el ORDEN CORRECTO
        const filesToConcat = [
          // 1. Base: jQuery
          'node_modules/jquery/dist/jquery.min.js',
          // 2. jQuery plugins
          'node_modules/jquery-validation/dist/jquery.validate.min.js',
          // 3. GSAP base
          'node_modules/gsap/dist/gsap.min.js',
          // 4. GSAP plugins
          'node_modules/gsap/dist/ScrollTrigger.min.js',
          // 5. Split-Type
          'node_modules/split-type/umd/index.min.js',
          // 6. Barba.js
          'node_modules/@barba/core/dist/barba.umd.js',
          // 7. Otras librerías
          'node_modules/swiper/swiper-bundle.min.js',
          'node_modules/lenis/dist/lenis.min.js'
        ];
        
        // Agregar archivos JS personalizados en ORDEN ESPECÍFICO
        const orderedCustomFiles = [
          'assets/js/main.js',
          'assets/js/general.js'
        ];
        
        // Agregar módulos ES6
        const moduleFiles = glob.globSync('assets/js/modules/**/*.js', { cwd: __dirname });
        
        // Agregar otros archivos JS personalizados
        const otherCustomFiles = glob.globSync('assets/js/**/*.js', { cwd: __dirname })
          .filter(file => !orderedCustomFiles.includes(file) && !file.includes('/modules/'));
        
        // Agregar archivos de bloques
        const blocksJsFiles = glob.globSync('blocks/**/script.js', { cwd: __dirname });
        
        // Concatenar en orden correcto
        filesToConcat.push(...orderedCustomFiles, ...moduleFiles, ...otherCustomFiles, ...blocksJsFiles);

        // Leer y concatenar todos los archivos
        let concatenatedContent = '';
        let totalSize = 0;
        let filesProcessed = 0;
        
        filesToConcat.forEach((filePath) => {
          const fullPath = resolve(__dirname, filePath);
          if (fs.existsSync(fullPath)) {
            let fileContent = fs.readFileSync(fullPath, 'utf8');
            
            // Transpilar sintaxis ES6
            if (filePath.includes('/modules/') || fileContent.includes('export ') || fileContent.includes('import ')) {
              fileContent = fileContent.replace(/export\s+function\s+(\w+)/g, 'window.$1 = function');
              fileContent = fileContent.replace(/export\s+{([^}]+)}/g, (match, exports) => {
                const exportsList = exports.split(',').map(exp => exp.trim());
                return exportsList.map(exp => `window.${exp} = ${exp};`).join('\n');
              });
              
              fileContent = fileContent.replace(/import\s+.*?from\s+['"][^'"]+['"];?\s*/g, '');
              fileContent = fileContent.replace(/import\s+['"][^'"]+['"];?\s*/g, '');
              fileContent = fileContent.replace(/export\s+default\s+/g, '');
            }
            
            const fileSize = fileContent.length;
            totalSize += fileSize;
            filesProcessed++;
            
            concatenatedContent += `\n/* === ${filePath} === */\n`;
            concatenatedContent += fileContent;
            concatenatedContent += '\n';
          }
        });

        // Agregar comentario de fuente
        const headerComment = `/* Concatenated JavaScript - Generated by Vite */
/* Solo JS mode - No CSS compilation */
/* Files: ${filesToConcat.join(', ')} */

`;
        concatenatedContent = headerComment + concatenatedContent;

        // Eliminar archivos JS generados por defecto por Vite
        Object.keys(bundle).forEach(fileName => {
          if (fileName.endsWith('.js') || fileName.endsWith('.js.map')) {
            delete bundle[fileName];
          }
        });

        try {
          const originalSize = concatenatedContent.length;
          
          // all.js - minificado con source map
          const minifiedDev = await minify(concatenatedContent, {
            compress: {
              drop_console: false,
              drop_debugger: false
            },
            mangle: false,
            format: {
              comments: false
            }
          });
          const devSize = minifiedDev.code?.length || originalSize;

          // all.min.js - minificado para producción
          const minifiedProd = await minify(concatenatedContent, {
            compress: {
              drop_console: true,
              drop_debugger: true
            },
            mangle: true,
            format: {
              comments: false
            }
          });
          const prodSize = minifiedProd.code?.length || originalSize;

          this.emitFile({
            type: 'asset',
            fileName: 'js/all.js',
            source: (minifiedDev.code || concatenatedContent)
          });

          this.emitFile({
            type: 'asset',
            fileName: 'js/all.min.js',
            source: minifiedProd.code || concatenatedContent
          });

          // Estadísticas
          const endTime = Date.now();
          const buildTime = endTime - startTime;
          const reduction = ((originalSize - prodSize) / originalSize * 100).toFixed(1);
          
          if (isWatchMode) {
            console.log(colors.green + 'Compilación JS completada' + colors.reset + colors.gray + ' en ' + buildTime + 'ms' + colors.reset);
            console.log(`   ${colors.cyan}${filesProcessed} archivos${colors.reset} → ${colors.white}all.js${colors.reset} ${colors.gray}(${(devSize/1024).toFixed(1)}KB)${colors.reset} / ${colors.white}all.min.js${colors.reset} ${colors.gray}(${(prodSize/1024).toFixed(1)}KB)${colors.reset}`);
            console.log(`   ${colors.yellow}Reducción: ${reduction}%${colors.reset} ${colors.gray}| Ahorro: ${((originalSize - prodSize)/1024).toFixed(1)}KB${colors.reset}`);
          } else {
            console.log('\n' + colors.green + '━'.repeat(60) + colors.reset);
            console.log(colors.bright + colors.green + 'BUILD JS COMPLETADO' + colors.reset);
            console.log(colors.green + '━'.repeat(60) + colors.reset);
            console.log(`   ${colors.cyan}Archivos procesados:${colors.reset} ${colors.white}${filesProcessed}${colors.reset}`);
            console.log(`   ${colors.cyan}Tamaño original:${colors.reset} ${colors.white}${(originalSize/1024).toFixed(1)}KB${colors.reset}`);
            console.log(`   ${colors.cyan}all.min.js:${colors.reset} ${colors.white}${(prodSize/1024).toFixed(1)}KB${colors.reset}`);
            console.log(`   ${colors.cyan}Reducción:${colors.reset} ${colors.yellow}${reduction}%${colors.reset}`);
            console.log(colors.green + '━'.repeat(60) + colors.reset + '\n');
          }
        } catch (error) {
          console.error('❌ Error al minificar:', error);
        }
      }
    };
  };

  return {
    root: __dirname,
    base: './',
    
    logLevel: 'silent',

    plugins: [
      // Plugin legacy para transpilación ES6 → ES5 compatible
      legacy({
        targets: ['defaults', 'not IE 11'],
        modernTargets: ['defaults', '> 1%'],
        additionalLegacyPolyfills: ['regenerator-runtime/runtime'],
        renderLegacyChunks: false,
        modernPolyfills: true
      }),
      concatenateJavaScript(),
    ],

    build: {
      outDir: 'assets/dist',
      emptyOutDir: false,
      assetsDir: 'assets',
      reportCompressedSize: false,
      
      target: 'es2015',
      
      // SOLO JAVASCRIPT
      rollupOptions: {
        input: {
          all: resolve(__dirname, 'assets/js/main.js'),
        },
        
        output: {
          entryFileNames: () => `js/all.js`,
          chunkFileNames: `js/[name]-[hash].js`,
        },

        external: [],
        
        plugins: [
          require('@rollup/plugin-terser')(),
        ],
      },
      
      minify: 'terser',
      sourcemap: true,
      
      terserOptions: {
        compress: {
          drop_console: true,
          drop_debugger: true,
        },
      },
    },
  };
});