<?php
require_once('../config.php');
require_once('../Functions/confirm_login.php');
// require_once('../Functions/uploads.php');
require_once('../Functions/multiple_imageupload.php');
require_once('../Functions/user_actions.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
$headers=apache_request_headers();
$autheader=$headers['Authorization'];

if(empty($autheader)){
    $resp=array(
        'status'=>false,
        'message'=>'Authorization header missing'

    );  
    
}

$decodeHeader=base64_decode($autheader);
// list($randomString,$expire_date,$role,$usr)=explode('|',$decodeHeader);
$splitHeader=explode('|',$decodeHeader);
$randomString=$splitHeader[0];
$expire_date=$splitHeader[1];
$role=$splitHeader[2];
$usr=$splitHeader[3];

if($expire_date > time()){
$sql='SELECT user_role FROM roles where role_id =:role';
$result=$db->prepare($sql);
$result->bindParam(":role",$role);
// $result->execute(['role' => $role]);
$result->execute();
$user_role=$result->fetchColumn();
if($user_role==0){
    $resp=array(
        'status'=>false,
        'message'=>'role not found'
    );
    
}
if ($user_role!=='Realtor'){
    $resp=array(
        'status'=>false,
        'message'=>'Unaothorize to perform this action'
    );
    
}
else{
    $json=file_get_contents('php://input');
    $data=json_decode($json,true);

    if(empty($data['description']) || empty($data['location']) || empty($data['price']) || empty($data['amenities']) || empty($data['image']) || empty($data['title'])){
        $resp=array(
            'status'=>false,
            'message'=>'All fields are required'
        );
        
    }

    $description=$data['description'];
    $location=$data['location'];
    $price=$data['price'];
    $amenities=$data['amenities'];
    // $file_url=$data['image'];
    $title=$data['title'];
    $bedrooms=$data['bedrooms'];
    $baths=$data['baths'];
    $size=$data['size'];

    $status=$data['status'];
    $house_type=$data['house_type'];
    $email=$usr;
    $User_id="SELECT User_id FROM user WHERE email=:email"; 
    $result=$db->prepare($User_id);
    $result->bindParam(':email',$email);
    $result->execute();
    $u_id=$result->fetchColumn();

    if($u_id==0){
        $resp=array(
            'status'=>false,
            'message'=>'User not found unaothorized to perform this action'
        );
       
    }

    $S_id='SELECT Status_id FROM status WHERE House_status=:status';
    $result=$db->prepare($S_id);
    $result->bindParam(":status",$status);
    $result->execute();
    $status_id=$result->fetchColumn();

    if ($status_id==0){
        $resp=array(
            'status'=>false,
            'message'=>"Status error"
        );

    }
    $h_type='SELECT housetype_id FROM house_type WHERE house_type=:house_type';
    $result=$db->prepare($h_type);
    $result->bindParam(":house_type",$house_type);
    $result->execute();
    $house_type=$result->fetchColumn();

    if ($house_type==0){
        $resp=array(
            'status'=>false,
            'message'=>"House eroor"
        );
     
    }
    try{
        // echo $data['image'];
        if (isset($data['image']) && !empty($data['image'])) {
            $response = upload_multiple($data['image'], 'C:\Users\kadima\Desktop\Codes\RealHouse\RealHouse\assets\uploads/');
            if($response['status']==false){
                $resp=array(
                    'status'=>false,
                    'message'=>'Failed to upload image'
                );
            }
            else{
                $file_url=$response['filename'];
                $file_url=json_encode($file_url);

                
            }
            
            
        } else {
            $resp=array(
                'status'=>false,
                'message'=>'No images provided'
            );
        }

        
        // $file_url=upload_image($data);
        $sql="INSERT INTO listings (Description,HouseType,Amenities,Price,file_url,User_id,Status_id,title,Location,bedrooms,baths,size)
        VALUES(:description,:house_type,:amenities,:price,:file_url,:u_id,:status_id,:title,:location,:bedrooms,:baths,:size)";
        $result=$db->prepare($sql);
        $result->bindParam(":description",$description);
        $result->bindParam(":house_type",$house_type);
        $result->bindParam(":location",$location);
        $result->bindParam(":amenities",$amenities);
        $result->bindParam(":price",$price);
        $result->bindParam(":file_url",$file_url);
        $result->bindParam(":u_id",$u_id);
        $result->bindParam(":status_id",$status_id);
        $result->bindParam(":title",$title);
        $result->bindParam(":bedrooms",$bedrooms);
        $result->bindParam(":baths",$baths);
        $result->bindParam(":size",$size);

        $result->execute();
        $action = 'Listing created';
        $details = 'Listing created with id ' . $db->lastInsertId() . 'was created by ' . $u_id;
        user_actions($db, $u_id, $action, $details);
        $resp=array(
            'status'=>true,
            'message'=>'Listing created successfully'
        );


    }
    catch(PDOException $e){
$resp=array(
    'status'=>false,
    'message'=>$e->getMessage()
);
    }




}

// if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=='POST'){
//     $json=file_get_contents('php://input');
//     $data=json_decode($json,true);



// }
}else{
    $resp=array(
        'status'=>false,
        'message'=>'Expired token'
    );
}
header('Content-Type: application/json');
echo json_encode($resp);
exit;
