<?php

require 'conexao.php'; 
header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Apenas administradores podem realizar esta ação.']);
    exit;
}

// Pega o método (POST, PUT, DELETE) e os dados JSON enviados pelo JavaScript
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// Usa uma estrutura switch para lidar com cada método
switch ($method) {
    // --- CASO 1: CRIAR um novo horário (método POST) ---
    case 'POST':
        // Validação simples para garantir que os dados necessários foram enviados
        if (!isset($data['data_hora'], $data['status'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos para criar horário.']);
            exit;
        }

        try {
            // Usa a tabela e colunas corretas: `disponibilidade` e `data_hora`
            $stmt = $pdo->prepare("INSERT INTO disponibilidade (data_hora, status) VALUES (?, ?)");
            $stmt->execute([$data['data_hora'], $data['status']]);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Disponibilidade salva com sucesso!']);
        } catch (PDOException $e) {
            http_response_code(500); // Erro no servidor
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar disponibilidade.']);
        }
        break;

    // --- CASO 2: ATUALIZAR um horário existente (método PUT) ---
    case 'PUT':
        if (!isset($data['id'], $data['status'])) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos para atualizar horário.']);
            exit;
        }

        try {
            // Atualiza apenas o status do horário com o ID correspondente
            $stmt = $pdo->prepare("UPDATE disponibilidade SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['id']]);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Disponibilidade atualizada com sucesso!']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar disponibilidade.']);
        }
        break;

    // --- CASO 3: EXCLUIR um horário existente (método DELETE) ---
    case 'DELETE':
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID do horário não fornecido para exclusão.']);
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM disponibilidade WHERE id = ?");
            $stmt->execute([$data['id']]);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Disponibilidade excluída com sucesso!']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir disponibilidade.']);
        }
        break;

    // --- CASO 4: Se o método não for POST, PUT ou DELETE ---
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
        break;
}
?>