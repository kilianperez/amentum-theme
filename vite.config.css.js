import { defineConfig } from 'vite';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import { glob } from 'glob';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig(() => {
  
  // Plugin para generar SCSS unificado con limpieza automática (como Gulp)
  const unifiedScssPlugin = () => {
    const tempFile = resolve(__dirname, 'assets/sass/.temp-unified-style.scss');
    let isWatchMode = false;
    let lastScssCheck = 0;
    
    // Colores ANSI
    const colors = {
      reset: '\x1b[0m',
      cyan: '\x1b[36m',
      green: '\x1b[32m',
      yellow: '\x1b[33m',
      magenta: '\x1b[35m',
      gray: '\x1b[90m'
    };
    
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
          return false;
        }
        
        lastScssCheck = latestScssTime;
      }
      
      // Buscar todos los archivos style.scss en bloques (como hace Gulp)
      const blocksScss = glob.globSync('blocks/**/style.scss', { cwd: __dirname });
      
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
      return true;
    };
    
    return {
      name: 'unified-scss-auto-cleanup',
      buildStart() {
        isWatchMode = process.argv.includes('--watch');
        
        // En modo watch, agregar archivos SCSS al watchlist
        if (isWatchMode) {
          const scssFiles = [
            resolve(__dirname, 'assets/sass/style.scss'),
            resolve(__dirname, 'assets/sass/admin.scss'),
            ...glob.globSync('assets/sass/**/*.scss', { cwd: __dirname }).map(file => 
              resolve(__dirname, file)
            ),
            ...glob.globSync('blocks/**/style.scss', { cwd: __dirname }).map(file => 
              resolve(__dirname, file)
            )
          ];
          
          scssFiles.forEach(file => {
            if (fs.existsSync(file)) {
              this.addWatchFile(file);
            }
          });
          
          console.log('\n' + colors.magenta + '━'.repeat(60) + colors.reset);
          console.log(colors.magenta + 'SOLO CSS WATCH MODE' + colors.reset + colors.gray + ' - Theme Amentum' + colors.reset);
          console.log(colors.magenta + '━'.repeat(60) + colors.reset);
          console.log(colors.gray + 'Monitoreando ' + colors.cyan + scssFiles.length + colors.gray + ' archivos SCSS' + colors.reset);
          console.log(colors.gray + 'Esperando cambios CSS...' + colors.reset + '\n');
        }
        
        generateUnifiedScss(true); // Siempre generar en buildStart
      },
      buildEnd() {
        // Limpiar el archivo temporal al finalizar (build o al terminar watch)
        if (fs.existsSync(tempFile)) {
          fs.unlinkSync(tempFile);
          if (isWatchMode) {
            console.log(`${colors.gray}Archivo temporal limpiado: .temp-unified-style.scss${colors.reset}`);
          }
        }
      },
      watchChange(id) {
        // Regenerar archivo temporal cuando cambian archivos SCSS relevantes
        if ((id.includes('assets/sass/') || id.includes('blocks/')) && id.endsWith('.scss')) {
          const regenerated = generateUnifiedScss();
          if (regenerated) {
            console.log(`${colors.gray}Archivo temporal regenerado por cambio en: ${id.split('/').slice(-2).join('/')}${colors.reset}`);
          }
        }
      }
    };
  };

  // Plugin para detectar cambios CSS y mostrar mensajes
  const cssChangeDetector = () => {
    let isWatchMode = false;
    
    const colors = {
      reset: '\x1b[0m',
      cyan: '\x1b[36m',
      green: '\x1b[32m',
      magenta: '\x1b[35m',
      gray: '\x1b[90m',
      white: '\x1b[37m'
    };
    
    return {
      name: 'css-change-detector',
      watchChange(id) {
        if (id.endsWith('.scss') && (
          id.includes('/assets/sass/') || 
          id.includes('/blocks/')
        )) {
          const fileName = id.split('/').slice(-2).join('/');
          const time = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          console.log(`\n${colors.gray}[${time}]${colors.reset} ${colors.magenta}CSS:${colors.reset} ${colors.cyan}${fileName}${colors.reset}`);
        }
      },
      buildStart() {
        isWatchMode = process.argv.includes('--watch');
      },
      generateBundle() {
        if (isWatchMode) {
          const startTime = Date.now();
          setTimeout(() => {
            const endTime = Date.now();
            const buildTime = endTime - startTime;
            console.log(`${colors.green}Compilación CSS completada${colors.reset}${colors.gray} en ${buildTime}ms${colors.reset}`);
            console.log(`   ${colors.cyan}CSS generado:${colors.reset} ${colors.white}style.css${colors.reset} ${colors.gray}(minificado + source map) + ${colors.white}style.min.css${colors.reset} ${colors.gray}(minificado)${colors.reset}`);
          }, 100);
        }
      }
    };
  };

  // Plugin para generar versiones minificadas de CSS
  const generateMinifiedCSS = () => {
    return {
      name: 'generate-minified-css-with-purgecss',
      async generateBundle(options, bundle) {
        let purgecss = null;
        
        // Cargar PurgeCSS SIEMPRE
        try {
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
              
              // Aplicar PurgeCSS
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
                    cssContent = purgeResult[0].css;
                  }
                } catch (error) {
                  console.warn(`⚠️ Error aplicando PurgeCSS a ${fileName}:`, error.message);
                }
              }
              
              // Crear versión minificada
              const minFileName = fileName.replace('.css', '.min.css');
              this.emitFile({
                type: 'asset',
                fileName: minFileName,
                source: cssContent
              });
            }
          }
        }
      }
    };
  };

  return {
    root: __dirname,
    base: './',
    
    logLevel: 'silent',
    
    css: {
      devSourcemap: true,
      postcss: {
        plugins: [
          require('autoprefixer')(),
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
          includePaths: ['node_modules', 'assets/sass/base'],
          quietDeps: true,
          silenceDeprecations: ['import'],
        },
      },
    },

    plugins: [
      unifiedScssPlugin(),
      cssChangeDetector(),
      generateMinifiedCSS(),
    ],

    build: {
      outDir: 'assets/dist',
      emptyOutDir: false,
      assetsDir: 'assets',
      reportCompressedSize: false,
      
      // SOLO CSS
      rollupOptions: {
        input: {
          style: resolve(__dirname, 'assets/sass/.temp-unified-style.scss'),
          admin: resolve(__dirname, 'assets/sass/admin.scss'),
        },
        
        output: {
          assetFileNames: (assetInfo) => {
            if (assetInfo.name.endsWith('.css')) {
              if (assetInfo.name.includes('admin')) {
                return `css/admin.css`;
              } else if (assetInfo.name.includes('style')) {
                return `css/style.css`;
              }
              return `css/[name].css`;
            }
            return 'assets/[name]-[hash][extname]';
          },
        },
      },
      
      minify: 'terser',
      sourcemap: true,
    },
  };
});