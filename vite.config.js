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
    const tempFile = resolve(__dirname, 'assets/sass/.temp-unified-style.scss');
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
          styleUnifiedContent += `@import '../../${file}';\n`;
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
        // Limpiar archivo temporal después de la compilación
        if (fs.existsSync(tempFile)) {
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
      async generateBundle(options, bundle) {
        console.log('🔄 Iniciando concatenación de JavaScript...');
        
        // Definir archivos a concatenar (replicando filesToAllJs de gulpfile.js)
        const filesToConcat = [
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/swiper/swiper-bundle.min.js', 
          'node_modules/lenis/dist/lenis.min.js',
          'node_modules/gsap/dist/gsap.min.js',
          'node_modules/gsap/dist/ScrollTrigger.min.js',
          'node_modules/split-type/umd/index.min.js',
          'node_modules/@barba/core/dist/barba.umd.js'
        ];
        
        // Agregar archivos JS personalizados del theme (assets/js/**/*.js)
        const customJsFiles = glob.globSync('assets/js/**/*.js', { cwd: __dirname });
        const blocksJsFiles = glob.globSync('blocks/**/script.js', { cwd: __dirname });
        
        filesToConcat.push(...customJsFiles, ...blocksJsFiles);

        console.log('📂 Archivos a concatenar:', filesToConcat);

        // Leer y concatenar todos los archivos
        let concatenatedContent = '';
        let sourceMapContent = '//# sourceMappingURL=all.js.map\n';
        
        filesToConcat.forEach((filePath) => {
          const fullPath = resolve(__dirname, filePath);
          if (fs.existsSync(fullPath)) {
            const fileContent = fs.readFileSync(fullPath, 'utf8');
            concatenatedContent += `\n/* === ${filePath} === */\n`;
            concatenatedContent += fileContent;
            concatenatedContent += '\n';
            console.log(`✅ Concatenado: ${filePath}`);
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
        
        // Archivo all.js (sin minificar - con source map)
        this.emitFile({
          type: 'asset',
          fileName: 'js/all.js',
          source: concatenatedContent + sourceMapContent
        });

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

        // Minificar contenido para all.min.js (sin source map)
        try {
          const minified = await minify(concatenatedContent, {
            compress: {
              drop_console: true,
              drop_debugger: true
            },
            mangle: true,
            format: {
              comments: false
            }
          });

          // Archivo all.min.js (minificado - sin source map)
          this.emitFile({
            type: 'asset',
            fileName: 'js/all.min.js',
            source: minified.code || concatenatedContent
          });
          
          console.log('✅ all.min.js minificado correctamente');
        } catch (error) {
          console.error('❌ Error al minificar:', error);
          // Si falla la minificación, usar el contenido sin minificar
          this.emitFile({
            type: 'asset',
            fileName: 'js/all.min.js',
            source: concatenatedContent
          });
        }

        console.log(`✅ Archivos all.js (con .map) y all.min.js (sin .map) generados`);
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
          console.log(`📋 Copiado: ${srcPath} → ${destPath}`);
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
          console.log(`🔄 Actualizado: ${srcPath} → ${destPath}`);
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
      
      if (isWatchMode && copiedCount > 0) {
        console.log(`✅ ${copiedCount} archivos ${srcPattern.includes('fonts') ? 'de fuentes' : 'de imágenes'} actualizados`);
      } else if (isWatchMode) {
        console.log(`⏭️ Sin cambios en ${srcPattern.includes('fonts') ? 'fuentes' : 'imágenes'}`);
      }
      
      return copiedCount;
    };
    
    return {
      name: 'smart-asset-copy',
      buildStart() {
        isWatchMode = process.argv.includes('--watch');
        if (isWatchMode) {
          console.log('👀 Modo watch: copia inteligente de assets activada');
        }
      },
      generateBundle() {
        console.log('📁 Procesando assets estáticos...');
        
        // Copiar fuentes
        const fontsCopied = processAssetDirectory('assets/fonts/**/*', 'assets/dist/fonts');
        
        // Copiar imágenes  
        const imagesCopied = processAssetDirectory('assets/img/**/*', 'assets/dist/img');
        
        if (!isWatchMode) {
          console.log(`✅ Assets copiados: ${fontsCopied} fuentes, ${imagesCopied} imágenes`);
        }
      }
    };
  };

  // Configuración de entrada
  const input = {
    // CSS principal unificado (style.scss + bloques automáticamente - IGUAL que Gulp)
    style: resolve(__dirname, 'assets/sass/.temp-unified-style.scss'),
    // CSS de admin
    admin: resolve(__dirname, 'assets/sass/admin.scss'),
    // JavaScript principal - crearemos un archivo dummy que será reemplazado por el plugin
    all: resolve(__dirname, 'assets/js/main.js'),
  };

  return {
    root: __dirname,
    
    css: {
      devSourcemap: true,
      postcss: {
        plugins: [
          require('autoprefixer')(),
          // TODO: PurgeCSS está instalado pero comentado por problemas de compatibilidad con Vite
          // Se puede habilitar más adelante o usar un plugin específico de Vite
        ],
      },
      preprocessorOptions: {
        scss: {
          api: 'modern-compiler',
          includePaths: ['node_modules', 'assets/sass/base'],
          quietDeps: true,
        },
      },
    },

    plugins: [
      // Plugin para generar SCSS unificado en memoria
      unifiedScssPlugin(),
      // Plugin personalizado para concatenar JavaScript
      concatenateJavaScript(),
      // Plugin para generar versiones minificadas de CSS
      generateMinifiedCSS(),
      // Plugin inteligente para copiar assets solo cuando cambien
      smartAssetCopy(),
    ],

    build: {
      outDir: 'assets/dist',
      emptyOutDir: false, // No limpiar todo el directorio
      
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