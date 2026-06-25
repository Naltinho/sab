<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

// Generate challenge
$challenge = random_bytes(32);
$_SESSION['webauthn_auth_challenge'] = base64_encode($challenge);

// Get all credentials
$stmt = $pdo->query("SELECT credencial_id FROM biometria");
$credentials = $stmt->fetchAll(PDO::FETCH_COLUMN);

$allow_credentials = [];
foreach ($credentials as $cid) {
    $allow_credentials[] = [
        'type' => 'public-key',
        'id' => $cid
    ];
}

$options = [
    'challenge' => base64UrlEncode($challenge),
    'allowCredentials' => $allow_credentials,
    'userVerification' => 'preferred',
    'timeout' => 60000
];

echo json_encode($options);

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
?>
