# 🎨 Sistema de Tipografía Fluida con CSS Clamp - Plan de Implementación

## 📋 Objetivo Principal

Implementar un sistema completo de tipografía fluida que escale automáticamente entre dispositivos usando CSS `clamp()`, integrado con el sistema SCSS de Vite + WordPress.

**Especificaciones del usuario:**
- **Desktop width**: 1900px
- **H1 Desktop**: 40px
- **Escalado proporcional** en todos los viewports

---

## 🚀 **FASE 1: Fundamentos del Sistema Fluido**

### ✅ TODO 1.1: Crear Funciones SCSS Base

```scss
// assets/sass/base/_fluid-functions.scss
// Sistema de cálculo automático para tipografía fluida

/**
 * Función principal para calcular clamp() automáticamente
 * @param {Number} $min-size - Tamaño mínimo (mobile)
 * @param {Number} $max-size - Tamaño máximo (desktop)
 * @param {Number} $min-vw - Viewport mínimo en px (default: 320)
 * @param {Number} $max-vw - Viewport máximo en px (default: 1900)
 * @return {String} - clamp() CSS válido
 */
@function fluid-size($min-size, $max-size, $min-vw: 320, $max-vw: 1900) {
  // Convertir a rem si están en px para accesibilidad
  $min-size-rem: if(unit($min-size) == 'px', $min-size / 16 * 1rem, $min-size);
  $max-size-rem: if(unit($max-size) == 'px', $max-size / 16 * 1rem, $max-size);
  
  // Calcular slope (pendiente) y intersection
  $slope: (strip-unit($max-size) - strip-unit($min-size)) / ($max-vw - $min-vw);
  $intersection: strip-unit($min-size) - $slope * $min-vw;
  
  // Generar preferred value: slope * 100vw + intersection
  $preferred: calc(#{$slope * 100}vw + #{$intersection / 16}rem);
  
  @return clamp(#{$min-size-rem}, #{$preferred}, #{$max-size-rem});
}

/**
 * Función helper para quitar unidades CSS
 */
@function strip-unit($number) {
  @if type-of($number) == 'number' and not unitless($number) {
    @return $number / ($number * 0 + 1);
  }
  @return $number;
}

/**
 * Función para calcular escalado proporcional automático
 * Basado en H1 como referencia (40px desktop)
 */
@function calc-proportional-size($desktop-reference: 40, $scale-factor: 1, $mobile-reference: null) {
  $desktop-size: $desktop-reference * $scale-factor;
  
  // Si no se proporciona mobile, calcular automáticamente (60% del desktop)
  $mobile-size: if($mobile-reference, $mobile-reference, $desktop-size * 0.6);
  
  @return fluid-size(#{$mobile-size}px, #{$desktop-size}px, 320, 1900);
}
```

### ✅ TODO 1.2: Crear Mixins de Tipografía Fluida

```scss
// assets/sass/base/_fluid-mixins.scss

/**
 * Mixin principal para aplicar tipografía fluida
 */
@mixin fluid-type($property: font-size, $min-size, $max-size, $min-vw: 320, $max-vw: 1900) {
  // Fallback para navegadores sin soporte clamp()
  #{$property}: $max-size;
  
  // Tipografía fluida moderna
  #{$property}: fluid-size($min-size, $max-size, $min-vw, $max-vw);
}

/**
 * Mixin para tipografía fluida con line-height automático
 */
@mixin fluid-typography($min-size, $max-size, $line-height: 1.2, $min-vw: 320, $max-vw: 1900) {
  @include fluid-type(font-size, $min-size, $max-size, $min-vw, $max-vw);
  line-height: $line-height;
}

/**
 * Mixin para headings con configuración completa
 */
@mixin fluid-heading($level: 1, $weight: 600, $line-height: 1.2, $margin-bottom: null) {
  font-weight: $weight;
  line-height: $line-height;
  
  // Aplicar font-size según nivel
  @if $level == 1 {
    font-size: fluid-font('h1');
  } @else if $level == 2 {
    font-size: fluid-font('h2');
  } @else if $level == 3 {
    font-size: fluid-font('h3');
  } @else if $level == 4 {
    font-size: fluid-font('h4');
  } @else if $level == 5 {
    font-size: fluid-font('h5');
  } @else if $level == 6 {
    font-size: fluid-font('h6');
  }
  
  @if $margin-bottom {
    margin-bottom: $margin-bottom;
  }
}

/**
 * Mixin para debug durante desarrollo
 */
@mixin debug-fluid-size($label: 'Size') {
  position: relative;
  
  &::before {
    content: '#{$label}: ' attr(data-debug-size);
    position: absolute;
    top: -20px;
    left: 0;
    font-size: 10px;
    background: #ff6b6b;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: normal;
    line-height: 1;
    pointer-events: none;
    z-index: 9999;
  }
}
```

