<?php
require_once '../config.php';

header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// function saveBase64Images($images, $uploadDir = '../uploads/') {
//     $response = array();
//     $uploadedFiles = array();
//     foreach ($images as $base64String) {
//         // Extract file extension from base64 string
//         $extension = explode('/', mime_content_type($base64String))[1];
        
//         // Generate unique file name
//         $fileName = uniqid() . '.' . $extension;
//         // Decode base64 string to binary data
//         $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64String));
//         // Save binary data to file
//         $filePath = $uploadDir . $fileName;
//         if (file_put_contents($filePath, $fileData)) {
//             $uploadedFiles[] = $filePath;
//         } else {
//             $response['status'] = false;
//             $response['message'] = 'Failed to save file ' . $fileName;
//             $response['uploadDir'] = $uploadDir;

//             return $response;
//         }
//     }

//     $response['status'] = true;
//     $response['message'] = 'Files uploaded successfully';
//     $response['files'] = $uploadedFiles;
//     return $response;
// }

// if (isset($data['images']) && !empty($data['images'])) {
//     $response = saveBase64Images($data['images']);
//     echo json_encode($response);
// } else {
//     echo json_encode(['status' => false, 'message' => 'No images provided']);
// }

function upload_multiple($images,$uploadDir){
    $uploadedFiles=[];
    $filenames=[];
    foreach($images as $image){
        $filename=basename($image['name']);
        $filepath=$uploadDir.$filename;
        
        $filedata=base64_decode($image['data']);
        $filenames[]=$filename;

        if(file_put_contents($filepath,$filedata)){
            $uploadedFiles[]=$filepath;
            $resp=array(
                'status'=>true,
                'message'=>'File uploaded successfully',
                'file'=>$uploadedFiles,
                'filename'=>$filenames

            );
            

        }
        else{
            // $uploadedFiles[]='Failed to upload' . $filename;
            $resp=array(
                'status'=>false,
                'message'=>'Failed to upload' . $filename,
                'file'=>$uploadedFiles,
                'filename'=>$filenames
            );
        }
    }

    return $resp;
    
}

// if (isset($data['images']) && !empty($data['images'])) {
//     $response = upload_multiple($data['images'],'../uploads');
//     echo json_encode($response);
// } else {
//     echo json_encode(['status' => false, 'message' => 'No images provided']);
// }
?>
