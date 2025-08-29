<?php
/**
 * Cuatro Imágenes Block - Muestra 4 imágenes en grid responsive
 *
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Cuatro Imágenes
 */
function amentum_register_cuatro_imagenes_block() {
    register_block_type('amentum/cuatro-imagenes', array(
        'render_callback' => 'amentum_render_cuatro_imagenes_block',
        'attributes' => array(
            'imagen1' => array(
                'type' => 'string',
                'default' => get_template_directory_uri() . '/assets/images/galeria-proyecto-1.png'
            ),
            'imagen2' => array(
                'type' => 'string',
                'default' => get_template_directory_uri() . '/assets/images/galeria-proyecto-2.png'
            ),
            'imagen3' => array(
                'type' => 'string',
                'default' => get_template_directory_uri() . '/assets/images/galeria-proyecto-3.png'
            ),
            'imagen4' => array(
                'type' => 'string',
                'default' => get_template_directory_uri() . '/assets/images/galeria-proyecto-4.png'
            ),
        )
    ));
}
add_action('init', 'amentum_register_cuatro_imagenes_block');

/**
 * Renderizar el bloque Cuatro Imágenes
 */
function amentum_render_cuatro_imagenes_block($attributes) {
    // Sanitizar datos de entrada
    $imagen1 = !empty($attributes['imagen1']) ? esc_url($attributes['imagen1']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-1.png';
    $imagen2 = !empty($attributes['imagen2']) ? esc_url($attributes['imagen2']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-2.png';
    $imagen3 = !empty($attributes['imagen3']) ? esc_url($attributes['imagen3']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-3.png';
    $imagen4 = !empty($attributes['imagen4']) ? esc_url($attributes['imagen4']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-4.png';

    ob_start();
    ?>
    <section class="block-cuatro-imagenes">
        <div class="container">
            <div class="cuatro-imagenes-grid">
                <div class="imagen-item">
                    <img src="<?php echo $imagen1; ?>" alt="Proyecto de galería 1" loading="lazy">
                </div>
                <div class="imagen-item">
                    <img src="<?php echo $imagen2; ?>" alt="Proyecto de galería 2" loading="lazy">
                </div>
                <div class="imagen-item">
                    <img src="<?php echo $imagen3; ?>" alt="Proyecto de galería 3" loading="lazy">
                </div>
                <div class="imagen-item">
                    <img src="<?php echo $imagen4; ?>" alt="Proyecto de galería 4" loading="lazy">
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue editor assets para Cuatro Imágenes block (Backend)
 */
function amentum_enqueue_cuatro_imagenes_block_editor_assets() {
    wp_enqueue_script(
        'amentum-cuatro-imagenes-editor',
        get_template_directory_uri() . '/blocks/cuatro-imagenes/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_cuatro_imagenes_block_editor_assets');