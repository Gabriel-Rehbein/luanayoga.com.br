document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  if (!calendarEl) {
    return;
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "timeGridWeek",
    locale: "pt-br",
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    slotMinTime: "08:00:00",
    slotMaxTime: "22:00:00",
    allDaySlot: false,
    nowIndicator: true,

    events: {
      url: `${API_URL}/php/disponibilidade.php`, // CORRIGIDO: /php/disponibilidade.php
      failure: function() {
        alert('Erro ao carregar os horários.');
      }
    },

    eventDataTransform: function(eventInfo) {
      if (eventInfo.extendedProps.status === 'indisponivel') {
        return {
          ...eventInfo,
          title: 'Indisponível',
          backgroundColor: '#a9a9a9',
          borderColor: '#a9a9a9',
          classNames: ['evento-indisponivel']
        };
      } else {
        return {
          ...eventInfo,
          title: 'Disponível',
          backgroundColor: '#27ae60',
          borderColor: '#27ae60'
        };
      }
    },

    eventClick: function (info) {
      if (info.event.extendedProps.status === 'indisponivel') {
        alert('Este horário não está mais disponível para agendamento.');
        return false;
      }

      fetch(`${API_URL}/php/verifica_login.php`, { credentials: "include" }) // CORRIGIDO: /php/verifica_login.php
        .then(res => res.json())
        .then(data => {
          if (!data.logado) {
            const modal = document.getElementById("loginModal");
            if (modal) modal.style.display = "block";
            return;
          }

          if (confirm(`Deseja agendar para ${new Date(info.event.start).toLocaleString('pt-BR')}?`)) {
            localStorage.setItem("data_agendamento", info.event.start.toISOString());
            // O caminho relativo para outra página dentro de /paginas/ continua o mesmo
            window.location.href = "ficha.html";
          }
        });
    }
  });

  calendar.render();
  calendar.refetchEvents();
});