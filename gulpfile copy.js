// Este archivo define y configura las tareas de Gulp para automatizar el flujo de trabajo
// del proyecto, incluyendo la compilación de SCSS a CSS, la concatenación y minificación
// de JavaScript, optimización de imágenes, y más.

// Imports de módulos de terceros
import concat from "gulp-concat";
import cleanCss from "gulp-clean-css"; // Reemplazo para gulp-cssmin
import esbuild from "./node_modules/gulp-esbuild"; // Reemplazo para gulp-uglify
import gulpCopy from "gulp-copy";
import gulpIf from "gulp-if";
import gulpSass from "gulp-sass"; // Importación corregida
import imagemin from "gulp-imagemin";
import imageminGifsicle from "imagemin-gifsicle";
import imageminMozjpeg from "imagemin-mozjpeg";
import imageminPngquant from "imagemin-pngquant";
import imageminSvgo from "imagemin-svgo";
import imageminWebp from "imagemin-webp";
import log from "fancy-log"; // Agregar fancy-log para depuración
import notifier from "node-notifier"; // Reemplazo para gulp-notify
import ora from "ora"; // Importa ora para el spinner
import prettyBytes from "pretty-bytes";
import plumber from "gulp-plumber";
import purgecss from "gulp-purgecss";
import rename from "gulp-rename";
import replace from "gulp-replace";
import sourcemaps from "gulp-sourcemaps";
import terser from "gulp-terser";
import through2 from "through2";
import { fork } from 'child_process';

import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';

// Imports adicionales
import fs from "fs"; // Para leer el package.json
// Imports de módulos de Node.js
import util from "util"; // Importar el módulo util de Node.js
// Imports del motor de Sass
import * as dartSass from "sass"; // Motor de Dart Sass
// Importación principal de gulp (generalmente al final)
import pkg from "gulp";

const { src, dest, parallel, series, watch } = pkg;
const sass = gulpSass( dartSass ); // Usar dartSass con gulp-sass
const isProd = process.env.NODE_ENV === "production"; // Variable de configuración para definir entorno
const showLogs = process.env.SHOW_LOGS === "true"; // Variable de configuración para mostrar logs
const purgar = false;

// Extraer la versión desde package.json
const packageJson = JSON.parse(fs.readFileSync('./package.json'));
const themeVersion = packageJson.version;

// Definimos los paths de los assets.
const paths = {
    scripts: {
        src: "assets/js",
        dest: "assets/dist/js",
    },
    styles: {
        sass: "assets/sass",
        dest: "assets/dist/css",
    },
    images: {
        src: "assets/img/**/*.{jpg,jpeg,png,gif,svg,webp}", // Filtrar solo los formatos que deseamos procesar
        dest: "assets/dist/img",
    },
    fonts: {
        src: "assets/fonts/**/*.{woff,woff2,ttf,eot}",
        dest: "assets/dist",
    },
    vendors: {
        dest: "assets/dist/vendors",
    },
};

/**--------------------------------------------------------------------------------------------------------------
 * CONFIGURACIÓN
 *
 * estas variables son las que definen qué hacer con cada
 * archivo JS, según el tratamiento que se necesite dar
 *
 --------------------------------------------------------------------------------------------------------------*/

/**
 * Archivos JS que se deben combinar.
 * Las 2 primeras líneas son necesarias para incluir todos los JS necesarios para Bootstrap5.
 * La 3 línea define el patrón de la carpeta JS, todos sus archivos serán combinados también.
 *
 * Cualquier otro archivo se debe incluir en esta lista, por debajo de la 3 línea
 */
const filesToAllJs = [
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    'node_modules/swiper/swiper-bundle.min.js',
    'node_modules/bs5-lightbox/dist/index.bundle.min.js',
    'node_modules/hc-sticky/dist/hc-sticky.js',
    'node_modules/jquery.marquee/jquery.marquee.js',
    paths.scripts.src + "/**/*.js",
];

/**
 * Archivos copiados individual y literalmente de vendors o módulos de Node.
 *
 */
