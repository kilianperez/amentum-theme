# Bloques Gutenberg Personalizados - Theme Amentum

## 📋 Arquitectura de Bloques

### Estructura Modular Obligatoria

Cada bloque personalizado debe seguir esta estructura exacta en el directorio `blocks/`:

```text

blocks/
└── nombre-bloque/
    ├── block.php      # Registro y renderizado server-side
    ├── editor.js      # Editor de Gutenberg (React)
    ├── style.css      # Estilos frontend y editor
    └── script.js      # JavaScript frontend (opcional)

```text

### Archivos Estándar de Bloques

- **`block.php`**: Registro y renderizado server-side (obligatorio)
- **`editor.js`**: Editor de Gutenberg con React (obligatorio)
- **`style.css`**: Estilos frontend y editor (obligatorio)
- **`script.js`**: JavaScript frontend para interactividad (opcional)

### Convenciones de Naming

- **Directorio:** `kebab-case` (ejemplo: `hero-full`, `servicios-columnas`)
- **Namespace:** `amentum/nombre-bloque`
- **Funciones PHP:** `amentum_register_[nombre]_block()`, `amentum_render_[nombre]_block()`
- **CSS Classes:** `.block-nombre-bloque`

---

## 🔧 Archivo block.php - Estructura Obligatoria

### Template Base

```php

<?php
/**

 * [Nombre] Block - [Descripción breve]

 *

 * @package Amentum

 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**

 * Registrar el bloque [Nombre]

 */
function amentum_register_[nombre]_block() {
    register_block_type('amentum/nombre-bloque', array(
        'render_callback' => 'amentum_render_[nombre]_block',
        'attributes' => array(
            // Definir atributos aquí
        )
    ));
}
add_action('init', 'amentum_register_[nombre]_block');

/**

 * Renderizar el bloque [Nombre]

 */
function amentum_render_[nombre]_block($attributes) {
    // Procesar atributos con defaults y sanitización

    ob_start();
    ?>
    <section class="block-nombre-bloque">
        <!-- HTML del bloque -->
    </section>
    <?php
    return ob_get_clean();
}

/**

 * ⚡ SISTEMA OPTIMIZADO DE CSS COMPILADO ⚡
 * Los estilos del bloque se cargan automáticamente desde blocks.css
 * Un solo archivo minificado para TODOS los bloques = mejor SEO

 */
// NOTA: No es necesario enqueue individual de CSS
// Los estilos se compilan automáticamente con: npm run build

/**

 * Enqueue editor assets para [Nombre] block (Backend)

 */
function amentum_enqueue_[nombre]_block_editor_assets() {
    // Solo JS del editor - CSS se carga desde blocks.css compilado
    wp_enqueue_script(
        'amentum-nombre-bloque-editor',
        get_template_directory_uri() . '/blocks/nombre-bloque/editor.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        wp_get_theme()->get('Version'),
        true
    );

    // CSS del editor YA incluido en blocks.css - NO duplicar
    // wp_enqueue_style(
    //     'amentum-nombre-bloque-editor-style',
    //     get_template_directory_uri() . '/blocks/nombre-bloque/style.css',
    //     array(),
    //     wp_get_theme()->get('Version')
    // );
}
add_action('enqueue_block_editor_assets', 'amentum_enqueue_[nombre]_block_editor_assets');

```text

### Patrones de Atributos Comunes

```php

'attributes' => array(
    // Textos editables
    'titulo' => array(
        'type' => 'string',
        'default' => 'Título por defecto'
    ),
    'contenido' => array(
        'type' => 'string',
        'default' => 'Contenido por defecto'
    ),

    // Arrays de elementos
    'elementos' => array(
        'type' => 'array',
        'default' => array(
            array('titulo' => 'Item 1', 'contenido' => 'Descripción 1'),
            array('titulo' => 'Item 2', 'contenido' => 'Descripción 2')
        )
    ),

    // Configuración numérica
    'columnasPorFila' => array(
        'type' => 'number',
        'default' => 3
    ),

    // URLs y enlaces
    'backgroundImage' => array(
        'type' => 'string',
        'default' => ''
    ),
    'ctaUrl' => array(
        'type' => 'string',
        'default' => '#'
    ),

    // Colores personalizables
    'backgroundColor' => array(
        'type' => 'string',
        'default' => '#f8f6f3'
    ),
    'textColor' => array(
        'type' => 'string',
        'default' => '#333333'
    )
)

```text

