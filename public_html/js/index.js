document.addEventListener("DOMContentLoaded", () => {
    
    // --- LÓGICA DO MENU HAMBURGUER ---
    const hamburger = document.getElementById("hamburger");
    if (hamburger) {
        hamburger.addEventListener("click", function () {
            const nav = document.querySelector(".nav-links");
            nav.classList.toggle("active");
            this.classList.toggle("active");
        });
    }

    // --- LÓGICA DO MODAL DE LOGIN (ABRIR E FECHAR) ---
    const modal = document.getElementById("loginModal");
    const loginTriggers = document.querySelectorAll(".login-trigger");
    const closeModalBtn = document.querySelector(".close-modal");

    if (modal) {
        const openModal = (e) => {
            if (e) e.preventDefault();
            modal.style.display = "block";
        };
        const closeModal = () => {
            modal.style.display = "none";
        };

        loginTriggers.forEach(trigger => trigger.addEventListener("click", openModal));
        if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
        window.addEventListener("click", (event) => {
            if (event.target == modal) closeModal();
        });
    }

    // --- LÓGICA DE SUBMISSÃO DO FORMULÁRIO DE LOGIN ---
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const email = document.getElementById("modalEmail").value.trim();
            const senha = document.getElementById("modalSenha").value.trim();
            const loginData = { email, senha };

            fetch(`${API_URL}/php/verifica_login.php`, { // CORRIGIDO: /php/verifica_login.php
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(loginData)
            })
            .then(res => res.ok ? res.json() : res.json().then(err => Promise.reject(err)))
            .then(data => {
                if (data.sucesso) {
                    // Usar caminhos a partir da raiz do site é mais seguro em produção
                    if (data.admin) {
                        window.location.href = "/paginas/dashboard.html"; 
                    } else {
                        window.location.href = "/paginas/agenda.html";
                    }
                } else {
                    alert("Erro no login: " + data.mensagem);
                }
            })
            .catch(err => {
                console.error("Erro na requisição de login:", err);
                alert(err.mensagem || "Erro de comunicação ao tentar fazer login.");
            });
        });
    }
    
    // --- LÓGICA DO BOTÃO DE LOGOUT ---
    const btnSair = document.getElementById('btn-sair');
    if (btnSair) {
        btnSair.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm("Tem certeza que deseja sair?")) {
                fetch(`${API_URL}/php/logout.php`, { // CORRIGIDO: /php/logout.php
                    method: 'POST',
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        alert("Você saiu com sucesso!");
                        window.location.href = '/index.html'; // Redireciona para a raiz do site
                    } else {
                        alert("Erro ao tentar sair.");
                    }
                });
            }
        });
    }

    // --- ATUALIZA VISIBILIDADE DOS BOTÕES LOGIN/LOGOUT ---
    const btnSairGlobal = document.getElementById("btn-sair");
    const btnLoginGlobal = document.querySelectorAll(".login-trigger");
    const linkDashboard = document.querySelector('a[href$="dashboard.html"]');

    if (btnSairGlobal && btnLoginGlobal.length > 0) {
        fetch(`${API_URL}/php/verifica_login.php`, { credentials: "include" }) // CORRIGIDO: /php/verifica_login.php
        .then((res) => res.json())
        .then((data) => {
            if (data.logado) {
                btnSairGlobal.style.display = "inline-block";
                btnLoginGlobal.forEach(btn => btn.style.display = "none");
                if (linkDashboard) {
                    linkDashboard.style.display = data.admin ? "inline-block" : "none";
                }
            } else {
                btnSairGlobal.style.display = "none";
                btnLoginGlobal.forEach(btn => btn.style.display = "inline-block");
                if (linkDashboard) {
                    linkDashboard.style.display = "none";
                }
            }
        });
    }
});