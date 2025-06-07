<?php
require 'conexao.php'; 
header('Content-Type: application/json'); 

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Você precisa estar logado.']);
    exit;
}

// VERIFICA O TIPO DE USUÁRIO (ADMIN OU PACIENTE)
$id_usuario_logado = $_SESSION['paciente_id'];
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

try {
    // CONSULTA SQL DE ACORDO COM O TIPO DE USUÁRIO
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

    // Se o usuário NÃO for um administrador, adicionamos o filtro WHERE
    // para buscar apenas os agendamentos do ID do usuário que está logado.
    if (!$is_admin) {
        $sql .= " WHERE a.id_paciente = ?";
    }
    
    $sql .= " ORDER BY a.data_agendamento DESC";

    // EXECUTA A CONSULTA USANDO PDO
    $stmt = $pdo->prepare($sql);

    if (!$is_admin) {
        // Se não for admin, ID dele é como parâmetro para o WHERE
        $stmt->execute([$id_usuario_logado]);
    } else {
        // Se for admin, executa a consulta sem o WHERE
        $stmt->execute();
    }

    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // RETORNA A LISTA DE AGENDAMENTOS EM JSON
    echo json_encode($agendamentos);

} catch (PDOException $e) {
    http_response_code(500); // Erro no Servidor
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao carregar os agendamentos.']);
    // Para depuração: error_log($e->getMessage());
}
?>