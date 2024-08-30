<?php
function upload_single($image,$uploadDir){
    $filename=basename($image['name']);
    $filepath=$uploadDir.$filename;
    
    $filedata=base64_decode($image['data']);
    if(file_put_contents($filepath,$filedata)){
        $resp=array(
            'status'=>true,
            'message'=>'File uploaded successfully',
            'file'=>$filepath,
            'filename'=>$filename

        );
    }else{
        $resp=array(
            'status'=>false,
            'message'=>'Failed to upload file',
            'file'=>$filepath,
            'filename'=>$filename
        );
    }
    return $resp;
}

// example usage
// $data = json_decode(file_get_contents('php://input'), true);

// if (isset($data['image']) && !empty($data['image'])) {
//     $response = upload_single($data['image'],'../uploads/');
//     echo json_encode($response);
// } else {
//     echo json_encode(['status' => false, 'message' => 'No image provided']);
// }

?>
