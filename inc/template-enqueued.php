<?php
/**
 * Enqueue scripts and styles.
 */
function amentumscripts() {
	wp_enqueue_style( 'amentum-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'amentum-style', 'rtl', 'replace' );

	// wp_enqueue_script( 'amentum-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'amentumscripts' );





//! scripts y styles
function amentum_scripts_styles(){

    // Estilos    
    wp_enqueue_style('all',get_template_directory_uri(). '/assets/dist/css/style.css', array(), '1.0.0');
    
    // CSS compilado de todos los bloques - Optimización SEO
    wp_enqueue_style('amentum-blocks', get_template_directory_uri() . '/assets/dist/css/blocks.css', array('all'), '1.0.0');    

   

    wp_enqueue_script('gsap','https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js', array('jquery'),'3.11.5', true);
    wp_enqueue_script('jquery-validate','https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/jquery.validationEngine.min.js', array('jquery'),'1.19.5', true);
    

    // true para que aparezca en el footer 
    wp_enqueue_script('split-type', 'https://unpkg.com/split-type', array('jquery','jquery-validate', 'gsap'),'1.0.0', true);
    wp_enqueue_script('scrollTrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/ScrollTrigger.min.js', array('jquery','jquery-validate', 'split-type', 'gsap'),'1.0.6', true);
    wp_enqueue_script('barba', 'https://unpkg.com/@barba/core', array('jquery', 'jquery-validate','gsap'),'1.0.0', true);
   



    wp_enqueue_script('all', get_template_directory_uri().'/assets/dist/js/all.js', array('jquery', 'jquery-validate','gsap', 'split-type', 'scrollTrigger','barba'),'1.0.0', true);

}

add_action('wp_enqueue_scripts', 'amentum_scripts_styles');


//! scripts y styles
function amentum_scripts_styles_editor(){

    // MÉTODO AVANZADO: Desregistrar completamente estilos reset de WordPress
    wp_deregister_style('wp-reset-editor-styles');
    wp_deregister_style('wp-block-library-theme');
    wp_deregister_style('wp-format-library');
    
    // Registrar nuestro admin.css con el handle de wp-reset-editor-styles
    // para mantener dependencias pero con nuestros estilos
    wp_register_style(
        'wp-reset-editor-styles',
        get_template_directory_uri() . '/assets/dist/css/admin.css',
        array(),
        '1.0.0',
        'all'
    );
    
    // Volver a enqueue solo estilos esenciales del editor (sin reset)
    wp_enqueue_style('wp-block-editor-styles', includes_url('css/dist/block-editor/style.css'), false);
    wp_enqueue_style('wp-edit-post-styles', includes_url('css/dist/edit-post/style.css'), false);
    

}


add_action( 'enqueue_block_editor_assets', 'amentum_scripts_styles_editor', 999 );