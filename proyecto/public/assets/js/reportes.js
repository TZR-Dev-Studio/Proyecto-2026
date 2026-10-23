document.getElementById('filtro').addEventListener('submit', async evento => {
    evento.preventDefault();
    try {
        const respuesta = await fetch('api/reportes.php?' + 'desde=' + encodeURIComponent(document.getElementById('desde').value) + '&' + 'hasta=' + encodeURIComponent(document.getElementById('hasta').value), {credentials:'same-origin'}); const json = await respuesta.json();
        if (!respuesta.ok || json.status !== 'success') throw new Error(json.message);
        const contenido = document.getElementById('contenido'); contenido.replaceChildren();
        for (const nombre in json.data) {
            const h = document.createElement('h2'); h.textContent = traducir(nombre); contenido.appendChild(h);
            const datos = json.data[nombre];
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
});
