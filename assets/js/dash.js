document.getElementById('toggleSidebar').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const iconsOnlyClass = 'w-20'; // Ancho para solo íconos
    const fullClass = 'w-64';      // Ancho para menú completo

    if (sidebar.classList.contains(fullClass)) {
        sidebar.classList.remove(fullClass);
        sidebar.classList.add(iconsOnlyClass);
    } else if (sidebar.classList.contains(iconsOnlyClass)) {
        sidebar.classList.remove(iconsOnlyClass);
        sidebar.classList.add('hidden'); // Ocultar completamente
    } else {
        sidebar.classList.remove('hidden');
        sidebar.classList.add(fullClass);
    }
});