### Sanitización de Datos OBLIGATORIA

```php

function amentum_render_[nombre]_block($attributes) {
    // SIEMPRE sanitizar datos de entrada
    $titulo = !empty($attributes['titulo']) ? esc_html($attributes['titulo']) : 'Default';
    $contenido = !empty($attributes['contenido']) ? wp_kses_post($attributes['contenido']) : '';
    $url = !empty($attributes['url']) ? esc_url($attributes['url']) : '#';
    $background_color = !empty($attributes['backgroundColor']) ? esc_attr($attributes['backgroundColor']) : '#f8f6f3';

    // Para arrays, validar cada elemento
    $elementos = !empty($attributes['elementos']) ? $attributes['elementos'] : array();

    ob_start();
    // ... resto del código
}

```text

---

## ⚛️ Archivo editor.js - Estructura React

### Template Base

```javascript

/**

 * [Nombre] Block - Editor JavaScript
 * [Descripción de funcionalidades específicas]

 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, RichText, ColorPalette } = wp.blockEditor;
    const { PanelBody, Button, RangeControl, TextControl } = wp.components;
    const { createElement: e, Fragment } = wp.element;

    registerBlockType('amentum/nombre-bloque', {
        title: '[Título del Bloque]',
        icon: 'admin-appearance', // O icon apropiado
        category: 'amentum-blocks',
        attributes: {
            // Copiar exactamente desde block.php
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { atributo1, atributo2 } = attributes;

            return e(Fragment, {}, [
                // Inspector Controls (Sidebar) - SOLO para configuración técnica
                e(InspectorControls, {}, [
                    e(PanelBody, { title: 'Configuración', initialOpen: true }, [
                        // Controles técnicos únicamente
                    ])
                ]),

                // Block Content Preview - Edición directa
                e('div', {
                    className: 'bloque-preview',
                    style: { /* estilos del preview */ }
                }, [
                    // Contenido editable directamente
                    e(RichText, {
                        tagName: 'h2',
                        value: atributo1,
                        onChange: (value) => setAttributes({ atributo1: value }),
                        placeholder: 'Escribe aquí...'
                    })
                ])
            ]);
        },

        save: function() {
            // Renderizado server-side, siempre return null
            return null;
        }
    });
})();

```text

### Principios de UX del Editor

1. **Sidebar SOLO para configuración técnica**: colores, columnas, URLs
2. **Edición directa en el bloque**: textos, títulos, contenido
3. **Preview realista**: el editor debe mostrar cómo se verá en frontend
4. **Controles intuitivos**: botones claros para añadir/eliminar elementos

### Componentes React Comunes

```javascript

// RichText para textos editables
e(RichText, {
    tagName: 'h2',
    value: titulo,
    onChange: (value) => setAttributes({ titulo: value }),
    placeholder: 'Título...',
    style: { /* estilos */ }
})

// RangeControl para números
e(RangeControl, {
    label: 'Columnas por fila',
    value: columnasPorFila,
    onChange: (value) => setAttributes({ columnasPorFila: value }),
    min: 1,
    max: 6
})

// Button para acciones
e(Button, {
    isPrimary: true,
    onClick: addElemento,
    style: { marginBottom: '15px' }
}, '+ Añadir Elemento')

// ColorPalette para colores
e(ColorPalette, {
    colors: [
        { name: 'Beige Claro', color: '#f8f6f3' },
        { name: 'Blanco', color: '#ffffff' }
    ],
    value: backgroundColor,
    onChange: (value) => setAttributes({ backgroundColor: value || '#f8f6f3' })
})

```text

---

## 🎨 Archivo style.css - Estructura CSS

### Template Base

```css

/**

 * [Nombre] Block Styles - [Descripción del sistema de layout]

 */

/* Contenedor principal */
.block-nombre-bloque {
    padding: 80px 0;
    background-color: #f8f6f3;
    position: relative;
}

.block-nombre-bloque .container {
    margin: 0 auto;
    padding: 0 20px;
}

/* Elementos principales del bloque */
.bloque-content {
    animation: fadeInUp 0.8s ease-out;
}

/* Sistema responsive OBLIGATORIO */
@media (max-width: 1024px) {
    .block-nombre-bloque {
        padding: 60px 0;
    }
}

@media (max-width: 768px) {
    .block-nombre-bloque {
        padding: 50px 0;
    }
}

@media (max-width: 480px) {
    .block-nombre-bloque .container {
        padding: 0 15px;
    }
}

/* Editor Styles - Para Gutenberg */
.wp-block-editor .bloque-preview {
    border: 2px dashed #ddd;
    min-height: 200px;
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .block-nombre-bloque {
        /* Estilos dark mode */
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

```text

