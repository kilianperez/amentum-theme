/**
 * DEBUG LIVE - Se concatena al final del bundle para debugging en tiempo real
 * Este archivo permite debug directo desde la consola del navegador
 */

// Debug global para acceder desde consola
window.amentumDebug = {
    
    // Info del paso actual
    paso: 5,
    descripcion: 'PASO 5 FINAL: ¡100% MIGRADO! Todas las librerías locales en un solo bundle',
    
    // Verificar estado de librerías
    checkLibraries: function() {
        console.log(`🔍 DEBUG PASO ${this.paso}: ${this.descripcion}`);
        console.log('='.repeat(60));
        
        const libs = {
            'jQuery (Local)': {
                available: typeof $ !== 'undefined',
                version: typeof $ !== 'undefined' ? $.fn?.jquery : 'N/A',
                test: () => {
                    if (typeof $ === 'undefined') return false;
                    try {
                        $('body').length;
                        return true;
                    } catch (e) {
                        return false;
                    }
                }
            },
            'Swiper (Local)': {
                available: typeof Swiper !== 'undefined',
                version: typeof Swiper !== 'undefined' ? Swiper.version : 'N/A',
                test: () => typeof Swiper === 'function'
            },
            'Lenis (Local)': {
                available: typeof Lenis !== 'undefined',
                version: 'No disponible',
                test: () => {
                    if (typeof Lenis === 'undefined') return false;
                    try {
                        const test = new Lenis({ duration: 0.1 });
                        test.destroy();
                        return true;
                    } catch (e) {
                        return false;
                    }
                }
            },
            'GSAP (Local)': {
                available: typeof gsap !== 'undefined',
                version: typeof gsap !== 'undefined' ? gsap.version : 'N/A',
                test: () => {
                    if (typeof gsap === 'undefined') return false;
                    try {
                        gsap.set('body', { opacity: 1 });
                        return true;
                    } catch (e) {
                        return false;
                    }
                }
            },
            'ScrollTrigger (Local)': {
                available: typeof gsap !== 'undefined' && !!gsap.ScrollTrigger,
                version: typeof gsap !== 'undefined' && gsap.ScrollTrigger ? 'Disponible' : 'N/A',
                test: () => typeof gsap !== 'undefined' && !!gsap.ScrollTrigger
            },
            'SplitType (Local)': {
                available: typeof SplitType !== 'undefined',
                version: typeof SplitType !== 'undefined' ? 'v0.3.4' : 'N/A',
                test: () => typeof SplitType === 'function'
            },
            'Barba (Local)': {
                available: typeof barba !== 'undefined',
                version: typeof barba !== 'undefined' ? 'v2.9.7' : 'N/A',
                test: () => typeof barba !== 'undefined' && typeof barba.init === 'function'
            },
            'jQuery Validate (Local)': {
                available: typeof $ !== 'undefined' && !!$.validator,
                version: typeof $ !== 'undefined' && $.validator ? 'v2.6.4' : 'N/A',
                test: () => typeof $ !== 'undefined' && !!$.validator
            }
        };
        
        let passedTests = 0;
        let totalTests = Object.keys(libs).length;
        
        Object.keys(libs).forEach(libName => {
            const lib = libs[libName];
            const available = lib.available;
            const version = lib.version;
            const testPassed = lib.test();
            
            if (testPassed) passedTests++;
            
            console.log(
                `${available ? '✅' : '❌'} ${libName}: ${available ? 'Disponible' : 'No disponible'}`,
                version !== 'N/A' ? `(v${version})` : '',
                `| Test: ${testPassed ? '✅ PASS' : '❌ FAIL'}`
            );
        });
        
        console.log('-'.repeat(60));
        console.log(`📊 Resultado: ${passedTests}/${totalTests} librerías funcionando`);
        
        if (passedTests === totalTests) {
            console.log(`🎉 PASO ${this.paso} COMPLETADO - Listo para siguiente paso`);
        } else {
            console.log(`❌ PASO ${this.paso} INCOMPLETO - Hay problemas que resolver`);
        }
        
        return passedTests === totalTests;
    },
    
    // Info rápida del bundle actual
    bundleInfo: function() {
        const scripts = Array.from(document.querySelectorAll('script[src]'));
        const localScript = scripts.find(s => s.src.includes('all.js'));
        const cdnScripts = scripts.filter(s => s.src.includes('cdnjs.cloudflare.com') || s.src.includes('unpkg.com'));
        
        console.log('📦 INFO DEL BUNDLE:');
        console.log(`Local: ${localScript ? localScript.src.split('/').pop() : 'No encontrado'}`);
        console.log(`CDN Scripts: ${cdnScripts.length}`);
        cdnScripts.forEach((script, i) => {
            const url = script.src;
            let lib = 'Desconocido';
            if (url.includes('gsap')) lib = 'GSAP';
            else if (url.includes('split-type')) lib = 'SplitType';
            else if (url.includes('barba')) lib = 'Barba';
            else if (url.includes('validate') || url.includes('Validation')) lib = 'jQuery Validate';
            console.log(`  ${i+1}. ${lib}: ${url}`);
        });
    },
    
    // Test específico para jQuery
    testJQuery: function() {
        console.log('🧪 TEST ESPECÍFICO JQUERY:');
        if (typeof $ === 'undefined') {
            console.log('❌ jQuery no disponible');
            return false;
        }
        
        try {
            console.log('✅ Versión:', $.fn.jquery);
            console.log('✅ Selector body:', $('body').length, 'elementos');
            console.log('✅ Document ready:', typeof $(document).ready === 'function' ? 'Disponible' : 'No disponible');
            console.log('✅ CSS manipulation:', $('body').css('display'));
            return true;
        } catch (e) {
            console.log('❌ Error en test jQuery:', e.message);
            return false;
        }
    },
    
    // Test rápido de todas las librerías
    quickTest: function() {
        console.clear();
        console.log(`🚀 QUICK TEST PASO ${this.paso}`);
        this.bundleInfo();
        console.log('');
        this.checkLibraries();
        console.log('\n💡 Comandos disponibles:');
        console.log('- amentumDebug.checkLibraries() - Verificar todas las librerías');
        console.log('- amentumDebug.testJQuery() - Test específico de jQuery');
        console.log('- amentumDebug.bundleInfo() - Info del bundle actual');
        console.log('- amentumDebug.quickTest() - Este test completo');
    }
};

// Auto-ejecutar quick test si estamos en modo debug
if (window.location.search.includes('debug=1') || window.location.hash.includes('debug')) {
    setTimeout(() => {
        console.log('🔍 Auto-debug activado');
        window.amentumDebug.quickTest();
    }, 1000);
}

// Log simple de inicialización
console.log(`✅ Debug Live cargado - Paso ${window.amentumDebug.paso} | Usar: amentumDebug.quickTest()`);