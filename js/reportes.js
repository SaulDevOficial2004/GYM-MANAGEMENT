//ACCIONES GENERALES DEL REPORTE

$('#clearAuditSearch').on('click',function(){
    $('#searchAudit').val('').trigger('focus');
    filterAuditSearch();
});

$('#refreshReportsData').on('click',function(){
    const button=$(this);

    button.prop('disabled',true).addClass('loading');
    button.find('span').text('Actualizando...');

    setTimeout(()=>{
        location.reload();
    },450);
});

$('#printFinancialReport').on('click',function(){
    window.print();
});

//HISTORIAL GENERAL

let reportsCurrentPage=1;
let reportsTotalPages=1;
let currentReportFilter='all';
let reportSearchTimer=null;

function escapeReportHtml(value){
    return $('<div>').text(value??'').html();
}

function formatReportCurrency(value){
    return new Intl.NumberFormat('es-MX',{
        style:'currency',
        currency:'MXN'
    }).format(Number(value)||0);
}

function getReportTypeData(type){
    const normalizedType=String(type||'OTRO').toUpperCase();

    switch(normalizedType){
        case 'MEMBRESIA':
            return{
                text:'Membresía',
                className:'report-type-membership',
                icon:'fa-id-card'
            };
        case 'PRODUCTO':
            return{
                text:'Producto',
                className:'report-type-product',
                icon:'fa-box'
            };
        case 'VISITA':
            return{
                text:'Visita',
                className:'report-type-visit',
                icon:'fa-user-clock'
            };
        case 'TOALLA':
            return{
                text:'Toalla',
                className:'report-type-towel',
                icon:'fa-shirt'
            };
        default:
            return{
                text:normalizedType.charAt(0)+normalizedType.slice(1).toLowerCase(),
                className:'report-type-default',
                icon:'fa-receipt'
            };
    }
}

async function loadSalesHistory(page=1){
    reportsCurrentPage=page;

    const parameters=new URLSearchParams({
        search:$('#searchReport').val().trim(),
        period:currentReportFilter,
        page:reportsCurrentPage
    });

    $('#reportsTable').removeClass('d-none');
    $('#reportsEmptyState').addClass('d-none');

    $('#reportsTableBody').html(`
        <tr>
            <td colspan="4" class="reports-loading">
                <i class="fas fa-spinner fa-spin"></i>
                Cargando historial...
            </td>
        </tr>
    `);

    try{
        const response=await fetch(
            'api/get_sales_history.php?'+parameters.toString()
        );

        const responseText=await response.text();
        let result;

        try{
            result=JSON.parse(responseText);
        }catch(error){
            console.error(responseText);
            throw new Error('La API devolvió una respuesta inválida.');
        }

        if(!result.success){
            throw new Error(
                result.message||'No fue posible cargar el historial.'
            );
        }

        renderSalesHistory(result.data);
        updateReportsPagination(result.pagination);
    }catch(error){
        console.error(error);

        $('#reportsTableBody').html(`
            <tr>
                <td colspan="4" class="reports-loading reports-error">
                    <i class="fas fa-circle-exclamation"></i>
                    No fue posible cargar el historial.
                </td>
            </tr>
        `);
    }
}

function renderSalesHistory(records){
    if(records.length===0){
        $('#reportsTableBody').html('');
        $('#reportsTable').addClass('d-none');
        $('#reportsEmptyState').removeClass('d-none');
        return;
    }

    let rows='';

    records.forEach(record=>{
        const typeData=getReportTypeData(record.tipo);
        const description=record.descripcion.trim()!== ''
            ? record.descripcion
            :'Sin descripción';

        rows+=`
            <tr>
                <td>
                    <div class="report-date-cell">
                        <span class="report-date-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div>
                            <strong>${escapeReportHtml(record.fecha)}</strong>
                            <small>${escapeReportHtml(record.hora)}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="report-type-badge ${typeData.className}">
                        <i class="fas ${typeData.icon}"></i>
                        ${escapeReportHtml(typeData.text)}
                    </span>
                </td>
                <td>
                    <div class="report-description-cell">
                        <strong>${escapeReportHtml(description)}</strong>
                        <small>Operación #${Number(record.id)}</small>
                    </div>
                </td>
                <td class="reports-total-column">
                    <span class="report-sale-total">
                        ${formatReportCurrency(record.total)}
                    </span>
                </td>
            </tr>
        `;
    });

    $('#reportsTableBody').html(rows);
    $('#reportsTable').removeClass('d-none');
    $('#reportsEmptyState').addClass('d-none');
}

