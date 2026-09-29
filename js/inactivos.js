//BUSCADOR
$('#searchInactive').on('keyup', function(){

    let valor = $(this).val().toLowerCase();

    $('#inactiveTable tbody tr').filter(function(){
        
        $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
    });
});

//HABILITAR PERSONA
$(document).on('click', '.enableBtn', async function(){

    let id = $(this).data('id');
    let nombre = $(this).data('nombre');
    
    let result = await Swal.fire({
        
        icon:'question',
        title:'Habilitar Persona',
        text:'¿Desea habilitar a ' + nombre + '?',
        showCancelButton:true,
        confirmButtonText:'Habilitar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:'#28a745',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){

        return;
    }

    try{

        let result = await apiFetch('/api/enable_person.php',{
            method: 'POST',
            body:JSON.stringify({
                id:id
            })
        });

        if(!result) return;

        let data = result.data;

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
            }, 1000);
        }else{

            Swal.fire({

                icon:'error',
                title:'Error',
                text:data.message
            });
        }
    }catch(error){
        console.log(error);

        Swal.fire({

            icon:'error',
            title:'Error',
            text:'Ocurrió un error inesperado'
        });
    }
});