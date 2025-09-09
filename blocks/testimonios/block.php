<?php
/**
 * Testimonios Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Testimonios
 */
function amentum_register_testimonios_block() {
    register_block_type('amentum/testimonios', array(
        'render_callback' => 'amentum_render_testimonios_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Lo que Dicen Nuestros Clientes'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Testimonios reales de clientes satisfechos'
            )
        )
    ));
}
add_action('init', 'amentum_register_testimonios_block');

/**
 * Renderizar el bloque Testimonios
 */
function amentum_render_testimonios_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Lo que Dicen Nuestros Clientes';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Testimonios reales de clientes satisfechos';

    $default_testimonials = array(
        array(
            'text' => 'El trabajo realizado por Amentum superó nuestras expectativas. Su enfoque profesional y creatividad nos ayudó a destacar en el mercado.',
            'author' => 'María García',
            'position' => 'CEO, TechStart'
        ),
        array(
            'text' => 'Profesionalismo y calidad en cada detalle. Recomiendo totalmente sus servicios para cualquier proyecto de branding.',
            'author' => 'Carlos Rodríguez',
            'position' => 'Director Creativo, DesignLab'
        ),
        array(
            'text' => 'La comunicación fue excelente durante todo el proceso. El resultado final fue exactamente lo que necesitábamos.',
            'author' => 'Ana López',
            'position' => 'Fundadora, EcoVerde'
        )
    );

    ob_start();
    ?>
    <section class="block-testimonios" id="testimonios-block">
        <div class="container">
            <div class="block-header">
            <h2 class="block-title"><?php echo $title; ?></h2>
            <p class="block-subtitle"><?php echo $subtitle; ?></p>
        </div>
        
        <div class="testimonios-grid">
            <?php foreach ($default_testimonials as $testimonial): ?>
            <div class="testimonio-card">
                <div class="testimonio-content">
                    <p class="testimonio-text">
                        "<?php echo esc_html($testimonial['text']); ?>"
                    </p>
                </div>
                <div class="testimonio-autor">
                    <div class="autor-info">
                        <h4 class="autor-name"><?php echo esc_html($testimonial['author']); ?></h4>
                        <p class="autor-cargo"><?php echo esc_html($testimonial['position']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}