document.addEventListener("DOMContentLoaded", () => {
  // Inicializa o calendário
  const calendarEl = document.getElementById('calendar');

  if (calendarEl) {
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'pt-br'
    });
    calendar.render();
  }

  // Código para buscar os agendamentos do dia
  const dataDeHoje = new Date().toISOString().slice(0, 10);

  fetch(`${API_URL}/php/meus_agendamentos.php?data=${dataDeHoje}`)
    .then(res => res.json())
    .then(agendamentos => {
      console.log(`Agendamentos para a data ${dataDeHoje}:`, agendamentos);
      // Aqui você pode usar os dados para atualizar a interface ou marcar eventos no calendário
    })
    .catch(err => console.error("Erro ao buscar agendamentos por data:", err));
});


fetch(window.baseUrl + "verifica_login.php")

document.getElementById("hamburger").addEventListener("click", function () {
  const nav = document.querySelector(".nav-links");
  nav.classList.toggle("active");
  this.classList.toggle("active");
});

function getBasePath() {
  return window.location.pathname.includes("./paginas/") ? "../" : "./";
}

// Modal de Login
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("loginModal");
  const loginLinks = document.querySelectorAll(
    'a[href*="login"], .login-trigger'
  );
  const closeBtn = document.querySelector(".close-modal");

  if (!modal || !closeBtn) return;

  function openModal(e) {
    if (e) e.preventDefault();
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
    document.getElementById("modalEmail")?.focus();
  }

  function closeModal() {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

  loginLinks.forEach((link) => {
    link.addEventListener("click", openModal);
  });

  closeBtn.addEventListener("click", closeModal);

  modal.addEventListener("click", function (e) {
    if (e.target === modal) closeModal();
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && modal.style.display === "block") {
      closeModal();
    }
  });

  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const email = document.getElementById("modalEmail").value.trim();
      const senha = document.getElementById("modalSenha").value.trim();

      // Validação básica
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert("Por favor, insira um e-mail válido.");
        return;
      }

      if (senha === "") {
        alert("Por favor, preencha o campo de senha.");
        return;
      }
      const loginData = {
        email: email,
        senha: senha
      };

      //Temporário 
       const requestOptions = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify(loginData)
      };

        console.log("DEBUG JS: Tentando fazer login com os seguintes dados:");
        console.log("DEBUG JS: URL:", `${API_URL}/php/verifica_login.php`);
        console.log("DEBUG JS: Opções da Requisição:", requestOptions);
        console.log("DEBUG JS: Cabeçalhos enviados:", requestOptions.headers);
        console.log("DEBUG JS: Corpo enviado (string JSON):", requestOptions.body);
      //Temporário
        fetch(`${API_URL}/php/verifica_login.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify(loginData)
      })
      .then((res) => {
        if (!res.ok) { 
          return res.json().then(errData => {
            throw new Error(errData.mensagem || `O servidor respondeu com erro: ${res.status}`);
          }).catch(() => {
            throw new Error(`O servidor respondeu com erro: ${res.status}`);
          });
        }
        return res.json(); 
      })
    
    .then((data) => {
          if (data.sucesso) {
            if (data.admin) {
            window.location.href = "https://luanayoga.com.br/paginas/dashboard.html";
            } else {
            window.location.href = "https://luanayoga.com.br/paginas/agenda.html";
            }
          } else {
            alert("Erro no login: " + data.mensagem);
          }
        })
        
      .catch((err) => {
        console.error("Erro na requisição ou processamento:", err);
        alert(err.message || "Erro ao tentar se conectar ao servidor."); 
      });
    }); // chave do form.addEventListener
  } // chave do if (loginForm)
}); 

// Abrir modal de login automático
document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem("openLoginModal") === "true") {
    localStorage.removeItem("openLoginModal");
    const modal = document.getElementById("loginModal");
    if (modal) {
      modal.style.display = "block";
      document.body.style.overflow = "hidden";
    }
  }
});

// Slider de imagens
document.addEventListener("DOMContentLoaded", function () {
  const slider = document.querySelector(".slider");
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  const prevBtn = document.querySelector(".prev-btn");
  const nextBtn = document.querySelector(".next-btn");

  if (!slider || slides.length === 0) return;

  let currentIndex = 0;
  let autoPlayInterval;
  const totalSlides = slides.length;

  function updateSlider() {
    slider.style.transform = `translateX(-${currentIndex * 100}%)`;
    dots.forEach((dot, index) => {
      dot.classList.toggle("active", index === currentIndex);
    });
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateSlider();
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
    updateSlider();
  }

  function startAutoPlay() {
    autoPlayInterval = setInterval(nextSlide, 5000);
  }

  function stopAutoPlay() {
    clearInterval(autoPlayInterval);
  }

  updateSlider();
  startAutoPlay();

  nextBtn.addEventListener("click", () => {
    stopAutoPlay();
    nextSlide();
    startAutoPlay();
  });

  prevBtn.addEventListener("click", () => {
    stopAutoPlay();
    prevSlide();
    startAutoPlay();
  });

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      stopAutoPlay();
      currentIndex = index;
      updateSlider();
      startAutoPlay();
    });
  });

  slider.addEventListener("mouseenter", stopAutoPlay);
  slider.addEventListener("mouseleave", startAutoPlay);
});

// FullCalendar (só agenda.html)
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar"); 
  if (!calendarEl) {
    return; 
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "timeGridWeek",
    slotMinTime: "08:00:00",
    slotMaxTime: "22:00:00",
    allDaySlot: false,
    nowIndicator: true,
    locale: "pt-br",
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    events: {
      url: `${API_URL}/php/disponibilidade.php`, 
      method: 'GET',
      failure: function() {
        alert('Erro ao carregar os horários disponíveis.'); 
      },
      eventDataTransform: function(eventInfo) {
        return {
          id: eventInfo.id,
          start: eventInfo.start,
          allDay: eventInfo.allDay, 
          extendedProps: eventInfo.extendedProps 
        };
      }
    },
    eventClick: function (info) {
      fetch(`${API_URL}/php/verifica_login.php`, {credentials: "include"}) 
        .then((res) => {
          if (!res.ok) { 
            return res.json().then(errData => {
              throw new Error(errData.mensagem || `Erro HTTP: ${res.status}`);
            }).catch(() => {
              throw new Error(`O servidor respondeu com erro: ${res.status} (e a resposta não era JSON)`);
            });
          }
          return res.json();
        })
        .then((data) => {
          if (!data.logado) {
            const modal = document.getElementById("loginModal");
            if (modal) {
              modal.style.display = "block";
              document.body.style.overflow = "hidden";
              document.getElementById("modalEmail")?.focus();
            }
            return;
          }

          const confirmar = confirm(
            `Deseja agendar para ${new Date(info.event.start).toLocaleString('pt-BR', {dateStyle: 'short', timeStyle: 'short'})}?`
          );
          if (confirmar) {
            localStorage.setItem(
              "data_agendamento",
              info.event.start.toISOString() 
            );
            window.location.href = "ficha.html"; 
          }
        })
        .catch(error => {
            console.error("Erro ao verificar login para agendamento:", error);
            alert(error.message || "Não foi possível verificar seu status de login. Tente novamente.");
        });
    } 
  }); 
  calendar.render();

}); // Fim do DOMContentLoaded fulCalendar

// Logout
function confirmarLogout(e) {
  if (e) e.preventDefault(); 
  const base = getBasePath(); 

  if (confirm("Tem certeza que deseja sair?")) {
    fetch(`${API_URL}/php/logout.php`, { 
      method: "POST", 
      credentials: "include" 
    })
    .then((res) => {
      if (!res.ok) {
        throw new Error(`Erro HTTP no logout: ${res.status}`);
      }
      return res.json(); 
    })
    .then((data) => {
      if (data.sucesso) {
        alert("Você saiu com sucesso!");
        window.location.href = "https://luanayoga.com.br/index.html";
      } else {
        alert("Ocorreu um erro no servidor ao tentar sair.");
      }
    })
    .catch((err) => {
      console.error("Erro ao fazer logout:", err);
      alert(err.message || "Erro de comunicação ao sair. Tente novamente.");
    });
  }
}

const btnSairElement = document.getElementById("btn-sair"); 
if (btnSairElement) {
    btnSairElement.addEventListener("click", confirmarLogout);
}

window.addEventListener("DOMContentLoaded", () => {
  const base = getBasePath();

  fetch(`${API_URL}/php/verifica_login.php`, {
    method: "GET",
    credentials: "include",
  })
    .then((res) => res.json())
    .then((data) => {
      const btnSair = document.getElementById("btn-sair"); 
      const btnLogin = document.getElementById("btn-login");

      if (!btnSair || !btnLogin) return;

      if (data.logado) {
        btnSair.style.display = "block";
        btnLogin.style.display = "none";
      } else {
        btnSair.style.display = "none";
        btnLogin.style.display = "block";
      }
    })
    .catch((err) => console.error("Erro ao verificar login na carga da página:", err));
});
function toggleQualAtividade() {
  const select = document.getElementById("atividade_fisica");
  const campo = document.getElementById("campo_qual_atividade");
  if (campo && select)
    campo.style.display = select.value === "Sim" ? "block" : "none";
  if (select && select.value !== "Sim") {
    const input = document.getElementById("qual_atividade");
    if (input) input.value = "";
  }
}

function toggleAlergia() {
  const select = document.getElementById("alergia");
  const campo = document.getElementById("campo_quais_alergias");
  if (campo && select)
    campo.style.display = select.value === "Sim" ? "block" : "none";
  if (select && select.value !== "Sim") {
    const input = document.getElementById("quais_alergias");
    if (input) input.value = "";
  }
}

// Salva Disponibilidade
document.addEventListener("DOMContentLoaded", () => {
  const calendarEl = document.getElementById("calendario-disponibilidade");
  if (!calendarEl) return;

  // Elementos do modal
  const modalEditar = document.getElementById("modalEditar");
  const modalExcluir = document.getElementById("modalExcluir");
  const infoEvento = document.getElementById("infoEvento");
  const selectStatus = document.getElementById("selectStatus");
  const btnSalvarEdicao = document.getElementById("btnSalvarEdicao");
  const btnCancelarEdicao = document.getElementById("btnCancelarEdicao");
  const btnConfirmarExcluir = document.getElementById("btnConfirmarExcluir");
  const btnCancelarExcluir = document.getElementById("btnCancelarExcluir");

  let eventoSelecionado = null;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "timeGridWeek",
    locale: "pt-br",
    slotMinTime: "08:00:00",
    slotMaxTime: "22:00:00",
    allDaySlot: false,
    selectable: true,
    events: {
    url: `${API_URL}/php/disponibilidade.php`, 
    method: 'GET', 
    failure: function() {
        alert('Erro ao carregar os horários disponíveis.'); 
    },
    eventDataTransform: function(eventInfo) {
        // Para o paciente, o backend já filtra para enviar apenas os 'disponivel'.
        // Retornamos o evento com um título padrão.
        return {
            id: eventInfo.id, 
            title: 'Disponível', 
            start: eventInfo.start,
            allDay: eventInfo.allDay,
            extendedProps: eventInfo.extendedProps
        };
    }
},
    select: (info) => {
      const status = prompt(
        "Digite 'd' para Disponível ou 'i' para Indisponível:",
        "d"
      );
      if (status !== "d" && status !== "i") {
        alert("Opção inválida.");
        return;
      }
      const statusFull = status === "d" ? "disponivel" : "indisponivel";

      fetch(`${API_URL}/php/salvar_disponibilidade.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          data: info.startStr.slice(0, 10),
          horario: info.startStr.slice(11, 19),
          status: statusFull,
        }),
      })
        .then((res) => res.text())
        .then((msg) => {
          alert(msg);
          calendar.refetchEvents();
        })
        .catch(() => alert("Erro ao salvar disponibilidade."));
    },
    eventClick: (info) => {
      eventoSelecionado = info.event;

      infoEvento.textContent = `Data: ${eventoSelecionado.start.toLocaleDateString()} - Horário: ${eventoSelecionado.start.toLocaleTimeString()}`;
      selectStatus.value = eventoSelecionado.extendedProps.status;

      // Abrir modal de edição
      modalEditar.style.display = "flex";
    },
  });

  calendar.render();

  // Salvar edição
  btnSalvarEdicao.addEventListener("click", () => {
    if (!eventoSelecionado) return;

    const novoStatus = selectStatus.value;

    fetch(`${API_URL}/php/salvar_disponibilidade.php`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: eventoSelecionado.id,
        data: eventoSelecionado.startStr.slice(0, 10),
        horario: eventoSelecionado.startStr.slice(11, 19),
        status: novoStatus,
      }),
    })
      .then((res) => res.text())
      .then((msg) => {
        alert(msg);
        calendar.refetchEvents();
        modalEditar.style.display = "none";
      })
      .catch(() => alert("Erro ao atualizar disponibilidade."));
  });

  // Cancelar edição
  btnCancelarEdicao.addEventListener("click", () => {
    modalEditar.style.display = "none";
  });

  // Abrir modal de exclusão via botão direito ou outro trigger (exemplo: botão excluir dentro do modal editar)
  // Para simplificar, vamos abrir o modal excluir pelo botão "Excluir" que vamos adicionar abaixo:

  // Cria botão excluir dentro do modal editar
  const btnExcluir = document.createElement("button");
  btnExcluir.textContent = "Excluir";
  btnExcluir.style.marginLeft = "1rem";
  btnExcluir.style.backgroundColor = "#e74c3c";
  btnExcluir.style.color = "white";
  btnExcluir.style.border = "none";
  btnExcluir.style.padding = "0.4rem 1rem";
  btnExcluir.style.borderRadius = "4px";
  btnExcluir.style.cursor = "pointer";

  btnExcluir.addEventListener("click", () => {
    modalEditar.style.display = "none";
    modalExcluir.style.display = "flex";
  });

  btnSalvarEdicao.insertAdjacentElement("afterend", btnExcluir);

  // Confirmar exclusão
  btnConfirmarExcluir.addEventListener("click", () => {
    if (!eventoSelecionado) return;

    fetch(`${API_URL}/php/salvar_disponibilidade.php`, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: eventoSelecionado.id }),
    })
      .then((res) => res.text())
      .then((msg) => {
        alert(msg);
        calendar.refetchEvents();
        modalExcluir.style.display = "none";
      })
      .catch(() => alert("Erro ao excluir disponibilidade."));
  });

  // Cancelar exclusão
  btnCancelarExcluir.addEventListener("click", () => {
    modalExcluir.style.display = "none";
  });
});


