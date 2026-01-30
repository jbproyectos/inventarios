
// Esperar a que se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    // Obtener la tabla
    const tabla = document.getElementById('mobiliario-table');

    // Verificar si la tabla existe
    if (tabla) {
        // Obtener el tbody
        let tbody = tabla.tBodies[0];

        // Si el tbody no existe, crear uno
        if (!tbody) {
            tbody = document.createElement('tbody');
            tabla.appendChild(tbody);
        }

        // Función para obtener los datos de la base de datos
        async function obtenerDatos() {
            try {
                const respuesta = await fetch('mobiliario/get_mobiliario.php');
                const datos = await respuesta.json();

                // Limpiar el tbody
                tbody.innerHTML = '';

                // Recorrer los datos y agregarlos a la tabla
                datos.forEach(dato => {
                    const fila = document.createElement('tr');

                    fila.innerHTML = `
                        <td>${dato.descripcion}</td>
                        <td>${dato.modelo}</td>
                        <td>${dato.marca}</td>
                        <td>${dato.oficina}</td>
                        <td>${dato.departamento}</td>
                        <td>${dato.costo}</td>
                        <td>${dato.fecha_compra}</td>
                        <td>${dato.vida_util}</td>
                        <td>${dato.vencimiento}</td>
                        <td>${dato.estado}</td>
                        <td>${dato.asignado_a}</td>
                        <td>
                            <!-- Agregar acciones aquí -->
                        </td>
                    `;

                    tbody.appendChild(fila);
                });
            } catch (error) {
                console.error('Error al obtener los datos:', error);
            }
        }

        // Llamar la función para obtener los datos
        obtenerDatos();
    } else {
        console.error('La tabla no existe');
    }
});