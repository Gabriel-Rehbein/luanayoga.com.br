<?php
require 'conexao.php';

header('Content-Type: application/json');

// Garante que apenas um administrador logado pode adicionar horários.
if (!isset($_SESSION['paciente_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); // Acesso Negado
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Apenas administradores podem realizar esta ação.']);
    exit;
}

// 3. Pega os dados JSON enviados pelo JavaScript do dashboard.
$dados = json_decode(file_get_contents('php://input'), true);

// O JavaScript do dashboard que fizemos envia um único campo 'data_hora'.
$data_hora = $dados['data_hora'] ?? null;

if (!$data_hora) {
    http_response_code(400); // Requisição Inválida
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos. A data e hora são obrigatórias.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO disponibilidade (data_hora, status) VALUES (?, 'disponivel')");
    $stmt->execute([$data_hora]);
    
    // 6. Resposta de sucesso em formato JSON.
    echo json_encode(['sucesso' => true, 'mensagem' => 'Novo horário de disponibilidade salvo com sucesso!']);

} catch (PDOException $e) {
    // Trata erros, como tentar inserir um horário que já existe (se a coluna for UNIQUE).
    if ($e->errorInfo[1] == 1062) { // Código de erro do MySQL/MariaDB para entrada duplicada.
        http_response_code(409); // Conflict
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este horário já existe na agenda.']);
    } else {
        http_response_code(500); 
        error_log("Erro ao adicionar disponibilidade: " . $e->getMessage()); 
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor ao tentar salvar a disponibilidade.']);
    }
}
?>