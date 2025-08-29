<?php
/**
 * Servicios Columnas Block - Bloque de servicios con columnas dinámicas
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Servicios Columnas
 */
function amentum_register_servicios_columnas_block() {
    register_block_type('amentum/servicios-columnas', array(
        'render_callback' => 'amentum_render_servicios_columnas_block',
        'attributes' => array(
            'elementos' => array(
                'type' => 'array',
                'default' => array(
                    array(
                        'titulo' => 'QUÉ HACEMOS',
                        'contenido' => 'Diseñamos, planificamos y producimos eventos a medida: selección del espacio ideal, diseño y decoración personalizada, experiencias gastronómicas únicas y coordinación integral de proveedores y servicios.'
                    ),
                    array(
                        'titulo' => 'CÓMO LO HACEMOS',
                        'contenido' => 'Diseñamos eventos únicos y personalizados, escuchando tus necesidades y cuidando cada detalle para que los asistentes vivan experiencias memorables.'
                    ),
                    array(
                        'titulo' => 'PARA QUIÉN',
                        'contenido' => 'Organizamos desde reuniones íntimas hasta celebraciones de gran escala (400-500 personas) para empresas, instituciones, organismos oficiales y clientes particulares.'
                    )
                )
            ),
            'columnasPorFila' => array(
                'type' => 'number',
                'default' => 3
            ),
            'ctaTexto' => array(
                'type' => 'string',
                'default' => '¿Evento a la vista? Deja que lo hagamos perfecto.'
            ),
            'ctaBoton' => array(
                'type' => 'string',
                'default' => 'Cuéntanos tu idea'
            ),
            'ctaUrl' => array(
                'type' => 'string',
                'default' => '#contacto'
            ),
            'backgroundColor' => array(
                'type' => 'string',
                'default' => '#f8f6f3'
            ),
            'textColor' => array(
                'type' => 'string',
                'default' => '#333333'
            )
        )
    ));
}
add_action('init', 'amentum_register_servicios_columnas_block');

/**
 * Renderizar el bloque Servicios Columnas
 */
function amentum_render_servicios_columnas_block($attributes) {
    $elementos = !empty($attributes['elementos']) ? $attributes['elementos'] : array();
    $columnas_por_fila = !empty($attributes['columnasPorFila']) ? intval($attributes['columnasPorFila']) : 3;
    $cta_texto = !empty($attributes['ctaTexto']) ? esc_html($attributes['ctaTexto']) : '¿Evento a la vista? Deja que lo hagamos perfecto.';
    $cta_boton = !empty($attributes['ctaBoton']) ? esc_html($attributes['ctaBoton']) : 'Cuéntanos tu idea';
    $cta_url = !empty($attributes['ctaUrl']) ? esc_url($attributes['ctaUrl']) : '#contacto';
    $background_color = !empty($attributes['backgroundColor']) ? esc_attr($attributes['backgroundColor']) : '#f8f6f3';
    $text_color = !empty($attributes['textColor']) ? esc_attr($attributes['textColor']) : '#333333';

    ob_start();
    ?>
    <section class="block-servicios-columnas" style="background-color: <?php echo $background_color; ?>; color: <?php echo $text_color; ?>;">
        <div class="container">
            <div class="servicios-columnas-content">
                <?php if (!empty($elementos)) : ?>
                    <div class="servicios-grid" data-columns="<?php echo $columnas_por_fila; ?>">
                        <?php foreach ($elementos as $elemento) : ?>
                            <div class="servicio-columna">
                                <?php if (!empty($elemento['titulo'])) : ?>
                                    <h3 class="servicio-titulo"><?php echo esc_html($elemento['titulo']); ?></h3>
                                <?php endif; ?>
                                
                                <?php if (!empty($elemento['contenido'])) : ?>
                                    <div class="servicio-contenido">
                                        <?php echo wp_kses_post(wpautop($elemento['contenido'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($cta_texto) || !empty($cta_boton)) : ?>
                    <div class="servicios-cta">
                        <?php if (!empty($cta_texto)) : ?>
                            <div class="cta-texto">
                                <p><?php echo $cta_texto; ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($cta_boton)) : ?>
                            <div class="cta-boton">
                                <a href="<?php echo $cta_url; ?>" class="btn-cta"><?php echo $cta_boton; ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue assets específicos para Servicios Columnas block (Frontend)
 */
function amentum_enqueue_servicios_columnas_block_assets() {
    // Solo cargar si el bloque está presente en la página
    if (has_block('amentum/servicios-columnas')) {
        wp_enqueue_style(
            'amentum-servicios-columnas-style',
            get_template_directory_uri() . '/blocks/servicios-columnas/style.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_servicios_columnas_block_assets');

/**
 * Enqueue editor assets para Servicios Columnas block (Backend)
 */
function amentum_enqueue_servicios_columnas_block_editor_assets() {
    // JS del editor
    wp_enqueue_script(
        'amentum-servicios-columnas-editor',
        get_template_directory_uri() . '/blocks/servicios-columnas/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // CSS del editor (para que se vea bien en el backend)
    wp_enqueue_style(
        'amentum-servicios-columnas-editor-style',
        get_template_directory_uri() . '/blocks/servicios-columnas/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_servicios_columnas_block_editor_assets');