### Sistema Flexbox para Columnas

```css

/* Grid flexbox con distribución exacta */
.elementos-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
}

/* Cálculos exactos de ancho - SIN max-width */
.elementos-grid[data-columns='1'] .elemento {
    flex: 0 0 100%;
}

.elementos-grid[data-columns='2'] .elemento {
    flex: 0 0 calc((100% - 30px) / 2);
}

.elementos-grid[data-columns='3'] .elemento {
    flex: 0 0 calc((100% - 60px) / 3);
}

.elementos-grid[data-columns='4'] .elemento {
    flex: 0 0 calc((100% - 90px) / 4);
}

/* Responsive - reducir columnas en pantallas pequeñas */
@media (max-width: 768px) {
    .elementos-grid[data-columns='3'] .elemento,
    .elementos-grid[data-columns='4'] .elemento {
        flex: 0 0 calc((100% - 30px) / 2);
    }
}

@media (max-width: 480px) {
    .elementos-grid .elemento {
        flex: 0 0 100%;
    }
}

```text

---

## 📜 Archivo script.js - JavaScript Frontend

### Template Base

```javascript

/**

 * [Nombre] Block - JavaScript Frontend
 * Maneja la interactividad del bloque en el frontend

 */

document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los bloques de este tipo en la página
    const bloqueElements = document.querySelectorAll('.block-nombre-bloque');

    if (bloqueElements.length === 0) {
        return; // No hay bloques de este tipo, salir
    }

    // Verificar dependencias si son necesarias
    if (typeof DependenciaRequerida === 'undefined') {
        console.error('Dependencia no disponible para el bloque nombre-bloque');
        return;
    }

    // Inicializar cada instancia del bloque
    bloqueElements.forEach(function(bloqueElement) {
        inicializarBloque(bloqueElement);
    });
});

function inicializarBloque(elemento) {
    // Lógica específica del bloque
    const config = {
        // Configuración del bloque
    };

    try {
        // Inicialización del bloque
        console.log('Bloque nombre-bloque inicializado correctamente');
    } catch (error) {
        console.error('Error inicializando bloque nombre-bloque:', error);
    }
}

```text

### Principios para script.js

1. **Solo JavaScript frontend**: No incluir código de editor/backend
2. **Múltiples instancias**: Manejar varios bloques del mismo tipo en una página
3. **Verificación de dependencias**: Comprobar librerías externas antes de usar
4. **Manejo de errores**: try/catch para evitar romper otros scripts
5. **Rendimiento**: Solo ejecutar si existen elementos del bloque en la página

### Compilación Automática

El archivo `script.js` se incluye automáticamente en la compilación:

- **Patrón de búsqueda**: `blocks/**/script.js`
- **Compilación**: Se añade a `all.js` y se minifica en `all.min.js`
- **Watch**: Los cambios activan recompilación automática con `npm run dev`

---

## 📝 Patrón de Desarrollo Completo

### 1. Planificación del Bloque

```markdown

- ¿Qué elementos necesita? (títulos, contenido, imágenes, etc.)
- ¿Cómo se distribuye visualmente? (columnas, filas, etc.)
- ¿Qué es configurable? (colores, columnas, URLs)
- ¿Qué se edita directamente? (textos principales)

```text

### 2. Crear Estructura de Archivos

```bash

mkdir blocks/nombre-bloque
touch blocks/nombre-bloque/block.php
touch blocks/nombre-bloque/editor.js
touch blocks/nombre-bloque/style.css
touch blocks/nombre-bloque/script.js  # Opcional: solo si necesita JS frontend

```text

### 3. Implementar block.php

- Definir atributos con defaults
- Crear función de renderizado con sanitización
- Implementar enqueue de assets
- Usar `ob_start()` y `ob_get_clean()`

### 4. Implementar editor.js

- Copiar atributos exactamente desde PHP
- Crear preview realista del bloque
- Controles técnicos en sidebar únicamente
- Edición directa con RichText

### 5. Implementar style.css

