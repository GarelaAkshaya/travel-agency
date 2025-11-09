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
            <a href="transport.php">Transport</a>
            <a href="hotels.php">Hotels</a>
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

        <h3>Leave a Review for a Booking</h3>
        <form action="my_bookings.php" method="post">
            <label for="booking_id">Select Booking ID:</label>
            <select id="booking_id" name="booking_id" required>
                <?php
                $sql = "SELECT id FROM bookings WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["id"] . "</option>";
                }
                ?>
            </select><br>
            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required><br>
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment"></textarea><br>
            <input type="submit" value="Submit Review">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rating'])) {
            $booking_id = $_POST['booking_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];

            $stmt = $conn->prepare("INSERT INTO reviews (user_id, booking_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $user_id, $booking_id, $rating, $comment);
            if ($stmt->execute()) {
                echo "<p>Review submitted!</p>";
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

