<?php
// Conexión a la base de datos
include "../includes/conexionbd.php";

// Consulta para obtener los datos
$query = $conexion->prepare("
    SELECT 
        Id_computadora,
        asignado_a,
        fechaDeAsignacion,
        fechaDeReasignacion,
        posibleFechaParaVenta,
        status,
        modelo,
        marca
    FROM computadora
");
$query->execute();
$resultado = $query->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para la gráfica
$data = [];
foreach ($resultado as $fila) {
    $tooltip = $fila['marca'] . " " . $fila['modelo'] . " (" . $fila['asignado_a'] . ")";
    $data[] = [
        'label' => $tooltip,
        'fechaDeAsignacion' => $fila['fechaDeAsignacion'],
        'fechaDeReasignacion' => $fila['fechaDeReasignacion'],
        'posibleFechaParaVenta' => $fila['posibleFechaParaVenta']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica de Línea de Tiempo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
</head>
<body>
    <div style="width: 80%; margin: auto;">
        <canvas id="timelineChart"></canvas>
    </div>
    <script>
        const rawData = <?php echo json_encode($data); ?>;

        // Procesar datos
        const datasets = rawData.map((item, index) => {
            return {
                label: item.label,
                data: [
                    { x: item.fechaDeAsignacion, y: index },
                    { x: item.fechaDeReasignacion, y: index },
                    { x: item.posibleFechaParaVenta, y: index }
                ].filter(point => point.x), // Excluir puntos sin fecha
                borderColor: `hsl(${index * 40}, 70%, 50%)`,
                backgroundColor: `hsl(${index * 40}, 70%, 50%)`,
                showLine: false, // Mostrar solo puntos
                pointRadius: 6
            };
        });

        // Configuración
        const config = {
            type: 'scatter',
            data: {
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month'
                        },
                        title: {
                            display: true,
                            text: 'Fechas'
                        }
                    },
                    y: {
                        ticks: {
                            callback: (value) => rawData[value]?.label || ''
                        },
                        title: {
                            display: true,
                            text: 'Equipos'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const data = context.raw;
                                return `${rawData[data.y].label} - ${new Date(data.x).toLocaleDateString()}`;
                            }
                        }
                    }
                }
            }
        };

        const ctx = document.getElementById('timelineChart').getContext('2d');
        new Chart(ctx, config);
    </script>
</body>
</html>
