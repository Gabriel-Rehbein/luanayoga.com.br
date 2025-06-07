<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require 'conexao.php';

$dados = json_decode(file_get_contents('php://input'), true);
$data = $dados['data'] ?? null;
$hora = $dados['hora'] ?? null;

if (!$data || !$hora) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO disponibilidades (data, horario) VALUES (?, ?)');
    $stmt->execute([$data, $hora]);
    echo json_encode(['sucesso' => true, 'mensagem' => 'Disponibilidade salva.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
}
