let currentReceptionistFilter='all';

function filterReceptionists(){
    const searchValue=$('#searchReceptionist').val().toLowerCase().trim();
    let visibleReceptionists=0;

    $('.receptionist-crm-card').each(function(){
        const card=$(this);
        const searchableText=String(card.data('search')).toLowerCase();
        const status=String(card.data('status'));
        const matchesSearch=searchableText.includes(searchValue);
        const matchesFilter=currentReceptionistFilter==='all'||status===currentReceptionistFilter;
        const shouldShow=matchesSearch&&matchesFilter;

        card.toggle(shouldShow);

        if(shouldShow){
            visibleReceptionists++;
        }
    });

    $('#visibleReceptionistsCount').text(visibleReceptionists);
    $('#receptionistsEmptyState').toggleClass('d-none',visibleReceptionists>0);
}

$('#searchReceptionist').on('input',function(){
    filterReceptionists();
});

$('#clearReceptionistSearch').on('click',function(){
    $('#searchReceptionist').val('').trigger('focus');
    filterReceptionists();
});

$('.receptionist-filter-button').on('click',function(){
    currentReceptionistFilter=String($(this).data('filter'));
    $('.receptionist-filter-button').removeClass('active');
    $(this).addClass('active');
    filterReceptionists();
});

$('#receptionist_telefono,#edit_receptionist_telefono').on('input',function(){
    this.value=this.value.replace(/\D/g,'').slice(0,10);
});

function togglePassword(inputSelector,button){
    const input=$(inputSelector);
    const icon=$(button).find('i');
    const showPassword=input.attr('type')==='password';

    input.attr('type',showPassword?'text':'password');

    icon.toggleClass('fa-eye',!showPassword);
    icon.toggleClass('fa-eye-slash',showPassword);
}

$('#toggleReceptionistPassword').on('click',function(){
    togglePassword('#receptionist_password',this);
});

$('#toggleEditReceptionistPassword').on('click',function(){
    togglePassword('#edit_receptionist_password',this);
});

$('#toggleResetReceptionistPassword').on('click',function(){
    togglePassword('#reset_receptionist_password',this);
});

