<?php
/**
 * Hero Full Block - Bloque Independiente para Pantalla Completa
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Hero Full
 */
function amentum_register_hero_full_block() {
    register_block_type('amentum/hero-full', array(
        'render_callback' => 'amentum_render_hero_full_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Bienvenido a Amentum'
            ),
            'content' => array(
                'type' => 'string',
                'default' => 'Somos una agencia creativa especializada en diseño, branding y desarrollo web que transforma ideas en experiencias digitales extraordinarias. Nuestro enfoque combina creatividad, estrategia y tecnología para crear soluciones que realmente conectan con tu audiencia.'
            ),
            'backgroundImage' => array(
                'type' => 'string',
                'default' => get_template_directory_uri() . '/assets/images/hero-full-bg.png'
            ),
            'overlayOpacity' => array(
                'type' => 'number',
                'default' => 0.5
            ),
            'textColor' => array(
                'type' => 'string',
                'default' => 'white'
            )
        )
    ));
}
add_action('init', 'amentum_register_hero_full_block');

/**
 * Función para convertir la primera letra en drop cap con serifa
 */
function amentum_add_drop_cap($text) {
    if (empty($text)) {
        return $text;
    }
    
    // Buscar la primera letra del texto (excluyendo espacios y HTML)
    $text = trim($text);
    
    // Regex para encontrar la primera letra después de cualquier etiqueta HTML
    $pattern = '/^(\s*(?:<[^>]*>)*\s*)([a-záéíóúüñA-ZÁÉÍÓÚÜÑ])/u';
    
    if (preg_match($pattern, $text, $matches)) {
        $before = $matches[1]; // Contenido antes de la primera letra (espacios, HTML)
        $first_letter = $matches[2]; // Primera letra
        $rest = substr($text, strlen($before . $first_letter)); // Resto del texto
        
        // Crear el drop cap con la primera letra
        return $before . '<span class="drop-cap">' . $first_letter . '</span>' . $rest;
    }
    
    return $text;
}

/**
 * Renderizar el bloque Hero Full
 */
function amentum_render_hero_full_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Bienvenido a Amentum';
    $content = !empty($attributes['content']) ? $attributes['content'] : 'Somos una agencia creativa especializada en diseño, branding y desarrollo web que transforma ideas en experiencias digitales extraordinarias.';
    $background_image = !empty($attributes['backgroundImage']) ? esc_url($attributes['backgroundImage']) : get_template_directory_uri() . '/assets/images/hero-full-bg.png';
    $overlay_opacity = isset($attributes['overlayOpacity']) ? floatval($attributes['overlayOpacity']) : 0.5;
    $text_color = !empty($attributes['textColor']) ? esc_attr($attributes['textColor']) : 'white';

    // Aplicar drop cap al contenido
    $content_with_drop_cap = amentum_add_drop_cap($content);

    ob_start();
    ?>
    <section class="block-hero-full" style="background-image: url('<?php echo $background_image; ?>');">
        <div class="hero-full-overlay" style="opacity: <?php echo $overlay_opacity; ?>;"></div>
        <div class="container">
            <div class="hero-full-content" style="color: <?php echo $text_color; ?>;">
                <h1 class="hero-full-title"><?php echo $title; ?></h1>
                <div class="hero-full-text">
                    <?php echo wp_kses_post($content_with_drop_cap); ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue assets específicos para Hero Full block (Frontend)
 */
function amentum_enqueue_hero_full_block_assets() {
    // Solo cargar si el bloque está presente en la página
    if (has_block('amentum/hero-full')) {
        wp_enqueue_style(
            'amentum-hero-full-style',
            get_template_directory_uri() . '/blocks/hero-full/style.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_hero_full_block_assets');

/**
 * Enqueue editor assets para Hero Full block (Backend)
 */
function amentum_enqueue_hero_full_block_editor_assets() {
    // JS del editor
    wp_enqueue_script(
        'amentum-hero-full-editor',
        get_template_directory_uri() . '/blocks/hero-full/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // CSS del editor (para que se vea bien en el backend)
    wp_enqueue_style(
        'amentum-hero-full-editor-style',
        get_template_directory_uri() . '/blocks/hero-full/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_hero_full_block_editor_assets');