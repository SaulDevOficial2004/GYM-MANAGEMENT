//ABRIR MODAL Y LLENAR DATOS
$('.updateBtn').click(function(){

        let id = $(this).data('id');
        let nombre = $(this).data('nombre');

        $('#cliente_id').val(id);
        $('#cliente_nombre').text(nombre);
});

//AUTOCOMPLETADO DE FECHAS
$('#persona_membresia_edit').change(function(){

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

    $('#fecha_ini').val(fechaInicio);
    $('#fecha_fin').val(fechaFin);

});

//FETCH ACTUALIZAR MEMBRESIA

$('#updateMembershipForm').submit(async function(e){
    
    e.preventDefault();

    let formData = {

        id: $('#cliente_id').val(),
        fecha_ini: $('#fecha_ini').val(),
        fecha_fin: $('#fecha_fin').val(),
        membresia_id: $('#persona_membresia').val()
    };

    try{

        let response = await fetch('/api/update_membership.php',{

            method: 'POST',
            headers: {
                'Content-Type':'application/json'
            },
            body: JSON.stringify(formData)
        });

        let data = await response.json()

        if(data.status === "success"){

            Toastify({
                text: data.message,
                duration:3000,
                gravity:"top",
                position:"right",

                style:{
                    background:
                    "linear-gradient(to right,#183B6B,#16BFFD)"
                }
            }).showToast();

            //CERRAR MODAL
            $('#updateMembershipModal').modal('hide');

            //RECARGAR TABLA
            setTimeout(()=> {
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
        console.log(error);
    }
});

// VISITANTES
$('#searchVisitante').on('keyup', function(){

    let search = $(this).val();

    if(search.length < 2){

        $('#visitantesResults').html('');
        return;

    }

    $.ajax({

        url:'api/search_visitantes.php',
        method:'GET',
        data:{
            search:search
        },

        success:function(response){

            let data = JSON.parse(response);
            let html = '';

            if(data.length > 0){

                data.forEach(visitante => {

                    html += `
                        <div class="visitante-item">

                            <span>
                                ${visitante.nombre}
                            </span>

                            <button
                                class="btn btn-primary-action registerVisitBtn"
                                data-id="${visitante.id}">

                                Registrar visita

                            </button>

                        </div>
                    `;

                });

            }else{

                html = `
                    <button
                        class="btn btn-primary-action"
                        id="newVisitanteBtn"
                        data-nombre="${search}">

                        Crear visitante

                    </button>
                `;
            }

            $('#visitantesResults').html(html);

        }

    });

});

// REGISTRAR VISITA

$(document).on('click','.registerVisitBtn',function(){

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

            $('#newVisitModal').modal('hide');

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
        }

    });

});

// CREAR VISITANTE

$(document).on('click','#newVisitanteBtn',function(){

    let nombre = $(this).data('nombre');

    $('#visitante_nombre').val(nombre);

    $('#createVisitanteModal').modal('show');

});

// GUARDAR VISITANTE

$('#createVisitanteForm').on('submit',function(e){

    e.preventDefault();

    let nombre = $('#visitante_nombre').val();

    fetch('api/create_visitante.php',{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            nombre:nombre
        })

    })
    .then(response => response.json())
    .then(data => {

        if(data.status === 'success'){

            $('#createVisitanteModal').modal('hide');
            $('#newVisitModal').modal('hide');

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

        }

    });

});

// BUSCAR PRODUCTOS

$('#searchProductDashboard').on('keyup', function(){

    let search = $(this).val();

    if(search.length < 2){

        $('#productsResults').html('');
        return;

    }

    $.ajax({

        url:'api/search_products.php',

        method:'GET',

        data:{
            search:search
        },

        success:function(response){

            let data = JSON.parse(response);

            let html = '';

            if(data.length > 0){

                data.forEach(producto => {

                    html += `
                        <div class="visitante-item">

                            <span>

                                ${producto.nombre}

                                ($${producto.precio})

                                Stock:
                                ${producto.stock}

                            </span>

                            <button
                                class="btn btn-primary-action sellProductDashboardBtn"
                                data-id="${producto.id}"
                                data-nombre="${producto.nombre}"
                                data-stock="${producto.stock}">

                                Vender

                            </button>

                        </div>
                    `;

                });

            }

            $('#productsResults').html(html);

        }

    });

});

// VENDER PRODUCTO DESDE DASHBOARD

