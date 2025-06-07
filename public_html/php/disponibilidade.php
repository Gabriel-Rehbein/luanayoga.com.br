<?php
// backend/php/disponibilidade.php (Versão Atualizada para incluir nome do paciente)

require 'conexao.php';
header('Content-Type: application/json');

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

try {
    // A consulta SQL agora é diferente APENAS para o admin
    if ($is_admin) {
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
    } else {
        // Para pacientes, a consulta continua a mesma: só mostra o que está disponível.
        $sql = "SELECT id, data_hora, status FROM disponibilidade WHERE status = 'disponivel' ORDER BY data_hora ASC";
    }
    
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

        // Se for admin e houver um nome de paciente, adiciona ao evento
        if ($is_admin && isset($row['nome_paciente'])) {
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