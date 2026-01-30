$(document).ready(function () {
    $('#crud-modal button[type="submit"]').on('click', function (e) {
        e.preventDefault();

        var selectedDepartamentolId = $('#departamento').val();

        
        var image1 = document.getElementById('foto1');
        var image_data1 = image1.files[0];

        var image2 = document.getElementById('foto2');
        var image_data2 = image2.files[0];

        var formData = new FormData();
       
        formData.append('descripcion', $('#descripcion').val());
        formData.append('total', $('#total').val());
        formData.append('departamento', selectedDepartamentolId);
        formData.append('direccion', $('#direccion').val());
        formData.append('municipio', $('#municipio').val());
        formData.append('ubicacion', $('#ubicacion').val());
        formData.append('empresa1', $('#empresa1').val());
        formData.append('empresa2', $('#empresa2').val());
    
        formData.append('foto1', image_data1);
        formData.append('foto2', image_data2);
        console.log(formData);

        $.ajax({
            type: 'POST',
            url: 'domicilio/addDomicilio.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    alert("Registro exitoso")
                } else {
                    alert('Error al registrar el domicilio');
                }
            },
            error: function (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});