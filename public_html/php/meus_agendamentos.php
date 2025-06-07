<?php
header("Access-Control-Allow-Origin: https://luanayoga.com.br");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Resposta imediata a requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

session_start();
require 'conexao.php';

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

$id_paciente = $_SESSION['paciente_id'];
$params = [];

$sql = "
    SELECT
        DATE(data_agendamento) as data,
        TIME(data_agendamento) as horario,
        status,
        plano,
        pago
    FROM agendamentos
    WHERE id_paciente = ?
";
$params[] = $id_paciente;

if (isset($_GET['data']) && !empty($_GET['data'])) {
    $sql .= " AND DATE(data_agendamento) = ?";
    $params[] = $_GET['data'];
}

$sql .= " ORDER BY data_agendamento DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($agendamentos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao buscar agendamentos.']);
}
