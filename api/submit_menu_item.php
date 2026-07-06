<?php
// submit_menu_item.php
// Receives one menu item at a time and appends it to menu_submissions.json,
// grouped by client_name. Copy that JSON file's content whenever you're ready to convert to SQL.

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['client_name']) || empty($data['name']) || !isset($data['price'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'client_name, name, and price are required']);
    exit;
}

$file = __DIR__ . '/menu_submissions.json';

$submissions = [];
if (file_exists($file)) {
    $existing = file_get_contents($file);
    $submissions = json_decode($existing, true) ?: [];
}

$clientKey = $data['client_name'];

if (!isset($submissions[$clientKey])) {
    $submissions[$clientKey] = [];
}

$submissions[$clientKey][] = [
    'category' => $data['category'] ?? null,
    'name' => $data['name'],
    'description' => $data['description'] ?? null,
    'price' => (float) $data['price'],
    'image' => $data['image'] ?? null,
    'tags' => $data['tags'] ?? [],
    'badge' => $data['badge'] ?? null
];

$saved = file_put_contents($file, json_encode($submissions, JSON_PRETTY_PRINT));

if ($saved === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save']);
    exit;
}

echo json_encode(['success' => true]);
