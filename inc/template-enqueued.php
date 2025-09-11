<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

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
   
    // PASO 1: Desregistrar jQuery de WordPress para usar nuestro local
    wp_deregister_script('jquery');
    
    // PASO 5 FINAL: Todas las librerías ahora están en bundle local - Sin dependencias CDN
   



    wp_enqueue_script('all', get_template_directory_uri().'/assets/dist/js/all.js', array(),'1.5.6', true);

}

add_action('wp_enqueue_scripts', 'amentum_scripts_styles');


//! scripts y styles
function amentum_scripts_styles_editor(){

    wp_enqueue_style(
        'amentum-admin-styles',
        get_template_directory_uri() . '/assets/dist/css/admin.css',
        array(),
        wp_get_theme()->get('Version'),
        'all'
    );

}


add_action( 'enqueue_block_editor_assets', 'amentum_scripts_styles_editor', 999 );