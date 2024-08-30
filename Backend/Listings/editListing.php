<?php
require_once '../config.php';
require_once '../Functions/getStatus.php';
require_once '../Functions/getHtype.php';
require_once '../Functions/uploads.php';
require_once '../Functions/multiple_imageupload.php';
require_once '../Functions/user_actions.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

$header = apache_request_headers();
$authHeader = $header['Authorization'];

if (empty($authHeader)) {
    $resp = array('status' => false, 'message' => 'Authorization header missing');
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
}

$decodedHeader = base64_decode($authHeader);
$decodedHeader = explode('|', $decodedHeader);

$randomString = $decodedHeader[0];
$expire_date = $decodedHeader[1];
$role = $decodedHeader[2];
$usr = $decodedHeader[3];

if ($expire_date > time()) {
    $user_role_query = 'SELECT user_role FROM roles WHERE role_id = :role';
    $result = $db->prepare($user_role_query);
    $result->bindParam(':role', $role);
    $result->execute();
    $user_role = $result->fetchColumn();

    if ($user_role == 0) {
        $resp = array('status' => false, 'message' => 'Role not found');
        header('Content-Type: application/json');
        echo json_encode($resp);
        exit();
    }
    
    if ($user_role !== 'Admin' && $user_role !== 'Realtor') {
        $resp = array('status' => false, 'message' => 'Unauthorized to perform this action');
        header('Content-Type: application/json');
        echo json_encode($resp);
        exit();
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['listing_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            // Check if listing exists
            $check = 'SELECT * FROM listings WHERE listing_id = :id';
            $stmt = $db->prepare($check);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $listing = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$listing) {
                $resp = array('status' => false, 'message' => 'Listing not found');
                header('Content-Type: application/json');
                echo json_encode($resp);
                exit();
            }

            // Check if user is the one who created the listing
            $user = 'SELECT User_id FROM user WHERE email = :usr';
            $stmt = $db->prepare($user);
            $stmt->bindParam(':usr', $usr);
            $stmt->execute();
            $user_id = $stmt->fetchColumn();

            if ($user_id == 0) {
                $resp = array('status' => false, 'message' => 'User not found');
                header('Content-Type: application/json');
                echo json_encode($resp);
                exit();
            }

            if ($user_id !== $listing['User_id']) {
                $resp = array('status' => false, 'message' => 'Unauthorized to perform this action');
                header('Content-Type: application/json');
                echo json_encode($resp);
                exit();
            } else {
                $house_type = isset($data['house_type']) ? $data['house_type'] : $listing['HouseType'];
                $price = isset($data['price']) ? $data['price'] : $listing['Price'];
                $location = isset($data['location']) ? $data['location'] : $listing['Location'];
                $status_id = isset($data['status']) ? $data['status'] : $listing['Status_id'];
                $description = isset($data['description']) ? $data['description'] : $listing['Description'];
                $title = isset($data['title']) ? $data['title'] : $listing['title'];
                $bedrooms = isset($data['bedrooms']) ? $data['bedrooms'] : $listing['bedrooms'];
                $baths = isset($data['baths']) ? $data['baths'] : $listing['baths'];
                $size = isset($data['size']) ? $data['size'] : $listing['size'];
                $amenities = isset($data['amenities']) ? $data['amenities'] : $listing['Amenities'];
                $images = isset($data['image']) ? $data['image'] : $listing['file_url'];

                $S_id = 'SELECT Status_id FROM status WHERE House_status = :status';
                $result = $db->prepare($S_id);
                $result->bindParam(':status', $status_id);
                $result->execute();
                $status_id = $result->fetchColumn();

                if (!$status_id) {
                    $resp = array('status' => false, 'message' => 'Status not found');
                    header('Content-Type: application/json');
                    echo json_encode($resp);
                    exit();
                }

                $h_type = 'SELECT housetype_id FROM house_type WHERE house_type = :house_type';
                $result = $db->prepare($h_type);
                $result->bindParam(':house_type', $house_type);
                $result->execute();
                $house_id = $result->fetchColumn();

                if (!$house_id) {
                    $resp = array('status' => false, 'message' => 'House type not found');
                    header('Content-Type: application/json');
                    echo json_encode($resp);
                    exit();
                }

                if (!empty($data['image'])) {
                    $response = upload_multiple($data['image'], 'C:\Users\kadima\Desktop\Codes\RealHouse\RealHouse\assets\uploads/');
                    if ($response['status'] == false) {
                        $resp = array('status' => false, 'message' => 'Failed to upload image');
                        header('Content-Type: application/json');
                        echo json_encode($resp);
                        exit();
                    } else {
                        $file_url = json_encode($response['filename']);
                    }
                } else {
                    $file_url = $listing['file_url'];
                }

                $sql = 'UPDATE listings SET HouseType = :house_id, Price = :price, Location = :location, Status_id = :status_id, Description = :description, file_url = :file_url, title = :title, bedrooms = :bedrooms, baths = :baths, size = :size, Amenities = :amenities WHERE listing_id = :listing_id';
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':house_id', $house_id);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':status_id', $status_id);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':file_url', $file_url);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':bedrooms', $bedrooms);
                $stmt->bindParam(':baths', $baths);
                $stmt->bindParam(':size', $size);
                $stmt->bindParam(':amenities', $amenities);
                $stmt->bindParam(':listing_id', $id);

                $stmt->execute();
                $action = 'Listing updated';
                user_actions($db, $listing['User_id'], $action);
                if ($stmt) {
                    $resp = array('status' => true, 'message' => 'Listing updated successfully');
                } else {
                    $resp = array('status' => false, 'message' => 'Failed to update listing');
                }

                header('Content-Type: application/json');
                echo json_encode($resp);
                exit();
            }
        }
    }
} else {
    $resp = array('status' => false, 'message' => 'Session expired');
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
}
?>
