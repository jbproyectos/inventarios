$(document).ready(function () {
    $('#crud-modal button[type="submit"]').on('click', function (e) {
        e.preventDefault();

        var selectedDepartamentolId = $('#departamento').val();
        var selectedOficinalId = $('#oficina').val();

        // var image = $('#foto');
        var image = document.getElementById('foto');
        var image_data = image.files[0];

        var formData = new FormData();
       
        formData.append('modelo', $('#modelo').val());
        formData.append('marca', $('#marca').val());
        formData.append('estado_bateria', $('#estado_bateria').val());
        formData.append('total', $('#total').val());
        formData.append('departamento', selectedDepartamentolId);
        formData.append('oficina', selectedOficinalId);
        formData.append('costo', $('#costo').val());
        formData.append('disponibilidad', $('#disponibilidad').val());
        formData.append('asignado_a', $('#asignado_a').val());
        formData.append('asignado_a2', $('#asignado_a2').val());
        formData.append('contrasena', $('#contrasena').val());
        formData.append('IMEI', $('#IMEI').val());
        formData.append('telefono1', $('#telefono1').val());
        formData.append('telefono2', $('#telefono2').val());
        formData.append('telefono3', $('#telefono3').val());
        formData.append('correo', $('#correo').val());
        formData.append('password', $('#password').val());
        formData.append('Icloud2', $('#Icloud2').val());
        formData.append('password2', $('#password2').val());
        formData.append('Llamadas', $('#Llamadas').val());
        formData.append('WhatsApp', $('#WhatsApp').val());
        formData.append('Status', $('#Status').val());
        //formData.append('foto', $('#foto').val());

        formData.append('foto', image_data);
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        $.ajax({
            type: 'POST',
            url: 'celular/addCelular.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    alert("Registro exitoso")
                } else {
                    alert('Error al registrar el celular');
                }
            },
            error: function (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});