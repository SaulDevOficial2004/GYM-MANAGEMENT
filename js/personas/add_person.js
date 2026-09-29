$('#createPersonForm').on('submit',async function(event){
    event.preventDefault();

    const submitButton=$(this).find('button[type="submit"]');
    const originalContent=submitButton.html();

    const formData={
        nombre:$('#persona_nombre').val().trim(),
        fecha_ini:$('#fecha_ini_persona').val(),
        fecha_fin:$('#fecha_fin_persona').val(),
        membresia_id:$('#persona_membresia').val()
    };

    if(!formData.nombre||!formData.membresia_id||!formData.fecha_ini||!formData.fecha_fin){
        Swal.fire({
            icon:'warning',
            title:'Información incompleta',
            text:'Completa todos los campos antes de registrar a la persona.',
            confirmButtonColor:'#176dd3'
        });
        return;
    }

    submitButton.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Registrando...'
    );

    try{
        const result=await apiFetch('api/create_person.php',{
            method:'POST',
            body:JSON.stringify(formData)
        });

        if(!result) return;

        const data=result.data;

        if(data.status!=='success'){
            throw new Error(
                data.message||'No fue posible registrar a la persona.'
            );
        }

        Toastify({
            text:data.message,
            duration:3000,
            gravity:'top',
            position:'right',
            style:{
                background:'linear-gradient(to right,#183B6B,#16BFFD)'
            }
        }).showToast();

        $('#addPersonModal').modal('hide');
        $('#createPersonForm')[0].reset();

        await Swal.fire({
            icon:'success',
            title:'Persona registrada',
            html:`
                <div class="registered-person-result">
                    <span class="registered-person-icon">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <p>El folio único generado es:</p>
                    <strong id="folioCliente">${data.folio}</strong>
                    <button type="button" class="copy-person-folio-button" data-folio="${data.folio}">
                        <i class="fas fa-copy"></i>
                        Copiar folio
                    </button>
                </div>
            `,
            confirmButtonText:'Finalizar',
            confirmButtonColor:'#176dd3',
            allowOutsideClick:false
        });

        location.reload();
    }catch(error){
        console.error(error);

        Swal.fire({
            icon:'error',
            title:'No se pudo registrar',
            text:error.message||'Ocurrió un error inesperado.',
            confirmButtonColor:'#176dd3'
        });
    }finally{
        submitButton.prop('disabled',false).html(originalContent);
    }
});

$(document).on('click','.copy-person-folio-button',async function(){
    const folio=String($(this).data('folio'));

    try{
        await navigator.clipboard.writeText(folio);

        Toastify({
            text:'Folio copiado correctamente.',
            duration:2500,
            gravity:'top',
            position:'right',
            style:{
                background:'#176dd3'
            }
        }).showToast();

        $(this).html(
            '<i class="fas fa-check"></i> Folio copiado'
        );
    }catch(error){
        console.error(error);

        Swal.fire({
            icon:'error',
            title:'No se pudo copiar',
            text:'Copia manualmente el folio mostrado.',
            confirmButtonColor:'#176dd3'
        });
    }
});

$('#persona_membresia').on('change',function(){
    const dias=Number(
        $(this).find(':selected').data('dias')
    );

    if(!dias){
        $('#fecha_ini_persona').val('');
        $('#fecha_fin_persona').val('');
        return;
    }

    const fechaActual=new Date();
    const fechaVencimiento=new Date(fechaActual);

    fechaVencimiento.setDate(
        fechaVencimiento.getDate()+dias
    );

    $('#fecha_ini_persona').val(
        formatPersonDate(fechaActual)
    );

    $('#fecha_fin_persona').val(
        formatPersonDate(fechaVencimiento)
    );
});

$('#createPersonForm').on('reset',function(){
    setTimeout(()=>{
        $('#fecha_ini_persona').val('');
        $('#fecha_fin_persona').val('');
    },0);
});

$('#addPersonModal').on('shown.bs.modal',function(){
    $('#persona_nombre').trigger('focus');
});

function formatPersonDate(date){
    const year=date.getFullYear();
    const month=String(date.getMonth()+1).padStart(2,'0');
    const day=String(date.getDate()).padStart(2,'0');

    return `${year}-${month}-${day}`;
}