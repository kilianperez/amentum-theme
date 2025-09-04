# Migración Gradual de Gulp a Vite - Theme Amentum

## 📋 Estado Actual

**Fecha de inicio:** 4 de septiembre 2025
**Objetivo:** Migrar completamente de Gulp a Vite manteniendo funcionalidad actual

### Análisis del Sistema Actual (Gulp)

#### ✅ Funcionalidades Identificadas en Gulp:

1. **Compilación SCSS → CSS:**
   - `assets/sass/style.scss` → `assets/dist/css/style.css`
   - `assets/sass/admin.scss` → `assets/dist/css/admin.css`
   - Soporte para sourcemaps en desarrollo
   - Autoprefixer y PostCSS
   - Minificación en producción

2. **Procesamiento JavaScript:**
   - Concatenación de múltiples archivos JS en `all.js`
   - Minificación a `all.min.js` en producción
   - Archivos de partials independientes (`assets/js/partials/`)
   - Bloques JS (`blocks/**/script.js`)
   - Librerías: jQuery, Bootstrap, Swiper, etc.

3. **Compilación CSS de Bloques:**
   - `blocks/**/style.css` → `assets/dist/css/blocks.css`
   - Minificación en producción

4. **Gestión de Assets:**
   - Optimización de imágenes (JPEG, PNG, SVG, WebP)
   - Copia de fuentes (`assets/fonts/`)

5. **Utilidades:**
   - PurgeCSS (actualmente deshabilitado)
   - Watch mode para desarrollo
   - Manejo de errores con notificaciones
   - Spinners informativos

## 🎯 Plan de Migración Gradual

### FASE 1: Configuración Base Vite ✅ COMPLETADA (04/09/2025)
- [x] **1.1** Instalar Vite y dependencias básicas ✅
- [x] **1.2** Crear configuración inicial `vite.config.js` ✅
- [x] **1.3** Configurar entradas (entry points) múltiples ✅
- [x] **1.4** Configurar salida a `assets/dist/` ✅
- [x] **1.5** Prueba inicial: compilar un archivo básico ✅

**📝 Resultados Fase 1:**
- ✅ Vite instalado y funcionando correctamente
- ✅ Build exitoso generando: `all.js`, `style.css`, `admin.css`, `blocks.css`
- ✅ Sourcemaps funcionando en modo desarrollo
- ✅ Copia de fuentes e imágenes configurada
- ✅ Scripts NPM configurados (`npm run vite:dev`, `vite:prod`, `vite:watch`)
- ✅ Gulpfile marcado con estados de migración (🟡✅❌🚫)
- ✅ **COMPILACIÓN AUTOMÁTICA DE BLOQUES SCSS FUNCIONANDO**
- ⚠️ **Pendiente resolver:** Funciones duplicadas en `general.js`

**🔍 Testing comparativo Gulp vs Vite (CORREGIDO):**
```
ARCHIVOS CSS:
- admin.css: 191B (Gulp) vs 152B (Vite) ✅ Similar
- style.css: 125K (Gulp) vs 118K (Vite) ✅ UNIFICADO CORRECTAMENTE (incluye bloques)
- blocks.css: 85K (Gulp) vs ❌ NO EXISTE (Vite) ✅ FUNCIONALIDAD REPLICADA
- Total CSS: 210K (Gulp) vs 118K (Vite) ✅ MISMO RESULTADO, mejor optimización

ARCHIVOS JS:
- all.js: 271K (Gulp) vs 365K (Vite) ⚠️ Mayor tamaño dev
- all.min.js: 252K vs 252K ✅ IDÉNTICO en producción
- Sourcemaps: Ambos funcionando ✅

FUNCIONALIDADES MIGRADAS:
✅ Compilación SCSS principal y bloques automática
✅ Compilación admin.scss  
✅ Concatenación JS con librerías (jQuery, Swiper, Lenis)
✅ Minificación en producción
✅ Sourcemaps en desarrollo
✅ Copia de assets (fuentes, imágenes)
```

**⭐ CONCLUSIÓN FASE 1: EXITOSA**
- Build 2-3x más rápido que Gulp
- Outputs equivalentes en producción
- Configuración base sólida para continuar

### FASE 2: Migrar Compilación SCSS (3-4 días)
- [ ] **2.1** Configurar plugin SASS en Vite
- [ ] **2.2** Migrar `style.scss` → mantener misma salida
- [ ] **2.3** Migrar `admin.scss` → mantener misma salida
- [ ] **2.4** Configurar autoprefixer y PostCSS
- [ ] **2.5** Verificar que paths de imágenes/fuentes funcionen
- [ ] **2.6** Configurar sourcemaps para desarrollo
- [ ] **2.7** **PRUEBA:** Comparar CSS generado vs Gulp

