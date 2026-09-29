let currentProductFilter='all';

function filterProducts(){
    const searchValue=$('#searchProduct').val().toLowerCase().trim();
    let visibleProducts=0;

    $('.product-card').each(function(){
        const card=$(this);
        const searchableText=String(card.data('search')).toLowerCase();
        const status=String(card.data('status'));
        const stockStatus=String(card.data('stock-status'));

        const matchesSearch=searchableText.includes(searchValue);
        let matchesFilter=true;

        if(currentProductFilter==='active'){
            matchesFilter=status==='active';
        }

        if(currentProductFilter==='inactive'){
            matchesFilter=status==='inactive';
        }

        if(currentProductFilter==='low'){
            matchesFilter=stockStatus==='low';
        }

        if(currentProductFilter==='out'){
            matchesFilter=stockStatus==='out';
        }

        const shouldShow=matchesSearch&&matchesFilter;

        card.toggle(shouldShow);

        if(shouldShow){
            visibleProducts++;
        }
    });

    $('#visibleProductsCount').text(visibleProducts);

    $('#productsEmptyState').toggleClass(
        'd-none',
        visibleProducts>0
    );
}

$('#searchProduct').on('input',function(){
    filterProducts();
});

$('#clearProductSearch').on('click',function(){
    $('#searchProduct').val('').trigger('focus');
    filterProducts();
});

$('.product-filter-button').on('click',function(){
    currentProductFilter=String(
        $(this).data('filter')
    );

    $('.product-filter-button').removeClass('active');
    $(this).addClass('active');

    filterProducts();
});

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