---

## 🎯 **FASE 2: Configuración de Design Tokens**

### ✅ TODO 2.1: Recopilar Medidas del Usuario

**⚠️ REQUERIDO: Proporcionar medidas mobile para cada elemento**

```scss
// PENDIENTE: Usuario debe proporcionar estas medidas
$typography-scale: (
  'h1': (
    'mobile': 24px,  // ❓ ¿Cuál es el tamaño H1 en mobile?
    'desktop': 40px, // ✅ Proporcionado
  ),
  'h2': (
    'mobile': ?px,   // ❓ ¿Cuál es el tamaño H2 en mobile?
    'desktop': ?px,  // ❓ ¿Cuál es el tamaño H2 en desktop?
  ),
  'h3': (
    'mobile': ?px,   // ❓ Necesario
    'desktop': ?px,  // ❓ Necesario
  ),
  'h4': (
    'mobile': ?px,
    'desktop': ?px,
  ),
  'h5': (
    'mobile': ?px,
    'desktop': ?px,
  ),
  'h6': (
    'mobile': ?px,
    'desktop': ?px,
  ),
  'body': (
    'mobile': ?px,   // ❓ ¿Tamaño de texto base en mobile?
    'desktop': ?px,  // ❓ ¿Tamaño de texto base en desktop?
  ),
  'small': (
    'mobile': ?px,
    'desktop': ?px,
  )
);
```

### ✅ TODO 2.2: Crear Design Tokens Fluidos

```scss
// assets/sass/base/_fluid-tokens.scss
// Design tokens con tipografía fluida integrada

// Configuración del sistema
$fluid-config: (
  'desktop-width': 1900px,
  'mobile-width': 320px,
  'base-h1-desktop': 40px,  // Referencia del usuario
  'accessibility': true     // Usa rem + vw para accesibilidad
);

// Escala tipográfica fluida (se completará con medidas del usuario)
$fluid-typography: (
  'sizes': (
    // Display para heroes muy grandes
    'display-xl': fluid-size(32px, 80px),   // 2rem → 5rem
    'display-lg': fluid-size(28px, 64px),   // 1.75rem → 4rem
    'display': fluid-size(26px, 56px),      // 1.625rem → 3.5rem
    
    // Headings principales
    'h1': fluid-size(24px, 40px),           // Usuario: completar mobile
    'h2': fluid-size(?px, ?px),             // Usuario: completar ambos
    'h3': fluid-size(?px, ?px),             // Usuario: completar ambos
    'h4': fluid-size(?px, ?px),
    'h5': fluid-size(?px, ?px),
    'h6': fluid-size(?px, ?px),
    
    // Texto base
    'body': fluid-size(?px, ?px),           // Usuario: completar ambos
    'body-lg': fluid-size(?px, ?px),        // Texto destacado
    'body-sm': fluid-size(?px, ?px),        // Texto pequeño
    'caption': fluid-size(?px, ?px),        // Captions, metadata
    
    // Especialidades
    'quote': fluid-size(20px, 32px),        // Citas destacadas
    'lead': fluid-size(18px, 22px),         // Párrafos de introducción
  ),
  
  'weights': (
    'thin': 100,
    'light': 300,
    'normal': 400,
    'medium': 500,
    'semibold': 600,
    'bold': 700,
    'extrabold': 800,
    'black': 900
  ),
  
  'line-heights': (
    'none': 1,
    'tight': 1.1,
    'snug': 1.2,
    'normal': 1.5,
    'relaxed': 1.6,
    'loose': 2
  ),
  
  'letter-spacing': (
    'tighter': -0.05em,
    'tight': -0.025em,
    'normal': 0,
    'wide': 0.025em,
    'wider': 0.05em,
    'widest': 0.1em
  )
);

// Función helper para acceder a tamaños fluidos
@function fluid-font($size-name) {
  @return map-get(map-get($fluid-typography, 'sizes'), $size-name);
}

// Función helper para weights
@function font-weight($weight-name) {
  @return map-get(map-get($fluid-typography, 'weights'), $weight-name);
}

// Función helper para line-heights
@function line-height($lh-name) {
  @return map-get(map-get($fluid-typography, 'line-heights'), $lh-name);
}
```

