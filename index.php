<?php
require_once 'config/database.php';
session_start();

// Redirecionar se já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: admin/dashboard.php');
    exit();
}

include 'includes/header.php';
?>

<div class="login-container">
    <button id="theme-toggle" class="theme-toggle" title="Alternar tema">
        <i class="fas fa-moon"></i>
    </button>

    <div class="login-card">
        <div class="logo">
            <i class="fas fa-fingerprint fa-3x" style="color: var(--primary-blue); margin-bottom: 15px;"></i>
        </div>
        <h1>SAB IPPEK</h1>
        <p>Sistema de Autenticação Biométrica</p>

        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="biometric">Biometria</button>
            <button class="auth-tab" data-tab="pin">PIN</button>
            <button class="auth-tab" data-tab="password">Senha</button>
        </div>

        <!-- Biometric Login Form -->
        <div id="biometric-form" class="auth-form active">
            <div class="biometric-scanner" id="scanner-btn">
                <i class="fas fa-fingerprint"></i>
                <div class="scanner-line"></div>
            </div>
            <p id="scanner-status" style="margin-top: 15px; font-weight: 500;">Toque no sensor para autenticar</p>
            <button class="btn-primary" style="margin-top: 20px;" id="simular-biometria">
                <i class="fas fa-vial"></i> Simular Biometria
            </button>
        </div>

        <!-- PIN Login Form -->
        <div id="pin-form" class="auth-form">
            <form id="login-pin" action="includes/auth.php" method="POST">
                <input type="hidden" name="type" value="pin">
                <div class="form-group">
                    <label>Número de Identificação</label>
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="id_number" placeholder="Ex: ADM001" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>PIN de 8 dígitos</label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" name="pin" maxlength="8" pattern="\d{8}" placeholder="********" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
        </div>

        <!-- Password Login Form -->
        <div id="password-form" class="auth-form">
            <form id="login-password" action="includes/auth.php" method="POST">
                <input type="hidden" name="type" value="password">
                <div class="form-group">
                    <label>Número de Identificação</label>
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="id_number" placeholder="Ex: ADM001" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Sua senha" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
        </div>

        <div id="login-feedback" style="margin-top: 20px; display: none;">
            <p class="alert alert-danger" style="color: var(--danger); font-size: 0.9rem;"></p>
        </div>

        <div class="login-footer" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <p style="font-size: 0.8rem;">Colégio IPPEK – Soyo &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