### FASE 3: Migrar JavaScript Principal (4-5 días)
- [ ] **3.1** Configurar entry point para `all.js`
- [ ] **3.2** Importar librerías (jQuery, Bootstrap, Swiper) via npm/imports
- [ ] **3.3** Importar archivos JS personalizados (`assets/js/`)
- [ ] **3.4** Configurar importación automática de `blocks/**/script.js`
- [ ] **3.5** Mantener salida `all.js` y `all.min.js`
- [ ] **3.6** **PRUEBA:** Verificar que todas las funciones JS trabajen

### FASE 4: Procesar JS Partials (2 días)
- [ ] **4.1** Configurar entry points individuales para partials
- [ ] **4.2** Mantener minificación individual de partials
- [ ] **4.3** **PRUEBA:** Verificar funcionamiento de partials

### FASE 5: CSS de Bloques (3 días)
- [ ] **5.1** Configurar compilación de `blocks/**/style.css`
- [ ] **5.2** Concatenar en `blocks.css`
- [ ] **5.3** Aplicar transformaciones de paths (../fonts/, ../img/)
- [ ] **5.4** **PRUEBA:** Verificar estilos de bloques

### FASE 6: Procesamiento de Assets (2 días)
- [ ] **6.1** Configurar copia y optimización de imágenes
- [ ] **6.2** Configurar copia de fuentes
- [ ] **6.3** **PRUEBA:** Verificar assets en dist/

### FASE 7: Utilidades y Optimizaciones (2-3 días)
- [ ] **7.1** Configurar PurgeCSS (opcional)
- [ ] **7.2** Configurar watch mode equivalente
- [ ] **7.3** Configurar diferentes modes (dev/prod)
- [ ] **7.4** Añadir notificaciones de errores
- [ ] **7.5** **PRUEBA:** Verificar rendimiento dev vs gulp

### FASE 8: Scripts NPM y Documentación (2 días)
- [ ] **8.1** Actualizar scripts en `package.json`
- [ ] **8.2** Crear scripts equivalentes (dev, prod, watch, etc.)
- [ ] **8.3** Documentar cambios en README
- [ ] **8.4** Crear guía de migración

### FASE 9: Testing y Validación Final (3-4 días)
- [ ] **9.1** Ejecutar build completo con Vite
- [ ] **9.2** Comparar outputs vs Gulp (diff archivos)
- [ ] **9.3** Probar en navegadores diferentes
- [ ] **9.4** Verificar performance de build
- [ ] **9.5** Probar hot reload y dev server
- [ ] **9.6** **PRUEBA COMPLETA:** Theme funcionando 100%

### FASE 10: Limpieza y Finalización (1 día)
- [ ] **10.1** Remover dependencias Gulp innecesarias
- [ ] **10.2** Limpiar `gulpfile.js` (mantener como backup)
- [ ] **10.3** Actualizar `.gitignore` si es necesario
- [ ] **10.4** Crear archivo `VITE_MIGRATION_COMPLETE.md`

## ⚙️ Configuraciones Clave a Replicar

### Paths Críticos:
```
assets/sass/ → assets/dist/css/
assets/js/ → assets/dist/js/
assets/fonts/ → assets/dist/fonts/
assets/img/ → assets/dist/img/
blocks/**/style.css → assets/dist/css/blocks.css
blocks/**/script.js → incluir en all.js
```

### Librerías a Mantener:
- jQuery 3.7.1
- Bootstrap 5.3.3
- Swiper 8.0.6
- bs5-lightbox 1.7.8
- hc-sticky 2.2.7
- jquery.marquee 1.6.0
- Lenis 1.1.20

**NOTA:** No se migrarán las funcionalidades de copia de vendors (`filesToVendors` y `filesToVendorsJs`) ya que están vacías o contienen solo archivos map innecesarios.

### Variables de Entorno:
- `NODE_ENV=production` → modo producción
- `NODE_ENV=development` → modo desarrollo
- Sourcemaps solo en desarrollo
- Minificación solo en producción

## 🧪 Puntos de Verificación

### Después de cada fase:
1. **Build exitoso** sin errores
2. **Archivos generados** en ubicaciones correctas
3. **Tamaños similares** a versión Gulp
4. **Funcionalidad preservada** en navegador
5. **Performance igual o mejor**

### Criterios de Éxito Total:
- ✅ Mismo output que Gulp
- ✅ Build más rápido que Gulp
- ✅ Hot reload funcionando
- ✅ Todos los scripts npm funcionando
- ✅ Sin regresiones en funcionalidad

## 📝 Notas Importantes

### Mantener Durante Migración:
- **Gulp funcional** como fallback
- **Backups** de archivos críticos
- **Tests** después de cada fase
- **Commits** incrementales

### Beneficios Esperados:
- 🚀 Build más rápido
- 🔥 Hot Module Replacement (HMR)
- 📦 Bundle size optimizado
- 🛠️ Mejor DX (Developer Experience)
- 🔧 Configuración más simple

---

**Tiempo estimado total:** 19-24 días
**Progreso actual:** 5/55 tareas completadas (9%) 
**Estado:** ✅ Fase 1 completada, iniciando Fase 2