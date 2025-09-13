# Sistema de Diseño - Botones

## Descripción

Sistema unificado de botones con línea negra debajo, diseñado para mantener consistencia visual en todo el tema Amentum. Todos los botones incluyen efectos de hover con elevación y línea animada.

## Características Principales

- ✅ Línea negra debajo de cada botón
- ✅ Efecto de hover con elevación (translateY)
- ✅ Animaciones suaves (0.3s ease)
- ✅ Versiones blanca y negra
- ✅ Variantes de tamaño
- ✅ Compatible con botones deshabilitados
- ✅ Responsive design
- ✅ Variables CSS customizables

## Clases Disponibles

### Clase Base
```html
<a href="#" class="btn">Botón Base</a>
```

### Versiones de Color

#### Botón Blanco (Por Defecto)
```html
<a href="#" class="btn">Botón Blanco</a>
<a href="#" class="btn btn--white">Botón Blanco Explícito</a>
```

#### Botón Negro
```html
<a href="#" class="btn btn--black">Botón Negro</a>
```

### Variantes de Tamaño

#### Botón Pequeño
```html
<a href="#" class="btn btn--small">Botón Pequeño</a>
```

#### Botón Grande
```html
<a href="#" class="btn btn--large">Botón Grande</a>
```

#### Botón de Ancho Completo
```html
<a href="#" class="btn btn--full-width">Botón Full Width</a>
```

### Estados Especiales

#### Botón Deshabilitado
```html
<a href="#" class="btn btn--disabled">Botón Deshabilitado</a>
<button class="btn" disabled>Botón Deshabilitado</button>
```

## Variables CSS Customizables

Puedes personalizar el sistema modificando estas variables en `:root`:

```css
:root {
  --btn-padding-x: 1.5rem;           /* Padding horizontal */
  --btn-padding-y: 0.75rem;          /* Padding vertical */
  --btn-font-size: 1rem;             /* Tamaño de fuente */
  --btn-font-weight: 500;            /* Peso de fuente */
  --btn-line-height: 1.5;            /* Altura de línea */
  --btn-border-radius: 0;            /* Radio de borde */
  --btn-transition: all 0.3s ease;   /* Transición */
  --btn-underline-height: 2px;       /* Altura de línea inferior */
  --btn-underline-spacing: 4px;      /* Espacio de línea inferior */
}
```

## Comportamiento y Efectos

### Efecto Hover/Focus
- El botón se eleva 2px (translateY(-2px))
- La línea negra se mueve 2px hacia abajo
- Cambio sutil de color de fondo

### Efecto Active
- El botón vuelve a su posición original
- La línea vuelve a su posición base

### Línea Negra Característica
- Siempre negra (#000) por debajo del botón
- Se anima junto con los efectos hover/focus
- Espaciado configurable con `--btn-underline-spacing`

## Migración desde Clases Obsoletas

### Clases Reemplazadas
Las siguientes clases han sido reemplazadas por el nuevo sistema:

| Clase Obsoleta | Nueva Clase |
|----------------|-------------|
| `.btn-cta` | `.btn` o `.btn btn--white` |
| `.slide-link` | `.btn btn--white` |
| `.slide-description` | `.btn btn--white` |
| `.btn-outline` | `.btn btn--white` |
| `.button` | `.btn btn--black` |

### Proceso de Migración Completado

✅ **Bloques actualizados:**
- `servicios-columnas/block.php` - `.btn-cta` → `.btn btn--white`
- `eventos-swiper/block.php` - `.slide-link` y `.slide-description` → `.btn btn--white`
- `about/block.php` - `.btn-outline` → `.btn btn--white`

✅ **Estilos eliminados:**
- Estilos obsoletos de `.btn-cta`, `.slide-link`, `.slide-description`
- Mantenida compatibilidad con clases legacy: `.cta`, `.button`, `.wp-block-button__link`

## Compatibilidad Legacy

El sistema mantiene compatibilidad con clases existentes de WordPress y temas legacy:

```scss
// Clases legacy que automáticamente usan el sistema
.cta,
.button,
.wp-block-button__link {
  @include btn-white;

  &.btn-black,
  &.black {
    @include btn-black;
  }
}
```

## Uso en Bloques Gutenberg

Para bloques de WordPress, el sistema incluye reset automático:

```scss
.wp-block-button {
  .wp-block-button__link {
    border-radius: var(--btn-border-radius);
    padding: var(--btn-padding-y) var(--btn-padding-x);
  }
}
```

## Ejemplos de Uso Completos

### En HTML/PHP
```php
<!-- Botón básico blanco -->
<a href="/contacto" class="btn">Contactar</a>

<!-- Botón negro grande -->
<a href="/servicios" class="btn btn--black btn--large">Ver Servicios</a>

<!-- Botón pequeño de ancho completo -->
<button class="btn btn--small btn--full-width" type="submit">Enviar</button>

<!-- Botón deshabilitado -->
<a href="#" class="btn btn--disabled">No disponible</a>
```

### Customización con Variables CSS
```css
/* Botón con estilo personalizado */
.mi-boton-especial {
  --btn-padding-x: 2rem;
  --btn-padding-y: 1rem;
  --btn-font-size: 1.2rem;
  --btn-underline-height: 3px;
}
```

## Arquitectura del Sistema

### Archivos del Sistema
```
assets/sass/base/_botones.scss    # Sistema completo de botones
```

### Estructura de Mixins
- `@mixin btn-base` - Estilos base comunes
- `@mixin btn-white` - Versión blanca
- `@mixin btn-black` - Versión negra

### Jerarquía de Clases
```scss
.btn {
  @include btn-base;
  @include btn-white; // Por defecto

  &--black { @include btn-black; }
  &--small { /* variantes de tamaño */ }
  &--large { /* variantes de tamaño */ }
  &--full-width { /* ancho completo */ }
  &--disabled { /* estado deshabilitado */ }
}
```

## Mantenimiento

### Para Agregar Nueva Variante
1. Crear mixin en `_botones.scss`
2. Agregar clase modificadora `.btn--nueva-variante`
3. Documentar en este archivo

### Para Personalizar Colores
Modificar las variables en el mixin correspondiente o crear override específico.

### Testing
- Probar en todos los bloques existentes
- Verificar comportamiento hover/focus
- Confirmar que la línea negra se anima correctamente
- Validar responsive design

---

**Creado:** $(date +'%d/%m/%Y')
**Versión:** 1.0.0
**Estado:** ✅ Implementado y migrado
**Mantenedor:** Sistema de Diseño Amentum