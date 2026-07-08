//BUSCADOR DE MEMBRESIAS

$('#clientSearch').on('keyup', async function(){

    let search = $(this).val().trim();

    if(search.length < 2){

        $('#resultsContainer').addClass('d-none');
        $('#emptyState').removeClass('d-none');
        $('#resultTable').html('');

        return;
    }

    try{

        let response = await fetch(`api/search_status.php?search=${encodeURIComponent(search)}`);

        let data = await response.json();

        $('#emptyState').addClass('d-none');
        $('#resultsContainer').removeClass('d-none');

        let html = '';
        let mobileHtml = '';

        //SIN RESULTADOS

        if(data.length === 0){

            html = `
                <tr>
                    <td colspan="4"
                        class="text-center py-4">
                        <i class="fas fa-search"></i>
                        <br><br>

                        No se encontraron coincidencias
                    </td>
                </tr>
            `;
        }else{

            let hoy = new Date();

            data.forEach(persona => {

                let fechaFin = new Date (persona.fecha_fin);

                let badge = '';

                if(fechaFin >= hoy){
                    badge = `
                        <span class="status-active">
                            Activo
                        </span>
                    `;
                }else{

                    badge = `
                    <span class="status-expired">
                        Vencido
                    </span>
                    `;
                }

                let fechaMostrar = fechaFin.toLocaleDateString('es-MX');

                mobileHtml += `

                    <div class="membership-card">
                        <h5>
                            <i class="fas fa-user"></i>

                            ${persona.nombre}
                        </h5>

                        <p>
                            <i class="fas fa-calendar"></i>

                            <strong>Vencimiento:</strong>

                            ${fechaMostrar}
                        </p>

                        ${badge}

                    </div>

                    `;

                if(window.innerWidth <= 768){

                    html += `

                    <div class="membership-card">
                        <h5>
                            <i class="fas fa-user"></i>

                            ${persona.nombre}
                        </h5>

                        <p>
                            <i class="fas fa-calendar"></i>

                            Vigencia:
                            ${fechaMostrar}
                        </p>

                        ${badge}

                    </div>

                    `;

                }else{

                    html += `
                        <tr>
                            <td>
                                ${persona.nombre}
                            </td>

                            <td>
                                ${fechaMostrar}
                            </td>

                            <td>
                                ${badge}
                            </td>
                        </tr>
                    `;

                }
            });
        }

        if(window.innerWidth <= 768){

            $('#resultsContainer').addClass('d-none');
            $('#mobileResults').removeClass('d-none');
            $('#mobileResults').html(mobileHtml);

        }else{

            $('#mobileResults').addClass('d-none');
            $('#resultsContainer').removeClass('d-none');
            $('#resultTable').html(html);

        }

    }catch(error){

        console.error(error);


        Swal.fire({

            icon:'error',
            title:'Error',
            text:'No fue posible realizar la búsqueda.',
            confirmButtonColor:'#183B6B'
        });
    }
});