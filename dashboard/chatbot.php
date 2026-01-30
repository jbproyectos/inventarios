<?php
header("Content-Type: application/json");

// Leer el mensaje enviado al servidor
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibió un mensaje
if (isset($data['message'])) {
    $message = $data['message'];
    error_log("Mensaje recibido del cliente: " . $message);

    // Respuestas genéricas predefinidas
    $genericResponses = ['hola', 'buenas', 'hi', 'hello', '¿cómo estás?'];
    if (in_array(strtolower($message), $genericResponses)) {
        echo json_encode(['response' => '¡Hola! ¿Cómo puedo ayudarte con los productos? Por favor, hazme una pregunta sobre productos específicos.']);
        exit;
    }

    // Conexión a la base de datos usando PDO
    include "../includes/conexionbd.php";
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Función para enviar el mensaje a la API de ChatGPT
    function callChatGPT($message) {
        $apiKey = 'apikeyfake'; // Reemplaza con tu clave de API
        $url = 'https://api.openai.com/v1/chat/completions';

        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente que convierte preguntas en lenguaje natural a consultas SQL para productos. Las preguntas están relacionadas con tablas como `computadora`, `celular`, `mobiliario`, y `usuarios`. Por favor, responde SOLO con una consulta SQL válida y nada más. No incluyas comentarios ni explicaciones.'],
                ['role' => 'user', 'content' => $message]
            ],
            'max_tokens' => 150,
            'temperature' => 0.7
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n" .
                            "Authorization: Bearer " . $apiKey . "\r\n",
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // Registrar posibles errores al conectar con la API
        if ($response === false) {
            error_log("Error al conectar con la API de OpenAI.");
            return null;
        }

        $responseData = json_decode($response, true);

        // Registrar la respuesta completa de la API
        error_log("Respuesta de la API: " . json_encode($responseData));

        return isset($responseData['choices'][0]['message']['content']) ? $responseData['choices'][0]['message']['content'] : null;
    }

    try {
        // Llamar a la API de ChatGPT para obtener la consulta SQL
        $sqlQuery = callChatGPT($message);

        // Validar la consulta generada
        if ($sqlQuery) {
            error_log("Consulta generada por ChatGPT: " . $sqlQuery);

            if (stripos($sqlQuery, "SELECT") !== false && stripos($sqlQuery, "FROM") !== false) {
                // Preparar y ejecutar la consulta SQL
                $stmt = $conexion->prepare($sqlQuery);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Generar HTML dinámico si hay resultados
                if (count($result) > 0) {
                    $htmlOutput = '';
                    foreach ($result as $row) {
                        $htmlOutput .= '<div class="card p-4 m-2 shadow-lg bg-white dark:bg-gray-800 rounded-lg w-60">';
                        foreach ($row as $key => $value) {
                            $htmlOutput .= '<div class="card-detail"><strong>' . ucfirst($key) . ':</strong> ' . htmlspecialchars($value) . '</div>';
                        }
                        $htmlOutput .= '</div>';
                    }

                    echo json_encode(['response' => 'success', 'html' => $htmlOutput]);
                } else {
                    echo json_encode(['response' => 'No se encontraron resultados.']);
                }
            } else {
                error_log("Consulta SQL inválida: " . $sqlQuery);
                echo json_encode(['response' => 'Error: La consulta generada no es válida.']);
            }
        } else {
            error_log("No se recibió una consulta válida de ChatGPT.");
            echo json_encode(['response' => 'Error: No se generó una consulta válida.']);
        }
    } catch (PDOException $e) {
        error_log("Error en la ejecución SQL: " . $e->getMessage());
        echo json_encode(['response' => 'Error de conexión: ' . $e->getMessage()]);
    }

    // Cerrar la conexión a la base de datos
    $conexion = null;
} else {
    error_log("No se recibió ningún mensaje del cliente.");
    echo json_encode(['response' => 'No se recibió ningún mensaje.']);
}
?>
