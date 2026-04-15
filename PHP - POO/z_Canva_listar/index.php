<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hikmadent - Agenda de Citas</title>
    
    <!-- FullCalendar CDN (v6) -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    
    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Estilos personalizados para Hikmadent -->
    <style>
        :root {
            --primary-color: #0077cc;
            --secondary-color: #eef7ff;
            --accent-color: #28a745;
            --text-dark: #333;
            --bg-light: #f8fbff;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }

        .header p {
            color: #666;
            margin-top: 5px;
        }

        #calendar {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 119, 204, 0.1);
        }

        /* Personalización de FullCalendar */
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600;
            color: var(--primary-color);
        }

        .fc-button-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .fc-button-primary:hover {
            background-color: #005fa3 !important;
        }

        .fc-daygrid-event {
            border-radius: 5px !important;
            padding: 2px 5px !important;
            font-size: 0.85rem !important;
        }

        /* Estilos para el Modal */
        #eventModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 25px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover { color: #333; }

        .modal-body p { margin: 8px 0; font-size: 0.95rem; }
        .modal-body strong { color: var(--primary-color); }
    </style>
</head>
<body>

    <div class="header">
        <h1>Hikmadent - Agenda de Citas</h1>
        <p>Gestiona tus citas dentales de forma profesional</p>
    </div>
    
    <div id='calendar'></div>

    <!-- Modal para detalles del evento -->
    <div id="eventModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle">Detalles de la Cita</h2>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> <span id="modalEventTitle"></span></p>
                <p><strong>Inicio:</strong> <span id="modalStart"></span></p>
                <p><strong>Fin:</strong> <span id="modalEnd"></span></p>
                <p><strong>Servicio:</strong> <span id="modalService"></span></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var modal = document.getElementById("eventModal");
            var span = document.getElementsByClassName("close")[0];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek', // Vista semanal por defecto para citas
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '09:00:00', // Abre a las 9 AM
                slotMaxTime: '18:00:00', // Cierra a las 6 PM
                slotDuration: '00:30:00', // Bloques de 30 mins
                allDaySlot: false,
                editable: true,
                selectable: true,
                events: [
                    {
                        title: 'Juan Pérez',
                        start: new Date().toISOString().slice(0, 10) + 'T10:00:00',
                        end: new Date().toISOString().slice(0, 10) + 'T11:00:00',
                        extendedProps: { service: 'Limpieza Dental' },
                        color: '#0077cc'
                    },
                    {
                        title: 'María García',
                        start: new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().slice(0, 10) + 'T15:30:00',
                        end: new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().slice(0, 10) + 'T16:00:00',
                        extendedProps: { service: 'Consulta General' },
                        color: '#28a745'
                    },
                    {
                        title: 'Roberto Sánchez',
                        start: new Date(new Date().setDate(new Date().getDate() + 2)).toISOString().slice(0, 10) + 'T09:00:00',
                        end: new Date(new Date().setDate(new Date().getDate() + 2)).toISOString().slice(0, 10) + 'T10:30:00',
                        extendedProps: { service: 'Ortodoncia' },
                        color: '#e67e22'
                    }
                ],
                
                // Mostrar modal al hacer click
                eventClick: function(info) {
                    document.getElementById('modalEventTitle').innerText = info.event.title;
                    document.getElementById('modalStart').innerText = info.event.start.toLocaleString();
                    document.getElementById('modalEnd').innerText = info.event.end ? info.event.end.toLocaleString() : 'N/A';
                    document.getElementById('modalService').innerText = info.event.extendedProps.service || 'No especificado';
                    modal.style.display = "block";
                },

                // Manejar selección de rango (Nueva Cita)
                select: function(info) {
                    var title = prompt('Nombre del Paciente:');
                    if (title) {
                        calendar.addEvent({
                            title: title,
                            start: info.startStr,
                            end: info.endStr,
                            color: '#0077cc'
                        });
                    }
                    calendar.unselect();
                }
            });

            calendar.render();

            // Cerrar modal
            span.onclick = function() { modal.style.display = "none"; }
            window.onclick = function(event) {
                if (event.target == modal) { modal.style.display = "none"; }
            }
        });
    </script>

</body>
</html>
