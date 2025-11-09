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
    <title>Book Hotels</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Book Hotels</h1>
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
        <h2>Search and Book Hotels</h2>
        <form action="hotels.php" method="post">
            <label for="destination">Select Destination:</label>
            <select id="destination" name="destination_id" required>
                <?php
                $sql = "SELECT id, name FROM destinations";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                }
                ?>
            </select><br>
            <input type="submit" value="Search Hotels">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destination_id'])) {
            $destination_id = $_POST['destination_id'];
            $sql = "SELECT h.id, h.name, h.description, h.price_per_night, d.name AS dest_name
                    FROM hotels h
                    JOIN destinations d ON h.destination_id = d.id
                    WHERE h.destination_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $destination_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<h3>Available Hotels</h3>";
                echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
                echo "<tr><th>Name</th><th>Description</th><th>Destination</th><th>Price/Night</th><th>Book</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>" . $row['dest_name'] . "</td>";
                    echo "<td>$" . $row['price_per_night'] . "</td>";
                    echo "<td><form action='hotels.php' method='post' style='display:inline;'><input type='hidden' name='hotel_id' value='" . $row['id'] . "'><input type='submit' value='Book'></form></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hotels found for this destination.</p>";
            }
            $stmt->close();
        }

        // Handle booking submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hotel_id'])) {
            $hotel_id = $_POST['hotel_id'];
            $user_id = $_SESSION['user_id'];
            $user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';
            $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'unknown@example.com';

            // Fetch hotel details for booking
            $sql = "SELECT * FROM hotels WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $hotel_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $hotel = $result->fetch_assoc();
                // Insert into bookings
                $stmt_insert = $conn->prepare("INSERT INTO bookings (user_id, name, email, destination_id, hotel_id, travel_date) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt_insert->bind_param("issii", $user_id, $user_name, $user_email, $hotel['destination_id'], $hotel_id);
                if ($stmt_insert->execute()) {
                    echo "<p>Hotel booked successfully!</p>";
                } else {
                    echo "<p>Error: " . $stmt_insert->error . "</p>";
                }
                $stmt_insert->close();
            } else {
                echo "<p>Hotel not found.</p>";
            }
            $stmt->close();
        }
        ?>
    </main>
</body>
</html>
<?php $conn->close(); ?>