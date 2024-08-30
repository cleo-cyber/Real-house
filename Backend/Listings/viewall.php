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

try {
    // Fetch all listings
    $sql = "SELECT * FROM listings";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($listings)) {
        sendResponse(false, 'No listings found');
    }

    // Decode and fetch additional data for each listing
    foreach ($listings as &$listing) {
        $houseTypeSql = "SELECT house_type FROM house_type WHERE housetype_id = :HouseType";
        $houseTypeStmt = $db->prepare($houseTypeSql);
        $houseTypeStmt->execute(['HouseType' => $listing['HouseType']]);
        $listing['house_type'] = $houseTypeStmt->fetchColumn();

        $statusSql = "SELECT House_status FROM status WHERE Status_id = :Status_id";
        $statusStmt = $db->prepare($statusSql);
        $statusStmt->execute(['Status_id' => $listing['Status_id']]);
        $listing['status'] = $statusStmt->fetchColumn();

        $fileUrls = json_decode($listing['file_url'], true);
        $listing['file_url'] = $fileUrls[0] ?? ''; // Ensure there's at least one file URL
    }

    sendResponse(true, 'Listings found', $listings);

} catch (PDOException $e) {
    sendResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage());
}
