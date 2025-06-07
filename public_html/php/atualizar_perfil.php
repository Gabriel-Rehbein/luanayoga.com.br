<?php
// backend/php/atualizar_perfil.php
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['paciente_id'])) {
    http_response_code(403);
    echo json_encode(['mensagem' => 'Acesso negado.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$id_admin = $_SESSION['paciente_id'];

$nome = $dados['nome'];
$email = $dados['email'];
$senha = $dados['senha'];

// Lógica para atualizar a senha apenas se uma nova for fornecida
if (!empty($senha)) {
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE pacientes SET nome = ?, email = ?, senha = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $senha_hash, $id_admin]);
} else {
    $stmt = $pdo->prepare("UPDATE pacientes SET nome = ?, email = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $id_admin]);
}

echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!']);
?>