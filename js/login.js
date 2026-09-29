const loginForm=document.getElementById('loginForm');
const telefonoInput=document.getElementById('telefono');
const passwordInput=document.getElementById('password');
const telefonoMessage=document.getElementById('telefonoMessage');
const passwordMessage=document.getElementById('passwordMessage');
const loginSubmitButton=document.getElementById('loginSubmitButton');
const togglePasswordButton=document.getElementById('togglePassword');

function setLoginFieldError(input,messageElement,message){
    input.classList.remove('input-success');
    input.classList.add('input-error');
    messageElement.textContent=message;
}

function setLoginFieldSuccess(input,messageElement){
    input.classList.remove('input-error');
    input.classList.add('input-success');
    messageElement.textContent='';
}

function clearLoginFieldState(input,messageElement){
    input.classList.remove('input-error','input-success');
    messageElement.textContent='';
}

function validateLoginForm(){
    const telefono=telefonoInput.value.trim();
    const password=passwordInput.value;
    let valid=true;

    clearLoginFieldState(telefonoInput,telefonoMessage);
    clearLoginFieldState(passwordInput,passwordMessage);

    if(telefono===''){
        setLoginFieldError(
            telefonoInput,
            telefonoMessage,
            'Ingresa tu número de teléfono.'
        );
        valid=false;
    }else{
        setLoginFieldSuccess(
            telefonoInput,
            telefonoMessage
        );
    }

    if(password===''){
        setLoginFieldError(
            passwordInput,
            passwordMessage,
            'Ingresa tu contraseña.'
        );
        valid=false;
    }else{
        setLoginFieldSuccess(
            passwordInput,
            passwordMessage
        );
    }

    return valid;
}

function setLoginLoading(loading){
    loginSubmitButton.disabled=loading;
    loginSubmitButton.classList.toggle('is-loading',loading);

    const icon=loginSubmitButton.querySelector(
        '.login-submit-icon i'
    );

    const text=loginSubmitButton.querySelector(
        '.login-submit-text'
    );

    if(loading){
        icon.className='fas fa-spinner';
        text.textContent='Verificando acceso...';
    }else{
        icon.className='fas fa-sign-in-alt';
        text.textContent='Ingresar al sistema';
    }
}

togglePasswordButton.addEventListener('click',function(){
    const showing=passwordInput.type==='text';

    passwordInput.type=showing
        ?'password'
        :'text';

    const icon=this.querySelector('i');

    icon.className=showing
        ?'fas fa-eye'
        :'fas fa-eye-slash';

    this.setAttribute(
        'aria-label',
        showing
            ?'Mostrar contraseña'
            :'Ocultar contraseña'
    );
});

telefonoInput.addEventListener('input',function(){
    clearLoginFieldState(
        telefonoInput,
        telefonoMessage
    );
});

passwordInput.addEventListener('input',function(){
    clearLoginFieldState(
        passwordInput,
        passwordMessage
    );
});

loginForm.addEventListener('submit',async function(event){
    event.preventDefault();

    if(!validateLoginForm()){
        return;
    }

    const telefono=telefonoInput.value.trim();
    const password=passwordInput.value;

    setLoginLoading(true);

    try{
        const response=await fetch('api/login_api.php',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json'
            },
            body:JSON.stringify({
                telefono,
                password
            })
        });

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
                data.message||'No fue posible iniciar sesión.'
            );
        }

        if(data.status==='success'){
            Toastify({
                text:data.message,
                duration:1800,
                gravity:'top',
                position:'right',
                stopOnFocus:true,
                style:{
                    background:'linear-gradient(135deg,#183b6b,#176dd3)',
                    borderRadius:'10px',
                    boxShadow:'0 10px 25px rgba(24,59,107,.2)',
                    fontFamily:'Poppins,sans-serif',
                    fontSize:'12px'
                }
            }).showToast();

            setTimeout(()=>{
                window.location.href=data.redirect||'pagina.php';
            },1000);

            return;
        }

        Swal.fire({
            icon:'error',
            title:'No fue posible ingresar',
            text:data.message||'Revisa tus credenciales e inténtalo nuevamente.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });
    }catch(error){
        console.error(error);

        Swal.fire({
            icon:'error',
            title:'Error de conexión',
            text:error.message||'No fue posible comunicarse con el servidor.',
            confirmButtonText:'Entendido',
            confirmButtonColor:'#176dd3'
        });
    }finally{
        setLoginLoading(false);
    }
});