function updateReportsPagination(pagination){
    reportsCurrentPage=Number(pagination.current_page);
    reportsTotalPages=Number(pagination.total_pages);

    $('#reportsTotalRecords').text(
        pagination.total_records
    );

    $('#reportsPageInformation').text(
        `Página ${reportsCurrentPage} de ${reportsTotalPages}`
    );

    $('#reportsPreviousPage').prop(
        'disabled',
        reportsCurrentPage<=1
    );

    $('#reportsNextPage').prop(
        'disabled',
        reportsCurrentPage>=reportsTotalPages
    );
}

$('#searchReport').on('input',function(){
    clearTimeout(reportSearchTimer);

    reportSearchTimer=setTimeout(()=>{
        loadSalesHistory(1);
    },350);
});

$('#clearReportSearch').on('click',function(){
    clearTimeout(reportSearchTimer);

    $('#searchReport').val('').trigger('focus');
    loadSalesHistory(1);
});

$('.btn-report-filter').on('click',function(){
    currentReportFilter=String(
        $(this).data('filter')
    );

    $('.btn-report-filter').removeClass('active');
    $(this).addClass('active');

    loadSalesHistory(1);
});

$('#reportsPreviousPage').on('click',function(){
    if(reportsCurrentPage>1){
        loadSalesHistory(
            reportsCurrentPage-1
        );
    }
});

$('#reportsNextPage').on('click',function(){
    if(reportsCurrentPage<reportsTotalPages){
        loadSalesHistory(
            reportsCurrentPage+1
        );
    }
});

$(document).ready(function(){
    loadSalesHistory(1);
});


// =====================================
// BITÁCORA DE ACTIVIDAD
// =====================================

let auditCurrentPage = 1;
let auditTotalPages = 1;


function escapeAuditHtml(value){

    return $('<div>').text(value ?? '').html();

}


function getAuditActionClass(action){

    switch(action){

        case 'CREAR':

            return 'audit-action-create';

        case 'EDITAR':

            return 'audit-action-edit';

        case 'ACTIVAR':

            return 'audit-action-enable';

        case 'DESACTIVAR':

            return 'audit-action-disable';

        case 'RESTABLECER_CONTRASEÑA':

            return 'audit-action-password';

        default:

            return 'audit-action-default';

    }

}


async function loadAuditLog(page = 1){

    auditCurrentPage = page;

    let parameters = new URLSearchParams({

            usuario_id: $('#auditUserFilter').val(),
            modulo: $('#auditModuleFilter').val(),
            accion: $('#auditActionFilter').val(),
            fecha_desde: $('#auditDateFrom').val(),
            fecha_hasta: $('#auditDateTo').val(),
            pagina: auditCurrentPage

        });

    $('#auditTableBody').html(`

        <tr>

            <td
                colspan="6"
                class="audit-loading">

                <i class="fas fa-spinner fa-spin"></i>

                Cargando bitácora...

            </td>

        </tr>

    `);

    try{

        let response = await fetch('api/get_audit_log.php?' + parameters.toString());
        let responseText = await response.text();
        let result;

        try{

            result = JSON.parse(responseText);

        }catch(error){

            console.error(responseText);

            throw new Error('La API devolvió una respuesta inválida.');

        }

        if(!result.success){

            throw new Error(result.message || 'No fue posible cargar la bitácora.');

        }

        renderAuditRows(result.data);
        updateAuditPagination(result.pagination);
        filterAuditSearch();

    }catch(error){

        console.error(error);

        $('#auditTableBody').html(`

            <tr>

                <td
                    colspan="6"
                    class="audit-empty">

                    <i class="fas fa-circle-exclamation"></i>

                    No fue posible cargar la bitácora.

                </td>

            </tr>

        `);

    }

}


