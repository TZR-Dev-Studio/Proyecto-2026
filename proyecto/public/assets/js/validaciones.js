function campoVacio(valor) {
    return valor.trim() === '';
}

function longitudMinima(valor, minimo) {
    return valor.trim().length >= minimo;
}

function mostrarError(input, mensaje) {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    const feedback = input.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = mensaje;
    }
}

function mostrarValido(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
}

function limpiarValidacion(input) {
    input.classList.remove('is-valid', 'is-invalid');
}