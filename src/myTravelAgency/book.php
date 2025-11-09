<?php include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's username (as name) and email from the database
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT username AS name, email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $user_name = $user['name'];  // This is actually the username
    $user_email = $user['email'];
} else {
    echo "<p>Error: User not found.</p>";
    exit();
}
$stmt_user->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Trip</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Book Your Trip</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="book.php">Book a Trip</a>

            <a href="transport.php">Transport</a>
            <a href="hotels.php">Hotels</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
        </nav>
    </header>
    <main>
        <p>Booking as: <strong><?php echo $user_name; ?> (<?php echo $user_email; ?>)</strong></p>
        <form action="book.php" method="post">
            <label for="destination">Destination:</label>
            <select id="destination" name="destination_id" required>
                <?php
                $sql = "SELECT id, name FROM destinations";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                    }
                } else {
                    echo "<p>No destinations available.</p>";
                }
                ?>
            </select><br>

            <label for="date">Travel Date:</label>
            <input type="date" id="date" name="travel_date" required><br>

            <input type="submit" value="Book Now">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $destination_id = mysqli_real_escape_string($conn, $_POST['destination_id']);
            $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);

            // Insert with username as name and email from the logged-in user
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, email, destination_id, travel_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issis", $user_id, $user_name, $user_email, $destination_id, $travel_date);

            if ($stmt->execute()) {
                echo "<p>Booking successful! Your trip is confirmed.</p>";
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
        ?>
    </main>
</body>
</html>
<?php $conn->close(); ?>