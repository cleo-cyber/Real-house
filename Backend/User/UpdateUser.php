<?php
require_once '../config.php';
require_once '../Functions/uploads.php';

$authHeader = apache_request_headers()['Authorization'];
$userId = $_GET['user_id'];

function sendResponse($status, $message)
{
    $resp = ['status' => $status, 'message' => $message];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
}

// Check if user ID and authorization header are present
if (empty($userId)) {
    sendResponse(false, 'User ID is missing');
}
if (empty($authHeader)) {
    sendResponse(false, 'Authorization header missing');
}

// Decode and validate authorization header
$decodeHeader = base64_decode($authHeader);
$parts = explode('|', $decodeHeader);

if (count($parts) !== 4) {
    sendResponse(false, 'Invalid authorization header');
}

list($randomString, $expire_date, $role, $usr) = $parts;

if ($expire_date < time()) {
    sendResponse(false, 'Authorization header expired');
}

// Ensure it's a PUT request
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(false, 'Unauthorized access');
}

// Retrieve JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

// Check required fields
if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
    sendResponse(false, 'All fields are required');
}

// Check if user exists
$sql = "SELECT * FROM user WHERE User_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendResponse(false, 'User not found');
}

// Extract data from JSON
$firstName = $data['first_name'];
$lastName = $data['last_name'];
$email = $data['email'];
$password = $data['password'];

$file_url = ''; // Initialize file URL variable
// Hash password
$password = password_hash($password, PASSWORD_DEFAULT);



// Check if image data is provided
if (isset($data['image']) && !empty($data['image'])) {
    // Handle multiple image upload (if needed)
    $response = upload_multiple($data['image'], 'C:\Users\kadima\Desktop\Codes\RealHouse\RealHouse\assets\uploads\User\\');

    if ($response['status'] == false) {
        sendResponse(false, 'Failed to upload image');
    } else {
        $file_url = json_encode($response['filename']);
    }
}

// Update user information in the database
try {
    $sql = "UPDATE user SET Firstname = :Firstname, LastName = :LastName, Email = :Email, Password = :Password, file_url = :file_url WHERE User_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'Firstname' => $firstName,
        'LastName' => $lastName,
        'Email' => $email,
        'Password' => $password,
        'file_url' => $file_url,
        'user_id' => $userId
    ]);

    if ($stmt->rowCount() > 0) {
        sendResponse(true, 'User updated successfully');
    } else {
        sendResponse(false, 'User not found or already updated');
    }
} catch (PDOException $e) {
    sendResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage());
}
?>
