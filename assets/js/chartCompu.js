document.addEventListener("DOMContentLoaded", () => {
    fetch('computadora/chart_marcaas.php')
        .then(response => response.json())
        .then(data => {
            // Crear los datos para el gráfico
            const marcasArray = data.map(item => item.marca);
            const cantidadesPorMarca = data.map(item => item.cantidad_equipos);
            const preciosPorMarca = data.map(item => {
                const precioTotal = parseFloat(item.precio_total);
                return isNaN(precioTotal) ? 0 : precioTotal;
            });

            // Ordenar los datos de mayor a menor según la cantidad de equipos
            const datosOrdenados = data.sort((a, b) => b.cantidad_equipos - a.cantidad_equipos);
            const marcasOrdenadas = datosOrdenados.map(item => item.marca);
            const cantidadesOrdenadas = datosOrdenados.map(item => item.cantidad_equipos);
            const preciosOrdenados = datosOrdenados.map(item => parseFloat(item.precio_total));

            // Crear el gráfico con ApexCharts
            const options = {
                series: [
                    {
                        name: 'Equipos',
                        type: 'bar',
                        data: cantidadesOrdenadas
                    },
                    {
                        name: 'Precio',
                        type: 'line',
                        data: preciosOrdenados
                    }
                ],
                chart: {
                    height: 400,
                    type: 'line',
                    zoom: { enabled: false },
                    toolbar: { show: false }
                },
                colors: ['#1D8CF8', '#FF6263'], // Colores base
                plotOptions: {
                    bar: {
                        borderRadius: 8, // Bordes redondeados
                        columnWidth: '40%',
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold',
                        colors: ['#333']
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: [0, 4]
                },
                fill: {
                    type: ['gradient', 'solid'],
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        gradientToColors: ['#4FACFE', '#FC466B'], // Degradados
                        stops: [0, 100]
                    }
                },
                xaxis: {
                    categories: marcasOrdenadas,
                    labels: {
                        style: { fontSize: '14px', colors: ['#666'] }
                    }
                },
                yaxis: [
                    {
                        title: { text: 'Cantidad de Equipos', style: { fontSize: '14px', color: '#666' } },
                        labels: { formatter: val => val.toFixed(0) }
                    },
                    {
                        opposite: true,
                        title: { text: 'Precio Total', style: { fontSize: '14px', color: '#666' } },
                        labels: {
                            formatter: val =>
                                new Intl.NumberFormat('es-ES', {
                                    style: 'currency',
                                    currency: 'MXN'
                                }).format(val)
                        }
                    }
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return val;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    markers: {
                        radius: 12
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#graficoEquipos"), options);
            chart.render();
        })
        .catch(error => {
            console.error('Error al obtener los datos:', error);
        });
});



document.addEventListener("DOMContentLoaded", () => {
    // Obtener los datos desde el servidor
    fetch('computadora/chart_pastel.php')
        .then(response => response.json())
        .then(data => {
            // Procesar los datos
            const labels = data.map(item => item.condicion);
            const cantidades = data.map(item => item.cantidad);

            // Crear la gráfica de pastel
            const ctx = document.getElementById('pie-chart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Condiciones de Computadoras',
                        data: cantidades,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Condiciones'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error:', error));
});


document.addEventListener("DOMContentLoaded", () => {
    // Obtener los datos desde el servidor
    fetch('computadora/chart_status.php')
        .then(response => response.json())
        .then(data => {
            // Procesar los datos
            const labels = data.map(item => item.status);
            const cantidades = data.map(item => item.cantidad);

            // Crear la gráfica de pastel
            const ctx = document.getElementById('pie-status').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Condiciones de Computadoras',
                        data: cantidades,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                        ],
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Condiciones'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error:', error));
});

document.addEventListener("DOMContentLoaded", () => {
    fetch('computadora/chart_oficina.php')
        .then(response => response.json())
        .then(data => {
            const oficinas = data.map(item => item.OFICINAS);
            const valores = data.map(item => parseInt(item.valor));
            const precios = data.map(item => parseFloat(item.precio_total));
            const oficinasv = data.map(item => parseInt(item.oficinasv));

            const datosOrdenados = data.sort((a, b) => b.valor - a.valor);
            const oficinasOrdenadas = datosOrdenados.map((item) => item.OFICINAS);
            const valoresOrdenados = datosOrdenados.map((item) => parseInt(item.valor));
            const preciosOrdenados = datosOrdenados.map((item) => parseFloat(item.precio_total));

            const oficinasvt = oficinasv.reduce((a, b) => a + b, 0);
            document.getElementById("total-Oficinas").textContent = `${oficinasvt.toLocaleString()} Oficinas`;

            const options = {
                series: [{
                        name: 'Equipos',
                        type: 'bar',
                        data: valoresOrdenados,
                        yAxisIndex: 0
                    },
                    {
                        name: 'Inversión',
                        type: 'line',
                        data: preciosOrdenados,
                        yAxisIndex: 1,
                        stroke: {
                            width: 4
                        }
                    }
                ],
                chart: {
                    height: 320,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: oficinasOrdenadas
                },
                yaxis: [{
                        title: {
                            text: 'Equipos'
                        },
                        labels: {
                            formatter: function(val) {
                                return val;
                            }
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Inversión'
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toLocaleString('en-US', {
                                    style: 'currency',
                                    currency: 'USD'
                                });
                            }
                        }
                    }
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                        }
                    }
                },
                legend: {
                    position: 'top'
                }
            };

            const chart = new ApexCharts(document.querySelector("#area-oficina"), options);
            chart.render();

            // Ajustar el gráfico al cambiar el tamaño de la ventana
            window.addEventListener("resize", () => {
                chart.updateOptions({
                    chart: {
                        width: document.querySelector("#area-oficina").clientWidth,
                        height: 350
                    }
                });
            });
        })
        .catch(error => console.error('Error fetching data:', error));
});



