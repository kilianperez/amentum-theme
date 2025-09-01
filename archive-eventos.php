<?php
/**
 * Template para el archivo de eventos
 * 
 * @package Amentum
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="eventos-archive-page">
        
        <!-- Hero Header -->
        <section class="eventos-hero">
            <div class="container">
                <div class="eventos-hero-content">
                    <h1 class="eventos-title">Nuestros Eventos</h1>
                    <p class="eventos-subtitle">Descubre los momentos únicos que creamos para hacer especial cada celebración</p>
                </div>
            </div>
        </section>

        <!-- Eventos Grid -->
        <section class="eventos-grid-section">
            <div class="container">
                
                <?php if (have_posts()) : ?>
                
                <div class="eventos-grid">
                    <?php while (have_posts()) : the_post(); 
                        $imagen = get_the_post_thumbnail_url(get_the_ID(), 'large');
                        if (!$imagen) {
                            $imagen = get_template_directory_uri() . '/assets/images/placeholder-evento.jpg';
                        }
                        $fecha = get_the_date('j M Y');
                        $excerpt = get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20);
                    ?>
                    
                    <article class="evento-card">
                        <a href="<?php the_permalink(); ?>" class="evento-card-link">
                            
                            <div class="evento-imagen">
                                <img src="<?php echo esc_url($imagen); ?>" 
                                     alt="<?php the_title_attribute(); ?>" 
                                     loading="lazy">
                                <div class="evento-overlay">
                                    <span class="evento-fecha-overlay"><?php echo esc_html($fecha); ?></span>
                                </div>
                            </div>
                            
                            <div class="evento-content">
                                <h2 class="evento-card-title"><?php the_title(); ?></h2>
                                <p class="evento-excerpt"><?php echo esc_html($excerpt); ?></p>
                                <div class="evento-meta">
                                    <time class="evento-fecha" datetime="<?php echo get_the_date('c'); ?>">
                                        <?php echo esc_html($fecha); ?>
                                    </time>
                                    <span class="evento-leer-mas">Ver evento →</span>
                                </div>
                            </div>
                            
                        </a>
                    </article>
                    
                    <?php endwhile; ?>
                </div>

                <!-- Paginación -->
                <div class="eventos-pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← Anterior',
                        'next_text' => 'Siguiente →',
                        'screen_reader_text' => 'Navegación de eventos'
                    ));
                    ?>
                </div>
                
                <?php else : ?>
                
                <div class="no-eventos">
                    <h2>No hay eventos disponibles</h2>
                    <p>Actualmente no tenemos eventos publicados. ¡Vuelve pronto para ver nuestras próximas celebraciones!</p>
                </div>
                
                <?php endif; ?>
                
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?>