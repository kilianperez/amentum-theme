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

// Post type Formularios
function amentum_formularios_post_type()
{
    $labels = array(
        'name'                  => _x('Formularios', 'amentum-text'),
        'singular_name'         => _x('Formulario', 'amentum-text'),
        'menu_name'             => __('Formularios', 'amentum-text'),
        'name_admin_bar'        => __('Formulario', 'amentum-text'),
        'archives'              => __('Archivos de formularios', 'amentum-text'),
        'attributes'            => __('Atributos de formularios', 'amentum-text'),
        'parent_item_colon'     => __('Formulario padre', 'amentum-text'),
        'all_items'             => __('Todos los formularios', 'amentum-text'),
        'add_new_item'          => __('Añadir nuevo formulario', 'amentum-text'),
        'add_new'               => __('Añadir nuevo', 'amentum-text'),
        'new_item'              => __('Nuevo formulario', 'amentum-text'),
        'edit_item'             => __('Editar formulario', 'amentum-text'),
        'update_item'           => __('Actualizar formulario', 'amentum-text'),
        'view_item'             => __('Ver formulario', 'amentum-text'),
        'view_items'            => __('Ver formularios', 'amentum-text'),
        'search_items'          => __('Buscar formulario', 'amentum-text'),
        'not_found'             => __('No se han encontrado formularios', 'amentum-text'),
        'not_found_in_trash'    => __('No se han encontrado formularios en la papelera', 'amentum-text'),
        'featured_image'        => __('Imagen destacada', 'amentum-text'),
        'set_featured_image'    => __('Establecer imagen destacada', 'amentum-text'),
        'remove_featured_image' => __('Eliminar imagen destacada', 'amentum-text'),
        'use_featured_image'    => __('Usar como imagen destacada', 'amentum-text'),
        'insert_into_item'      => __('Insertar en formulario', 'amentum-text'),
        'uploaded_to_this_item' => __('Subir formulario', 'amentum-text'),
        'items_list'            => __('Lista de formularios', 'amentum-text'),
        'items_list_navigation' => __('Navegar en los formularios', 'amentum-text'),
        'filter_items_list'     => __('Filtrar formularios', 'amentum-text'),
    );
    
    $args = array(
        'label'                 => __('Formulario', 'amentum-text'),
        'description'           => __('Crea y gestiona formularios personalizados', 'amentum-text'),
        'labels'                => $labels,
        'supports'              => array('title'),
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-feedback',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    
    register_post_type('formularios', $args);
}
add_action('init', 'amentum_formularios_post_type', 0);

/**
 * Personalizar las columnas del listado de formularios
 */
function amentum_formularios_columns($columns)
{
    // Remover columnas que no necesitamos
    unset($columns['date']);

    // Agregar nuestras columnas personalizadas
    $columns['shortcode'] = 'Shortcode';
    $columns['campos_count'] = 'Campos';
    $columns['status'] = 'Estado';
    $columns['date'] = 'Fecha'; // Volver a agregar la fecha al final

    return $columns;
}
add_filter('manage_formularios_posts_columns', 'amentum_formularios_columns');

/**
 * Mostrar contenido de las columnas personalizadas
 */
function amentum_formularios_column_content($column, $post_id)
{
    switch ($column) {
        case 'shortcode':
            echo '<code class="amentum-shortcode-copy" onclick="amentumCopyShortcode(this)" style="cursor: pointer; background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 12px; user-select: all;" title="Clic para copiar">[amentum_formulario id="' . $post_id . '"]</code>';
            break;

        case 'campos_count':
            $config = get_post_meta($post_id, '_amentum_formulario_config', true);
            $campos = isset($config['campos']) ? $config['campos'] : array();
            $count = count($campos);

            if ($count === 0) {
                echo '<span style="color: #d63638;">Sin campos</span>';
            } else {
                echo '<span style="color: #00a32a;"><strong>' . $count . '</strong> campos</span>';
            }
            break;

        case 'status':
            $config = get_post_meta($post_id, '_amentum_formulario_config', true);
            $campos = isset($config['campos']) ? $config['campos'] : array();
            $email_destino = isset($config['email_destino']) ? $config['email_destino'] : '';

            if (empty($campos)) {
                echo '<span style="color: #d63638;">⚠️ Sin configurar</span>';
            } elseif (empty($email_destino)) {
                echo '<span style="color: #dba617;">⚠️ Sin email</span>';
            } else {
                echo '<span style="color: #00a32a;">✅ Listo</span>';
            }
            break;
    }
}
add_action('manage_formularios_posts_custom_column', 'amentum_formularios_column_content', 10, 2);

/**
 * Hacer que algunas columnas sean ordenables
 */
function amentum_formularios_sortable_columns($columns)
{
    $columns['campos_count'] = 'campos_count';
    return $columns;
}
add_filter('manage_edit-formularios_sortable_columns', 'amentum_formularios_sortable_columns');

/**
 * JavaScript para copiar shortcode al clipboard
 */
function amentum_formularios_admin_scripts($hook)
{
    global $post_type;

    if ($hook === 'edit.php' && $post_type === 'formularios') {
        ?>
        <script type="text/javascript">
        function amentumCopyShortcode(element) {
            const text = element.textContent;

            // Usar la API moderna de clipboard si está disponible
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    amentumShowCopyMessage(element, '¡Copiado!');
                }).catch(function() {
                    amentumFallbackCopy(text, element);
                });
            } else {
                amentumFallbackCopy(text, element);
            }
        }

        function amentumFallbackCopy(text, element) {
            // Método de respaldo para navegadores más antiguos
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                amentumShowCopyMessage(element, '¡Copiado!');
            } catch (err) {
                amentumShowCopyMessage(element, 'Error al copiar');
            } finally {
                document.body.removeChild(textArea);
            }
        }

        function amentumShowCopyMessage(element, message) {
            const originalText = element.textContent;
            const originalStyle = element.style.backgroundColor;

            element.textContent = message;
            element.style.backgroundColor = '#00a32a';
            element.style.color = 'white';

            setTimeout(function() {
                element.textContent = originalText;
                element.style.backgroundColor = originalStyle;
                element.style.color = '';
            }, 1500);
        }
        </script>

        <style type="text/css">
        .amentum-shortcode-copy:hover {
            background: #2271b1 !important;
            color: white !important;
        }

        .column-shortcode {
            width: 280px;
        }

        .column-campos_count {
            width: 80px;
            text-align: center;
        }

        .column-status {
            width: 100px;
            text-align: center;
        }
        </style>
        <?php
    }
}
add_action('admin_footer', 'amentum_formularios_admin_scripts');