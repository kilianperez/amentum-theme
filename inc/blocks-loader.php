<?php
/**
 * CARGADOR DE BLOQUES MODULARES AMENTUM
 * Sistema que carga automáticamente todos los bloques individuales
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cargar todos los bloques individuales automáticamente
 * Detecta automáticamente todas las carpetas con archivo block.php
 */
function amentum_load_individual_blocks() {
    $blocks_directory = get_template_directory() . '/blocks/';
    
    // Obtener todas las carpetas del directorio blocks
    if (is_dir($blocks_directory)) {
        $folders = scandir($blocks_directory);
        
        foreach ($folders as $folder) {
            // Saltar . y .. y archivos
            if ($folder === '.' || $folder === '..' || !is_dir($blocks_directory . $folder)) {
                continue;
            }
            
            // Saltar carpeta 'shared' ya que contiene variables CSS globales
            if ($folder === 'shared') {
                continue;
            }
            
            // Verificar si existe block.php en la carpeta
            $block_file = $blocks_directory . $folder . '/block.php';
            if (file_exists($block_file)) {
                error_log('BLOCKS LOADER: Cargando bloque desde ' . $folder);
                if ($folder === 'formulario-selector') {
                    error_log('🎯 BLOCKS LOADER: CARGANDO FORMULARIO-SELECTOR desde: ' . $block_file);
                }
                require_once $block_file;
                if ($folder === 'formulario-selector') {
                    error_log('✅ BLOCKS LOADER: FORMULARIO-SELECTOR cargado exitosamente');
                }
            } else {
                if ($folder === 'formulario-selector') {
                    error_log('❌ BLOCKS LOADER: block.php NO encontrado para formulario-selector en: ' . $block_file);
                }
            }
        }
    }
}

// Cargar todos los bloques
error_log('BLOCKS LOADER: Iniciando carga de bloques individuales');
amentum_load_individual_blocks();
error_log('BLOCKS LOADER: Carga de bloques individuales completada');

/**
 * Enqueue variables CSS globales
 */
function amentum_enqueue_global_block_variables() {
    wp_enqueue_style(
        'amentum-blocks-variables',
        get_template_directory_uri() . '/blocks/shared/variables.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_global_block_variables');
add_action('enqueue_block_editor_assets', 'amentum_enqueue_global_block_variables');

/**
 * Enqueue automático de scripts del editor para todos los bloques
 * Se basa en los nombres de las carpetas y detecta archivos editor.js automáticamente
 */
function amentum_enqueue_all_block_editor_assets() {
    $blocks_directory = get_template_directory() . '/blocks/';
    $blocks_uri = get_template_directory_uri() . '/blocks/';
    
    if (is_dir($blocks_directory)) {
        $folders = scandir($blocks_directory);
        
        foreach ($folders as $folder) {
            // Saltar directorios especiales
            if ($folder === '.' || $folder === '..' || $folder === 'shared' || !is_dir($blocks_directory . $folder)) {
                continue;
            }
            
            $editor_js_file = $blocks_directory . $folder . '/editor.js';

            // Saltar formulario-selector ya que maneja su propio enqueue con datos específicos
            if ($folder === 'formulario-selector') {
                continue;
            }

            // Si existe el archivo editor.js, encolarlo automáticamente
            if (file_exists($editor_js_file)) {
                $script_handle = 'amentum-' . $folder . '-editor';
                wp_enqueue_script(
                    $script_handle,
                    $blocks_uri . $folder . '/editor.js',
                    array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
                    wp_get_theme()->get('Version'),
                    true
                );
                
                // Localizar variables globales para cada script
                wp_localize_script($script_handle, 'wpGlobalSettings', array(
                    'templateDirectoryUri' => get_template_directory_uri()
                ));
            }
        }
    }
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_all_block_editor_assets');

/**
 * Enqueue moderno de CSS para bloques - WordPress 6.3+ Optimizado
 * Usa add_editor_style para el editor + CSS compilado unificado para frontend
 */
function amentum_enqueue_blocks_editor_styles() {
    // Para el EDITOR: usar add_editor_style con especificidad automática
    $compiled_blocks_css = get_template_directory() . '/assets/dist/css/blocks.css';
    
    if (file_exists($compiled_blocks_css)) {
        // Esto automáticamente añade la clase .editor-styles-wrapper
        add_editor_style('assets/dist/css/blocks.css');
    }
}

/**
 * Enqueue CSS unificado para el frontend
 */
function amentum_enqueue_blocks_frontend_styles() {
    // Solo en frontend
    if (!is_admin()) {
        $compiled_blocks_css = get_template_directory() . '/assets/dist/css/blocks.css';
        
        if (file_exists($compiled_blocks_css)) {
            wp_enqueue_style(
                'amentum-blocks-unified',
                get_template_directory_uri() . '/assets/dist/css/blocks.css',
                array(),
                wp_get_theme()->get('Version')
            );
        }
    }
}

// Hooks específicos para mejor rendimiento
add_action('after_setup_theme', 'amentum_enqueue_blocks_editor_styles');
add_action('wp_enqueue_scripts', 'amentum_enqueue_blocks_frontend_styles');

/**
 * Agregar categoría de bloques Amentum
 */
function amentum_blocks_category($categories) {
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'amentum-blocks',
                'title' => __('Bloques Amentum', 'amentum'),
                'icon' => 'star-filled'
            )
        )
    );
}
add_filter('block_categories_all', 'amentum_blocks_category', 10, 2);