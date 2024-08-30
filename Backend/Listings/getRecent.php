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
            $sql="SELECT * FROM listings ORDER BY Date_created DESC LIMIT 3";
            $stmt=$db->prepare($sql);
            $stmt->execute();
            $listings=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($listings)){
                $resp=array('status'=>false, 'message'=>'No listings found');
            }
            else{
                // decode image
                foreach($listings as $key=>$value){
                    $sql="SELECT house_type FROM house_type WHERE housetype_id=:HouseType";
                    $stmt=$db->prepare($sql);
                    $stmt->execute(['HouseType'=>$value['HouseType']]);
                    $house_type=$stmt->fetchColumn();

                    $sql="SELECT House_status FROM status WHERE Status_id=:Status_id";
                    $stmt=$db->prepare($sql);
                    $stmt->execute(['Status_id'=>$value['Status_id']]);
                    $status=$stmt->fetchColumn();

                    $listings[$key]['house_type']=$house_type;
                    $listings[$key]['status']=$status;
                    // $listings[$key]['file_url']=substr(json_decode(explode(',',$value['file_url'])),2);
                    $listings[$key]['file_url']=json_decode($value['file_url'])[0];
                
                }

                $resp=array('status'=>true, 'message'=>'Listings found', 'data'=>$listings);
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