<?php
require 'conexao.php';
header('Content-Type: application/json');

//Garante que apenas um admin logado pode ver as fichas.
if (!isset($_SESSION['paciente_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); // Acesso Negado
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}

$id_paciente_alvo = $_GET['id_paciente'] ?? null;

if (!$id_paciente_alvo) {
    http_response_code(400); // Requisição Inválida
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID do paciente não fornecido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM fichas WHERE id_paciente = ? ORDER BY data_preenchimento DESC");
    $stmt->execute([$id_paciente_alvo]);
    $fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode(['sucesso' => true, 'fichas' => $fichas]);

} catch (PDOException $e) {
    http_response_code(500); // Erro no Servidor
    error_log("Erro ao buscar fichas: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar as fichas no banco de dados.']);
}
?>