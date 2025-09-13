<?php
/**
 * Bloque Texto + Formulario - Layout 4/6 con espacio
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque texto + formulario
 */
function amentum_register_texto_formulario_block()
{
    register_block_type('amentum/texto-formulario', array(
        'render_callback' => 'amentum_render_texto_formulario_block',
        'attributes' => array(
            'titulo' => array(
                'type' => 'string',
                'default' => 'Soy Beatriz Llamas'
            ),
            'contenido' => array(
                'type' => 'string',
                'default' => 'Crecí en Madrid, rodeada de pasión por la gastronomía y el cuidado de los detalles.'
            ),
            'formularioId' => array(
                'type' => 'number',
                'default' => 0
            ),
            'espacioEntreCols' => array(
                'type' => 'string',
                'default' => '2rem'
            )
        ),
        'supports' => array(
            'align' => array('wide', 'full'),
            'anchor' => true
        )
    ));
}
add_action('init', 'amentum_register_texto_formulario_block');

/**
 * Callback para renderizar el bloque
 */
function amentum_render_texto_formulario_block($attributes, $content)
{
    $titulo = isset($attributes['titulo']) ? $attributes['titulo'] : 'Soy Beatriz Llamas';
    $contenido = isset($attributes['contenido']) ? $attributes['contenido'] : 'Crecí en Madrid, rodeada de pasión por la gastronomía...';
    $formulario_id = isset($attributes['formularioId']) ? $attributes['formularioId'] : 0;
    $espacio_cols = isset($attributes['espacioEntreCols']) ? $attributes['espacioEntreCols'] : '2rem';

    // Generar ID único para el bloque
    $block_id = 'amentum-texto-formulario-' . uniqid();

    ob_start();
    ?>
    <section class="block-texto-formulario" id="<?php echo esc_attr($block_id); ?>">
        <div class="container">
            <div class="texto-formulario-content">

                <!-- Columna de Texto (4/12) -->
                <div class="texto-column">
                    <?php if ($titulo): ?>
                        <h2 class="texto-title"><?php echo wp_kses_post($titulo); ?></h2>
                    <?php endif; ?>

                    <?php if ($contenido): ?>
                        <p class="texto-description"><?php echo wp_kses_post($contenido); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Columna de Formulario (6/12) -->
                <div class="formulario-column">
                    <?php if ($formulario_id > 0): ?>
                        <?php
                        // Usar el shortcode de formulario
                        $shortcode_params = array(
                            'id' => $formulario_id,
                            'titulo' => 'true',
                            'descripcion' => 'true',
                            'clase' => 'formulario-en-bloque'
                        );

                        $shortcode = '[amentum_formulario';
                        foreach ($shortcode_params as $key => $value) {
                            $shortcode .= ' ' . $key . '="' . $value . '"';
                        }
                        $shortcode .= ']';

                        echo do_shortcode($shortcode);
                        ?>
                    <?php else: ?>
                        <div class="formulario-placeholder">
                            <div class="placeholder-content">
                                <div class="placeholder-icon">📝</div>
                                <h3>Selecciona un Formulario</h3>
                                <p>Configura este bloque para mostrar un formulario aquí.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- Los estilos ahora son globales via SCSS -->
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue script del editor para el bloque
 */
function amentum_enqueue_texto_formulario_editor_assets()
{
    $editor_js_path = get_template_directory_uri() . '/blocks/texto-formulario/editor.js';

    // Usar timestamp como versión para desarrollo (fuerza actualización)
    $version = filemtime(get_template_directory() . '/blocks/texto-formulario/editor.js');

    wp_enqueue_script(
        'amentum-texto-formulario-editor',
        $editor_js_path,
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'),
        $version,
        true
    );

    // Localizar datos de formularios para el editor
    $formularios = get_posts(array(
        'post_type' => 'formularios',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields' => 'ids'
    ));

    $formularios_options = array();
    foreach ($formularios as $id) {
        $titulo = get_the_title($id);
        $formularios_options[] = array(
            'value' => $id,
            'label' => $titulo
        );
    }

    wp_localize_script('amentum-texto-formulario-editor', 'amentumFormularios', array(
        'formularios' => $formularios_options
    ));
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_texto_formulario_editor_assets');