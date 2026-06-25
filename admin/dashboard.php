<?php
require_once '../config/database.php';
session_start();

// Simulação de login
$_SESSION['usuario_nome'] = $_SESSION['usuario_nome'] ?? 'Administrador';
$_SESSION['usuario_tipo'] = $_SESSION['usuario_tipo'] ?? 'administrador';

// Get dynamic data from DB
// Total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_users = $stmt->fetch()['total'];

// Alunos presentes hoje
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM presencas p JOIN usuarios u ON p.usuario_id = u.id WHERE p.data = ? AND u.tipo = 'aluno' AND p.status = 'presente'");
$stmt->execute([$today]);
$alunos_presentes = $stmt->fetch()['total'];

// Funcionários
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'funcionario'");
$total_funcionarios = $stmt->fetch()['total'];

// Atrasos hoje
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM presencas WHERE data = ? AND status = 'atrasado'");
$stmt->execute([$today]);
$atrasos_hoje = $stmt->fetch()['total'];

// Presenças recentes
$stmt = $pdo->query("SELECT p.*, u.nome, u.numero_identificacao, u.tipo FROM presencas p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC LIMIT 5");
$presencas_recentes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SAB IPPEK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght300;400;500;600;700&family=Orbitron:wght400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="light-theme">

<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="page-title">
            <h1 style="font-size: 1.5rem; font-weight: 600;">Painel de Controle</h1>
            <p style="color: var(--gray-600); font-size: 0.9rem;">Bem-vindo ao sistema de controle biométrico do IPPEK</p>
        </div>
        
        <div class="user-profile">
            <button id="theme-toggle-admin" class="theme-toggle" style="position: static; margin-right: 15px;">
                <i class="fas fa-moon"></i>
            </button>
            <div class="user-info text-right">
                <span style="display: block; font-weight: 600;"><?php echo $_SESSION['usuario_nome']; ?></span>
                <span style="display: block; font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase;"><?php echo $_SESSION['usuario_tipo']; ?></span>
            </div>
            <div class="user-avatar">
                <i class="fas fa-user-shield fa-lg" style="color: var(--primary-blue);"></i>
            </div>
        </div>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(0, 74, 173, 0.1); color: var(--primary-blue);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Total Usuários</h3>
                <p><?php echo $total_users; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: var(--success);">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-info">
                <h3>Alunos Presentes</h3>
                <p><?php echo $alunos_presentes; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(0, 174, 239, 0.1); color: var(--secondary-blue);">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-info">
                <h3>Funcionários</h3>
                <p><?php echo $total_funcionarios; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: var(--danger);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>Atrasos Hoje</h3>
                <p><?php echo $atrasos_hoje; ?></p>
            </div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h2>Frequência Semanal</h2>
            <canvas id="weeklyChart"></canvas>
        </div>
        <div class="chart-card">
            <h2>Distribuição de Usuários</h2>
            <canvas id="distributionChart"></canvas>
        </div>
    </div>

    <div class="recent-activity" style="margin-top: 40px;">
        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin-bottom: 0;">Presenças Recentes</h2>
                <a href="presencas.php" style="color: var(--primary-blue); font-size: 0.85rem; text-decoration: none; font-weight: 500;">Ver tudo</a>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--gray-600); font-size: 0.85rem;">
                            <th style="padding: 15px 10px;">Usuário</th>
                            <th style="padding: 15px 10px;">Identificação</th>
                            <th style="padding: 15px 10px;">Tipo</th>
                            <th style="padding: 15px 10px;">Entrada</th>
                            <th style="padding: 15px 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.9rem;">
                        <?php if (empty($presencas_recentes)): ?>
                            <tr>
                                <td colspan="5" style="padding: 30px; text-align: center; color: var(--gray-500);">Nenhuma presença registrada hoje.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($presencas_recentes as $p): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($p['numero_identificacao']); ?></td>
                                <td style="padding: 15px 10px;"><?php echo ucfirst($p['tipo']); ?></td>
                                <td style="padding: 15px 10px;"><?php echo $p['entrada']; ?></td>
                                <td style="padding: 15px 10px;">
                                    <span style="padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; background: <?php echo $p['status'] == 'presente' ? 'rgba(40,167,69,0.1)' : 'rgba(220,53,69,0.1)'; ?>; color: <?php echo $p['status'] == 'presente' ? 'var(--success)' : 'var(--danger)'; ?>;">
                                        <?php echo ucfirst($p['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle-admin');
    const body = document.body;
    const icon = themeToggle?.querySelector('i');

    themeToggle?.addEventListener('click', () => {
        body.classList.toggle('dark-theme');
        if (icon) {
            if (body.classList.contains('dark-theme')) {
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        }
    });

    // Charts Initialization
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
            datasets: [{
                label: 'Presenças',
                data: [850, 920, 880, 942, 910, 450],
                borderColor: '#004AAD',
                backgroundColor: 'rgba(0, 74, 173, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Alunos', 'Funcionários', 'Admin'],
            datasets: [{
                data: [1150, 120, 14],
                backgroundColor: ['#004AAD', '#00AEEF', '#002D6B'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>
