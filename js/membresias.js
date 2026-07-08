$('#searchMembership').on('keyup', function(){

    let value = $(this).val().toLowerCase();

    $('#membershipTable tbody tr').filter(function(){

        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

//CREAR PROMOCION

$('#promocion').on('change', function(){

    if($(this).is(':checked')){

        $('#promoContainer').removeClass('d-none');
    }else{

        $('#promoContainer').addClass('d-none');
        $('#precio_promocion').val('');
    }
});

//EDITAR PROMOCION

$('#edit_promocion').on('change', function(){

    if($(this).is(':checked')){

        $('#editPromoContainer').removeClass('d-none');
    }else{

        $('#editPromoContainer').addClass('d-none');
        $('#edit_precio_promocion').val('');
    }
});

//ABRIR MODAL EDITAR

$(document).on('click', '.editMembershipBtn', function(){

    let id = $(this).data('id');
    let nombre = $(this).data('nombre');
    let descripcion = $(this).data('descripcion');
    let precio = $(this).data('precio');
    let promocion = $(this).data('promocion');
    let precioPromocion = $(this).data('precio_promocion');
    let dias = $(this).data('dias');
    
    $('#edit_id').val(id);
    $('#edit_nombre').val(nombre);
    $('#edit_descripcion').val(descripcion);
    $('#edit_precio').val(precio);
    $('#edit_dias').val(dias);

    if(promocion == 1){

        $('#edit_promocion').prop('checked', true);
        $('#editPromoContainer').removeClass('d-none');
        $('#edit_precio_promocion').val(precioPromocion);

    }else{

        $('#edit_promocion').prop('checked', false);
        $('#editPromoContainer').addClass('d-none');
        $('#edit_precio_promocion').val('');
    }

    $('#editMembershipModal').modal('show');
});

// CREAR MEMBRESIA

$('#createMembershipForm').on('submit', async function(e){

        e.preventDefault();

        let formData = new FormData();

        formData.append('nombre', $('#nombre').val());
        formData.append('descripcion', $('#descripcion').val());
        formData.append('precio', $('#precio').val());
        formData.append('promocion', $('#promocion').is(':checked') ? 1 : 0);
        formData.append('precio_promocion', $('#precio_promocion').val());
        formData.append('dias', $('#dias').val());

        try{

            let response = await fetch('api/create_membership.php', {

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

                $('#addMembershipModal').modal('hide');

                $('#createMembershipForm')[0].reset();

                $('#promoContainer').addClass('d-none');

                setTimeout(() => {

                    location.reload();

                },1000);

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
                text:'No fue posible registrar la membresía.',
                confirmButtonColor:'#183B6B'
            });

        }

    });

// EDITAR MEMBRESIA


$('#editMembershipForm').on('submit', async function(e){

        e.preventDefault();

        let formData = new FormData();

        formData.append('id', $('#edit_id').val());
        formData.append('nombre', $('#edit_nombre').val());
        formData.append('descripcion', $('#edit_descripcion').val());
        formData.append('precio', $('#edit_precio').val());
        formData.append('promocion', $('#edit_promocion').is(':checked') ? 1 : 0);
        formData.append('precio_promocion', $('#edit_precio_promocion').val());
        formData.append('dias', $('#edit_dias').val());

        try{

            let response = await fetch('api/update_membership_admin.php', {

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

                $('#editMembershipModal').modal('hide');

                setTimeout(() => {

                    location.reload();

                },1000);

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
                text:'No fue posible actualizar la membresía.',
                confirmButtonColor:'#183B6B'
            });

        }

    });

// ACTIVAR / DESACTIVAR MEMBRESIA

$(document).on('click', '.toggleMembershipBtn', async function(){

        let id = $(this).data('id');
        let estadoActual = parseInt($(this).data('estado'));
        let nombre = $(this).data('nombre');

        let nuevoEstado = estadoActual === 1 
        ? 0 
        : 1;

        let accion = nuevoEstado === 1
        ? 'activar'
        : 'desactivar';

        let result = await Swal.fire({

            icon:'warning',
            title:`¿Desea ${accion} esta membresía?`,
            text:nombre,
            showCancelButton:true,
            confirmButtonText:'Aceptar',
            cancelButtonText:'Cancelar',
            confirmButtonColor:'#183B6B',
            cancelButtonColor:'#dc3545'
        });

        if(!result.isConfirmed){

            return;
        }

        try{

            let response = await fetch('api/toggle_membership.php', {

                    method:'POST',
                    headers:{

                        'Content-Type': 'application/x-www-form-urlencoded'
                    },

                    body: `id=${id}&estado=${nuevoEstado}`

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

                setTimeout(() => {

                    location.reload();

                },1000);

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
                text:'No fue posible actualizar el estado.',
                confirmButtonColor:'#183B6B'
            });

        }
    });

    // =====================================
// ELIMINAR MEMBRESIA
// =====================================

$(document).on('click', '.deleteMembershipBtn', async function(){

        let id = $(this).data('id');

        let nombre = $(this).data('nombre');

        let result = await Swal.fire({

            icon:'warning',
            title:'¿Eliminar definitivamente?',
            text:`La membresía "${nombre}" será eliminada.`,
            showCancelButton:true,
            confirmButtonText:'Eliminar',
            cancelButtonText:'Cancelar',
            confirmButtonColor:'#dc3545',
            cancelButtonColor:'#6c757d'
        });

        if(!result.isConfirmed){

            return;
        }

        try{

            let response = await fetch('api/delete_membership.php', {

                    method:'POST',
                    headers:{
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },

                    body:`id=${id}`
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
                        "linear-gradient(to right,#dc3545,#ff6b6b)"
                    }

                }).showToast();

                setTimeout(() => {
                    location.reload();
                },1000);

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
                text:'No fue posible eliminar la membresía.',
                confirmButtonColor:'#183B6B'
            });

        }
    });