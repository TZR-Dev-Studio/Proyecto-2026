let solicitudes = [
    { id: 1, docente: 'Prof. González', tipo: 'instalacion', descripcion: 'Instalar Python 3.12 en Lab 2', laboratorio: 'Lab 2', fecha: '25/06/2026', urgencia: 'alta', estado: 'pendiente' },
    { id: 2, docente: 'Prof. Martínez', tipo: 'laboratorio', descripcion: 'Preparar Lab 1 para evaluación', laboratorio: 'Lab 1', fecha: '22/06/2026', urgencia: 'media', estado: 'en proceso' }
];

let contadorIdSolicitud = 3;

function getBadgeUrgencia(urgencia) {
    const badges = {
        'alta': '<span class="badge bg-danger">Alta</span>',
        'media': '<span class="badge bg-warning text-dark">Media</span>',
        'baja': '<span class="badge bg-success">Baja</span>'
    };
    return badges[urgencia] || urgencia;
}

function getBadgeEstadoSolicitud(estado) {
    const badges = {
        'pendiente': '<span class="badge bg-warning text-dark">Pendiente</span>',
        'en proceso': '<span class="badge bg-primary">En proceso</span>',
        'resuelto': '<span class="badge bg-success">Resuelto</span>'
    };
    return badges[estado] || estado;
}

function renderizarSolicitudes() {
    const tbody = document.getElementById('tablaSolicitudes');
    if (!tbody) return;

    if (solicitudes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">No hay solicitudes registradas</td></tr>';
        return;
    }

    tbody.innerHTML = solicitudes.map(function(s) {
        return '<tr>' +
            '<td>' + s.id + '</td>' +
            '<td>' + s.docente + '</td>' +
            '<td>' + s.tipo + '</td>' +
            '<td>' + s.descripcion + '</td>' +
            '<td>' + s.laboratorio + '</td>' +
            '<td>' + s.fecha + '</td>' +
            '<td>' + getBadgeUrgencia(s.urgencia) + '</td>' +
            '<td>' + getBadgeEstadoSolicitud(s.estado) + '</td>' +
            '<td>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarSolicitud(' + s.id + ')">Eliminar</button>' +
            '</td>' +
        '</tr>';
    }).join('');
}

function eliminarSolicitud(id) {
    solicitudes = solicitudes.filter(function(s) { return s.id !== id; });
    renderizarSolicitudes();
}

document.addEventListener('DOMContentLoaded', function() {

    renderizarSolicitudes();

    const btnGuardar = document.getElementById('btnGuardarSolicitud');
    if (!btnGuardar) return;

    btnGuardar.addEventListener('click', function() {

        const docente = document.getElementById('docenteSolicitud');
        const tipo = document.getElementById('tipoSolicitud');
        const descripcion = document.getElementById('descripcionSolicitud');
        const laboratorio = document.getElementById('laboratorioSolicitud');
        const fecha = document.getElementById('fechaSolicitud');
        const urgencia = document.getElementById('urgenciaSolicitud');
        let valido = true;

        const campos = [
            { campo: docente, mensaje: 'Ingresá el nombre del docente.' },
            { campo: tipo, mensaje: 'Seleccioná el tipo de solicitud.' },
            { campo: laboratorio, mensaje: 'Ingresá el laboratorio o aula.' },
            { campo: fecha, mensaje: 'Seleccioná una fecha.' },
            { campo: urgencia, mensaje: 'Seleccioná la urgencia.' }
        ];

        campos.forEach(function(item) {
            if (campoVacio(item.campo.value)) {
                mostrarError(item.campo, item.mensaje);
                valido = false;
            } else {
                mostrarValido(item.campo);
            }
        });

        if (campoVacio(descripcion.value) || !longitudMinima(descripcion.value, 10)) {
            mostrarError(descripcion, 'La descripción debe tener al menos 10 caracteres.');
            valido = false;
        } else {
            mostrarValido(descripcion);
        }

        if (!valido) return;

        const nuevaSolicitud = {
            id: contadorIdSolicitud++,
            docente: docente.value.trim(),
            tipo: tipo.value,
            descripcion: descripcion.value.trim(),
            laboratorio: laboratorio.value.trim(),
            fecha: new Date(fecha.value).toLocaleDateString('es-UY'),
            urgencia: urgencia.value,
            estado: 'pendiente'
        };

        solicitudes.push(nuevaSolicitud);
        renderizarSolicitudes();

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevaSolicitud'));
        modal.hide();
        document.getElementById('formNuevaSolicitud').reset();
        campos.forEach(function(item) { limpiarValidacion(item.campo); });
        limpiarValidacion(descripcion);
    });

});