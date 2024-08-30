<?php
function checkAuthentication($requiredRole = null) {
    // Start the session
    session_start();

    // Check if the user is not authenticated or doesn't have the required role
    if (
        !isset($_SESSION['isLoggedIn']) || // Check if user is not logged in
        $_SESSION['isLoggedIn'] !== true || // Check if user is not properly authenticated
        ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) // Check if user doesn't have the required role
    ) {
        $resp=array(
            'status'=>false,
            'message'=>'Unaothorized'
        );
        
    }
}
?>
