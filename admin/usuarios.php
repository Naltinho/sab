<?php
require_once '../config/database.php';
session_start();

// Simulação de login
$_SESSION['usuario_nome'] = $_SESSION['usuario_nome'] ?? 'Administrador';
$_SESSION['usuario_tipo'] = $_SESSION['usuario_tipo'] ?? 'administrador';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add super admin
    if (isset($_POST['add_super_admin'])) {
        $senha_hash = password_hash('Naltinho', PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, numero_identificacao, tipo, senha, pin) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['Super Administrador', '935956775', 'administrador', $senha_hash, '12345678']);
            $success = "Super administrador adicionado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao adicionar super administrador: " . $e->getMessage();
        }
    }
    
    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$user_id]);
            $success = "Usuário removido com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao remover usuário: " . $e->getMessage();
        }
    }
    
    // Edit user
    if (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $nome = $_POST['nome'];
        $numero_identificacao = $_POST['numero_identificacao'];
        $tipo = $_POST['tipo'];
        
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, numero_identificacao = ?, tipo = ? WHERE id = ?");
            $stmt->execute([$nome, $numero_identificacao, $tipo, $user_id]);
            $success = "Usuário atualizado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar usuário: " . $e->getMessage();
        }
    }
}

// Buscar usuários no banco de dados com status biométrico
$stmt = $pdo->query("
    SELECT u.*, 
           CASE WHEN b.id IS NOT NULL THEN 1 ELSE 0 END as has_biometric
    FROM usuarios u
    LEFT JOIN biometria b ON u.id = b.usuario_id
    GROUP BY u.id
    ORDER BY u.id DESC
");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - SAB IPPEK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght300;400;500;600;700&family=Orbitron:wght400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="light-theme">

<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <div class="page-title">
            <h1 style="font-size: 1.5rem; font-weight: 600;">Gestão de Usuários</h1>
            <p style="color: var(--gray-600); font-size: 0.9rem;">Gerencie alunos, funcionários e administradores</p>
        </div>
        
        <div class="user-profile" style="display: flex; gap:10px;">
            <form method="POST" style="display: inline;">
                <button type="submit" name="add_super_admin" class="btn-primary" style="padding: 10px 20px; background: var(--secondary-blue);">
                    <i class="fas fa-shield-alt"></i> Adicionar Super Admin
                </button>
            </form>
            <button class="btn-primary" id="add-user-btn" style="padding: 10px 20px;">
                <i class="fas fa-user-plus"></i> Novo Usuário
            </button>
        </div>
    </header>

    <?php if (isset($success)): ?>
        <div class="alert" style="background: rgba(40,167,69,0.1); color: var(--success); padding:15px; border-radius:10px; margin-bottom:20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert" style="background: rgba(220,53,69,0.1); color: var(--danger); padding:15px; border-radius:10px; margin-bottom:20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="chart-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); color: var(--gray-600); font-size: 0.85rem;">
                        <th style="padding: 15px 10px;">ID</th>
                        <th style="padding: 15px 10px;">Nome</th>
                        <th style="padding: 15px 10px;">Identificação</th>
                        <th style="padding: 15px 10px;">Tipo</th>
                        <th style="padding: 15px 10px;">Biometria</th>
                        <th style="padding: 15px 10px;">Ações</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem;">
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: var(--gray-500);">Nenhum usuário cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $user): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 10px;">#<?php echo $user['id']; ?></td>
                            <td style="padding: 15px 10px;"><?php echo htmlspecialchars($user['nome']); ?></td>
                            <td style="padding: 15px 10px;"><?php echo htmlspecialchars($user['numero_identificacao']); ?></td>
                            <td style="padding: 15px 10px;">
                                <span style="padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; background: rgba(0, 74, 173, 0.1); color: var(--primary-blue); text-transform: capitalize;">
                                    <?php echo $user['tipo']; ?>
                                </span>
                            </td>
                            <td style="padding: 15px 10px;">
                                <?php if ($user['has_biometric']): ?>
                                    <span style="padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; background: rgba(40, 167, 69, 0.1); color: var(--success);">
                                        <i class="fas fa-check-circle"></i> Cadastrada
                                    </span>
                                <?php else: ?>
                                    <span style="padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; background: rgba(220, 53, 69, 0.1); color: var(--danger);">
                                        <i class="fas fa-times-circle"></i> Não cadastrada
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px 10px;">
                                <button class="btn-icon" style="color: var(--primary-blue); margin-right: 10px; background: none; border: none; cursor: pointer;" title="Registrar Biometria" onclick="registerBiometric(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nome']); ?>', '<?php echo $user['numero_identificacao']; ?>')">
                                    <i class="fas fa-fingerprint"></i>
                                </button>
                                <button class="btn-icon" style="color: var(--primary-blue); margin-right: 10px; background: none; border: none; cursor: pointer;" title="Editar Usuário" onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nome']); ?>', '<?php echo $user['numero_identificacao']; ?>', '<?php echo $user['tipo']; ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn-icon" style="color: var(--danger); background: none; border: none; cursor: pointer;" onclick="return confirm('Tem certeza que deseja remover este usuário?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal de Cadastro -->