- Estilos base del bloque
- Sistema responsive obligatorio
- Estilos para editor (`.wp-block-editor`)
- Animaciones y estados hover

### 6. Implementar script.js (Opcional)

- JavaScript frontend para interactividad
- Manejo de múltiples instancias del bloque
- Verificación de dependencias externas
- Manejo de errores con try/catch

### 7. Compilar Recursos de Bloques

```bash

# Compilar todos los CSS de bloques en un archivo único

npm run build

```text

### 8. Testing y Refinamiento

```bash

# Testing básico

python3 logs/debug.py

# Verificar en WordPress admin

# Probar responsive design

# Verificar funcionamiento en frontend

# Validar que blocks.css se genera correctamente

```text

---

## ⚡ Sistema de CSS Optimizado para SEO

### 🎯 Problema Resuelto

**ANTES:** Cada bloque cargaba su CSS individual

- Multiple peticiones HTTP (malo para SEO)
- Renderizado bloqueante
- Archivos CSS no minificados
- Cache menos eficiente

**AHORA:** Un solo archivo CSS compilado para todos los bloques

- Una sola petición HTTP `blocks.css`
- Archivo minificado automáticamente
- Mejor puntuación en PageSpeed Insights
- Cache optimizado para todos los bloques

### 🔧 Configuración Automática

El sistema está configurado en `vite.config.js`:

```javascript

function blocksCss() {
    return src('blocks/**/style.css')
        .pipe(concat('blocks.css'))
        .pipe(cleanCss())
        .pipe(dest('assets/dist/css/'));
}

```text

Y se registra en `inc/template-enqueued.php`:

```php

// CSS compilado de todos los bloques - Optimización SEO
wp_enqueue_style('amentum-blocks',
    get_template_directory_uri() . '/assets/dist/css/blocks.css',
    array('all'),
    '1.0.0'
);

```text

### 🚀 Comandos de Compilación

```bash

# Build completo (recomendado)

npm run build

# Modo desarrollo con watch

npm run dev

```text

### ✅ Beneficios SEO

1. **Menos peticiones HTTP** - Una sola descarga vs múltiples
2. **CSS minificado** - Menor peso de página
3. **Cache eficiente** - Un archivo para todos los bloques
4. **PageSpeed mejor** - Puntuación más alta
5. **Mobile First** - Optimización responsive

---

## 🚀 Ejemplos de Bloques Implementados

### Hero Full - Bloque de Hero con Drop Cap

### Características

- Imagen de fondo personalizable
- Títulos editables con diferentes tamaños
- Drop cap automático en primer párrafo
- Sistema de overlay con colores

**Archivos:** `blocks/hero-full/`

### Servicios Columnas - Sistema Flexible de Elementos

### Características

- Elementos independientes de visualización
- Configuración de 1-6 columnas por fila
- Distribución proporcional sin max-width
- Sección CTA integrada
- Edición directa sin controles duplicados

**Archivos:** `blocks/servicios-columnas/`

---

## ⚠️ Errores Comunes a Evitar

### ❌ NO Hacer

- **Hardcodear contenido** en front-page.php o templates
- **Max-width en elementos** que deben ocupar ancho completo
- **Controles duplicados** en sidebar e inline
- **Olvidar sanitización** de datos de entrada
- **Estilos sin responsive** design
- **Atributos diferentes** entre PHP y JS

### ✅ SIEMPRE Hacer

- **Server-side rendering** con ob_start()
- **Sanitización completa** de todos los inputs
- **Sistema responsive** en todos los bloques
- **Preview realista** en el editor
- **Assets condicionales** con `has_block()`
- **Namespace consistente** `amentum/nombre-bloque`

---

## 🔧 Integración con Theme

### Registro Automático

El theme debe registrar automáticamente todos los bloques ubicados en `blocks/`. Verificar que `functions.php` incluya:

```php

// Auto-registrar bloques personalizados
function amentum_register_custom_blocks() {
    $blocks_dir = get_template_directory() . '/blocks/';
    $blocks = array_filter(glob($blocks_dir . '*'), 'is_dir');

    foreach ($blocks as $block_dir) {
        $block_php = $block_dir . '/block.php';
        if (file_exists($block_php)) {
            require_once $block_php;
        }
    }
}
add_action('after_setup_theme', 'amentum_register_custom_blocks');

// Categoría de bloques personalizada
function amentum_register_block_category($categories) {
    return array_merge($categories, array(
        array(
            'slug' => 'amentum-blocks',
            'title' => 'Amentum Blocks'
        )
    ));
}
add_filter('block_categories_all', 'amentum_register_block_category', 10, 2);

```text
---

