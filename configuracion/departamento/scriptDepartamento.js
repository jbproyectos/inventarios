$(document).ready(function () {
    $('#crud-modal button[type="submit"]').on('click', function (e) {
        e.preventDefault();


        var formData = new FormData();
        formData.append('nombre', $('#nombre').val());
        
        console.log(formData);

        $.ajax({
            type: 'POST',
            url: 'departamento/addDepartamento.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    alert("Registro exitoso")
                } else {
                    alert('Error al registrar la oficina');
                }
            },
            error: function (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});
