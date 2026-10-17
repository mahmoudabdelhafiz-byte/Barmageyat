<?php
header('Content-Type: application/json');

$from = $_GET['from'] ?? 'USD';
$to = $_GET['to'] ?? 'EGP';

if ($from === $to) {
    echo json_encode(['rate' => 1]);
    exit;
}

$url = "https://open.er-api.com/v6/latest/" . urlencode($from);
$response = file_get_contents($url);

if (!$response) {
    echo json_encode(['error' => 'Unable to fetch exchange rate']);
    exit;
}

$data = json_decode($response, true);

if (!isset($data['rates'][$to])) {
    echo json_encode(['error' => 'Rate not found']);
    exit;
}

echo json_encode([
    'rate' => $data['rates'][$to],
    'source' => 'open.er-api.com',
    'date' => date('Y-m-d')
]);
?>