// Datos de prueba
let tickets = [
    { id: 1, descripcion: 'PC sin encender', laboratorio: 'Lab 3', estado: 'pendiente', prioridad: 'alta', fecha: '20/06/2026' },
    { id: 2, descripcion: 'Proyector sin señal', laboratorio: 'Aula 5', estado: 'en proceso', prioridad: 'media', fecha: '19/06/2026' },
    { id: 3, descripcion: 'Teclado roto', laboratorio: 'Lab 1', estado: 'resuelto', prioridad: 'baja', fecha: '18/06/2026' }
];

let contadorId = 4;

function getBadgeEstado(estado) {
    const badges = {
        'pendiente': '<span class="badge bg-warning text-dark">Pendiente</span>',
        'en proceso': '<span class="badge bg-primary">En proceso</span>',
        'resuelto': '<span class="badge bg-success">Resuelto</span>'
    };
    return badges[estado] || estado;
}

function getBadgePrioridad(prioridad) {
    const badges = {
        'alta': '<span class="badge bg-danger">Alta</span>',
        'media': '<span class="badge bg-warning text-dark">Media</span>',
        'baja': '<span class="badge bg-success">Baja</span>'
    };
    return badges[prioridad] || prioridad;
}

function renderizarTickets(lista) {
    const tbody = document.getElementById('tablaTickets');
    if (!tbody) return;

    if (lista.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No hay tickets para mostrar</td></tr>';
        return;
    }

    tbody.innerHTML = lista.map(function(ticket) {
        return '<tr>' +
            '<td>' + ticket.id + '</td>' +
            '<td>' + ticket.descripcion + '</td>' +
            '<td>' + ticket.laboratorio + '</td>' +
            '<td>' + getBadgeEstado(ticket.estado) + '</td>' +
            '<td>' + getBadgePrioridad(ticket.prioridad) + '</td>' +
            '<td>' + ticket.fecha + '</td>' +
            '<td>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarTicket(' + ticket.id + ')">Eliminar</button>' +
            '</td>' +
        '</tr>';
    }).join('');
}

function eliminarTicket(id) {
    tickets = tickets.filter(function(t) { return t.id !== id; });
    renderizarTickets(tickets);
}

document.addEventListener('DOMContentLoaded', function() {

    renderizarTickets(tickets);

    const btnGuardar = document.getElementById('btnGuardarTicket');
    if (!btnGuardar) return;

    btnGuardar.addEventListener('click', function() {

        const descripcion = document.getElementById('descripcionTicket');
        const laboratorio = document.getElementById('laboratorioTicket');
        const prioridad = document.getElementById('prioridadTicket');
        let valido = true;

        if (campoVacio(descripcion.value) || !longitudMinima(descripcion.value, 10)) {
            mostrarError(descripcion, 'La descripción debe tener al menos 10 caracteres.');
            valido = false;
        } else {
            mostrarValido(descripcion);
        }

        if (campoVacio(laboratorio.value)) {
            mostrarError(laboratorio, 'Ingresá el laboratorio o aula.');
            valido = false;
        } else {
            mostrarValido(laboratorio);
        }

        if (campoVacio(prioridad.value)) {
            mostrarError(prioridad, 'Seleccioná una prioridad.');
            valido = false;
        } else {
            mostrarValido(prioridad);
        }

        if (!valido) return;

        const nuevoTicket = {
            id: contadorId++,
            descripcion: descripcion.value.trim(),
            laboratorio: laboratorio.value.trim(),
            estado: 'pendiente',
            prioridad: prioridad.value,
            fecha: new Date().toLocaleDateString('es-UY')
        };

        tickets.push(nuevoTicket);
        renderizarTickets(tickets);

        // Cerramos el modal y limpiamos el formulario
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTicket'));
        modal.hide();
        document.getElementById('formNuevoTicket').reset();
        limpiarValidacion(descripcion);
        limpiarValidacion(laboratorio);
        limpiarValidacion(prioridad);
    });

    // Filtros
    const formFiltros = document.getElementById('formFiltros');
    if (formFiltros) {
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault();
            const estado = document.getElementById('filtroEstado').value;
            const prioridad = document.getElementById('filtroPrioridad').value;

            const filtrados = tickets.filter(function(t) {
                return (estado === '' || t.estado === estado) &&
                       (prioridad === '' || t.prioridad === prioridad);
            });

            renderizarTickets(filtrados);
        });
    }

});