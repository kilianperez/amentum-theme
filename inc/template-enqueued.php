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

    // wp_enqueue_style('Estilos',get_template_directory_uri(). '/assets/css/estilos.css', array(), '1.0.0');    
    // wp_enqueue_style('Arreglos',get_template_directory_uri(). '/assets/css/arreglos.css', array(), '1.0.0');    
    // CDN 
    // wp_enqueue_style('CDNCss', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.0.0'); 
    
    // Archivo externo GF y FA
    // wp_enqueue_style('OpenSans', 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap', array(), '1.0.0');    
    // wp_enqueue_style('Montserrat', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', array(), '1.0.0');    
    
    // wp_enqueue_style('FontAwesome6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css', array(), '6.0.0');    
    // wp_enqueue_style('FontAwesome4', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css', array(), '4.7.0');    
    
    // wp_enqueue_style('SlickCSS', get_template_directory_uri().'/assets/slick-1.8.1/slick/slick.css', array(), '1.8.1');    
    // wp_enqueue_style('SlickThemeCSS', get_template_directory_uri().'/assets/slick-1.8.1/slick/slick-theme.css', array(), '1.8.1');    
        
    // hoja de estilos principal 
    wp_enqueue_style('style', get_stylesheet_uri(), array(
    'all',
    // 'Estilos',
    // 'CDNCss',
    // 'OpenSans',
    // 'Montserrat',
    // 'FontAwesome6',
    // 'FontAwesome4',
    // 'SlickCSS',
    // 'SlickThemeCSS',
    ), '1.0.0');


    wp_enqueue_script('gsap','https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js', array('jquery'),'3.11.5', true);
    wp_enqueue_script('jquery-validate','https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/jquery.validationEngine.min.js', array('jquery'),'1.19.5', true);
    

    // true para que aparezca en el footer 
    wp_enqueue_script('split-type', 'https://unpkg.com/split-type', array('jquery','jquery-validate', 'gsap'),'1.0.0', true);
    wp_enqueue_script('scrollTrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/ScrollTrigger.min.js', array('jquery','jquery-validate', 'split-type', 'gsap'),'1.0.6', true);
    wp_enqueue_script('barba', 'https://unpkg.com/@barba/core', array('jquery', 'jquery-validate','gsap'),'1.0.0', true);
   



    wp_enqueue_script('all', get_template_directory_uri().'/assets/dist/js/all.js', array('jquery', 'jquery-validate','gsap', 'split-type', 'scrollTrigger','barba'),'1.0.0', true);
    // wp_enqueue_script('slickJS', get_template_directory_uri().'/assets/slick-1.8.1/slick/slick.min.js', array('jquery'),'1.8.1',true);
    // wp_enqueue_script('swup','https://cdnjs.cloudflare.com/ajax/libs/swup/3.0.6/Swup.umd.js', array('jquery'),'3.0.6',true);


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