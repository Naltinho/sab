<?php
require_once '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $id_number = $_POST['id_number'];
    $tipo = $_POST['tipo'];
    
    // Senha padrão para novos usuários (123456)
    $senha = password_hash('123456', PASSWORD_DEFAULT);
    $pin = '12345678';

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, numero_identificacao, tipo, senha, pin) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $id_number, $tipo, $senha, $pin]);
        
        log_acao($pdo, $_SESSION['usuario_id'] ?? null, 'CADASTRO_USUARIO', "Cadastrou o usuário: $nome ($id_number)");
        
        header('Location: usuarios.php?success=1');
    } catch (PDOException $e) {
        header('Location: usuarios.php?error=' . urlencode($e->getMessage()));
    }
}
?>
