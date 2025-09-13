# 🚀 TODO: Mejoras Barba.js Theme Amentum

**Fecha inicio:** 09/09/2025
**Estado:** 🟡 En planificación
**Prioridad:** Alta - Memory leaks y arquitectura no escalable

---

## 📋 **Plan General de Mejoras**

### 🎯 **Objetivo Principal**

Refactorizar sistema Barba.js eliminando duplicación, memory leaks y mejorando arquitectura para escalabilidad futura.

### 📊 **Métrica Objetivo**

- **Actual**: 6.5/10 → **Meta**: 9/10
- Memory leaks: ❌ → ✅ Eliminados
- Code duplication: ❌ → ✅ Eliminada
- Scalability: 3/10 → 9/10

---

## 🏗️ **FASE 1: Refactorización Crítica (OBLIGATORIO)**

*Tiempo estimado: 2-3 horas*
*Riesgo: Bajo - Mejoras sin cambio de arquitectura*

### 📝 **Tareas Fase 1**

#### ✅ 1.1 Eliminar Duplicación de Código

- [ ] **Archivo**: `barba-transitions.js`
- [ ] **Líneas**: 28-34 (once) y 104-111 (enter)
- [ ] **Acción**: Crear función `initPageModules()`
- [ ] **Testing**: Verificar navegación funcione igual

```javascript

// ✅ SOLUCIÓN
function initPageModules() {
    contentAnimation();
    callIfExists('inicializarEventosSwiper');
    autoPlayVideos();
}

```text
### 🧪 Test Checkpoint 1.1

```bash

# Navegación debe funcionar idéntico

1. Cargar página inicial → ✅ Funciones se ejecutan
2. Navegar a otra página → ✅ Funciones se ejecutan
3. Scroll, menú, videos → ✅ Todo funciona igual

```text
#### ✅ 1.2 Implementar Cleanup Básico

- [ ] **Archivo**: `barba-transitions.js`
- [ ] **Hook**: `beforeLeave()`
- [ ] **Objetivo**: Evitar memory leaks básicos

```javascript

// ✅ SOLUCIÓN
beforeLeave(data) {
    // Existente: menú cleanup

    // ✅ AGREGAR: Basic cleanup
    if (window.swiperInstances) {
        window.swiperInstances.forEach(swiper => swiper.destroy());
        window.swiperInstances = [];
    }

    document.querySelectorAll('video').forEach(video => {
        video.pause();
        video.currentTime = 0;
    });
}

```text
### 🧪 Test Checkpoint 1.2

```bash

# Abrir DevTools → Memory tab

1. Navegar 10 páginas → Memory no debe crecer indefinidamente
2. Videos pausan al cambiar página → ✅
3. Swipers no se acumulan → ✅

```text
#### ✅ 1.3 Documentar Delays

- [ ] **Archivo**: `barba-transitions.js` línea 102
- [ ] **Acción**: Explicar por qué 50ms delay

```javascript

// ✅ DOCUMENTAR
setTimeout(() => {
    // Wait for DOM to be fully ready after Barba container swap
    // 50ms ensures proper element availability for jQuery/vanilla selectors
    initPageModules();
}, 50);

```text
### 🧪 Test Checkpoint 1.3

```bash

# Testing de timing

1. Reducir delay a 0ms → ¿Rompe algo?
2. Si no rompe → eliminar delay
3. Si rompe → documentar qué necesita el delay

```text
### 🎯 **Entregable Fase 1**

- ✅ Eliminada duplicación código
- ✅ Memory leaks básicos solucionados
- ✅ Documentación mejorada
- ✅ **Testing completo** - navegación debe funcionar **idéntico**

---

## 🏗️ **FASE 2: Arquitectura Mejorada (RECOMENDADO)**

*Tiempo estimado: 4-6 horas*
*Riesgo: Medio - Cambios arquitectónicos*

### 📝 **Tareas Fase 2**

#### ✅ 2.1 Crear Module Manager Base

- [ ] **Archivo nuevo**: `assets/js/modules/module-manager.js`
- [ ] **Objetivo**: Sistema centralizado de módulos

```javascript

// ✅ CREAR ARCHIVO
export class AmentumModuleManager {
    constructor() {
        this.modules = new Map();
        this.isDebug = true; // Para development
    }

    register(name, moduleConfig) {
        if (!moduleConfig.init || !moduleConfig.destroy) {
            console.warn(`⚠️ Module ${name} missing init/destroy`);
        }
        this.modules.set(name, moduleConfig);
        return this; // chainable
    }

    async initAll() {
        const results = [];
        for (const [name, module] of this.modules) {
            try {
                await module.init?.();
                results.push(`✅ ${name}`);
                if (this.isDebug) console.log(`✅ ${name} initialized`);
            } catch (error) {
                results.push(`❌ ${name}: ${error.message}`);
                console.error(`❌ ${name} failed:`, error);
            }
        }
        return results;
    }

    async destroyAll() {
        for (const [name, module] of this.modules) {
            try {
                await module.destroy?.();
                if (this.isDebug) console.log(`🧹 ${name} cleaned`);
            } catch (error) {
                console.warn(`⚠️ ${name} cleanup failed:`, error);
            }
        }
    }
}

// Global instance
window.amentumModules = new AmentumModuleManager();

```text
### 🧪 Test Checkpoint 2.1

