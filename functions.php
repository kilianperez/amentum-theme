<?php
/**
 * Amentum functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Amentum
 */

if (! defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.F
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function amentumsetup()
{
    /*
        * Make theme available for translation.
        * Translations can be filed in the /languages/ directory.
        * If you're building a theme based on Amentum, use a find and replace
        * to change 'amentum' to the name of your theme in all the template files.
        */
    load_theme_textdomain('amentum', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
        * Let WordPress manage the document title.
        * By adding theme support, we declare that this theme does not use a
        * hard-coded <title> tag in the document head, and expect WordPress to
        * provide it for us.
        */
    add_theme_support('title-tag');

    /*
        * Enable support for Post Thumbnails on posts and pages.
        *
        * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
        */
    add_theme_support('post-thumbnails');

    // SOLUCIÓN CORRECTA: Soporte para estilos del editor y admin.css
    add_theme_support('editor-styles');
    add_editor_style('assets/dist/css/admin.css');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'amentum'),
            'menu-social' => esc_html__('Social', 'amentum'),
            'menu-legal' => esc_html__('Legal', 'amentum'),
        )
    );

    /*
        * Switch default core markup for search form, comment form, and comments
        * to output valid HTML5.
        */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'amentumcustom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'amentumsetup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function amentumcontent_width()
{
    $GLOBALS['content_width'] = apply_filters('amentumcontent_width', 640);
}
add_action('after_setup_theme', 'amentumcontent_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function amentumwidgets_init()
{
    register_sidebar(
        array(
            'name'          => esc_html__('Sidebar', 'amentum'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'amentum'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
// add_action( 'widgets_init', 'amentumwidgets_init' );

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/template-enqueued.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 */



function my_customizer_settings($wp_customize)
{
    // Añadir sección
    $wp_customize->add_section('my_custom_section', array(
      'title' => 'Configuración Amentum',
      'priority' => 30,
    ));
  
    // Añadir control de campo de texto
    $wp_customize->add_setting('amentum_copyright', array(
      'default' => '',
      'sanitize_callback' => 'sanitize_text_field',
    ));
  
    $wp_customize->add_control('amentum_copyright_control', array(
      'label' => 'Texto Copyright',
      'section' => 'my_custom_section',
      'settings' => 'amentum_copyright',
      'type' => 'text',
    ));

    // Añadir control de campo de texto
    $wp_customize->add_setting('amentum_newsletter', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        ));
    
    $wp_customize->add_control('amentum_newsletter_control', array(
        'label' => 'Texto Newsletter',
        'section' => 'my_custom_section',
        'settings' => 'amentum_newsletter',
        'type' => 'text',
    ));
    /*
    // Añadir control de selector de páginas URL
    $wp_customize->add_setting('amentum_url_privacidad', array(
      'default' => '',
      'sanitize_callback' => 'esc_url',
    ));

    $wp_customize->add_control(new WP_Customize_Control(
      $wp_customize,
      'amentum_url_privacidad_control',
      array(
        'label' => 'URL de la Página',
        'section' => 'my_custom_section',
        'settings' => 'amentum_url_privacidad',
        'type' => 'dropdown-pages',
      )
    ));
    */
    // Añadir control de correo electrónico
    $wp_customize->add_setting('amentum_email', array(
      'default' => '',
      'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'amentum_email_control',
        array(
        'label' => 'Email correos',
        'section' => 'my_custom_section',
        'settings' => 'amentum_email',
        'type' => 'email',
        )
    ));

    // Añadir control de logo footer en la sección "Identidad del sitio"
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
}
add_action('customize_register', 'my_customizer_settings');
  



// Register Custom Post Type
function custom_post_type()
{

    // Post type Proyectos
    $labels = array(
        'name'                  => _x('Proyectos', 'amentum-text'),
        'singular_name'         => _x('Proyecto', 'amentum-text'),
        'menu_name'             => __('Proyectos', 'amentum-text'),
        'name_admin_bar'        => __('Proyectos', 'amentum-text'),
        'archives'              => __('Archivos de proyectos', 'amentum-text'),
        'attributes'            => __('Atributos de proyectos', 'amentum-text'),
        'parent_item_colon'     => __('Proyectos padre', 'amentum-text'),
        'all_items'             => __('Todos los proyectos', 'amentum-text'),
        'add_new_item'          => __('Añadir nuevo proyecto', 'amentum-text'),
        'add_new'               => __('Añadir nuevo', 'amentum-text'),
        'new_item'              => __('Nuevo proyecto', 'amentum-text'),
        'edit_item'             => __('Editar proyecto', 'amentum-text'),
        'update_item'           => __('Actualizar proyecto', 'amentum-text'),
        'view_item'             => __('Ver proyecto', 'amentum-text'),
        'view_items'            => __('Ver proyectos', 'amentum-text'),
        'search_items'          => __('Buscar proyecto', 'amentum-text'),
        'not_found'             => __('No se han encontrado proyectos', 'amentum-text'),
        'not_found_in_trash'    => __('No se han encontrado proyectos en la papelera', 'amentum-text'),
        'featured_image'        => __('Imagen destacada', 'amentum-text'),
        'set_featured_image'    => __('Establecer imagen destacada', 'amentum-text'),
        'remove_featured_image' => __('Eliminar imagen destacada', 'amentum-text'),
        'use_featured_image'    => __('Usar como imagen destacada', 'amentum-text'),
        'insert_into_item'      => __('Insertar en proyecto', 'amentum-text'),
        'uploaded_to_this_item' => __('Subir proyecto', 'amentum-text'),
        'items_list'            => __('Lista de proyectos', 'amentum-text'),
        'items_list_navigation' => __('Navegar en los proyectos', 'amentum-text'),
        'filter_items_list'     => __('Filtrar proyectos', 'amentum-text'),
    );
    $rewrite = array(
        'slug'                  => 'projects',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __('Proyecto', 'amentum-text'),
        'description'           => __('Añade y gestiona todos tus proyectos', 'amentum-text'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'            => array(  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-art',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type('projects', $args);


     // Post type Merch
     $labels = array(
        'name'                  => _x('Merch', 'amentum-text'),
        'singular_name'         => _x('Merch', 'amentum-text'),
        'menu_name'             => __('Merch', 'amentum-text'),
        'name_admin_bar'        => __('Merch', 'amentum-text'),
        'archives'              => __('Archivos de merch', 'amentum-text'),
        'attributes'            => __('Atributos de merch', 'amentum-text'),
        'parent_item_colon'     => __('Merch padre', 'amentum-text'),
        'all_items'             => __('Todos los merch', 'amentum-text'),
        'add_new_item'          => __('Añadir nuevo merch', 'amentum-text'),
        'add_new'               => __('Añadir nuevo', 'amentum-text'),
        'new_item'              => __('Nuevo merch', 'amentum-text'),
        'edit_item'             => __('Editar merch', 'amentum-text'),
        'update_item'           => __('Actualizar merch', 'amentum-text'),
        'view_item'             => __('Ver merch', 'amentum-text'),
        'view_items'            => __('Ver merch', 'amentum-text'),
        'search_items'          => __('Buscar merch', 'amentum-text'),
        'not_found'             => __('No se han encontrado merch', 'amentum-text'),
        'not_found_in_trash'    => __('No se han encontrado merch en la papelera', 'amentum-text'),
        'featured_image'        => __('Imagen destacada', 'amentum-text'),
        'set_featured_image'    => __('Establecer imagen destacada', 'amentum-text'),
        'remove_featured_image' => __('Eliminar imagen destacada', 'amentum-text'),
        'use_featured_image'    => __('Usar como imagen destacada', 'amentum-text'),
        'insert_into_item'      => __('Insertar en merch', 'amentum-text'),
        'uploaded_to_this_item' => __('Subir merch', 'amentum-text'),
        'items_list'            => __('Lista de merch', 'amentum-text'),
        'items_list_navigation' => __('Navegar en los merch', 'amentum-text'),
        'filter_items_list'     => __('Filtrar merch', 'amentum-text'),
    );
    $rewrite = array(
        'slug'                  => 'merch',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __('Merch', 'amentum-text'),
        'description'           => __('Añade y gestiona todos tus merch', 'amentum-text'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'            => array(  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-products',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type('merch', $args);

    // Post type Magazine
    $labels = array(
        'name'                  => _x('Magazine', 'amentum-text'),
        'singular_name'         => _x('Magazine', 'amentum-text'),
        'menu_name'             => __('Magazine', 'amentum-text'),
        'name_admin_bar'        => __('Magazine', 'amentum-text'),
        'archives'              => __('Archivos de magazine', 'amentum-text'),
        'attributes'            => __('Atributos de magazine', 'amentum-text'),
        'parent_item_colon'     => __('Magazine padre', 'amentum-text'),
        'all_items'             => __('Todos los magazine', 'amentum-text'),
        'add_new_item'          => __('Añadir nuevo magazine', 'amentum-text'),
        'add_new'               => __('Añadir nuevo', 'amentum-text'),
        'new_item'              => __('Nuevo magazine', 'amentum-text'),
        'edit_item'             => __('Editar magazine', 'amentum-text'),
        'update_item'           => __('Actualizar magazine', 'amentum-text'),
        'view_item'             => __('Ver magazine', 'amentum-text'),
        'view_items'            => __('Ver magazine', 'amentum-text'),
        'search_items'          => __('Buscar magazine', 'amentum-text'),
        'not_found'             => __('No se han encontrado magazine', 'amentum-text'),
        'not_found_in_trash'    => __('No se han encontrado magazine en la papelera', 'amentum-text'),
        'featured_image'        => __('Imagen destacada', 'amentum-text'),
        'set_featured_image'    => __('Establecer imagen destacada', 'amentum-text'),
        'remove_featured_image' => __('Eliminar imagen destacada', 'amentum-text'),
        'use_featured_image'    => __('Usar como imagen destacada', 'amentum-text'),
        'insert_into_item'      => __('Insertar en magazine', 'amentum-text'),
        'uploaded_to_this_item' => __('Subir magazine', 'amentum-text'),
        'items_list'            => __('Lista de magazine', 'amentum-text'),
        'items_list_navigation' => __('Navegar en los magazine', 'amentum-text'),
        'filter_items_list'     => __('Filtrar magazine', 'amentum-text'),
    );
    $rewrite = array(
        'slug'                  => 'magazine',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __('Magazine', 'amentum-text'),
        'description'           => __('Añade y gestiona todos tus magazine', 'amentum-text'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'            => array(  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-media-document',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type('magazine', $args);
    

    /*

    // Post type Equipo
    $labels = array(
        'name'                  => _x( 'Equipo', 'amentum-text' ),
        'singular_name'         => _x( 'Equipo'	, 'amentum-text' ),
        'menu_name'             => __( 'Equipo', 'amentum-text' ),
        'name_admin_bar'        => __( 'Equipo', 'amentum-text' ),
        'archives'              => __( 'Archivos de integrantes', 'amentum-text' ),
        'attributes'            => __( 'Atributos de integrantes', 'amentum-text' ),
        'parent_item_colon'     => __( 'Equipo padre', 'amentum-text' ),
        'all_items'             => __( 'Todos los integrantes', 'amentum-text' ),
        'add_new_item'          => __( 'Añadir integrante', 'amentum-text' ),
        'add_new'               => __( 'Añadir integrante', 'amentum-text' ),
        'new_item'              => __( 'Nuevo integrante', 'amentum-text' ),
        'edit_item'             => __( 'Editar integrante', 'amentum-text' ),
        'update_item'           => __( 'Actualizar integrante', 'amentum-text' ),
        'view_item'             => __( 'Ver integrante', 'amentum-text' ),
        'view_items'            => __( 'Ver integrantes', 'amentum-text' ),
        'search_items'          => __( 'Buscar integrante', 'amentum-text' ),
        'not_found'             => __( 'No se han encontrado integrantes', 'amentum-text' ),
        'not_found_in_trash'    => __( 'No se han encontrado integrantes en la papelera', 'amentum-text' ),
        'featured_image'        => __( 'Imagen destacada', 'amentum-text' ),
        'set_featured_image'    => __( 'Establecer imagen destacada', 'amentum-text' ),
        'remove_featured_image' => __( 'Eliminar imagen destacada', 'amentum-text' ),
        'use_featured_image'    => __( 'Usar como imagen destacada', 'amentum-text' ),
        'insert_into_item'      => __( 'Insertar en integrante', 'amentum-text' ),
        'uploaded_to_this_item' => __( 'Subir integrante', 'amentum-text' ),
        'items_list'            => __( 'Lista de integrantes', 'amentum-text' ),
        'items_list_navigation' => __( 'Navegar en los integrantes', 'amentum-text' ),
        'filter_items_list'     => __( 'Filtrar integrantes', 'amentum-text' ),
    );
    $rewrite = array(
        'slug'                  => 'equipo',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Equipo', 'amentum-text' ),
        'description'           => __( 'Añade y gestiona todos tus equipos', 'amentum-text' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'            => array(  ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-portfolio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
        'menu_order'			=> true
    );
    register_post_type( 'equipo', $args );
    */
}
add_action('init', 'custom_post_type', 0);
/*
// redirect single posts to the archive page, scrolled to current ID

function amentum_redirecion_equipo() {
    if ( is_singular('equipo') ) {
        global $post;
        $redirect_link = get_post_type_archive_link( 'equipo' );
        wp_safe_redirect( $redirect_link, 302 );
        exit;
    }
}

add_action( 'template_redirect', 'amentum_redirecion_equipo');
*/
// Register Custom Taxonomy
function amentum_taxonomy()
{

    $labels = array(
        'name'              => _x('Tipos', 'taxonomy general name'),
        'singular_name'     => _x('Tipo', 'taxonomy singular name'),
        'search_items'      => __('Buscar tipos'),
        'all_items'         => __('Todos los tipos'),
        'parent_item'       => __('Tipo padre'),
        'parent_item_colon' => __('Tipo padre:'),
        'edit_item'         => __('Editar tipo'),
        'update_item'       => __('Actualizar tipo'),
        'add_new_item'      => __('Añadir nuevo tipo'),
        'new_item_name'     => __('Nombre del nuevo tipo'),
        'menu_name'         => __('Tipos'),
    );
    
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'tipo' ),
    );
    
    register_taxonomy('tipo', array( 'projects' ), $args);

}
add_action('init', 'amentum_taxonomy', 0);

// Enqueue javascript file
add_action('wp_enqueue_scripts', 'amentum_insert_custom_js');
function amentum_insert_custom_js()
{
    wp_localize_script(
        'all',
        'ajax_forms',
        [ 'ajaxUrl'=>admin_url('admin-ajax.php'),
          'frmNonce' => wp_create_nonce('secret-key-form'),
          'isUserLoggedIn' => is_user_logged_in() ? 'true' : 'false'
        ]
    );
}

// Create the contact form
add_filter('the_content', 'amentum_show_contact_ajax_form');
function amentum_show_contact_ajax_form($content)
{
    ob_start();
    ?>

<?php
    $htm_form = ob_get_contents();
    ob_end_clean();

    return $content . $htm_form;
}

// Process ajax request
add_action('wp_ajax_nopriv_amentum_ajax_frm_contact', 'amentum_process_contact_form');
add_action('wp_ajax_amentum_ajax_frm_contact', 'amentum_process_contact_form');
function amentum_process_contact_form()
{
    function sanitize_field($value, $type)
    {
        switch ($type) {
            case 'date':
            case 'text':
            case 'select':
            case 'tags':
                return sanitize_text_field($value);
            case 'titulo_seccion':
                return ''; // Los títulos de sección no se envían
            case 'email':
                return sanitize_email($value);
            case 'textarea':
                return sanitize_textarea_field($value);
            case 'phone':
            case 'url':
                return esc_url($value);
            case 'checkbox':
                return in_array($value, ['on', 'true', '1']) ? 'Sí' : 'No';
            default:
                return sanitize_text_field($value);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nonce = $_POST['nonce'] ?? '';
        amentum_validate_nonce($nonce, 'secret-key-form');

        $sanitize_data = [];
        $sanitize_email = '';
        $sanitize_name = '';

        foreach ($_POST as $key => $field) {
            // Manejar campos dinámicos con estructura de Liberta
            if (isset($field['type']) && isset($field['value'])) {
                $value = $field['value'];
                $type = $field['type'];
                $placeholder = $field['placeholder'] ?? $key;
                $option = $field['option'] ?? $type;

                // Identificar email y nombre para headers
                if ($type == 'email') {
                    $sanitize_email = $value;
                }
                if (strpos($key, 'nombre') !== false || $option == 'name') {
                    $sanitize_name = $value;
                }

                // Sanitizar y almacenar
                $sanitized_value = sanitize_field($value, $type);
                $sanitize_data[$key] = [
                    'value' => $sanitized_value,
                    'type' => $type,
                    'placeholder' => $placeholder,
                    'option' => $option
                ];
            }
            // Manejar campos simples (compatibilidad hacia atrás)
            elseif (is_string($field) && !in_array($key, ['nonce', 'action'])) {
                $sanitized_value = sanitize_text_field($field);
                
                // Detectar tipo por nombre del campo
                $type = 'text';
                if (strpos($key, 'email') !== false) {
                    $type = 'email';
                    $sanitized_value = sanitize_email($field);
                    $sanitize_email = $sanitized_value;
                }
                if (strpos($key, 'mensaje') !== false) {
                    $type = 'textarea';
                    $sanitized_value = sanitize_textarea_field($field);
                }
                if (strpos($key, 'nombre') !== false) {
                    $sanitize_name = $sanitized_value;
                }

                $sanitize_data[$key] = [
                    'value' => $sanitized_value,
                    'type' => $type,
                    'placeholder' => ucfirst($key),
                    'option' => $type
                ];
            }
            // Manejar arrays (para campos tags)
            elseif (is_array($field)) {
                $sanitized_values = array_map('sanitize_text_field', $field);
                $sanitize_data[$key] = [
                    'value' => implode(', ', $sanitized_values),
                    'type' => 'tags',
                    'placeholder' => ucfirst($key),
                    'option' => 'tags'
                ];
            }
        }

        // Usar email configurado en customizer o fallback
        $admin_email = get_theme_mod('amentum_email') ?: 'hello@amentum.com';
        $subject = 'Formulario de contacto - Amentum';
        $headers = "Reply-to: " . $sanitize_name . " <" . $sanitize_email . ">";

        // Construir mensaje
        $msg = '';
        foreach ($sanitize_data as $field) {
            if (!empty($field['value'])) {
                $msg .= $field['placeholder'] . ": " . $field['value'] . "\n";
            }
        }


        // Enviar email
        $sent = wp_mail($admin_email, $subject, $msg, $headers);

        // Procesar newsletter si está habilitada
        if (isset($_POST['newsletter']) && ($_POST['newsletter'] === 'on' || $_POST['newsletter'] === '1')) {
            amentum_mailchimp();
        }

        // Respuesta más detallada
        if ($sent) {
            $res = ['status' => 1, 'message' => 'Se envió correctamente el formulario'];
        } else {
            // Obtener más información sobre el error
            $error_info = '';
            if (function_exists('error_get_last')) {
                $last_error = error_get_last();
                if ($last_error) {
                    $error_info = ' | Last PHP Error: ' . $last_error['message'];
                }
            }
            
            error_log('AMENTUM: wp_mail FAILED - checking mail configuration');
            $res = ['status' => 0, 'message' => 'Hubo un error en el envío (wp_mail)' . $error_info];
        }

        wp_send_json($res);
    }
}

function amentum_validate_nonce($nonce, $nonce_name)
{
    if (! wp_verify_nonce($nonce, $nonce_name)) {
        $res = [ 'status' => 0, 'message' => '✋ Error nonce validation' ];
        wp_send_json($res);
    }
}
/**
 * @link https://angelcruz.dev/post/como-usar-de-forma-sencilla-mailchimp-en-wordpress
 * @link https://rudrastyh.com/wordpress/using-mailchimp-api.html
 */
// amentum_mailchimp();
function amentum_mailchimp()
{

    if (! empty($_POST)) {

        $email = $_POST['email'];
		$nombre = $_POST['nombre'] ?? ''; 
		$tipo = $_POST['tipo'] ?? ''; 
        $api = 'eb664283d682fbb4b1c3c1584258a2c9-us13';
        $list_id = 'e5895892ea';
        $subscription_page = $_POST['pagina'];
        // URL para suscribir al usuario
        $subscribe_url = 'https://' . substr($api, strpos($api, '-')+1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($email));

        // Datos del campo personalizado
        $merge_fields = array(
            'PAGINA' => $subscription_page,
			'FNAME' => $nombre,
			'TIPO' => $tipo
        );

        // Datos de la suscripción
        $subscription_data = array(
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => $merge_fields
        );

        // Realizar la solicitud para suscribir al usuario
        $response = wp_remote_request(
            $subscribe_url,
            array(
                'method' => 'PUT',
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode('user:' . $api)
                ),
                'body' => json_encode($subscription_data)
            )
        );

        // if('OK' === wp_remote_retrieve_response_message($response)) {
        //     echo 'The user has been successfully subscribed.';
        // }
    }
}


// Función para agregar un tamaño de imagen personalizado
function amentum_tamano_personalizado() {
    add_image_size('card_image', 600, 900, true); // 'nombre_tamano' es un nombre descriptivo, 300 es el ancho, 200 es la altura, y true indica si se debe recortar la imagen
}

// Engancha la función a la acción 'after_setup_theme'
add_action('after_setup_theme', 'amentum_tamano_personalizado');

// Habilitar soporte para archivos SVG
function amentum_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'amentum_mime_types');

// Arreglar la visualización de SVG en el Media Library
function amentum_fix_svg_thumb_display($response, $attachment, $meta) {
    if($response['mime'] === 'image/svg+xml' && empty($response['sizes'])) {
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

// Obtener dimensiones de SVG
function amentum_get_svg_dimensions($svg_path) {
    $svg = @file_get_contents($svg_path);
    $xml = @simplexml_load_string($svg);
    $width = 0;
    $height = 0;
    
    if($xml) {
        $attributes = $xml->attributes();
        $width = (int) $attributes->width;
        $height = (int) $attributes->height;
        
        if(!$width && !$height && $attributes->viewBox) {
            $viewBox = explode(' ', $attributes->viewBox);
            $width = (int) $viewBox[2];
            $height = (int) $viewBox[3];
        }
    }
    
    return array('width' => $width ?: 150, 'height' => $height ?: 150);
}

/**
 * Suprimir algunos warnings de WordPress
 */
if (!function_exists('amentum_suppress_doing_it_wrong')) {
    function amentum_suppress_doing_it_wrong($trigger, $function, $message, $version) {
        // Suprimir algunos warnings específicos si es necesario
        return $trigger;
    }
    add_filter('doing_it_wrong_trigger_error', 'amentum_suppress_doing_it_wrong', 10, 4);
}

// CSS personalizado para iconos de post types
add_action('admin_head', 'custom_post_type_icons_css');
function custom_post_type_icons_css() {
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
 * INCLUIR SISTEMA DE BLOQUES INDEPENDIENTE
 */
require_once get_template_directory() . '/inc/blocks-loader.php';