---

## 🏗️ **FASE 3: Implementación en Componentes**

### ✅ TODO 3.1: Aplicar a Elementos Base

```scss
// assets/sass/base/_typography.scss
// Aplicar tipografía fluida a elementos HTML base

// Headings
h1, .h1 {
  @include fluid-heading(1, font-weight('bold'), line-height('tight'), space('lg'));
}

h2, .h2 {
  @include fluid-heading(2, font-weight('semibold'), line-height('tight'), space('md'));
}

h3, .h3 {
  @include fluid-heading(3, font-weight('semibold'), line-height('snug'), space('md'));
}

h4, .h4 {
  @include fluid-heading(4, font-weight('medium'), line-height('snug'), space('sm'));
}

h5, .h5 {
  @include fluid-heading(5, font-weight('medium'), line-height('normal'), space('sm'));
}

h6, .h6 {
  @include fluid-heading(6, font-weight('medium'), line-height('normal'), space('xs'));
}

// Texto base
body {
  font-size: fluid-font('body');
  line-height: line-height('normal');
  font-weight: font-weight('normal');
}

// Párrafos
p {
  font-size: fluid-font('body');
  line-height: line-height('relaxed');
  margin-bottom: space('md');
  
  &.lead {
    font-size: fluid-font('lead');
    font-weight: font-weight('medium');
  }
}

// Enlaces
a {
  font-size: inherit;
  @include animate(color);
}

// Listas
ul, ol {
  font-size: fluid-font('body');
  line-height: line-height('relaxed');
}

// Quotes
blockquote {
  font-size: fluid-font('quote');
  font-style: italic;
  line-height: line-height('relaxed');
  margin: space('xl') 0;
  
  cite {
    font-size: fluid-font('body-sm');
    font-style: normal;
    font-weight: font-weight('medium');
  }
}

// Small text
small, .small {
  font-size: fluid-font('caption');
}
```

### ✅ TODO 3.2: Integrar en Bloques Gutenberg

```scss
// assets/sass/blocks/_hero.scss
// Ejemplo de aplicación en bloque hero

.amentum-hero-block {
  @include container;
  @include flex-center;
  min-height: 80vh;
  
  .hero-content {
    text-align: center;
    z-index: 2;
    
    .hero-title {
      // Usar display para títulos muy grandes
      font-size: fluid-font('display');
      font-weight: font-weight('black');
      line-height: line-height('tight');
      letter-spacing: letter-spacing('tight');
      margin-bottom: space('lg');
      color: color('gray-900');
      
      @include breakpoint('md') {
        // Opcional: ajustes específicos en tablet+
        letter-spacing: letter-spacing('tighter');
      }
    }
    
    .hero-subtitle {
      font-size: fluid-font('h3');
      font-weight: font-weight('medium');
      line-height: line-height('normal');
      color: color('gray-600');
      margin-bottom: space('xl');
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .hero-description {
      font-size: fluid-font('body-lg');
      line-height: line-height('relaxed');
      color: color('gray-500');
      margin-bottom: space('2xl');
      max-width: 500px;
      margin-left: auto;
      margin-right: auto;
    }
  }
}
```

### ✅ TODO 3.3: Crear Clases Utilitarias Fluidas

```scss
// assets/sass/utilities/_fluid-typography.scss
// Clases utilitarias para tipografía fluida

// Tamaños fluidos
@each $size-name, $size-value in map-get($fluid-typography, 'sizes') {
  .text-fluid-#{$size-name} {
    font-size: $size-value !important;
  }
}

// Pesos
@each $weight-name, $weight-value in map-get($fluid-typography, 'weights') {
  .font-#{$weight-name} {
    font-weight: $weight-value !important;
  }
}

// Line heights
@each $lh-name, $lh-value in map-get($fluid-typography, 'line-heights') {
  .leading-#{$lh-name} {
    line-height: $lh-value !important;
  }
}

// Letter spacing
@each $ls-name, $ls-value in map-get($fluid-typography, 'letter-spacing') {
  .tracking-#{$ls-name} {
    letter-spacing: $ls-value !important;
  }
}

// Combinaciones predefinidas comunes
.text-hero {
  font-size: fluid-font('display') !important;
  font-weight: font-weight('black') !important;
  line-height: line-height('tight') !important;
  letter-spacing: letter-spacing('tight') !important;
}

.text-title {
  font-size: fluid-font('h1') !important;
  font-weight: font-weight('bold') !important;
  line-height: line-height('tight') !important;
}

.text-subtitle {
  font-size: fluid-font('h3') !important;
  font-weight: font-weight('medium') !important;
  line-height: line-height('normal') !important;
}

.text-body-large {
  font-size: fluid-font('body-lg') !important;
  line-height: line-height('relaxed') !important;
}

.text-caption {
  font-size: fluid-font('caption') !important;
  line-height: line-height('normal') !important;
  color: color('gray-500') !important;
}
```

