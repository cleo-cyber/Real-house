<?php
require_once '../config.php';
$authHeader = apache_request_headers()['Authorization'];

function get_listings($db, $user_id) {
    $sql = "SELECT role FROM user WHERE User_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $role_id = $stmt->fetchColumn();
    $sql = "SELECT user_role FROM roles WHERE role_id = :role_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':role_id', $role_id);
    $stmt->execute();
    $user_role = $stmt->fetchColumn();

    if ($user_role == 'Admin') {
        $sql = "SELECT Description, HouseType, Amenities, Price, Status_id, title, bedrooms, baths, size FROM listings";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sql = "SELECT Description, HouseType, Amenities, Price, Status_id, title, bedrooms, baths, size FROM listings WHERE User_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $listings;
}

if (empty($authHeader)) {
    $resp = array('status' => false, 'message' => 'Authorization header missing');
    echo json_encode($resp);
    exit;
}

$decodeHeader = base64_decode($authHeader); 
$splitHeader = explode('|', $decodeHeader);
$randomString = $splitHeader[0];
$expire_date = $splitHeader[1];
$role = $splitHeader[2];
$usr = $splitHeader[3];
$sql = "SELECT User_id FROM user WHERE Email = :usr";
$stmt = $db->prepare($sql);
$stmt->execute(['usr' => $usr]);
$user_id = $stmt->fetchColumn();

$listings = get_listings($db, $user_id);

if (empty($listings)) {
    $resp = array('status' => false, 'message' => 'No listings found');
    echo json_encode($resp);
    exit;
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="listings.csv"');
    
    $fp = fopen('php://output', 'w');
    fputcsv($fp, array('Description', 'HouseType', 'Amenities', 'Price', 'Status_id', 'title', 'bedrooms', 'baths', 'size'));
    foreach ($listings as $listing) {
        fputcsv($fp, $listing);
    }
    fclose($fp);
    exit;
}
?>
