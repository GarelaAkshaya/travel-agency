<?php
$servername = "localhost";
$username = "root";          // Change to your MySQL username
$password = "root";              // Change to your MySQL password
$dbname = "agency_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data and sanitize
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);
$message = $conn->real_escape_string($_POST['message']);

$sql = "INSERT INTO customers (name, email, message) VALUES ('$name', '$email', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "Thank you! Your message has been received.";
    //TODO Route back to homepage after 3 seconds

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>