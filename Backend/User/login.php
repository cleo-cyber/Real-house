<?php
// include_once '../Functions/confirm_login.php';
// checkAuthentication();


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
require_once '../config.php';
require_once '../Functions/user_actions.php';
// session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

if ($data !=null && isset($data['email']) && isset($data['password'])) {
        $email = $data['email'];
        $password = $data['password'];
        $sql = "SELECT * FROM user WHERE email=:email";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            $resp = array('status' => false, 'message' => 'Wrong email or password');
        } else {
            if (password_verify($password, $user['Password'])) {
            
                // Generate token
                $randomString = bin2hex(random_bytes(50));
                $expire_date = strtotime('+15min');
                $role=$user['role'];


                // Store session data
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['expire'] = $expire_date;
                $_SESSION['user'] = $user['Email'];
                $_SESSION['role']=$role;
                // Prepare response
                $usr = $user['Email'];
                $action='Successful login';
                user_actions($db, $user['User_id'], $action, $user['Email']);
                $token=$randomString. '|'. $expire_date. '|'. $role . '|'. $usr;
                $token=base64_encode($token);


                $resp = array('status' => true, 'message' => 'Login successful', 'token' => $token);
            } else {
                $action='Failed login';
                user_actions($db, $user['User_id'], $action, $user['Email']);
                $resp = array('status' => false, 'message' => 'Wrong email or password');
            }
        }
    } else {

        $resp = array('status' => false, 'message' => 'Please fill in all fields');
    }
    
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}
