<?php
header("Access-Control-Allow-Origin: https://luanayoga.com.br");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Responde à requisição OPTIONS imediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['paciente_id'])) {
        echo json_encode([
            'logado' => true,
            'admin' => isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true,
            'paciente_id' => $_SESSION['paciente_id'],
            'nome' => $_SESSION['paciente_nome'] ?? 'Nome não encontrado'
        ]);
    } else {
        echo json_encode(['logado' => false]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email_do_formulario = $input['email'] ?? '';
    $senha_do_formulario = isset($input['senha']) ? trim($input['senha']) : '';

    if (empty($email_do_formulario) || empty($senha_do_formulario)) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail e senha são obrigatórios.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, senha, admin FROM pacientes WHERE email = ?");
        $stmt->execute([$email_do_formulario]);
        $paciente = $stmt->fetch();

        if ($paciente && password_verify($senha_do_formulario, $paciente['senha'])) {
            $_SESSION['paciente_id'] = $paciente['id'];
            $_SESSION['paciente_nome'] = $paciente['nome'];
            $_SESSION['is_admin'] = (bool)$paciente['admin'];

            echo json_encode([
                'sucesso' => true,
                'nome' => $paciente['nome'],
                'admin' => $_SESSION['is_admin']
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $paciente
                    ? 'E-mail ou senha inválidos. (Senha não confere)'
                    : 'E-mail ou senha inválidos. (Usuário não encontrado)'
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor ao tentar fazer login.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
