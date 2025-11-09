<?php include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>My Bookings</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="book.php">Book a Trip</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
        </nav>
    </header>
    <main>
        <h2>Your Bookings</h2>
        <?php
        $user_id = $_SESSION['user_id'];

        // Query to get user's bookings with destination details and user info from users table
        $sql = "SELECT b.id, u.username, u.email, b.travel_date, d.name AS dest_name, d.price
                FROM bookings b
                JOIN destinations d ON b.destination_id = d.id
                JOIN users u ON b.user_id = u.id
                WHERE b.user_id = ?
                ORDER BY b.travel_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
            echo "<tr><th>Booking ID</th><th>Name</th><th>Email</th><th>Destination</th><th>Price</th><th>Travel Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['dest_name'] . "</td>";
                echo "<td>$" . $row['price'] . "</td>";
                echo "<td>" . $row['travel_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no bookings yet. <a href='book.php'>Book a trip now</a>.</p>";
        }

        $stmt->close();
        ?>
    </main>
</body>
</html>
<?php $conn->close(); ?>