<?php
header("Access-Control-Allow-Origin: http://luanayoga.com.br"); // Permite requisições de http://localhost
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); 
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    http_response_code(200);
    exit(); 
}

// --- GERENCIAMENTO DE SESSÃO ---
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, 
        'path' => '/',
        'secure' => false,
        'httponly' => true, 
        'samesite' => 'Lax' // 'Lax' é um bom padrão para a maioria dos casos
    ]);
    session_start();
}

// --- CONEXÃO COM O BANCO DE DADOS ---
$host = 'mysql.hostinger.com';
$user = 'u426680106_LuanaMoreira';
$pass = 'LmBD123@';
$db   = 'u426680106_luana_moreira';
$charset = 'utf8mb4'; // <- Faltava isso aqui!


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    // Para depuração: error_log("Erro de conexão PDO: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>