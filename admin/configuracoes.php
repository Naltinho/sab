<?php
require_once '../config/database.php';
session_start();

$_SESSION['usuario_nome'] = $_SESSION['usuario_nome'] ?? 'Administrador';
$_SESSION['usuario_tipo'] = $_SESSION['usuario_tipo'] ?? 'administrador';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - SAB IPPEK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="light-theme">

<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="page-title">
            <h1 style="font-size: 1.5rem; font-weight: 600;">Configurações do Sistema</h1>
            <p style="color: var(--gray-600); font-size: 0.9rem;">Gerencie as preferências e segurança</p>
        </div>
    </header>

    <div class="chart-card" style="max-width: 600px;">
        <h2 style="margin-bottom: 25px;">Perfil de Administrador</h2>
        <form>
            <div class="form-group">
                <label>Nome do Colégio</label>
                <div class="input-icon">
                    <i class="fas fa-school"></i>
                    <input type="text" value="Colégio IPPEK – Soyo">
                </div>
            </div>
            <div class="form-group">
                <label>Horário Limite de Entrada (Sem Atraso)</label>
                <div class="input-icon">
                    <i class="fas fa-clock"></i>
                    <input type="time" value="08:00">
                </div>
            </div>
            <div class="form-group">
                <label>Notificações de Atraso</label>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                    <input type="checkbox" id="notify" checked style="width: 20px; height: 20px;">
                    <label for="notify" style="margin-bottom: 0;">Enviar alertas para o administrador</label>
                </div>
            </div>
            <button class="btn-primary" style="margin-top: 25px; padding: 12px 25px; width: auto;">Salvar Alterações</button>
        </form>
    </div>
</main>

</body>
</html>
