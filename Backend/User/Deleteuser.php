<?php
require_once '../config.php';
require_once '../Functions/user_actions.php';
// Set header to return JSON content
header("Content-Type: application/json");

// Function to send JSON response
function sendResponse($status, $message) {
    echo json_encode(array('status' => $status, 'message' => $message));
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

// Check if request method is DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    sendResponse(false, 'Unauthorized access');
}

// Get the user ID from query parameters
parse_str(file_get_contents("php://input"), $delete_vars);
$userId = $delete_vars['User_id'] ?? null;

if (!$userId) {
    sendResponse(false, 'User ID is missing');
}

try {
    // Prepare SQL statement to delete user
    $sql = "DELETE FROM user WHERE User_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);

    if ($stmt->rowCount() > 0) {
        $action = 'User deleted';
        user_actions($db, $userId, $action);
        sendResponse(true, 'User deleted successfully');
    } else {
        sendResponse(false, 'User not found or already deleted');
    }

} catch (PDOException $e) {
    sendResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage());
}
