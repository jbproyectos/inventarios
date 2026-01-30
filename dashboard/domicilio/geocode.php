<?php
$direccion = $_GET['q'] ?? '';
if(!$direccion) exit(json_encode([]));

$opts = ["http" => ["header" => "User-Agent: MiApp/1.0\r\n"]];
$context = stream_context_create($opts);
$url = "https://nominatim.openstreetmap.org/search?format=json&q=".urlencode($direccion);
echo file_get_contents($url, false, $context);
