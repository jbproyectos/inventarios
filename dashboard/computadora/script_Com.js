$(document).ready(function () {
    $('#crud-modal button[type="submit"]').on('click', function (e) {
        e.preventDefault();

        var selectedDepartamentolId = $('#departamento').val();
        var selectedOficinalId = $('#oficina').val();

        
        var image = document.getElementById('foto');
        var image_data = image.files[0];
        
        var image2 = document.getElementById('foto2');
        var image_data2 = image2.files[0];

        var formData = new FormData();
       
        formData.append('modelo', $('#modelo').val());
        formData.append('marca', $('#marca').val());
        formData.append('tipoDisco', $('#tipoDisco').val());
        formData.append('procesador', $('#procesador').val());
        formData.append('espacioDisco', $('#espacioDisco').val());
        formData.append('ram', $('#ram').val());
        formData.append('condicion', $('#condicion').val());
        formData.append('contrasena', $('#contrasena').val());
        formData.append('total', $('#total').val());
        formData.append('departamento', selectedDepartamentolId);
        formData.append('oficina', selectedOficinalId);
        formData.append('costo', $('#costo').val());
        formData.append('disponibilidad', $('#disponibilidad').val());
        formData.append('asignado_a', $('#asignado_a').val());

        formData.append('foto', image_data);
      
        formData.append('correo_asociado', $('#correo_asociado').val());
        formData.append('contrasenaGmail1', $('#contrasenaGmail1').val());
        formData.append('contrasenaOutlook1', $('#contrasenaOutlook1').val());
        formData.append('correoAsociado2', $('#correoAsociado2').val());
        formData.append('contrasenaGmail2', $('#contrasenaGmail2').val());
        formData.append('contrasenaOutlook2', $('#contrasenaOutlook2').val());
        formData.append('correoAsociado3', $('#correoAsociado3').val());
        formData.append('tipo', $('#tipo').val());
        formData.append('fechaAsignacion', $('#fechaAsignacion').val());
        formData.append('anoProcedor', $('#anoProcedor').val());
        formData.append('fechaLanzamiento', $('#fechaLanzamiento').val());
        formData.append('status', $('#status').val());
        formData.append('posibleFechaParaVenta', $('#posibleFechaParaVenta').val());
        formData.append('nuevaCompra', $('#nuevaCompra').val());
        formData.append('pcAnterior', $('#pcAnterior').val());
        formData.append('posibleAsignacion', $('#posibleAsignacion').val());
        formData.append('costoAlComprar', $('#costoAlComprar').val());
        formData.append('costoALaVenta', $('#costoALaVenta').val());
        formData.append('propietarioDestino', $('#propietarioDestino').val());
        formData.append('fechaReasignacion', $('#fechaReasignacion').val());

        formData.append('foto2', image_data2);
        console.log(formData);

        $.ajax({
            type: 'POST',
            url: 'computadora/addComputadora.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    alert("Registro exitoso")
                } else {
                    alert('Error al registrar el computadora');
                }
            },
            error: function (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});