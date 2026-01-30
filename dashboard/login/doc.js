$(document).ready(function () {
    $('#crud-modal button[type="submit"]').on('click', function (e) {
        e.preventDefault();

        var selectedRolId = $('#rol').val();
        var selectedDepartamentolId = $('#depasin').val();
        // var selectedDepartamentolIds = $('#depasin').val();
        var selectedOficinalId = $('#oficina').val();

        const selectElement = document.getElementById("departamento");
        const selectedOptions = Array.from(selectElement.selectedOptions).map(option => option.text);

        console.log('====================================');
        console.log(selectedOptions);
        console.log('====================================');

        var formData = new FormData();
        formData.append('rolId', selectedRolId);
        formData.append('departamento', selectedDepartamentolId);
        formData.append('departamentos', selectedOptions);
        formData.append('oficina', selectedOficinalId);
        formData.append('name', $('#name').val());
        formData.append('apellido', $('#apellido').val());
        formData.append('email', $('#email').val());
        formData.append('contrasena', $('#contrasena').val());
        formData.append('verificar', $('#verificar').val());

        $.ajax({
            type: 'POST',
            url: 'login/addUser.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    alert("Registro exitoso")
                } else {
                    alert('Error al registrar usuario');
                }
            },
            error: function (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});
