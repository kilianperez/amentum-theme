<?php
/**
 * Template del Bloque Selector de Formulario
 * 
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener datos del bloque
$formulario_seleccionado = get_field('formulario_seleccionado');
$mostrar_titulo = get_field('mostrar_titulo_formulario');
$mostrar_descripcion = get_field('mostrar_descripcion_formulario');
$clase_css_personalizada = get_field('clase_css_personalizada');

// Si no hay formulario seleccionado, mostrar mensaje en el editor
if (!$formulario_seleccionado) {
    if (is_admin()) {
        echo '<div style="padding: 2rem; border: 2px dashed #ccc; text-align: center; color: #666;">';
        echo '<p><strong>Selector de Formulario</strong></p>';
        echo '<p>Selecciona un formulario para mostrar aquí.</p>';
        echo '</div>';
    }
    return;
}

// Obtener datos del formulario desde el sistema nativo
$formulario_id = $formulario_seleccionado->ID;
$config = get_post_meta($formulario_id, '_amentum_formulario_config', true);
$config = $config ?: array();

$titulo_formulario = get_the_title($formulario_id);
$descripcion_formulario = $config['descripcion'] ?? '';
$campos_formulario = $config['campos'] ?? array();
$boton_texto = $config['boton_texto'] ?? 'Enviar';
$mensaje_exito = $config['mensaje_exito'] ?? 'Tu mensaje ha sido enviado correctamente.';

// Generar ID único para el formulario
$form_id = 'amentum-form-' . $formulario_id . '-' . uniqid();

// Clases CSS
$css_classes = 'amentum-formulario-container';
if ($clase_css_personalizada) {
    $css_classes .= ' ' . sanitize_html_class($clase_css_personalizada);
}

// Si no hay campos, mostrar mensaje
if (!$campos_formulario || empty($campos_formulario)) {
    if (is_admin()) {
        echo '<div style="padding: 2rem; border: 2px solid #f0ad4e; background: #fcf8e3; text-align: center;">';
        echo '<p><strong>Formulario seleccionado: ' . esc_html($formulario_seleccionado->post_title) . '</strong></p>';
        echo '<p>Este formulario no tiene campos configurados.</p>';
        echo '</div>';
    }
    return;
}

?>

<div class="<?php echo esc_attr($css_classes); ?>" id="<?php echo esc_attr($form_id); ?>-container">
    
    <?php if ($mostrar_titulo && $titulo_formulario): ?>
        <h2 class="amentum-formulario-titulo"><?php echo esc_html($titulo_formulario); ?></h2>
    <?php endif; ?>
    
    <?php if ($mostrar_descripcion && $descripcion_formulario): ?>
        <div class="amentum-formulario-descripcion">
            <?php echo wp_kses_post($descripcion_formulario); ?>
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
                $tipo = $campo['campo_tipo'];
                $nombre = $campo['campo_nombre'];
                $ancho = $campo['campo_ancho'];
                $requerido = $campo['campo_requerido'];
                $placeholder = $campo['campo_placeholder'] ?? '';
                $opciones = $campo['campo_opciones'] ?? array();
                
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
                                        $opcion_valor = !empty($opcion['opcion_valor']) ? $opcion['opcion_valor'] : $opcion['opcion_nombre']; ?>
                                        <option value="<?php echo esc_attr($opcion_valor); ?>">
                                            <?php echo esc_html($opcion['opcion_nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break;
                            
                            case 'tags': ?>
                                <div class="amentum-campo-tags">
                                    <?php foreach ($opciones as $index => $opcion): 
                                        $opcion_valor = !empty($opcion['opcion_valor']) ? $opcion['opcion_valor'] : $opcion['opcion_nombre'];
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
                                                <?php echo esc_html($opcion['opcion_nombre']); ?>
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

<style>
.amentum-formulario-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.amentum-formulario-titulo {
    text-align: center;
    margin-bottom: 1rem;
    font-size: 2rem;
    color: #333;
}

.amentum-formulario-descripcion {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
}

.amentum-formulario-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.amentum-formulario-fieldset {
    border: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
}

.amentum-formulario-legend {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: #333;
}

.amentum-campo-wrapper {
    display: flex;
    flex-direction: column;
}

.col-3 { grid-column: span 3; }
.col-4 { grid-column: span 4; }
.col-6 { grid-column: span 6; }
.col-8 { grid-column: span 8; }
.col-9 { grid-column: span 9; }
.col-12 { grid-column: span 12; }

@media (max-width: 768px) {
    .amentum-formulario-grid [class*="col-"] {
        grid-column: span 12 !important;
    }
}

.amentum-campo-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.requerido {
    color: #e74c3c;
}

.amentum-campo-input,
.amentum-campo-textarea,
.amentum-campo-select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.amentum-campo-input:focus,
.amentum-campo-textarea:focus,
.amentum-campo-select:focus {
    border-color: #6B46C1;
    outline: none;
}

.amentum-campo-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.amentum-tag-item .amentum-campo-checkbox {
    display: none;
}

.amentum-tag-label {
    padding: 0.5rem 1rem;
    border: 2px solid #ddd;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.amentum-campo-checkbox:checked + .amentum-tag-label {
    background-color: #6B46C1;
    color: white;
    border-color: #6B46C1;
}

.amentum-formulario-submit {
    text-align: center;
}

.amentum-boton-enviar {
    background-color: #6B46C1;
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 4px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.amentum-boton-enviar:hover {
    background-color: #553C9A;
}

.amentum-boton-enviar:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.amentum-formulario-mensaje {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 4px;
    text-align: center;
    font-weight: 600;
}

.amentum-formulario-mensaje.exito {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.amentum-formulario-mensaje.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.amentum-campo-error {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.campo-error {
    border-color: #e74c3c !important;
}
</style>