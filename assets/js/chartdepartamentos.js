
document.addEventListener("DOMContentLoaded", () => {
    // Llamada al endpoint PHP para obtener los datos
    fetch('computadora/chart2.php')
        .then(response => response.json())
        .then(data => {
            // Extraer nombres y valores para el gráfico
            const departamentos = data.map(item => item.DEPARTAMENTOS);
            const valores = data.map(item => parseInt(item.valor)); // Equipos
            const dep = data.map(item => parseInt(item.depa));
            const dinero = data.map(item => parseFloat(item.precio_total)); // Inversión

            // Ordenar los datos de mayor a menor por 'valor'
            const datosOrdenados = data.sort((a, b) => b.valor - a.valor); // Ordenar por 'valor' de mayor a menor

            // Actualizar las categorías, valores y precios con los datos ordenados
            const departamentosOrdenados = datosOrdenados.map((item) => item.DEPARTAMENTOS);
            const valoresOrdenados = datosOrdenados.map((item) => parseInt(item.valor));
            const dineroOrdenado = datosOrdenados.map((item) => parseFloat(item.precio_total));

            // Sumar el total de usuarios
            const totalUsers = valoresOrdenados.reduce((a, b) => a + b, 0);
            const totaldep = dep.reduce((a, b) => a + b, 0);
            const totaldinero = dineroOrdenado.reduce((a, b) => a + b, 0);

            document.getElementById("total-users").textContent = `${totaldep.toLocaleString()} Departamentos`;
            document.getElementById("total-anio").textContent = `$${totaldinero.toLocaleString()} `;

            // Configurar el gráfico con ejes duales
            const options = {
                series: [{
                        name: 'Equipos',
                        type: 'bar',
                        data: valoresOrdenados,
                        yAxisIndex: 0 // Eje Y izquierdo
                    },
                    {
                        name: 'Inversión',
                        type: 'line',
                        data: dineroOrdenado,
                        yAxisIndex: 1, // Eje Y derecho
                        stroke: {
                            width: 4
                        }
                    }
                ],
                chart: {
                    height: 350,
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
                    categories: departamentosOrdenados
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

            // Renderizar el gráfico
            const chart = new ApexCharts(document.querySelector("#area-chart"), options);
            chart.render();
        })
        .catch(error => console.error('Error fetching data:', error));
});