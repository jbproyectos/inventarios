<?php
// Conectar a la base de datos
include '../../includes/conexionbd.php'; // Asegúrate de tener la conexión correcta.

// Obtener los datos (asegurándose de filtrar los registros con 'costoActualEquipo' válido)
$query = "
    SELECT YEAR(fechaDeLanzamiento) AS year, SUM(costoActualEquipo) AS total_cost
    FROM equipos
    WHERE costoActualEquipo != '' AND costoActualEquipo != 'N/A'
    GROUP BY YEAR(fechaDeLanzamiento)
    ORDER BY YEAR(fechaDeLanzamiento) DESC
";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'year' => $row['year'],
        'total_cost' => $row['total_cost']
    ];

}

// Devolver los datos como JSON
echo json_encode($data);
?>
