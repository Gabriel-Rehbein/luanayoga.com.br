<?php
require 'conexao.php';

header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);
$email = $dados['email'] ?? '';

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
        $protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
        
        // Pega o host
        $host = $_SERVER['HTTP_HOST'];
        
        // Monta a URL base do frontend.
        // Na Hostinger, o frontend está na raiz. No seu XAMPP, está dentro de /fullstackLuanaMoreira/frontend/
        // Para testes locais, você pode precisar ajustar para:
        // $baseUrl = "{$protocolo}://{$host}/fullstackLuanaMoreira/frontend";
        $baseUrl = "{$protocolo}://{$host}";

        $linkDeRecuperacao = $baseUrl . "/paginas/redefinir_senha.html?token=" . $token;
        
        // --- LÓGICA DE ENVIO DE E-MAIL ---
        $assunto = "Recuperação de Senha - Luana Moreira Fisioterapia";
        $corpo = "Olá!\n\nVocê solicitou a redefinição de sua senha. Se foi você, clique no link a seguir para criar uma nova senha. O link é válido por 1 hora:\n\n" . $linkDeRecuperacao . "\n\nSe não foi você, por favor, ignore este e-mail.";
        $headers = "From: nao-responda@luanayoga.com.br";
        
        // mail($email, $assunto, $corpo, $headers); // A função de envio real
    }

    // Responde ao frontend com uma mensagem genérica de sucesso por segurança
    echo json_encode(['sucesso' => true, 'mensagem' => 'Se um e-mail correspondente for encontrado em nosso sistema, um link de recuperação foi enviado.']);
    
} catch (PDOException $e) {
    // Para o usuário, a resposta em caso de erro de banco é a mesma por segurança.
    // error_log("Erro no processo de recuperação: " . $e->getMessage());
    echo json_encode(['sucesso' => true, 'mensagem' => 'Se um e-mail correspondente for encontrado em nosso sistema, um link de recuperação foi enviado.']);
    exit;
}
?>