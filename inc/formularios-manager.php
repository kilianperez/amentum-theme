<?php
/**
 * Sistema Nativo de Gestión de Formularios
 * Meta box personalizado para crear campos de formulario dinámicamente
 * 
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Agregar meta boxes para configuración de formularios
 */
function amentum_add_formularios_meta_boxes()
{
    // Meta box principal para campos del formulario
    add_meta_box(
        'amentum_formulario_campos',
        'Campos del Formulario',
        'amentum_formulario_campos_callback',
        'formularios',
        'normal',
        'high'
    );
    
    // Meta box lateral para configuración general
    add_meta_box(
        'amentum_formulario_config',
        'Configuración General',
        'amentum_formulario_config_callback',
        'formularios',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'amentum_add_formularios_meta_boxes');

/**
 * Remover metaboxes no deseados del post type formularios
 */
function amentum_remove_unwanted_metaboxes()
{
    // Remover el metabox de custom fields
    remove_meta_box('postcustom', 'formularios', 'normal');
    remove_meta_box('postcustom', 'formularios', 'side');
    remove_meta_box('postcustom', 'formularios', 'advanced');
    
    // Remover otros metaboxes que podrían interferir
    remove_meta_box('authordiv', 'formularios', 'normal');
    remove_meta_box('commentstatusdiv', 'formularios', 'normal');
    remove_meta_box('commentsdiv', 'formularios', 'normal');
    remove_meta_box('revisionsdiv', 'formularios', 'normal');
    remove_meta_box('slugdiv', 'formularios', 'normal');
    remove_meta_box('trackbacksdiv', 'formularios', 'normal');
}
add_action('add_meta_boxes', 'amentum_remove_unwanted_metaboxes', 999);

/**
 * Callback del meta box de configuración
 */
function amentum_formulario_config_callback($post)
{
    // Nonce para seguridad
    wp_nonce_field('amentum_formulario_meta_nonce', 'amentum_formulario_nonce');
    
    // Obtener datos existentes
    $config = get_post_meta($post->ID, '_amentum_formulario_config', true);
    $config = $config ?: array();
    
    // Valores por defecto
    $defaults = array(
        'descripcion' => '',
        'boton_texto' => 'Enviar',
        'email_destino' => get_option('admin_email'),
        'mensaje_exito' => 'Tu mensaje ha sido enviado correctamente. Te contactaremos pronto.',
        'campos' => array()
    );
    
    $config = wp_parse_args($config, $defaults);
    
    ?>
    <div id="amentum-formulario-config-sidebar">
        <div class="config-field">
            <label for="formulario_descripcion">Descripción</label>
            <textarea id="formulario_descripcion" name="formulario_config[descripcion]" 
                      rows="2" placeholder="Descripción opcional..."><?php echo esc_textarea($config['descripcion']); ?></textarea>
        </div>

        <div class="config-field">
            <label for="formulario_boton_texto">Texto del Botón</label>
            <input type="text" id="formulario_boton_texto" name="formulario_config[boton_texto]" 
                   value="<?php echo esc_attr($config['boton_texto']); ?>" placeholder="Enviar">
        </div>

        <div class="config-field">
            <label for="formulario_email_destino">Email Destino</label>
            <input type="email" id="formulario_email_destino" name="formulario_config[email_destino]" 
                   value="<?php echo esc_attr($config['email_destino']); ?>" placeholder="tu@email.com">
            <small>Donde se reciben los envíos</small>
        </div>

        <div class="config-field">
            <label for="formulario_mensaje_exito">Mensaje de Éxito</label>
            <textarea id="formulario_mensaje_exito" name="formulario_config[mensaje_exito]" 
                      rows="2" placeholder="¡Gracias! Te contactaremos pronto."><?php echo esc_textarea($config['mensaje_exito']); ?></textarea>
        </div>
    </div>

    <style>
        /* Estilos compactos para el sidebar de configuración */
        #amentum-formulario-config-sidebar {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .config-field {
            margin-bottom: 16px;
        }
        
        .config-field:last-child {
            margin-bottom: 0;
        }
        
        .config-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }
        
        .config-field input,
        .config-field textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            background: #fff;
            transition: border-color 0.2s ease;
        }
        
        .config-field input:focus,
        .config-field textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.1);
        }
        
        .config-field textarea {
            resize: vertical;
            min-height: 50px;
        }
        
        .config-field small {
            display: block;
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            font-style: italic;
        }
        
        .config-field input::placeholder,
        .config-field textarea::placeholder {
            color: #9ca3af;
            font-style: italic;
        }
        
        /* Estilos para el área principal de campos */
        #amentum-formulario-campos {
            margin: 20px 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .amentum-form-section-main {
            background: #fff;
        }
        
        .campo-editor {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 12px;
            position: relative;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        .campo-editor:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        
        .campo-editor.collapsed .campo-content {
            display: none;
        }
        
        .campo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 16px 20px;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }
        
        .campo-header:hover {
            background-color: #f8fafc;
        }
        
        .campo-header-left {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }
        
        .campo-drag-handle {
            cursor: grab;
            color: #94a3b8;
            margin-right: 12px;
            font-size: 16px;
            transition: color 0.2s ease;
        }
        
        .campo-drag-handle:hover {
            color: #475569;
        }
        
        .campo-drag-handle:active {
            cursor: grabbing;
        }
        
        .campo-info {
            flex: 1;
            min-width: 0;
        }
        
        .campo-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 15px;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .campo-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 4px;
        }
        
        .campo-type-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .campo-width-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #e0e7ff;
            color: #5b21b6;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .campo-required-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .campo-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .campo-toggle {
            color: #6b7280;
            font-size: 14px;
            transition: transform 0.2s ease;
        }
        
        .campo-editor.collapsed .campo-toggle {
            transform: rotate(-90deg);
        }
        
        .campo-action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .campo-action-btn:hover {
            background: #f3f4f6;
            color: #374151;
        }
        
        .campo-action-btn.delete:hover {
            background: #fef2f2;
            color: #dc2626;
        }
        
        .campo-content {
            padding: 0 20px 20px 20px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            border-top: 1px solid #f1f5f9;
        }
        
        .campo-content > div {
            display: flex;
            flex-direction: column;
        }
        
        .campo-content label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .campo-content select,
        .campo-content input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }
        
        .campo-content select:focus,
        .campo-content input[type="text"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .campo-full {
            grid-column: 1 / -1;
        }
        
        /* Ocultar TODOS los checkboxes dentro del contenido excepto nuestros toggles */
        .campo-content input[type="checkbox"] {
            display: none !important;
        }
        
        .campo-content label[for*="requerido"] {
            display: none !important;
        }
        
        /* Solo mostrar nuestros toggle switches */
        .toggle-input-compact {
            display: none !important; /* Este ya está oculto por diseño */
        }
        
        
        /* Toggle Switch Compact (para las acciones del header) */
        .toggle-switch-compact {
            display: flex;
            align-items: center;
        }
        
        .toggle-input-compact {
            display: none;
        }
        
        .toggle-label-compact {
            position: relative;
            display: inline-block;
            width: 32px;
            height: 16px;
            background-color: #e5e7eb;
            border-radius: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0;
        }
        
        .toggle-label-compact:hover {
            background-color: #d1d5db;
        }
        
        .toggle-slider-compact {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 12px;
            height: 12px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .toggle-input-compact:checked + .toggle-label-compact {
            background-color: #3b82f6;
        }
        
        .toggle-input-compact:checked + .toggle-label-compact:hover {
            background-color: #2563eb;
        }
        
        .toggle-input-compact:checked + .toggle-label-compact .toggle-slider-compact {
            transform: translateX(16px);
        }
        
        .toggle-input-compact:focus + .toggle-label-compact {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        
        .opciones-container {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            background: #f9fafb;
        }
        
        .opciones-list {
            margin-bottom: 12px;
        }
        
        .opcion-item {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            align-items: center;
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .opcion-item input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .opcion-item button {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            border: none;
            background: #f3f4f6;
            color: #dc2626;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .opcion-item button:hover {
            background: #fef2f2;
        }
        
        .add-campo-section {
            text-align: center;
            padding: 32px 0;
            border-top: 2px dashed #e5e7eb;
            margin-top: 24px;
        }
        
        #add-campo-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }
        
        #add-campo-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        #add-campo-btn:active {
            transform: translateY(0);
        }
        
        .sortable-ghost {
            opacity: 0.5;
            transform: scale(0.98);
        }
        
        .sortable-placeholder {
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .campo-content {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .campo-content {
                grid-template-columns: 1fr;
            }
            
            .campo-header {
                padding: 12px 16px;
            }
            
            .campo-content {
                padding: 0 16px 16px 16px;
            }
            
            .amentum-form-section {
                padding: 16px;
            }
        }
    </style>
    <?php
}

/**
 * Callback del meta box de campos del formulario
 */
function amentum_formulario_campos_callback($post)
{
    // Obtener datos existentes
    $config = get_post_meta($post->ID, '_amentum_formulario_config', true);
    $config = $config ?: array();
    
    // Valores por defecto para campos
    $campos = $config['campos'] ?? array();
    
    ?>
    <div id="amentum-formulario-campos">
        <div class="amentum-form-section-main">
            <div id="campos-container">
                <?php if (!empty($campos)): ?>
                    <?php foreach ($campos as $index => $campo): ?>
                        <?php amentum_render_campo_editor($index, $campo); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="add-campo-section">
                <button type="button" id="add-campo-btn" class="button button-secondary">
                    <span class="dashicons dashicons-plus-alt"></span> Añadir Campo
                </button>
            </div>
        </div>
    </div>

    <script type="text/template" id="campo-template">
        <?php amentum_render_campo_editor('{{INDEX}}', array()); ?>
    </script>
    <?php
}

/**
 * Renderizar editor de campo individual
 */
function amentum_render_campo_editor($index, $campo = array())
{
    $defaults = array(
        'tipo' => 'text',
        'nombre' => '',
        'ancho' => '12',
        'requerido' => false,
        'placeholder' => '',
        'opciones' => array()
    );
    
    $campo = wp_parse_args($campo, $defaults);
    $is_template = $index === '{{INDEX}}';
    $display_name = $campo['nombre'] ?: 'Nuevo Campo';
    ?>
    
    <div class="campo-editor" data-index="<?php echo esc_attr($index); ?>">
        <div class="campo-header" onclick="toggleCampo(this)">
            <div class="campo-header-left">
                <span class="campo-drag-handle dashicons dashicons-menu"></span>
                <div class="campo-info">
                    <div class="campo-title"><?php echo esc_html($display_name); ?></div>
                    <div class="campo-meta">
                        <span class="campo-type-badge"><?php echo esc_html($campo['tipo']); ?></span>
                        <?php
                        $width_labels = array(
                            '3' => '25%', '4' => '33%', '6' => '50%', 
                            '8' => '66%', '9' => '75%', '12' => '100%'
                        );
                        ?>
                        <span class="campo-width-badge"><?php echo esc_html($width_labels[$campo['ancho']] ?? $campo['ancho']); ?></span>
                        <?php if ($campo['requerido']): ?>
                            <span class="campo-required-badge">Obligatorio</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="campo-actions">
                <div class="toggle-switch-compact" <?php echo $campo['tipo'] === 'titulo_seccion' ? 'style="display:none;"' : ''; ?>>
                    <input type="checkbox" name="formulario_config[campos][<?php echo esc_attr($index); ?>][requerido]" 
                           value="1" <?php checked($campo['requerido']); ?>
                           onchange="updateCampoRequired(this)"
                           id="required_<?php echo esc_attr($index); ?>" class="toggle-input-compact">
                    <label for="required_<?php echo esc_attr($index); ?>" class="toggle-label-compact" title="Campo obligatorio">
                        <span class="toggle-slider-compact"></span>
                    </label>
                </div>
                <span class="campo-toggle dashicons dashicons-arrow-down"></span>
                <button type="button" class="campo-action-btn" onclick="event.stopPropagation(); duplicarCampo(this)" title="Duplicar campo">
                    <span class="dashicons dashicons-admin-page"></span>
                </button>
                <button type="button" class="campo-action-btn delete" onclick="event.stopPropagation(); eliminarCampo(this)" title="Eliminar campo">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
        
        <div class="campo-content">
            <div>
                <label><strong>Tipo de Campo</strong></label>
                <select name="formulario_config[campos][<?php echo esc_attr($index); ?>][tipo]" 
                        onchange="updateCampoType(this)" class="campo-tipo">
                    <option value="titulo_seccion" <?php selected($campo['tipo'], 'titulo_seccion'); ?>>Título de Sección</option>
                    <option value="text" <?php selected($campo['tipo'], 'text'); ?>>Texto</option>
                    <option value="email" <?php selected($campo['tipo'], 'email'); ?>>Email</option>
                    <option value="phone" <?php selected($campo['tipo'], 'phone'); ?>>Teléfono</option>
                    <option value="url" <?php selected($campo['tipo'], 'url'); ?>>URL</option>
                    <option value="textarea" <?php selected($campo['tipo'], 'textarea'); ?>>Área de Texto</option>
                    <option value="select" <?php selected($campo['tipo'], 'select'); ?>>Lista Desplegable</option>
                    <option value="tags" <?php selected($campo['tipo'], 'tags'); ?>>Tags/Checkboxes</option>
                    <option value="checkbox" <?php selected($campo['tipo'], 'checkbox'); ?>>Checkbox Individual</option>
                </select>
            </div>
            
            <div>
                <label><strong>Nombre del Campo</strong></label>
                <input type="text" name="formulario_config[campos][<?php echo esc_attr($index); ?>][nombre]" 
                       value="<?php echo esc_attr($campo['nombre']); ?>" 
                       placeholder="Ej: Nombre completo"
                       onchange="updateCampoTitle(this)" class="regular-text">
            </div>
            
            <div>
                <label><strong>Ancho</strong></label>
                <select name="formulario_config[campos][<?php echo esc_attr($index); ?>][ancho]"
                        onchange="updateCampoWidth(this)">
                    <option value="3" <?php selected($campo['ancho'], '3'); ?>>1/4 (25%)</option>
                    <option value="4" <?php selected($campo['ancho'], '4'); ?>>1/3 (33%)</option>
                    <option value="6" <?php selected($campo['ancho'], '6'); ?>>1/2 (50%)</option>
                    <option value="8" <?php selected($campo['ancho'], '8'); ?>>2/3 (66%)</option>
                    <option value="9" <?php selected($campo['ancho'], '9'); ?>>3/4 (75%)</option>
                    <option value="12" <?php selected($campo['ancho'], '12'); ?>>Completo (100%)</option>
                </select>
            </div>
            
            
            <div class="campo-placeholder-container campo-full"
                 <?php echo in_array($campo['tipo'], ['text', 'email', 'phone', 'url', 'textarea', 'select']) ? '' : 'style="display:none;"'; ?>>
                <label><strong>Placeholder</strong></label>
                <input type="text" name="formulario_config[campos][<?php echo esc_attr($index); ?>][placeholder]" 
                       value="<?php echo esc_attr($campo['placeholder']); ?>" 
                       placeholder="Texto de ayuda dentro del campo"
                       class="large-text">
            </div>
            
            <div class="campo-opciones-container campo-full" 
                 <?php echo in_array($campo['tipo'], ['select', 'tags']) ? '' : 'style="display:none;"'; ?>>
                <label><strong>Opciones</strong></label>
                <div class="opciones-container">
                    <div class="opciones-list" data-index="<?php echo esc_attr($index); ?>">
                        <?php if (!empty($campo['opciones'])): ?>
                            <?php foreach ($campo['opciones'] as $opt_index => $opcion): ?>
                                <div class="opcion-item">
                                    <input type="text" 
                                           name="formulario_config[campos][<?php echo esc_attr($index); ?>][opciones][<?php echo esc_attr($opt_index); ?>][nombre]" 
                                           value="<?php echo esc_attr($opcion['nombre'] ?? ''); ?>" 
                                           placeholder="Nombre de la opción" class="regular-text">
                                    <input type="text" 
                                           name="formulario_config[campos][<?php echo esc_attr($index); ?>][opciones][<?php echo esc_attr($opt_index); ?>][valor]" 
                                           value="<?php echo esc_attr($opcion['valor'] ?? ''); ?>" 
                                           placeholder="Valor (opcional)" class="regular-text">
                                    <button type="button" onclick="eliminarOpcion(this)" class="button-link" style="color: #d63638;">
                                        <span class="dashicons dashicons-minus"></span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" onclick="añadirOpcion(this)" class="button button-small">
                        <span class="dashicons dashicons-plus-alt"></span> Añadir Opción
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Guardar configuración del formulario
 */
function amentum_save_formulario_meta($post_id)
{
    // Verificar nonce
    if (!isset($_POST['amentum_formulario_nonce']) || 
        !wp_verify_nonce($_POST['amentum_formulario_nonce'], 'amentum_formulario_meta_nonce')) {
        return;
    }

    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Verificar que es el post type correcto
    if (get_post_type($post_id) !== 'formularios') {
        return;
    }

    // Guardar configuración
    if (isset($_POST['formulario_config'])) {
        $config = $_POST['formulario_config'];
        
        // Sanitizar datos
        $config['descripcion'] = sanitize_textarea_field($config['descripcion']);
        $config['boton_texto'] = sanitize_text_field($config['boton_texto']);
        $config['email_destino'] = sanitize_email($config['email_destino']);
        $config['mensaje_exito'] = sanitize_textarea_field($config['mensaje_exito']);
        
        // Sanitizar campos
        if (isset($config['campos']) && is_array($config['campos'])) {
            foreach ($config['campos'] as $index => $campo) {
                $config['campos'][$index]['tipo'] = sanitize_text_field($campo['tipo']);
                $config['campos'][$index]['nombre'] = sanitize_text_field($campo['nombre']);
                $config['campos'][$index]['ancho'] = sanitize_text_field($campo['ancho']);
                $config['campos'][$index]['requerido'] = isset($campo['requerido']) ? true : false;
                $config['campos'][$index]['placeholder'] = sanitize_text_field($campo['placeholder'] ?? '');
                
                // Sanitizar opciones
                if (isset($campo['opciones']) && is_array($campo['opciones'])) {
                    foreach ($campo['opciones'] as $opt_index => $opcion) {
                        $config['campos'][$index]['opciones'][$opt_index]['nombre'] = sanitize_text_field($opcion['nombre'] ?? '');
                        $config['campos'][$index]['opciones'][$opt_index]['valor'] = sanitize_text_field($opcion['valor'] ?? '');
                    }
                } else {
                    $config['campos'][$index]['opciones'] = array();
                }
            }
        }
        
        update_post_meta($post_id, '_amentum_formulario_config', $config);
    }
}
add_action('save_post', 'amentum_save_formulario_meta');

/**
 * Enqueue scripts para el editor de formularios
 */
function amentum_enqueue_formulario_admin_scripts($hook)
{
    global $post_type;
    
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        if ($post_type === 'formularios') {
            wp_enqueue_script(
                'amentum-formularios-admin',
                get_template_directory_uri() . '/assets/js/admin/formularios-manager.js',
                array('jquery', 'jquery-ui-sortable'),
                wp_get_theme()->get('Version'),
                true
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'amentum_enqueue_formulario_admin_scripts');