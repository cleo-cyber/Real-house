<?php
require '../vendor/autoload.php'; // Make sure you include the autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once '../config.php';

try {


    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        // Respond to preflight request
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        http_response_code(204); // No content
        exit;
    }

    header("Access-Control-Allow-Origin: *");

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Invalid data received');
    }

    $name = isset($data['name']) ? htmlspecialchars(trim($data['name'])) : '';
    $email = isset($data['email']) ? htmlspecialchars(trim($data['email'])) : '';
    $phone = isset($data['phone']) ? htmlspecialchars(trim($data['phone'])) : '';
    $message = isset($data['message']) ? htmlspecialchars(trim($data['message'])) : '';

    // All fields are required
    if (empty($name) || empty($email) || empty($message)) {
        throw new Exception('Name, email, and message are required fields');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    $listing_id = $_GET['listing_id'] ?? null;
    if ($listing_id === null) {
        throw new Exception('Listing ID is required');
    }

    $sql = "SELECT email FROM user WHERE User_id = (SELECT User_id FROM listings WHERE Listing_id = :listing_id)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':listing_id', $listing_id);
    $stmt->execute();
    $recipient_email = $stmt->fetchColumn();
    if (!$recipient_email) {
        throw new Exception('Recipient email not found for the specified listing');
    }

    // Setup PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0;                                       
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                       
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = '';                  // SMTP username
        $mail->Password   = '';                      // SMTP password
        $mail->SMTPSecure = 'tls';                                
        $mail->Port       = 587;                                    

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress($recipient_email);                        
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);                                        
        $mail->Subject = 'Message from ' . $name;
        $mail->Body    = nl2br($message);                           
        $mail->AltBody = $message;                                  

        $mail->send();
        $response = array(
            'status' => true,
            'message' => 'Email sent successfully',
            'recipient_email' => $recipient_email
        );
    } catch (Exception $e) {
        throw new Exception('Failed to send email. Mailer Error: ' . $mail->ErrorInfo);
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'status' => false,
        'message' => $e->getMessage()
    ));
}

?>