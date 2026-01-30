document.addEventListener('DOMContentLoaded', function() {
    const tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: 'shadow-md bg-purple-dark',
            scrollTo: true,
            cancelIcon: {
                enabled: true
            }
        }
    });

    tour.addStep({
        id: 'welcome',
        text: 'Bienvenido al panel de control. Te guiaré a través de las características principales.',
        attachTo: {
            element: '.main-content',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Siguiente',
                action: tour.next
            }
        ]
    });

    tour.addStep({
        id: 'charts',
        text: 'Aquí puedes ver las estadísticas y gráficos principales del sistema.',
        attachTo: {
            element: '#column-chart',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Anterior',
                action: tour.back
            },
            {
                text: 'Siguiente',
                action: tour.next
            }
        ]
    });

    tour.addStep({
        id: 'area-chart',
        text: 'Este gráfico muestra la distribución de equipos por departamento.',
        attachTo: {
            element: '#area-chart',
            on: 'top'
        },
        buttons: [
            {
                text: 'Anterior',
                action: tour.back
            },
            {
                text: 'Siguiente',
                action: tour.next
            }
        ]
    });

    tour.addStep({
        id: 'pie-chart',
        text: 'Aquí puedes ver la distribución de condiciones de los equipos.',
        attachTo: {
            element: '#pie-chart',
            on: 'left'
        },
        buttons: [
            {
                text: 'Anterior',
                action: tour.back
            },
            {
                text: 'Siguiente',
                action: tour.next
            }
        ]
    });

    tour.addStep({
        id: 'calendar',
        text: 'El calendario muestra eventos importantes y fechas de mantenimiento.',
        attachTo: {
            element: '#calendar',
            on: 'right'
        },
        buttons: [
            {
                text: 'Anterior',
                action: tour.back
            },
            {
                text: 'Finalizar',
                action: tour.complete
            }
        ]
    });

    // Add a button to start the tour
    const tourButton = document.createElement('button');
    tourButton.innerHTML = '🎯 Iniciar Tour';
    tourButton.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-90 bg-purple-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-purple-700 transition-all duration-200 hover:scale-105';
    tourButton.onclick = () => tour.start();
    document.body.appendChild(tourButton);
});