<?php
require 'conexao.php'; 
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $senha = $input['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        http_response_code(400); 
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail e senha são obrigatórios.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, senha, admin FROM pacientes WHERE email = ?");
        $stmt->execute([$email]);
        $paciente = $stmt->fetch();

        if ($paciente && password_verify($senha, $paciente['senha'])) {
            
            $_SESSION['paciente_id'] = $paciente['id'];
            $_SESSION['paciente_nome'] = $paciente['nome'];
            $_SESSION['is_admin'] = (bool)$paciente['admin'];

            echo json_encode([
                'sucesso' => true,
                'nome' => $paciente['nome'],
                'admin' => $_SESSION['is_admin']
            ]);

        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail ou senha inválidos.']);
        }
    } catch (PDOException $e) {
        http_response_code(500); // Erro no servidor
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor, tente novamente mais tarde.']);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['paciente_id'])) {
        echo json_encode([
            'logado' => true,
            'admin' => isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true,
            'paciente_id' => $_SESSION['paciente_id']
        ]);
    } else {
        echo json_encode(['logado' => false]);
    }
    exit;
}

http_response_code(405); // Method Not Allowed
echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
?>