(function(){
    const checkInterval=5000;
    let redirecting=false;

    async function forceLogout(){
        if(redirecting){
            return;
        }

        redirecting=true;
        document.querySelectorAll('button,input,select,textarea,a').forEach((element)=>{
            element.setAttribute('disabled','disabled');
            element.style.pointerEvents='none';
        });

        await fetch('php_action/logout.php',{cache:'no-store'}).catch(()=>{});
        window.location.href='index.php?session=disabled';
    }

    async function validateSession(){
        try{
            const response=await fetch('api/session_status.php',{cache:'no-store'});
            const data=await response.json().catch(()=>({}));

            if(response.status===401||data.force_logout||data.active===false){
                await forceLogout();
            }
        }catch(error){
            return;
        }
    }

    //VALIDACION USUARIO
    window.setInterval(validateSession,checkInterval);
    validateSession();
})();
