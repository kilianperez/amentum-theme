<?php
/**
 * About Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque About
 */
function amentum_register_about_block() {
    error_log('ABOUT BLOCK: Registrando bloque about');
    register_block_type('amentum/about', array(
        'render_callback' => 'amentum_render_about_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Soy Beatriz Llamas'
            ),
            'description' => array(
                'type' => 'string',
                'default' => 'Crecí en Madrid, rodeada de pasión por la gastronomía y el cuidado de los detalles. Fundé mi primer catering, La Colineta, durante la universidad y más tarde dirigí la escuela de cocina Alambique, donde organicé eventos exclusivos y comencé a escribir sobre gastronomía. Tras varios años en el extranjero, creé Amentum un proyecto que une mi experiencia en cocina, eventos y relaciones humanas para ofrecer experiencias únicas y personalizadas.'
            ),
            'mainImage' => array(
                'type' => 'object',
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/img/template/about/chef-beatriz.png',
                    'alt' => 'Beatriz Llamas - Chef'
                )
            ),
            'decorativeImage' => array(
                'type' => 'object',
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/img/template/about/decorativa-paisaje.png',
                    'alt' => 'Imagen decorativa'
                )
            ),
            'buttonText' => array(
                'type' => 'string',
                'default' => 'Saber más'
            ),
            'buttonLink' => array(
                'type' => 'string',
                'default' => '#'
            ),
            'showDecorative' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showButton' => array(
                'type' => 'boolean',
                'default' => true
            )
        )
    ));
}
add_action('init', 'amentum_register_about_block');

/**
 * Renderizar el bloque About
 */
function amentum_render_about_block($attributes) {
    // Debug: verificar si esta función se ejecuta
    error_log('ABOUT BLOCK: Función de renderizado ejecutándose');
    $title = !empty($attributes['title']) ? wp_kses_post($attributes['title']) : 'Soy Beatriz Llamas';
    $description = !empty($attributes['description']) ? wp_kses_post($attributes['description']) : 'Crecí en Madrid, rodeada de pasión por la gastronomía...';
    $main_image = !empty($attributes['mainImage']) ? $attributes['mainImage'] : array(
        'url' => get_template_directory_uri() . '/assets/img/template/about/chef-beatriz.png',
        'alt' => 'Beatriz Llamas - Chef'
    );
    $decorative_image = !empty($attributes['decorativeImage']) ? $attributes['decorativeImage'] : array(
        'url' => get_template_directory_uri() . '/assets/img/template/about/decorativa-paisaje.png',
        'alt' => 'Imagen decorativa'
    );
    $button_text = !empty($attributes['buttonText']) ? esc_html($attributes['buttonText']) : 'Saber más';
    $button_link = !empty($attributes['buttonLink']) ? esc_url($attributes['buttonLink']) : '#';
    $show_decorative = isset($attributes['showDecorative']) ? $attributes['showDecorative'] : true;
    $show_button = isset($attributes['showButton']) ? $attributes['showButton'] : true;

    ob_start();
    ?>
    <section class="block-about" id="about-block">
        <div class="container">
            <div class="about-content">
                <!-- Mobile First: Texto primero -->
                <div class="about-text">
                    <h2 class="about-title">
                        <?php echo $title; ?>
                    </h2>
                    <p class="about-description">
                        <?php echo $description; ?>
                    </p>
                    <?php if ($show_button && $button_link && $button_text) : ?>
                        <div class="about-actions">
                            <a href="<?php echo $button_link; ?>" class="btn btn--white">
                                <?php echo $button_text; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Imagen principal después del texto -->
                <div class="about-image-main">
                    <?php if ($main_image && isset($main_image['url'])) : ?>
                        <img 
                            src="<?php echo esc_url($main_image['url']); ?>" 
                            alt="<?php echo esc_attr($main_image['alt'] ?? ''); ?>"
                            loading="lazy"
                            class="main-image"
                        />
                    <?php endif; ?>
                </div>

                <!-- Imagen decorativa al final -->
                <?php if ($show_decorative) : ?>
                    <div class="about-image-decorative">
                        <?php if ($decorative_image && isset($decorative_image['url'])) : ?>
                            <img 
                                src="<?php echo esc_url($decorative_image['url']); ?>" 
                                alt="<?php echo esc_attr($decorative_image['alt'] ?? ''); ?>"
                                loading="lazy"
                                class="decorative-image"
                            />
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}


