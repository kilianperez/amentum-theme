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
            'mostrarEventos' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'numeroEventos' => array(
                'type' => 'number',
                'default' => 5
            ),
            'eventosSeleccionados' => array(
                'type' => 'array',
                'default' => array()
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
    $texto_inicial = !empty($attributes['textoInicial']) ? esc_html($attributes['textoInicial']) : '';
    $descripcion_inicial = !empty($attributes['descripcionInicial']) ? esc_html($attributes['descripcionInicial']) : 'Ver más';
    $mostrar_eventos = isset($attributes['mostrarEventos']) ? $attributes['mostrarEventos'] : true;
    $numero_eventos = isset($attributes['numeroEventos']) ? intval($attributes['numeroEventos']) : 5;
    $eventos_seleccionados = isset($attributes['eventosSeleccionados']) ? $attributes['eventosSeleccionados'] : array();

    // Obtener eventos publicados
    $eventos = array();
    $primer_evento_enlace = '';
    $primer_evento_titulo = '';
    $imagen_inicial_auto = get_template_directory_uri() . '/assets/images/evento-inicial.jpg';
    $eventos_query = null;
    
    if ($mostrar_eventos) {
        // Modo automático: obtener eventos recientes
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
    } else if (!empty($eventos_seleccionados)) {
        // Modo manual: obtener eventos específicos seleccionados
        $eventos_query = new WP_Query(array(
            'post_type' => 'eventos',
            'post__in' => $eventos_seleccionados,
            'post_status' => 'publish',
            'orderby' => 'post__in', // Mantener el orden seleccionado
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'EXISTS'
                )
            )
        ));
    }

    if ($eventos_query && $eventos_query->have_posts()) {
        $first_event = true;
        while ($eventos_query->have_posts()) {
            $eventos_query->the_post();
            $post_id = get_the_ID();
            $evento_data = array(
                'id' => $post_id,
                'titulo' => get_the_title(),
                'imagen' => get_the_post_thumbnail_url($post_id, 'full'),
                'enlace' => get_permalink($post_id),
                'fecha' => get_the_date('j M Y')
            );
            
            // Si es el primer evento, usar su imagen, enlace y título para el slide inicial
            if ($first_event) {
                $primer_evento_enlace = $evento_data['enlace'];
                $primer_evento_titulo = $evento_data['titulo'];
                if (!empty($evento_data['imagen'])) {
                    $imagen_inicial_auto = $evento_data['imagen'];
                }
                $first_event = false;
            }
            
            $eventos[] = $evento_data;
        }
        wp_reset_postdata();
    }

    // Si no hay texto inicial personalizado, usar el título del primer evento
    if (empty($texto_inicial) && !empty($primer_evento_titulo)) {
        $texto_inicial = $primer_evento_titulo;
    } elseif (empty($texto_inicial)) {
        $texto_inicial = 'Historias en imágenes';
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
                        <img src="<?php echo esc_url($imagen_inicial_auto); ?>" alt="<?php echo esc_attr($texto_inicial); ?>" loading="lazy">
                    </div>
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <div class="slide-inner">
                            <h2 class="slide-title"><?php echo $texto_inicial; ?></h2>
                            <?php if (!empty($primer_evento_enlace)): ?>
                                <a href="<?php echo esc_url($primer_evento_enlace); ?>" class="btn btn--white"><?php echo $descripcion_inicial; ?></a>
                            <?php else: ?>
                                <p class="slide-description"><?php echo $descripcion_inicial; ?></p>
                            <?php endif; ?>
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
                                    <a href="<?php echo esc_url($evento['enlace']); ?>" class="btn btn--white">Ver evento</a>
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
