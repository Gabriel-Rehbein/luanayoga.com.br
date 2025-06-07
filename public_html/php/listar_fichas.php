<?php
require 'conexao.php'; 
header('Content-Type: application/json'); 

// SEGURANÇA: proteção dos dados dos clientes.
if (!isset($_SESSION['paciente_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); // Acesso Negado
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}

try {
    // Busca os dados na tabela `pacientes`.
    $stmt = $pdo->query("SELECT id, nome, email, telefone FROM pacientes WHERE admin = 0 ORDER BY nome ASC");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envia a lista de clientes para o JavaScript.
    echo json_encode($clientes);

} catch (PDOException $e) {
    http_response_code(500); // Erro no servidor
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar a lista de clientes.']);
}
?>