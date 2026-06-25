<?php
require_once '../config/database.php';
session_start();

// Simulação de login
$_SESSION['usuario_nome'] = $_SESSION['usuario_nome'] ?? 'Administrador';
$_SESSION['usuario_tipo'] = $_SESSION['usuario_tipo'] ?? 'administrador';

// Buscar presenças no banco de dados
$stmt = $pdo->query("SELECT p.*, u.nome, u.numero_identificacao, u.tipo FROM presencas p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC");
$presencas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controlo de Presenças - SAB IPPEK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="light-theme">

<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="page-title">
            <h1 style="font-size: 1.5rem; font-weight: 600;">Controlo de Presenças</h1>
            <p style="color: var(--gray-600); font-size: 0.9rem;">Histórico completo de acessos</p>
        </div>
        
        <div class="user-profile">
            <button class="btn-primary" style="padding: 10px 20px; background: var(--success);">
                <i class="fas fa-file-excel"></i> Exportar Relatório
            </button>
        </div>
    </header>

    <div class="chart-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); color: var(--gray-600); font-size: 0.85rem;">
                        <th style="padding: 15px 10px;">Data</th>
                        <th style="padding: 15px 10px;">Usuário</th>
                        <th style="padding: 15px 10px;">Identificação</th>
                        <th style="padding: 15px 10px;">Entrada</th>
                        <th style="padding: 15px 10px;">Saída</th>
                        <th style="padding: 15px 10px;">Status</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem;">
                    <?php if (empty($presencas)): ?>
                        <!-- Mock data for demonstration if DB is empty -->
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 10px;"><?php echo date('d/m/Y'); ?></td>
                            <td style="padding: 15px 10px;">João Paulo Simão</td>
                            <td style="padding: 15px 10px;">ALU2024-085</td>
                            <td style="padding: 15px 10px;">07:45:22</td>
                            <td style="padding: 15px 10px;">12:30:15</td>
                            <td style="padding: 15px 10px;"><span style="padding: 5px 10px; border-radius: 20px; background: rgba(40, 167, 69, 0.1); color: var(--success); font-size: 0.75rem;">No Horário</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 10px;"><?php echo date('d/m/Y'); ?></td>
                            <td style="padding: 15px 10px;">Maria Antónia</td>
                            <td style="padding: 15px 10px;">FUN2022-012</td>
                            <td style="padding: 15px 10px;">07:58:10</td>
                            <td style="padding: 15px 10px;">16:00:00</td>
                            <td style="padding: 15px 10px;"><span style="padding: 5px 10px; border-radius: 20px; background: rgba(40, 167, 69, 0.1); color: var(--success); font-size: 0.75rem;">No Horário</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 10px;"><?php echo date('d/m/Y'); ?></td>
                            <td style="padding: 15px 10px;">Carlos Manuel</td>
                            <td style="padding: 15px 10px;">ALU2024-156</td>
                            <td style="padding: 15px 10px;">08:15:44</td>
                            <td style="padding: 15px 10px;">--:--:--</td>
                            <td style="padding: 15px 10px;"><span style="padding: 5px 10px; border-radius: 20px; background: rgba(220, 53, 69, 0.1); color: var(--danger); font-size: 0.75rem;">Atrasado</span></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($presencas as $p): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 10px;"><?php echo date('d/m/Y', strtotime($p['data'])); ?></td>
                            <td style="padding: 15px 10px;"><?php echo htmlspecialchars($p['nome']); ?></td>
                            <td style="padding: 15px 10px;"><?php echo htmlspecialchars($p['numero_identificacao']); ?></td>
                            <td style="padding: 15px 10px;"><?php echo $p['entrada']; ?></td>
                            <td style="padding: 15px 10px;"><?php echo $p['saida'] ?: '--:--:--'; ?></td>
                            <td style="padding: 15px 10px;">
                                <span style="padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; background: rgba(40, 167, 69, 0.1); color: var(--success); text-transform: capitalize;">
                                    <?php echo $p['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>
