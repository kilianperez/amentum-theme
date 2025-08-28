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

/**
 * Enqueue assets específicos para Testimonios block (Frontend)
 */
function amentum_enqueue_testimonios_block_assets() {
    // Solo cargar si el bloque está presente en la página
    if (has_block('amentum/testimonios')) {
        wp_enqueue_style(
            'amentum-testimonios-style',
            get_template_directory_uri() . '/blocks/testimonios/style.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_testimonios_block_assets');

/**
 * Enqueue editor assets para Testimonios block (Backend)
 */
function amentum_enqueue_testimonios_block_editor_assets() {
    // JS del editor
    wp_enqueue_script(
        'amentum-testimonios-editor',
        get_template_directory_uri() . '/blocks/testimonios/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // CSS del editor (para que se vea bien en el backend)
    wp_enqueue_style(
        'amentum-testimonios-editor-style',
        get_template_directory_uri() . '/blocks/testimonios/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_testimonios_block_editor_assets');