const filesToVendors = [
    // 'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
];

const filesToVendorsJs = [
    // 'node_modules/swiper/swiper-bundle.min.js.map',
    'node_modules/swiper/swiper-bundle.min.js.map',
];

/**--------------------------------------------------------------------------------------------------------------
 *  FIN CONFIGURACIÓN
 --------------------------------------------------------------------------------------------------------------*/

/**
 * Manejo de errores para notificaciones en tiempo de ejecución.
 *
 * @param {string} taskName - El nombre de la tarea para identificar el error en la notificación.
 * @return {Function} - Retorna una función de manejo de errores para usar con gulp-plumber.
 */
function errorHandler( taskName ) {
    return plumber( {
        errorHandler( err ) {
            // Notifica al sistema operativo sobre el error
            notifier.notify( {
                title: `Error en ${ taskName }`,
                message: err.message,
            } );

            // Muestra el error en la consola con detalles adicionales
            log.error(
                `Error en ${ taskName }:`,
                util.inspect( err, { depth: null, colors: true } ),
            );

            this.emit( "end" );
        },
    } );
}

/**
 * Maneja el estado del spinner y los errores con mensajes personalizados opcionales.
 *
 * @param {object} spinner - El spinner de ora.
 * @param {string} taskName - Nombre de la tarea para los mensajes de error.
 * @param {null} [successMessage] - Mensaje de éxito opcional.
 * @param {null} [errorMessage] - Mensaje de error opcional.
 * @return {stream.Transform} - Retorna el stream con el manejo de errores y el spinner.
 */
function handleTaskWithSpinnerAndErrors(spinner, taskName, successMessage = null, errorMessage = null) {
    // Mensaje por defecto si no se proporciona
    const defaultSuccessMessage = `Tarea ${taskName} completada.`;
    const defaultErrorMessage = `Error durante la tarea ${taskName}.`;

    // Si los mensajes de éxito y error no están definidos, usar mensajes por defecto
    const finalSuccessMessage = successMessage || defaultSuccessMessage;
    const finalErrorMessage = errorMessage || defaultErrorMessage;

    return through2.obj((file, enc, cb) => {
        cb(null, file);
    }).on('end', () => {
        spinner.succeed(finalSuccessMessage);
    }).on('error', (err) => {
        spinner.fail(finalErrorMessage);
        log.error(`Error en la tarea ${taskName}:`, err.message);
    });
}

/**
 * Maneja una tarea sincrónica con un spinner en la terminal.
 *
 * @param {ChildProcess} spinnerProcess - El proceso del spinner que se comunica con `spinner.js` para mostrar la animación.
 * @param {string} taskName - El nombre de la tarea que se está ejecutando, usado para crear mensajes personalizados de error.
 * @param {string} [errorMessage=null] - Mensaje de error personalizado en caso de fallo de la tarea. Usa un mensaje por defecto si no se proporciona.
 * @returns {Stream} - Un stream transformado que maneja el spinner y los mensajes de error.
 */
function handleSyncTaskWithSpinnerAndErrors(spinnerProcess, taskName, errorMessage = null) {
    const defaultErrorMessage = `Error durante la tarea ${taskName}.`;
    const finalErrorMessage = errorMessage || defaultErrorMessage;

    return through2.obj((file, enc, cb) => {
        cb(null, file);
    })
        .on('finish', () => {
            spinnerProcess.send({ type: 'succeed' });
        })
        .on('error', (err) => {
            spinnerProcess.send({ type: 'fail' });
            log.error(finalErrorMessage);
            log.error(`Error en la tarea ${taskName}:`, err.message);
        });
}

/**
 * Copia literal de archivos que pertenecen a VENDORS.
 * Esta tarea es útil para mover archivos específicos de bibliotecas o dependencias que
 * no necesitan procesamiento adicional y solo deben estar disponibles en el proyecto.
 *
 * @return {stream.Writable} - Stream de Gulp que finaliza la tarea de copia.
 */
