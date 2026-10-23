async function cargar() {
    const id = document.getElementById('equipo_id').value;
    const datos = (await llamarApi('historial_equipo', 'GET', null, id)).data;
    const contenedor = document.getElementById('contenido');
    for (const seccion of ['equipo', 'prestamos', 'tickets', 'diagnosticos', 'cambios', 'cambios_tickets']) {
        const h = document.createElement('h2'); h.textContent = traducir(seccion); contenedor.appendChild(h);
        const filas = Array.isArray(datos[seccion]) ? datos[seccion] : [datos[seccion]];
        for (const fila of filas) { const registro = document.createElement('dl');
            for (const campo in fila) { const etiqueta = document.createElement('dt'); etiqueta.textContent = traducir(campo); const valor = document.createElement('dd'); if (campo === 'detalle' && fila[campo]) { const anteriores = JSON.parse(fila[campo]); for (const nombre in anteriores) { const linea = document.createElement('p'); linea.textContent = traducir(nombre) + ': ' + (anteriores[nombre] ?? ''); valor.appendChild(linea); } } else valor.textContent = fila[campo] ?? ''; registro.append(etiqueta, valor); }
            contenedor.appendChild(registro);
        }
    }
}
cargar().catch(error => mostrarMensaje(error.message, true));
