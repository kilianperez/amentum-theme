import { defineConfig } from 'vite';
import { resolve, dirname, join } from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import { glob } from 'glob';
import copy from 'rollup-plugin-copy';
import { createRequire } from 'module';

const require = createRequire(import.meta.url);
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Leer package.json para obtener la versión
const packageJson = JSON.parse(fs.readFileSync('./package.json', 'utf8'));

export default defineConfig(() => {
  
  // Plugin para generar SCSS unificado con limpieza automática (como Gulp)
  const unifiedScssPlugin = () => {
    const tempFile = resolve(__dirname, 'assets/sass/.temp-unified-style.scss'); // RESTAURAR ubicación original
    let isWatchMode = false;
    let lastScssCheck = 0;
    
    const generateUnifiedScss = (force = false) => {
      // En watch mode, verificar si necesitamos regenerar
      if (isWatchMode && !force) {
        // Verificar timestamps de archivos SCSS
        const mainScssPath = resolve(__dirname, 'assets/sass/style.scss');
        const mainScssTime = fs.existsSync(mainScssPath) ? fs.statSync(mainScssPath).mtime.getTime() : 0;
        
        const blocksScss = glob.globSync('blocks/**/style.scss', { cwd: __dirname });
        const blocksMaxTime = blocksScss.reduce((maxTime, file) => {
          const filePath = resolve(__dirname, file);
          const fileTime = fs.existsSync(filePath) ? fs.statSync(filePath).mtime.getTime() : 0;
          return Math.max(maxTime, fileTime);
        }, 0);
        
        const latestScssTime = Math.max(mainScssTime, blocksMaxTime);
        
        // Si no hay cambios en SCSS, no regenerar
        if (latestScssTime <= lastScssCheck) {
          console.log('⏭️ Sin cambios en archivos SCSS - reutilizando unificado');
          return false;
        }
        
        lastScssCheck = latestScssTime;
      }
      
      // Buscar todos los archivos style.scss en bloques (como hace Gulp)
      const blocksScss = glob.globSync('blocks/**/style.scss', { cwd: __dirname });
      console.log(`📁 Encontrados ${blocksScss.length} archivos SCSS de bloques:`, blocksScss);
      
      // Generar contenido SCSS unificado
      let styleUnifiedContent = `// Archivo unificado generado automáticamente (se limpia automáticamente)
// Replica la funcionalidad de Gulp que compila style.scss + blocks/**/style.scss
@import './style.scss';
`;
      
      if (blocksScss.length > 0) {
        styleUnifiedContent += '\n// Bloques SCSS importados automáticamente:\n';
        blocksScss.forEach(file => {
          styleUnifiedContent += `@import '../../${file}';\n`; // RESTAURAR rutas originales
        });
      }
      
      // Crear archivo temporal con punto inicial para ocultarlo
      fs.writeFileSync(tempFile, styleUnifiedContent);
      console.log(isWatchMode ? '🔄 SCSS unificado actualizado' : '✅ SCSS unificado generado (con limpieza automática)');
      return true;
    };
    
    return {
      name: 'unified-scss-auto-cleanup',
      buildStart() {
        isWatchMode = process.argv.includes('--watch');
        generateUnifiedScss(true); // Siempre generar en buildStart
      },
      buildEnd() {
        // Solo limpiar en build normal, no en watch mode
        if (!isWatchMode && fs.existsSync(tempFile)) {
          fs.unlinkSync(tempFile);
          console.log('🧹 Archivo temporal SCSS limpiado');
        }
      }
    };
  };

  // Plugin para generar versiones minificadas de CSS y source maps + PurgeCSS
  const generateMinifiedCSS = () => {
    return {
      name: 'generate-minified-css-with-purgecss',
      async generateBundle(options, bundle) {
        let purgecss = null;
        
        // Cargar PurgeCSS SIEMPRE (sin diferencia prod/dev)
        try {
          // Usar PurgeCSS directamente, no como plugin PostCSS
          const { PurgeCSS } = require('purgecss');
          purgecss = new PurgeCSS();
        } catch (error) {
          console.warn('⚠️ PurgeCSS no disponible, continuando sin optimización CSS');
        }
        
        // Buscar todos los archivos CSS generados
        for (const fileName of Object.keys(bundle)) {
          if (fileName.endsWith('.css') && !fileName.includes('.min.css')) {
            const cssAsset = bundle[fileName];
            if (cssAsset.type === 'asset') {
              let cssContent = cssAsset.source;
              const baseName = fileName.replace('css/', '').replace('.css', '');
              
              // Aplicar PurgeCSS SIEMPRE
              if (purgecss) {
                try {
                  const purgeResult = await purgecss.purge({
                    content: [
                      './**/*.php',
                      './blocks/**/*.php',
                      './assets/js/**/*.js', 
                      './blocks/**/script.js',
                    ],
                    css: [{ raw: cssContent }],
                    safelist: [
                      // WordPress core classes
                      /^wp-/, /^admin-/, /^post-/, /^page-/, /^attachment-/,
                      // Swiper classes
                      /^swiper/,
                      // Bootstrap classes
                      /^btn/, /^navbar/, /^modal/, /^carousel/,
                      // Classes dinámicas
                      /^js-/, /^is-/, /^has-/,
                      'active', 'show', 'fade', 'in', 'out'
                    ],
                    keyframes: true,
                    fontFace: true,
                  });
                  
                  if (purgeResult && purgeResult[0] && purgeResult[0].css) {
                    const originalSize = cssContent.length;
                    cssContent = purgeResult[0].css;
                    const newSize = cssContent.length;
                    const reduction = ((originalSize - newSize) / originalSize * 100).toFixed(1);
                    console.log(`🧹 PurgeCSS aplicado a ${fileName}: ${originalSize}B → ${newSize}B (${reduction}% reducción)`);
                  }
                } catch (error) {
                  console.warn(`⚠️ Error aplicando PurgeCSS a ${fileName}:`, error.message);
                }
              }
              
              // Agregar referencia al source map en el CSS normal
              const cssWithMap = cssContent + `\n/*# sourceMappingURL=${baseName}.css.map */`;
              
              // Actualizar el archivo CSS con la referencia al map
              bundle[fileName].source = cssWithMap;
              
              // Crear versión minificada (sin source map)
              const minFileName = fileName.replace('.css', '.min.css');
              this.emitFile({
                type: 'asset',
                fileName: minFileName,
                source: cssContent // Sin referencia al map
              });
              
              // Generar source map para el CSS normal
              const sourceMap = {
                version: 3,
                sources: [`../../assets/sass/${baseName}.scss`],
                names: [],
                mappings: 'AAAA',
                file: `${baseName}.css`,
                sourceRoot: ''
              };
              
              this.emitFile({
                type: 'asset',
                fileName: fileName + '.map',
                source: JSON.stringify(sourceMap, null, 2)
              });
              
              console.log(`✅ Generado: ${minFileName} y ${fileName}.map`);
            }
          }
        }
      }
    };
  };

  // Plugin personalizado para concatenar JavaScript igual que Gulp
  const concatenateJavaScript = () => {
    const { minify } = require('terser');
    
    return {
      name: 'concatenate-javascript',
      buildStart() {
        // En modo watch, agregar archivos JS al watchlist de Vite
        const isWatchMode = process.argv.includes('--watch');
        if (isWatchMode) {
          const sourceFiles = [
            resolve(__dirname, 'assets/js/main.js'),
            resolve(__dirname, 'assets/js/general.js'),
            resolve(__dirname, 'assets/js/debug-live.js'),
            ...glob.globSync('blocks/**/script.js', { cwd: __dirname }).map(file => 
              resolve(__dirname, file)
            )
          ];
          
          sourceFiles.forEach(file => {
            if (fs.existsSync(file)) {
              this.addWatchFile(file);
            }
          });
          
        }
      },
      async generateBundle(options, bundle) {
        const startTime = Date.now();
        
        // Definir archivos a concatenar en el ORDEN CORRECTO (replicando la versión CDN que funcionaba)
        const filesToConcat = [
          // 1. Base: jQuery
          'node_modules/jquery/dist/jquery.min.js',
          // 2. jQuery plugins que dependen de jQuery
          'node_modules/jquery-validation/dist/jquery.validate.min.js',
          // 3. GSAP base (debe ir antes que sus plugins y Split-Type)
          'node_modules/gsap/dist/gsap.min.js',
          // 4. GSAP plugins (después de GSAP base)
          'node_modules/gsap/dist/ScrollTrigger.min.js',
          // 5. Split-Type (necesita GSAP disponible)
          'node_modules/split-type/umd/index.min.js',
          // 6. Barba.js (necesita las bases anteriores)
          'node_modules/@barba/core/dist/barba.umd.js',
          // 7. Otras librerías
          'node_modules/swiper/swiper-bundle.min.js',
          'node_modules/lenis/dist/lenis.min.js'
        ];
        
        // Agregar archivos JS personalizados en ORDEN ESPECÍFICO
        // ORDEN CRÍTICO: main.js (verificaciones) → general.js (código principal) → otros
        const orderedCustomFiles = [
          'assets/js/main.js',     // 1. Verificaciones y logs
          'assets/js/general.js'   // 2. Código principal (Barba, animaciones, etc.)
        ];
        
        // Agregar otros archivos JS personalizados (si los hay, excluyendo los ya ordenados)
        const otherCustomFiles = glob.globSync('assets/js/**/*.js', { cwd: __dirname })
          .filter(file => !orderedCustomFiles.includes(file));
        
        // Agregar archivos de bloques
        const blocksJsFiles = glob.globSync('blocks/**/script.js', { cwd: __dirname });
        
        // Concatenar en orden correcto
        filesToConcat.push(...orderedCustomFiles, ...otherCustomFiles, ...blocksJsFiles);

        // Leer y concatenar todos los archivos
        let concatenatedContent = '';
        let sourceMapContent = '//# sourceMappingURL=all.js.map\n';
        let totalSize = 0;
        let filesProcessed = 0;
        
        filesToConcat.forEach((filePath) => {
          const fullPath = resolve(__dirname, filePath);
          if (fs.existsSync(fullPath)) {
            const fileContent = fs.readFileSync(fullPath, 'utf8');
            const fileSize = fileContent.length;
            totalSize += fileSize;
            filesProcessed++;
            
            concatenatedContent += `\n/* === ${filePath} === */\n`;
            concatenatedContent += fileContent;
            concatenatedContent += '\n';
          } else {
            console.warn(`⚠️ Archivo no encontrado: ${fullPath}`);
          }
        });

        // Agregar comentario de fuente
        const headerComment = `/* Concatenated JavaScript - Generated by Vite */
/* Replicates Gulp filesToAllJs functionality */
/* Files concatenated in order: ${filesToConcat.join(', ')} */

`;
        concatenatedContent = headerComment + concatenatedContent;

        // Eliminar todos los archivos JS generados por defecto por Vite
        Object.keys(bundle).forEach(fileName => {
          if (fileName.endsWith('.js') || fileName.endsWith('.js.map')) {
            delete bundle[fileName];
          }
        });
        
        // Generar archivos JS minificados

        // Generar source map para all.js
        const sourceMap = {
          version: 3,
          sources: filesToConcat,
          names: [],
          mappings: '',
          file: 'all.js'
        };
        
        this.emitFile({
          type: 'asset',
          fileName: 'js/all.js.map',
          source: JSON.stringify(sourceMap)
        });

        try {
          const originalSize = concatenatedContent.length;
          
          // all.js - minificado pero con source map para debugging
          const minifiedDev = await minify(concatenatedContent, {
            compress: {
              drop_console: false, // Mantener console para debugging
              drop_debugger: false
            },
            mangle: false, // No mangle para mejor debugging
            format: {
              comments: false
            }
          });
          const devSize = minifiedDev.code?.length || originalSize;

          // all.min.js - minificado completamente para producción
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

          // Archivo all.js (minificado con source map)
          this.emitFile({
            type: 'asset',
            fileName: 'js/all.js',
            source: (minifiedDev.code || concatenatedContent) + '\n//# sourceMappingURL=all.js.map'
          });

          // Archivo all.min.js (minificado para producción)
          this.emitFile({
            type: 'asset',
            fileName: 'js/all.min.js',
            source: minifiedProd.code || concatenatedContent
          });

          console.log(`✅ JS compilado: all.js (${(devSize/1024).toFixed(1)}KB) + all.min.js (${(prodSize/1024).toFixed(1)}KB)`);
        } catch (error) {
          console.error('❌ Error al minificar:', error);
          
          // Si falla la minificación, crear archivos sin minificar
          this.emitFile({
            type: 'asset',
            fileName: 'js/all.js',
            source: concatenatedContent + sourceMapContent
          });
          
          this.emitFile({
            type: 'asset',
            fileName: 'js/all.min.js',
            source: concatenatedContent
          });
        }
      }
    };
  };

  // Plugin inteligente para copia selectiva de assets
  const smartAssetCopy = () => {
    let isWatchMode = false;
    const copiedFiles = new Map(); // Cache de archivos copiados con sus timestamps
    
    const copyFileIfChanged = (srcPath, destPath) => {
      try {
        const srcStats = fs.statSync(srcPath);
        const destExists = fs.existsSync(destPath);
        
        // En build normal, siempre copiar
        if (!isWatchMode) {
          const destDir = dirname(destPath);
          if (!fs.existsSync(destDir)) {
            fs.mkdirSync(destDir, { recursive: true });
          }
          fs.copyFileSync(srcPath, destPath);
          return true;
        }
        
        // En watch mode, solo copiar si cambió
        const lastModified = copiedFiles.get(srcPath);
        const srcTime = srcStats.mtime.getTime();
        
        if (!destExists || !lastModified || srcTime > lastModified) {
          const destDir = dirname(destPath);
          if (!fs.existsSync(destDir)) {
            fs.mkdirSync(destDir, { recursive: true });
          }
          fs.copyFileSync(srcPath, destPath);
          copiedFiles.set(srcPath, srcTime);
          return true;
        }
        
        return false; // No se copió porque no cambió
      } catch (error) {
        console.warn(`⚠️ Error copiando ${srcPath}:`, error.message);
        return false;
      }
    };
    
    const processAssetDirectory = (srcPattern, destBase) => {
      const files = glob.globSync(srcPattern, { cwd: __dirname });
      let copiedCount = 0;
      
      files.forEach(file => {
        const srcPath = resolve(__dirname, file);
        const relativePath = file.replace(/^assets\/(fonts|img)\//, '');
        const destPath = resolve(__dirname, destBase, relativePath);
        
        if (copyFileIfChanged(srcPath, destPath)) {
          copiedCount++;
        }
      });
      
      
      return copiedCount;
    };
    
    return {
      name: 'smart-asset-copy',
      buildStart() {
        isWatchMode = process.argv.includes('--watch');
      },
      generateBundle() {
        // Copiar fuentes
        const fontsCopied = processAssetDirectory('assets/fonts/**/*', 'assets/dist/fonts');
        
        // Copiar imágenes  
        const imagesCopied = processAssetDirectory('assets/img/**/*', 'assets/dist/img');
        
        if (!isWatchMode && (fontsCopied > 0 || imagesCopied > 0)) {
          console.log(`✅ Assets: ${fontsCopied} fuentes, ${imagesCopied} imágenes`);
        }
      }
    };
  };

  // Configuración de entrada - SIN archivo temporal problemático
  const input = {
    // CSS principal (style.scss directo)
    style: resolve(__dirname, 'assets/sass/style.scss'),
    // CSS de admin
    admin: resolve(__dirname, 'assets/sass/admin.scss'),
    // JavaScript principal
    all: resolve(__dirname, 'assets/js/main.js'),
  };

  return {
    root: __dirname,
    base: './', // Usar rutas relativas para assets
    
    // LOGS DE DEBUG PARA EL WATCH
    logLevel: 'info',
    
    css: {
      devSourcemap: true,
      postcss: {
        plugins: [
          require('autoprefixer')(),
          // Plugin personalizado para corregir rutas de assets en WordPress
          (() => {
            const plugin = {
              postcssPlugin: 'fix-wordpress-asset-urls',
              Once(root) {
                root.walkDecls(decl => {
                  if (decl.value.includes('url(/assets/')) {
                    decl.value = decl.value.replace(/url\(\/assets\//g, 'url(assets/');
                  }
                });
              }
            };
            plugin.postcssPlugin = 'fix-wordpress-asset-urls';
            return plugin;
          })()
        ],
      },
      preprocessorOptions: {
        scss: {
          api: 'modern-compiler',
          includePaths: ['node_modules', 'assets/sass/base'], // RESTAURAR configuración original
          quietDeps: true,
          silenceDeprecations: ['import'], // Suprimir warnings de @import deprecados
        },
      },
    },

    plugins: [
      // Plugin personalizado para concatenar JavaScript
      concatenateJavaScript(),
      // Plugin para generar versiones minificadas de CSS
      generateMinifiedCSS(),
      // Plugin inteligente para copiar assets solo cuando cambien
      smartAssetCopy(),
      // Plugin para corregir rutas de assets después del build
      {
        name: 'fix-asset-urls',
        writeBundle() {
          const fs = require('fs');
          const path = require('path');
          
          // Corregir rutas en archivos CSS
          const cssFiles = [
            path.resolve(__dirname, 'assets/dist/css/style.css'),
            path.resolve(__dirname, 'assets/dist/css/style.min.css')
          ];
          
          cssFiles.forEach(file => {
            if (fs.existsSync(file)) {
              let content = fs.readFileSync(file, 'utf8');
              content = content.replace(/url\(\/assets\//g, 'url(assets/');
              fs.writeFileSync(file, content);
              console.log(`✅ Rutas de assets corregidas en ${path.basename(file)}`);
            }
          });
        }
      },
    ],

    build: {
      outDir: 'assets/dist',
      emptyOutDir: false, // No limpiar todo el directorio
      assetsDir: 'assets', // Carpeta para assets estáticos
      
      // CORREGIR rutas de assets para WordPress
      assetsInlineLimit: 0, // No inline assets
      cssCodeSplit: false, // No split CSS
      
      
      rollupOptions: {
        input,
        
        output: {
          // Configuración para CSS
          assetFileNames: (assetInfo) => {
            if (assetInfo.name.endsWith('.css')) {
              if (assetInfo.name.includes('admin')) {
                return `css/admin.css`;
              } else if (assetInfo.name.includes('style') || assetInfo.name.includes('unified-style')) {
                return `css/style.css`;
              }
              return `css/[name].css`;
            }
            return 'assets/[name]-[hash][extname]';
          },
          
          // Configuración para JS
          entryFileNames: (chunkInfo) => {
            if (chunkInfo.name === 'all') {
              return `js/all.js`;
            }
            if (chunkInfo.name.startsWith('partials-')) {
              const partialName = chunkInfo.name.replace('partials-', '');
              return `js/${partialName}.js`;
            }
            return `js/[name].js`;
          },
          
          // Configuración para chunks
          chunkFileNames: `js/[name]-[hash].js`,
        },

        external: [],
        
        plugins: [
          require('@rollup/plugin-terser')(),
        ],
      },
      
      minify: 'terser',
      sourcemap: true,
      
      // Configuración de terser
      terserOptions: {
        compress: {
          drop_console: true,
          drop_debugger: true,
        },
      },
    },
  };
});