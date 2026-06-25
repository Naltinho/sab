<?php
require_once '../config/database.php';
session_start();

$_SESSION['usuario_nome'] = $_SESSION['usuario_nome'] ?? 'Administrador';
$_SESSION['usuario_tipo'] = $_SESSION['usuario_tipo'] ?? 'administrador';

// Get data for reports
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT p.*, u.nome, u.numero_identificacao, u.tipo FROM presencas p JOIN usuarios u ON p.usuario_id = u.id WHERE p.data = ? ORDER BY p.id DESC");
$stmt->execute([$today]);
$presencas_hoje = $stmt->fetchAll();

// Weekly data (last 7 days)
$week_ago = date('Y-m-d', strtotime('-7 days'));
$stmt = $pdo->prepare("SELECT p.*, u.nome, u.numero_identificacao, u.tipo FROM presencas p JOIN usuarios u ON p.usuario_id = u.id WHERE p.data BETWEEN ? AND ? ORDER BY p.id DESC");
$stmt->execute([$week_ago, $today]);
$presencas_semana = $stmt->fetchAll();

// Monthly data (current month)
$month_start = date('Y-m-01');
$stmt = $pdo->prepare("SELECT p.*, u.nome, u.numero_identificacao, u.tipo FROM presencas p JOIN usuarios u ON p.usuario_id = u.id WHERE p.data BETWEEN ? AND ? ORDER BY p.id DESC");
$stmt->execute([$month_start, $today]);
$presencas_mes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - SAB IPPEK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght300;400;500;600;700&family=Orbitron:wght400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="light-theme">

<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="page-title">
            <h1 style="font-size: 1.5rem; font-weight: 600;">Relatórios e Estatísticas</h1>
            <p style="color: var(--gray-600); font-size: 0.9rem;">Gere relatórios de presença detalhados</p>
        </div>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Relatório Diário</h3>
                <p>Presenças de hoje (<?php echo date('d/m/Y'); ?>)</p>
                <button class="btn-primary" style="margin-top: 15px; padding: 10px;" onclick="generatePDF('daily')">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </button>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Relatório Semanal</h3>
                <p>Resumo da semana</p>
                <button class="btn-primary" style="margin-top: 15px; padding: 10px;" onclick="generatePDF('weekly')">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </button>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Relatório Mensal</h3>
                <p>Histórico do mês</p>
                <button class="btn-primary" style="margin-top: 15px; padding: 10px;" onclick="generatePDF('monthly')">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Preview of today's data -->
    <div class="chart-card" style="margin-top: 30px;">
        <h2>Prévia: Presenças de Hoje</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); color: var(--gray-600); font-size: 0.85rem;">
                        <th style="padding:10px;">Data</th>
                        <th style="padding:10px;">Nome</th>
                        <th style="padding:10px;">Identificação</th>
                        <th style="padding:10px;">Tipo</th>
                        <th style="padding:10px;">Entrada</th>
                        <th style="padding:10px;">Status</th>
                    </tr>
                </thead>
                <tbody style="font-size:0.9rem;">
                    <?php if (empty($presencas_hoje)): ?>
                        <tr>
                            <td colspan="6" style="padding:20px; text-align:center;">Nenhuma presença registrada hoje.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($presencas_hoje as $p): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:10px;"><?php echo date('d/m/Y', strtotime($p['data'])); ?></td>
                            <td style="padding:10px;"><?php echo htmlspecialchars($p['nome']); ?></td>
                            <td style="padding:10px;"><?php echo htmlspecialchars($p['numero_identificacao']); ?></td>
                            <td style="padding:10px;"><?php echo ucfirst($p['tipo']); ?></td>
                            <td style="padding:10px;"><?php echo $p['entrada']; ?></td>
                            <td style="padding:10px;">
                                <span style="padding:5px 10px; border-radius:20px; font-size:0.75rem; background:<?php echo $p['status'] == 'presente' ? 'rgba(40,167,69,0.1)' : 'rgba(220,53,69,0.1)'; ?>; color:<?php echo $p['status'] == 'presente' ? 'var(--success)' : 'var(--danger)'; ?>;">
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
</main>

<script>
    window.jsPDF = window.jspdf.jsPDF;
    
    // Pass PHP data to JS
    const presencasHoje = <?php echo json_encode($presencas_hoje); ?>;
    const presencasSemana = <?php echo json_encode($presencas_semana); ?>;
    const presencasMes = <?php echo json_encode($presencas_mes); ?>;
    
    function generatePDF(type) {
        const doc = new jsPDF();
        
        let title, data;
        
        switch(type) {
            case 'daily':
                title = 'Relatório Diário de Presenças - ' + new Date().toLocaleDateString('pt-BR');
                data = presencasHoje;
                break;
            case 'weekly':
                title = 'Relatório Semanal de Presenças';
                data = presencasSemana;
                break;
            case 'monthly':
                title = 'Relatório Mensal de Presenças';
                data = presencasMes;
                break;
        }
        
        // Header
        doc.setFontSize(20);
        doc.text('SAB IPPEK', 105, 20, { align: 'center' });
        doc.setFontSize(14);
        doc.text(title, 105, 30, { align: 'center' });
        
        // Table
        let yPos = 40;
        doc.setFontSize(10);
        doc.text('Data', 20, yPos);
        doc.text('Nome', 50, yPos);
        doc.text('Identificação', 120, yPos);
        doc.text('Tipo', 160, yPos);
        doc.text('Entrada', 180, yPos);
        doc.text('Status', 200, yPos);
        
        yPos += 10;
        
        data.forEach((item) => {
            if (yPos > 280) {
                doc.addPage();
                yPos = 20;
            }
            
            const dataFormatada = new Date(item.data).toLocaleDateString('pt-BR');
            doc.text(dataFormatada, 20, yPos);
            doc.text(item.nome.substring(0, 30), 50, yPos);
            doc.text(item.numero_identificacao, 120, yPos);
            doc.text(item.tipo, 160, yPos);
            doc.text(item.entrada, 180, yPos);
            doc.text(item.status, 200, yPos);
            
            yPos += 7;
        });
        
        // Footer
        doc.setFontSize(8);
        doc.text('Relatório gerado em ' + new Date().toLocaleString('pt-BR'), 105, 290, { align: 'center' });
        
        // Download
        doc.save(`relatorio_${type}_${Date.now()}.pdf`);
    }
</script>

</body>
</html>
