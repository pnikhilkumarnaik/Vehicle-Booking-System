<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
    
    // Redirect to the home page or login page
    header("Location: index.php"); // Change this to the desired redirect page
    exit();
} else {
    // If no session exists, redirect to the home page
    header("Location: index.php");
    exit();
}
?>
