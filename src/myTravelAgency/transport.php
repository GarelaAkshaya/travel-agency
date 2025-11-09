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
    <title>Book Transport</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Book Transport</h1>
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
        <h2>Search and Book Transport</h2>
         <p>Booking as: <strong><?php echo $user_name; ?> (<?php echo $user_email; ?>)</strong></p>

        <form action="transport.php" method="post">
            <label for="from">From Destination:</label>
            <select id="from" name="from_id" required>
                <?php
                $sql = "SELECT id, name FROM destinations";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                }
                ?>
            </select><br>
            <label for="to">To Destination:</label>
            <select id="to" name="to_id" required>
                <?php
                $sql = "SELECT id, name FROM destinations";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                }
                ?>
            </select><br>
            <input type="submit" value="Search Transport">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['from_id'])) {
            $from_id = $_POST['from_id'];
            $to_id = $_POST['to_id'];
            $sql = "SELECT t.id, t.type, t.provider, t.price, t.departure_time, d1.name AS from_dest, d2.name AS to_dest
                    FROM transport t
                    JOIN destinations d1 ON t.from_destination_id = d1.id
                    JOIN destinations d2 ON t.to_destination_id = d2.id
                    WHERE t.from_destination_id = ? AND t.to_destination_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $from_id, $to_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<h3>Available Transport Options</h3>";
                echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
                echo "<tr><th>Type</th><th>Provider</th><th>From</th><th>To</th><th>Price</th><th>Departure</th><th>Book</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['provider'] . "</td>";
                    echo "<td>" . $row['from_dest'] . "</td>";
                    echo "<td>" . $row['to_dest'] . "</td>";
                    echo "<td>$" . $row['price'] . "</td>";
                    echo "<td>" . $row['departure_time'] . "</td>";
                    echo "<td><form action='transport.php' method='post' style='display:inline;'><input type='hidden' name='transport_id' value='" . $row['id'] . "'><input type='submit' value='Book'></form></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No transport options found for this route.</p>";
            }
            $stmt->close();
        }

    // Handle booking submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transport_id'])) {
        $transport_id = $_POST['transport_id'];
        $user_id = $_SESSION['user_id'];
        $user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
        $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'unknown@example.com';

        // Fetch transport details for booking
        $sql = "SELECT * FROM transport WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $transport_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $transport = $result->fetch_assoc();
            // Insert into bookings
            $stmt_insert = $conn->prepare("INSERT INTO bookings (user_id, name, email, destination_id, transport_id, travel_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ississ", $user_id, $user_name, $user_email, $transport['to_destination_id'], $transport_id, $transport['departure_time']);
            if ($stmt_insert->execute()) {
                echo "<p>Transport booked successfully!</p>";
            } else {
                echo "<p>Error: " . $stmt_insert->error . "</p>";
            }
            $stmt_insert->close();
        } else {
            echo "<p>Transport not found.</p>";
        }
        $stmt->close();
    }
        ?>
    </main>
</body>
</html>
<?php $conn->close(); ?>