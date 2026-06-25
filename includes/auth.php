<?php
require_once '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $id_number = $_POST['id_number'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE numero_identificacao = ?");
        $stmt->execute([$id_number]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            header('Location: ../index.php?error=usuario_nao_encontrado');
            exit();
        }

        if ($type === 'password') {
            $password = $_POST['password'] ?? '';
            if (password_verify($password, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                
                log_acao($pdo, $usuario['id'], 'LOGIN_SENHA', 'Login via senha');
                
                header('Location: ../admin/dashboard.php');
                exit();
            } else {
                header('Location: ../index.php?error=senha_incorreta');
                exit();
            }
        } elseif ($type === 'pin') {
            $pin = $_POST['pin'] ?? '';
            if ($pin === $usuario['pin']) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                
                log_acao($pdo, $usuario['id'], 'LOGIN_PIN', 'Login via PIN');
                
                header('Location: ../admin/dashboard.php');
                exit();
            } else {
                header('Location: ../index.php?error=pin_incorreto');
                exit();
            }
        }
    } catch (PDOException $e) {
        header('Location: ../index.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}
header('Location: ../index.php');
exit();
?>
