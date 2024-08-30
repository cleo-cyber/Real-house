<?php
require_once '../config.php';
$authHeader = apache_request_headers()['Authorization'];

// users report downloaded by the admin

function get_users($db, $user_id) {
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
        $sql = "SELECT FirstName, LastName, Email, date_created, role FROM user";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {    
        $resp=array('status'=>false, 'message'=>'You are not authorized to download this report');
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

$users = get_users($db, $user_id);

if (empty($users)) {
    $resp = array('status' => false, 'message' => 'No users found');
    echo json_encode($resp);
    exit;
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="users.csv"');
    
    $fp = fopen('php://output', 'w');
    fputcsv($fp, array('FirstName', 'LastName', 'Email', 'date_created', 'role'));
    foreach ($users as $user) {
        fputcsv($fp, $user);
    }
    fclose($fp);
}

?>


