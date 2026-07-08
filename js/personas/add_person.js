//FETCH PARA CREAR PERSONA
$('#createPersonForm').submit(async function(e){

    e.preventDefault();

    let formData = {

        nombre: $('#persona_nombre').val(),
        fecha_ini: $('#fecha_ini_persona').val(),
        fecha_fin: $('#fecha_fin_persona').val(),
        membresia_id: $('#persona_membresia').val()
    };

    try{

        let response = await fetch('/api/create_person.php',{
            
            method: 'POST',
            headers: {
                'Content-Type':'application/json'
            },

            body: JSON.stringify(formData)
        });

        let data = await response.json();

        if(data.status === 'success'){

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

            $('#addPersonModal').modal('hide');
            $('#createPersonForm')[0].reset();

            Swal.fire({
                icon:'success',
                title:'Persona registrada',
                
                html:`

                    <p>

                        Su folio único es:

                    </p>

                    <h3
                        id="folioCliente"
                        style="
                            color:#183B6B;
                            font-weight:bold;
                        ">

                        ${data.folio}

                    </h3>

                    <button
                        class="btn btn-save"
                        onclick="
                            navigator.clipboard.writeText(
                                '${data.folio}'
                            )
                        ">

                        Copiar Folio

                    </button>

                `,

                confirmButtonColor:'#183B6B'

            }).then(() => {

                location.reload();

            });

        }else{

            Swal.fire({

                icon:'error',
                title:'error',
                text:data.message,
                confirmButtonColor:'#183B6B'
            });

        }
    }catch(error){
        console.log(error);
    }
});

//AUTOLLENADO DE FECHAS DE PERSONAS
$('#persona_membresia').change(function(){

    let dias = $(this)
        .find(':selected')
        .data('dias');

    if(!dias){
        return;
    }

    let fechaActual = new Date();

    let fechaVencimiento = new Date(

        fechaActual.getTime() +
        dias * 24 * 60 * 60 * 1000
    );

    let fechaInicio = fechaActual
        .toISOString()
        .split('T')[0];

    let fechaFin = fechaVencimiento
        .toISOString()
        .split('T')[0];

    $('#fecha_ini_persona')
        .val(fechaInicio);

    $('#fecha_fin_persona')
        .val(fechaFin);

});
