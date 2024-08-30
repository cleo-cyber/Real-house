<?php
require_once '../config.php';
require_once '../Functions/user_actions.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

$header=apache_request_headers();
$authHeader=$header['Authorization'];

if(empty($authHeader)){
    $resp=array('status'=>false, 'message'=>'Authorization header missing');

}
else{
    $decodedHeader=base64_decode($authHeader);
    // list($randomString, $expire_date, $role, $usr)=explode('|', $decodedHeader);
    $splitHeader=explode('|', $decodedHeader);
    $randomString=$splitHeader[0];
    $expire_date=$splitHeader[1];
    $role=$splitHeader[2];
    $usr=$splitHeader[3];
    
    if ($expire_date>time()){
    $user_role='SELECT user_role FROM roles WHERE role_id=:role';
    $result=$db->prepare($user_role);
    $result->bindParam(':role', $role);
    $result->execute();
    $user_role=$result->fetchColumn();

    if($user_role==0){
        $resp=array('status'=>false, 'message'=>'Role not found');
    }
    if ($user_role !=='Admin' && $user_role!=='Realtor' ){
        $resp=array('status'=>false,'message'=>'Unauthorized to perform this action');
        // echo json_encode($resp);
    }
    else{
        $data=json_decode(file_get_contents('php://input'),true);
        // $data=json_decode($json,true);
        $id=$data['listing_id'];


        if ($_SERVER['REQUEST_METHOD']=='DELETE'){
            // check if listing exists

            $check='SELECT * FROM listings WHERE listing_id=:id';
            $stmt=$db->prepare($check);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $listing=$stmt->fetch(PDO::FETCH_ASSOC);

            if(!$listing){
                $action='delete failed';
                
                user_actions($db, $listing['User_id'], $action);
                $resp=array('status'=>false, 'message'=>'Listing not found');
                // echo json_encode($resp);
                exit();
            }
            $delete_date=date('Y-m-d H:i:s');

            $sql='INSERT INTO deleted_listings (Description,HouseType,Amenities,Price,file_url,User_id,Status_id,title,Location,bedrooms,baths,size,delete_date)
            VALUES(:description,:house_type,:amenities,:price,:file_url,:u_id,:status_id,:title,:location,:bedrooms,:baths,:size,:delete_date)';
            $stmt=$db->prepare($sql);
            $stmt->bindParam(':description', $listing['Description']);
            $stmt->bindParam(':house_type', $listing['HouseType']);
            $stmt->bindParam(':amenities', $listing['Amenities']);
            $stmt->bindParam(':price', $listing['Price']);
            $stmt->bindParam(':file_url', $listing['file_url']);
            $stmt->bindParam(':u_id', $listing['User_id']);
            $stmt->bindParam(':status_id', $listing['Status_id']);
            $stmt->bindParam(':title', $listing['title']);
            $stmt->bindParam(':location', $listing['Location']);
            $stmt->bindParam(':bedrooms', $listing['bedrooms']);
            $stmt->bindParam(':baths', $listing['baths']);
            $stmt->bindParam(':size', $listing['size']);
            $stmt->bindParam(':delete_date', $delete_date);
            $stmt->execute();

            

            $sql='DELETE FROM listings where listing_id=:id';
            $stmt=$db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $action='Listing deleted';
            $details='Listing with id '.$id.' was deleted by '.$usr;
            user_actions($db, $listing['User_id'], $action, $details);
            $resp=array('status'=>true, 'message'=>'Listing deleted successfully');
            // echo json_encode($resp);
        }
    }   

}
else{

    $resp=array('status'=>false, 'message'=>'Session expired');
    // echo json_encode($resp);
}
header ('Content-Type: application/json');
echo json_encode($resp);
exit();
}


?>
