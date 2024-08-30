<?php
function validateImage($file_path) {
    $check = getimagesize($file_path);
    return $check !== false ? $check : null;
}

function generateUniqueFileName($dir, $file_name) {
    $file_base = pathinfo($file_name, PATHINFO_FILENAME);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $unique_file_name = $file_base . '.' . $file_ext;
    $counter = 1;

    while (file_exists($dir . $unique_file_name)) {
        $unique_file_name = $file_base . '_' . $counter . '.' . $file_ext;
        $counter++;
    }

    return $unique_file_name;
}

function saveImageFile($dir, $file_name, $file_data) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $unique_file_name = generateUniqueFileName($dir, $file_name);
    $target_file = $dir . $unique_file_name;
    $success = file_put_contents($target_file, $file_data);

    return $success ? $unique_file_name : false;
}

function getUploadPath($file_name) {
    return "../uploads/" . $file_name;
}

// function uploadImage() {
//     $json = file_get_contents('php://input');
//     $data = json_decode($json, true);

//     if (isset($data['images'])) {
//         $file_path = $data['images'];
//         $file_name = basename($file_path);
//         $file_data = file_get_contents($file_path);
//         $file_size = strlen($file_data);

//         $image_info = validateImage($file_path);
//         if ($image_info === null) {
//             echo "File is not a valid image.<br>";
//             return;
//         }

//         $image_mime = $image_info["mime"];
//         $allowedFormats = ['image/jpeg', 'image/png', 'image/gif'];
//         if (!in_array($image_mime, $allowedFormats)) {
//             echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
//             return;
//         }

//         if ($file_size > 5000000) { // 5MB limit
//             echo "Sorry, your file is too large.<br>";
//             return;
//         }

//         $uploaded_file_name = saveImageFile("../uploads/", $file_name, $file_data);
//         if ($uploaded_file_name === false) {
//             echo "Sorry, there was an error uploading your file.<br>";
//             return;
//         }

//         echo "The file " . htmlspecialchars($uploaded_file_name) . " has been uploaded successfully.<br>";
//     } else {
//         echo "Invalid input data.<br>";
//     }
// }

// uploadImage();

header('Content-Type: application/json');

?>