$('#createProductForm').on('submit',async function(event){
    event.preventDefault();

    const nombre=$('#nombre').val().trim();
    const descripcion=$('#descripcion').val().trim();
    const precio=Number($('#precio').val());
    const stock=Number($('#stock').val());
    const button=$('#createProductBtn');
    const originalContent=button.html();

    if(!validateProductData(nombre,precio,stock)){
        return;
    }

    const formData=new FormData();

    formData.append('nombre',nombre);
    formData.append('descripcion',descripcion);
    formData.append('precio',precio);
    formData.append('stock',stock);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Guardando...'
    );

    try{
        const result=await apiFetch('api/create_product.php',{
            method:'POST',
            body:formData
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message||'No fue posible registrar el producto.'
            );
        }

        showProductToast(data.message);
        $('#addProductModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showProductError(
            error.message||'No fue posible registrar el producto.'
        );
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$('#editProductForm').on('submit',async function(event){
    event.preventDefault();

    const id=$('#edit_id').val();
    const nombre=$('#edit_nombre').val().trim();
    const descripcion=$('#edit_descripcion').val().trim();
    const precio=Number($('#edit_precio').val());
    const stock=Number($('#edit_stock').val());
    const button=$('#updateProductBtn');
    const originalContent=button.html();

    if(!validateProductData(nombre,precio,stock)){
        return;
    }

    const formData=new FormData();

    formData.append('id',id);
    formData.append('nombre',nombre);
    formData.append('descripcion',descripcion);
    formData.append('precio',precio);
    formData.append('stock',stock);

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Guardando...'
    );

    try{
        const result=await apiFetch('api/update_product.php',{
            method:'POST',
            body:formData
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message||'No fue posible actualizar el producto.'
            );
        }

        showProductToast(data.message);
        $('#editProductModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showProductError(
            error.message||'No fue posible actualizar el producto.'
        );
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$(document).on('click','.toggleProductBtn',async function(){
    const id=$(this).data('id');
    const estadoActual=Number($(this).data('estado'));
    const nombre=$(this).data('nombre');
    const nuevoEstado=estadoActual===1?0:1;
    const accion=nuevoEstado===1?'activar':'desactivar';

    const result=await Swal.fire({
        icon:nuevoEstado===1?'question':'warning',
        title:`¿Deseas ${accion} este producto?`,
        text:nombre,
        showCancelButton:true,
        confirmButtonText:nuevoEstado===1?'Activar':'Desactivar',
        cancelButtonText:'Cancelar',
        confirmButtonColor:nuevoEstado===1?'#23984f':'#d99400',
        cancelButtonColor:'#6c757d'
    });

    if(!result.isConfirmed){
        return;
    }

    try{
        const result=await apiFetch('api/toggle_product.php',{
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:new URLSearchParams({
                id:id,
                estado:nuevoEstado
            }).toString()
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message||'No fue posible actualizar el estado.'
            );
        }

        showProductToast(data.message);

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showProductError(
            error.message||'No fue posible actualizar el estado.'
        );
    }
});

$(document).on('click','.deleteProductBtn',async function(){
    const id=$(this).data('id');
    const nombre=$(this).data('nombre');

    const result=await Swal.fire({
        icon:'warning',
        title:'¿Eliminar producto?',
        text:`El producto "${nombre}" será eliminado definitivamente.`,
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
        const result=await apiFetch('api/delete_product.php',{
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:new URLSearchParams({
                id:id
            }).toString()
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message||'No fue posible eliminar el producto.'
            );
        }

        Toastify({
            text:data.message,
            duration:3000,
            gravity:'top',
            position:'right',
            style:{
                background:'linear-gradient(to right,#d93a62,#ff6b81)'
            }
        }).showToast();

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showProductError(
            error.message||'No fue posible eliminar el producto.'
        );
    }
});

$(document).on('click','.sellProductBtn',function(){
    const id=$(this).data('id');
    const nombre=$(this).data('nombre');
    const precio=Number($(this).data('precio'));
    const stock=Number($(this).data('stock'));

    $('#sell_id').val(id);
    $('#sell_nombre').val(nombre);
    $('#sell_precio').val('$'+precio.toFixed(2));
    $('#sell_stock').val(stock);
    $('#sell_cantidad').val(1).attr('max',stock);
    $('#sellProductNameDisplay').text(nombre);

    updateSellProductTotal();

    $('#sellProductModal').modal('show');
});

$('#sell_cantidad').on('input',function(){
    updateSellProductTotal();
});

function updateSellProductTotal(){
    const priceText=$('#sell_precio').val().replace('$','');
    const precio=Number(priceText)||0;
    const cantidad=Number($('#sell_cantidad').val())||0;
    const total=precio*cantidad;

    $('#sellProductTotal').text(
        '$'+total.toFixed(2)
    );
}

$('#sellProductForm').on('submit',async function(event){
    event.preventDefault();

    const productoId=$('#sell_id').val();
    const cantidad=Number($('#sell_cantidad').val());
    const stock=Number($('#sell_stock').val());
    const button=$('#sellProductSubmitBtn');
    const originalContent=button.html();

    if(!Number.isInteger(cantidad)||cantidad<1){
        showProductWarning(
            'Cantidad no válida',
            'Ingresa una cantidad válida para la venta.'
        );

        return;
    }

    if(cantidad>stock){
        showProductWarning(
            'Stock insuficiente',
            `Solo existen ${stock} unidades disponibles.`
        );

        return;
    }

    button.prop('disabled',true).html(
        '<i class="fas fa-spinner fa-spin"></i> Vendiendo...'
    );

    try{
        const result=await apiFetch('api/sell_product.php',{
            method:'POST',
            body:JSON.stringify({
                producto_id:productoId,
                cantidad:cantidad
            })
        });

        if(!result) return;

        const data=result.data;

        if(!data.success){
            throw new Error(
                data.message||'No fue posible registrar la venta.'
            );
        }

        showProductToast(data.message);
        $('#sellProductModal').modal('hide');

        setTimeout(()=>{
            location.reload();
        },900);
    }catch(error){
        console.error(error);

        showProductError(
            error.message||'No fue posible registrar la venta.'
        );
    }finally{
        button.prop('disabled',false).html(originalContent);
    }
});

$('#addProductModal').on('hidden.bs.modal',function(){
    $('#createProductForm')[0].reset();
});

$('#editProductModal').on('hidden.bs.modal',function(){
    $('#editProductForm')[0].reset();
});

$('#sellProductModal').on('hidden.bs.modal',function(){
    $('#sellProductForm')[0].reset();
    $('#sellProductNameDisplay').text('Producto');
    $('#sellProductTotal').text('$0.00');
});

function validateProductData(nombre,precio,stock){
    if(nombre.length<2){
        showProductWarning(
            'Nombre no válido',
            'El nombre debe contener al menos 2 caracteres.'
        );

        return false;
    }

    if(!Number.isFinite(precio)||precio<0){
        showProductWarning(
            'Precio no válido',
            'Ingresa un precio válido.'
        );

        return false;
    }

    if(!Number.isInteger(stock)||stock<0){
        showProductWarning(
            'Stock no válido',
            'El stock debe ser un número entero igual o mayor a cero.'
        );

        return false;
    }

    return true;
}

function showProductWarning(title,message){
    Swal.fire({
        icon:'warning',
        title:title,
        text:message,
        confirmButtonColor:'#176dd3'
    });
}

function showProductError(message){
    Swal.fire({
        icon:'error',
        title:'Error',
        text:message,
        confirmButtonColor:'#176dd3'
    });
}

function showProductToast(message){
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

filterProducts();