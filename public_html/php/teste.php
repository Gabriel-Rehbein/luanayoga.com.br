<?php
// Inicia o teste com uma tela limpa
echo "<h1>Diagnóstico do Servidor</h1>";

// === TESTE 1: SESSÕES ===
echo "<h2>1. Teste de Sessão</h2>";

// Tenta iniciar uma sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tenta gravar um valor na sessão
$_SESSION['teste_sbc'] = 'funciona';

// Verifica se o valor foi realmente gravado
if (isset($_SESSION['teste_sbc']) && $_SESSION['teste_sbc'] === 'funciona') {
    echo "<p style='color:green; font-weight:bold;'>SUCESSO: As sessões estão a funcionar corretamente no seu servidor.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>FALHA: As sessões NÃO estão a funcionar. Esta é a causa provável do seu problema de login.</p>";
    echo "<p>Isso geralmente ocorre porque o PHP não tem permissão para escrever na pasta de sessões do servidor. Verifique as configurações do 'session.save_path' no seu arquivo php.ini.</p>";
}
// Limpa a sessão de teste
session_destroy();
echo "<hr>";


// === TESTE 2: BANCO DE DADOS ===
echo "<h2>2. Teste de Banco de Dados</h2>";

try {
    // Inclui o mesmo arquivo de conexão que a sua aplicação usa
    require 'conexao.php';
    echo "<p style='color:green; font-weight:bold;'>SUCESSO: O arquivo 'conexao.php' foi carregado sem erros.</p>";

    // Tenta fazer uma consulta simples
    $stmt = $pdo->query("SELECT COUNT(*) FROM pacientes");
    $total_pacientes = $stmt->fetchColumn();

    echo "<p style='color:green; font-weight:bold;'>SUCESSO: A conexão com o banco de dados '{$db}' foi bem-sucedida.</p>";
    echo "<p>Total de pacientes encontrados na tabela: <strong>{$total_pacientes}</strong></p>";

} catch (PDOException $e) {
    echo "<p style='color:red; font-weight:bold;'>FALHA: Não foi possível conectar ao banco de dados.</p>";
    echo "<p><strong>Mensagem de Erro do PHP:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Verifique se o nome do banco ('{$db}'), usuário ('{$user}') e senha no arquivo 'conexao.php' estão corretos e se o seu servidor de banco de dados (MySQL/MariaDB) está a ser executado.</p>";
} catch (Throwable $t) {
    echo "<p style='color:red; font-weight:bold;'>FALHA: Ocorreu um erro geral ao tentar conectar.</p>";
    echo "<p><strong>Mensagem de Erro do PHP:</strong> " . $t->getMessage() . "</p>";
}

?>