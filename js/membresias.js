let currentMembershipFilter='all';

function filterMemberships(){
    const searchValue=$('#searchMembership').val().toLowerCase().trim();
    let visibleMemberships=0;

    $('.membership-card').each(function(){
        const card=$(this);
        const searchableText=String(card.data('search')).toLowerCase();
        const status=String(card.data('status'));
        const promotion=String(card.data('promotion'));

        const matchesSearch=searchableText.includes(searchValue);

        let matchesFilter=true;

        if(currentMembershipFilter==='active'){
            matchesFilter=status==='active';
        }

        if(currentMembershipFilter==='inactive'){
            matchesFilter=status==='inactive';
        }

        if(currentMembershipFilter==='promotion'){
            matchesFilter=promotion==='promotion';
        }

        const shouldShow=matchesSearch&&matchesFilter;

        card.toggle(shouldShow);

        if(shouldShow){
            visibleMemberships++;
        }
    });

    $('#visibleMembershipsCount').text(visibleMemberships);

    $('#membershipsEmptyState').toggleClass(
        'd-none',
        visibleMemberships>0
    );
}

$('#searchMembership').on('input',function(){
    filterMemberships();
});

$('#clearMembershipSearch').on('click',function(){
    $('#searchMembership').val('').trigger('focus');
    filterMemberships();
});

$('.membership-filter-button').on('click',function(){
    currentMembershipFilter=String(
        $(this).data('filter')
    );

    $('.membership-filter-button').removeClass('active');
    $(this).addClass('active');

    filterMemberships();
});

$('#promocion').on('change',function(){
    const promotionEnabled=$(this).is(':checked');

    $('#promoContainer').toggleClass(
        'd-none',
        !promotionEnabled
    );

    $('#precio_promocion').prop(
        'required',
        promotionEnabled
    );

    if(!promotionEnabled){
        $('#precio_promocion').val('');
    }
});

$('#edit_promocion').on('change',function(){
    const promotionEnabled=$(this).is(':checked');

    $('#editPromoContainer').toggleClass(
        'd-none',
        !promotionEnabled
    );

    $('#edit_precio_promocion').prop(
        'required',
        promotionEnabled
    );

    if(!promotionEnabled){
        $('#edit_precio_promocion').val('');
    }
});

$(document).on('click','.editMembershipBtn',function(){
    const promocion=Number(
        $(this).data('promocion')
    )===1;

    $('#edit_id').val(
        $(this).data('id')
    );

    $('#edit_nombre').val(
        $(this).data('nombre')
    );

    $('#edit_descripcion').val(
        $(this).data('descripcion')
    );

    $('#edit_precio').val(
        $(this).data('precio')
    );

    $('#edit_dias').val(
        $(this).data('dias')
    );

    $('#edit_promocion').prop(
        'checked',
        promocion
    );

    $('#editPromoContainer').toggleClass(
        'd-none',
        !promocion
    );

    $('#edit_precio_promocion')
        .prop('required',promocion)
        .val(
            promocion
                ? $(this).data('precio_promocion')
                : ''
        );

    $('#editMembershipModal').modal('show');
});

$('#createMembershipForm').on('submit',async function(event){
    event.preventDefault();

    const button=$('#createMembershipBtn');
    const originalContent=button.html();

    const nombre=$('#nombre').val().trim();
    const descripcion=$('#descripcion').val().trim();
    const precio=Number($('#precio').val());
    const dias=Number($('#dias').val());
    const promocion=$('#promocion').is(':checked');
    const precioPromocion=Number(
        $('#precio_promocion').val()
    );

    if(!validateMembershipData(
        nombre,
        precio,
        dias,
        promocion,
        precioPromocion
    )){
        return;
    }

    const formData=new FormData();

    formData.append('nombre',nombre);
    formData.append('descripcion',descripcion);
    formData.append('precio',precio);
    formData.append('promocion',promocion?1:0);
    formData.append(
        'precio_promocion',
        promocion?precioPromocion:''
    );
    formData.append('dias',dias);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Guardando...'
    );

    try{
        const result=await apiFetch(
            'api/create_membership.php',
            {
                method:'POST',
                body:formData
            }
        );

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message
                ||'No fue posible registrar la membresía.'
            );
        }

        showMembershipToast(data.message);

        $('#addMembershipModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showMembershipError(
            error.message
            ||'No fue posible registrar la membresía.'
        );
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$('#editMembershipForm').on('submit',async function(event){
    event.preventDefault();

    const button=$('#updateMembershipAdminBtn');
    const originalContent=button.html();

    const id=$('#edit_id').val();
    const nombre=$('#edit_nombre').val().trim();
    const descripcion=$('#edit_descripcion').val().trim();
    const precio=Number($('#edit_precio').val());
    const dias=Number($('#edit_dias').val());
    const promocion=$('#edit_promocion').is(':checked');
    const precioPromocion=Number(
        $('#edit_precio_promocion').val()
    );

    if(!validateMembershipData(
        nombre,
        precio,
        dias,
        promocion,
        precioPromocion
    )){
        return;
    }

    const formData=new FormData();

    formData.append('id',id);
    formData.append('nombre',nombre);
    formData.append('descripcion',descripcion);
    formData.append('precio',precio);
    formData.append('promocion',promocion?1:0);
    formData.append(
        'precio_promocion',
        promocion?precioPromocion:''
    );
    formData.append('dias',dias);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Guardando...'
    );

    try{
        const result=await apiFetch(
            'api/update_membership_admin.php',
            {
                method:'POST',
                body:formData
            }
        );

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message
                ||'No fue posible actualizar la membresía.'
            );
        }

        showMembershipToast(data.message);

        $('#editMembershipModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showMembershipError(
            error.message
            ||'No fue posible actualizar la membresía.'
        );
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$(document).on('click','.toggleMembershipBtn',async function(){
    const id=$(this).data('id');
    const estadoActual=Number(
        $(this).data('estado')
    );

    const nombre=$(this).data('nombre');
    const nuevoEstado=estadoActual===1?0:1;
    const accion=nuevoEstado===1?'activar':'desactivar';

    const result=await Swal.fire({
        icon:nuevoEstado===1?'question':'warning',
        title:`¿Deseas ${accion} esta membresía?`,
        text:nombre,
        showCancelButton:true,
        confirmButtonText:nuevoEstado===1
            ?'Activar'
            :'Desactivar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:nuevoEstado===1
            ?'#23984f'
            :'#d99400',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){
        return;
    }

    try{
        const result=await apiFetch(
            'api/toggle_membership.php',
            {
                method:'POST',
                headers:{
                    'Content-Type':
                    'application/x-www-form-urlencoded'
                },
                body:new URLSearchParams({
                    id:id,
                    estado:nuevoEstado
                }).toString()
            }
        );

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message
                ||'No fue posible actualizar el estado.'
            );
        }

        showMembershipToast(data.message);

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showMembershipError(
            error.message
            ||'No fue posible actualizar el estado.'
        );
    }
});

