/**
 * Sistema de Gestión de Formularios - Admin JavaScript
 * Manejo dinámico de campos de formulario en el editor
 */

jQuery(document).ready(function($) {
    let campoIndex = $('.campo-editor').length;
    
    
    // Inicializar sortable para reordenar campos
    initializeSortable();
    
    // Botón añadir campo
    $('#add-campo-btn').on('click', function() {
        añadirNuevoCampo();
    });
    
    /**
     * Inicializar funcionalidad sortable para reordenar campos
     */
    function initializeSortable() {
        $('#campos-container').sortable({
            handle: '.campo-drag-handle',
            placeholder: 'sortable-placeholder',
            opacity: 0.7,
            tolerance: 'pointer',
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            update: function(e, ui) {
                // Reindexar campos después de reordenar
                reindexarCampos();
            }
        });
    }
    
    /**
     * Añadir nuevo campo
     */
    function añadirNuevoCampo() {
        const template = $('#campo-template').html();
        const newCampo = template.replace(/\{\{INDEX\}\}/g, campoIndex);
        
        $('#campos-container').append(newCampo);
        
        // Inicializar el nuevo campo
        const newCampoElement = $('.campo-editor').last();
        newCampoElement.find('.campo-title').text('Nuevo Campo');
        newCampoElement.find('.campo-type-label').text('(text)');
        
        campoIndex++;
        
        // Scroll al nuevo campo
        newCampoElement[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Focus en el nombre del campo
        setTimeout(() => {
            newCampoElement.find('input[name*="[nombre]"]').focus();
        }, 300);
    }
    
    /**
     * Reindexar todos los campos después de reordenar
     */
    function reindexarCampos() {
        $('.campo-editor').each(function(index) {
            const $campo = $(this);
            
            // Actualizar data-index
            $campo.attr('data-index', index);
            
            // Actualizar todos los names de los inputs
            $campo.find('input, select, textarea').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                if (name && name.includes('[campos][')) {
                    const newName = name.replace(/\[campos\]\[\d+\]/, `[campos][${index}]`);
                    $input.attr('name', newName);
                }
            });
        });
    }
});

/**
 * Toggle mostrar/ocultar contenido del campo
 */
function toggleCampo(header) {
    const campo = header.closest('.campo-editor');
    campo.classList.toggle('collapsed');
}

/**
 * Eliminar campo
 */
function eliminarCampo(button) {
    if (confirm('¿Estás seguro de que quieres eliminar este campo?')) {
        const campo = button.closest('.campo-editor');
        campo.remove();
        
        // Reindexar después de eliminar
        jQuery(document).ready(function($) {
            $('.campo-editor').each(function(index) {
                const $campo = $(this);
                $campo.attr('data-index', index);
                
                $campo.find('input, select, textarea').each(function() {
                    const $input = $(this);
                    const name = $input.attr('name');
                    if (name && name.includes('[campos][')) {
                        const newName = name.replace(/\[campos\]\[\d+\]/, `[campos][${index}]`);
                        $input.attr('name', newName);
                    }
                });
            });
        });
    }
}

/**
 * Duplicar campo
 */
