<?php

// Send email from logged in user to the user who posted the listing
require_once '../config.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (empty($data['message']) || empty($data['listing_id'])) {
    echo json_encode(['status' => false, 'message' => 'All fields are required']);
    return;
}

$message = $data['message'];
$listing_id = $data['user_id'];

// Get the email of the user who posted the listing

$listing = "SELECT Email FROM user WHERE user_id=:listing_id";
$result = $db->prepare($listing);
$result->bindParam(":listing_id", $listing_id);
$result->execute();
$email = $result->fetchColumn();

if ($email == 0) {
    echo json_encode(['status' => false, 'message' => 'Listing not found']);
    return;
}

// Get the email of the logged in user
$usr = $_SESSION['email'];
$user = "SELECT Email FROM user WHERE Email=:email";
$result = $db->prepare($user);
$result->bindParam(":email", $usr);
$result->execute();
$from = $result->fetchColumn();

if ($from == 0) {
    echo json_encode(['status' => false, 'message' => 'User not found']);
    return;
}

$subject = "Message from " . $from;
$headers = "From: " . $from;

if (mail($email, $subject, $message, $headers)) {
    echo json_encode(['status' => true, 'message' => 'Email sent']);
} else {
    echo json_encode(['status' => false, 'message' => 'Failed to send email']);
}
?>
