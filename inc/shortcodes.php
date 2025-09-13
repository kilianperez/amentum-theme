<?php
/**
 * Shortcodes de Amentum
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode para mostrar formularios
 * Uso: [amentum_formulario id="123"]
 * Uso: [amentum_formulario id="123" titulo="false" descripcion="false"]
 * Uso: [amentum_formulario id="123" clase="mi-clase-personalizada"]
 */
function amentum_formulario_shortcode($atts)
{
    // Normalizar atributos
    $atts = shortcode_atts(array(
        'id' => 0,
        'titulo' => 'true',
        'descripcion' => 'true',
        'clase' => ''
    ), $atts, 'amentum_formulario');

    $formulario_id = intval($atts['id']);
    $mostrar_titulo = ($atts['titulo'] !== 'false');
    $mostrar_descripcion = ($atts['descripcion'] !== 'false');
    $clase_personalizada = sanitize_html_class($atts['clase']);

    // Validar ID
    if (!$formulario_id) {
        return '<div style="padding: 1rem; border: 2px solid #f0ad4e; background: #fcf8e3; text-align: center; border-radius: 4px;">
            <strong>Error en shortcode:</strong> Debes especificar un ID de formulario. Ejemplo: [amentum_formulario id="123"]
        </div>';
    }

    // Obtener el formulario
    $formulario = get_post($formulario_id);

    if (!$formulario || $formulario->post_type !== 'formularios' || $formulario->post_status !== 'publish') {
        return '<div style="padding: 1rem; border: 2px solid #dc3545; background: #f8d7da; text-align: center; color: #721c24; border-radius: 4px;">
            <strong>Error:</strong> Formulario no encontrado o no publicado (ID: ' . $formulario_id . ')
        </div>';
    }

    // Obtener configuración del formulario
    $config = get_post_meta($formulario_id, '_amentum_formulario_config', true);
    $config = !empty($config) ? $config : array();

    $titulo_formulario = get_the_title($formulario_id);
    $descripcion_formulario = isset($config['descripcion']) ? $config['descripcion'] : '';
    $campos_formulario = isset($config['campos']) ? $config['campos'] : array();
    $boton_texto = isset($config['boton_texto']) ? $config['boton_texto'] : 'Enviar';

    if (empty($campos_formulario)) {
        return '<div style="padding: 1rem; border: 2px solid #f0ad4e; background: #fcf8e3; text-align: center; border-radius: 4px;">
            <strong>Formulario: ' . esc_html($titulo_formulario) . '</strong><br>
            Este formulario no tiene campos configurados.
        </div>';
    }

    // Generar ID único para el formulario
    $form_id = 'amentum-form-' . $formulario_id . '-' . uniqid();

    // Clases CSS
    $css_classes = 'amentum-formulario-container';
    if ($clase_personalizada) {
        $css_classes .= ' ' . $clase_personalizada;
    }

    // Iniciar buffer de salida
    ob_start();
    ?>

    <div class="<?php echo esc_attr($css_classes); ?>" id="<?php echo esc_attr($form_id); ?>-container">

        <?php if ($mostrar_titulo && $titulo_formulario): ?>
            <h2 class="amentum-formulario-titulo"><?php echo esc_html($titulo_formulario); ?></h2>
        <?php endif; ?>

        <?php if ($mostrar_descripcion && $descripcion_formulario): ?>
            <div class="amentum-formulario-descripcion">
                <?php echo wp_kses_post(wpautop($descripcion_formulario)); ?>
            </div>
        <?php endif; ?>

        <form id="<?php echo esc_attr($form_id); ?>" class="amentum-formulario" method="post" data-form-id="<?php echo esc_attr($formulario_id); ?>">

            <?php wp_nonce_field('amentum_form_nonce_' . $formulario_id, 'amentum_form_nonce'); ?>
            <input type="hidden" name="formulario_id" value="<?php echo esc_attr($formulario_id); ?>">
            <input type="hidden" name="pagina_origen" value="<?php echo esc_url(get_permalink()); ?>">
            <input type="hidden" name="action" value="amentum_process_form">

            <div class="amentum-formulario-grid">
                <?php
                $current_fieldset = null;

                foreach ($campos_formulario as $campo):
                    $tipo = isset($campo['tipo']) ? $campo['tipo'] : 'text';
                    $nombre = isset($campo['nombre']) ? $campo['nombre'] : '';
                    $ancho = isset($campo['ancho']) ? $campo['ancho'] : '12';
                    $requerido = isset($campo['requerido']) ? $campo['requerido'] : false;
                    $placeholder = isset($campo['placeholder']) ? $campo['placeholder'] : '';
                    $opciones = isset($campo['opciones']) ? $campo['opciones'] : array();

                    // Generar nombre del campo para el formulario
                    $field_name = 'campo_' . sanitize_title($nombre);
                    $field_id = $form_id . '_' . $field_name;

                    // Si es título de sección, cerrar fieldset anterior y abrir nuevo
                    if ($tipo === 'titulo_seccion'):
                        if ($current_fieldset !== null): ?>
                            </fieldset>
                        <?php endif;
                        $current_fieldset = $nombre; ?>

                        <fieldset class="amentum-formulario-fieldset col-<?php echo esc_attr($ancho); ?>">
                            <legend class="amentum-formulario-legend"><?php echo esc_html($nombre); ?></legend>

                    <?php else: ?>

                        <div class="amentum-campo-wrapper col-<?php echo esc_attr($ancho); ?>" data-tipo="<?php echo esc_attr($tipo); ?>">

                            <?php if ($tipo !== 'checkbox'): ?>
                                <label for="<?php echo esc_attr($field_id); ?>" class="amentum-campo-label">
                                    <?php echo esc_html($nombre); ?>
                                    <?php if ($requerido): ?>
                                        <span class="requerido">*</span>
                                    <?php endif; ?>
                                </label>
                            <?php endif; ?>

                            <?php
                            switch ($tipo):
                                case 'text':
                                case 'email':
                                case 'phone':
                                case 'url': ?>
                                    <div class="input-wrapper">
                                        <input
                                            type="<?php echo $tipo === 'phone' ? 'tel' : ($tipo === 'url' ? 'url' : ($tipo === 'email' ? 'email' : 'text')); ?>"
                                            id="<?php echo esc_attr($field_id); ?>"
                                            name="<?php echo esc_attr($field_name); ?>"
                                            class="amentum-campo-input"
                                            placeholder="<?php echo esc_attr($placeholder); ?>"
                                            <?php echo $requerido ? 'required' : ''; ?>
                                        >
                                    </div>
                                    <?php break;

                                case 'textarea': ?>
                                    <div class="input-wrapper">
                                        <textarea
                                            id="<?php echo esc_attr($field_id); ?>"
                                            name="<?php echo esc_attr($field_name); ?>"
                                            class="amentum-campo-textarea"
                                            placeholder="<?php echo esc_attr($placeholder); ?>"
                                            rows="4"
                                            <?php echo $requerido ? 'required' : ''; ?>
                                        ></textarea>
                                    </div>
                                    <?php break;

                                case 'select': ?>
                                    <div class="input-wrapper">
                                        <select
                                            id="<?php echo esc_attr($field_id); ?>"
                                            name="<?php echo esc_attr($field_name); ?>"
                                            class="amentum-campo-select"
                                            <?php echo $requerido ? 'required' : ''; ?>
                                        >
                                            <option value="">Selecciona una opción</option>
                                            <?php foreach ($opciones as $opcion):
                                                $opcion_valor = !empty($opcion['valor']) ? $opcion['valor'] : $opcion['nombre']; ?>
                                                <option value="<?php echo esc_attr($opcion_valor); ?>">
                                                    <?php echo esc_html($opcion['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php break;

                                case 'tags': ?>
                                    <div class="amentum-campo-tags">
                                        <?php foreach ($opciones as $index => $opcion):
                                            $opcion_valor = !empty($opcion['valor']) ? $opcion['valor'] : $opcion['nombre'];
                                            $checkbox_id = $field_id . '_' . $index; ?>
                                            <div class="amentum-tag-item">
                                                <input
                                                    type="checkbox"
                                                    id="<?php echo esc_attr($checkbox_id); ?>"
                                                    name="<?php echo esc_attr($field_name); ?>[]"
                                                    value="<?php echo esc_attr($opcion_valor); ?>"
                                                    class="amentum-campo-checkbox"
                                                    <?php echo $requerido ? 'data-required="required"' : ''; ?>
                                                >
                                                <label for="<?php echo esc_attr($checkbox_id); ?>" class="amentum-tag-label">
                                                    <?php echo esc_html($opcion['nombre']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php break;

                                case 'checkbox': ?>
                                    <div class="amentum-campo-checkbox-wrapper">
                                        <input
                                            type="checkbox"
                                            id="<?php echo esc_attr($field_id); ?>"
                                            name="<?php echo esc_attr($field_name); ?>"
                                            value="1"
                                            class="amentum-campo-checkbox"
                                            <?php echo $requerido ? 'required' : ''; ?>
                                        >
                                        <label for="<?php echo esc_attr($field_id); ?>" class="amentum-checkbox-label">
                                            <?php echo esc_html($nombre); ?>
                                            <?php if ($requerido): ?>
                                                <span class="requerido">*</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <?php break;
                            endswitch; ?>

                            <div class="amentum-campo-error" style="display: none;"></div>

                        </div>

                    <?php endif;
                endforeach;

                // Cerrar último fieldset si existe
                if ($current_fieldset !== null): ?>
                    </fieldset>
                <?php endif; ?>
            </div>

            <div class="amentum-formulario-submit">
                <button type="submit" class="amentum-boton-enviar">
                    <span class="amentum-boton-texto"><?php echo esc_html($boton_texto); ?></span>
                    <span class="amentum-boton-loading" style="display: none;">Enviando...</span>
                </button>
            </div>

            <div class="amentum-formulario-mensaje" style="display: none;"></div>

        </form>
    </div>

    <?php
    return ob_get_clean();
}

// Registrar el shortcode
add_shortcode('amentum_formulario', 'amentum_formulario_shortcode');

/**
 * Shortcode simple para listar formularios disponibles
 * Uso: [amentum_lista_formularios]
 * Uso: [amentum_lista_formularios mostrar="id,titulo"]
 */
function amentum_lista_formularios_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'mostrar' => 'titulo'
    ), $atts, 'amentum_lista_formularios');

    $formularios = get_posts(array(
        'post_type' => 'formularios',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));

    if (empty($formularios)) {
        return '<p>No hay formularios disponibles.</p>';
    }

    $output = '<div class="amentum-lista-formularios">';
    $output .= '<h3>Formularios Disponibles</h3>';
    $output .= '<ul>';

    foreach ($formularios as $formulario) {
        $mostrar_campos = explode(',', $atts['mostrar']);
        $info = array();

        foreach ($mostrar_campos as $campo) {
            $campo = trim($campo);
            switch ($campo) {
                case 'id':
                    $info[] = 'ID: ' . $formulario->ID;
                    break;
                case 'titulo':
                    $info[] = $formulario->post_title;
                    break;
            }
        }

        $output .= '<li>';
        $output .= implode(' - ', $info);
        $output .= ' <code>[amentum_formulario id="' . $formulario->ID . '"]</code>';
        $output .= '</li>';
    }

    $output .= '</ul>';
    $output .= '</div>';

    return $output;
}

// Registrar el shortcode de lista
add_shortcode('amentum_lista_formularios', 'amentum_lista_formularios_shortcode');