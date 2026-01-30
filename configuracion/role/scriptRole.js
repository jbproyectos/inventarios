$(document).ready(function () {
    $('#crud-modal button[type="submit"]').on('click', function (e) {
        e.preventDefault();


        var formData = new FormData();
       
        formData.append('nombre_rol', $('#nombre_rol').val());
        formData.append('subname', $('#subname').val());
        
        console.log(formData);

        $.ajax({
            type: 'POST',
            url: 'role/addRole.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    alert("Registro exitoso")
                } else {
                    alert('Error al registrar el rol');
                }
            },
            error: function (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});