function vendorsCopy() {

    const spinner = ora({
        text: "Iniciando la copia de archivos VENDORS...",
        spinner: "fingerDance",
    }).start();

    if (filesToVendors.length > 0) {
        // Copia los archivos y maneja errores con errorHandler
        const stream = src( filesToVendors ).
        pipe( errorHandler( "vendorsCopy" ) ) // Manejo de errores
            .pipe( dest( paths.vendors.dest ) );

        return stream.pipe(
            handleTaskWithSpinnerAndErrors(
                spinner,
                "vendorsCopy",
                `Se han copiado un total de ${ filesToVendors.length } archivos a '${ paths.vendors.dest }'.`,
                "Error durante la copia de archivos VENDORS",
            ),
        );
    }
    spinner.succeed(
        "No se han copiado archivos porque 'filesToVendors' está vacío.",
    );
    return src( ".", { allowEmpty: true } );
}

/**
 * Copia literal de archivos que pertenecen a VENDORS en carpeta JS/.
 *
 * Tarea encargada de realizar copias exactas de archivos que pertenecen a módulos de NODE
 * o a VENDORS en general que no son instalables desde NODE y se copian en JS/
 */
function vendorsCopyJs() {
    const spinner = ora({
        text: "Iniciando la copia de archivos VENDORS JS...",
        spinner: "fingerDance",
    }).start();

    if (filesToVendorsJs.length > 0) {
        // Copia los archivos y maneja errores con errorHandler
        const stream = src( filesToVendorsJs ).
        pipe( errorHandler( "vendorsCopyJs" ) ) // Manejo de errores
            .pipe( dest( paths.scripts.dest ) ) ;

        return stream.pipe(
            handleTaskWithSpinnerAndErrors(
                spinner,
                "vendorsCopyJs",
                `Se han copiado un total de ${ filesToVendorsJs.length } archivos a '${ paths.vendors.dest }'.`,
                "Error durante la copia de archivos VENDORS JS",
            ),
        );
    }
    spinner.succeed(
        "No se han copiado archivos porque 'filesToVendorsJs' está vacío.",
    );
    return src( ".", { allowEmpty: true } );
}

/**
 * Minificación de archivos JS que están en la carpeta partials/.
 *
 * Tarea encargada de copiar los archivos JS de la carpeta partials a la carpeta distribuida
 * En caso de ser producción minifica el archivo sin añadir extensión.
 */
function minPartialsJs() {
    const spinner = ora({
        text: "Iniciando la minificación de archivos JS de la carpeta partials...",
        spinner: "fingerDance",
    }).start();

    // Variables para el conteo de archivos
    let totalFiles = 0;
    let totalSizeBefore = 0;
    let totalSizeAfter = 0;

    // Crear el stream
    const stream = src( paths.scripts.src + "/partials/*.js" ).
    pipe( errorHandler( "minPartialsJs" ) ).
    pipe(
        through2.obj( ( file, enc, cb ) => {
            // Solo contar si el archivo no está en modo stream
            if (! file.isStream()) {
                totalFiles++;
                totalSizeBefore += file.contents.length;
            }
            cb( null, file );
        } ),
    ).
    pipe(
        esbuild( {
            minify: true,
            target: "es2015", // o la versión de ECMAScript que necesites
        } ),
    ).
    pipe(
        through2.obj( ( file, enc, cb ) => {
            // Solo contar si el archivo no está en modo stream
            if (! file.isStream()) {
                totalSizeAfter += file.contents.length;
            }
            cb( null, file );
        } ),
    ).
    pipe( dest( paths.scripts.dest ) );

    // Manejo del spinner para eventos 'end' y 'error'
    stream.
    on( "end", () => {
        if (totalFiles === 0) {
            spinner.succeed(
                "No se encontraron archivos JS para procesar." );
        } else {
            const totalSizeBeforeMB = prettyBytes( totalSizeBefore );
            const totalSizeAfterMB = prettyBytes( totalSizeAfter );
            const totalSizeSaved = totalSizeBefore - totalSizeAfter;
            const totalSizeSavedMB = prettyBytes( totalSizeSaved );
            const savedPercentage = (
                ( totalSizeSaved / totalSizeBefore ) *
                100
            ).toFixed( 2 );

            spinner.succeed(
                "Minificación completada.\n" +
                `${ totalFiles } archivos procesados.\n` +
                `Tamaño original: ${ totalSizeBeforeMB }, Tamaño minificado: ${ totalSizeAfterMB }.\n` +
                `Espacio ahorrado: ${ totalSizeSavedMB } (${ savedPercentage }% de ahorro).`,
            );
        }
    } ).
    on( "error", ( err ) => {
        spinner.fail( "Error durante la minificación de JavaScript." );
        log.error( "Error en la tarea de minificación de JavaScript:",
            err.message );
    } );

    // Retornar el stream
    return stream;
}

