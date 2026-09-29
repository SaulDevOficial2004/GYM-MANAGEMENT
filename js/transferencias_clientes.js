$(document).on('click','.viewComprobanteBtn',function(){
    const archivo=$(this).data('archivo');

    if(!archivo){
        Swal.fire({
            icon:'error',
            title:'Archivo no disponible',
            text:'No fue posible localizar el comprobante.',
            confirmButtonColor:'#176dd3'
        });

        return;
    }

    const extension=archivo
        .split('.')
        .pop()
        .toLowerCase();

    const archivoSeguro=encodeURIComponent(archivo);

    $('#previewImage')
        .attr('src','')
        .addClass('d-none');

    $('#previewPdf')
        .attr('src','')
        .addClass('d-none');

    if(extension==='pdf'){
        $('#previewPdf')
            .attr('src',rutaArchivoFirmado($(this).data('id')))
            .removeClass('d-none');
    }else{
        $('#previewImage')
            .attr('src',rutaArchivoFirmado($(this).data('id')))
            .removeClass('d-none');
    }

    $('#viewComprobanteModal').modal('show');
});

function rutaArchivoFirmado(id){
    const parametros=new URLSearchParams(window.location.search);

    return 'api/ver_archivo.php?id='+encodeURIComponent(id)
        +'&f='+encodeURIComponent(parametros.get('f')||'')
        +'&e='+encodeURIComponent(parametros.get('e')||'')
        +'&s='+encodeURIComponent(parametros.get('s')||'');
}

$('#viewComprobanteModal').on('hidden.bs.modal',function(){
    $('#previewImage')
        .attr('src','')
        .addClass('d-none');

    $('#previewPdf')
        .attr('src','')
        .addClass('d-none');
});

$('#uploadComprobanteForm').submit(async function(event){
    event.preventDefault();

    const formulario=this;
    const archivoInput=document.getElementById('archivo');
    const submitButton=document.getElementById('uploadComprobanteButton');

    if(!archivoInput.files.length){
        Swal.fire({
            icon:'warning',
            title:'Selecciona un archivo',
            text:'Adjunta una imagen o un documento PDF.',
            confirmButtonColor:'#176dd3'
        });

        return;
    }

    const archivo=archivoInput.files[0];
    const extensionesPermitidas=['jpg','jpeg','png','pdf'];
    const extension=archivo.name.split('.').pop().toLowerCase();

    if(!extensionesPermitidas.includes(extension)){
        Swal.fire({
            icon:'warning',
            title:'Formato no permitido',
            text:'Solo se permiten archivos JPG, PNG o PDF.',
            confirmButtonColor:'#176dd3'
        });

        return;
    }

    const formData=new FormData();

    formData.append(
        'persona_id',
        $('#persona_id').val()
    );

    formData.append(
        'folio_cliente',
        $('#folio_cliente').val()
    );

    formData.append(
        'concepto',
        $('#concepto').val().trim()
    );

    formData.append(
        'archivo',
        archivo
    );

    const parametrosEnlace=new URLSearchParams(window.location.search);

    formData.append('f',parametrosEnlace.get('f')||'');
    formData.append('e',parametrosEnlace.get('e')||'');
    formData.append('s',parametrosEnlace.get('s')||'');

    if(window.turnstile){
        formData.append(
            'turnstile_token',
            window.turnstile.getResponse()||''
        );
    }

    setUploadLoading(true);

    try{
        const response=await fetch(
            'api/upload_comprobante.php',
            {
                method:'POST',
                body:formData,
                headers:{
                    'Accept':'application/json'
                }
            }
        );

        const responseText=await response.text();
        let data;

        try{
            data=JSON.parse(responseText);
        }catch(error){
            console.error(responseText);

            throw new Error(
                'El servidor devolvió una respuesta inválida.'
            );
        }

        if(!response.ok){
            throw new Error(
                data.message||
                'No fue posible subir el comprobante.'
            );
        }

        if(data.success){
            Toastify({
                text:data.message,
                duration:2500,
                gravity:'top',
                position:'right',
                stopOnFocus:true,
                style:{
                    background:
                        'linear-gradient(135deg,#183b6b,#176dd3)',
                    borderRadius:'10px',
                    boxShadow:
                        '0 10px 24px rgba(24,59,107,.2)',
                    fontFamily:'Poppins,sans-serif',
                    fontSize:'11px'
                }
            }).showToast();

            formulario.reset();

            setTimeout(()=>{
                window.location.reload();
            },1000);

            return;
        }

        Swal.fire({
            icon:'error',
            title:'No fue posible enviar el comprobante',
            text:data.message||
                'Revisa la información e inténtalo nuevamente.',
            confirmButtonColor:'#176dd3'
        });
    }catch(error){
        console.error(error);

        Swal.fire({
            icon:'error',
            title:'Error al subir el comprobante',
            text:error.message||
                'No fue posible comunicarse con el servidor.',
            confirmButtonColor:'#176dd3'
        });
    }finally{
        setUploadLoading(false);
    }
});

function setUploadLoading(loading){
    const button=document.getElementById(
        'uploadComprobanteButton'
    );

    const archivoInput=document.getElementById('archivo');
    const conceptoInput=document.getElementById('concepto');

    if(!button){
        return;
    }

    button.disabled=loading;

    if(archivoInput){
        archivoInput.disabled=loading;
    }

    if(conceptoInput){
        conceptoInput.disabled=loading;
    }

    if(loading){
        button.innerHTML=
            '<i class="fas fa-spinner fa-spin"></i>'+
            '<span>Enviando comprobante...</span>';
    }else{
        button.innerHTML=
            '<i class="fas fa-paper-plane"></i>'+
            '<span>Enviar comprobante</span>';
    }
}