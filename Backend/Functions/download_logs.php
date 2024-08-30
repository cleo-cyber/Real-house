<?php
// user log download as csv

header('Content-Type: application/json');
require_once '../config.php';
$authHeader = apache_request_headers()['Authorization'];

function get_logs($db, $user_id) {
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
        $sql = "SELECT action, date_performed,details FROM user_actions";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $resp = array('status' => false, 'message' => 'You are not authorized to download this report');
    }
    return $logs;
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

$logs = get_logs($db, $user_id);

if (empty($logs)) {
    $resp = array('status' => false, 'message' => 'No logs found');
    echo json_encode($resp);
    exit;
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="logs.csv"');

    $fp = fopen('php://output', 'w');
    fputcsv($fp, array('action', 'date_created', 'details'));
    foreach ($logs as $log) {
        fputcsv($fp, $log);
    }
    fclose($fp);
}

?>
