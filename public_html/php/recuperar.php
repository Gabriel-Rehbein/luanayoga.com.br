<?php
require 'conexao.php';
// Use as próximas duas linhas caso precise depurar o envio de e-mail no futuro
// require '../vendor/autoload.php'; 
// use PHPMailer\PHPMailer\PHPMailer;

// Define que a resposta será sempre JSON
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);
$email = $dados['email'] ?? '';

// Validação do e-mail recebido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); 
    echo json_encode(['sucesso' => false, 'mensagem' => 'Formato de e-mail inválido.']);
    exit;
}

try {
    // Verifica se o e-mail existe na tabela `pacientes`
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE email = ?");
    $stmt->execute([$email]);
    $paciente = $stmt->fetch();
    
    // Se o e-mail existir, gera o token e prepara o e-mail
    if ($paciente) {
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);

        $linkDeRecuperacao = "http://localhost/https-luanayoga.com.br-/paginas/redefinir_senha.html?token=" . $token;
        
        // --- LÓGICA DE ENVIO DE E-MAIL (Usando mail() básico como placeholder) ---
        // Lembre-se que o ideal aqui é usar PHPMailer para garantir a entrega
        $assunto = "Recuperação de Senha - Luana Moreira Fisioterapia";
        $corpo = "Olá! Você solicitou a redefinição de sua senha. Clique no link a seguir para continuar: " . $linkDeRecuperacao;
        $headers = "From: nao-responda@luanamoreira.com";
        // mail($email, $assunto, $corpo, $headers); // A função de envio real
    }

    // IMPORTANTE: enviar uma resposta de SUCESSO e genérica para o frontend, boa prática de segurança.
    echo json_encode(['sucesso' => true, 'mensagem' => 'Se um e-mail correspondente for encontrado em nosso sistema, um link de recuperação foi enviado.']);
    
} catch (PDOException $e) {
    // Em caso de erro de banco, não informe o erro detalhado ao usuário.
    // Opcional: error_log("Erro no processo de recuperação: " . $e->getMessage());
    // Ainda assim, enviar uma resposta genérica de sucesso para o frontend para não dar pistas.
    echo json_encode(['sucesso' => true, 'mensagem' => 'Se um e-mail correspondente for encontrado em nosso sistema, um link de recuperação foi enviado.']);
    exit;
}
?>