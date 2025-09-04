<?php
/**
 * Funciones de soporte para imágenes y archivos SVG
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Agregar tamaños de imagen personalizados
 */
function amentum_tamano_personalizado()
{
    add_image_size('card_image', 600, 900, true); // Para cards de diseño
}
add_action('after_setup_theme', 'amentum_tamano_personalizado');

/**
 * Habilitar soporte para archivos SVG
 */
function amentum_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'amentum_mime_types');

/**
 * Arreglar la visualización de SVG en el Media Library
 */
function amentum_fix_svg_thumb_display($response, $attachment, $meta)
{
    if ($response['mime'] === 'image/svg+xml' && empty($response['sizes'])) {
        $svg_path = get_attached_file($attachment->ID);
        $dimensions = amentum_get_svg_dimensions($svg_path);
        
        $response['sizes'] = array(
            'full' => array(
                'url' => $response['url'],
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'orientation' => $dimensions['width'] > $dimensions['height'] ? 'landscape' : 'portrait'
            )
        );
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'amentum_fix_svg_thumb_display', 10, 3);

/**
 * Obtener dimensiones de archivos SVG
 */
function amentum_get_svg_dimensions($svg_path)
{
    $svg = @file_get_contents($svg_path);
    $xml = @simplexml_load_string($svg);
    $width = 0;
    $height = 0;
    
    if ($xml) {
        $attributes = $xml->attributes();
        $width = (int) $attributes->width;
        $height = (int) $attributes->height;
        
        if (!$width && !$height && $attributes->viewBox) {
            $viewBox = explode(' ', $attributes->viewBox);
            $width = (int) $viewBox[2];
            $height = (int) $viewBox[3];
        }
    }
    
    return array('width' => $width ?: 150, 'height' => $height ?: 150);
}