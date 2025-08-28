// spinner.js
import ora from 'ora';

let spinner;
let startMessage = "Tarea en progreso...";
let successMessage = "Tarea completada.";
let errorMessage = "Error durante la tarea.";
let spinnerType = "dots";

/**
 * Maneja la animación del spinner y los mensajes en la terminal.
 *
 * Escucha mensajes del proceso principal para iniciar, finalizar y manejar el spinner.
 */
process.on('message', (msg) => {
    switch (msg.type) {
        case 'start':
            // Configura el spinner con el mensaje inicial y tipo especificado
            startMessage = msg.startMessage || startMessage;
            successMessage = msg.successMessage || successMessage;
            errorMessage = msg.errorMessage || errorMessage;
            spinnerType = msg.spinnerType || spinnerType;
            spinner = ora({ text: startMessage, spinner: spinnerType }).start();
            break;
        case 'succeed':
            // Muestra un mensaje de éxito y detiene el spinner
            if (spinner) {
                spinner.succeed(successMessage);
                spinner.stop();
            }
            process.send({ type: 'done', exitCode: 0 });
            process.exit(0);
            break;
        case 'fail':
            // Muestra un mensaje de error y detiene el spinner
            if (spinner) {
                spinner.fail(errorMessage);
                spinner.stop();
            }
            process.send({ type: 'done', exitCode: 1 });
            process.exit(1);
            break;
        case 'finish':
            // Solo detiene el spinner
            if (spinner) {
                spinner.stop();
            }
            process.send({ type: 'done', exitCode: 0 });
            process.exit(0);
            break;
        default:
            console.error('Mensaje no reconocido:', msg);
            process.send({ type: 'done', exitCode: 1 });
            process.exit(1);
            break;
    }
});
