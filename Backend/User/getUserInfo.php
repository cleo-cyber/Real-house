<?php
require_once '../config.php';

// Set header to return JSON content
header("Content-Type: application/json");

// Function to send JSON response
function sendResponse($status, $message, $data = null) {
    echo json_encode(array('status' => $status, 'message' => $message, 'data' => $data));
    exit();
}

// Get authorization header
$authHeader = apache_request_headers()['Authorization'] ?? '';

if (empty($authHeader)) {
    sendResponse(false, 'Authorization header missing');
}

// Decode authorization header
$decodeHeader = base64_decode($authHeader);
$parts = explode('|', $decodeHeader);

if (count($parts) !== 4) {
    sendResponse(false, 'Invalid authorization header');
}

list($randomString, $expire_date, $role, $usr) = $parts;

// Check if token has expired
if ($expire_date < time()) {
    sendResponse(false, 'Authorization header expired');
}

// Check if request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(false, 'Unauthorized access');
}

// Get the user ID from query parameter
$userId = "SELECT user_id FROM user WHERE email = :usr";
$stmt = $db->prepare($userId);
$stmt->execute(['usr' => $usr]);
$userId = $stmt->fetchColumn();


if (!$userId) {
    sendResponse(false, 'User ID is missing');
}

try {
    // Fetch user details
    $sql = "SELECT * FROM user WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sendResponse(false, 'User not found');
    }

    sendResponse(true, 'User found', $user);

} catch (PDOException $e) {
    sendResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage());
}
