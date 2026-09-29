function loginError(){

    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Usuario o contraseña incorrectos',
        confirmButtonColor: '#183B6B',
        background:'#ffffff',
        color:'#183B6B'
    });

}


function loginSuccess(){

    Toastify({
        text: "Inicio de sesión exitoso",
        duration: 3000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,

        style: {
            background: "linear-gradient(to right, #183B6B, #16BFFD)",
            borderRadius: "10px"
        }

    }).showToast();

}

function updateMembership(){
    Toastify({
        text: "Membresía actualizada exitosamente",
        duration: 3000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,

        style: {
            background: "linear-gradient(to right, #183B6B, #16BFFD)",
            borderRadius: "10px"

        }
    }).showToast();
}

document.addEventListener('DOMContentLoaded',function(){
    const flashes=String(
        document.body?document.body.dataset.flash||'':''
    ).split(',');

    if(flashes.includes('loginError')){
        loginError();
    }

    if(flashes.includes('loginSuccess')){
        loginSuccess();
    }

    if(flashes.includes('updateMembership')){
        updateMembership();
    }
});