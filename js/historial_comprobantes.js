let paymentHistoryCurrentPage=1;
let paymentHistoryTotalPages=1;
let paymentHistoryStatus='all';
let paymentHistorySearchTimer=null;

function escapePaymentHistoryHtml(value){
    return $('<div>').text(value??'').html();
}

function getPaymentStatusData(status){
    const normalizedStatus=String(status||'').toUpperCase();

    switch(normalizedStatus){
        case 'PENDIENTE':
            return{
                text:'Pendiente',
                className:'payment-status-pending',
                icon:'fa-clock'
            };
        case 'CONFIRMADO':
            return{
                text:'Confirmado',
                className:'payment-status-confirmed',
                icon:'fa-check'
            };
        case 'RECHAZADO':
            return{
                text:'Rechazado',
                className:'payment-status-rejected',
                icon:'fa-times'
            };
        default:
            return{
                text:'Sin estado',
                className:'payment-status-unknown',
                icon:'fa-question'
            };
    }
}

async function loadPaymentHistory(page=1){
    paymentHistoryCurrentPage=page;

    const parameters=new URLSearchParams({
        search:$('#paymentHistorySearch').val().trim(),
        status:paymentHistoryStatus,
        page:paymentHistoryCurrentPage
    });

    $('#paymentHistoryTable').removeClass('d-none');
    $('#paymentHistoryEmpty').addClass('d-none');

    $('#paymentHistoryTableBody').html(`
        <tr>
            <td colspan="7" class="payment-history-loading">
                <i class="fas fa-spinner fa-spin"></i>
                Cargando comprobantes...
            </td>
        </tr>
    `);

    try{
        const response=await fetch('api/get_payment_history.php?'+parameters.toString());
        const responseText=await response.text();
        let result;

        try{
            result=JSON.parse(responseText);
        }catch(error){
            console.error(responseText);
            throw new Error('La API devolvió una respuesta inválida.');
        }

        if(!result.success){
            throw new Error(result.message||'No fue posible cargar los comprobantes.');
        }

        renderPaymentHistory(result.data);
        updatePaymentHistoryPagination(result.pagination);
        updatePaymentHistoryStats(result.stats||{});
    }catch(error){
        console.error(error);

        $('#paymentHistoryTableBody').html(`
            <tr>
                <td colspan="7" class="payment-history-loading payment-history-error">
                    <i class="fas fa-circle-exclamation"></i>
                    No fue posible cargar los comprobantes.
                </td>
            </tr>
        `);
    }
}

function renderPaymentHistory(records){
    if(records.length===0){
        $('#paymentHistoryTableBody').html('');
        $('#paymentHistoryTable').addClass('d-none');
        $('#paymentHistoryEmpty').removeClass('d-none');
        return;
    }

    let rows='';

    records.forEach(record=>{
        const statusData=getPaymentStatusData(record.status);
        const reviewName=record.revisado_por||'Pendiente';
        const concept=record.concepto||'Sin concepto';

        rows+=`
            <tr>
                <td>
                    <div class="payment-client-cell">
                        <span>
                            <i class="fas fa-user"></i>
                        </span>
                        <div>
                            <strong>${escapePaymentHistoryHtml(record.nombre)}</strong>
                            <small>Cliente #${Number(record.persona_id)}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="folio-hidden">
                        <i class="fas fa-eye-slash"></i>
                        Folio oculto
                    </span>
                </td>
                <td>
                    <div class="payment-concept-cell">
                        <strong>${escapePaymentHistoryHtml(concept)}</strong>
                        <small>Comprobante #${Number(record.id)}</small>
                    </div>
                </td>
                <td>
                    <span class="payment-status ${statusData.className}">
                        <i class="fas ${statusData.icon}"></i>
                        ${escapePaymentHistoryHtml(statusData.text)}
                    </span>
                </td>
                <td>
                    <div class="payment-date-cell">
                        <strong>${escapePaymentHistoryHtml(record.fecha_subida)}</strong>
                        <small>${escapePaymentHistoryHtml(record.hora_subida)}</small>
                    </div>
                </td>
                <td>
                    <div class="payment-review-cell">
                        <i class="fas fa-user-check"></i>
                        <span>${escapePaymentHistoryHtml(reviewName)}</span>
                    </div>
                </td>
                <td>
                    <button type="button" class="payment-view-button viewHistoryPaymentBtn"
                        data-id="${Number(record.id)}"
                        data-persona="${Number(record.persona_id)}"
                        data-nombre="${escapePaymentHistoryHtml(record.nombre)}"
                        data-folio="${escapePaymentHistoryHtml(record.folio)}"
                        data-membresiaid="${Number(record.membresia_id)}"
                        data-concepto="${escapePaymentHistoryHtml(concept)}"
                        data-status="${escapePaymentHistoryHtml(record.status)}"
                        data-fecha="${escapePaymentHistoryHtml(record.fecha_completa)}"
                        data-fecharevision="${escapePaymentHistoryHtml(record.fecha_revision)}"
                        data-revisado="${escapePaymentHistoryHtml(reviewName)}"
                        data-archivo="${escapePaymentHistoryHtml(record.archivo)}"
                        data-motivo="${escapePaymentHistoryHtml(record.motivo)}">
                        <i class="fas fa-eye"></i>
                        Ver
                    </button>
                </td>
            </tr>
        `;
    });

    $('#paymentHistoryTableBody').html(rows);
    $('#paymentHistoryTable').removeClass('d-none');
    $('#paymentHistoryEmpty').addClass('d-none');
}