function duplicarCampo(button) {
    const campo = button.closest('.campo-editor');
    const clone = campo.cloneNode(true);
    
    // Actualizar el índice del campo clonado
    const newIndex = document.querySelectorAll('.campo-editor').length;
    clone.setAttribute('data-index', newIndex);
    
    // Actualizar nombres de inputs en el clon
    clone.querySelectorAll('input, select, textarea').forEach(function(input) {
        const name = input.getAttribute('name');
        if (name && name.includes('[campos][')) {
            const newName = name.replace(/\[campos\]\[\d+\]/, `[campos][${newIndex}]`);
            input.setAttribute('name', newName);
        }
    });
    
    // Actualizar el título del campo duplicado
    const titleElement = clone.querySelector('.campo-title');
    titleElement.textContent = titleElement.textContent + ' (Copia)';
    
    // Insertar después del campo original
    campo.parentNode.insertBefore(clone, campo.nextSibling);
    
    // Scroll al campo duplicado
    clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

/**
 * Actualizar tipo de campo - mostrar/ocultar opciones relevantes
 */
function updateCampoType(select) {
    const campo = select.closest('.campo-editor');
    const tipo = select.value;
    
    // Actualizar badge del tipo en el header
    const typeBadge = campo.querySelector('.campo-type-badge');
    if (typeBadge) {
        typeBadge.textContent = tipo.toUpperCase();
    }
    
    // Mostrar/ocultar contenedores según el tipo
    const placeholderContainer = campo.querySelector('.campo-placeholder-container');
    const opcionesContainer = campo.querySelector('.campo-opciones-container');
    
    // Resetear visibilidad
    placeholderContainer.style.display = 'none';
    opcionesContainer.style.display = 'none';
    
    // Mostrar/ocultar toggle compacto en el header
    const compactToggle = campo.querySelector('.toggle-switch-compact');
    
    // Configurar según tipo
    switch (tipo) {
        case 'titulo_seccion':
            if (compactToggle) compactToggle.style.display = 'none';
            break;
            
        case 'text':
        case 'email':
        case 'phone':
        case 'url':
        case 'textarea':
            placeholderContainer.style.display = 'block';
            if (compactToggle) compactToggle.style.display = 'flex';
            break;
            
        case 'select':
        case 'tags':
            opcionesContainer.style.display = 'block';
            if (compactToggle) compactToggle.style.display = 'flex';
            // Asegurar que hay al menos una opción
            const opcionesList = campo.querySelector('.opciones-list');
            if (opcionesList.children.length === 0) {
                añadirOpcion(opcionesContainer.querySelector('button'));
            }
            break;
            
        case 'checkbox':
            if (compactToggle) compactToggle.style.display = 'flex';
            break;
    }
}

/**
 * Actualizar título del campo en el header
 */
function updateCampoTitle(input) {
    const campo = input.closest('.campo-editor');
    const titulo = campo.querySelector('.campo-title');
    titulo.textContent = input.value || 'Nuevo Campo';
}

/**
 * Actualizar badge de ancho cuando cambia el select
 */
function updateCampoWidth(select) {
    const campo = select.closest('.campo-editor');
    const widthBadge = campo.querySelector('.campo-width-badge');
    if (widthBadge) {
        const widthLabels = {
            '3': '25%', '4': '33%', '6': '50%', 
            '8': '66%', '9': '75%', '12': '100%'
        };
        widthBadge.textContent = widthLabels[select.value] || select.value;
    }
}

/**
 * Actualizar badge de requerido cuando cambia el checkbox
 */
function updateCampoRequired(checkbox) {
    const campo = checkbox.closest('.campo-editor');
    const meta = campo.querySelector('.campo-meta');
    let requiredBadge = campo.querySelector('.campo-required-badge');
    
    if (checkbox.checked) {
        if (!requiredBadge) {
            requiredBadge = document.createElement('span');
            requiredBadge.className = 'campo-required-badge';
            requiredBadge.textContent = 'Obligatorio';
            meta.appendChild(requiredBadge);
        }
    } else {
        if (requiredBadge) {
            requiredBadge.remove();
        }
    }
}

/**
 * Añadir nueva opción a un campo select/tags
 */
function añadirOpcion(button) {
    const opcionesContainer = button.closest('.opciones-container');
    const opcionesList = opcionesContainer.querySelector('.opciones-list');
    const campoIndex = opcionesList.getAttribute('data-index');
    const opcionIndex = opcionesList.children.length;
    
    const nuevaOpcion = document.createElement('div');
    nuevaOpcion.className = 'opcion-item';
    nuevaOpcion.innerHTML = `
        <input type="text" 
               name="formulario_config[campos][${campoIndex}][opciones][${opcionIndex}][nombre]" 
               value="" 
               placeholder="Nombre de la opción" 
               class="regular-text">
        <input type="text" 
               name="formulario_config[campos][${campoIndex}][opciones][${opcionIndex}][valor]" 
               value="" 
               placeholder="Valor (opcional)" 
               class="regular-text">
        <button type="button" onclick="eliminarOpcion(this)" class="button-link" style="color: #d63638;">
            <span class="dashicons dashicons-minus"></span>
        </button>
    `;
    
    opcionesList.appendChild(nuevaOpcion);
    
    // Focus en el input de nombre
    nuevaOpcion.querySelector('input').focus();
}

/**
 * Eliminar opción de un campo select/tags
 */
function eliminarOpcion(button) {
    const opcionItem = button.closest('.opcion-item');
    const opcionesList = opcionItem.parentNode;
    
    opcionItem.remove();
    
    // Reindexar opciones
    Array.from(opcionesList.children).forEach((opcion, index) => {
        const inputs = opcion.querySelectorAll('input');
        inputs[0].name = inputs[0].name.replace(/\[opciones\]\[\d+\]/, `[opciones][${index}]`);
        inputs[1].name = inputs[1].name.replace(/\[opciones\]\[\d+\]/, `[opciones][${index}]`);
    });
}

/**
 * Configuración por defecto para nuevos formularios
 */
function cargarCamposPorDefecto() {
    if (document.querySelectorAll('.campo-editor').length === 0) {
        // Añadir campos por defecto si no hay ninguno
        const camposDefecto = [
            { tipo: 'text', nombre: 'Nombre', ancho: '6', requerido: true, placeholder: 'Tu nombre' },
            { tipo: 'text', nombre: 'Apellido', ancho: '6', requerido: true, placeholder: 'Tu apellido' },
            { tipo: 'email', nombre: 'Email', ancho: '6', requerido: true, placeholder: 'tu@email.com' },
            { tipo: 'phone', nombre: 'Teléfono', ancho: '6', requerido: false, placeholder: '+34 123 456 789' },
            { 
                tipo: 'select', 
                nombre: '¿Por qué nos contactas?', 
                ancho: '12', 
                requerido: true,
                opciones: [
                    { nombre: 'Información general', valor: 'info' },
                    { nombre: 'Solicitar presupuesto', valor: 'presupuesto' },
                    { nombre: 'Soporte técnico', valor: 'soporte' }
                ]
            },
            { tipo: 'textarea', nombre: 'Mensaje', ancho: '12', requerido: true, placeholder: 'Cuéntanos cómo podemos ayudarte...' }
        ];
        
        // Esta funcionalidad se puede activar si se desea pre-poblar formularios nuevos
        console.log('Formulario vacío detectado. Campos por defecto disponibles:', camposDefecto);
    }
}