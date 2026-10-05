const URL_API = "../api/tickets.php";

const cuerpoTabla = document.querySelector("#tabla-tickets tbody");
const cajaMensaje = document.getElementById("mensaje");

function mostrarMensaje(texto) {
    cajaMensaje.textContent = texto;
}

function celda(texto) {
    const td = document.createElement("td");
    td.textContent = texto ?? "";
    return td;
}

function dibujarTickets(tickets) {
    cuerpoTabla.replaceChildren();

    if (tickets.length === 0) {
        mostrarMensaje("No hay tickets registrados.");
        return;
    }

    tickets.forEach(function(ticket) {
        const fila = document.createElement("tr");

        fila.append(
            celda(ticket.id_ticket),
            celda(ticket.descripcion),
            celda(ticket.laboratorio),
            celda(ticket.prioridad),
            celda(ticket.fecha_inicio),
            celda(ticket.fecha_limite),
            celda(ticket.estado),
            celda(ticket.creadoPor)
        );

        cuerpoTabla.appendChild(fila);
    });
}

async function cargarTickets() {
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

        dibujarTickets(json.datos.tickets);
    } catch (error) {
        mostrarMensaje("No se pudieron cargar los tickets: " + error.message);
    }
}

document.addEventListener("DOMContentLoaded", cargarTickets);