<?php
// backend/php/disponibilidade.php (Versão Unificada e Final)

require 'conexao.php';
header('Content-Type: application/json');

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

try {
    // UMA ÚNICA CONSULTA PARA TODOS!
    // A consulta com LEFT JOIN agora é padrão para todos os usuários.
    // Assim, todos (admin e pacientes) recebem a lista completa de horários.
    $sql = "
        SELECT 
            d.id, 
            d.data_hora, 
            d.status,
            p.nome as nome_paciente 
        FROM 
            disponibilidade d
        LEFT JOIN agendamentos a ON d.id = a.id_disponibilidade
        LEFT JOIN pacientes p ON a.id_paciente = p.id
        ORDER BY d.data_hora ASC
    ";
    
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $eventos = [];

    foreach ($results as $row) {
        $evento_data = [
            'id'    => $row['id'],
            'start' => $row['data_hora'], 
            'allDay' => false, 
            'extendedProps' => [          
                'status' => $row['status']
            ]
        ];

        // A distinção entre admin e paciente é feita APENAS aqui, ao montar a resposta:
        // Se for admin E o horário estiver indisponível, adiciona o nome do paciente.
        if ($is_admin && $row['status'] == 'indisponivel' && isset($row['nome_paciente'])) {
            $evento_data['extendedProps']['nome_paciente'] = $row['nome_paciente'];
        }

        $eventos[] = $evento_data;
    }
    
    echo json_encode($eventos);

} catch (PDOException $e) {
    http_response_code(500); 
    error_log("Erro em disponibilidade.php: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao carregar os horários.']);
}
?>