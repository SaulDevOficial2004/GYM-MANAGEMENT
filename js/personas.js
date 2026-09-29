let showInactivePersons=false;
let currentPersonFilter='all';

function filterPersons(){
    const searchValue=$('#searchPerson').val().toLowerCase().trim();
    let visibleCount=0;

    $('.person-crm-card').each(function(){
        const card=$(this);
        const systemStatus=Number(card.data('system-status'));
        const membershipStatus=String(card.data('membership-status'));
        const searchableText=String(card.data('search')).toLowerCase();
        const matchesSearch=searchableText.includes(searchValue);
        const allowedBySystem=systemStatus===1||showInactivePersons;
        const matchesFilter=currentPersonFilter==='all'||membershipStatus===currentPersonFilter;
        const shouldShow=matchesSearch&&allowedBySystem&&matchesFilter;

        card.toggle(shouldShow);

        if(shouldShow){
            visibleCount++;
        }
    });

    $('#visiblePersonsCount').text(visibleCount);
    $('#personsEmptyState').toggleClass('d-none',visibleCount>0);
}

$('#searchPerson').on('input',function(){
    filterPersons();
});

$('.person-filter-button').on('click',function(){
    currentPersonFilter=String($(this).data('filter'));
    $('.person-filter-button').removeClass('active');
    $(this).addClass('active');
    filterPersons();
});

$('#toggleInactivePersons').on('click',function(){
    showInactivePersons=!showInactivePersons;

    const icon=$(this).find('i');
    const text=$(this).find('span');

    if(showInactivePersons){
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
        text.text('Ocultar inhabilitados');
        $(this).addClass('showing-inactive');
    }else{
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
        text.text('Mostrar inhabilitados');
        $(this).removeClass('showing-inactive');
    }

    filterPersons();
});

$(document).on('click','.editBtn',function(){
    $('#edit_id').val($(this).data('id'));
    $('#edit_nombre').val($(this).data('nombre'));
    $('#edit_fecha_ini').val($(this).data('fecha_ini'));
    $('#edit_fecha_fin').val($(this).data('fecha_fin'));
    $('#editPersonModal').modal('show');
});

$('#editPersonForm').on('submit',async function(event){
    event.preventDefault();

    const formData={
        id:$('#edit_id').val(),
        nombre:$('#edit_nombre').val().trim(),
        fecha_ini:$('#edit_fecha_ini').val(),
        fecha_fin:$('#edit_fecha_fin').val()
    };

    try{
        const result=await apiFetch('api/update_person.php',{
            method:'POST',
            body:JSON.stringify(formData)
        });

        if(!result) return;

        const data=result.data;

        if(data.status==='success'){
            Toastify({
                text:data.message,
                duration:3000,
                gravity:'top',
                position:'right',
                style:{
                    background:'linear-gradient(to right,#183B6B,#16BFFD)'
                }
            }).showToast();

            $('#editPersonModal').modal('hide');

            setTimeout(()=>{
                location.reload();
            },1000);
        }else{
            showPersonError(data.message);
        }
    }catch(error){
        console.error(error);
        showPersonError('No fue posible actualizar a la persona.');
    }
});

$(document).on('click','.disablePersonBtn',async function(){
    const id=$(this).data('id');
    const nombre=$(this).data('nombre');

    const result=await Swal.fire({
        icon:'warning',
        title:'¿Inhabilitar persona?',
        text:`${nombre} dejará de aparecer en el listado principal.`,
        showCancelButton:true,
        confirmButtonText:'Inhabilitar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#d93a62',
        cancelButtonColor:'#183B6B'
    });

    if(!result.isConfirmed){
        return;
    }

    await changePersonStatus(
        'api/delete_person.php',
        id,
        'No fue posible inhabilitar a la persona.'
    );
});

$(document).on('click','.enablePersonBtn',async function(){
    const id=$(this).data('id');
    const nombre=$(this).data('nombre');

    const result=await Swal.fire({
        icon:'question',
        title:'¿Habilitar persona?',
        text:`${nombre} volverá a aparecer como habilitada en el sistema.`,
        showCancelButton:true,
        confirmButtonText:'Habilitar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#176dd3',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){
        return;
    }

    await changePersonStatus(
        'api/enable_person.php',
        id,
        'No fue posible habilitar a la persona.'
    );
});

async function changePersonStatus(url,id,errorMessage){
    try{
        const result=await apiFetch(url,{
            method:'POST',
            body:JSON.stringify({
                id:id
            })
        });

        if(!result) return;

        const data=result.data;

        if(data.status==='success'){
            Toastify({
                text:data.message,
                duration:3000,
                gravity:'top',
                position:'right',
                style:{
                    background:'linear-gradient(to right,#183B6B,#16BFFD)'
                }
            }).showToast();

            setTimeout(()=>{
                location.reload();
            },800);
        }else{
            showPersonError(data.message);
        }
    }catch(error){
        console.error(error);
        showPersonError(errorMessage);
    }
}

function showPersonError(message){
    Swal.fire({
        icon:'error',
        title:'Error',
        text:message,
        confirmButtonColor:'#183B6B'
    });
}

$(document).on('click','.copiarEnlaceBtn',async function(){
    const folio=String($(this).data('folio')||'').trim();

    if(folio===''){
        showPersonError('Folio no disponible.');
        return;
    }

    try{
        const result=await apiFetch('api/generar_enlace_cliente.php',{
            method:'POST',
            body:JSON.stringify({
                folio:folio
            })
        });

        if(!result) return;

        const data=result.data;

        if(!data.success||!data.enlace){
            throw new Error(data.message||'No fue posible generar el enlace.');
        }

        await navigator.clipboard.writeText(data.enlace);

        Toastify({
            text:'Enlace copiado. Listo para WhatsApp.',
            duration:3000,
            gravity:'top',
            position:'right',
            style:{
                background:'linear-gradient(to right,#183B6B,#16BFFD)'
            }
        }).showToast();
    }catch(error){
        console.error(error);
        showPersonError(error.message||'No fue posible copiar el enlace.');
    }
});

$(document).on('click','.toggleFolioBtn',function(){
    const id=$(this).data('id');
    const folio=$(this).data('folio');
    const span=$('#folio-'+id);
    const isHidden=span.text().trim()==='**********';

    span.text(isHidden?folio:'**********');
    $(this).html(
        isHidden
            ? '<i class="fas fa-eye-slash"></i>'
            : '<i class="fas fa-eye"></i>'
    );
});

filterPersons();