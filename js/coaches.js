//CREAR COACH

$('#createCoachForm').on('submit', async function(e){

    e.preventDefault();

    let formData = new FormData();

    formData.append('nombre', $('#coach_nombre').val());

    formData.append('edad', $('#coach_edad').val());

    formData.append('especialidad', $('#coach_especialidad').val());

    formData.append('descripcion', $('#coach_descripcion').val());

    formData.append('foto', $('#coach_foto')[0].files[0]);

    try{

        let response = await fetch('api/create_coach.php', {
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

            $('#addCoachModal').modal('hide');
            $('#createCoachForm')[0].reset();
            $('#previewCoach').hide();

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
            text:'No fue posible registrar el coach.',
            confirmButtonColor:'#183B6B'

        });

    }

});

//ABRIR MODAL EDITAR

$(document).on('click', '.editCoachBtn', function(){

    $('#edit_id').val($(this).data('id'));
    $('#edit_nombre').val($(this).data('nombre'));
    $('#edit_edad').val($(this).data('edad'));
    $('#edit_especialidad').val($(this).data('especialidad'));
    $('#edit_descripcion').val($(this).data('descripcion'));
    $('#editPreviewCoach').attr('src', $(this).data('foto'));
    $('#editCoachModal').modal('show');
});

//PREVIEW AL CAMBIAR FOTO
$('#edit_foto').on('change', function(){

    let file = this.files[0];

    if(!file) return;

    let reader = new FileReader();

    reader.onload = function(e){

        $('#editPreviewCoach').attr('src', e.target.result);
    };

    reader.readAsDataURL(file);
})

//ACTUALIZAR COACH

$('#editCoachForm').on('submit', async function(e){

    e.preventDefault();

    let formData = new FormData();

    formData.append('id', $('#edit_id').val());
    formData.append('nombre', $('#edit_nombre').val());
    formData.append('edad', $('#edit_edad').val());
    formData.append('especialidad', $('#edit_especialidad').val());
    formData.append('descripcion', $('#edit_descripcion').val());

    let nuevaFoto = $('#edit_foto')[0].files[0];

    if(nuevaFoto){

        formData.append('foto', nuevaFoto);
    }

    try{

        let response = await fetch('/api/update_coach.php', {

            method: 'POST',
            body:formData
        });

        let data = await response.json();

        if(data.success){

            Toastify({

                text:data.message,
                duration:3000,
                gravity:"top",
                position:"right",

                style:{

                background:
                "linear-gradient(to right,#183B6B,#16BFFD)"

                }
            }).showToast();

            $('#editCoachModal').modal('hide');

            setTimeout(() => {
                location.reload();
            },1000)
        }else{

            Swal.fire({
                
                icon:'error',
                title:'Error',
                text:data.message,
                confirmButtonColor:'183B6B'
            });
        }
    }catch(error){

        console.error(error);

        Swal.fire({

            icon:'error',
            title:'Error',
            text:'No fue posible actualizar al coach.',
            confirmButtonColor:'183B6B'
        });
    }

});

//INACTIVAR COACH
$(document).on('click', '.deleteCoachBtn', async function(){

    let id = $(this).data('id');
    let nombre = $(this).data('nombre');

    let result = await Swal.fire({

        icon:'warning',
        title:'¿Eliminar coach?',
        text:`${nombre} dejará de mostrarse.`,
        showCancelButton:true,
        confirmButtonText:'Eliminar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc3545',
        cancelButtonColor:'#183B6B'
    });

    if(!result.isConfirmed){

        return;
    }

    try{

        let response = await fetch('/api/delete_coach.php',{

            method: 'POST',
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
                gravity:"top",
                position:"right",

                style:{
                    background:
                    "linear-gradient(to right,#183B6B,#16BFFD)"

                }
            }).showToast();

            setTimeout(()=> {
                location.reload();
            }, 1000);
        }else{

            Swal.fire({

                icon:'error',
                title:'Error',
                text:data.message
            });
        }
    }catch(error){
        console.error(error);
    }
});

//ACTIVACION O DESACTIVACION DE COACH

$(document).on('click', '.toggleCoachBtn', async function(){

    let id = $(this).data('id');
    let estado = $(this).data('estado');
    let nombre = $(this).data('nombre');

    let accion = estado == 1
    ? 'activar'
    : 'desactivar';

    let result = await Swal.fire({

        icon:'warning',
        title:`¿Desea ${accion} este coach?`,
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

        let response = await fetch('/api/toggle_coach.php', {
            
            method: 'POST',
            headers:{

                'Content-Type':'application/x-www-form-urlencoded'
            },

            body: `id=${id}&estado=${estado}`
        });

        let data = await response.json();

        if(data.success){

            Toastify({
                text:data.message,
                duration:3000,
                gravity:"top",
                position:"right",

                style:{

                    background:
                    "linear-gradient(to right,#183B6B,#16BFFD)"

                }
            }).showToast();

            setTimeout(() => {
                location.reload()
            }, 1000);
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