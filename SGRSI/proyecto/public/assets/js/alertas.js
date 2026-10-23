async function cargarAlertas(url) {
    try {
        const respuesta = await fetch(url, {credentials:'same-origin'}); const json = await respuesta.json();
        if (!respuesta.ok || json.status !== 'success') throw new Error(json.message);
        const contenido = document.getElementById('contenido'); contenido.replaceChildren();
        for (const nombre in json.data) {
            const h = document.createElement('h2'); h.textContent = traducir(nombre); contenido.appendChild(h);
            const datos = json.data[nombre];
            if (nombre === 'referencia') { document.getElementById('fecha').value = datos.fecha; document.getElementById('hora').value = datos.hora; }
            if (datos !== null && typeof datos === 'object') {
                const tabla = document.createElement('table'); tabla.className = 'table';
                for (const clave in datos) { const fila = document.createElement('tr'); agregarCelda(fila, clave);
                    if (datos[clave] !== null && typeof datos[clave] === 'object') { for (const campo in datos[clave]) agregarCelda(fila, datos[clave][campo] ?? ''); }
                    else agregarCelda(fila, datos[clave]); tabla.appendChild(fila);
                }
                contenido.appendChild(tabla);
            } else { const valor = document.createElement('p'); valor.textContent = datos === null ? traducir('Sin datos medibles') : datos; contenido.appendChild(valor); }
        }
    } catch (error) { mostrarMensaje(error.message, true); }
}


document.getElementById('filtro').addEventListener('submit', e => { e.preventDefault(); cargarAlertas('api/alertas.php?fecha=' + encodeURIComponent(document.getElementById('fecha').value) + '&hora=' + encodeURIComponent(document.getElementById('hora').value)); });
cargarAlertas('api/alertas.php');