$(document).on(
    'click',
    '.sellProductDashboardBtn',
    function(){

        let productoId = $(this).data('id');
        let nombre = $(this).data('nombre');
        let stock = $(this).data('stock');

        $('#dashboard_producto_id').val(
            productoId
        );

        $('#dashboard_producto_nombre').val(
            nombre
        );

        $('#dashboard_producto_stock').val(
            stock
        );

        $('#sellProductDashboardModal').modal('hide');

        $('#sellProductDashboardModal').one(
            'hidden.bs.modal',
            function(){

                $('#sellProductQuantityModal')
                    .modal('show');

            }
        );

    }
);

$('#sellProductDashboardForm').submit(
    async function(e){

        e.preventDefault();

        let formData = {

            producto_id:
                $('#dashboard_producto_id').val(),

            cantidad:
                $('#dashboard_producto_cantidad').val()

        };

        let response =
            await fetch(
                'api/sell_product.php',
                {

                    method:'POST',

                    headers:{
                        'Content-Type':
                        'application/json'
                    },

                    body:
                    JSON.stringify(formData)

                }
            );

        let data =
            await response.json();

        if(data.success){

            $('#sellProductQuantityModal')
                .modal('hide');

            $('#sellProductDashboardModal')
                .modal('hide');

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

    }
);

// RENTAR TOALLA

$('#rentTowelForm').submit(
    async function(e){

        e.preventDefault();

        try{

            let response =
                await fetch(
                    'api/rent_towel.php',
                    {
                        method:'POST'
                    }
                );

            let data =
                await response.json();

            if(data.success){

                $('#rentTowelModal')
                    .modal('hide');

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

            }

        }catch(error){

            console.log(error);

        }

    });

//=======================================
// MOSTRAR / OCULTAR COMPROBANTES
//=======================================

$('#showPendingPayments').click(function(){

    $('#pendingPaymentsContainer').slideToggle(300);

});

//=======================================
// VER COMPROBANTE
//=======================================

$(document).on('click', '.viewPaymentBtn', function(){

    const archivo = $(this).data('archivo');
    const extension = archivo.split('.').pop().toLowerCase();

    $('#adminPreviewImage').addClass('d-none');
    $('#adminPreviewPdf').addClass('d-none');

    if(extension === 'pdf'){

        $('#adminPreviewPdf')
            .attr('src', 'uploads/' + archivo)
            .removeClass('d-none');

    }else{

        $('#adminPreviewImage')
            .attr('src', 'uploads/' + archivo)
            .removeClass('d-none');

    }

    $('#reviewNombre').text($(this).data('nombre'));

    $('#reviewFolio').text($(this).data('folio'));

    $('#reviewMembership').val($(this).data('membresiaid'));

    $('#reviewConcepto').text(

        $(this).data('concepto') || 'Sin concepto'

    );

    $('#reviewFecha').text(

        $(this).data('fecha')

    );

    $('#reviewComprobanteId').val(

        $(this).data('id')

    );

    $('#paymentReviewModal').modal('show');

});

$(document).on('click','.viewHistoryPaymentBtn',function(){

    const archivo = $(this).data('archivo');

    const extension = archivo.split('.').pop().toLowerCase();

    $('#historyPreviewImage').addClass('d-none');
    $('#historyPreviewPdf').addClass('d-none');

    if(extension == 'pdf'){

        $('#historyPreviewPdf').attr('src', 'uploads/' + archivo).removeClass('d-none');

    }else{

        $('#historyPreviewImage').attr('src', 'uploads/' + archivo).removeClass('d-none');

    }

    $('#historyNombre').text($(this).data('nombre'));
    $('#historyFolio').text($(this).data('folio'));
    $('#historyConcepto').text($(this).data('concepto') || 'Sin concepto');
    $('#historyFecha').text($(this).data('fecha'));
    $('#historyEstado').text($(this).data('status'));
    $('#paymentHistoryViewModal').modal('show');

});


//=======================================
// CONFIRMAR COMPROBANTE
//=======================================

$('#confirmPaymentBtn').click(function(){

    Swal.fire({

        title:'¿Confirmar pago?',
        text:'Se renovará automáticamente la membresía.',
        icon:'question',
        showCancelButton:true,
        confirmButtonColor:'#183B6B',
        cancelButtonColor:'#d33',
        confirmButtonText:'Sí, confirmar',
        cancelButtonText:'Cancelar'

    }).then(async(result)=>{

        if(!result.isConfirmed){

            return;

        }

        $('#confirmPaymentBtn').prop('disabled', true);
        const formData = new FormData();

        formData.append('id', $('#reviewComprobanteId').val());
        formData.append('membresia_id', $('#reviewMembership').val());

        try{

            const response = await fetch('api/confirmar_comprobante.php', {

                    method:'POST',
                    body:formData

                });

            if(!response.ok){

                throw new Error('Respuesta inválida del servidor.');

            }

            const data = await response.json();

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

                $('#paymentReviewModal').modal('hide');

                setTimeout(()=>{
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
                text:'No fue posible confirmar el pago.',
                confirmButtonColor:'#183B6B'

            });

        }finally{

            $('#confirmPaymentBtn').prop('disabled', false);

        }
    });
});

//=======================================
// RECHAZAR COMPROBANTE
//=======================================

$('#rejectPaymentBtn').click(function(){

    const id = $('#reviewComprobanteId').val();

    $('#paymentReviewModal').modal('hide');

    setTimeout(() => {

        Swal.fire({

            title:'Rechazar comprobante',
            input:'textarea',
            inputLabel:'Motivo del rechazo',
            inputPlaceholder:'Escribe el motivo...',
            inputAttributes:{
                maxlength:300
            },

            showCancelButton:true,
            confirmButtonText:'Rechazar',
            cancelButtonText:'Cancelar',
            confirmButtonColor:'#d33',
            cancelButtonColor:'#183B6B',

            inputValidator:(value)=>{

                if(!value){

                    return 'Debes escribir un motivo.';

                }

            }

        }).then(async(result)=>{

            if(!result.isConfirmed){
                return;

            }

            const formData = new FormData();

            formData.append('id',$('#reviewComprobanteId').val());
            formData.append('motivo', result.value);

            try{

                const response = await fetch('api/rechazar_comprobante.php', {

                        method:'POST',
                        body:formData

                    });

                const data = await response.json();

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

                    $('#paymentReviewModal').modal('hide');

                    setTimeout(()=>{
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

            }

        });

    }, 300);

});


//=======================================
// CONFIGURAR TRANSFERENCIAS
//=======================================

$('#transferSettingsForm').submit(async function(e){

    e.preventDefault();

    const formData = new FormData();

    formData.append('id', $('#transfer_config_id').val());
    formData.append('banco', $('#transfer_banco').val());
    formData.append('titular', $('#transfer_titular').val());
    formData.append('clabe', $('#transfer_clabe').val());

    try{

        const response = await fetch('api/update_transferencias.php', {

                method:'POST',
                body:formData

            });

        const data = await response.json();

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

            $('#transferSettingsModal').modal('hide');

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

    }
});

//=======================================
// INHABILITAR CLIENTE
//=======================================

$(document).on('click', '.disableMemberBtn', function(e){

    e.preventDefault();

    const id = $(this).data('id');

    Swal.fire({

        title:'¿Inhabilitar cliente?',
        text:'El cliente dejará de aparecer como miembro activo.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonColor:'#183B6B',
        confirmButtonText:'Sí, inhabilitar',
        cancelButtonText:'Cancelar'

    }).then((result)=>{

        if(!result.isConfirmed){

            return;

        }

        fetch(`del_ven.php?id=${id}`)

        .then(response=>response.text())

        .then(()=>{

            Toastify({

                text:'Cliente inhabilitado correctamente.',
                duration:3000,
                gravity:'top',
                position:'right',

                style:{

                    background:
                    "linear-gradient(to right,#183B6B,#16BFFD)"

                }

            }).showToast();

            setTimeout(()=>{

                location.reload();

            },1000);

        })

        .catch(()=>{

            Swal.fire({

                icon:'error',
                title:'Error',
                text:'No fue posible inhabilitar al cliente.',
                confirmButtonColor:'#183B6B'

            });

        });

    });

});

//=======================================
// INHABILITAR CLIENTE
//=======================================

$(document).on('click', '.disableMemberBtn', function(e){

    e.preventDefault();

    const id = $(this).data('id');

    Swal.fire({

        title:'¿Inhabilitar cliente?',
        text:'El cliente dejará de aparecer como miembro activo.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        cancelButtonColor:'#183B6B',
        confirmButtonText:'Sí, inhabilitar',
        cancelButtonText:'Cancelar'

    }).then(async(result)=>{

        if(!result.isConfirmed){
            return;

        }

        const formData = new FormData();

        formData.append('id', id);

        try{

            const response = await fetch('api/disable_member.php',{

                method:'POST',
                body:formData

            });

            if(!response.ok){

                throw new Error();

            }

            const data = await response.json();

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

                setTimeout(()=>{

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
                text:'No fue posible inhabilitar al cliente.',
                confirmButtonColor:'#183B6B'

            });

        }

    });

});