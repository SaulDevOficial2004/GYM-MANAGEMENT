//BUSCAR VISITA
$('#searchVisitantes').on('keyup', function(){

    let value = $(this).val().toLowerCase();

    $('.visitante-card').each(function(){

        let visible = $(this)
            .text()
            .toLowerCase()
            .includes(value);

        $(this).toggle(visible);

    });

    $('.visit-date-group').each(function(){

        let visibles = $(this)
            .find('.visitante-card:visible')
            .length;

        $(this).toggle(visibles > 0);

    });

    // Si el buscador está vacío mostrar todo otra vez
    if(value === ''){

        $('.visitante-card').show();
        $('.visit-date-group').show();

    }

});

//REGISTRAR VISITA

$(document).on('click','.btn-register-visit',function(){

    let visitante_id = $(this).data('id');

    fetch('api/create_visit.php',{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            visitante_id:visitante_id
        })

    })
    .then(response => response.json())
    .then(data => {

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

            setTimeout(() => {

                location.reload();

            },1000);

        }

    });

});