document.addEventListener("DOMContentLoaded", () => {
    // Hacer una solicitud a chart_hardware.php para obtener los datos
    fetch('computadora/chart_hardware.php')
        .then(response => response.json())
        .then(apiData => {
            const tbody = document.getElementById("tabla-body");

            // Verificar si tbody está correctamente seleccionado
            // console.log(tbody); // Si el tbody es null, hay que verificar el HTML

            // Agrupar los detalles por categoria (modificado según tus claves en la base de datos)
            const groupByCategory = (data) => {
                return data.reduce((acc, item) => {
                    // Mapear las categorías correctamente
                    let category = item.categoria.trim().toLowerCase();

                    if (category === "disco") category = "tipoDeDisco";
                    else if (category === "procesador") category = "procesador";
                    else if (category === "ram") category = "ram";

                    if (!acc[category]) acc[category] = [];
                    acc[category].push(item);
                    return acc;
                }, {});
            };

            const groupedDetails = groupByCategory(apiData.detalles);

            // Verificar que los detalles se han agrupado correctamente
            // console.log("Detalles agrupados:", groupedDetails);

            // Iterar sobre los totales y agregar las filas correspondientes
            Object.keys(apiData.totals).forEach((category) => {
                const total = apiData.totals[category];
                const categoryName = category.charAt(0).toUpperCase() + category.slice(1);
                let details = [];

                // Aquí mapeamos las categorías correctas según la estructura de la BD
                if (category === "discos") {
                    details = groupedDetails["tipoDeDisco"] || [];
                } else if (category === "procesadores") {
                    details = groupedDetails["procesador"] || [];
                } else if (category === "ram") {
                    details = groupedDetails["ram"] || [];
                }

                // Crear la fila principal con el total
                const mainRow = document.createElement("tr");
                mainRow.innerHTML = `
        <td class="border border-gray-300 p-2">${categoryName}</td>
        <td class="border border-gray-300 p-2">${total}</td>
        <td class="border border-gray-300 p-2">
            <button class="toggle-details px-2 py-1 bg-blue-500 text-white rounded">Expandir</button>
        </td>
    `;
                tbody.appendChild(mainRow);

                // Crear la fila de detalles (inicialmente oculta)
                const detailsRow = document.createElement("tr");
                detailsRow.classList.add("hidden");
                detailsRow.innerHTML = `
        <td colspan="3" class="border border-gray-300 p-2">
            <table class="table-auto w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-300 p-2 text-left">Cantidad</th>
                        <th class="border border-gray-300 p-2 text-left">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    ${details
                        .map(
                            (item) => ` 
                        <tr>
                            <td class="border border-gray-300 p-2">${item.cantidad}</td>
                            <td class="border border-gray-300 p-2">${item.valor}</td>
                        </tr>
                    `
                        )
                        .join("")}
                </tbody>
            </table>
        </td>
    `;
                tbody.appendChild(detailsRow);

                // Manejar el botón para expandir o contraer detalles
                mainRow.querySelector(".toggle-details").addEventListener("click", () => {
                    if (detailsRow.classList.contains("hidden")) {
                        detailsRow.classList.remove("hidden");
                        mainRow.querySelector(".toggle-details").textContent = "Contraer";
                    } else {
                        detailsRow.classList.add("hidden");
                        mainRow.querySelector(".toggle-details").textContent = "Expandir";
                    }
                });
            });
        })
        .catch(error => {
            console.error('Error al obtener los datos:', error);
        });
});
