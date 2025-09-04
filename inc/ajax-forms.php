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
        $nonce = $_POST['nonce'] ?? '';
        amentum_validate_nonce($nonce, 'secret-key-form');

        $sanitize_data = [];
        $sanitize_email = '';
        $sanitize_name = '';

        foreach ($_POST as $key => $field) {
            // Manejar campos dinámicos con estructura de Liberta
            if (isset($field['type']) && isset($field['value'])) {
                $value = $field['value'];
                $type = $field['type'];
                $placeholder = $field['placeholder'] ?? $key;
                $option = $field['option'] ?? $type;

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
        $nombre = $_POST['nombre'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
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