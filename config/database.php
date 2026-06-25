<?php
// Configurações do Banco de Dados
$host = 'localhost';
$dbname = 'ippek_biometric';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar o modo de busca padrão para array associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Função auxiliar para logs do sistema
function log_acao($pdo, $usuario_id, $acao, $detalhes = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $acao, $detalhes]);
    } catch (PDOException $e) {
        // Silenciosamente ignora erros de log para não quebrar o fluxo principal
    }
}
?>