// mostrar agendamentos
document.addEventListener("DOMContentLoaded", () => {
  const base = getBasePath();

  fetch(`${API_URL}/php/verifica_login.php`, { credentials: "include" })
    .then((res) => res.json())
    .then((data) => {
      if (data.logado) {
        const secao = document.getElementById("meus-agendamentos");
        if (secao) secao.style.display = "block";

        fetch(`${API_URL}/php/meus_agendamentos.php`, { credentials: "include" })
          .then((res) => res.json())
          .then((agendamentos) => {
            const lista = document.getElementById("lista-agendamentos");

            if (!lista) return;

            function formatarData(data) {
              if (!data) return "Não informada";
              const partes = data.split("-");
              return `${partes[2]}/${partes[1]}/${partes[0]}`;
            }

            function formatarHorario(horario) {
              if (!horario) return "";
              const partes = horario.split(":");
              return `${partes[0]}:${partes[1]}`;
            }

            if (Array.isArray(agendamentos) && agendamentos.length > 0) {
              lista.innerHTML = "";

              agendamentos.forEach((item) => {
                const li = document.createElement("li");

                const dataFormatada = formatarData(item.data);
                const horarioFormatado = formatarHorario(item.horario);
                const plano = item.plano ?? "Não informado";
                const status = item.status ?? "Pendente";
                const pago = item.pago ? "Sim" : "Não";

                li.innerHTML = `
  <span><strong>Data:</strong> ${dataFormatada}</span>
  <span><strong>Horário:</strong> ${horarioFormatado}</span>
  <span><strong>Status:</strong> ${status}</span>
  <span><strong>Plano:</strong> ${plano}</span>
  <span><strong>Pago:</strong> ${pago}</span>
`;

                lista.appendChild(li);
              });
            } else {
              lista.innerHTML = "<li>Você ainda não possui agendamentos.</li>";
            }
          })
          .catch((err) => console.error("Erro ao carregar agendamentos:", err));
      }
    })
    .catch((err) => console.error("Erro ao verificar login:", err));
});

document.addEventListener("DOMContentLoaded", () => {
    const dataDeHoje = new Date().toISOString().slice(0, 10); // Pega a data de hoje no formato AAAA-MM-DD

    fetch(`${API_URL}/php/meus_agendamentos.php?data=${dataDeHoje}`)
        .then(res => res.json())
        .then(agendamentos => {
            console.log(`Agendamentos para a data ${dataDeHoje}:`, agendamentos);
            // Aqui você pode usar os dados para atualizar a interface
        })
        .catch(err => console.error("Erro ao buscar agendamentos por data:", err));
});