/**
 * Compilación de archivos SCSS a CSS.
 * Utiliza Dart Sass para compilar los archivos SCSS en un solo archivo CSS. Se incluye
 * el manejo de errores y la generación de sourcemaps para facilitar la depuración.
 *
 * @param {string} inputFile - La ruta del archivo SCSS a compilar.
 * @return {stream.Transform} - Stream de Gulp con el CSS compilado.
 */
function compileSass(inputFile) {
    //let inputFile= paths.styles.sass + "/style.scss";
    return src(inputFile)
        //.pipe(errorHandler("compileSass"))
        .pipe(sourcemaps.init())
        .pipe(
            sass({
                includePaths: ["node_modules"],
                outputStyle: "expanded",
                quietDeps: !showLogs,
            }).on("error", sass.logError)
        )
        .pipe(replace(/url\(['"]?(?:\.\.\/)?(fonts|img)\/([^'"]+)['"]?\)/g, 'url(assets/dist/$1/$2)'))  // Reemplaza las rutas de fuentes e imágenes
        .pipe(replace(/Version:\s*\d+\.\d+\.\d+/, `Version: ${themeVersion}`))  // Reemplaza la versión en la cabecera
        .pipe(
            gulpIf(
                !isProd,
                sourcemaps.write(
                    '.',
                    {   // Escribe los sourcemaps en un archivo separado en ../maps
                        includeContent: false,              // Excluye el contenido de los archivos fuente en el sourcemap
                        sourceRoot: paths.styles.sass            // Ajusta la ruta de los archivos fuente en el sourcemap
                    }
                )
            )
        );
}

/**
 * Reemplaza rutas relativas en el CSS.
 *
 * @param {stream.Transform} stream - El stream de Gulp con el contenido del CSS.
 * @return {NodeJS.ReadWriteStream} - Retorna el stream con las rutas relativas reemplazadas.
 */
function replacePaths( stream ) {
    return stream.pipe( replace( /(\.\.\/)+/g, "../" ) );
}

/*

/**
 * Minimiza el CSS.
 * Si el entorno es de producción, se realiza la minificación del CSS y se le agrega un sufijo `.min`.
 *
 * @param {stream.Readable} inputStream - El stream de entrada con archivos CSS.
 * @param {string} outputFile - El nombre del archivo de salida para el CSS minimizado.
 * @return {stream.Writable} - Retorna el stream de salida con el CSS minimizado.
 */
function minifyCss(inputStream, outputFile) {
    return inputStream
        .pipe(
            gulpIf(
                isProd,
                cleanCss()
            )
        )
        .pipe(
            gulpIf(
                isProd,
                rename({
                    suffix: ".min",
                }),
            ),
        );
}



/**
 * 
 * Compila archivos SCSS a CSS mostrando un spinner durante el proceso.
 *
 * @returns {Promise} - Una promesa que se resuelve al completar la tarea con éxito o se rechaza en caso de error.
 */