$('#createReceptionistForm').on('submit',async function(event){
    event.preventDefault();

    const nombre=$('#receptionist_nombre').val().trim();
    const telefono=$('#receptionist_telefono').val().trim();
    const password=$('#receptionist_password').val();
    const passwordConfirm=$('#receptionist_password_confirm').val();
    const button=$('#createReceptionistBtn');
    const originalContent=button.html();

    if(!validateReceptionistData(nombre,telefono,password,passwordConfirm,true)){
        return;
    }

    const formData=new FormData();
    formData.append('nombre',nombre);
    formData.append('telefono',telefono);
    formData.append('password',password);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Registrando...'
    );

    try{
        const result=await apiFetch('api/create_receptionist.php',{
            method:'POST',
            body:formData
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(data.message||'No fue posible registrar al recepcionista.');
        }

        showReceptionistToast(data.message);
        $('#addReceptionistModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);
        showReceptionistError(error.message||'No fue posible registrar al recepcionista.');
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$('#addReceptionistModal').on('hidden.bs.modal',function(){
    $('#createReceptionistForm')[0].reset();
    $('#receptionist_password').attr('type','password');
    $('#toggleReceptionistPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
});

$(document).on('click','.editReceptionistBtn',function(){
    $('#edit_receptionist_id').val($(this).data('id'));
    $('#edit_receptionist_nombre').val($(this).data('nombre'));
    $('#edit_receptionist_telefono').val($(this).data('telefono'));
    $('#edit_receptionist_password').val('').attr('type','password');
    $('#edit_receptionist_password_confirm').val('');
    $('#editPasswordConfirmContainer').addClass('d-none');
    $('#toggleEditReceptionistPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
    $('#editReceptionistModal').modal('show');
});

$('#edit_receptionist_password').on('input',function(){
    const hasPassword=$(this).val()!=='';

    $('#editPasswordConfirmContainer').toggleClass('d-none',!hasPassword);

    if(!hasPassword){
        $('#edit_receptionist_password_confirm').val('');
    }
});

$('#editReceptionistForm').on('submit',async function(event){
    event.preventDefault();

    const id=$('#edit_receptionist_id').val();
    const nombre=$('#edit_receptionist_nombre').val().trim();
    const telefono=$('#edit_receptionist_telefono').val().trim();
    const password=$('#edit_receptionist_password').val();
    const passwordConfirm=$('#edit_receptionist_password_confirm').val();
    const button=$('#updateReceptionistBtn');
    const originalContent=button.html();

    if(!validateReceptionistData(nombre,telefono,password,passwordConfirm,false)){
        return;
    }

    const formData=new FormData();
    formData.append('id',id);
    formData.append('nombre',nombre);
    formData.append('telefono',telefono);
    formData.append('password',password);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Guardando...'
    );

    try{
        const result=await apiFetch('api/update_receptionist.php',{
            method:'POST',
            body:formData
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(data.message||'No fue posible actualizar al recepcionista.');
        }

        showReceptionistToast(data.message);
        $('#editReceptionistModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);
        showReceptionistError(error.message||'No fue posible actualizar al recepcionista.');
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$('#editReceptionistModal').on('hidden.bs.modal',function(){
    $('#editReceptionistForm')[0].reset();
    $('#editPasswordConfirmContainer').addClass('d-none');
    $('#edit_receptionist_password').attr('type','password');
    $('#toggleEditReceptionistPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
});

$(document).on('click','.toggleReceptionistBtn',async function(){
    const id=$(this).data('id');
    const estado=Number($(this).data('estado'));
    const nombre=$(this).data('nombre');
    const accion=estado===1?'activar':'desactivar';

    const result=await Swal.fire({
        icon:estado===1?'question':'warning',
        title:`¿Deseas ${accion} este recepcionista?`,
        text:nombre,
        showCancelButton:true,
        confirmButtonText:estado===1?'Activar':'Desactivar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:estado===1?'#23984f':'#d93a62',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){
        return;
    }

    const formData=new FormData();
    formData.append('id',id);
    formData.append('estado',estado);

    try{
        const result=await apiFetch('api/toggle_receptionist.php',{
            method:'POST',
            body:formData
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(data.message||'No fue posible actualizar el estado.');
        }

        showReceptionistToast(data.message);

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);
        showReceptionistError(error.message||'No fue posible actualizar el estado del recepcionista.');
    }
});

$(document).on('click','.resetReceptionistPasswordBtn',function(){
    $('#reset_receptionist_id').val($(this).data('id'));
    $('#resetReceptionistName').text($(this).data('nombre'));
    $('#reset_receptionist_password').val('').attr('type','password');
    $('#reset_receptionist_password_confirm').val('');
    $('#toggleResetReceptionistPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
    $('#resetReceptionistPasswordModal').modal('show');
});

$('#resetReceptionistPasswordForm').on('submit',async function(event){
    event.preventDefault();

    const id=$('#reset_receptionist_id').val();
    const password=$('#reset_receptionist_password').val();
    const passwordConfirm=$('#reset_receptionist_password_confirm').val();
    const button=$('#resetReceptionistPasswordSubmitBtn');
    const originalContent=button.html();

    if(password.length<6){
        showReceptionistWarning(
            'Contraseña no válida',
            'La contraseña debe contener al menos 6 caracteres.'
        );
        return;
    }

    if(password!==passwordConfirm){
        showReceptionistWarning(
            'Las contraseñas no coinciden',
            'Confirma correctamente la nueva contraseña.'
        );
        return;
    }

    const confirmation=await Swal.fire({
        icon:'warning',
        title:'¿Restablecer contraseña?',
        text:'La contraseña anterior dejará de funcionar.',
        showCancelButton:true,
        confirmButtonText:'Restablecer',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#d99400',
        cancelButtonColor:'#6c757d'
    });

    if(!confirmation.isConfirmed){
        return;
    }

    const formData=new FormData();
    formData.append('id',id);
    formData.append('password',password);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Restableciendo...'
    );

    try{
        const result=await apiFetch('api/reset_receptionist_password.php',{
            method:'POST',
            body:formData
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(data.message||'No fue posible restablecer la contraseña.');
        }

        showReceptionistToast(data.message);
        $('#resetReceptionistPasswordModal').modal('hide');
    }catch(error){
        console.error(error);
        showReceptionistError(error.message||'No fue posible restablecer la contraseña.');
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$('#resetReceptionistPasswordModal').on('hidden.bs.modal',function(){
    $('#resetReceptionistPasswordForm')[0].reset();
    $('#resetReceptionistName').text('');
    $('#reset_receptionist_password').attr('type','password');
    $('#toggleResetReceptionistPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
});

function validateReceptionistData(nombre,telefono,password,passwordConfirm,passwordRequired){
    if(nombre.length<3){
        showReceptionistWarning(
            'Nombre no válido',
            'El nombre debe contener al menos 3 caracteres.'
        );
        return false;
    }

    if(!/^[0-9]{10}$/.test(telefono)){
        showReceptionistWarning(
            'Teléfono no válido',
            'El teléfono debe contener exactamente 10 números.'
        );
        return false;
    }

    if(passwordRequired&&password.length<6){
        showReceptionistWarning(
            'Contraseña no válida',
            'La contraseña debe contener al menos 6 caracteres.'
        );
        return false;
    }

    if(!passwordRequired&&password!==''&&password.length<6){
        showReceptionistWarning(
            'Contraseña no válida',
            'La nueva contraseña debe contener al menos 6 caracteres.'
        );
        return false;
    }

    if(password!==''&&password!==passwordConfirm){
        showReceptionistWarning(
            'Las contraseñas no coinciden',
            'Confirma correctamente la contraseña.'
        );
        return false;
    }

    return true;
}

function showReceptionistWarning(title,message){
    Swal.fire({
        icon:'warning',
        title:title,
        text:message,
        confirmButtonColor:'#176dd3'
    });
}

function showReceptionistError(message){
    Swal.fire({
        icon:'error',
        title:'Error',
        text:message,
        confirmButtonColor:'#176dd3'
    });
}

function showReceptionistToast(message){
    Toastify({
        text:message,
        duration:3000,
        gravity:'top',
        position:'right',
        style:{
            background:'linear-gradient(to right,#183B6B,#16BFFD)'
        }
    }).showToast();
}

filterReceptionists();