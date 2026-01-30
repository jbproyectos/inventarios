<?php
include '../../includes/conexionbd.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $puestoId = $_POST['puestos'];  // El ID del puesto
    $oficinaId = $_POST['oficina'];  // El ID de la oficina
    $Id_departamentos = $_POST['Id_departamentos'];  // El ID de la oficina

    $name = $_POST['name'];  // Nombre
    $apellido = $_POST['apellido'];  // Apellidos
    $email = $_POST['email'];  // Email
    $contrasena = $_POST['contrasena'];  // Contraseña
    $verificar = $_POST['verificar'];  // Verificar contraseña
    $nivel = isset($_POST['nivel']) ? implode(",", $_POST['nivel']) : '';  // Los CEOS seleccionados (IDs)
    $roleId = $_POST['role'];  // ID del rol seleccionado

    // Validar los campos obligatorios
    if (empty($name) || empty($apellido) || empty($email) || empty($contrasena) || empty($verificar) || empty($roleId)) {
        $response = array('success' => false, 'message' => 'Todos los campos son obligatorios');
    } else {
        // Verificar que las contraseñas coincidan
        if ($contrasena !== $verificar) {
            $response = array('success' => false, 'message' => 'Las contraseñas no coinciden');
        } else {
            // Encriptar la contraseña
            $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

            try {
                // Insertar los datos en la base de datos
                $sql = "INSERT INTO usuarios (Id_puesto, Id_Oficina, Id_departamento, nombre, apellido, email, contrasena, verificacionContrasena, rolActual, administra)
                        VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$puestoId, $oficinaId, $Id_departamentos, $name, $apellido, $email, $hashedPassword, $verificar, $roleId, $nivel]);

                // Respuesta exitosa
                $response = array('success' => true, 'message' => 'Registro exitoso');
            } catch (PDOException $e) {
                $response = array('success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage());
            }
        }
    }

    // Retornar la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Si no es una solicitud POST
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'No se recibieron datos por POST'));
}
?>