function css() {
    return new Promise((resolve, reject) => {
        const spinnerProcess = fork('./gulp/spinner.js');

        // Enviar configuración al proceso del spinner
        spinnerProcess.send({
            type: 'start',
            startMessage: 'Compilando SCSS a CSS...',
            successMessage: 'Compilación de CSS completada exitosamente.',
            errorMessage: 'Error durante la compilación de CSS.',
            spinnerType: 'dots8Bit'
        });

        const stream = minifyCss(
            replacePaths(
                compileSass(
                    paths.styles.sass + "/style.scss"
                )
            ),
            "style.css"
        );

        let finished = false;

        stream
            .pipe(dest(paths.styles.dest)) // Primero se guarda el CSS compilado
            .pipe(postcss([autoprefixer()])) // Luego se le aplican los plugins de PostCSS
            .pipe(dest(paths.styles.dest)) // Se guarda nuevamente el archivo CSS con autoprefixer aplicado
            .pipe(handleSyncTaskWithSpinnerAndErrors(spinnerProcess, 'css'))
            .on('finish', () => {
                if (!finished) {
                    spinnerProcess.send({ type: 'finish' });
                    finished = true;
                }
            })
            .on('error', () => {
                if (!finished) {
                    spinnerProcess.send({ type: 'finish' });
                    finished = true;
                }
            });

        spinnerProcess.on('message', (msg) => {
            if (msg.type === 'done') {
                if (msg.exitCode === 0) {
                    resolve();
                } else {
                    reject(new Error('Proceso del spinner terminó con error.'));
                }
                // Limpiar y terminar el proceso del spinner
                spinnerProcess.removeAllListeners();
                spinnerProcess.kill();
            }
        });

        // En caso de error en el proceso del spinner
        spinnerProcess.on('error', () => {
            reject(new Error('Error en el proceso del spinner.'));
            spinnerProcess.removeAllListeners();
            spinnerProcess.kill();
        });
    });
}

/**
 * Compila archivos SCSS a CSS mostrando un spinner durante el proceso.
 *
 * @returns {Promise} - Una promesa que se resuelve al completar la tarea con éxito o se rechaza en caso de error.
 */
function adminCss() {

    return new Promise((resolve, reject) => {
        const spinnerProcess = fork('./gulp/spinner.js');

        // Enviar configuración al proceso del spinner
        spinnerProcess.send({
            type: 'start',
            startMessage: 'Compilando SCSS a CSS para admin...',
            successMessage: 'Compilación de CSS para admin completada exitosamente.',
            errorMessage: 'Error durante la compilación de CSS para admin.',
            spinnerType: 'dots8Bit'
        });

        const stream = minifyCss(
            replacePaths(
                compileSass(
                    paths.styles.sass + "/admin.scss"
                )
            ),
            "admin.css"
        );

        let finished = false;

        stream
            .pipe(dest(paths.styles.dest))
            .pipe(handleSyncTaskWithSpinnerAndErrors(spinnerProcess, 'adminCss'))
            .on('finish', () => {
                if (!finished) {
                    spinnerProcess.send({ type: 'finish' });
                    finished = true;
                }
            })
            .on('error', () => {
                if (!finished) {
                    spinnerProcess.send({ type: 'finish' });
                    finished = true;
                }
            });

        spinnerProcess.on('message', (msg) => {
            if (msg.type === 'done') {
                if (msg.exitCode === 0) {
                    resolve();
                } else {
                    reject(new Error('Proceso del spinner terminó con error.'));
                }
                // Limpiar y terminar el proceso del spinner
                spinnerProcess.removeAllListeners();
                spinnerProcess.kill();
            }
        });

        // En caso de error en el proceso del spinner
        spinnerProcess.on('error', () => {
            reject(new Error('Error en el proceso del spinner.'));
            spinnerProcess.removeAllListeners();
            spinnerProcess.kill();
        });
    });
}

/**
 * Combina archivos JS en un solo archivo.
 *
 * @return {stream.Writable} - Retorna el stream de Gulp con los archivos JS combinados.
 */
