<?php
$url = $_SERVER['REQUEST_URI'];

$segments = explode('/', $url);

// Filtrar segmentos vacíos y carpetas principales
$segments = array_filter($segments, function($segment) {
    return !empty($segment) && $segment !== 'sistemas' && $segment !== 'dashboard';
});

echo '<nav class="flex hidden md:block" aria-label="Breadcrumb">';
echo '<ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">';

echo '<li class="inline-flex items-center">';
echo '<a href="./" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">';
echo '<svg class="w-3 h-3 me-2.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">';
echo '<path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>';
echo '</svg>Home</a></li>';

$partialUrl = '';
foreach ($segments as $segment) {
    // Busca la posición del carácter "?" en el segmento
    $questionMarkPosition = strpos($segment, '?');

    if ($questionMarkPosition !== false) {
        // Si hay un "?", agrega el separador antes del "?"
        $segmentText = ucwords(str_replace('_', ' ', substr($segment, 0, $questionMarkPosition)));
        $partialUrl .= '/' . substr($segment, 0, $questionMarkPosition);
    } else {
        // Si no hay "?", procede como antes
        $segmentText = ucwords(str_replace('_', ' ', $segment));
        $partialUrl .= '/' . $segment;
    }

    echo '<li>';
    echo '<div class="flex items-center">';
    echo '<svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">';
    echo '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>';
    echo '<a href="' . $partialUrl . '" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">' . $segmentText . '</a>';
    echo '</div></li>';
}

echo '</ol></nav>';
?>
