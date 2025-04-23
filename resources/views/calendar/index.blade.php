<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>
    <style>
        #calendar {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 10px;
        }
    </style>
</head>
<body>
    <div id='calendar'></div>

    <div id="eventModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Nuevo Evento</h2>
        <form id="eventForm">
            <div>
                <label for="eventTitle">Título:</label>
                <input type="text" id="eventTitle" required>
            </div>
            <div>
                <label for="eventStart">Inicio:</label>
                <input type="datetime-local" id="eventStart" required>
            </div>
            <div>
                <label for="eventEnd">Fin:</label>
                <input type="datetime-local" id="eventEnd">
            </div>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('eventModal');
        var span = document.getElementsByClassName('close')[0];
        
        span.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/calendar/events',
            dateClick: function(info) {
                document.getElementById('eventStart').value = info.dateStr + 'T00:00';
                modal.style.display = 'block';
            }
        });
        
        calendar.render();
        
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var eventData = {
                title: document.getElementById('eventTitle').value,
                start: document.getElementById('eventStart').value,
                end: document.getElementById('eventEnd').value
            };
            
            fetch('/calendar/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(eventData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.style.display = 'none';
                    calendar.refetchEvents();
                } else {
                    alert('Error al guardar el evento');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el evento');
            });
        });
    });
</script>
</body>
</html>