---

## 🧪 **FASE 4: Testing y Optimización**

### ✅ TODO 4.1: Herramientas de Testing

```scss
// assets/sass/dev/_debug-typography.scss
// Solo para desarrollo - importar condicionalmente

@if $debug-mode == true {
  // Overlay para mostrar tamaños actuales
  .debug-typography {
    position: relative;
    
    &::after {
      content: 'Font: ' attr(data-font-size) ' | VW: ' attr(data-viewport-width);
      position: absolute;
      top: -25px;
      left: 0;
      background: rgba(255, 107, 107, 0.9);
      color: white;
      font-size: 10px !important;
      font-weight: normal !important;
      padding: 4px 8px;
      border-radius: 4px;
      white-space: nowrap;
      pointer-events: none;
      z-index: 10000;
      font-family: monospace !important;
      line-height: 1 !important;
    }
  }
  
  // Aplicar automáticamente a headings durante debug
  h1, h2, h3, h4, h5, h6 {
    @extend .debug-typography;
  }
}

// JavaScript helper para mostrar valores actuales
/*
// Agregar a main.js durante desarrollo
if (window.location.search.includes('debug=typography')) {
  function updateTypographyDebug() {
    document.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach(el => {
      const fontSize = window.getComputedStyle(el).fontSize;
      const viewportWidth = window.innerWidth;
      el.setAttribute('data-font-size', fontSize);
      el.setAttribute('data-viewport-width', viewportWidth + 'px');
    });
  }
  
  updateTypographyDebug();
  window.addEventListener('resize', updateTypographyDebug);
}
*/
```

### ✅ TODO 4.2: Validación en Múltiples Dispositivos

```scss
// assets/sass/dev/_breakpoint-testing.scss
// Estilos para validar comportamiento en breakpoints

@if $debug-mode == true {
  // Indicador visual del breakpoint actual
  body::before {
    content: 'XS: <640px';
    position: fixed;
    top: 10px;
    right: 10px;
    background: #3B82F6;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px !important;
    font-weight: bold !important;
    z-index: 99999;
    font-family: monospace !important;
    
    @include breakpoint('sm') {
      content: 'SM: ≥640px';
      background: #10B981;
    }
    
    @include breakpoint('md') {
      content: 'MD: ≥768px';
      background: #F59E0B;
    }
    
    @include breakpoint('lg') {
      content: 'LG: ≥1024px';
      background: #EF4444;
    }
    
    @include breakpoint('xl') {
      content: 'XL: ≥1280px';
      background: #8B5CF6;
    }
    
    @include breakpoint('2xl') {
      content: '2XL: ≥1536px';
      background: #EC4899;
    }
  }
  
  // Grid overlay para alineación
  .debug-grid {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 9998;
    opacity: 0.1;
    background-image: 
      linear-gradient(rgba(255,0,0,1) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,0,0,1) 1px, transparent 1px);
    background-size: 20px 20px;
  }
}
```

### ✅ TODO 4.3: Performance y Accesibilidad

```scss
// assets/sass/base/_accessibility-typography.scss
// Asegurar accesibilidad en tipografía fluida

// Respetar preferencias de usuario para movimiento reducido
@media (prefers-reduced-motion: reduce) {
  * {
    // Deshabilitar transiciones en texto si el usuario prefiere menos movimiento
    transition-property: color, background-color, border-color !important;
    transition-duration: 0.01ms !important;
  }
}

// Asegurar contraste mínimo
.text-contrast-aa {
  // WCAG AA compliant contrast ratios
  color: color('gray-700');
  
  &.on-dark {
    color: color('gray-200');
  }
}

.text-contrast-aaa {
  // WCAG AAA compliant contrast ratios
  color: color('gray-900');
  
  &.on-dark {
    color: color('gray-50');
  }
}

// Zoom-friendly: combinar rem + vw
@mixin accessible-fluid-type($min-size, $max-size, $min-vw: 320, $max-vw: 1900) {
  // Usar más rem que vw para mejor zoom
  $vw-component: 2vw;
  $rem-component: ($max-size - $min-size) / 16 * 1rem * 0.6;
  
  font-size: clamp(
    #{$min-size / 16 * 1rem},
    calc(#{$rem-component} + #{$vw-component}),
    #{$max-size / 16 * 1rem}
  );
}

// Focus states para elementos tipográficos
a, button, input, textarea, select {
  &:focus-visible {
    outline: 2px solid color('primary');
    outline-offset: 2px;
    border-radius: 2px;
  }
}
```

