# Sistema de Bloques Modulares Amentum

## Arquitectura

Cada bloque es completamente independiente y autosuficiente. Cada carpeta de bloque contiene:

```
blocks/
├── shared/
│   └── variables.css          # Variables CSS globales
├── [nombre-bloque]/
│   ├── block.php             # Registro, renderizado y enqueue del bloque
│   ├── style.css             # Estilos del frontend
│   ├── editor.js             # Script del editor Gutenberg
│   └── README.md             # Documentación del bloque (opcional)
└── README.md                 # Este archivo
```

## Bloques Disponibles

- **hero/** - Sección hero con título, subtítulo y botones
- **servicios/** - Grid de servicios con iconos SVG
- **proyectos/** - Listado dinámico de custom post type 'projects'
- **magazine/** - Listado dinámico de custom post type 'magazine'
- **testimonios/** - Grid de testimonios estáticos
- **contacto/** - Formulario de contacto con información

## Carga Automática

Los bloques se cargan automáticamente mediante `/inc/blocks-loader.php` que:

1. Include todos los archivos `block.php` de cada carpeta
2. Cada `block.php` se autoregistra usando `add_action('init')`
3. Cada bloque gestiona sus propios assets con `has_block()` para carga condicional

## Ventajas de esta Arquitectura

✅ **Completamente modular** - Cada bloque es independiente
✅ **Fácil mantenimiento** - Un bloque por carpeta con toda su lógica
✅ **Carga condicional** - Solo se cargan CSS/JS si el bloque está en uso
✅ **Escalable** - Agregar nuevo bloque = crear nueva carpeta
✅ **Sin conflictos** - Cada bloque maneja sus propios hooks y assets

## Agregar Nuevo Bloque

1. Crear carpeta `blocks/nuevo-bloque/`
2. Crear `block.php` con registro y renderizado
3. Crear `style.css` con estilos
4. Crear `editor.js` con interfaz de Gutenberg
5. Agregar carpeta al array en `blocks-loader.php`

## Variables CSS Globales

Todas disponibles en `/blocks/shared/variables.css`:

- `--color-primary`
- `--color-secondary` 
- `--color-accent`
- `--spacing-section`
- `--border-radius`
- etc.