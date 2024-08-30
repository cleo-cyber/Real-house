<?php
require_once '../config.php';
require_once '../Functions/uploads.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if ($data != null && isset($data['firstname']) && isset($data['lastname']) && isset($data['email']) && isset($data['password']) && isset($data['password2'])) {
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $email = $data['email'];
        $pass = $data['password'];
        $pass2 = $data['password2'];
        $role = $data['role'];

        if ($pass != $pass2) {
            $resp = array('status' => false, 'message' => 'Passwords do not match');
            // Send the response and exit
            header('Content-Type: application/json');
            echo json_encode($resp);
            exit;
        }

        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $resp = array('status' => false, 'message' => 'Email already exists');
            // Send the response and exit
            header('Content-Type: application/json');
            echo json_encode($resp);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $resp = array('status' => false, 'message' => 'Invalid email address');
            // Send the response and exit
            header('Content-Type: application/json');
            echo json_encode($resp);
            exit;
        }

        if (strlen($pass) < 6 || !preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/", $pass)) {
            $resp = array('status' => false, 'message' => 'Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter, and one digit');
            // Send the response and exit
            header('Content-Type: application/json');
            echo json_encode($resp);
            exit;
        }

        $password = password_hash($pass, PASSWORD_DEFAULT);

        // Get role id from roles table
        $sql = "SELECT role_id FROM roles WHERE user_role = :role";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $role_id = $stmt->fetchColumn();
        if ($role_id === false) {
            $resp = array('status' => false, 'message' => 'Error fetching role_id');
            // Send the response and exit
            header('Content-Type: application/json');
            echo json_encode($resp);
            exit;
        } elseif (!$role_id) {
            $resp = array('status' => false, 'message' => 'Role does not exist');
            // Send the response and exit
            header('Content-Type: application/json');
            echo json_encode($resp);
            exit;
        } else {
            $role = $role_id;
        }

        try {
            $sql = "INSERT INTO user (Firstname, Lastname, Email, Password, role)
                    VALUES (:firstname, :lastname, :email, :password, :role)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            if ($stmt->execute()) {
                $resp = array('status' => true, 'message' => 'User created successfully');
            } else {
                $resp = array('status' => false, 'message' => 'Error creating user');
            }

        } catch (PDOException $e) {
            $resp = array('status' => false, 'message' => 'Error: ' . $e->getMessage());
        }

    } else {
        $resp = array('status' => false, 'message' => 'Please fill in all fields', 'data' => $data);
    }

    // Send the final response
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}
