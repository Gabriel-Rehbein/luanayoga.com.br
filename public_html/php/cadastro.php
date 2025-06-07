<?php
// backend/php/cadastro.php (Versão Final e Correta)

require 'conexao.php';
header('Content-Type: application/json');

// Pega os dados JSON enviados pelo JavaScript
$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    http_response_code(400); 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos ou mal formatados.']);
    exit;
}

// Atribui os dados a variáveis
$nome = $dados['nome'] ?? '';
$email = $dados['email'] ?? '';
$senha = $dados['senha'] ?? '';
$telefone = $dados['telefone'] ?? ''; // Telefone pode ser opcional

// Validações no lado do servidor
if (empty($nome) || empty($email) || empty($senha)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nome, e-mail e senha são obrigatórios.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Formato de e-mail inválido.']);
    exit;
}
if (strlen($senha) < 6) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'A senha deve ter pelo menos 6 caracteres.']);
    exit;
}

// Cria o hash seguro da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Verifica se o e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este e-mail já está cadastrado. Tente outro ou faça login.']);
        exit;
    }

    // Insere o novo usuário no banco com admin = 0 (padrão para paciente)
    $stmt = $pdo->prepare(
        "INSERT INTO pacientes (nome, email, senha, telefone, admin) VALUES (?, ?, ?, ?, 0)"
    );
    $stmt->execute([$nome, $email, $senha_hash, $telefone]);

    // Responde com sucesso
    echo json_encode(['sucesso' => true, 'mensagem' => 'Cadastro realizado com sucesso!']);
    exit;

} catch (PDOException $e) {
    error_log("Erro no cadastro PDO: " . $e->getMessage()); // Para você ver o erro no log do servidor
    http_response_code(500); // Internal Server Error
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ocorreu um erro no servidor ao tentar realizar o cadastro.']);
    exit;
}
?>