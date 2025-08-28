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
                require_once $block_file;
            }
        }
    }
}

// Cargar todos los bloques
amentum_load_individual_blocks();

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