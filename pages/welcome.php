<?php
require_once '../config/database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Get today's attendance
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM presencas WHERE usuario_id = ? AND data = ?");
$stmt->execute([$_SESSION['usuario_id'], $today]);
$presenca = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - SAB IPPEK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght300;400;500;600;700&family=Orbitron:wght400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .dark-theme .welcome-card {
            background: var(--card-bg);
        }
        .welcome-icon {
            font-size: 80px;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            margin: 20px 0;
        }
        .status-presente {
            background: rgba(40,167,69,0.1);
            color: var(--success);
        }
        .status-atrasado {
            background: rgba(220,53,69,0.1);
            color: var(--danger);
        }
        .btn-home {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: var(--primary-blue);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-home:hover {
            background: var(--secondary-blue);
        }
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body class="light-theme">
    <button id="theme-toggle" class="theme-toggle" title="Alternar tema">
        <i class="fas fa-moon"></i>
    </button>
    
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="welcome-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 style="margin-bottom: 10px;">Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
            <p style="color: var(--gray-600); font-size: 18px; margin-bottom: 10px;">
                <?php echo ucfirst($usuario['tipo']); ?> | <?php echo htmlspecialchars($usuario['numero_identificacao']); ?>
            </p>
            
            <?php if ($presenca): ?>
                <div class="status-badge status-<?php echo $presenca['status']; ?>">
                    <i class="fas fa-clock"></i> 
                    Entrada registrada às <?php echo date('H:i', strtotime($presenca['entrada'])); ?>
                    - <?php echo ucfirst($presenca['status']); ?>
                </div>
            <?php else: ?>
                <div class="status-badge status-presente">
                    <i class="fas fa-clock"></i> 
                    Presença registrada com sucesso!
                </div>
            <?php endif; ?>
            
            <p style="margin-top: 20px; color: var(--gray-600);">
                Data: <?php echo date('d/m/Y', strtotime($today)); ?>
            </p>
            
            <a href="../index.php" class="btn-home">
                <i class="fas fa-home"></i> Voltar ao início
            </a>
        </div>
    </div>
    
    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const icon = themeToggle.querySelector('i');
            if (body.classList.contains('dark-theme')) {
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        });
    </script>
</body>
</html>