function concatJs() {
    const spinner = ora({
        text: "Uniendo archivos JS...",
        spinner: "fistBump",
    }).start();

    // Crear el flujo de trabajo
    const stream = src( filesToAllJs ).
    pipe( errorHandler( "concatJs" ) ).
    pipe( sourcemaps.init() ).
    pipe( concat( "all.js" ) ).
    pipe( sourcemaps.write( "." ) ).
    pipe( dest( paths.scripts.dest ) );

    return stream.pipe(
        handleTaskWithSpinnerAndErrors(
            spinner,
            "concatJs",
            "Concatenación de archivos JS completada.",
            "Error durante la concatenación de archivos JS",
        ),
    );
}

/**
 * Minifica el archivo JS combinado.
 *
 * @return {stream.Writable} - Retorna el stream de Gulp con el archivo JS minificado.
 */
function minifyJs() {

    return new Promise((resolve, reject) => {
        const spinnerProcess = fork( './gulp/spinner.js' );

        // Enviar configuración al proceso del spinner
        spinnerProcess.send( {
            type: 'start',
            startMessage: 'Minificando archivo all.js...',
            successMessage: 'Minificación de archivo all.min.js completada.',
            errorMessage: 'Error durante la minificación de archivo all.min.js',
            spinnerType: 'fistBump'
        } );

        // Crear el flujo de trabajo
        const stream = src( paths.scripts.dest + "/all.js" ).
        pipe( errorHandler( "minifyJs" ) ).
        pipe( terser() ).
        pipe( rename( "all.min.js" ) ).
        pipe( dest( paths.scripts.dest ) );

        let finished = false;

        stream
            .pipe(handleSyncTaskWithSpinnerAndErrors(spinnerProcess, 'minifyJs'))
            .on('finish', () => {
                if (!finished) {
                    spinnerProcess.send({ type: 'finish' });
                    finished = true;
                }
            })
            .on('error', () => {
                if (!finished) {
                    spinnerProcess.send({ type: 'finish' });
                    finished = true;
                }
            });

        spinnerProcess.on('message', (msg) => {
            if (msg.type === 'done') {
                if (msg.exitCode === 0) {
                    resolve();
                } else {
                    reject(new Error('Proceso del spinner terminó con error.'));
                }
                // Limpiar y terminar el proceso del spinner
                spinnerProcess.removeAllListeners();
                spinnerProcess.kill();
            }
        });

        // En caso de error en el proceso del spinner
        spinnerProcess.on('error', () => {
            reject(new Error('Error en el proceso del spinner.'));
            spinnerProcess.removeAllListeners();
            spinnerProcess.kill();
        });
    });
}

/**
 * Combina y minifica archivos JS
 */
const js = series( concatJs, minifyJs );

/**
 * Optimización de imágenes
 */
function img() {
    const spinner = ora({
        text: "Optimizando imágenes...",
        spinner: "moon",
    }).start();

    let stream = src(paths.images.src, { encoding: false })
        .pipe(errorHandler('optimizeImageFormats'))
        .pipe(
            imagemin(
                [
                    imageminMozjpeg({
                        quality: 75,
                        progressive: true,
                    }),
                    imageminPngquant({
                        speed: 5,
                        strip: true,
                        quality: [0.3, 0.5],
                    }),
                    imageminGifsicle({
                        optimizationLevel: 3,
                        strip: true,
                        quality: [0.3, 0.5],
                    }),
                    imageminSvgo({
                        plugins: [
                            {
                                name: 'removeViewBox',
                                active: true,
                            },
                        ],
                    }),
                    imageminWebp({
                        quality: 75,
                    }),
                ],
                {
                    verbose: showLogs,
                    silent: !showLogs,
                }
            )
        );

    if (showLogs) {
        stream = stream.on("data", (file) => {
            // Guardamos el mensaje actual del spinner para no perderlo
            spinner.clear();
            log(`Processing file: ${file.path}`);
            spinner.render(); // Restauramos el spinner en la misma posición
        });
    }

    // Copiamos las imágenes optimizadas al destino final
    stream = stream.pipe(dest(paths.images.dest));

    return stream.pipe(
        handleTaskWithSpinnerAndErrors(
            spinner,
            "img",
            "Optimización de imágenes completada.",
            "Error durante la optimización de imágenes.",
        ),
    );
}

