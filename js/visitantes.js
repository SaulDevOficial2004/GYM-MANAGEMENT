function filterVisitors(){
    const searchValue=$('#searchVisitantes').val().toLowerCase().trim();
    let visibleVisits=0;

    $('.visitor-crm-card').each(function(){
        const card=$(this);
        const searchableText=String(card.data('search')).toLowerCase();
        const isVisible=searchableText.includes(searchValue);

        card.toggle(isVisible);

        if(isVisible){
            visibleVisits++;
        }
    });

    $('.visit-date-group').each(function(){
        const group=$(this);
        const visibleCards=group.find('.visitor-crm-card:visible').length;

        group.toggle(visibleCards>0);
        group.find('.visit-date-counter').text(
            `${visibleCards} ${visibleCards===1?'visita':'visitas'}`
        );
    });

    $('#visibleVisitsCount').text(visibleVisits);
    $('#visitorsEmptyState').toggleClass('d-none',visibleVisits>0);
}

$('#searchVisitantes').on('input',function(){
    filterVisitors();
});

$('#clearVisitorSearch').on('click',function(){
    $('#searchVisitantes').val('').trigger('focus');
    filterVisitors();
});

$(document).on('click','.btn-register-visit',async function(){
    const button=$(this);
    const visitanteId=button.data('id');
    const nombre=button.data('nombre');
    const originalContent=button.html();

    const result=await Swal.fire({
        icon:'question',
        title:'¿Registrar nueva visita?',
        text:`Se registrará una nueva entrada para ${nombre}.`,
        showCancelButton:true,
        confirmButtonText:'Registrar visita',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#176dd3',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){
        return;
    }

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i><span>Registrando...</span>'
    );

    try{
        const result=await apiFetch('api/create_visit.php',{
            method:'POST',
            body:JSON.stringify({
                visitante_id:visitanteId
            })
        });

        if(!result) return;

        const data=result.data;

        if(data.status!=='success'){
            throw new Error(
                data.message||'No fue posible registrar la visita.'
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

        button.html(
            '<i class="fas fa-check"></i><span>Visita registrada</span>'
        );

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        button.prop('disabled',false).html(originalContent);

        Swal.fire({
            icon:'error',
            title:'No se pudo registrar',
            text:error.message||'Ocurrió un error inesperado.',
            confirmButtonColor:'#176dd3'
        });
    }
});

filterVisitors();