<?php
/**
 * Funciones para formularios AJAX y procesamiento de datos
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue javascript file y configurar variables AJAX
 */
add_action('wp_enqueue_scripts', 'amentum_insert_custom_js');
function amentum_insert_custom_js()
{
    wp_localize_script(
        'all',
        'ajax_forms',
        [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'frmNonce' => wp_create_nonce('secret-key-form'),
            'isUserLoggedIn' => is_user_logged_in() ? 'true' : 'false'
        ]
    );

    // Enqueue y configurar script para formularios nativos
    wp_enqueue_script(
        'amentum-formularios-handler',
        get_template_directory_uri() . '/assets/js/formularios-handler.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );

    wp_localize_script(
        'amentum-formularios-handler',
        'amentum_ajax',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amentum_form_ajax_nonce'),
        ]
    );
}

/**
 * Crear el formulario de contacto AJAX
 */
add_filter('the_content', 'amentum_show_contact_ajax_form');
function amentum_show_contact_ajax_form($content)
{
    ob_start();
    ?>

    <?php
    $htm_form = ob_get_contents();
    ob_end_clean();

    return $content . $htm_form;
}

/**
 * Procesar solicitudes AJAX del formulario de contacto
 */
add_action('wp_ajax_nopriv_amentum_ajax_frm_contact', 'amentum_process_contact_form');
add_action('wp_ajax_amentum_ajax_frm_contact', 'amentum_process_contact_form');
function amentum_process_contact_form()
{
    /**
     * Sanitizar campos según su tipo
     */
    function sanitize_field($value, $type)
    {
        switch ($type) {
            case 'date':
            case 'text':
            case 'select':
            case 'tags':
                return sanitize_text_field($value);
            case 'titulo_seccion':
                return ''; // Los títulos de sección no se envían
            case 'email':
                return sanitize_email($value);
            case 'textarea':
                return sanitize_textarea_field($value);
            case 'phone':
            case 'url':
                return esc_url($value);
            case 'checkbox':
                return in_array($value, ['on', 'true', '1']) ? 'Sí' : 'No';
            default:
                return sanitize_text_field($value);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
        amentum_validate_nonce($nonce, 'secret-key-form');

        $sanitize_data = [];
        $sanitize_email = '';
        $sanitize_name = '';

        foreach ($_POST as $key => $field) {
            // Manejar campos dinámicos con estructura de Liberta
            if (isset($field['type']) && isset($field['value'])) {
                $value = $field['value'];
                $type = $field['type'];
                $placeholder = isset($field['placeholder']) ? $field['placeholder'] : $key;
                $option = isset($field['option']) ? $field['option'] : $type;

                // Identificar email y nombre para headers
                if ($type == 'email') {
                    $sanitize_email = $value;
                }
                if (strpos($key, 'nombre') !== false || $option == 'name') {
                    $sanitize_name = $value;
                }

                // Sanitizar y almacenar
                $sanitized_value = sanitize_field($value, $type);
                $sanitize_data[$key] = [
                    'value' => $sanitized_value,
                    'type' => $type,
                    'placeholder' => $placeholder,
                    'option' => $option
                ];
            }
            // Manejar campos simples (compatibilidad hacia atrás)
            elseif (is_string($field) && !in_array($key, ['nonce', 'action'])) {
                $sanitized_value = sanitize_text_field($field);
                
                // Detectar tipo por nombre del campo
                $type = 'text';
                if (strpos($key, 'email') !== false) {
                    $type = 'email';
                    $sanitized_value = sanitize_email($field);
                    $sanitize_email = $sanitized_value;
                }
                if (strpos($key, 'mensaje') !== false) {
                    $type = 'textarea';
                    $sanitized_value = sanitize_textarea_field($field);
                }
                if (strpos($key, 'nombre') !== false) {
                    $sanitize_name = $sanitized_value;
                }

                $sanitize_data[$key] = [
                    'value' => $sanitized_value,
                    'type' => $type,
                    'placeholder' => ucfirst($key),
                    'option' => $type
                ];
            }
            // Manejar arrays (para campos tags)
            elseif (is_array($field)) {
                $sanitized_values = array_map('sanitize_text_field', $field);
                $sanitize_data[$key] = [
                    'value' => implode(', ', $sanitized_values),
                    'type' => 'tags',
                    'placeholder' => ucfirst($key),
                    'option' => 'tags'
                ];
            }
        }

        // Usar email configurado en customizer o fallback
        $admin_email = get_theme_mod('amentum_email') ?: 'hello@amentum.com';
        $subject = 'Formulario de contacto - Amentum';
        $headers = "Reply-to: " . $sanitize_name . " <" . $sanitize_email . ">";

        // Construir mensaje
        $msg = '';
        foreach ($sanitize_data as $field) {
            if (!empty($field['value'])) {
                $msg .= $field['placeholder'] . ": " . $field['value'] . "\n";
            }
        }

        // Enviar email
        $sent = wp_mail($admin_email, $subject, $msg, $headers);

        // Procesar newsletter si está habilitada
        if (isset($_POST['newsletter']) && ($_POST['newsletter'] === 'on' || $_POST['newsletter'] === '1')) {
            amentum_mailchimp();
        }

        // Respuesta más detallada
        if ($sent) {
            $res = ['status' => 1, 'message' => 'Se envió correctamente el formulario'];
        } else {
            // Obtener más información sobre el error
            $error_info = '';
            if (function_exists('error_get_last')) {
                $last_error = error_get_last();
                if ($last_error) {
                    $error_info = ' | Last PHP Error: ' . $last_error['message'];
                }
            }
            
            error_log('AMENTUM: wp_mail FAILED - checking mail configuration');
            $res = ['status' => 0, 'message' => 'Hubo un error en el envío (wp_mail)' . $error_info];
        }

        wp_send_json($res);
    }
}

/**
 * Validar nonce para seguridad de formularios
 */
function amentum_validate_nonce($nonce, $nonce_name)
{
    if (!wp_verify_nonce($nonce, $nonce_name)) {
        $res = ['status' => 0, 'message' => '✋ Error nonce validation'];
        wp_send_json($res);
    }
}

/**
 * Integración con MailChimp para suscripciones
 * 
 * @link https://angelcruz.dev/post/como-usar-de-forma-sencilla-mailchimp-en-wordpress
 * @link https://rudrastyh.com/wordpress/using-mailchimp-api.html
 */
function amentum_mailchimp()
{
    if (!empty($_POST)) {
        $email = $_POST['email'];
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
        $api = 'eb664283d682fbb4b1c3c1584258a2c9-us13';
        $list_id = 'e5895892ea';
        $subscription_page = $_POST['pagina'];

        // URL para suscribir al usuario
        $subscribe_url = 'https://' . substr($api, strpos($api, '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($email));

        // Datos del campo personalizado
        $merge_fields = array(
            'PAGINA' => $subscription_page,
            'FNAME' => $nombre,
            'TIPO' => $tipo
        );

        // Datos de la suscripción
        $subscription_data = array(
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => $merge_fields
        );

        // Realizar la solicitud para suscribir al usuario
        $response = wp_remote_request(
            $subscribe_url,
            array(
                'method' => 'PUT',
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode('user:' . $api)
                ),
                'body' => json_encode($subscription_data)
            )
        );

        // if('OK' === wp_remote_retrieve_response_message($response)) {
        //     echo 'The user has been successfully subscribed.';
        // }
    }
}

/**
 * ===============================================
 * SISTEMA DE FORMULARIOS NATIVOS AMENTUM
 * ===============================================
 */

/**
 * Procesar formularios nativos de Amentum via AJAX
 */
add_action('wp_ajax_amentum_process_form', 'amentum_process_native_form');
add_action('wp_ajax_nopriv_amentum_process_form', 'amentum_process_native_form');
function amentum_process_native_form()
{
    // Verificar nonce
    $formulario_id = absint(isset($_POST['formulario_id']) ? $_POST['formulario_id'] : 0);
    if (!wp_verify_nonce(isset($_POST['amentum_form_nonce']) ? $_POST['amentum_form_nonce'] : '', 'amentum_form_nonce_' . $formulario_id)) {
        wp_send_json_error(['mensaje' => 'Error de seguridad. Por favor recarga la página.']);
    }

    if (!$formulario_id) {
        wp_send_json_error(['mensaje' => 'ID de formulario no válido.']);
    }

    // Obtener configuración del formulario
    $config = get_post_meta($formulario_id, '_amentum_formulario_config', true);
    if (!$config || !isset($config['campos'])) {
        wp_send_json_error(['mensaje' => 'Configuración de formulario no encontrada.']);
    }

    $campos = $config['campos'];
    $titulo_formulario = get_the_title($formulario_id);
    $mensaje_exito = isset($config['mensaje_exito']) ? $config['mensaje_exito'] : 'Tu mensaje ha sido enviado correctamente.';

    // Validar y sanitizar datos del formulario
    $datos_formulario = [];
    $errores = [];

    foreach ($campos as $campo) {
        $tipo = isset($campo['tipo']) ? $campo['tipo'] : 'text';
        $nombre = isset($campo['nombre']) ? $campo['nombre'] : '';
        $requerido = isset($campo['requerido']) ? $campo['requerido'] : false;
        
        // Generar nombre del campo para comparar
        $field_name = 'campo_' . sanitize_title($nombre);
        $valor = isset($_POST[$field_name]) ? $_POST[$field_name] : '';

        // Validar campos requeridos
        if ($requerido && empty($valor) && $tipo !== 'titulo_seccion') {
            $errores[] = sprintf('El campo "%s" es obligatorio.', $nombre);
            continue;
        }

        // Saltear títulos de sección
        if ($tipo === 'titulo_seccion') {
            continue;
        }

        // Sanitizar según el tipo de campo
        $valor_sanitizado = amentum_sanitize_form_field($valor, $tipo);
        
        if ($valor_sanitizado !== '' && $valor_sanitizado !== null) {
            $datos_formulario[$nombre] = [
                'valor' => $valor_sanitizado,
                'tipo' => $tipo
            ];
        }
    }

    // Si hay errores de validación
    if (!empty($errores)) {
        wp_send_json_error(['mensaje' => implode(' ', $errores)]);
    }

    // Información adicional del envío
    $pagina_origen = sanitize_url(isset($_POST['pagina_origen']) ? $_POST['pagina_origen'] : '');
    $fecha_envio = current_time('mysql');
    $ip_usuario = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    // Construir mensaje de email
    $contenido_email = "Nuevo mensaje desde: {$titulo_formulario}\n";
    $contenido_email .= "Fecha: {$fecha_envio}\n";
    $contenido_email .= "Página: {$pagina_origen}\n";
    $contenido_email .= "IP: {$ip_usuario}\n\n";
    $contenido_email .= str_repeat('-', 50) . "\n\n";

    $email_remitente = '';
    $nombre_remitente = '';

    foreach ($datos_formulario as $label => $datos) {
        $contenido_email .= "{$label}: {$datos['valor']}\n";
        
        // Capturar email y nombre para headers
        if ($datos['tipo'] === 'email') {
            $email_remitente = $datos['valor'];
        }
        if (stripos($label, 'nombre') !== false && empty($nombre_remitente)) {
            $nombre_remitente = $datos['valor'];
        }
    }

    // Configurar envío de email
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $subject = sprintf('[%s] %s', $site_name, $titulo_formulario);

    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if ($email_remitente && $nombre_remitente) {
        $headers[] = "Reply-To: {$nombre_remitente} <{$email_remitente}>";
    } elseif ($email_remitente) {
        $headers[] = "Reply-To: {$email_remitente}";
    }

    // Enviar email
    $email_enviado = wp_mail($admin_email, $subject, $contenido_email, $headers);

    // Guardar en base de datos (opcional para futuras funcionalidades)
    amentum_save_form_submission($formulario_id, $datos_formulario, $pagina_origen, $ip_usuario);

    // Respuesta
    if ($email_enviado) {
        wp_send_json_success(['mensaje' => $mensaje_exito]);
    } else {
        error_log('AMENTUM: Error al enviar email del formulario ID ' . $formulario_id);
        wp_send_json_error(['mensaje' => 'Hubo un problema al enviar tu mensaje. Por favor intenta de nuevo.']);
    }
}

/**
 * Sanitizar campos de formulario según su tipo
 */
function amentum_sanitize_form_field($value, $type)
{
    if (is_array($value)) {
        // Para campos de tags (múltiples valores)
        return implode(', ', array_map('sanitize_text_field', $value));
    }

    switch ($type) {
        case 'email':
            return sanitize_email($value);
        case 'textarea':
            return sanitize_textarea_field($value);
        case 'url':
            return esc_url_raw($value);
        case 'phone':
            return preg_replace('/[^0-9+\-\(\)\s]/', '', $value);
        case 'checkbox':
            return $value ? 'Sí' : 'No';
        case 'text':
        case 'select':
        case 'tags':
        default:
            return sanitize_text_field($value);
    }
}

/**
 * Guardar envío de formulario en la base de datos
 */
function amentum_save_form_submission($formulario_id, $datos, $pagina_origen, $ip_usuario)
{
    // Crear post tipo para almacenar envíos (para futuras funcionalidades como dashboard de envíos)
    $submission_data = [
        'post_type' => 'form_submission',
        'post_status' => 'private',
        'post_title' => sprintf('Envío formulario %d - %s', $formulario_id, current_time('d/m/Y H:i')),
        'meta_input' => [
            '_formulario_id' => $formulario_id,
            '_datos_formulario' => $datos,
            '_pagina_origen' => $pagina_origen,
            '_ip_usuario' => $ip_usuario,
            '_fecha_envio' => current_time('mysql')
        ]
    ];

    // Solo crear el post si el tipo existe (opcional, no es crítico)
    if (post_type_exists('form_submission')) {
        wp_insert_post($submission_data);
    }
}