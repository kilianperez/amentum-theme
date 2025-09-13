# 📋 TODO: MIGRACIÓN DE LIBRERÍAS PASO A PASO

## 🚨 PROBLEMA IDENTIFICADO

**ERROR METODOLÓGICO**: He intentado importar 8 librerías de golpe en un bundle sin verificar una por una. Esto hace imposible identificar qué librería específica está causando problemas de:

- ❌ Conflictos de versiones ES6 vs UMD
- ❌ Problemas de scope global
- ❌ Errores de concatenación IIFE
- ❌ Incompatibilidades entre librerías
- ❌ Problemas de inicialización

## 🎯 METODOLOGÍA CORRECTA

### Importar UNA librería a la vez, verificar que funcione, eliminar del enqueue CDN, continuar con la siguiente

---

## 📋 PLAN DETALLADO PASO A PASO

### ⚪ PASO 0: RESETEAR AL ESTADO BASE

- [ ] **Limpiar vite.config.js**: Quitar todas las librerías externas de `filesToConcat`
- [ ] **Mantener solo**: jQuery (ya incluido en WordPress) + archivos personales del theme
- [ ] **Restablecer enqueue**: Volver a cargar todas las librerías por CDN temporalmente
- [ ] **Verificar**: Que la web funcione igual que antes con CDN
- [ ] **Build y test**: `npm run vite:build` + test en navegador

### 🟡 PASO 1: IMPORTAR SOLO JQUERY (BASE)

- [ ] **Objetivo**: Verificar que jQuery local funciona igual que el CDN
- [ ] **vite.config.js**: Agregar SOLO `'node_modules/jquery/dist/jquery.js'`
- [ ] **Enqueue**: Mantener todas las demás librerías CDN, quitar jQuery CDN
- [ ] **Testing específico**:

  ```javascript

  console.log('jQuery version:', $().jquery);
  $('body').css('opacity'); // Test básico

  ```

- [ ] **Verificar**: Que no hay errores y jQuery funciona
- [ ] **Solo continuar si**: ✅ jQuery funciona perfectamente

### 🟡 PASO 2: AGREGAR SWIPER

- [ ] **vite.config.js**: Agregar `'node_modules/swiper/swiper-bundle.js'`
- [ ] **Enqueue**: Quitar Swiper CDN, mantener resto CDN
- [ ] **Testing específico**:

  ```javascript

  console.log('Swiper disponible:', typeof Swiper);
  console.log('Swiper version:', Swiper.version);
  // Test instanciación básica si hay elementos .swiper

  ```

- [ ] **Verificar**: Que Swiper funciona + jQuery sigue funcionando
- [ ] **Solo continuar si**: ✅ Ambos funcionan

### 🟡 PASO 3: AGREGAR GSAP + ScrollTrigger

- [ ] **vite.config.js**: Agregar GSAP:

  ```javascript

  'node_modules/gsap/dist/gsap.js',
  'node_modules/gsap/dist/ScrollTrigger.js'

  ```

- [ ] **Enqueue**: Quitar GSAP CDN, mantener resto
- [ ] **Expose-globals.js**: Verificar exposición de GSAP
- [ ] **Testing específico**:

  ```javascript

  console.log('GSAP disponible:', typeof gsap);
  console.log('GSAP version:', gsap.version);
  gsap.set('body', {opacity: 1}); // Test básico
  gsap.registerPlugin(ScrollTrigger); // Test plugin

  ```

- [ ] **Verificar**: GSAP + Swiper + jQuery funcionan
- [ ] **Solo continuar si**: ✅ Los 3 funcionan

### 🟡 PASO 4: AGREGAR LENIS

- [ ] **vite.config.js**: Agregar `'node_modules/lenis/dist/lenis.js'`
- [ ] **Enqueue**: Quitar Lenis CDN
- [ ] **Testing específico**:

  ```javascript

  console.log('Lenis disponible:', typeof Lenis);
  // Test instanciación
  const testLenis = new Lenis({duration: 1.2});
  console.log('Lenis creado:', testLenis);
  testLenis.destroy(); // Limpiar test

  ```

- [ ] **Verificar**: Scroll suave + todas las anteriores funcionan
- [ ] **Solo continuar si**: ✅ Las 4 funcionan

### 🟡 PASO 5: AGREGAR SPLITTYPE

- [ ] **vite.config.js**: Agregar `'node_modules/split-type/dist/index.js'`
- [ ] **Enqueue**: Quitar SplitType CDN
- [ ] **Testing específico**:

  ```javascript

  console.log('SplitType disponible:', typeof SplitType);
  // Test con elemento temporal
  const testEl = document.createElement('div');
  testEl.textContent = 'Test';
  document.body.appendChild(testEl);
  const split = new SplitType(testEl);
  console.log('SplitType creado:', split);
  document.body.removeChild(testEl);

  ```

