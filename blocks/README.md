# Sistema de Bloques Modulares Amentum

## ⚡ Arquitectura Optimizada para SEO

Cada bloque es completamente independiente y autosuficiente. El sistema usa **CSS compilado único** para máximo rendimiento.

```text

blocks/
├── shared/
│   └── variables.css          # Variables CSS globales
├── [nombre-bloque]/
│   ├── block.php             # Registro y renderizado (SIN enqueue CSS individual)
│   ├── style.css             # CSS fuente (se compila automáticamente)
│   ├── editor.js             # Script del editor Gutenberg
│   └── README.md             # Documentación del bloque (opcional)
└── README.md                 # Este archivo

```text

### 🚀 Sistema CSS Optimizado

**ANTES:** Cada bloque cargaba su CSS individual (múltiples peticiones HTTP)
**AHORA:** Un solo `blocks.css` minificado para todos los bloques

## Bloques Disponibles

### 🎯 Bloques Principales

- **hero-full/** - Hero pantalla completa con drop cap y overlay dinámico
- **servicios-columnas/** - Sistema flexible de columnas configurables (1-6)
- **hero/** - Sección hero con título, subtítulo y botones
- **servicios/** - Grid de servicios con iconos SVG

### 📊 Bloques de Contenido

- **proyectos/** - Listado dinámico de custom post type 'projects'
- **magazine/** - Listado dinámico de custom post type 'magazine'
- **testimonios/** - Grid de testimonios estáticos
- **contacto/** - Formulario de contacto con información

## 🔧 Carga Automática y CSS Compilado

### Registro de Bloques

Los bloques se cargan automáticamente mediante `/inc/blocks-loader.php` que:

1. Include todos los archivos `block.php` de cada carpeta
2. Cada `block.php` se autoregistra usando `add_action('init')`
3. **NUEVO:** CSS se compila automáticamente en `blocks.css` único

### Sistema CSS Optimizado

```bash

# Compilar CSS de todos los bloques

npx gulp blocksCss

# Build completo (incluye CSS de bloques)

npx gulp

```text

El CSS se registra una sola vez en `inc/template-enqueued.php`:

```php

wp_enqueue_style('amentum-blocks',
    get_template_directory_uri() . '/assets/dist/css/blocks.css',
    array('all'),
    '1.0.0'
);

```

## ✅ Ventajas de esta Arquitectura

### 🚀 Rendimiento SEO Optimizado

✅ **Un solo CSS minificado** - Mejor puntuación PageSpeed
✅ **Menos peticiones HTTP** - Una sola descarga vs múltiples archivos
✅ **Cache optimizado** - Todos los estilos en un archivo cacheable
✅ **Carga más rápida** - Reducción significativa del tiempo de renderizado

### 🔧 Desarrollo Eficiente

✅ **Completamente modular** - Cada bloque mantiene su CSS individual
✅ **Fácil mantenimiento** - Un bloque por carpeta con toda su lógica
✅ **Compilación automática** - Gulp compila todos los CSS automáticamente
✅ **Escalable** - Agregar nuevo bloque = crear carpeta + recompilar
✅ **Sin conflictos** - Arquitectura limpia y organizada

## 📝 Agregar Nuevo Bloque

### Pasos para Crear Nuevo Bloque

1. **Crear estructura**

   ```bash

   mkdir blocks/nuevo-bloque
   touch blocks/nuevo-bloque/block.php
   touch blocks/nuevo-bloque/style.css
   touch blocks/nuevo-bloque/editor.js

   ```

1. **Implementar archivos**
   - `block.php`: Registro y renderizado (sin enqueue CSS individual)
   - `style.css`: Estilos que se compilarán automáticamente
   - `editor.js`: Interfaz de Gutenberg

1. **Compilar CSS**

   ```bash

   npx gulp blocksCss

   ```

1. **Usar templates de la documentación completa**

   Ver: `/BLOQUES_GUTENBERG.md` para templates y ejemplos completos

### ⚠️ Importante

- **NO** incluir `wp_enqueue_style()` individual en `block.php`
- Los estilos se compilan automáticamente en `blocks.css`
- Usar las convenciones de naming documentadas

## 🎨 Variables CSS Globales

Todas disponibles en `/blocks/shared/variables.css`:

- `--color-primary`
- `--color-secondary`
- `--color-accent`
- `--spacing-section`
- `--border-radius`
- etc.

---

## 📚 Documentación Completa

Para guías detalladas, templates y ejemplos completos:

👉 **Ver: [`/BLOQUES_GUTENBERG.md`](../BLOQUES_GUTENBERG.md)**

Incluye:

- Templates completos de PHP, JS y CSS
- Patrones de desarrollo probados
- Ejemplos reales (hero-full, servicios-columnas)
- Sistema de CSS optimizado explicado
- Mejores prácticas y errores comunes

---

**Sistema optimizado para SEO** ⚡
**Creado:** 29/08/2025 | **Actualizado:** Sistema CSS compilado implementado
