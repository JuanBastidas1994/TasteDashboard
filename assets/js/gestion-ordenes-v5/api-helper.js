// Manejo de respuestas de la API de gestión de órdenes.
// Uso: fetch(...).then(apiJson).then(response => {...}).catch(error => messageDone(mensajeErrorApi(error), 'error'));

// Lee la respuesta como texto y la parsea. Si no es JSON válido (fatal de PHP, HTML de error,
// respuesta vacía, 502/504...) lanza un error con el status HTTP y el inicio de lo que respondió el servidor.
function apiJson(res) {
    return res.text().then(texto => {
        try {
            return JSON.parse(texto);
        } catch (e) {
            const err = new Error('Respuesta inválida del servidor');
            err.apiStatus = res.status;
            err.apiUrl = res.url;
            err.apiBody = (texto || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim().substring(0, 300);
            console.error('Respuesta no JSON de ' + res.url, res.status, texto);
            throw err;
        }
    });
}

// Mensaje legible para mostrar en el modal (SweetAlert) a partir del error del catch.
function mensajeErrorApi(error, fallback) {
    fallback = fallback || 'Ocurrió un error';

    if (error && error.apiStatus !== undefined) {
        let msg = fallback + ': el servidor respondió algo inesperado (HTTP ' + error.apiStatus + ')';
        if (error.apiBody) {
            msg += ' — "' + error.apiBody + '"';
        } else {
            msg += ' sin contenido';
        }
        return msg;
    }
    // fetch rechaza con TypeError cuando no hay red, CORS o el servidor no responde
    if (error instanceof TypeError) {
        return fallback + ': no se pudo conectar con el servidor, revisa tu conexión a internet e inténtalo de nuevo';
    }
    if (error && error.message) {
        return fallback + ': ' + error.message;
    }
    return fallback;
}