/**
 * Copia de fuentes
 */
function fonts() {

    const spinner = ora({
        text: "Copiando fuentes...",
        spinner: "dwarfFortress",
    }).start();

    const stream = src(paths.fonts.src)
        .pipe(errorHandler("fonts"))
        .pipe(gulpCopy(paths.fonts.dest, { prefix: 1 }));

    return stream.pipe(
        handleTaskWithSpinnerAndErrors(
            spinner,
            "fonts",
            "Fuentes copiadas correctamente.",
            "Error durante la copia de fuentes."
        )
    );
}

/**
 * Tarea para observar cambios y recompilar automáticamente
 */
function watchFiles() {
    watch( paths.styles.sass + "/**/*.scss", series( css ) );
    watch( paths.scripts.src + "/**/*.js", series( js ) );
    watch( paths.scripts.src + "/partials/*.js", series( minPartialsJs ) );
    watch( paths.images.src + "/**/*", series( img ) ); // Observa cambios en imágenes
}

/**
 * Eliminación de CSS no utilizado con PurgeCSS
 */
function purgeCss() {
    // Crear el spinner
    const spinner = ora( "Purgando CSS..." ).start();
    spinner.spinner = "dwarfFortress";

    if (! purgar) {
        // Si `purgar` es falso, simplemente muestra un mensaje y termina el proceso
        return src( ".", { allowEmpty: true } ).
        pipe( errorHandler( "purgeCss" ) ). // Manejo de errores
            on( "end", () => {
                spinner.succeed(
                    "No se han purgado los CSS porque no está habilitado" );
            } );
    } else {

        const stream = src( paths.styles.dest + "/style.css" ).
        pipe( errorHandler( "purgeCss" ) ).
        pipe(
            purgecss( {
                content: [ "**/*.php", "**/*.html" ], // Ruta a tus archivos PHP y HTML para detectar el CSS utilizado
                defaultExtractor: ( content ) => content.match(
                    /[\w-/:]+(?<!:)/g ) || [],
                safelist: {
                    standard: [
                        // Clases de Bootstrap 5 que deseas conservar
                        "container",
                        "row",
                        "col",
                        "navbar",
                        "dropdown",
                        "btn",
                        "show",
                    ],
                    deep: [
                        /^bs-/, // Patrones de expresiones regulares para clases de Bootstrap generadas por JavaScript
                        /^swiper-/, // Otros patrones necesarios
                        /^badge.*/,
                        /^breadcrumb.*/,
                        /^btn.*/,
                        /^btn-group.*/,
                        /^card.*/,
                        /^carousel.*/,
                        /^form-.*/,
                        /^custom.*/,
                        /^modal.*/,
                        /^table.*/,
                        /^container.*/,
                        /^row.*/,
                        /^col.*/,
                        /^p.*/,
                        /^m.*/,
                        /^w.*/,
                        /^bg-.*/,
                        /^border-.*/,
                        /^text-.*/,
                        /^align-.*/,
                        /^justify.*/,
                    ],
                },
            } ),
        ).
        pipe( dest( paths.styles.dest ) );

        return stream.pipe(
            handleTaskWithSpinnerAndErrors(
                spinner,
                "purgeCss",
                "Se han purgado los CSS correctamente",
                "Error durante el purgado de CSS.",
            ),
        );
    }
}

/**
 * Tareas exportadas
 */
export {
    vendorsCopy,
    vendorsCopyJs,
    compileSass,
    css,
    adminCss,
    concatJs,
    minifyJs,
    js,
    img,
    minPartialsJs,
    fonts,
    purgeCss,
    watchFiles,
};
export default series(
    vendorsCopy,
    vendorsCopyJs,
    css,
    adminCss,
    js,
    minPartialsJs,
    img,
    fonts,
    purgeCss,
);
export const serve = parallel(
    vendorsCopy,
    vendorsCopyJs,
    css,
    adminCss,
    js,
    minPartialsJs,
    img,
    fonts,
    purgeCss,
    watchFiles,
);
