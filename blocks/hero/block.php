<?php
/**
 * Hero Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Hero
 */
function amentum_register_hero_block() {
    register_block_type('amentum/hero', array(
        'render_callback' => 'amentum_render_hero_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Bienvenido a Amentum'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Agencia creativa especializada en diseño, branding y desarrollo web'
            ),
            'showButtons' => array(
                'type' => 'boolean',
                'default' => true
            )
        )
    ));
}
add_action('init', 'amentum_register_hero_block');

/**
 * Renderizar el bloque Hero
 */
function amentum_render_hero_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Bienvenido a Amentum';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Agencia creativa especializada en diseño, branding y desarrollo web';
    $show_buttons = isset($attributes['showButtons']) ? $attributes['showButtons'] : true;

    ob_start();
    ?>
    <section class="block-hero" id="hero-block">
        <div class="container">
            <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title animate-fade-up">
                    <?php echo $title; ?>
                </h1>
                <p class="hero-subtitle animate-fade-up delay-1">
                    <?php echo $subtitle; ?>
                </p>
                <?php if ($show_buttons) : ?>
                    <div class="hero-actions animate-fade-up delay-2">
                        <a href="#proyectos-block" class="btn btn-primary">
                            Ver Proyectos
                        </a>
                        <a href="#contacto-block" class="btn btn-outline">
                            Contactar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="hero-visual">
                <div class="hero-shape animate-float">
                    <svg viewBox="0 0 200 200" class="shape-svg">
                        <circle cx="100" cy="100" r="80" fill="var(--color-primary)" opacity="0.2"/>
                        <circle cx="100" cy="100" r="60" fill="var(--color-secondary)" opacity="0.3"/>
                        <circle cx="100" cy="100" r="40" fill="var(--color-accent)" opacity="0.4"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue assets específicos para Hero block (Frontend)
 */
function amentum_enqueue_hero_block_assets() {
    // Solo cargar si el bloque está presente en la página
    if (has_block('amentum/hero')) {
        wp_enqueue_style(
            'amentum-hero-style',
            get_template_directory_uri() . '/blocks/hero/style.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_hero_block_assets');

/**
 * Enqueue editor assets para Hero block (Backend)
 */
function amentum_enqueue_hero_block_editor_assets() {
    // JS del editor
    wp_enqueue_script(
        'amentum-hero-editor',
        get_template_directory_uri() . '/blocks/hero/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // CSS del editor (para que se vea bien en el backend)
    wp_enqueue_style(
        'amentum-hero-editor-style',
        get_template_directory_uri() . '/blocks/hero/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_hero_block_editor_assets');