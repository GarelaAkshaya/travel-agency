<?php
session_start();  // Start session for login tracking
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "travel_agency";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>