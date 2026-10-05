const URL_API = "../api/solicitudes.php";

const cuerpoTabla = document.querySelector("#tabla-solicitudes tbody");
const cajaMensaje = document.getElementById("mensaje");

function mostrarMensaje(texto) {
    cajaMensaje.textContent = texto;
}

function celda(texto) {
    const td = document.createElement("td");
    td.textContent = texto ?? "";
    return td;
}

function dibujarSolicitudes(solicitudes) {
    cuerpoTabla.replaceChildren();

    if (solicitudes.length === 0) {
        mostrarMensaje("No hay solicitudes registradas.");
        return;
    }

    solicitudes.forEach(function(solicitud) {
        const fila = document.createElement("tr");

        fila.append(
            celda(solicitud.id_solicitud),
            celda(solicitud.tipo),
            celda(solicitud.descripcion),
            celda(solicitud.laboratorio),
            celda(solicitud.fecha_solicitada),
            celda(solicitud.urgencia),
            celda(solicitud.estado),
            celda(solicitud.creadoPor)
        );

        cuerpoTabla.appendChild(fila);
    });
}

async function cargarSolicitudes() {
    try {
        const respuesta = await fetch(URL_API, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            },
            credentials: "same-origin"
        });

        const json = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(json.mensaje || "Error " + respuesta.status);
        }

        dibujarSolicitudes(json.datos.solicitudes);
    } catch (error) {
        mostrarMensaje("No se pudieron cargar las solicitudes: " + error.message);
    }
}

document.addEventListener("DOMContentLoaded", cargarSolicitudes);