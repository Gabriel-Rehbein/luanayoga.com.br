<?php
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado para agendar.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados || !isset($dados['data_agendamento'], $dados['plano'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos para o agendamento.']);
    exit;
}

$id_paciente = $_SESSION['paciente_id'];
$iso_data_agendamento = $dados['data_agendamento']; // Data em formato ISO UTC (ex: ...T12:00:00.000Z)
$plano = $dados['plano'];

try {
    // --- CORREÇÃO DE FUSO HORÁRIO ---
    $date_obj_utc = new DateTime($iso_data_agendamento);
    $fuso_horario_local = new DateTimeZone('America/Sao_Paulo');
    // Converte o objeto de data de UTC para o fuso horário local
    $date_obj_local = $date_obj_utc->setTimezone($fuso_horario_local);
    
    // Formata o objeto para a string no padrão do MySQL DATETIME ('YYYY-MM-DD HH:MM:SS')
    $mysql_datetime_format = $date_obj_local->format('Y-m-d H:i:s');
    // -----------------------------------------------------------------------------

    $stmtHorario = $pdo->prepare("SELECT id FROM disponibilidade WHERE data_hora = ? AND status = 'disponivel'");
    $stmtHorario->execute([$mysql_datetime_format]); // <<< Usa a variável corrigida
    $horario = $stmtHorario->fetch();

    if (!$horario) {
        http_response_code(409); // Conflict
        echo json_encode(['sucesso' => false, 'mensagem' => 'Desculpe, este horário foi agendado por outra pessoa enquanto você preenchia a ficha. Por favor, escolha outro.']);
        exit;
    }
    $id_disponibilidade = $horario['id'];

    // Inserir os dados da ficha na tabela 'fichas'
    $stmtFicha = $pdo->prepare(
        "INSERT INTO fichas (id_paciente, nome, idade, estado_civil, email, nascimento, telefone, praticou_yoga, coluna, cirurgias, atividade_fisica, qual_atividade, plano) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmtFicha->execute([
        $id_paciente, $dados['nome'] ?? null, $dados['idade'] ?? null, $dados['estado_civil'] ?? null, $dados['email'] ?? null, 
        $dados['nascimento'] ?? null, $dados['telefone'] ?? null, $dados['praticou_yoga'] ?? null, $dados['coluna'] ?? null, 
        $dados['cirurgias'] ?? null, $dados['atividade_fisica'] ?? null, $dados['qual_atividade'] ?? null, $plano
    ]);

    // Inserir na tabela de agendamentos
    $stmtAgendamento = $pdo->prepare(
        "INSERT INTO agendamentos (id_paciente, id_disponibilidade, data_agendamento, plano, status) VALUES (?, ?, ?, ?, 'Confirmado')"
    );
    $stmtAgendamento->execute([$id_paciente, $id_disponibilidade, $mysql_datetime_format, $plano]);

    // Marcar o horário como indisponível
    $stmtUpdate = $pdo->prepare("UPDATE disponibilidade SET status = 'indisponivel' WHERE id = ?");
    $stmtUpdate->execute([$id_disponibilidade]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Agendamento e ficha enviados com sucesso! Você será redirecionado.']);

} catch (Exception $e) {
    http_response_code(500);
    error_log("Erro em salvar_ficha.php: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ocorreu um erro inesperado no servidor.']);
}
?>