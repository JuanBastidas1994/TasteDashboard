$(document).ready(function() {
    loadCalendar();
}); 

function loadCalendar(){
    var calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        //lang: 'es',
        defaultView: 'agendaWeek',
        allDaySlot: false,
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        slotDuration: '00:30:00',
        slotLabelInterval: '00:30:00',
        //slotLabelFormat: 'h (:mm)a',
        slotLabelFormat: 'H:mm',
        minTime: '07:00:00',
        maxTime: '23:00:00',
        eventSources: [
            {
                url: 'json-pedidos-programados.php', // use the `url` property
                type: 'GET',
            }
        ],
        timeFormat: 'H(:mm)',
        eventClick: function(info) {
            let div = document.createElement('div');
            div.className = 'mail-item';
            div.setAttribute("data-value", info.id);
            var div_element = document.getElementById("calendar");
            div_element.appendChild(div);

            div.click();
            div.remove();
            $("#calendarModal").modal('hide');
        },
        eventMouseover: function(event, jsEvent, view) {
            $(this).attr('id', event.id);

            $('#'+event.id).popover({
                template: '<div class="popover popover-primary" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
                title: event.title,
                content: event.description,
                placement: 'top',
            });

            $('#'+event.id).popover('show');
        },
        eventMouseout: function(event, jsEvent, view) {
            $('#'+event.id).popover('hide');
        },
    });
}

function openCalendar(){
    $("#calendarModal").modal();

    var sources = {
        url: 'json-pedidos-programados.php', // use the `url` property
        type: 'GET',
    }
    /*
    $('#calendar').fullCalendar('removeEvents');
    $('#calendar').fullCalendar('addEventSource', sources);
    $('#calendar').fullCalendar('rerenderEvents');*/

    //$('#calendar').fullCalendar('removeEvents');
    $('#calendar').fullCalendar('removeEventSource', sources);
    $('#calendar').fullCalendar('addEventSource', sources);
    $('#calendar').fullCalendar('refetchEvents');
    $('#calendar').fullCalendar('render');

}