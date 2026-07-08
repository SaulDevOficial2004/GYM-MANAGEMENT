//BUSCADOR DE PERSONAS
$('#searchPerson').on('keyup', function(){

    let valor = $(this).val().toLowerCase();

    $('#personsTable tbody tr').filter(function(){

        $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
    });
})

//ABRIR MODAL EDITAR

$(document).on('click', '.editBtn', function(){

    $('#edit_id').val($(this).data('id'));
    $('#edit_nombre').val($(this).data('nombre'));
    $('#edit_folio').val($(this).data('folio'));
    $('#edit_fecha_ini').val($(this).data('fecha_ini'));
    $('#edit_fecha_fin').val($(this).data('fecha_fin'));
    $('#editPersonModal').modal('show');
});

$('#editPersonForm').submit(async function(e){

    e.preventDefault();

    let formData = {
        id: $('#edit_id').val(),
        nombre: $('#edit_nombre').val(),
        folio: $('#edit_folio').val(),
        fecha_ini: $('#edit_fecha_ini').val(),
        fecha_fin: $('#edit_fecha_fin').val()
    };

    try{
        
        let response = await fetch('/api/update_person.php',{

            method: 'POST',
            headers: {
                'Content-Type':'application/json'
            },

            body:JSON.stringify(formData)
        });

        let data = await response.json();

        if(data.status === "success"){
            
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

            $('#editPersonModal').modal('hide');

            setTimeout(()=> {
                location.reload();
            },1000)
        }else{

            Swal.fire({

                icon:'error',
                title:'Error',
                text:data.message
            });
        }
    }catch(error){
        console.log(error);
    }
});

//ELIMINAR PERSONA

$(document).on('click', '.deleteBtn', async function(){
    let id = $(this).data('id');
    let nombre = $(this).data('nombre');

    let result = await Swal.fire({
        icon:'warning',
        title:'Eliminar persona',
        text:'¿Deseas eliminar a ' + nombre + '?',
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
        let response = await fetch('/api/delete_person.php', {
            method: 'POST',
            headers:{
                'Content-Type':'application/json'
            },

            body:JSON.stringify({
                id:id
            })
        });

        let data = await response.json();

        if(data.status === "success"){
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
                location.reload();
            }, 800)
        }else{
            Swal.fire({
                icon:'error',
                title:'Error',
                text:data.message
            });
        }
    }catch(error){
        console.log(error);
    }
});


$(document).on('click', '.toggleFolioBtn', function(){

        let id = $(this).data('id');
        let folio = $(this).data('folio');
        let span = $('#folio-' + id);

        if(span.text() === '**********'){

            span.text(folio);

            $(this).html('<i class="fas fa-eye-slash"></i>');

        }else{

            span.text('**********');

            $(this).html('<i class="fas fa-eye"></i>');

        }
    }
);