$(document).on('click','.deleteMembershipBtn',async function(){
    const id=$(this).data('id');
    const nombre=$(this).data('nombre');

    const result=await Swal.fire({
        icon:'warning',
        title:'¿Eliminar definitivamente?',
        text:`La membresía "${nombre}" será eliminada.`,
        showCancelButton:true,
        confirmButtonText:'Eliminar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#d93a62',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){
        return;
    }

    try{
        const result=await apiFetch(
            'api/delete_membership.php',
            {
                method:'POST',
                headers:{
                    'Content-Type':
                    'application/x-www-form-urlencoded'
                },
                body:new URLSearchParams({
                    id:id
                }).toString()
            }
        );

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message
                ||'No fue posible eliminar la membresía.'
            );
        }

        Toastify({
            text:data.message,
            duration:3000,
            gravity:'top',
            position:'right',
            style:{
                background:
                'linear-gradient(to right,#d93a62,#ff6b81)'
            }
        }).showToast();

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showMembershipError(
            error.message
            ||'No fue posible eliminar la membresía.'
        );
    }
});

$('#addMembershipModal').on('hidden.bs.modal',function(){
    $('#createMembershipForm')[0].reset();
    $('#promoContainer').addClass('d-none');
    $('#precio_promocion').prop('required',false).val('');
});

$('#editMembershipModal').on('hidden.bs.modal',function(){
    $('#editMembershipForm')[0].reset();
    $('#editPromoContainer').addClass('d-none');

    $('#edit_precio_promocion')
        .prop('required',false)
        .val('');
});

function validateMembershipData(
    nombre,
    precio,
    dias,
    promocion,
    precioPromocion
){
    if(nombre.length<2){
        showMembershipWarning(
            'Nombre no válido',
            'El nombre debe contener al menos 2 caracteres.'
        );

        return false;
    }

    if(!Number.isFinite(dias)||dias<1){
        showMembershipWarning(
            'Duración no válida',
            'La duración debe ser de al menos un día.'
        );

        return false;
    }

    if(!Number.isFinite(precio)||precio<0){
        showMembershipWarning(
            'Precio no válido',
            'Ingresa un precio normal válido.'
        );

        return false;
    }

    if(
        promocion
        &&(
            !Number.isFinite(precioPromocion)
            ||precioPromocion<0
        )
    ){
        showMembershipWarning(
            'Promoción no válida',
            'Ingresa un precio de promoción válido.'
        );

        return false;
    }

    if(promocion&&precioPromocion>=precio){
        showMembershipWarning(
            'Revisa la promoción',
            'El precio promocional debe ser menor al precio normal.'
        );

        return false;
    }

    return true;
}

function showMembershipWarning(title,message){
    Swal.fire({
        icon:'warning',
        title:title,
        text:message,
        confirmButtonColor:'#176dd3'
    });
}

function showMembershipError(message){
    Swal.fire({
        icon:'error',
        title:'Error',
        text:message,
        confirmButtonColor:'#176dd3'
    });
}

function showMembershipToast(message){
    Toastify({
        text:message,
        duration:3000,
        gravity:'top',
        position:'right',
        style:{
            background:
            'linear-gradient(to right,#183B6B,#16BFFD)'
        }
    }).showToast();
}

filterMemberships();