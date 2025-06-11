<?php
require 'conexao.php';
header('Content-Type: application/json');

// Segurança: Garante que um paciente está logado para poder agendar
if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado para agendar.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

// Validação dos dados essenciais recebidos
if (!$dados || !isset($dados['data_agendamento'], $dados['plano'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos para o agendamento.']);
    exit;
}

$id_paciente = $_SESSION['paciente_id'];
$iso_data_agendamento = $dados['data_agendamento'];
$plano = $dados['plano'];

try {
    // Converte a data do formato ISO UTC (do JavaScript) para o formato DATETIME do MySQL no fuso local
    $date_obj = new DateTime($iso_data_agendamento, new DateTimeZone('UTC'));
    $date_obj->setTimezone(new DateTimeZone('America/Sao_Paulo'));
    $mysql_datetime_format = $date_obj->format('Y-m-d H:i:s');

    // Encontra o horário na tabela de disponibilidade para garantir que ele ainda está livre
    $stmtHorario = $pdo->prepare("SELECT id FROM disponibilidade WHERE data_hora = ? AND status = 'disponivel'");
    $stmtHorario->execute([$mysql_datetime_format]);
    $horario = $stmtHorario->fetch();

    if (!$horario) {
        http_response_code(409); // Conflict
        echo json_encode(['sucesso' => false, 'mensagem' => 'Desculpe, este horário foi agendado por outra pessoa enquanto você preenchia a ficha. Por favor, escolha outro.']);
        exit;
    }
    $id_disponibilidade = $horario['id'];

    // Insere os dados da ficha na tabela 'fichas'
    $stmtFicha = $pdo->prepare(
        "INSERT INTO fichas (id_paciente, nome, idade, estado_civil, email, nascimento, telefone, praticou_yoga, coluna, cirurgias, atividade_fisica, qual_atividade, plano) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmtFicha->execute([
        $id_paciente, $dados['nome'] ?? null, $dados['idade'] ?? null, $dados['estado_civil'] ?? null, $dados['email'] ?? null, 
        $dados['nascimento'] ?? null, $dados['telefone'] ?? null, $dados['praticou_yoga'] ?? null, $dados['coluna'] ?? null, 
        $dados['cirurgias'] ?? null, $dados['atividade_fisica'] ?? null, $dados['qual_atividade'] ?? null, $plano
    ]);

    // Insere o registro na tabela de agendamentos
    $stmtAgendamento = $pdo->prepare(
        "INSERT INTO agendamentos (id_paciente, id_disponibilidade, data_agendamento, plano, status, pago) VALUES (?, ?, ?, ?, 'Confirmado', 0)"
    );
    $stmtAgendamento->execute([$id_paciente, $id_disponibilidade, $mysql_datetime_format, $plano]);

    // Atualiza o status do horário para 'indisponivel'
    $stmtUpdate = $pdo->prepare("UPDATE disponibilidade SET status = 'indisponivel' WHERE id = ?");
    $stmtUpdate->execute([$id_disponibilidade]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Agendamento realizado com sucesso!']);

} catch (Exception $e) {
    http_response_code(500);
    error_log("Erro em salvar_ficha.php: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ocorreu um erro inesperado no servidor.']);
}
?>