let equipos = [
    { id: 1, nombre: 'Notebook Dell 01', tipo: 'notebook', marca: 'Dell', estado: 'disponible', ubicacion: 'Lab 3' },
    { id: 2, nombre: 'Notebook HP 02', tipo: 'notebook', marca: 'HP', estado: 'prestado', ubicacion: 'Depósito' },
    { id: 3, nombre: 'Proyector Epson', tipo: 'proyector', marca: 'Epson', estado: 'reparacion', ubicacion: 'Aula 5' }
];

let contadorIdEquipo = 4;

function getBadgeEstadoEquipo(estado) {
    const badges = {
        'disponible': '<span class="badge bg-success">Disponible</span>',
        'prestado': '<span class="badge bg-warning text-dark">Prestado</span>',
        'reparacion': '<span class="badge bg-danger">En reparación</span>'
    };
    return badges[estado] || estado;
}

function actualizarContadores() {
    const disponibles = equipos.filter(function(e) { return e.estado === 'disponible'; }).length;
    const prestados = equipos.filter(function(e) { return e.estado === 'prestado'; }).length;
    const reparacion = equipos.filter(function(e) { return e.estado === 'reparacion'; }).length;

    document.getElementById('contadorDisponibles').textContent = disponibles;
    document.getElementById('contadorPrestados').textContent = prestados;
    document.getElementById('contadorReparacion').textContent = reparacion;
}

function renderizarEquipos() {
    const tbody = document.getElementById('tablaEquipos');
    if (!tbody) return;

    if (equipos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No hay equipos registrados</td></tr>';
        actualizarContadores();
        return;
    }

    tbody.innerHTML = equipos.map(function(equipo) {
        return '<tr>' +
            '<td>' + equipo.id + '</td>' +
            '<td>' + equipo.nombre + '</td>' +
            '<td>' + equipo.tipo + '</td>' +
            '<td>' + equipo.marca + '</td>' +
            '<td>' + getBadgeEstadoEquipo(equipo.estado) + '</td>' +
            '<td>' + equipo.ubicacion + '</td>' +
            '<td>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="eliminarEquipo(' + equipo.id + ')">Eliminar</button>' +
            '</td>' +
        '</tr>';
    }).join('');

    actualizarContadores();
}

function eliminarEquipo(id) {
    equipos = equipos.filter(function(e) { return e.id !== id; });
    renderizarEquipos();
}

document.addEventListener('DOMContentLoaded', function() {

    renderizarEquipos();

    const btnGuardar = document.getElementById('btnGuardarEquipo');
    if (!btnGuardar) return;

    btnGuardar.addEventListener('click', function() {

        const nombre = document.getElementById('nombreEquipo');
        const tipo = document.getElementById('tipoEquipo');
        const marca = document.getElementById('marcaEquipo');
        const ubicacion = document.getElementById('ubicacionEquipo');
        const estado = document.getElementById('estadoEquipo');
        let valido = true;

        if (campoVacio(nombre.value)) {
            mostrarError(nombre, 'Ingresá el nombre del equipo.');
            valido = false;
        } else {
            mostrarValido(nombre);
        }

        if (campoVacio(tipo.value)) {
            mostrarError(tipo, 'Seleccioná el tipo de equipo.');
            valido = false;
        } else {
            mostrarValido(tipo);
        }

        if (campoVacio(marca.value)) {
            mostrarError(marca, 'Ingresá la marca del equipo.');
            valido = false;
        } else {
            mostrarValido(marca);
        }

        if (campoVacio(ubicacion.value)) {
            mostrarError(ubicacion, 'Ingresá la ubicación del equipo.');
            valido = false;
        } else {
            mostrarValido(ubicacion);
        }

        if (campoVacio(estado.value)) {
            mostrarError(estado, 'Seleccioná el estado del equipo.');
            valido = false;
        } else {
            mostrarValido(estado);
        }

        if (!valido) return;

        const nuevoEquipo = {
            id: contadorIdEquipo++,
            nombre: nombre.value.trim(),
            tipo: tipo.value,
            marca: marca.value.trim(),
            ubicacion: ubicacion.value.trim(),
            estado: estado.value
        };

        equipos.push(nuevoEquipo);
        renderizarEquipos();

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoEquipo'));
        modal.hide();
        document.getElementById('formNuevoEquipo').reset();
        [nombre, tipo, marca, ubicacion, estado].forEach(function(campo) {
            limpiarValidacion(campo);
        });
    });

});