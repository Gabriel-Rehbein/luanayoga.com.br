<?php
//  conexão, sessão e cabeçalhos CORS.
require 'conexao.php';
header('Content-Type: application/json');

// Segurança: Garante que um usuário está logado.
if (!isset($_SESSION['paciente_id'])) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Você precisa estar logado.']);
    exit;
}

// Pega os dados JSON e o ID da sessão.
$dados = json_decode(file_get_contents('php://input'), true);
$id_logado = $_SESSION['paciente_id'];

// Validação dos dados recebidos.
$nome = $dados['nome'] ?? '';
$email = $dados['email'] ?? '';
$senha = $dados['senha'] ?? ''; // Senha pode ser vazia

if (empty($nome) || empty($email)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nome e e-mail são campos obrigatórios.']);
    exit;
}

try {
    // Lógica para atualizar a senha apenas se uma nova for fornecida.
    if (!empty($senha)) {
        // Validação extra: força da nova senha.
        if (strlen($senha) < 6) {
             http_response_code(400);
             echo json_encode(['sucesso' => false, 'mensagem' => 'A nova senha deve ter pelo menos 6 caracteres.']);
             exit;
        }
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE pacientes SET nome = ?, email = ?, senha = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $senha_hash, $id_logado]);
    } else {
        // Se nenhuma senha nova foi enviada, atualiza apenas nome e e-mail.
        $stmt = $pdo->prepare("UPDATE pacientes SET nome = ?, email = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $id_logado]);
    }
    
    // Atualiza o nome na sessão para que ele apareça atualizado no site sem precisar de um novo login.
    $_SESSION['paciente_nome'] = $nome;

    // Envia a resposta de sucesso.
    echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!']);

} catch (PDOException $e) {
    // Tratamento de erro específico para e-mail duplicado.
    // O código de erro '1062' é padrão do MySQL/MariaDB para 'Duplicate entry'.
    if ($e->errorInfo[1] == 1062) {
        http_response_code(409); // Conflict
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este e-mail já está em uso por outra conta.']);
    } else {
        http_response_code(500);
        error_log("Erro ao atualizar perfil: " . $e->getMessage()); // Grava o erro real no log do servidor.
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ocorreu um erro no servidor ao atualizar o perfil.']);
    }
}
?>