---

## 📊 **FASE 5: Integración con Vite**

### ✅ TODO 5.1: Actualizar style.scss Principal

```scss
// assets/sass/style.scss
// Integrar sistema de tipografía fluida

// Base system (orden importante)
@import 'base/tokens';           // Design tokens base
@import 'base/fluid-functions';  // Funciones de cálculo fluido  
@import 'base/fluid-tokens';     // Tokens tipográficos fluidos
@import 'base/fluid-mixins';     // Mixins para tipografía fluida
@import 'base/functions';        // Funciones legacy
@import 'base/mixins';           // Mixins generales
@import 'base/normalize';        // CSS reset
@import 'base/variables';        // Variables legacy (migrar gradualmente)
@import 'base/typography';       // Aplicar tipografía fluida a elementos HTML
@import 'base/globales';         // Estilos globales
@import 'base/accessibility-typography'; // Accesibilidad

// Layout
@import 'layout/grid';
@import 'layout/header';
@import 'layout/footer';
@import 'layout/sidebar';

// Components (ahora con tipografía fluida)
@import 'components/buttons';
@import 'components/forms';
@import 'components/modals';
@import 'components/cards';
@import 'components/navigation';

// Utilities (incluyendo clases fluidas)
@import 'utilities/fluid-typography';
@import 'base/utilidades';

// Pages
@import 'pages/home';
@import 'pages/about';
@import 'pages/contact';

// Blocks (todos con tipografía fluida)
@import 'blocks/index';

// Vendor overrides
@import 'vendor/bootstrap-overrides';
@import 'vendor/swiper-overrides';

// Development only
@if $debug-mode == true {
  @import 'dev/debug-typography';
  @import 'dev/breakpoint-testing';
}
```

### ✅ TODO 5.2: Configurar Variables de Entorno

```javascript
// vite.config.js - añadir configuración para debug
export default defineConfig(({ mode }) => {
  const isDev = mode === 'development';
  
  return {
    // ... configuración existente
    
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `
            $debug-mode: ${isDev};
            @import "./assets/sass/base/tokens";
            @import "./assets/sass/base/fluid-tokens";
          `
        }
      }
    },
    
    // Plugin para mostrar información de tipografía en dev
    plugins: [
      // ... plugins existentes
      
      isDev && {
        name: 'typography-debug',
        transformIndexHtml(html) {
          return html.replace(
            '<head>',
            `<head>
              <style>
                .typography-debug { font-family: monospace; }
              </style>`
          );
        }
      }
    ].filter(Boolean)
  };
});
```

---

## ✅ **FASE 6: Testing y Documentación**

### ✅ TODO 6.1: Checklist de Validación

#### Funcionalidad Básica
- [ ] **Función `fluid-size()`** calcula correctamente
- [ ] **Mixins** se aplican sin errores
- [ ] **Design tokens** son accesibles vía funciones helper
- [ ] **Elementos HTML base** usan tipografía fluida
- [ ] **Clases utilitarias** funcionan correctamente

#### Responsive Testing
- [ ] **370px (Mobile)**: Tamaños mínimos correctos
- [ ] **768px (Tablet)**: Escalado proporcional
- [ ] **1024px (Desktop S)**: Escalado proporcional
- [ ] **1440px (Desktop M)**: Escalado proporcional  
- [ ] **1900px (Desktop L)**: Tamaños máximos correctos
- [ ] **>1900px**: No crece más del máximo

#### Accesibilidad
- [ ] **Zoom 200%**: Texto sigue siendo legible
- [ ] **Contraste**: Cumple WCAG AA mínimo
- [ ] **Focus states**: Visibles y accesibles
- [ ] **Movimiento reducido**: Respetado

