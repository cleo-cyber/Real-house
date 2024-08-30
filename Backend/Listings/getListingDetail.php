<?php   
require_once '../config.php';

$authHeader = apache_request_headers()['Authorization'];

if (empty($authHeader)) {
    $resp = array('status' => false, 'message' => 'Authorization header missing');
}
else{
    $decodeHeader = base64_decode($authHeader);
    // echo $decodeHeader;
    list($randomString, $expire_date, $role, $usr) = explode('|', $decodeHeader);
    
    if ($expire_date > time()) {

        if($_SERVER['REQUEST_METHOD']=='GET'){
            $listing_id=$_GET['listing_id'];
            // get listing detail
            $sql="SELECT * FROM listings WHERE Listing_id=:listing_id";
            $stmt=$db->prepare($sql);
            $stmt->execute(['listing_id'=>$listing_id]);
            $listing=$stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($listing)){
                $resp=array('status'=>false, 'message'=>'No listing found');
            }
            else{
                // decode image
                $sql="SELECT house_type FROM house_type WHERE housetype_id=:HouseType";
                $stmt=$db->prepare($sql);
                $stmt->execute(['HouseType'=>$listing['HouseType']]);
                $house_type=$stmt->fetchColumn();

                $sql="SELECT House_status FROM status WHERE Status_id=:Status_id";
                $stmt=$db->prepare($sql);
                $stmt->execute(['Status_id'=>$listing['Status_id']]);
                $status=$stmt->fetchColumn();

                $listing['house_type']=$house_type;
                $listing['status']=$status;
                // $listing['file_url']=json_decode($value['file_url']);
                $resp=array('status'=>true, 'message'=>'Listing found', 'data'=>$listing);

        }
        }
        else{
            $resp=array('status'=>false, 'message'=>'Unauthorized access');
        }
    
    }
    else{
        $resp=array('status'=>false, 'message'=>'Authorization header expired');

    
    
    }
}
header('Content-Type: application/json');
echo json_encode($resp);
?>