function renderAuditRows(records){

    if(records.length === 0){

        $('#auditTableBody').html(`

            <tr>

                <td
                    colspan="6"
                    class="audit-empty">

                    <i class="fas fa-inbox"></i>

                    No se encontraron movimientos con estos filtros.

                </td>

            </tr>

        `);

        return;

    }

    let rows = '';

    records.forEach(record => {

        let actionClass = getAuditActionClass(record.accion);

        let actionText = record.accion.replaceAll('_', ' ');

        let registerText = record.registro_id !== null ? `#${record.registro_id}` : '—';

        rows += `

            <tr>

                <td class="audit-date">

                    ${escapeAuditHtml(
                        record.fecha
                    )}

                </td>

                <td>

                    <div class="audit-user">

                        <span class="audit-user-icon">

                            <i class="fas fa-user"></i>

                        </span>

                        ${escapeAuditHtml(
                            record.usuario
                        )}

                    </div>

                </td>

                <td>

                    <span
                        class="audit-action-badge ${actionClass}">

                        ${escapeAuditHtml(
                            actionText
                        )}

                    </span>

                </td>

                <td>

                    <span class="audit-module-badge">

                        ${escapeAuditHtml(
                            record.modulo
                        )}

                    </span>

                </td>

                <td class="audit-description">

                    ${escapeAuditHtml(
                        record.descripcion
                    )}

                    ${
                        record.motivo
                        ? `

                            <small class="audit-reason">

                                Motivo:
                                ${escapeAuditHtml(
                                    record.motivo
                                )}

                            </small>

                        `
                        : ''
                    }

                </td>

                <td>

                    <span class="audit-register-id">

                        ${registerText}

                    </span>

                </td>

            </tr>

        `;

    });

    $('#auditTableBody').html(rows);

}


function updateAuditPagination(pagination){

    auditCurrentPage = Number(pagination.pagina_actual);
    auditTotalPages = Number(pagination.total_paginas);

    $('#auditTotalRecords').text(pagination.total_registros);
    $('#auditPageInformation').text(`Página ${auditCurrentPage} de ${auditTotalPages}`);
    $('#auditPreviousPage').prop('disabled', auditCurrentPage <= 1);
    $('#auditNextPage').prop('disabled', auditCurrentPage >= auditTotalPages);

}


function filterAuditSearch(){

    let value = $('#searchAudit').val().toLowerCase().trim();

    $('#auditTableBody tr').each(function(){

            let row = $(this);

            if(row.find('.audit-loading').length || row.find('.audit-empty').length){

                return;

            }

            row.toggle(row.text().toLowerCase().indexOf(value) > -1

            );

        });

}


//FILTROS DE BITÁCORA

$(
    '#auditUserFilter, '
    + '#auditModuleFilter, '
    + '#auditActionFilter, '
    + '#auditDateFrom, '
    + '#auditDateTo'
).on('change', function(){

    loadAuditLog(1);

});


$('#searchAudit').on('keyup', function(){

        filterAuditSearch();

    });


//LIMPIAR FILTROS

$('#clearAuditFilters').on('click', function(){

        $('#auditUserFilter').val('0');
        $('#auditModuleFilter').val('');
        $('#auditActionFilter').val('');
        $('#auditDateFrom').val('');
        $('#auditDateTo').val('');
        $('#searchAudit').val('');

        loadAuditLog(1);

    });


//PAGINACIÓN

$('#auditPreviousPage').on('click', function(){

        if(auditCurrentPage > 1){

            loadAuditLog(auditCurrentPage - 1);

        }

    });


$('#auditNextPage').on('click', function(){

        if(auditCurrentPage < auditTotalPages){

            loadAuditLog(auditCurrentPage + 1);

        }

    });


//CARGA INICIAL

$(document).ready(function(){

    loadAuditLog(1);

});

// =====================================
// VENTAS DE LOS ÚLTIMOS 7 DÍAS
// =====================================

let lastSevenDaysChart = null;


function formatCurrency(value){

    return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(value);

}


function formatChartDate(dateValue){

    let date = new Date(dateValue + 'T00:00:00');

    return new Intl.DateTimeFormat('es-MX', {
            weekday: 'short',
            day: '2-digit',
            month: 'short'
        }).format(date);

}


