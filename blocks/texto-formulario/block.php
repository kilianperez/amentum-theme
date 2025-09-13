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

        <style>
        #<?php echo esc_attr($block_id); ?> {
            width: 100%;
            max-width: 100%;
            padding: 40px 20px;
            background-color: #f8f9fa;
        }

        #<?php echo esc_attr($block_id); ?> .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        #<?php echo esc_attr($block_id); ?> .texto-formulario-content {
            display: grid;
            grid-template-columns: 1fr 1.5fr; /* 4/12 vs 6/12 aproximadamente */
            gap: <?php echo esc_attr($espacio_cols); ?>;
            align-items: start;
            max-width: 100%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        #<?php echo esc_attr($block_id); ?> .texto-column {
            padding-right: 1rem;
        }

        #<?php echo esc_attr($block_id); ?> .texto-title {
            margin: 0 0 1.5rem 0;
            font-size: 2.5rem;
            font-weight: 600;
            line-height: 1.2;
            color: #2c3e50;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #<?php echo esc_attr($block_id); ?> .texto-description {
            font-size: 1.125rem;
            line-height: 1.7;
            color: #4a5568;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #<?php echo esc_attr($block_id); ?> .formulario-column {
            min-height: 300px;
        }

        #<?php echo esc_attr($block_id); ?> .formulario-placeholder {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        #<?php echo esc_attr($block_id); ?> .placeholder-content .placeholder-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.6;
        }

        #<?php echo esc_attr($block_id); ?> .placeholder-content h3 {
            margin: 0 0 1rem 0;
            color: #495057;
            font-size: 1.5rem;
            font-weight: 600;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #<?php echo esc_attr($block_id); ?> .placeholder-content p {
            margin: 0;
            color: #6c757d;
            font-size: 1rem;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Formulario dentro del bloque */
        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque.amentum-formulario-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            color: #ffffff;
        }

        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque h2,
        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque h3,
        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque label {
            color: #ffffff !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque input,
        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque textarea,
        #<?php echo esc_attr($block_id); ?> .formulario-en-bloque select {
            background-color: rgba(255,255,255,0.95);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #<?php echo esc_attr($block_id); ?> {
                padding: 20px 15px;
            }

            #<?php echo esc_attr($block_id); ?> .texto-formulario-content {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 20px;
            }

            #<?php echo esc_attr($block_id); ?> .texto-column {
                padding-right: 0;
            }

            #<?php echo esc_attr($block_id); ?> .texto-title {
                font-size: 2rem;
            }

            #<?php echo esc_attr($block_id); ?> .texto-description {
                font-size: 1rem;
            }

            #<?php echo esc_attr($block_id); ?> .formulario-en-bloque.amentum-formulario-container {
                padding: 1.5rem;
            }
        }
        </style>
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