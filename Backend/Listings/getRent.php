<?php
require_once '../config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

// Get role from Authorization header
$headers = apache_request_headers();
$authHeader = $headers['Authorization'];

if (empty($authHeader)) {
    $resp = array('status' => false, 'message' => 'Authorization header missing');
}else{
    $decodeHeader = base64_decode($authHeader);
    $splitHeader=explode('|',$decodeHeader);
    $randomString=$splitHeader[0];
    $expire_date=$splitHeader[1];
    $role=$splitHeader[2];
    $usr=$splitHeader[3];

    if($expire_date>time()){
        $sql = 'SELECT user_role FROM roles WHERE role_id = :role';
        $result = $db->prepare($sql);
        $result->bindParam(':role', $role);
        $result->execute();
        $user_role = $result->fetchColumn();
        if ($user_role == 0) {
            $resp = array('status' => false, 'message' => 'Role not found');
        } elseif ($user_role !== 'Realtor') {
            $resp = array('status' => false, 'message' => 'Unauthorized to perform this action');
        }
        else{
            // Get Rent listings from users filtered by house type;
            $json=json_decode(file_get_contents('php://input'),true);
            $data = json_decode($json, true);
            $type=$data['HouseType'];

            if($_SERVER['REQUEST_METHOD']=='POST'){
                $sql="SELECT * FROM listings WHERE HouseType=:type";
                $stmt=$db->prepare($sql);
                $stmt->execute();
                $listings=$stmt->fetchAll(PDO::FETCH_ASSOC);

                // Get house_type and status
                foreach ($listings as $key => $value) {
                    $sql = "SELECT house_type FROM house_type WHERE housetype_id = :HouseType";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['HouseType' => $value['HouseType']]);
                    $house_type = $stmt->fetchColumn();

                    $sql = "SELECT House_status FROM status WHERE Status_id = :Status_id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['Status_id' => $value['Status_id']]);
                    $status = $stmt->fetchColumn();

                    $listings[$key]['house_type'] = $house_type;
                    $listings[$key]['status'] = $status;
                }
                if ($listings) {
                    $resp = array('status' => true, 'message' => 'Listings fetched successfully', 'data' => $listings);
                } else {
                    $resp = array('status' => false, 'message' => 'No listings found');
                }



            }
            header('Content-Type: application/json');
            echo json_encode($resp);

        }

    }

}