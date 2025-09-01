<?php
/**
 * Eventos Swiper Block - Swiper fullscreen con eventos
 *
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Eventos Swiper
 */
function amentum_register_eventos_swiper_block() {
    register_block_type('amentum/eventos-swiper', array(
        'render_callback' => 'amentum_render_eventos_swiper_block',
        'attributes' => array(
            'textoInicial' => array(
                'type' => 'string',
                'default' => 'Historias en imágenes'
            ),
            'descripcionInicial' => array(
                'type' => 'string',
                'default' => 'Ver más'
            ),
            'imagenInicial' => array(
                'type' => 'string',
                'default' => get_template_directory_uri() . '/assets/images/evento-inicial.jpg'
            ),
            'mostrarEventos' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'numeroEventos' => array(
                'type' => 'number',
                'default' => 5
            )
        )
    ));
}
add_action('init', 'amentum_register_eventos_swiper_block');

/**
 * Renderizar el bloque Eventos Swiper
 */
function amentum_render_eventos_swiper_block($attributes) {
    // Sanitizar datos de entrada
    $texto_inicial = !empty($attributes['textoInicial']) ? esc_html($attributes['textoInicial']) : 'Historias en imágenes';
    $descripcion_inicial = !empty($attributes['descripcionInicial']) ? esc_html($attributes['descripcionInicial']) : 'Ver más';
    $imagen_inicial = !empty($attributes['imagenInicial']) ? esc_url($attributes['imagenInicial']) : get_template_directory_uri() . '/assets/images/evento-inicial.jpg';
    $mostrar_eventos = isset($attributes['mostrarEventos']) ? $attributes['mostrarEventos'] : true;
    $numero_eventos = isset($attributes['numeroEventos']) ? intval($attributes['numeroEventos']) : 5;

    // Obtener eventos publicados
    $eventos = array();
    if ($mostrar_eventos) {
        $eventos_query = new WP_Query(array(
            'post_type' => 'eventos',
            'posts_per_page' => $numero_eventos,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'EXISTS'
                )
            )
        ));

        if ($eventos_query->have_posts()) {
            while ($eventos_query->have_posts()) {
                $eventos_query->the_post();
                $eventos[] = array(
                    'id' => get_the_ID(),
                    'titulo' => get_the_title(),
                    'imagen' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'enlace' => get_the_permalink(),
                    'fecha' => get_the_date('j M Y')
                );
            }
        }
        wp_reset_postdata();
    }

    // Generar ID único para el swiper
    $swiper_id = 'eventos-swiper-' . uniqid();

    ob_start();
    ?>
    <section class="block-eventos-swiper">
        <div class="swiper <?php echo esc_attr($swiper_id); ?>">
            <div class="swiper-wrapper">
                
                <!-- Slide inicial editable -->
                <div class="swiper-slide slide-inicial">
                    <div class="slide-background">
                        <img src="<?php echo $imagen_inicial; ?>" alt="<?php echo esc_attr($texto_inicial); ?>" loading="lazy">
                    </div>
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <div class="slide-inner">
                            <h2 class="slide-title"><?php echo $texto_inicial; ?></h2>
                            <p class="slide-description"><?php echo $descripcion_inicial; ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($eventos)): ?>
                    <?php foreach ($eventos as $evento): ?>
                        <div class="swiper-slide slide-evento" data-evento-id="<?php echo esc_attr($evento['id']); ?>">
                            <div class="slide-background">
                                <img src="<?php echo esc_url($evento['imagen']); ?>" alt="<?php echo esc_attr($evento['titulo']); ?>" loading="lazy">
                            </div>
                            <div class="slide-overlay"></div>
                            <div class="slide-content">
                                <div class="slide-inner">
                                    <h2 class="slide-title"><?php echo esc_html($evento['titulo']); ?></h2>
                                    <p class="slide-date"><?php echo esc_html($evento['fecha']); ?></p>
                                    <a href="<?php echo esc_url($evento['enlace']); ?>" class="slide-link">Ver evento</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- Navegación -->
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <?php
    return ob_get_clean();
}

/**
 * ⚡ SISTEMA OPTIMIZADO DE CSS COMPILADO ⚡
 * Los estilos del bloque se cargan automáticamente desde blocks.css
 */

/**
 * Enqueue editor assets para Eventos Swiper block (Backend)
 */
function amentum_enqueue_eventos_swiper_block_editor_assets() {
    wp_enqueue_script(
        'amentum-eventos-swiper-editor',
        get_template_directory_uri() . '/blocks/eventos-swiper/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        wp_get_theme()->get('Version') . '-' . time(),
        true
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_eventos_swiper_block_editor_assets');