async function loadLastSevenDaysChart(){

    let loading = $('#lastSevenDaysLoading');

    loading.show();

    try{

        let response = await fetch('api/get_sales_last_7_days.php');

        let responseText = await response.text();

        let result;

        try{

            result = JSON.parse(responseText);

        }catch(error){

            console.error(responseText);

            throw new Error('La API devolvió una respuesta inválida.');

        }

        if(!result.success){

            throw new Error(result.message || 'No fue posible cargar las ventas.');

        }

        let labels = result.data.map(item => formatChartDate(item.fecha));
        let totals = result.data.map(item => Number(item.total));
        let periodTotal = totals.reduce((total, value) => total + value, 0);

        $('#lastSevenDaysTotal').text(formatCurrency(periodTotal));

        renderLastSevenDaysChart(labels, totals);

    }catch(error){

        console.error(error);

        loading.html(`

            <div class="chart-error">

                <i class="fas fa-circle-exclamation"></i>

                No fue posible cargar la gráfica.

            </div>

        `);

        return;

    }

    loading.hide();

}


function renderLastSevenDaysChart(labels, totals){

    let canvas = document.getElementById('lastSevenDaysChart');

    if(!canvas){

        return;

    }

    if(lastSevenDaysChart){

        lastSevenDaysChart.destroy();

    }

    lastSevenDaysChart = new Chart(canvas, {

            type: 'line',

            data: {

                labels: labels,
                datasets: [

                    {

                        label: 'Ventas',
                        data: totals,
                        borderColor: '#183B6B',
                        backgroundColor:
                            'rgba(22,191,253,.12)',
                        pointBackgroundColor:
                            '#16BFFD',
                        pointBorderColor:
                            '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 3,
                        fill: true,
                        tension: .35

                    }

                ]

            },

            options: {

                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'

                },

                plugins: {
                    legend: {
                        display: false

                    },

                    tooltip: {
                        callbacks: {
                            label: function(context){

                                return formatCurrency(context.parsed.y);

                            }

                        }

                    }

                },

                scales: {

                    x: {
                        grid: {
                            display: false
                        },

                        ticks: {
                            color: '#6c757d'
                        }

                    },

                    y: {
                        beginAtZero: true,
                        grid: {
                            color:
                                'rgba(24,59,107,.08)'

                        },

                        ticks: {
                            color: '#6c757d',
                            callback: function(value){

                                return '$'
                                    + Number(value)
                                        .toLocaleString(
                                            'es-MX'
                                        );

                            }

                        }

                    }

                }

            }

        }
    );

}


$(document).ready(function(){

    loadLastSevenDaysChart();

});

// =====================================
// VENTAS MENSUALES DEL AÑO ACTUAL
// =====================================

let monthlySalesChart = null;


async function loadMonthlySalesChart(){

    let loading =
        $('#monthlySalesLoading');

    loading.show();

    try{

        let response = await fetch(
            'api/get_monthly_sales_current_year.php'
        );

        let responseText =
            await response.text();

        let result;

        try{

            result =
                JSON.parse(responseText);

        }catch(error){

            console.error(responseText);

            throw new Error(
                'La API devolvió una respuesta inválida.'
            );

        }

        if(!result.success){

            throw new Error(
                result.message
                || 'No fue posible cargar las ventas mensuales.'
            );

        }

        let monthNames = [

            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'

        ];

        let labels =
            result.data.map(
                item =>
                    monthNames[
                        Number(item.mes) - 1
                    ]
            );

        let totals =
            result.data.map(
                item =>
                    Number(item.total)
            );

        let annualTotal =
            totals.reduce(
                (total, value) =>
                    total + value,
                0
            );

        $('#monthlySalesYear').text(
            `Total ${result.anio}`
        );

        $('#monthlySalesTotal').text(
            formatCurrency(
                annualTotal
            )
        );

        renderMonthlySalesChart(
            labels,
            totals
        );

    }catch(error){

        console.error(error);

        loading.html(`

            <div class="chart-error">

                <i class="fas fa-circle-exclamation"></i>

                No fue posible cargar la gráfica.

            </div>

        `);

        return;

    }

    loading.hide();

}


