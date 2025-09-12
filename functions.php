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

    // This theme uses wp_nav_menu() in multiple locations.
    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'amentum'),
            'menu-social' => esc_html__('Social', 'amentum'),
            'menu-legal' => esc_html__('Legal', 'amentum'),
            'menu-secondary' => esc_html__('Secondary Header', 'amentum'),
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
 * Funciones de formularios AJAX y procesamiento de datos.
 */
require get_template_directory() . '/inc/ajax-forms.php';

/**
 * Funciones de soporte para imágenes y archivos SVG.
 */
require get_template_directory() . '/inc/image-support.php';

/**
 * Estilos y funciones para el área de administración.
 */
require get_template_directory() . '/inc/admin-styles.php';

/**
 * Funciones utilitarias para posts y contenido.
 */
require get_template_directory() . '/inc/post-utils.php';

// Incluir post types personalizados
require get_template_directory() . '/inc/post-types.php';

/**
 * INCLUIR SISTEMA DE BLOQUES INDEPENDIENTE
 */
require_once get_template_directory() . '/inc/blocks-loader.php';
