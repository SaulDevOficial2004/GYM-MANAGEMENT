// BUSCADOR

$('#searchReport').on(
    'keyup',
    function(){

        let value =
            $(this)
            .val()
            .toLowerCase();

        $('#reportsTable tbody tr')
            .filter(function(){

                $(this).toggle(

                    $(this)
                    .text()
                    .toLowerCase()
                    .indexOf(value) > -1

                );

            });

    }
);

// FILTROS

$('.btn-report-filter').click(function(){

    $('.btn-report-filter')
        .removeClass('active');

    $(this)
        .addClass('active');

    let filter =
        $(this).data('filter');

    let today =
        new Date()
        .toISOString()
        .split('T')[0];

    let month =
        today.substring(0,7);

    let year =
        today.substring(0,4);

    $('#reportsTable tbody tr')
        .each(function(){

            let date =
                $(this)
                .data('date');

            if(filter === 'all'){

                $(this).show();

            }

            else if(
                filter === 'today'
            ){

                $(this).toggle(
                    date.startsWith(today)
                );

            }

            else if(
                filter === 'month'
            ){

                $(this).toggle(
                    date.startsWith(month)
                );

            }

            else if(
                filter === 'year'
            ){

                $(this).toggle(
                    date.startsWith(year)
                );

            }

        });

});