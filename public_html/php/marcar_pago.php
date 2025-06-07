<?php
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); // Forbidden
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}

// Pega os dados JSON enviados pelo JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Verifica se o ID do agendamento foi realmente enviado.
if (empty($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID do agendamento não fornecido.']);
    exit;
}

try {
    // Atualiza o status de pagamento.
    $stmt = $pdo->prepare("UPDATE agendamentos SET pago = 1 WHERE id = ?");
    $stmt->execute([$data['id']]);

    // Informa ao frontend que a operação foi bem-sucedida.
    echo json_encode(['sucesso' => true, 'mensagem' => 'Agendamento marcado como pago.']);

} catch (PDOException $e) {
    http_response_code(500); // Erro no Servidor
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar o agendamento.']);
}
?>