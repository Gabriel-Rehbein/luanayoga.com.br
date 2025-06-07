document.addEventListener('DOMContentLoaded', function() {
        const formCadastro = document.getElementById('formCadastro');
        const mensagemCadastroEl = document.getElementById('mensagemCadastro');

        if (formCadastro) {
            formCadastro.addEventListener('submit', function(event) {
                event.preventDefault(); // Impede o recarregamento da página
                mensagemCadastroEl.textContent = '';
                mensagemCadastroEl.className = 'mensagem-form';

                const nome = document.getElementById('cadastroNome').value.trim();
                const email = document.getElementById('cadastroEmail').value.trim();
                const telefone = document.getElementById('cadastroTelefone').value.trim();
                const senha = document.getElementById('cadastroSenha').value;
                const confirmarSenha = document.getElementById('cadastroConfirmarSenha').value;

                if (!nome || !email || !senha || !confirmarSenha) {
                    mensagemCadastroEl.textContent = 'Por favor, preencha todos os campos obrigatórios (*).';
                    mensagemCadastroEl.classList.add('erro');
                    return;
                }

                if (senha !== confirmarSenha) {
                    mensagemCadastroEl.textContent = 'As senhas não coincidem.';
                    mensagemCadastroEl.classList.add('erro');
                    return;
                }
                
                if (senha.length < 6) {
                    mensagemCadastroEl.textContent = 'A senha deve ter pelo menos 6 caracteres.';
                    mensagemCadastroEl.classList.add('erro');
                    return;
                }

                const dadosCadastro = { nome, email, telefone, senha };

                fetch(`${API_URL}/php/cadastro.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dadosCadastro)
                })
                .then(response => {
                    if (!response.ok) { 
                        return response.json().then(errData => { throw new Error(errData.mensagem || `Erro HTTP: ${response.status}`); });
                    }
                    return response.json(); 
                })
                .then(data => {
                    if (data.sucesso) {
                        mensagemCadastroEl.textContent = data.mensagem + " Você será redirecionado para a página de login em 3 segundos.";
                        mensagemCadastroEl.classList.add('sucesso');
                        formCadastro.reset();
                        setTimeout(() => { window.location.href = 'cadastro.html'; }, 3000);
                    } else {
                        mensagemCadastroEl.textContent = data.mensagem;
                        mensagemCadastroEl.classList.add('erro');
                    }
                })
                .catch(error => {
                    console.error('Erro no cadastro:', error);
                    mensagemCadastroEl.textContent = error.message || 'Ocorreu um erro ao tentar cadastrar. Tente novamente.';
                    mensagemCadastroEl.classList.add('erro');
                });
            });
        }
    });