- [ ] **Verificar**: SplitType + todas las anteriores
- [ ] **Solo continuar si**: ✅ Las 5 funcionan

### 🟡 PASO 6: AGREGAR BARBA.JS

- [ ] **vite.config.js**: Agregar `'node_modules/@barba/core/dist/barba.umd.js'`
- [ ] **Enqueue**: Quitar Barba CDN
- [ ] **Testing específico**:

  ```javascript

  console.log('Barba disponible:', typeof barba);
  console.log('Barba methods:', Object.keys(barba));

  ```

- [ ] **Verificar**: Navegación + todas las anteriores
- [ ] **Solo continuar si**: ✅ Las 6 funcionan

### 🟡 PASO 7: AGREGAR JQUERY VALIDATE

- [ ] **vite.config.js**: Agregar `'node_modules/jquery-validation/dist/jquery.validate.js'`
- [ ] **Enqueue**: Quitar jQuery Validate CDN (último CDN)
- [ ] **Testing específico**:

  ```javascript

  console.log('jQuery Validate:', typeof $.validator);
  console.log('$.fn.validate:', typeof $.fn.validate);
  // Test formulario temporal
  const testForm = $('<form><input required></form>');
  testForm.validate();
  console.log('Validación aplicada');

  ```

- [ ] **Verificar**: Validación + todas las anteriores
- [ ] **Solo continuar si**: ✅ Las 7 funcionan

### 🟢 PASO 8: TESTING FINAL COMPLETO

- [ ] **Verificar enqueue**: Solo `all.js` cargado, sin CDNs
- [ ] **Testing completo**: Con script de debugging
- [ ] **Testing funcional**:
  - Scroll suave (Lenis)
  - Animaciones (GSAP)
  - Sliders (Swiper)
  - Navegación (Barba)
  - Texto animado (SplitType)
  - Validación (jQuery Validate)
- [ ] **Performance**: Verificar mejora de rendimiento
- [ ] **Documentación**: Actualizar archivos de documentación

---

## 🔧 HERRAMIENTAS DE TESTING POR PASO

### Script de Testing Individual

Para cada paso, usar este template en consola:

```javascript

console.clear();
console.log('=== TESTING PASO X ===');

// Verificar librería recién agregada
console.log('Nueva librería:', typeof [LIBRERIA]);

// Verificar librerías anteriores siguen funcionando
console.log('jQuery:', typeof $);
console.log('Swiper:', typeof Swiper); // si ya se agregó
console.log('GSAP:', typeof gsap); // si ya se agregó
// etc...

// Test específico de la nueva librería
// [CÓDIGO DE TEST ESPECÍFICO]

console.log('=== RESULTADO ===');
console.log('✅ Todo funciona' || '❌ Error detectado');

```text
### Comandos entre Pasos

```bash

# Entre cada paso

npm run vite:build

# Abrir <http://localhost:8001>


# F12 → Console → Pegar script de test


# Verificar resultado antes de continuar

```

---

## 🚨 CRITERIOS DE ÉXITO/FALLO

### ✅ CONTINUAR AL SIGUIENTE PASO SI

- No hay errores en consola
- La librería nueva está disponible globalmente
- Las librerías anteriores siguen funcionando
- El functionality test específico pasa

### ❌ PARAR Y DEBUG SI

- Errores de JavaScript en consola
- Cualquier librería anterior deja de funcionar
- La nueva librería no está disponible
- Errores de sintaxis/concatenación

---

## 📝 NOTAS IMPORTANTES

1. **UN SOLO CAMBIO POR VEZ**: Solo modificar vite.config.js con una librería nueva
2. **TESTING INMEDIATO**: Después de cada cambio, build y test en navegador
3. **NO CONTINUAR CON ERRORES**: Si algo falla, arreglar antes de seguir
4. **DOCUMENTAR PROBLEMAS**: Anotar qué librería causa qué problema específico
5. **MANTENER CDN BACKUP**: Solo quitar CDN cuando local funcione 100%

---

## 🎯 OBJETIVO FINAL

Al final de este proceso tendremos:

- ✅ Bundle funcional con todas las librerías
- ✅ Identificación clara de problemas por librería
- ✅ Metodología reproducible para futuras librerías
- ✅ Sistema de debugging robusto
- ✅ Documentación precisa de la solución

---

### 🚀 EMPEZAR CON PASO 0: RESETEAR AL ESTADO BASE
