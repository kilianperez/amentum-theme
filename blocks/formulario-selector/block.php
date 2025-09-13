<?php
/**
 * Bloque Selector de Formulario - Sistema Nativo sin ACF
 * 
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque selector de formulario nativo
 */
function amentum_register_formulario_selector_block()
{
    // Registrar usando block.json (método moderno de WordPress 5.5+)
    $block_registered = register_block_type(
        __DIR__ . '/block.json',
        array(
            'render_callback' => 'amentum_render_formulario_selector_block',
        )
    );
}
add_action('init', 'amentum_register_formulario_selector_block');

/**
 * Callback para renderizar el bloque
 */
function amentum_render_formulario_selector_block($attributes, $content)
{
    // Capturar cualquier error fatal para debugging
    try {
    
    $formulario_id = isset($attributes['formularioId']) ? $attributes['formularioId'] : 0;
    $mostrar_titulo = isset($attributes['mostrarTitulo']) ? $attributes['mostrarTitulo'] : true;
    $mostrar_descripcion = isset($attributes['mostrarDescripcion']) ? $attributes['mostrarDescripcion'] : true;
    $clase_personalizada = isset($attributes['clasePersonalizada']) ? $attributes['clasePersonalizada'] : '';
    
    if (!$formulario_id) {
        // En el editor siempre mostrar placeholder
        return '<div style="padding: 2rem; border: 2px dashed #ccc; text-align: center; color: #666; border-radius: 4px;">
            <p style="margin: 0 0 0.5rem 0;"><strong>Selector de Formulario</strong></p>
            <p style="margin: 0; font-size: 14px;">Selecciona un formulario para mostrar aquí.</p>
        </div>';
    }
    
    // Obtener el formulario
    $formulario = get_post($formulario_id);
    if (!$formulario || $formulario->post_type !== 'formularios') {
        return '<div style="padding: 2rem; border: 2px solid #dc3545; background: #f8d7da; text-align: center; color: #721c24; border-radius: 4px;">
            <p style="margin: 0 0 0.5rem 0;"><strong>Error</strong></p>
            <p style="margin: 0; font-size: 14px;">Formulario no encontrado (ID: ' . $formulario_id . ')</p>
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
        return '<div style="padding: 2rem; border: 2px solid #f0ad4e; background: #fcf8e3; text-align: center; border-radius: 4px;">
            <p style="margin: 0 0 0.5rem 0;"><strong>Formulario: ' . esc_html($titulo_formulario) . '</strong></p>
            <p style="margin: 0; font-size: 14px;">Este formulario no tiene campos configurados.</p>
        </div>';
    }
    
    // Generar ID único para el formulario
    $form_id = 'amentum-form-' . $formulario_id . '-' . uniqid();
    
    // Clases CSS
    $css_classes = 'amentum-formulario-container';
    if ($clase_personalizada) {
        $css_classes .= ' ' . sanitize_html_class($clase_personalizada);
    }

    // Iniciar buffer de salida DENTRO del try para manejo seguro
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
                                    <input 
                                        type="<?php echo $tipo === 'phone' ? 'tel' : ($tipo === 'url' ? 'url' : ($tipo === 'email' ? 'email' : 'text')); ?>"
                                        id="<?php echo esc_attr($field_id); ?>"
                                        name="<?php echo esc_attr($field_name); ?>"
                                        class="amentum-campo-input"
                                        placeholder="<?php echo esc_attr($placeholder); ?>"
                                        <?php echo $requerido ? 'required' : ''; ?>
                                    >
                                    <?php break;
                                
                                case 'textarea': ?>
                                    <textarea 
                                        id="<?php echo esc_attr($field_id); ?>"
                                        name="<?php echo esc_attr($field_name); ?>"
                                        class="amentum-campo-textarea"
                                        placeholder="<?php echo esc_attr($placeholder); ?>"
                                        rows="4"
                                        <?php echo $requerido ? 'required' : ''; ?>
                                    ></textarea>
                                    <?php break;
                                
                                case 'select': ?>
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

    } catch (Exception $e) {
        // Limpiar buffer de salida en caso de error
        if (ob_get_level()) {
            ob_end_clean();
        }

        // En caso de error, devolver mensaje de error amigable
        error_log('FORMULARIO BLOCK ERROR: ' . $e->getMessage());
        return '<div style="padding: 2rem; border: 2px solid #dc3545; background: #f8d7da; text-align: center; color: #721c24; border-radius: 4px;">
            <p style="margin: 0 0 0.5rem 0;"><strong>Error en el Bloque</strong></p>
            <p style="margin: 0; font-size: 14px;">No se pudo renderizar el formulario. Revisa los logs para más detalles.</p>
        </div>';
    } catch (Error $e) {
        // Limpiar buffer de salida en caso de error fatal
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Capturar errores fatales (PHP 7+)
        error_log('FORMULARIO BLOCK FATAL ERROR: ' . $e->getMessage());
        return '<div style="padding: 2rem; border: 2px solid #dc3545; background: #f8d7da; text-align: center; color: #721c24; border-radius: 4px;">
            <p style="margin: 0 0 0.5rem 0;"><strong>Error Fatal</strong></p>
            <p style="margin: 0; font-size: 14px;">Error interno del bloque. Contacta al administrador.</p>
        </div>';
    }
}

/**
 * Enqueue script del editor para el bloque
 */
function amentum_enqueue_formulario_selector_editor_assets()
{
    wp_enqueue_script(
        'amentum-formulario-selector-editor',
        get_template_directory_uri() . '/blocks/formulario-selector/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'),
        wp_get_theme()->get('Version'),
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
        $formularios_options[] = array(
            'value' => $id,
            'label' => get_the_title($id)
        );
    }
    
    wp_localize_script('amentum-formulario-selector-editor', 'amentumFormularios', array(
        'formularios' => $formularios_options
    ));
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_formulario_selector_editor_assets');