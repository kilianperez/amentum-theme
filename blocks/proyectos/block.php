<?php
/**
 * Proyectos Block - Bloque Independiente
 * 
 * @package Amentum
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar el bloque Proyectos
 */
function amentum_register_proyectos_block() {
    register_block_type('amentum/proyectos', array(
        'render_callback' => 'amentum_render_proyectos_block',
        'attributes' => array(
            'title' => array(
                'type' => 'string',
                'default' => 'Proyectos Destacados'
            ),
            'subtitle' => array(
                'type' => 'string',
                'default' => 'Algunos de nuestros trabajos más recientes'
            ),
            'postsToShow' => array(
                'type' => 'number',
                'default' => 6
            ),
            'showViewAll' => array(
                'type' => 'boolean',
                'default' => true
            )
        )
    ));
}
add_action('init', 'amentum_register_proyectos_block');

/**
 * Renderizar el bloque Proyectos
 */
function amentum_render_proyectos_block($attributes) {
    $title = !empty($attributes['title']) ? esc_html($attributes['title']) : 'Proyectos Destacados';
    $subtitle = !empty($attributes['subtitle']) ? esc_html($attributes['subtitle']) : 'Algunos de nuestros trabajos más recientes';
    $posts_to_show = isset($attributes['postsToShow']) ? intval($attributes['postsToShow']) : 6;
    $show_view_all = isset($attributes['showViewAll']) ? $attributes['showViewAll'] : true;

    // Query para obtener proyectos
    $projects_query = new WP_Query(array(
        'post_type' => 'projects',
        'posts_per_page' => $posts_to_show,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    ob_start();
    ?>
    <section class="block-proyectos" id="proyectos-block">
        <div class="container">
            <div class="block-header">
            <h2 class="block-title"><?php echo $title; ?></h2>
            <p class="block-subtitle"><?php echo $subtitle; ?></p>
        </div>
        
        <?php if ($projects_query->have_posts()) : ?>
            <div class="proyectos-grid">
                <?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
                    <article class="proyecto-card">
                        <div class="proyecto-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('card_image'); ?>
                                </a>
                            <?php else : ?>
                                <div class="proyecto-placeholder">
                                    <span>Proyecto</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="proyecto-overlay">
                                <a href="<?php the_permalink(); ?>" class="proyecto-link">
                                    Ver Proyecto
                                </a>
                            </div>
                        </div>
                        
                        <div class="proyecto-content">
                            <h3 class="proyecto-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <?php if ($show_view_all) : ?>
                <div class="block-actions">
                    <a href="<?php echo get_post_type_archive_link('projects'); ?>" class="btn btn-outline">
                        Ver Todos los Proyectos
                    </a>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <div class="no-content">
                <p>No hay proyectos disponibles. Crea algunos proyectos en el admin de WordPress.</p>
                <a href="<?php echo admin_url('post-new.php?post_type=projects'); ?>" class="btn btn-primary">
                    Crear Primer Proyecto
                </a>
            </div>
        <?php endif; ?>
        </div>
    </section>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * Enqueue assets específicos para Proyectos block (Frontend)
 */
function amentum_enqueue_proyectos_block_assets() {
    // Solo cargar si el bloque está presente en la página
    if (has_block('amentum/proyectos')) {
        wp_enqueue_style(
            'amentum-proyectos-style',
            get_template_directory_uri() . '/blocks/proyectos/style.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'amentum_enqueue_proyectos_block_assets');

/**
 * Enqueue editor assets para Proyectos block (Backend)
 */
function amentum_enqueue_proyectos_block_editor_assets() {
    // JS del editor
    wp_enqueue_script(
        'amentum-proyectos-editor',
        get_template_directory_uri() . '/blocks/proyectos/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // CSS del editor (para que se vea bien en el backend)
    wp_enqueue_style(
        'amentum-proyectos-editor-style',
        get_template_directory_uri() . '/blocks/proyectos/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_proyectos_block_editor_assets');