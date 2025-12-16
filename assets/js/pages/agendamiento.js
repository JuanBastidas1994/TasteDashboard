$(function(){
    const colors = [
        'red',
        'purple',
        'orange',
        'teal',
        'pink',
        'gray',
        'black',
        '#3780f3',
        '#378322',
        '#378011',
        '#378001',
        '#378201',
        '#372001',
        '#373001',
        '#374002'
    ]
    const offices = []
    const cmb = document.getElementById('sucursalId')
    for(let i = 0; i< cmb.length; i++){
        const newElement = {
            office_id: cmb[i].value,
            color: colors[i],
            nameOffice: cmb[i].text
        }
        $(`#office_color_${newElement.office_id}`).css('background', colors[i])
        offices.push(newElement)
    }

    let calendar = null
    function eventsUpdate(event, delta, revertFunc){
        const idUser = $('#userId').val()
        const newForm = new FormData()
        newForm.append('cod_usuario', idUser)
        newForm.append('hora_inicio', event.start.format('HH:mm'))
        newForm.append('hora_final', event.end.format('HH:mm'))
        newForm.append('cod_disponibilidad', event.event_id)
        newForm.append('dia', event.start.day())
        newForm.append('cod_sucursal', event.cod_sucursal);
        $.ajax({
            data: newForm,
            url: 'controllers/controlador_agendamiento.php?metodo=actualizar',
            processData: false,
            contentType: false,
            type: 'post',
            beforeSend: () => OpenLoad('Actualizando...'),
            success: response => {
                console.log(response)
                console.log('Actualizado con exito')
            },
            error: e => {
                revertFunc()
                console.log(e)
            },
            complete: () => CloseLoad()
        })
    }

    
    function createCalendar(){
        return $('#calendar').fullCalendar({
            header: {
                left: 'agendaWeek',
            },
            columnFormat: 'dddd',
            defaultView: 'agendaWeek',
            selectable: true,
            allDaySlot: false,
            navLinks: true,
            editable: true,
            timeFormat: 'H:mm',
            minTime: '5:00',
            maxTime: '21:00',
            slotLabelFormat: "HH:mm",
            select: (start, end) => {
                const startH = start.format('HH:mm')
                const endH = end.format('HH:mm')
                $('#lblInicio').text('Hora inicio: '+ startH)
                $('#lblFin').text('Hora final: '+ endH)
                $('#lblDia').text('Día: '+ start.format('dddd'))
                $('#horaInicioId').val(startH)
                $('#horaFinalId').val(endH)
                $('#diaId').val(start.day())
                $('#formSucursal').modal()
            },
            eventMouseover: (event, jsEvent, view) => {
                // console.log('Hola mundo')
            },
            eventRenderWait: 1000,
            events: {
                'url': 'controllers/controlador_agendamiento.php?metodo=obtenerPorUsuario&cod_usuario='+$('#userId').val(),
                'type': 'GET'
            },
            eventRender: (eventObj, $el)=>{
                const office = offices.find(item => item.office_id == eventObj.cod_sucursal)
                eventObj.id = `event_${eventObj.event_id}`
                console.log(office)
   
                $($el).css('background-color', office.color)
                $($el).css('border-color', office.color)        
            },
            eventClick: (calEvent, jsEvent, view) => {
                console.log(calEvent)
                swal.fire({
                    title: '¿Estas seguro?',
                    text: "¡No podrás revertir esto!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    padding: '2em'
                }).then(result => {
                    if(result.value){
                        $.ajax({
                            url: `controllers/controlador_agendamiento.php?metodo=eliminar&cod_disponibilidad=${calEvent.event_id}`,
                            beforeSend: () => OpenLoad('Eliminando...'),
                            success: response => {
                                console.log(response)
                            },
                            error: e => console.log(e),
                            complete: () => CloseLoad()

                        })
                    }
                })
            },
            eventResize: eventsUpdate,
            eventDrop: eventsUpdate
        })
    }

    function reload(){
        if(calendar != null)
            calendar.fullCalendar('refetchEvents')
        else
            calendar = createCalendar()
    }
    $('#addUserAvailable').on('submit', function(event){
        event.preventDefault()
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr('method'),
            beforeSend: () => OpenLoad('Actualizando...'),
            success: response => {
                $('#formSucursal').modal('hide')
                reload()
                console.log(response)
            },
            error: e => {
                console.log(`Error: ${e}`)
            },
            complete: () => CloseLoad()

        })
    })
    $('.event-check').on('change', function(e){
        const formData = new FormData()
        const idService = $(this).data('service')
        const idUser = $('#userId').val()
        console.log(idUser)
        console.log(idService)
        formData.append('cod_usuario', idUser)
        formData.append('cod_servicio', idService)
        $.ajax({
            url: `controllers/controlador_agendamiento.php?metodo=crearServicioUsuario`,
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: () => OpenLoad('Actualizando...'),
            success: response => {
                console.log(response)
            },
            error: e => {
                console.log(e)
            },
            complete: () => CloseLoad()

        })
    })

    reload()
})