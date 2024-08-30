<?php
require_once '../config.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

$headers = apache_request_headers();
$authHeader = $headers['Authorization'];


// refresh token function irregardless of role

if (empty($authHeader)) {
    $resp = array('status' => false, 'message' => 'Authorization header missing');
}
else{
    $decodeHeader = base64_decode($authHeader);
    $splitHeader=explode('|',$decodeHeader);
    $randomString=$splitHeader[0];
    $expire_date=$splitHeader[1];
    $role=$splitHeader[2];
    $usr=$splitHeader[3];
    
    if ($expire_date > time()) {
        $sql = 'SELECT user_role FROM roles WHERE role_id = :role';
        $result = $db->prepare($sql);
        $result->bindParam(':role', $role);
        $result->execute();
        $user_role = $result->fetchColumn();
        if ($user_role == 0) {
            $resp = array('status' => false, 'message' => 'Role not found');
        } else {
            $resp = array('status' => true, 'message' => 'Token refreshed successfully');
        }
    } else {
        $resp = array('status' => false, 'message' => 'Token expired');
    }
}