```javascript

// Test básico del manager
window.amentumModules
    .register('test', {
        init: () => console.log('test init'),
        destroy: () => console.log('test destroy')
    });

await window.amentumModules.initAll(); // ✅ debe mostrar "test init"
await window.amentumModules.destroyAll(); // ✅ debe mostrar "test destroy"

```text
#### ✅ 2.2 Refactorizar Módulo Swiper

- [ ] **Archivo nuevo**: `assets/js/modules/swiper-module.js`
- [ ] **Migrar**: Lógica de `inicializarEventosSwiper`

```javascript

// ✅ CREAR ARCHIVO
export const swiperModule = {
    instances: [],

    async init() {
        this.instances = []; // Reset array

        // Migrar lógica existente de inicializarEventosSwiper aquí
        const swiperElements = document.querySelectorAll('.swiper');

        swiperElements.forEach(element => {
            const swiperInstance = new Swiper(element, {
                // Tu configuración existente
            });
            this.instances.push(swiperInstance);
        });

        window.swiperInstances = this.instances; // Backward compatibility
    },

    async destroy() {
        this.instances.forEach(instance => {
            try {
                instance.destroy(true, true); // Remove DOM and event listeners
            } catch (error) {
                console.warn('Swiper destroy failed:', error);
            }
        });
        this.instances = [];
        window.swiperInstances = [];
    }
};

// Auto-register
if (window.amentumModules) {
    window.amentumModules.register('swiper', swiperModule);
}

```text
#### ✅ 2.3 Refactorizar Módulo Videos

- [ ] **Archivo nuevo**: `assets/js/modules/video-module.js`
- [ ] **Migrar**: Lógica de `autoPlayVideos`

```javascript

// ✅ CREAR ARCHIVO
export const videoModule = {
    videos: [],
    observers: [],

    async init() {
        // Migrar lógica de autoPlayVideos aquí
        this.videos = Array.from(document.querySelectorAll('video'));

        this.videos.forEach(video => {
            // Tu lógica existente de autoplay
        });
    },

    async destroy() {
        this.videos.forEach(video => {
            video.pause();
            video.currentTime = 0;
        });

        this.observers.forEach(observer => observer.disconnect());
        this.observers = [];
        this.videos = [];
    }
};

if (window.amentumModules) {
    window.amentumModules.register('videos', videoModule);
}

```text
#### ✅ 2.4 Refactorizar Módulo Animations

- [ ] **Archivo**: `assets/js/modules/animations.js` (modificar existente)
- [ ] **Agregar**: init/destroy methods

```javascript

// ✅ MODIFICAR EXISTENTE
// Al final del archivo agregar:

export const animationModule = {
    timelines: [],

    async init() {
        // Tu lógica existente de contentAnimation
        contentAnimation(); // Mantener función existente
    },

    async destroy() {
        // Kill any GSAP timelines
        this.timelines.forEach(tl => tl.kill());
        this.timelines = [];

        // Reset any GSAP transforms
        gsap.set('[data-animation]', { clearProps: 'all' });
    }
};

if (window.amentumModules) {
    window.amentumModules.register('animations', animationModule);
}

```text
#### ✅ 2.5 Integrar Module Manager en Barba

- [ ] **Archivo**: `barba-transitions.js`
- [ ] **Reemplazar**: `initPageModules()` por module manager

```javascript

// ✅ REEMPLAZAR initPageModules()
async function initPageModules() {
    if (window.amentumModules) {
        const results = await window.amentumModules.initAll();
        console.log('🚀 Modules initialized:', results);
    } else {
        // Fallback a método anterior
        contentAnimation();
        callIfExists('inicializarEventosSwiper');
        autoPlayVideos();
    }
}

// ✅ MEJORAR beforeLeave
beforeLeave(data) {
    // Existing menu logic...

    // ✅ AGREGAR: Module cleanup
    if (window.amentumModules) {
        window.amentumModules.destroyAll();
    }
}

```text
### 🧪 Test Checkpoint 2.5

```bash

# Test completo de navegación con Module Manager

1. Primera carga → ✅ Todos módulos se inicializan
2. Navegar página → ✅ Cleanup + reinit automático
3. DevTools console → ✅ Ver logs de módulos
4. Funcionalidad → ✅ Todo funciona igual que antes

```text
### 🎯 **Entregable Fase 2**

- ✅ Module Manager implementado
- ✅ 3 módulos refactorizados (swiper, videos, animations)
- ✅ Integración completa con Barba.js
- ✅ Backward compatibility mantenida
- ✅ **Testing exhaustivo** - funcionalidad idéntica + mejor performance

