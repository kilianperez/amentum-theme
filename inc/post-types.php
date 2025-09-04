<?php
/**
 * Custom Post Types para el theme Amentum
 * 
 * @package Amentum
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function amentum_custom_post_types()
{
    // Post type Eventos
    $labels = array(
        'name'                  => _x('Eventos', 'amentum-text'),
        'singular_name'         => _x('Evento', 'amentum-text'),
        'menu_name'             => __('Eventos', 'amentum-text'),
        'name_admin_bar'        => __('Evento', 'amentum-text'),
        'archives'              => __('Archivos de eventos', 'amentum-text'),
        'attributes'            => __('Atributos de eventos', 'amentum-text'),
        'parent_item_colon'     => __('Evento padre', 'amentum-text'),
        'all_items'             => __('Todos los eventos', 'amentum-text'),
        'add_new_item'          => __('Añadir nuevo evento', 'amentum-text'),
        'add_new'               => __('Añadir nuevo', 'amentum-text'),
        'new_item'              => __('Nuevo evento', 'amentum-text'),
        'edit_item'             => __('Editar evento', 'amentum-text'),
        'update_item'           => __('Actualizar evento', 'amentum-text'),
        'view_item'             => __('Ver evento', 'amentum-text'),
        'view_items'            => __('Ver eventos', 'amentum-text'),
        'search_items'          => __('Buscar evento', 'amentum-text'),
        'not_found'             => __('No se han encontrado eventos', 'amentum-text'),
        'not_found_in_trash'    => __('No se han encontrado eventos en la papelera', 'amentum-text'),
        'featured_image'        => __('Imagen destacada', 'amentum-text'),
        'set_featured_image'    => __('Establecer imagen destacada', 'amentum-text'),
        'remove_featured_image' => __('Eliminar imagen destacada', 'amentum-text'),
        'use_featured_image'    => __('Usar como imagen destacada', 'amentum-text'),
        'insert_into_item'      => __('Insertar en evento', 'amentum-text'),
        'uploaded_to_this_item' => __('Subir evento', 'amentum-text'),
        'items_list'            => __('Lista de eventos', 'amentum-text'),
        'items_list_navigation' => __('Navegar en los eventos', 'amentum-text'),
        'filter_items_list'     => __('Filtrar eventos', 'amentum-text'),
    );
    
    $rewrite = array(
        'slug'                  => 'eventos',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    
    $args = array(
        'label'                 => __('Evento', 'amentum-text'),
        'description'           => __('Añade y gestiona todos tus eventos', 'amentum-text'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', 'editor', 'revisions', 'custom-fields' ),
        'taxonomies'            => array(  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-calendar-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    
    register_post_type('eventos', $args);
}
add_action('init', 'amentum_custom_post_types', 0);

// Register Custom Taxonomy para Eventos
function amentum_eventos_taxonomy()
{
    $labels = array(
        'name'              => _x('Tipos de Evento', 'taxonomy general name'),
        'singular_name'     => _x('Tipo de Evento', 'taxonomy singular name'),
        'search_items'      => __('Buscar tipos'),
        'all_items'         => __('Todos los tipos'),
        'parent_item'       => __('Tipo padre'),
        'parent_item_colon' => __('Tipo padre:'),
        'edit_item'         => __('Editar tipo'),
        'update_item'       => __('Actualizar tipo'),
        'add_new_item'      => __('Añadir nuevo tipo'),
        'new_item_name'     => __('Nombre del nuevo tipo'),
        'menu_name'         => __('Tipos de Evento'),
    );
    
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'tipo-evento' ),
    );
    
    register_taxonomy('tipo_evento', array( 'eventos' ), $args);
}
add_action('init', 'amentum_eventos_taxonomy', 0);