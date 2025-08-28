<?php
/**
 * Contacto Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Contacto
 */
function amentum_register_contacto_block() {
    register_block_type('amentum/contacto', array(
        'render_callback' => 'amentum_render_contacto_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => '¿Listo para comenzar tu proyecto?'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Cuéntanos sobre tu idea y hagámosla realidad'
            ),
            'email' => array(
                'type' => 'string',
                'default' => 'hello@amentum.com'
            ),
            'responseTime' => array(
                'type' => 'string',
                'default' => 'Respondemos en menos de 24 horas'
            )
        )
    ));
}
add_action('init', 'amentum_register_contacto_block');

/**
 * Renderizar el bloque Contacto
 */
function amentum_render_contacto_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : '¿Listo para comenzar tu proyecto?';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Cuéntanos sobre tu idea y hagámosla realidad';
    $email = !empty($attributes['email']) ? esc_attr($attributes['email']) : 'hello@amentum.com';
    $response_time = !empty($attributes['responseTime']) ? esc_html($attributes['responseTime']) : 'Respondemos en menos de 24 horas';

    ob_start();
    ?>
    <section class="block-contacto" id="contacto-block">
        <div class="container">
            <div class="block-header">
            <h2 class="block-title"><?php echo $title; ?></h2>
            <p class="block-subtitle"><?php echo $subtitle; ?></p>
        </div>
        
        <div class="contacto-content">
            <div class="contacto-form">
                <form id="amentum-contact-form" class="contact-form native-form" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact-name">Nombre *</label>
                            <input type="text" id="contact-name" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email *</label>
                            <input type="email" id="contact-email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-message">Cuéntanos sobre tu proyecto *</label>
                        <textarea id="contact-message" name="mensaje" rows="5" required placeholder="Describe tu idea, objetivos y cualquier detalle relevante..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-large">
                            Enviar Mensaje
                        </button>
                    </div>
                    
                    <input type="hidden" name="action" value="amentum_ajax_frm_contact">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('secret-key-form'); ?>">
                </form>
                
                <div id="form-response" class="form-response" style="display: none;"></div>
            </div>
            
            <div class="contacto-info">
                <div class="info-item">
                    <h3>Información de Contacto</h3>
                    <p>Estamos aquí para ayudarte con tu próximo proyecto.</p>
                </div>
                
                <div class="info-item">
                    <h4>Email</h4>
                    <p>
                        <a href="mailto:<?php echo $email; ?>">
                            <?php echo esc_html($email); ?>
                        </a>
                    </p>
                </div>
                
                <div class="info-item">
                    <h4>Tiempo de Respuesta</h4>
                    <p><?php echo $response_time; ?></p>
                </div>
            </div>
        </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue assets específicos para Contacto block (Frontend)
 */
function amentum_enqueue_contacto_block_assets() {
    // Solo cargar si el bloque está presente en la página
    if (has_block('amentum/contacto')) {
        wp_enqueue_style(
            'amentum-contacto-style',
            get_template_directory_uri() . '/blocks/contacto/style.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_contacto_block_assets');

/**
 * Enqueue editor assets para Contacto block (Backend)
 */
function amentum_enqueue_contacto_block_editor_assets() {
    // JS del editor
    wp_enqueue_script(
        'amentum-contacto-editor',
        get_template_directory_uri() . '/blocks/contacto/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // CSS del editor (para que se vea bien en el backend)
    wp_enqueue_style(
        'amentum-contacto-editor-style',
        get_template_directory_uri() . '/blocks/contacto/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_contacto_block_editor_assets');