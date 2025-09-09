<?php
/**
 * Eventos Destacados Block - Muestra 4 eventos destacados en grid responsive
 *
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Eventos Destacados
 */
function amentum_register_eventos_destacados_block() {
    register_block_type('amentum/eventos-destacados', array(
        'render_callback' => 'amentum_render_eventos_destacados_block',
        'attributes' => array(
            'titulo' => array(
                'type' => 'string',
                'default' => 'Sobre nosotros'
            ),
            'descripcion' => array(
                'type' => 'string',
                'default' => 'Amentum nace de la pasión por la gastronomía, la excelencia en los detalles y el arte de reunir a las personas.'
            ),
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
add_action('init', 'amentum_register_eventos_destacados_block');

/**
 * Renderizar el bloque Eventos Destacados
 */
function amentum_render_eventos_destacados_block($attributes) {
    // Sanitizar datos de entrada
    $titulo = !empty($attributes['titulo']) ? esc_html($attributes['titulo']) : 'Sobre nosotros';
    $descripcion = !empty($attributes['descripcion']) ? esc_html($attributes['descripcion']) : 'Amentum nace de la pasión por la gastronomía, la excelencia en los detalles y el arte de reunir a las personas.';
    $imagen1 = !empty($attributes['imagen1']) ? esc_url($attributes['imagen1']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-1.png';
    $imagen2 = !empty($attributes['imagen2']) ? esc_url($attributes['imagen2']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-2.png';
    $imagen3 = !empty($attributes['imagen3']) ? esc_url($attributes['imagen3']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-3.png';
    $imagen4 = !empty($attributes['imagen4']) ? esc_url($attributes['imagen4']) : get_template_directory_uri() . '/assets/images/galeria-proyecto-4.png';

    ob_start();
    ?>
    <section class="block-eventos-destacados">
        <div class="container">
            <div class="eventos-destacados-header">
                <h2><?php echo $titulo; ?></h2>
                <p><?php echo nl2br($descripcion); ?></p>
                <div class="eventos-link">
                    <a href="<?php echo get_post_type_archive_link('eventos'); ?>">Nuestros eventos</a>
                </div>
            </div>
            <div class="eventos-destacados-grid">
                <div class="imagen-item">
                    <img src="<?php echo $imagen1; ?>" alt="Evento destacado 1" loading="lazy">
                </div>
                <div class="imagen-item">
                    <img src="<?php echo $imagen2; ?>" alt="Evento destacado 2" loading="lazy">
                </div>
                <div class="imagen-item">
                    <img src="<?php echo $imagen3; ?>" alt="Evento destacado 3" loading="lazy">
                </div>
                <div class="imagen-item">
                    <img src="<?php echo $imagen4; ?>" alt="Evento destacado 4" loading="lazy">
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}