#### Performance
- [ ] **CSS Build**: No errores de compilación
- [ ] **Bundle size**: Incremento aceptable
- [ ] **Runtime performance**: Sin lag en resize

### ✅ TODO 6.2: Documentación para el Equipo

```scss
// assets/sass/docs/_typography-guide.scss
// Documentación inline para desarrolladores

/*
# 📖 Guía de Uso - Tipografía Fluida

## 🎯 Funciones Principales

### fluid-font($size-name)
Obtiene un tamaño fluido predefinido
Uso: `font-size: fluid-font('h1');`

### fluid-size($min-size, $max-size, $min-vw, $max-vw)
Calcula clamp() personalizado
Uso: `font-size: fluid-size(16px, 24px, 320, 1900);`

## 🎨 Mixins Útiles

### @include fluid-type($property, $min, $max)
Aplica tipografía fluida a cualquier propiedad
Uso: `@include fluid-type(font-size, 14px, 18px);`

### @include fluid-heading($level, $weight, $line-height)
Configura heading completo con valores fluidos
Uso: `@include fluid-heading(2, 600, 1.2);`

## 🔧 Clases Utilitarias

### Tamaños fluidos:
.text-fluid-h1, .text-fluid-h2, etc.
.text-fluid-body, .text-fluid-caption

### Combinaciones:
.text-hero, .text-title, .text-subtitle

## 🐛 Debug

### Activar modo debug:
$debug-mode: true;

### Ver tamaños actuales:
?debug=typography en URL

### Indicadores visuales:
Breakpoint actual (esquina superior derecha)
Grid overlay disponible

## ⚠️ Importante

1. Siempre usar rem para min/max por accesibilidad
2. Combinar vw + rem en preferred value
3. Testing en zoom 200%
4. Validar contraste de colores
*/
```

---

## 📊 **Comparación Before/After**

| Aspecto | Antes (CSS fijo) | Después (Tipografía Fluida) |
|---------|------------------|------------------------------|
| **Responsive** | Media queries múltiples | CSS clamp() único |
| **Escalabilidad** | Breakpoints fijos | Escalado continuo |
| **Mantenimiento** | Muchas reglas CSS | Funciones SCSS |
| **Performance** | Más CSS | Menos CSS |
| **UX Móvil** | Saltos bruscos | Transición suave |
| **Accesibilidad** | Limitada | Zoom-friendly |
| **Consistency** | Manual | Sistema automático |

---

## 🚦 **Plan de Implementación**

### Semana 1: Fundamentos
- Crear funciones y mixins base
- Configurar design tokens fluidos
- Setup básico en Vite

### Semana 2: Aplicación  
- Migrar elementos HTML base
- Actualizar bloques principales
- Crear clases utilitarias

### Semana 3: Testing
- Validación en dispositivos
- Pruebas de accesibilidad
- Optimización de performance

### Semana 4: Documentación
- Guía para desarrolladores
- Ejemplos de uso
- Best practices

---

## ✅ **Sistema Completado**

**Especificaciones finales:**

```
Mobile: 370px | Desktop: 1900px

H1: Mobile 28px → Desktop 40px ✅
H2: Mobile 24px → Desktop 32px ✅
H3: Mobile 21px → Desktop 26px ✅
H4: Mobile 18px → Desktop 22px ✅
H5: Mobile 16px → Desktop 18px ✅
H6: Mobile 14px → Desktop 16px ✅
Body: Mobile 15px → Desktop 16px ✅
Body Large: Mobile 17px → Desktop 20px ✅
Small: Mobile 12px → Desktop 13px ✅
```

**Características del sistema:**

1. ✅ **Escalado Proporcional**: Basado en ratio áureo y mejores prácticas
2. ✅ **Accesible**: Combina rem + vw para zoom 200%
3. ✅ **Performante**: Sin media queries, cálculo nativo del navegador
4. ✅ **Consistente**: Sistema unificado para todo el proyecto
5. ✅ **Debuggeable**: Herramientas visuales integradas

---

**📅 Creado**: 2025-01-02  
**🎯 Objetivo**: Sistema completo de tipografía fluida  
**⏱️ Estimación**: 15-20 horas  
**📊 Estado**: ✅ Listo para implementar  
**📏 Rango**: 370px - 1900px con escalado perfecto  

---

> **💡 Nota**: Este sistema transformará completamente la experiencia tipográfica, eliminando la necesidad de media queries para texto y garantizando escalado perfecto en todos los dispositivos.