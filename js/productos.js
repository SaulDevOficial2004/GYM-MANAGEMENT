// =====================================
// BUSCAR PRODUCTO
// =====================================

$('#searchProduct').on('keyup', function(){

    let value = $(this).val().toLowerCase();

    $('#productTable tbody tr').filter(function(){

        $(this).toggle(
            $(this)
            .text()
            .toLowerCase()
            .indexOf(value) > -1
        );

    });

});

// =====================================
// ABRIR MODAL EDITAR
// =====================================

$(document).on('click','.editProductBtn',function(){

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

    $('#edit_stock').val(
        $(this).data('stock')
    );

    $('#editProductModal').modal('show');

});

// =====================================
// CREAR PRODUCTO
// =====================================

$('#createProductForm').submit(async function(e){

    e.preventDefault();

    let formData = new FormData();

    formData.append(
        'nombre',
        $('#nombre').val()
    );

    formData.append(
        'descripcion',
        $('#descripcion').val()
    );

    formData.append(
        'precio',
        $('#precio').val()
    );

    formData.append(
        'stock',
        $('#stock').val()
    );

    try{

        let response = await fetch(
            'api/create_product.php',
            {
                method:'POST',
                body:formData
            }
        );

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

            $('#addProductModal').modal('hide');

            setTimeout(()=>{

                location.reload();

            },1000);

        }

    }catch(error){

        console.error(error);

    }

});

// =====================================
// EDITAR PRODUCTO
// =====================================

$('#editProductForm').submit(async function(e){

    e.preventDefault();

    let formData = new FormData();

    formData.append(
        'id',
        $('#edit_id').val()
    );

    formData.append(
        'nombre',
        $('#edit_nombre').val()
    );

    formData.append(
        'descripcion',
        $('#edit_descripcion').val()
    );

    formData.append(
        'precio',
        $('#edit_precio').val()
    );

    formData.append(
        'stock',
        $('#edit_stock').val()
    );

    try{

        let response = await fetch(
            'api/update_product.php',
            {
                method:'POST',
                body:formData
            }
        );

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

            $('#editProductModal').modal('hide');

            setTimeout(()=>{

                location.reload();

            },1000);

        }

    }catch(error){

        console.error(error);

    }

});

// =====================================
// ACTIVAR / DESACTIVAR
// =====================================

$(document).on('click','.toggleProductBtn',async function(){

    let id = $(this).data('id');

    let estadoActual =
        parseInt(
            $(this).data('estado')
        );

    let nuevoEstado =
        estadoActual === 1 ? 0 : 1;

    let response = await fetch(

        'api/toggle_product.php',

        {

            method:'POST',

            headers:{
                'Content-Type':
                'application/x-www-form-urlencoded'
            },

            body:
            `id=${id}&estado=${nuevoEstado}`

        }

    );

    let data = await response.json();

    if(data.success){

        location.reload();

    }

});

// =====================================
// ELIMINAR
// =====================================

$(document).on('click','.deleteProductBtn',async function(){

    let id =
        $(this).data('id');

    let nombre =
        $(this).data('nombre');

    let result =
        await Swal.fire({

            icon:'warning',

            title:'¿Eliminar producto?',

            text:nombre,

            showCancelButton:true,

            confirmButtonText:'Eliminar',

            cancelButtonText:'Cancelar',

            confirmButtonColor:'#dc3545'

        });

    if(!result.isConfirmed){

        return;

    }

    let response = await fetch(

        'api/delete_product.php',

        {

            method:'POST',

            headers:{
                'Content-Type':
                'application/x-www-form-urlencoded'
            },

            body:`id=${id}`

        }

    );

    let data =
        await response.json();

    if(data.success){

        location.reload();

    }

});

// =====================================
// ABRIR MODAL VENDER
// =====================================

$(document).on('click','.sellProductBtn',function(){

    $('#sell_id').val(
        $(this).data('id')
    );

    $('#sell_nombre').val(
        $(this).data('nombre')
    );

    $('#sell_precio').val(
        '$' + $(this).data('precio')
    );

    $('#sell_stock').val(
        $(this).data('stock')
    );

    $('#sellProductModal').modal('show');

});

// =====================================
// VENDER PRODUCTO
// =====================================

$('#sellProductForm').submit(async function(e){

    e.preventDefault();

    let formData = {

        producto_id:
            $('#sell_id').val(),

        cantidad:
            $('#sell_cantidad').val()

    };

    try{

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

            $('#sellProductModal')
                .modal('hide');

            setTimeout(()=>{

                location.reload();

            },1000);

        }else{

            Swal.fire({

                icon:'error',

                title:'Error',

                text:data.message,

                confirmButtonColor:
                '#183B6B'

            });

        }

    }catch(error){

        console.log(error);

    }

});