<div id="user-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div class="login-card" style="max-width: 500px; padding: 30px;">
        <h2 style="margin-bottom: 20px;">Cadastrar Novo Usuário</h2>
        <form action="save_user.php" method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nome" required>
                </div>
            </div>
            <div class="form-group">
                <label>Nº Identificação</label>
                <div class="input-icon">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="id_number" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tipo de Usuário</label>
                <select name="tipo" id="tipo-select" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--gray-100);">
                    <option value="aluno">Aluno</option>
                    <option value="funcionario">Funcionário</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <div class="form-group" id="funcionario-cargo-group" style="display: none;">
                <label>Cargo do Funcionário</label>
                <select name="cargo" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--gray-100);">
                    <option value="Professor">Professor</option>
                    <option value="Diretor">Diretor</option>
                    <option value="Secretário">Secretário</option>
                    <option value="Porteiro">Porteiro</option>
                    <option value="Limpeza">Limpeza</option>
                    <option value="Cantina">Cantina</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div style="display: flex; gap: 15px; margin-top: 25px;">
                <button type="button" class="btn-primary" style="background: var(--gray-500);" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Salvar Usuário</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Edição -->
<div id="edit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div class="login-card" style="max-width: 500px; padding: 30px;">
        <h2 style="margin-bottom: 20px;">Editar Usuário</h2>
        <form method="POST" id="edit-form">
            <input type="hidden" name="user_id" id="edit-user-id">
            <div class="form-group">
                <label>Nome Completo</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nome" id="edit-nome" required>
                </div>
            </div>
            <div class="form-group">
                <label>Nº Identificação</label>
                <div class="input-icon">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="numero_identificacao" id="edit-numero" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tipo de Usuário</label>
                <select name="tipo" id="edit-tipo" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 10px; background: var(--gray-100);">
                    <option value="aluno">Aluno</option>
                    <option value="funcionario">Funcionário</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <div style="display: flex; gap: 15px; margin-top: 25px;">
                <button type="button" class="btn-primary" style="background: var(--gray-500);" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" name="edit_user" class="btn-primary">Atualizar Usuário</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('user-modal');
    const btn = document.getElementById('add-user-btn');
    const tipoSelect = document.getElementById('tipo-select');
    const funcionarioCargoGroup = document.getElementById('funcionario-cargo-group');

    btn.onclick = () => modal.style.display = 'flex';
    const closeModal = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target == modal) closeModal(); }
    
    // Show/hide cargo field
    tipoSelect.addEventListener('change', () => {
        if (tipoSelect.value === 'funcionario') {
            funcionarioCargoGroup.style.display = 'block';
        } else {
            funcionarioCargoGroup.style.display = 'none';
        }
    });
    
    // Edit modal
    function openEditModal(id, nome, numero, tipo) {
        document.getElementById('edit-user-id').value = id;
        document.getElementById('edit-nome').value = nome;
        document.getElementById('edit-numero').value = numero;
        document.getElementById('edit-tipo').value = tipo;
        document.getElementById('edit-modal').style.display = 'flex';
    }
    
    function closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }
    
    window.onclick = (e) => {
        if (e.target == modal) closeModal();
        if (e.target == document.getElementById('edit-modal')) closeEditModal();
    }

    // Helper functions for WebAuthn
    function base64UrlDecode(str) {
        str = str.replace(/-/g, '+').replace(/_/g, '/');
        while (str.length % 4) {
            str += '=';
        }
        const binary = atob(str);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes.buffer;
    }

    function base64UrlEncode(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary).replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
    }

    async function registerBiometric(userId, userName, userIdentification) {
        try {
            const response = await fetch('../api/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId, name: userName, identification: userIdentification })
            });
            
            const options = await response.json();
            console.log('Register options:', options);
            
            if (options.error) {
                alert('Erro: ' + options.error);
                return;
            }
            
            // Convert to ArrayBuffer
            options.challenge = base64UrlDecode(options.challenge);
            options.user.id = base64UrlDecode(options.user.id);
            
            const credential = await navigator.credentials.create({ publicKey: options });
            console.log('Credential:', credential);
            
            // Prepare for server
            const credentialForServer = {
                id: credential.id,
                rawId: base64UrlEncode(credential.rawId),
                type: credential.type,
                response: {
                    attestationObject: base64UrlEncode(credential.response.attestationObject),
                    clientDataJSON: base64UrlEncode(credential.response.clientDataJSON)
                }
            };
            
            const verifyResponse = await fetch('../api/register_verify.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: userId,
                    credential: credentialForServer
                })
            });
            
            const result = await verifyResponse.json();
            console.log('Verify result:', result);
            
            if (result.success) {
                alert('Biometria registrada com sucesso!');
                location.reload();
            } else {
                alert('Erro ao registrar biometria: ' + (result.error || 'Desconhecido'));
            }
        } catch (e) {
            console.error(e);
            alert('Erro no processo de registro biométrico: ' + (e.message || 'Desconhecido'));
        }
    }
</script>

</body>
</html>