---

## 🏗️ **FASE 3: Optimizaciones Avanzadas (OPCIONAL)**

*Tiempo estimado: 6-8 horas*
*Riesgo: Bajo - Solo mejoras adicionales*

### 📝 **Tareas Fase 3**

#### ✅ 3.1 Sistema de Eventos Personalizado

- [ ] **Archivo nuevo**: `assets/js/modules/event-system.js`
- [ ] **Objetivo**: Comunicación entre módulos

#### ✅ 3.2 Lazy Loading de Módulos

- [ ] **Objetivo**: Cargar módulos solo cuando se necesiten
- [ ] **Beneficio**: Performance inicial mejorada

#### ✅ 3.3 Performance Monitoring

- [ ] **Objetivo**: Métricas de tiempo de init/destroy
- [ ] **Herramienta**: Performance API

#### ✅ 3.4 Testing Unitario Básico

- [ ] **Framework**: Jest o testing simple
- [ ] **Objetivo**: Tests para module manager

---

## 📊 **Sistema de Validación por Fase**

### 🧪 **Tests Obligatorios Pre-Deploy**

#### Test Suite Fase 1

```bash

# Manual testing checklist

□ Navegación home → about → contact → home funciona
□ Menú móvil abre/cierra correctamente
□ Videos se reproducen automáticamente
□ Swipers funcionan en todas las páginas
□ No hay errores en consola
□ Memory tab no muestra crecimiento indefinido

```text
#### Test Suite Fase 2

```bash

# Module Manager testing

□ Console muestra logs de módulos inicializándose
□ Todos los módulos se registran correctamente
□ Destroy/init funciona en cada navegación
□ Funcionalidad 100% idéntica a versión anterior
□ Performance igual o mejor que antes

```text
#### Performance Benchmarks

```javascript

// Medir tiempos de inicialización
console.time('modules-init');
await window.amentumModules.initAll();
console.timeEnd('modules-init'); // Debe ser < 100ms

```text
### 🚨 **Criterios de Rollback**

Si cualquiera de estos falla → **ROLLBACK inmediato**:

- [ ] Navegación rota
- [ ] Menú no funciona
- [ ] Videos no se reproducen
- [ ] Swipers rotos
- [ ] Errores JavaScript en console
- [ ] Performance significativamente peor

---

## 📅 **Timeline Sugerido**

### **Semana 1: Fase 1 (Crítica)**

- **Lunes**: Eliminar duplicación + testing
- **Martes**: Cleanup básico + testing
- **Miércoles**: Buffer día + documentación

### **Semana 2: Fase 2 (Arquitectura)**

- **Lunes**: Module Manager + Swiper module
- **Martes**: Video + Animation modules
- **Miércoles**: Integración Barba + testing
- **Jueves**: Testing exhaustivo
- **Viernes**: Deploy + monitoreo

### **Semana 3: Fase 3 (Opcional)**

- Solo si Fases 1-2 están 100% estables

---

## 🎯 **Métricas de Éxito**

### **Antes → Después**

- **Code Duplication**: ❌ Alta → ✅ Eliminada
- **Memory Management**: ❌ Leaks → ✅ Clean
- **Maintainability**: ❌ 4/10 → ✅ 9/10
- **Scalability**: ❌ 3/10 → ✅ 9/10
- **Performance**: ⚠️ 6/10 → ✅ 8/10
- **Developer Experience**: ⚠️ 5/10 → ✅ 9/10

### **Indicadores Técnicos**

```javascript

// Memory usage debe mantenerse estable
Performance.measure('memory-stable', 'navigation-start', 'navigation-end');

// Module init time debe ser rápido
console.time('module-init'); // < 100ms target

// Console logs limpios
0 errors, 0 warnings en navegación normal

```text
---

## 🚀 **Getting Started**

### **Paso 1: Backup**

```bash

# Crear backup antes de comenzar

git add .
git commit -m "Backup antes de refactorización Barba.js"
git checkout -b refactor/barba-improvements

```text
### **Paso 2: Setup Desarrollo**

```bash

# Asegurar entorno de desarrollo

./dev.sh up

# Abrir DevTools → Console + Memory tabs


# Tener navegación lista para testing

```text
### **Paso 3: Comenzar Fase 1**

```bash

# Editar barba-transitions.js


# Implementar initPageModules()


# Test inmediato después de cada cambio

```

---

## 📞 **Soporte y Dudas**

- **Rollback plan**: Git branches por cada fase
- **Testing**: Manual testing obligatorio en cada paso
- **Performance**: DevTools Memory tab monitoring
- **Dudas técnicas**: Documentación en BARBA_ANALYSIS.md

---

**💡 Recuerda**: Cada cambio debe ser **incremental** y **probado inmediatamente**. Si algo no funciona, rollback y analizar antes de continuar.

**🎯 Objetivo final**: Sistema robusto, mantenible y sin memory leaks, pero funcionalmente **idéntico** al actual.
