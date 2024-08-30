<?php


require_once '../config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

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

    if ($role != 'Admin') {
        $resp = array('status' => false, 'message' => 'Unauthorized access');
    }else{
    }
    if ($_SERVER['REQUEST_METHOD']=='PUT'){
        $json = file_get_contents('php://input');   
        $data = json_decode($json, true);

        // Get user id from the URL parameter and edit the user
        $id = $data['id'];

        $sql = "SELECT * FROM user WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if ($user) {
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];
            $email = $data['email'];
            $role = $data['role'];

            $sql = "UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, role = :role WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'role' => $role, 'id' => $id]);
            $resp = array('status' => true, 'message' => 'User updated successfully');
        }else{
            $resp = array('status' => false, 'message' => 'User not found');
        }

    }else{
        $resp = array('status' => false, 'message' => 'Invalid request method');

        
    }

    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;

}

