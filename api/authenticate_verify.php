<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$credential = $input['credential'] ?? null;

if (!$credential) {
    echo json_encode(['error' => 'Credencial não fornecida']);
    exit;
}

try {
    $credential_id = base64UrlEncode(base64_decode($credential['id']));
    
    $stmt = $pdo->prepare("SELECT * FROM biometria WHERE credencial_id = ?");
    $stmt->execute([$credential_id]);
    $biometria = $stmt->fetch();

    if ($biometria) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$biometria['usuario_id']]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            // Register attendance
            $data = date('Y-m-d');
            $entrada = date('H:i:s');
            
            $stmt = $pdo->prepare("SELECT id FROM presencas WHERE usuario_id = ? AND data = ?");
            $stmt->execute([$usuario['id'], $data]);
            $presenca_existente = $stmt->fetch();

            if (!$presenca_existente) {
                $hora_limite = date('H:i:s', strtotime('08:00:00'));
                $status = ($entrada <= $hora_limite) ? 'presente' : 'atrasado';
                $stmt = $pdo->prepare("INSERT INTO presencas (usuario_id, data, entrada, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$usuario['id'], $data, $entrada, $status]);
            }

            log_acao($pdo, $usuario['id'], 'LOGIN_BIOMETRICO', 'Login via biometria');
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['error' => 'Credencial não reconhecida']);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
?>