function updatePaymentHistoryPagination(pagination){
    paymentHistoryCurrentPage=Number(pagination.current_page);
    paymentHistoryTotalPages=Number(pagination.total_pages);

    $('#paymentHistoryRecords').text(pagination.total_records);
    $('#paymentHistoryPageInformation').text(`Página ${paymentHistoryCurrentPage} de ${paymentHistoryTotalPages}`);
    $('#paymentHistoryPrevious').prop('disabled',paymentHistoryCurrentPage<=1);
    $('#paymentHistoryNext').prop('disabled',paymentHistoryCurrentPage>=paymentHistoryTotalPages);
}

function updatePaymentHistoryStats(stats){
    $('#paymentHistoryTotal').text(Number(stats.total||0));
    $('#paymentHistoryPending').text(Number(stats.pending||0));
    $('#paymentHistoryConfirmed').text(Number(stats.confirmed||0));
    $('#paymentHistoryRejected').text(Number(stats.rejected||0));
}

$('#paymentHistorySearch').on('input',function(){
    clearTimeout(paymentHistorySearchTimer);

    paymentHistorySearchTimer=setTimeout(()=>{
        loadPaymentHistory(1);
    },350);
});

$('#clearPaymentHistorySearch').on('click',function(){
    clearTimeout(paymentHistorySearchTimer);
    $('#paymentHistorySearch').val('').trigger('focus');
    loadPaymentHistory(1);
});

$('.payment-history-filter').on('click',function(){
    paymentHistoryStatus=String($(this).data('status'));

    $('.payment-history-filter').removeClass('active');
    $(this).addClass('active');

    loadPaymentHistory(1);
});

$('#paymentHistoryPrevious').on('click',function(){
    if(paymentHistoryCurrentPage>1){
        loadPaymentHistory(paymentHistoryCurrentPage-1);
    }
});

$('#paymentHistoryNext').on('click',function(){
    if(paymentHistoryCurrentPage<paymentHistoryTotalPages){
        loadPaymentHistory(paymentHistoryCurrentPage+1);
    }
});

$(document).on('click','.viewHistoryPaymentBtn',function(){
    const archivo=String($(this).data('archivo')||'').trim();

    if(!archivo){
        Swal.fire({
            icon:'warning',
            title:'Archivo no disponible',
            text:'Este comprobante no tiene un archivo disponible.',
            confirmButtonColor:'#176dd3'
        });

        return;
    }

    const extension=archivo.split('.').pop().toLowerCase();
    const comprobanteId=$(this).data('id');
    const rutaArchivo='api/ver_archivo.php?id='+encodeURIComponent(comprobanteId);
    const $imagen=$('#historyPreviewImage');
    const $pdf=$('#historyPreviewPdf');

    $imagen.attr('src','').addClass('d-none');
    $pdf.attr('src','').addClass('d-none');

    if(extension==='pdf'){
        $pdf.attr('src',rutaArchivo).removeClass('d-none');
    }else{
        $imagen.attr('src',rutaArchivo).removeClass('d-none');
    }

    const estado=String($(this).data('status')||'');
    const motivo=String($(this).data('motivo')||'');

    $('#historyNombre').text($(this).data('nombre')||'Sin nombre');
    $('#historyFolio').text($(this).data('folio')||'Sin folio');
    $('#historyConcepto').text($(this).data('concepto')||'Sin concepto');
    $('#historyFecha').text($(this).data('fecha')||'Sin fecha');
    $('#historyEstado').text(estado||'Sin estado');
    $('#historyRevisado').text($(this).data('revisado')||'Pendiente');
    $('#historyFechaRevision').text($(this).data('fecharevision')||'Pendiente');

    if(estado==='RECHAZADO'&&motivo!==''){
        $('#historyMotivo').text(motivo);
        $('#historyMotivoContainer').removeClass('d-none');
    }else{
        $('#historyMotivo').text('');
        $('#historyMotivoContainer').addClass('d-none');
    }

    $('#paymentHistoryViewModal').modal('show');
});

$('#paymentHistoryViewModal').on('hidden.bs.modal',function(){
    $('#historyPreviewImage').attr('src','').addClass('d-none');
    $('#historyPreviewPdf').attr('src','').addClass('d-none');
    $('#historyNombre').text('');
    $('#historyFolio').text('');
    $('#historyConcepto').text('');
    $('#historyFecha').text('');
    $('#historyEstado').text('');
    $('#historyRevisado').text('');
    $('#historyFechaRevision').text('');
    $('#historyMotivo').text('');
    $('#historyMotivoContainer').addClass('d-none');
});

$(document).ready(function(){
    loadPaymentHistory(1);
});