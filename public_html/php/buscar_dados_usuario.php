<?php
require 'conexao.php';
header('Content-Type: application/json');

// Garante que há um usuário logado
if (!isset($_SESSION['paciente_id'])) {
    http_response_code(403); // Acesso Negado
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

// Pega o ID do próprio usuário logado a partir da sessão
$id = $_SESSION['paciente_id'];

try {
    // Busca nome e email do usuário logado
    $stmt = $pdo->prepare("SELECT nome, email FROM pacientes WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode(['sucesso' => true, 'usuario' => $usuario]);
    } else {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário da sessão não encontrado no banco.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor.']);
}
?>