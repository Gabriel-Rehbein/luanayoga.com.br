<?php
require 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Você precisa estar logado.']);
    exit;
}

$id_usuario_logado = $_SESSION['paciente_id'];
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$params = [];

$sql = "
    SELECT 
        a.id, 
        p.nome as nome_paciente, 
        a.data_agendamento, 
        a.plano, 
        a.pago,
        a.status
    FROM agendamentos a
    JOIN pacientes p ON a.id_paciente = p.id
";

// Se não for admin, filtra apenas pelos seus próprios agendamentos
if (!$is_admin) {
    $sql .= " WHERE a.id_paciente = ?";
    $params[] = $id_usuario_logado;
}

// Adiciona o filtro de data
if (isset($_GET['data']) && !empty($_GET['data'])) {
    // Se já tiver um WHERE, usa AND. Se não, usa WHERE.
    $sql .= $is_admin ? " WHERE DATE(a.data_agendamento) = ?" : " AND DATE(a.data_agendamento) = ?";
    $params[] = $_GET['data'];
}

$sql .= " ORDER BY a.data_agendamento DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($agendamentos);

} catch (PDOException $e) {
    http_response_code(500); // Erro no Servidor
    error_log("Erro em meus_agendamentos.php: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao carregar os agendamentos.']);
}
?>