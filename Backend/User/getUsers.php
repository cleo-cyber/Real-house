<?php

require_once '../config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

// Get role from Authorization header
$headers = apache_request_headers();
$authHeader = $headers['Authorization'];

if (empty($authHeader)) {
    $resp = array('status' => false, 'message' => 'Authorization header missing');
}else{

$decodeHeader = base64_decode($authHeader);
list($randomString, $expire_date,$role,$usr) = explode('|', $decodeHeader);

if ($expire_date < time()) {
    $resp = array('status' => false, 'message' => 'Session expired');
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}

// Select user_role from role table

$sql = "SELECT user_role FROM roles WHERE role_id = :role";
$stmt = $db->prepare($sql);
$stmt->bindParam(':role', $role);
$stmt->execute();
$user_role = $stmt->fetchColumn();
if ($user_role === false) {

    $resp = array('status' => false, 'message' => 'Error fetching user_role');
} elseif (!$user_role) {

    $resp = array('status' => false, 'message' => 'User role does not exist');
} else {
    $role = $user_role;
}
try{
    if ($role != 'Admin') {
        $resp = array('status' => false, 'message' => 'Unauthorized access');
    }else{
        $sql = "SELECT * FROM user";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();
        // Get role name 
        foreach ($users as $key => $value) {
            $sql = "SELECT user_role FROM roles WHERE role_id = :role";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':role', $value['role']);
            $stmt->execute();
            $user_role = $stmt->fetchColumn();
            $users[$key]['role'] = $user_role;
        }
    
    
        $resp = array('status' => true, 'message' => 'Users fetched successfully', 'users' => $users);
    }
    
}
catch(PDOException $e){
    $resp = array('status' => false, 'message' => 'Error fetching users');
}
header('Content-Type: application/json');
    echo json_encode($resp);
    exit;

}
?>