function renderMonthlySalesChart(
    labels,
    totals
){

    let canvas =
        document.getElementById(
            'monthlySalesChart'
        );

    if(!canvas){

        return;

    }

    if(monthlySalesChart){

        monthlySalesChart.destroy();

    }

    monthlySalesChart = new Chart(
        canvas,
        {

            type: 'bar',

            data: {

                labels: labels,

                datasets: [

                    {

                        label:
                            'Ventas mensuales',

                        data: totals,

                        backgroundColor:
                            'rgba(24,59,107,.85)',

                        borderColor:
                            '#183B6B',

                        borderWidth: 1,

                        borderRadius: 8,

                        borderSkipped: false,

                        maxBarThickness: 45

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {

                    intersect: false,

                    mode: 'index'

                },

                plugins: {

                    legend: {

                        display: false

                    },

                    tooltip: {

                        callbacks: {

                            label: function(context){

                                return formatCurrency(
                                    context.parsed.y
                                );

                            }

                        }

                    }

                },

                scales: {

                    x: {

                        grid: {

                            display: false

                        },

                        ticks: {

                            color: '#6c757d',

                            maxRotation: 45,

                            minRotation: 0

                        }

                    },

                    y: {

                        beginAtZero: true,

                        grid: {

                            color:
                                'rgba(24,59,107,.08)'

                        },

                        ticks: {

                            color: '#6c757d',

                            callback: function(value){

                                return '$'
                                    + Number(value)
                                        .toLocaleString(
                                            'es-MX'
                                        );

                            }

                        }

                    }

                }

            }

        }
    );

}


$(document).ready(function(){

    loadMonthlySalesChart();

});

// =====================================
// DISTRIBUCIÓN DE INGRESOS
// =====================================

let salesDistributionChart = null;


async function loadSalesDistributionChart(){

    let loading =
        $('#salesDistributionLoading');

    loading.show();

    try{

        let response = await fetch(
            'api/get_sales_distribution.php'
        );

        let responseText =
            await response.text();

        let result;

        try{

            result =
                JSON.parse(responseText);

        }catch(error){

            console.error(responseText);

            throw new Error(
                'La API devolvió una respuesta inválida.'
            );

        }

        if(!result.success){

            throw new Error(
                result.message
                || 'No fue posible cargar la distribución.'
            );

        }

        let labels =
            result.data.map(
                item => item.tipo
            );

        let totals =
            result.data.map(
                item => Number(item.total)
            );

        let generalTotal =
            totals.reduce(
                (total, value) =>
                    total + value,
                0
            );

        $('#salesDistributionTotal').text(
            formatCurrency(generalTotal)
        );

        renderSalesDistributionChart(
            labels,
            totals
        );

        renderSalesDistributionSummary(
            labels,
            totals,
            generalTotal
        );

    }catch(error){

        console.error(error);

        loading.html(`

            <div class="chart-error">

                <i class="fas fa-circle-exclamation"></i>

                No fue posible cargar la gráfica.

            </div>

        `);

        return;

    }

    loading.hide();

}


function renderSalesDistributionChart(
    labels,
    totals
){

    let canvas =
        document.getElementById(
            'salesDistributionChart'
        );

    if(!canvas){

        return;

    }

    if(salesDistributionChart){

        salesDistributionChart.destroy();

    }

    salesDistributionChart = new Chart(
        canvas,
        {

            type: 'doughnut',

            data: {

                labels: labels,

                datasets: [

                    {

                        data: totals,

                        backgroundColor: [

                            '#183B6B',
                            '#16BFFD',
                            '#28A745',
                            '#F4B942'

                        ],

                        borderColor: '#ffffff',

                        borderWidth: 4,

                        hoverOffset: 10

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '66%',

                plugins: {

                    legend: {

                        display: false

                    },

                    tooltip: {

                        callbacks: {

                            label: function(context){

                                return `${context.label}: ${
                                    formatCurrency(
                                        context.parsed
                                    )
                                }`;

                            }

                        }

                    }

                }

            }

        }
    );

}


function renderSalesDistributionSummary(
    labels,
    totals,
    generalTotal
){

    let colors = [

        '#183B6B',
        '#16BFFD',
        '#28A745',
        '#F4B942'

    ];

    let summary = '';

    labels.forEach(
        (label, index) => {

            let percentage =
                generalTotal > 0
                ? (
                    totals[index]
                    / generalTotal
                ) * 100
                : 0;

            summary += `

                <div class="distribution-summary-item">

                    <div class="distribution-summary-label">

                        <span
                            class="distribution-color"
                            style="background:${colors[index]}">
                        </span>

                        <span>

                            ${escapeAuditHtml(label)}

                        </span>

                    </div>

                    <div class="distribution-summary-values">

                        <strong>

                            ${formatCurrency(
                                totals[index]
                            )}

                        </strong>

                        <small>

                            ${percentage.toFixed(1)}%

                        </small>

                    </div>

                </div>

            `;

        }
    );

    $('#salesDistributionSummary').html(
        summary
    );

}


$(document).ready(function(){

    loadSalesDistributionChart();

});

// =====================================
// VENTAS POR USUARIO RESPONSABLE
// =====================================

let salesByUserChart = null;
let salesByUserPeriod = 'month';


async function loadSalesByUserChart(
    period = 'month'
){

    salesByUserPeriod = period;

    let loading =
        $('#salesByUserLoading');

    loading.show();

    try{

        let response = await fetch(

            'api/get_sales_by_user.php?periodo='
            + encodeURIComponent(period)

        );

        let responseText =
            await response.text();

        let result;

        try{

            result =
                JSON.parse(responseText);

        }catch(error){

            console.error(responseText);

            throw new Error(
                'La API devolvió una respuesta inválida.'
            );

        }

        if(!result.success){

            throw new Error(
                result.message
                || 'No fue posible cargar las ventas por usuario.'
            );

        }

        $('#salesByUserTotal').text(
            formatCurrency(
                Number(
                    result.total_general
                )
            )
        );

        $('#salesByUserOperations').text(
            Number(
                result.total_operaciones
            )
        );

        renderSalesByUserChart(
            result.data
        );

        renderSalesByUserTable(
            result.data
        );

    }catch(error){

        console.error(error);

        loading.html(`

            <div class="chart-error">

                <i class="fas fa-circle-exclamation"></i>

                No fue posible cargar la información.

            </div>

        `);

        $('#salesByUserTableBody').html(`

            <tr>

                <td
                    colspan="4"
                    class="text-center text-danger">

                    No fue posible cargar los datos.

                </td>

            </tr>

        `);

        return;

    }

    loading.hide();

}


function renderSalesByUserChart(
    records
){

    let canvas =
        document.getElementById(
            'salesByUserChart'
        );

    if(!canvas){

        return;

    }

    if(salesByUserChart){

        salesByUserChart.destroy();

    }

    let labels =
        records.map(
            item => item.nombre
        );

    let totals =
        records.map(
            item => Number(item.total)
        );

    salesByUserChart = new Chart(
        canvas,
        {

            type: 'bar',

            data: {

                labels: labels,

                datasets: [

                    {

                        label: 'Total vendido',

                        data: totals,

                        backgroundColor:
                            'rgba(24,59,107,.88)',

                        borderColor:
                            '#183B6B',

                        borderWidth: 1,

                        borderRadius: 8,

                        borderSkipped: false,

                        maxBarThickness: 38

                    }

                ]

            },

            options: {

                indexAxis: 'y',

                responsive: true,

                maintainAspectRatio: false,

                interaction: {

                    intersect: false,

                    mode: 'index'

                },

                plugins: {

                    legend: {

                        display: false

                    },

                    tooltip: {

                        callbacks: {

                            label: function(context){

                                return formatCurrency(
                                    context.parsed.x
                                );

                            }

                        }

                    }

                },

                scales: {

                    x: {

                        beginAtZero: true,

                        grid: {

                            color:
                                'rgba(24,59,107,.08)'

                        },

                        ticks: {

                            color: '#6c757d',

                            callback: function(value){

                                return '$'
                                    + Number(value)
                                        .toLocaleString(
                                            'es-MX'
                                        );

                            }

                        }

                    },

                    y: {

                        grid: {

                            display: false

                        },

                        ticks: {

                            color: '#183B6B',

                            font: {

                                weight: 600

                            }

                        }

                    }

                }

            }

        }
    );

}


function renderSalesByUserTable(
    records
){

    if(records.length === 0){

        $('#salesByUserTableBody').html(`

            <tr>

                <td
                    colspan="4"
                    class="text-center">

                    No hay ventas registradas en este periodo.

                </td>

            </tr>

        `);

        return;

    }

    let rows = '';

    records.forEach(
        record => {

            rows += `

                <tr>

                    <td>

                        <div class="user-sales-person">

                            <span>

                                <i class="fas fa-user"></i>

                            </span>

                            ${escapeAuditHtml(
                                record.nombre
                            )}

                        </div>

                    </td>

                    <td>

                        ${Number(
                            record.operaciones
                        )}

                    </td>

                    <td>

                        ${formatCurrency(
                            Number(
                                record.promedio
                            )
                        )}

                    </td>

                    <td>

                        <strong>

                            ${formatCurrency(
                                Number(
                                    record.total
                                )
                            )}

                        </strong>

                    </td>

                </tr>

            `;

        }
    );

    $('#salesByUserTableBody').html(
        rows
    );

}


//FILTROS

$(document).on(
    'click',
    '.btn-user-sales-filter',
    function(){

        $('.btn-user-sales-filter')
            .removeClass('active');

        $(this)
            .addClass('active');

        let period =
            $(this).data('period');

        loadSalesByUserChart(
            period
        );

    }
);


//CARGA INICIAL

$(document).ready(function(){

    loadSalesByUserChart(
        salesByUserPeriod
    );

});

// =====================================
// COMPARATIVO HOY, MES Y AÑO
// =====================================

let salesComparisonChart = null;


function loadSalesComparisonChart(){

    let dataContainer =
        document.getElementById(
            'salesComparisonData'
        );

    let canvas =
        document.getElementById(
            'salesComparisonChart'
        );

    if(
        !dataContainer
        || !canvas
    ){

        return;

    }

    let todayTotal = Number(
        dataContainer.dataset.today
        || 0
    );

    let monthTotal = Number(
        dataContainer.dataset.month
        || 0
    );

    let yearTotal = Number(
        dataContainer.dataset.year
        || 0
    );

    let totals = [

        todayTotal,
        monthTotal,
        yearTotal

    ];

    let highestTotal =
        Math.max(...totals);

    $('#salesComparisonHighest').text(
        formatCurrency(
            highestTotal
        )
    );

    if(salesComparisonChart){

        salesComparisonChart.destroy();

    }

    salesComparisonChart = new Chart(
        canvas,
        {

            type: 'bar',

            data: {

                labels: [

                    'Hoy',
                    'Mes',
                    'Año'

                ],

                datasets: [

                    {

                        label:
                            'Ingresos registrados',

                        data: totals,

                        backgroundColor: [

                            'rgba(22,191,253,.85)',
                            'rgba(24,59,107,.82)',
                            'rgba(40,167,69,.82)'

                        ],

                        borderColor: [

                            '#16BFFD',
                            '#183B6B',
                            '#28A745'

                        ],

                        borderWidth: 1,

                        borderRadius: 10,

                        borderSkipped: false,

                        maxBarThickness: 90

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {

                    intersect: false,

                    mode: 'index'

                },

                plugins: {

                    legend: {

                        display: false

                    },

                    tooltip: {

                        callbacks: {

                            label: function(context){

                                return formatCurrency(
                                    context.parsed.y
                                );

                            }

                        }

                    }

                },

                scales: {

                    x: {

                        grid: {

                            display: false

                        },

                        ticks: {

                            color: '#183B6B',

                            font: {

                                weight: 600

                            }

                        }

                    },

                    y: {

                        beginAtZero: true,

                        grid: {

                            color:
                                'rgba(24,59,107,.08)'

                        },

                        ticks: {

                            color: '#6c757d',

                            callback: function(value){

                                return '$'
                                    + Number(value)
                                        .toLocaleString(
                                            'es-MX'
                                        );

                            }

                        }

                    }

                }

            }

        }
    );

}


$(document).ready(function(){

    loadSalesComparisonChart();

});