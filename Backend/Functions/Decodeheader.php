<?php
function Decode_header(){
    $header=apache_request_headers();
    $authHeader=$header['Authorization'];
    if(empty($authHeader)){
        $resp=array('status'=>false, 'message'=>'Authorization header missing');
        return $resp;
    }
    else{
        $decodedHeader=base64_decode($authHeader);
        $splitHeader=explode('|', $decodedHeader);
        // $randomString=$splitHeader[0];
        // $expire_date=$splitHeader[1];
        // $role=$splitHeader[2];
        // $usr=$splitHeader[3];
        return $splitHeader;
    }
}