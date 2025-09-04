<?php
/**
 * Estilos y funciones para el área de administración
 *
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * CSS personalizado para iconos de post types en el admin
 */
add_action('admin_head', 'amentum_custom_post_type_icons_css');
function amentum_custom_post_type_icons_css()
{
    echo '<style>
    /* Iconos de post types personalizados en crema */
    #menu-posts-projects .wp-menu-image:before,
    #menu-posts-merch .wp-menu-image:before,
    #menu-posts-magazine .wp-menu-image:before {
        color: #d4c5a5 !important;
    }
    
    /* Hover state */
    #menu-posts-projects:hover .wp-menu-image:before,
    #menu-posts-merch:hover .wp-menu-image:before,
    #menu-posts-magazine:hover .wp-menu-image:before {
        color: #e6dcc1 !important;
    }
    
    /* Current/active state */
    #menu-posts-projects.wp-has-current-submenu .wp-menu-image:before,
    #menu-posts-merch.wp-has-current-submenu .wp-menu-image:before,
    #menu-posts-magazine.wp-has-current-submenu .wp-menu-image:before,
    #menu-posts-projects.current .wp-menu-image:before,
    #menu-posts-merch.current .wp-menu-image:before,
    #menu-posts-magazine.current .wp-menu-image:before {
        color: #c2b08a !important;
    }
    </style>';
}

/**
 * Suprimir algunos warnings de WordPress si es necesario
 */
if (!function_exists('amentum_suppress_doing_it_wrong')) {
    function amentum_suppress_doing_it_wrong($trigger, $function, $message, $version)
    {
        // Suprimir algunos warnings específicos si es necesario
        return $trigger;
    }
    add_filter('doing_it_wrong_trigger_error', 'amentum_suppress_doing_it_wrong', 10, 4);
}