<?php
// --- 1. CONTROLE DE ACESSO (CORS) DINÂMICO ---
// Esta lógica permite que o script funcione tanto no localhost quanto no site da Hostinger.
$permitirOrigem = '';
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Lista de origens que têm permissão para acessar o backend
    $origensPermitidas = [
        'http://localhost',             // Para acesso via XAMPP
        'http://127.0.0.1:5500',        // Para o Live Server do VS Code
        'https://luanayoga.com.br'      // Seu domínio de produção (com HTTPS)
        // Adicione 'http://luanayoga.com.br' se você não força HTTPS
    ];

    if (in_array($_SERVER['HTTP_ORIGIN'], $origensPermitidas)) {
        $permitirOrigem = $_SERVER['HTTP_ORIGIN'];
    }
}

header("Access-Control-Allow-Origin: " . $permitirOrigem);
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// --- 2. TRATAMENTO DE REQUISIÇÕES OPTIONS (pre-flight) ---
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

// --- 3. GERENCIAMENTO DE SESSÃO SEGURO E DINÂMICO ---
if (session_status() === PHP_SESSION_NONE) {
    $is_production = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

    session_set_cookie_params([
        'lifetime' => 86400, // 24 horas
        'path' => '/',
        // Em produção, é bom especificar o domínio. Em localhost, deixar vazio funciona melhor.
        'domain' => $is_production ? '.luanayoga.com.br' : '', 
        // Em produção (HTTPS), o cookie DEVE ser seguro. Em localhost (HTTP), DEVE ser falso.
        'secure' => $is_production, 
        'httponly' => true, 
        'samesite' => 'Lax' // 'Lax' é um padrão moderno e seguro para a maioria dos casos.
    ]);
    session_start();
}

// --- 4. CONEXÃO COM O BANCO DE DADOS (credenciais da Hostinger) ---
$host = 'mysql.hostinger.com';
$user = 'u426680106_LuanaMoreira';
$pass = 'LmBD123@';
$db   = 'u426680106_luana_moreira';
$charset = 'utf8mb4';

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
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão com o banco de dados.']);
    exit;
}
?>