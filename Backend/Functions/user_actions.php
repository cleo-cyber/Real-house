<?php
// user actions
header('Content-Type: application/json');
require_once '../config.php';

// function to store user actions on the website

function user_actions($db, $user_id, $action,$details='') {
    $sql = "INSERT INTO user_actions (User_id, action,details) VALUES (:user_id, :action,:details)";
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'action' => $action,'details'=>$details]);
    
    return true;

}

// function to get user actions

function get_user_actions($db, $user_id) {
    $sql = "SELECT action FROM user_actions WHERE User_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$actions) {
        $resp = array('status' => false, 'message' => 'No actions found');
        echo json_encode($resp);
        exit;
    }

    echo json_encode($actions);
    exit;

}

// function for user password reset action

function reset_password($db, $email, $password) {
    $sql = "SELECT User_id FROM user WHERE Email = :email";
    $stmt = $db->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $resp = array('status' => false, 'message' => 'Email not found');
        echo json_encode($resp);
        exit;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE user SET Password = :password WHERE Email = :email";
    $stmt = $db->prepare($sql);
    $stmt->execute(['password' => $password, 'email' => $email]);

    $resp = array('status' => true, 'message' => 'Password reset successful');
    echo json_encode($resp);
    exit;

}

