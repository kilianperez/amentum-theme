<?php
/**
 * Configuración del Customizer de WordPress para el tema Amentum
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Añadir configuraciones personalizadas al Customizer
 */
function amentum_customizer_settings($wp_customize)
{
    // Añadir sección principal
    $wp_customize->add_section('amentum_custom_section', array(
        'title' => 'Configuración Amentum',
        'priority' => 30,
    ));

    // Control de campo de texto para Copyright
    $wp_customize->add_setting('amentum_copyright', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('amentum_copyright_control', array(
        'label' => 'Texto Copyright',
        'section' => 'amentum_custom_section',
        'settings' => 'amentum_copyright',
        'type' => 'text',
    ));

    // Control de campo de texto para Newsletter
    $wp_customize->add_setting('amentum_newsletter', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('amentum_newsletter_control', array(
        'label' => 'Texto Newsletter',
        'section' => 'amentum_custom_section',
        'settings' => 'amentum_newsletter',
        'type' => 'text',
    ));

    // Control de correo electrónico para formularios
    $wp_customize->add_setting('amentum_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
    ));

    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'amentum_email_control',
        array(
            'label' => 'Email para formularios',
            'section' => 'amentum_custom_section',
            'settings' => 'amentum_email',
            'type' => 'email',
        )
    ));

    // Control de logo footer en la sección "Identidad del sitio"
    $wp_customize->add_setting('amentum_footer_logo', array(
        'default' => '',
        'sanitize_callback' => 'absint', // absint para WP_Customize_Media_Control que guarda attachment ID
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control(
        $wp_customize,
        'amentum_footer_logo_control',
        array(
            'label' => 'Logo Footer (A-S)',
            'description' => 'Logo reducido para el footer del sitio',
            'section' => 'title_tagline', // Sección "Identidad del sitio"
            'settings' => 'amentum_footer_logo',
            'mime_type' => 'image',
        )
    ));

    /*
    // Ejemplo de control de selector de páginas (comentado)
    $wp_customize->add_setting('amentum_url_privacidad', array(
        'default' => '',
        'sanitize_callback' => 'esc_url',
    ));

    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'amentum_url_privacidad_control',
        array(
            'label' => 'URL de la Página',
            'section' => 'amentum_custom_section',
            'settings' => 'amentum_url_privacidad',
            'type' => 'dropdown-pages',
        )
    ));
    */
}
add_action('customize_register', 'amentum_customizer_settings');