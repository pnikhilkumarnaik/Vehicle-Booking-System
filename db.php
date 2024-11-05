<?php
$servername = "localhost";
$username = "NIKHIL"; // Make sure this is your actual username
$password = "nikhil@1729"; // Your actual password
$dbname = "rideme"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
