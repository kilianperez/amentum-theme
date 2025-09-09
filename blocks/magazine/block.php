<?php
/**
 * Magazine Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Magazine
 */
function amentum_register_magazine_block() {
    register_block_type('amentum/magazine', array(
        'render_callback' => 'amentum_render_magazine_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Últimos Artículos'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Mantente al día con nuestras últimas publicaciones'
            ),
            'postsToShow' => array(
                'type' => 'number',
                'default' => 3
            )
        )
    ));
}
add_action('init', 'amentum_register_magazine_block');

/**
 * Renderizar el bloque Magazine
 */
function amentum_render_magazine_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Últimos Artículos';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Mantente al día con nuestras últimas publicaciones';
    $posts_to_show = isset($attributes['postsToShow']) ? intval($attributes['postsToShow']) : 3;

    // Query para obtener artículos de magazine
    $magazine_query = new WP_Query(array(
        'post_type' => 'magazine',
        'posts_per_page' => $posts_to_show,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    ob_start();
    ?>
    <section class="block-magazine" id="magazine-block">
        <div class="container">
            <div class="block-header">
            <h2 class="block-title"><?php echo $title; ?></h2>
            <p class="block-subtitle"><?php echo $subtitle; ?></p>
        </div>
        
        <?php if ($magazine_query->have_posts()) : ?>
            <div class="magazine-grid">
                <?php while ($magazine_query->have_posts()) : $magazine_query->the_post(); ?>
                    <article class="magazine-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="magazine-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="magazine-content">
                            <h3 class="magazine-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php if (has_excerpt()) : ?>
                                <p class="magazine-excerpt">
                                    <?php the_excerpt(); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="magazine-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="block-actions">
                <a href="<?php echo get_post_type_archive_link('magazine'); ?>" class="btn btn-outline">
                    Ver Todos los Artículos
                </a>
            </div>
            
        <?php else : ?>
            <div class="no-content">
                <p>No hay artículos disponibles.</p>
                <a href="<?php echo admin_url('post-new.php?post_type=magazine'); ?>" class="btn btn-primary">
                    Crear Primer Artículo
                </a>
            </div>
        <?php endif; ?>
        </div>
    </section>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}


