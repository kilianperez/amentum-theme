<?php
/**
 * Servicios Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Servicios
 */
function amentum_register_servicios_block() {
    register_block_type('amentum/servicios', array(
        'render_callback' => 'amentum_render_servicios_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Nuestros Servicios'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Ofrecemos soluciones creativas integrales'
            )
        )
    ));
}
add_action('init', 'amentum_register_servicios_block');

/**
 * Renderizar el bloque Servicios
 */
function amentum_render_servicios_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Nuestros Servicios';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Ofrecemos soluciones creativas integrales';

    ob_start();
    ?>
    <section class="block-servicios" id="servicios-block">
        <div class="container">
            <div class="block-header">
            <h2 class="block-title"><?php echo $title; ?></h2>
            <p class="block-subtitle"><?php echo $subtitle; ?></p>
        </div>
        
        <div class="servicios-grid">
            <div class="servicio-card">
                <div class="servicio-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <h3 class="servicio-title">Diseño Gráfico</h3>
                <p class="servicio-description">
                    Creamos identidades visuales impactantes y memorables para tu marca.
                </p>
            </div>
            
            <div class="servicio-card">
                <div class="servicio-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2z"/>
                    </svg>
                </div>
                <h3 class="servicio-title">Branding</h3>
                <p class="servicio-description">
                    Desarrollamos estrategias de marca completas que conectan con tu audiencia.
                </p>
            </div>
            
            <div class="servicio-card">
                <div class="servicio-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-1 12H5V8h14v8z"/>
                    </svg>
                </div>
                <h3 class="servicio-title">Desarrollo Web</h3>
                <p class="servicio-description">
                    Construimos sitios web modernos, rápidos y optimizados para conversión.
                </p>
            </div>
            
            <div class="servicio-card">
                <div class="servicio-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-5H17V4.5C17 3.12 15.88 2 14.5 2h-5C8.12 2 7 3.12 7 4.5V6H4.5C4.22 6 4 6.22 4 6.5S4.22 7 4.5 7H6v10.5c0 1.38 1.12 2.5 2.5 2.5h7c1.38 0 2.5-1.12 2.5-2.5V7h1.5c.28 0 .5-.22.5-.5S19.78 6 19.5 6z"/>
                    </svg>
                </div>
                <h3 class="servicio-title">Fotografía</h3>
                <p class="servicio-description">
                    Capturamos la esencia de tu marca con fotografía profesional y creativa.
                </p>
            </div>
        </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}