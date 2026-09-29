document.addEventListener('DOMContentLoaded',function(){
    initCounters();
    initTransferFlow();
    initFolioSearch();
    initNavbarClose();
});

function initCounters(){
    const counters=document.querySelectorAll('.counter');

    if(!counters.length){
        return;
    }

    const observer=new IntersectionObserver(entries=>{
        entries.forEach(entry=>{
            if(!entry.isIntersecting){
                return;
            }

            animateCounter(entry.target);
            observer.unobserve(entry.target);
        });
    },{
        threshold:.45
    });

    counters.forEach(counter=>{
        observer.observe(counter);
    });
}

function animateCounter(counter){
    const target=parseInt(counter.dataset.target,10);

    if(Number.isNaN(target)||target<0){
        counter.textContent='0';
        return;
    }

    if(target===0){
        counter.textContent='0';
        return;
    }

    const duration=900;
    const startTime=performance.now();

    function updateCounter(currentTime){
        const elapsed=currentTime-startTime;
        const progress=Math.min(elapsed/duration,1);
        const easedProgress=1-Math.pow(1-progress,3);
        const currentValue=Math.floor(target*easedProgress);

        counter.textContent=currentValue.toLocaleString('es-MX');

        if(progress<1){
            requestAnimationFrame(updateCounter);
        }else{
            counter.textContent=target.toLocaleString('es-MX');
        }
    }

    requestAnimationFrame(updateCounter);
}

function initTransferFlow(){
    const goToFolioButton=document.getElementById('goToFolioBtn');

    if(!goToFolioButton){
        return;
    }

    goToFolioButton.addEventListener('click',function(){
        $('#transferenciaModal').modal('hide');

        setTimeout(()=>{
            $('#folioModal').modal('show');

            setTimeout(()=>{
                const folioInput=document.getElementById('folio_cliente');

                if(folioInput){
                    folioInput.focus();
                }
            },250);
        },300);
    });
}

function initFolioSearch(){
    const folioButton=document.getElementById('ingresarFolioBtn');
    const folioInput=document.getElementById('folio_cliente');

    if(!folioButton||!folioInput){
        return;
    }

    folioInput.addEventListener('input',function(){
        this.value=this.value
            .toUpperCase()
            .replace(/[^A-Z0-9-]/g,'')
            .slice(0,10);

        this.classList.remove('is-invalid');
    });

    folioInput.addEventListener('keydown',function(event){
        if(event.key==='Enter'){
            event.preventDefault();
            searchFolio();
        }
    });

    folioButton.addEventListener('click',searchFolio);
}

async function searchFolio(){
    const folioInput=document.getElementById('folio_cliente');
    const folioButton=document.getElementById('ingresarFolioBtn');

    if(!folioInput||!folioButton){
        return;
    }

    const folio=folioInput.value.trim().toUpperCase();

    if(folio===''){
        folioInput.classList.add('is-invalid');

        Swal.fire({
            icon:'warning',
            title:'Ingresa tu folio',
            text:'Escribe el folio que recibiste al registrarte.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });

        folioInput.focus();
        return;
    }

    if(!/^CLI-[A-Z0-9]{6}$/.test(folio)){
        folioInput.classList.add('is-invalid');

        Swal.fire({
            icon:'warning',
            title:'Folio no válido',
            text:'El formato correcto es CLI-XXXXXX.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });

        folioInput.focus();
        return;
    }

    setFolioLoading(true);

    try{
        const formData=new FormData();
        formData.append('folio',folio);

        const response=await fetch('api/search_folio.php',{
            method:'POST',
            body:formData,
            headers:{
                'Accept':'application/json'
            }
        });

        const responseText=await response.text();
        let data;

        try{
            data=JSON.parse(responseText);
        }catch(error){
            console.error(responseText);
            throw new Error('El servidor devolvió una respuesta inválida.');
        }

        if(!response.ok){
            throw new Error(
                data.message||'No fue posible consultar el folio.'
            );
        }

        if(data.success&&data.redirect){
            window.location.href=data.redirect;
            return;
        }

        Swal.fire({
            icon:'error',
            title:'Folio no encontrado',
            text:data.message||'Verifica el folio e inténtalo nuevamente.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });
    }catch(error){
        console.error(error);

        Swal.fire({
            icon:'error',
            title:'Error de consulta',
            text:error.message||'No fue posible comunicarse con el servidor.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });
    }finally{
        setFolioLoading(false);
    }
}

function setFolioLoading(loading){
    const folioButton=document.getElementById('ingresarFolioBtn');
    const folioInput=document.getElementById('folio_cliente');

    if(!folioButton||!folioInput){
        return;
    }

    folioButton.disabled=loading;
    folioInput.disabled=loading;

    if(loading){
        folioButton.innerHTML=
            '<i class="fas fa-spinner fa-spin mr-2"></i>Consultando...';
    }else{
        folioButton.innerHTML=
            '<i class="fas fa-arrow-right mr-2"></i>Ingresar';
    }
}

function initNavbarClose(){
    const navLinks=document.querySelectorAll(
        '#navbarNav .nav-link,#navbarNav .nav-membership-button'
    );

    navLinks.forEach(link=>{
        link.addEventListener('click',function(){
            if(window.innerWidth>991){
                return;
            }

            const navbarCollapse=document.getElementById('navbarNav');

            if(
                navbarCollapse
                &&navbarCollapse.classList.contains('show')
            ){
                $('#navbarNav').collapse('hide');
            }
        });
    });
}