
// CONTADOR ANIMADO

const counters =
document.querySelectorAll('.counter');

const observer =
new IntersectionObserver(

(entries) => {

    entries.forEach(entry => {

        if(entry.isIntersecting){

            const counter =
            entry.target;

            const target =
            parseInt(
                counter.dataset.target
            );

            let current = 0;

            const increment =
            Math.ceil(target / 100);

            const updateCounter = () => {

                current += increment;

                if(current >= target){

                    counter.innerText =
                    target;

                    return;
                }

                counter.innerText =
                current;

                requestAnimationFrame(
                    updateCounter
                );

            };

            updateCounter();

            observer.unobserve(counter);

        }

    });

},

{
    threshold:0.5
}

);

counters.forEach(counter => {

    observer.observe(counter);

});




$('#goToFolioBtn').click(function(){

    $('#transferenciaModal')
        .modal('hide');

    setTimeout(() => {

        $('#folioModal')
            .modal('show');

    },300);

});

//BUSCAR FOLIO
$('#ingresarFolioBtn').click(async function(){

    let folio = $('#folio_cliente').val().trim().toUpperCase();

    let formData = new FormData();

    formData.append('folio', folio);

    let response =
        await fetch('api/search_folio.php',{
                method:'POST',
                body:formData
            }
        );

    let data = await response.json();

    if(data.success){

        window.location.href = data.redirect;

    }else{

        Swal.fire({
            icon:'error',
            title:'Error',
            text:data.message,
            confirmButtonColor:
            '#183B6B'
        });

    }

});
