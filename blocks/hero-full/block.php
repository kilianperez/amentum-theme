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
            'superTitle' => array(
                'type' => 'string',
                'default' => 'En'
            ),
            'title' => array(
                'type' => 'string',
                'default' => 'Amentum Events'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'creamos experiencias que dejan huella.'
            ),
            'backgroundImage' => array(
                'type' => 'string',
                'default' => '/wp-content/themes/amentum/assets/images/amentum-events-bg.png'
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
 * Función REUTILIZABLE para convertir la primera letra en drop cap con serifa
 * Úsala en cualquier parte del theme pasándole cualquier texto
 * 
 * @param string $text El texto al que aplicar drop cap
 * @param array $options Opciones personalizables
 * @return string Texto con drop cap aplicado
 */
function amentum_add_drop_cap($text, $options = array()) {
    if (empty($text)) {
        return $text;
    }
    
    // Opciones por defecto
    $defaults = array(
        'font_family' => "'Times New Roman', 'Georgia', 'Playfair Display', serif",
        'font_size' => 'inherit',
        'color' => 'currentColor',
        'class' => 'drop-cap'
    );
    
    $options = wp_parse_args($options, $defaults);
    
    // Buscar la primera letra del texto (excluyendo espacios y HTML)
    $text = trim($text);
    
    // Regex para encontrar la primera letra después de cualquier etiqueta HTML
    $pattern = '/^(\s*(?:<[^>]*>)*\s*)([a-záéíóúüñA-ZÁÉÍÓÚÜÑ])/u';
    
    if (preg_match($pattern, $text, $matches)) {
        $before = $matches[1]; // Contenido antes de la primera letra (espacios, HTML)
        $first_letter = $matches[2]; // Primera letra
        $rest = substr($text, strlen($before . $first_letter)); // Resto del texto
        
        // Crear el drop cap con la primera letra y estilos inline
        $drop_cap_html = sprintf(
            '<span class="%s" style="font-family: %s; font-size: %s; color: %s;">%s</span>',
            esc_attr($options['class']),
            esc_attr($options['font_family']),
            esc_attr($options['font_size']),
            esc_attr($options['color']),
            esc_html($first_letter)
        );
        
        return $before . $drop_cap_html . $rest;
    }
    
    return $text;
}

/**
 * Renderizar el bloque Hero Full
 */
function amentum_render_hero_full_block($attributes) {
    $super_title = !empty($attributes['superTitle']) ? esc_html($attributes['superTitle']) : 'En';
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Amentum Events';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'creamos experiencias que dejan huella.';
    $background_image = !empty($attributes['backgroundImage']) ? esc_url($attributes['backgroundImage']) : get_template_directory_uri() . '/assets/images/hero-full-bg.png';
    $overlay_opacity = isset($attributes['overlayOpacity']) ? floatval($attributes['overlayOpacity']) : 0.5;
    $text_color = !empty($attributes['textColor']) ? esc_attr($attributes['textColor']) : 'white';

    // Aplicar drop cap al super título
    $super_title_with_drop_cap = amentum_add_drop_cap($super_title);

    ob_start();
    ?>
    <section class="block-hero-full" style="background-image: url('<?php echo $background_image; ?>');">
        <div class="hero-full-overlay" style="opacity: <?php echo $overlay_opacity; ?>;"></div>
        <div class="container">
            <div class="hero-full-content" style="color: <?php echo $text_color; ?>;">
                <div class="hero-full-super-title"><?php echo wp_kses_post($super_title_with_drop_cap); ?></div>
                <h1 class="hero-full-title"><?php echo $title; ?></h1>
                <div class="hero-full-subtitle"><?php echo $subtitle; ?></div>
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