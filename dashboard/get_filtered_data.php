<?php
include "../includes/conexionbd.php";

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);
$filterType = isset($data['filterType']) ? $data['filterType'] : null;
$filterValue = isset($data['filterValue']) ? $data['filterValue'] : null;

$response = [];

try {
    if ($filterType === 'getYears') {
        // Obtener años únicos con la inversión total por año
        $stmt = $conexion->query("SELECT 
                                    YEAR(fechaDeAsignacion) AS year,
                                    SUM(CAST(REPLACE(REPLACE(costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2))) AS totalInversion
                                  FROM computadora
                                  WHERE fechaDeAsignacion IS NOT NULL
                                  GROUP BY YEAR(fechaDeAsignacion)
                                  ORDER BY year ASC");
        $years = [];
        $investment = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $years[] = $row['year'];
            $investment[] = $row['totalInversion'];
        }
    
        $response['years'] = $years;
        $response['investment'] = $investment;
    }
    elseif ($filterType === 'year') {
        // Obtener todas las oficinas y su inversión, sin filtrar por año
        $stmt = $conexion->query("SELECT o.nombre AS office_name, 
                                          COALESCE(SUM(CAST(REPLACE(REPLACE(c.costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2))), 0) AS inversion
                                  FROM oficina o
                                  LEFT JOIN computadora c ON c.Id_oficina = o.Id_Oficina
                                  GROUP BY o.nombre
                                  ORDER BY o.nombre ASC");
    
        $response = ['offices' => [], 'investment' => []];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response['offices'][] = $row['office_name'];
            $response['investment'][] = $row['inversion'];
        }
    }
    
    
    elseif ($filterType === 'office') {
        // Obtener departamentos e inversión por oficina
        $stmt = $conexion->prepare("SELECT DISTINCT d.nombre AS department_name, 
                                              COALESCE(SUM(CAST(REPLACE(REPLACE(c.costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2))), 0) AS inversion
                                     FROM departamentos d
                                     JOIN computadora c ON c.Id_departamento = d.Id_departamento
                                     JOIN oficina o ON o.Id_Oficina = c.Id_oficina
                                     WHERE o.nombre = :office_name
                                     GROUP BY d.nombre
                                     ORDER BY d.nombre ASC");
        $stmt->bindParam(':office_name', $filterValue, PDO::PARAM_STR);
        $stmt->execute();
        
        $response = ['departments' => [], 'investment' => []];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response['departments'][] = $row['department_name'];
            $response['investment'][] = $row['inversion'];
        }
    }
    
    elseif ($filterType === 'department') {
        // Obtener el ID del departamento basado en su nombre
        $stmt = $conexion->prepare("SELECT Id_departamento 
                                    FROM departamentos 
                                    WHERE nombre = :department_name");
        $stmt->bindParam(':department_name', $filterValue, PDO::PARAM_STR);
        $stmt->execute();
        
        // Verificamos si existe el departamento
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Obtener el ID del departamento
            $departmentId = $row['Id_departamento'];
            
            // Ahora obtenemos las marcas e inversión por ese ID de departamento
            $stmt = $conexion->prepare("SELECT marca, 
                                               COALESCE(SUM(CAST(REPLACE(REPLACE(costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2))), 0) AS inversion 
                                       FROM computadora 
                                       WHERE Id_departamento = :department_id 
                                       GROUP BY marca");
            $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT); // Usamos el ID como parámetro
            $stmt->execute();
            
            $response = ['brands' => [], 'investment' => []];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $response['brands'][] = $row['marca'];
                $response['investment'][] = $row['inversion'];
            }
            
            // Aquí solo debes enviar una respuesta JSON
            echo json_encode($response);
            exit;  // Detener el script para que no siga ejecutando el código y devuelva múltiples respuestas
        } else {
            // Si no se encuentra el departamento, devolver error
            echo json_encode(['error' => 'Departamento no encontrado']);
            exit;  // Detener el script aquí también
        }
    }
    
    
    

    // Devolver respuesta en formato JSON
    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
