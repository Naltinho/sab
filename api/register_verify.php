<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['user_id'] ?? null;
$credential = $input['credential'] ?? null;

if (!$user_id || !$credential) {
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

try {
    // Store credential info
    $credential_id = base64UrlEncode(base64_decode($credential['id']));
    $public_key = isset($credential['response']['publicKey']) 
        ? base64_encode($credential['response']['publicKey']) 
        : '';
    
    // Delete existing credential for user
    $stmt = $pdo->prepare("DELETE FROM biometria WHERE usuario_id = ?");
    $stmt->execute([$user_id]);
    
    // Insert new
    $stmt = $pdo->prepare("INSERT INTO biometria (usuario_id, credencial_id, chave_publica) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $credential_id, $public_key]);
    
    log_acao($pdo, $user_id, 'BIOMETRIA_REGISTRADA', 'Biometria registrada com sucesso');
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro no banco: ' . $e->getMessage()]);
}

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
?>
