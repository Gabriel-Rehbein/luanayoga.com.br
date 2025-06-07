<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["sucesso" => false, "mensagem" => "Não autorizado"]);
  exit;
}
require 'conexao.php';
$data = json_decode(file_get_contents("php://input"), true)["data"];
$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, data, status, pago) VALUES (?, ?, 'agendado', 0)");
$ok = $stmt->execute([$usuario_id, $data]);
echo json_encode(["sucesso" => $ok, "mensagem" => $ok ? "Agendamento realizado com sucesso!" : "Erro ao agendar."]);
?>