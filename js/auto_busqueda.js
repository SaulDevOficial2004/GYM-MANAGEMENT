const clientSearch=document.getElementById('clientSearch');
const clearSearchButton=document.getElementById('clearSearchButton');
const emptyState=document.getElementById('emptyState');
const noResultsState=document.getElementById('noResultsState');
const searchLoading=document.getElementById('searchLoading');
const resultsSection=document.getElementById('resultsSection');
const resultsContainer=document.getElementById('resultsContainer');
const resultTable=document.getElementById('resultTable');
const mobileResults=document.getElementById('mobileResults');
const resultsCount=document.getElementById('resultsCount');

let searchTimeout=null;
let activeRequest=null;

clientSearch.addEventListener('input',function(){
    this.value=this.value
        .toUpperCase()
        .replace(/[^A-Z0-9-]/g,'')
        .slice(0,10);

    const search=this.value.trim();

    clearSearchButton.classList.toggle(
        'd-none',
        search.length===0
    );

    clearTimeout(searchTimeout);

    if(!/^CLI-[A-Z0-9]{6}$/.test(search)){
        resetSearchResults();
        return;
    }

    searchTimeout=setTimeout(()=>{
        searchMemberships(search);
    },350);
});

clearSearchButton.addEventListener('click',function(){
    clientSearch.value='';
    clientSearch.focus();
    clearSearchButton.classList.add('d-none');
    resetSearchResults();
});

async function searchMemberships(search){
    if(activeRequest){
        activeRequest.abort();
    }

    activeRequest=new AbortController();

    showSearchLoading();

    try{
        const response=await fetch(
            'api/search_status.php?search='+
            encodeURIComponent(search),
            {
                method:'GET',
                headers:{
                    'Accept':'application/json'
                },
                signal:activeRequest.signal
            }
        );

        const responseText=await response.text();
        let data;

        try{
            data=JSON.parse(responseText);
        }catch(error){
            console.error(responseText);

            throw new Error(
                'El servidor devolvió una respuesta inválida.'
            );
        }

        if(!response.ok){
            throw new Error(
                data.message||
                'No fue posible realizar la búsqueda.'
            );
        }

        if(!Array.isArray(data)){
            throw new Error(
                'La información recibida no es válida.'
            );
        }

        renderMembershipResults(data);
    }catch(error){
        if(error.name==='AbortError'){
            return;
        }

        console.error(error);

        hideSearchLoading();
        resetSearchResults(false);

        Swal.fire({
            icon:'error',
            title:'Error de búsqueda',
            text:error.message||
                'No fue posible realizar la búsqueda.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });
    }finally{
        activeRequest=null;
    }
}

function renderMembershipResults(data){
    hideSearchLoading();

    if(data.length===0){
        emptyState.classList.add('d-none');
        resultsSection.classList.add('d-none');
        noResultsState.classList.remove('d-none');
        resultTable.innerHTML='';
        mobileResults.innerHTML='';
        resultsCount.textContent='0 resultados';
        return;
    }

    const tableRows=[];
    const mobileCards=[];

    data.forEach(persona=>{
        const nombre=escapeHtml(
            persona.nombre||'Cliente'
        );

        const fecha=normalizeDate(
            persona.fecha_fin
        );

        const active=isMembershipActive(
            persona.fecha_fin
        );

        const badge=active
            ?`
                <span class="status-active">
                    <i class="fas fa-check-circle"></i>
                    Activa
                </span>
            `
            :`
                <span class="status-expired">
                    <i class="fas fa-times-circle"></i>
                    Vencida
                </span>
            `;

        tableRows.push(`
            <tr>
                <td>
                    <div class="membership-client">
                        <span>
                            <i class="fas fa-user"></i>
                        </span>

                        <div>
                            <strong>${nombre}</strong>
                            <small>Cliente registrado</small>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="membership-date">
                        <i class="fas fa-calendar-alt"></i>
                        ${fecha}
                    </span>
                </td>

                <td>
                    ${badge}
                </td>
            </tr>
        `);

        mobileCards.push(`
            <article class="membership-card">
                <div class="membership-card-header">
                    <div class="membership-card-client">
                        <span>
                            <i class="fas fa-user"></i>
                        </span>

                        <div>
                            <h5>${nombre}</h5>
                            <small>Cliente registrado</small>
                        </div>
                    </div>

                    ${badge}
                </div>

                <div class="membership-card-date">
                    <span>
                        <i class="fas fa-calendar-alt"></i>
                        Fecha de vencimiento
                    </span>

                    <strong>${fecha}</strong>
                </div>
            </article>
        `);
    });

    resultTable.innerHTML=tableRows.join('');
    mobileResults.innerHTML=mobileCards.join('');

    resultsCount.textContent=
        data.length+
        (data.length===1?' resultado':' resultados');

    emptyState.classList.add('d-none');
    noResultsState.classList.add('d-none');
    resultsSection.classList.remove('d-none');
}

function showSearchLoading(){
    emptyState.classList.add('d-none');
    noResultsState.classList.add('d-none');
    resultsSection.classList.add('d-none');
    searchLoading.classList.remove('d-none');
}

function hideSearchLoading(){
    searchLoading.classList.add('d-none');
}

function resetSearchResults(showEmpty=true){
    if(activeRequest){
        activeRequest.abort();
        activeRequest=null;
    }

    hideSearchLoading();

    resultTable.innerHTML='';
    mobileResults.innerHTML='';
    resultsCount.textContent='0 resultados';

    resultsSection.classList.add('d-none');
    noResultsState.classList.add('d-none');

    emptyState.classList.toggle(
        'd-none',
        !showEmpty
    );
}

function normalizeDate(dateValue){
    if(!dateValue){
        return 'Sin fecha registrada';
    }

    const parts=dateValue
        .toString()
        .split(' ')[0]
        .split('-');

    if(parts.length!==3){
        return 'Fecha no disponible';
    }

    const year=Number(parts[0]);
    const month=Number(parts[1])-1;
    const day=Number(parts[2]);

    const date=new Date(
        year,
        month,
        day
    );

    if(Number.isNaN(date.getTime())){
        return 'Fecha no disponible';
    }

    return date.toLocaleDateString(
        'es-MX',
        {
            day:'2-digit',
            month:'2-digit',
            year:'numeric'
        }
    );
}

function isMembershipActive(dateValue){
    if(!dateValue){
        return false;
    }

    const parts=dateValue
        .toString()
        .split(' ')[0]
        .split('-');

    if(parts.length!==3){
        return false;
    }

    const endDate=new Date(
        Number(parts[0]),
        Number(parts[1])-1,
        Number(parts[2]),
        23,
        59,
        59
    );

    const today=new Date();

    today.setHours(
        0,
        0,
        0,
        0
    );

    return endDate>=today;
}

function escapeHtml(value){
    return String(value)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;')
        .replace(/'/g,'&#039;');
}