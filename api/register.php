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
$name = $input['name'] ?? '';
$identification = $input['identification'] ?? '';

if (!$user_id) {
    echo json_encode(['error' => 'ID do usuário não fornecido']);
    exit;
}

// Generate challenge
$challenge = random_bytes(32);
$_SESSION['webauthn_challenge'] = base64_encode($challenge);
$_SESSION['webauthn_user_id'] = $user_id;

// WebAuthn options
$options = [
    'challenge' => base64UrlEncode($challenge),
    'rp' => [
        'name' => 'SAB IPPEK'
    ],
    'user' => [
        'id' => base64UrlEncode((string)$user_id),
        'name' => $identification,
        'displayName' => $name
    ],
    'pubKeyCredParams' => [
        ['type' => 'public-key', 'alg' => -7],   // ES256
        ['type' => 'public-key', 'alg' => -257] // RS256
    ],
    'authenticatorSelection' => [
        'authenticatorAttachment' => 'platform',
        'userVerification' => 'preferred',
        'requireResidentKey' => false
    ],
    'timeout' => 60000,
    'attestation' => 'none'
];

echo json_encode($options);

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
?>
