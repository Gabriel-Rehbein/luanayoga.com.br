document.getElementById('formRecuperar').addEventListener('submit', function(event) {
            event.preventDefault();
            const mensagemEl = document.getElementById('mensagemRecuperar');
            const email = document.getElementById('email').value;

            mensagemEl.textContent = 'Enviando...';
            mensagemEl.className = 'mensagem-form';

            if (!email) {
                mensagemEl.textContent = 'Por favor, digite seu e-mail.';
                mensagemEl.classList.add('erro');
                return;
            }

            fetch(`${API_URL}/php/recuperar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mensagemEl.textContent = data.mensagem;
                    mensagemEl.classList.remove('erro');
                    mensagemEl.classList.add('sucesso');
                } else {
                    // Mesmo em caso de erro no backend (ex: email não encontrado),
                    // mostramos uma mensagem de sucesso para o usuário por segurança.
                    // A mensagem de erro real só apareceria se o script falhasse completamente.
                    mensagemEl.textContent = 'Se um e-mail correspondente for encontrado, um link de recuperação foi enviado.';
                    mensagemEl.classList.add('sucesso');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                // Em caso de erro de rede, também mostramos a mensagem genérica.
                mensagemEl.textContent = 'Se um e-mail correspondente for encontrado, um link de recuperação foi enviado.';
                mensagemEl.classList.add('sucesso');
            });
        });