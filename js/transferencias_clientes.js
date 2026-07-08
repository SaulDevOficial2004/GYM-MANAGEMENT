$(document).on('click', '.viewComprobanteBtn', function(){

    let archivo = $(this).data('archivo');

    $('#previewImage').addClass('d-none');
    $('#previewPdf').addClass('d-none');

    let extension = archivo.split('.').pop().toLowerCase();

    if(extension == 'pdf'){

        $('#previewPdf').attr('src', 'uploads/' + archivo).removeClass('d-none');

    }else{

        $('#previewImage').attr('src', 'uploads/' + archivo).removeClass('d-none');

    }

    $('#viewComprobanteModal').modal('show');
});

//=======================================
// SUBIR COMPROBANTE
//=======================================

$('#uploadComprobanteForm').submit(async function(e){

    e.preventDefault();

    let formData = new FormData();

    formData.append('persona_id', $('#persona_id').val());
    formData.append('folio_cliente', $('#folio_cliente').val());
    formData.append('concepto', $('#concepto').val());
    formData.append('archivo', $('#archivo')[0].files[0]);

    try{

        let response = await fetch('api/upload_comprobante.php', {

                    method:'POST',
                    body:formData

                });

        let data = await response.json();

        if(data.success){

            Toastify({
                text:data.message,
                duration:3000,
                gravity:'top',
                position:'right',

                style:{
                    background:
                    "linear-gradient(to right,#183B6B,#16BFFD)"
                }

            }).showToast();

            $('#uploadComprobanteForm')[0].reset();

            setTimeout(()=>{
                location.reload();
            },1200);

        }else{

            Swal.fire({
                icon:'error',
                title:'Error',
                text:data.message,
                confirmButtonColor:'#183B6B'
            });
        }

    }catch(error){

        console.error(error);

        Swal.fire({
            icon:'error',
            title:'Error',
            text:'No fue posible subir el comprobante.',
            confirmButtonColor:'#183B6B'
        });

    }
});

$(document).on('click', '.viewPaymentBtn', function(){

    let archivo = $(this).data('archivo');
    let extension = archivo.split('.').pop().toLowerCase();

    $('#adminPreviewImage').addClass('d-none');
    $('#adminPreviewPdf').addClass('d-none');

    if(extension=='pdf'){

        $('#adminPreviewPdf').attr('src', 'uploads/' + archivo).removeClass('d-none');

    }else{

        $('#adminPreviewImage').attr('src', 'uploads/' + archivo).removeClass('d-none');

    }

    $('#reviewNombre').text($(this).data('nombre'));
    $('#reviewFolio').text($(this).data('folio'));
    $('#reviewMembresia').text($(this).data('membresia'));

$('#reviewConcepto').text($(this).data('concepto') || 'Sin concepto');
$('#reviewFecha').text($(this).data('fecha'));
$('#paymentReviewModal').modal('show');

});