## 📚 Recursos y Referencias

### WordPress Documentation

- [Block API Reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/)
- [Gutenberg Components](https://developer.wordpress.org/block-editor/reference-guides/components/)
- [Block Supports](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/)

### React/JavaScript

- [wp.element](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-element/)
- [wp.components](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-components/)
- [wp.blockEditor](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/)

### CSS y Responsive

- [Flexbox Guide](https://css-tricks.com/snippets/css/a-guide-to-flexbox/)
- [CSS calc() Function](https://developer.mozilla.org/en-US/docs/Web/CSS/calc)
- [Media Queries](https://developer.mozilla.org/en-US/docs/Web/CSS/Media_Queries)

---

## 🤖 Automatización: Inserción Programática de Bloques

### Crear Bloques en Páginas Específicas con WP-CLI

Para insertar bloques automáticamente en páginas concretas sin hacerlo manualmente desde el editor, utilizar los siguientes comandos WP-CLI:

#### 📋 Comandos Básicos de Inserción

```bash

# Insertar bloque al final de una página existente

docker compose exec wordpress-cli wp post update [POST_ID] --post_content="$(docker compose exec wordpress-cli wp post get [POST_ID] --field=content)

<!-- wp:amentum/nombre-bloque -->
<!-- /wp:amentum/nombre-bloque -->
"

# Ejemplo real - Insertar bloque eventos-swiper en página principal (ID=2)

docker compose exec wordpress-cli wp post update 2 --post_content="$(docker compose exec wordpress-cli wp post get 2 --field=content)

<!-- wp:amentum/eventos-swiper -->
<!-- /wp:amentum/eventos-swiper -->
"

```text
#### 🎯 Inserción con Configuración Específica

```bash

# Bloque con atributos personalizados (formato JSON)

docker compose exec wordpress-cli wp post update [POST_ID] --post_content="$(docker compose exec wordpress-cli wp post get [POST_ID] --field=content)

<!-- wp:amentum/nombre-bloque {\"atributo1\":\"valor1\",\"atributo2\":\"valor2\"} -->
<!-- /wp:amentum/nombre-bloque -->
"

# Ejemplo real - Bloque servicios-columnas con 4 columnas

docker compose exec wordpress-cli wp post update 2 --post_content="$(docker compose exec wordpress-cli wp post get 2 --field=content)

<!-- wp:amentum/servicios-columnas {\"columnasPorFila\":4} -->
<!-- /wp:amentum/servicios-columnas -->
"

```text
#### 📄 Crear Página Nueva con Bloque Incluido

```bash

# Crear página nueva con bloque desde el principio

docker compose exec wordpress-cli wp post create \
    --post_type=page \
    --post_title="Mi Nueva Página" \
    --post_status=publish \
    --post_content="<!-- wp:amentum/nombre-bloque -->
<!-- /wp:amentum/nombre-bloque -->"

# Ejemplo - Página de eventos con swiper incluido

docker compose exec wordpress-cli wp post create \
    --post_type=page \
    --post_title="Galería de Eventos" \
    --post_status=publish \
    --post_content="<!-- wp:amentum/eventos-swiper {\"textoInicial\":\"Nuestros Eventos\",\"descripcionInicial\":\"Descubre más\"} -->
<!-- /wp:amentum/eventos-swiper -->"

```text
#### 🔍 Verificar Contenido Actual de una Página

```bash

# Ver el contenido actual antes de modificar

docker compose exec wordpress-cli wp post get [POST_ID] --field=content

# Listar todas las páginas para encontrar el ID correcto

docker compose exec wordpress-cli wp post list --post_type=page --fields=ID,post_title,post_status

```text
### ⚙️ Casos de Uso Avanzados

#### Reemplazar Bloque Existente

```bash

# 1. Obtener contenido actual

CURRENT_CONTENT=$(docker compose exec wordpress-cli wp post get [POST_ID] --field=content)

# 2. Usar sed para reemplazar bloque específico

NEW_CONTENT=$(echo "$CURRENT_CONTENT" | sed 's/<!-- wp:amentum\/old-block -->/<!-- wp:amentum\/new-block -->/g')

# 3. Actualizar la página con el nuevo contenido

docker compose exec wordpress-cli wp post update [POST_ID] --post_content="$NEW_CONTENT"

```text
#### Insertar Múltiples Bloques Secuencialmente

```bash

# Script para insertar varios bloques en orden


#!/bin/bash

POST_ID=2

# Bloque 1: Hero

docker compose exec wordpress-cli wp post update $POST_ID --post_content="$(docker compose exec wordpress-cli wp post get $POST_ID --field=content)

<!-- wp:amentum/hero-full -->
<!-- /wp:amentum/hero-full -->
"

# Bloque 2: Servicios

docker compose exec wordpress-cli wp post update $POST_ID --post_content="$(docker compose exec wordpress-cli wp post get $POST_ID --field=content)

<!-- wp:amentum/servicios-columnas -->
<!-- /wp:amentum/servicios-columnas -->
"

# Bloque 3: Eventos Swiper

docker compose exec wordpress-cli wp post update $POST_ID --post_content="$(docker compose exec wordpress-cli wp post get $POST_ID --field=content)

<!-- wp:amentum/eventos-swiper -->
<!-- /wp:amentum/eventos-swiper -->
"

```text
### 🛠️ Tips de Automatización

#### Crear Script de Deploy Completo

```bash

#!/bin/bash


# deploy-blocks.sh - Automatizar despliegue de bloques

# Variables

HOME_PAGE_ID=2
ABOUT_PAGE_ID=3

echo "🚀 Desplegando bloques automáticamente..."

# Compilar CSS de bloques

npm run build

# Insertar bloque hero en página principal

docker compose exec wordpress-cli wp post update $HOME_PAGE_ID --post_content="<!-- wp:amentum/hero-full {\"backgroundImage\":\"/wp-content/themes/amentum/assets/images/hero-bg.jpg\"} -->
<!-- /wp:amentum/hero-full -->

<!-- wp:amentum/servicios-columnas {\"columnasPorFila\":3} -->
<!-- /wp:amentum/servicios-columnas -->

<!-- wp:amentum/eventos-swiper -->
<!-- /wp:amentum/eventos-swiper -->"

echo "✅ Bloques insertados correctamente"

# Verificar resultado

docker compose exec wordpress-cli wp post get $HOME_PAGE_ID --field=post_title

```text
#### Validar Inserción de Bloques

```bash

# Comprobar que el bloque se insertó correctamente

docker compose exec wordpress-cli wp post get [POST_ID] --field=content | grep "wp:amentum/nombre-bloque"

# Resultado esperado: debe mostrar la línea del bloque si existe

```text
### ⚠️ Consideraciones Importantes

#### Formato de Bloques Gutenberg

- **Obligatorio:** Usar formato exacto `<!-- wp:namespace/block-name -->`
- **Atributos:** JSON válido entre llaves `{\"key\":\"value\"}`
- **Cierre:** Siempre cerrar con `<!-- /wp:namespace/block-name -->`

#### Caracteres de Escape

```bash

# En atributos JSON, escapar comillas dobles

{\"texto\":\"Mi texto con \\\"comillas\\\" internas\"}

# URLs deben usar barras escapadas si es necesario

{\"backgroundImage\":\"/wp-content/themes/amentum/assets/images/bg.jpg\"}

```text
#### Backup Antes de Automatizar

```bash

# Crear backup de la página antes de modificar

docker compose exec wordpress-cli wp db export backup-pre-automation.sql

# En caso de error, restaurar

docker compose exec mysql mysql -u wp_dev_user -pwp_dev_pass_2024 wordpress_dev_db < backup-pre-automation.sql

```text
### 📊 Monitoreo y Testing

```bash

# Script de verificación post-despliegue


#!/bin/bash

echo "🔍 Verificando bloques insertados..."

# Comprobar bloques en página principal

BLOCKS_COUNT=$(docker compose exec wordpress-cli wp post get 2 --field=content | grep -c "wp:amentum")
echo "📊 Bloques encontrados: $BLOCKS_COUNT"

# Testing con debug.py

python3 logs/debug.py web

echo "✅ Verificación completada"

```

---

**Creado:** 29/08/2025
**Actualizado:** 01/09/2025
**Autor:** Theme Amentum Development Team
**Versión:** 1.1.0
**Estado:** ✅ Documentación Completa + Automatización
