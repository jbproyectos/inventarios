    <canvas id="chart"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
const ctx = document.getElementById('chart').getContext('2d');
let myChart = new Chart(ctx, {
    type: 'bar', // Tipo de gráfica
    data: {
        labels: [], // Etiquetas dinámicas
        datasets: [{
            label: 'Inversión',
            data: [], // Valores dinámicos
            backgroundColor: ['#20b612', '#28a745', '#17a2b8']
        }]
    },
    options: {
        responsive: true,
        onClick: (event, elements) => {
            if (elements.length > 0) {
                const index = elements[0].index;
                const selectedLabel = myChart.data.labels[index]; // Elemento seleccionado
                const currentFilter = myChart.data.datasets[0].label;

                // Secuencia de filtros
                if (currentFilter === 'Inversión por año') {
                    fetchFilteredData('year', selectedLabel);  // Filtrar por año
                } else if (currentFilter === 'Inversión por oficina') {
                    fetchFilteredData('office', selectedLabel);  // Filtrar por oficina
                } else if (currentFilter === 'Inversión por departamento') {
                    fetchFilteredData('department', selectedLabel);  // Filtrar por departamento
                } else if (currentFilter === 'Inversión por marca') {
                    fetchFilteredData('brand', selectedLabel);  // Filtrar por marca
                }
            }
        }
    }
});

// Llama al backend para inicializar la gráfica con los años
fetchFilteredData('getYears', '');

// Función para hacer las consultas dinámicas
function fetchFilteredData(filterType, filterValue) {
    fetch('get_filtered_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ filterType, filterValue })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error(data.error);
        } else {
            if (filterType === 'getYears') {
                updateChart(data.years || [], data.investment || [], 'Inversión por año');
            } else if (filterType === 'year') {
                updateChart(data.offices || [], data.investment || [], 'Inversión por oficina');
            } else if (filterType === 'office') {
                updateChart(data.departments || [], data.investment || [], 'Inversión por departamento');
            } else if (filterType === 'department') {
                // Asegurarse de que los datos de marcas y su inversión están presentes
                if (data.brands && data.investment) {
                    updateChart(data.brands || [], data.investment || [], 'Inversión por marca');
                } else {
                    console.error('No se encontraron marcas o inversión para este departamento');
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Actualiza el gráfico
function updateChart(labels, data, label) {
    // Verifica que labels y data no sean undefined o null
    if (!labels || !data) {
        console.error('Labels o datos no válidos', labels, data);
        return;
    }

    // Convierte los valores de inversión a numéricos (es importante hacer esto para evitar problemas con el gráfico)
    const processedData = data.map(value => parseFloat(value));

    // Si las etiquetas son números o IDs de oficina, las convertimos en nombres legibles
    const processedLabels = labels.map(label => {
        // Si la etiqueta es un número o algo que no es un nombre legible, la dejamos como está
        return isNaN(label) ? label : `Año ${label}`;
    });

    myChart.data.labels = processedLabels; // Actualiza etiquetas
    myChart.data.datasets[0].data = processedData; // Actualiza datos
    myChart.data.datasets[0].label = label; // Actualiza el título
    myChart.update(); // Refresca la gráfica
}
</script>
