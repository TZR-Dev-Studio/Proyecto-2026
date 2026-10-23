// Fetch y lectura JSON: mismo procedimiento que usuarios.js del profesor.
async function llamarApi(recurso, metodo = 'GET', datos = null, id = null) {
    let url = 'api/' + recurso + '.php';
    if (id !== null) url += '?id=' + encodeURIComponent(id);
    const opciones = {method: metodo, credentials: 'same-origin', headers: {}};
    if (metodo !== 'GET') {
        opciones.headers['Content-Type'] = 'application/json';
        opciones.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').content;
        opciones.body = JSON.stringify(datos || {});
    }
    const respuesta = await fetch(url, opciones);
    const json = await respuesta.json();
    if (!respuesta.ok || json.status !== 'success') throw new Error(json.message || 'No se pudo completar la operación.');
    return json;
}
function mostrarMensaje(mensaje, error = false) {
    const salida = document.getElementById('mensaje');
    salida.textContent = typeof traducir === 'function' ? traducir(mensaje) : mensaje;
    salida.className = error ? 'alert alert-danger' : 'alert alert-success';
}
function agregarCelda(fila, valor) {
    const celda = document.createElement('td');
    celda.textContent = valor === null ? '' : valor;
    fila.appendChild(celda);
}
function botonFila(fila, texto, accion) {
    const boton = document.createElement('button');
    boton.type = 'button'; boton.className = 'btn btn-sm btn-outline-primary m-1';
    boton.textContent = typeof traducir === 'function' ? traducir(texto) : texto;
    boton.addEventListener('click', accion); fila.appendChild(boton);
}
