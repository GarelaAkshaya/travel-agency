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

            <a href="transport.php">Transport</a>
            <a href="hotels.php">Hotels</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
        </nav>
    </header>
    <main>
        <h2>Your Destination Bookings</h2>
        <?php
        $user_id = $_SESSION['user_id'];

        // Query for destination bookings (removed email)
        $sql = "SELECT b.id, b.name, b.travel_date, d.name AS dest_name, d.price
                FROM bookings b
                JOIN destinations d ON b.destination_id = d.id
                WHERE b.user_id = ?
                ORDER BY b.travel_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
            echo "<tr><th>Booking ID</th><th>Name</th><th>Destination</th><th>Price</th><th>Travel Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['dest_name'] . "</td>";
                echo "<td>$" . $row['price'] . "</td>";
                echo "<td>" . $row['travel_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no destination bookings yet.</p>";
        }

        $stmt->close();
        ?>

        <h2>Your Transport Bookings</h2>
        <?php
        // Query for transport bookings (removed email)
        $sql_transport = "SELECT b.id, b.name, b.travel_date, t.type, t.provider, t.price, d1.name AS from_dest, d2.name AS to_dest
                          FROM bookings b
                          JOIN transport t ON b.transport_id = t.id
                          JOIN destinations d1 ON t.from_destination_id = d1.id
                          JOIN destinations d2 ON t.to_destination_id = d2.id
                          WHERE b.user_id = ? AND b.transport_id IS NOT NULL
                          ORDER BY b.travel_date DESC";

        $stmt_transport = $conn->prepare($sql_transport);
        $stmt_transport->bind_param("i", $user_id);
        $stmt_transport->execute();
        $result_transport = $stmt_transport->get_result();

        if ($result_transport->num_rows > 0) {
            echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
            echo "<tr><th>Booking ID</th><th>Name</th><th>Type</th><th>Provider</th><th>From</th><th>To</th><th>Price</th><th>Departure</th></tr>";
            while ($row = $result_transport->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['type'] . "</td>";
                echo "<td>" . $row['provider'] . "</td>";
                echo "<td>" . $row['from_dest'] . "</td>";
                echo "<td>" . $row['to_dest'] . "</td>";
                echo "<td>$" . $row['price'] . "</td>";
                echo "<td>" . $row['travel_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no transport bookings yet.</p>";
        }

        $stmt_transport->close();
        ?>

        <h2>Your Hotel Bookings</h2>
        <?php
        // Query for hotel bookings (removed email)
        $sql_hotel = "SELECT b.id, b.name, b.travel_date, h.name AS hotel_name, h.description, h.price_per_night, d.name AS dest_name
                      FROM bookings b
                      JOIN hotels h ON b.hotel_id = h.id
                      JOIN destinations d ON h.destination_id = d.id
                      WHERE b.user_id = ? AND b.hotel_id IS NOT NULL
                      ORDER BY b.travel_date DESC";

        $stmt_hotel = $conn->prepare($sql_hotel);
        $stmt_hotel->bind_param("i", $user_id);
        $stmt_hotel->execute();
        $result_hotel = $stmt_hotel->get_result();

        if ($result_hotel->num_rows > 0) {
            echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
            echo "<tr><th>Booking ID</th><th>Name</th><th>Hotel</th><th>Description</th><th>Destination</th><th>Price/Night</th><th>Booking Date</th></tr>";
            while ($row = $result_hotel->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['hotel_name'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['dest_name'] . "</td>";
                echo "<td>$" . $row['price_per_night'] . "</td>";
                echo "<td>" . $row['travel_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no hotel bookings yet.</p>";
        }

        $stmt_hotel->close();
        ?>

        <h2>Your Reviews</h2>
        <?php
        // Query for user's reviews (email not relevant here, so unchanged)
        $sql_reviews = "SELECT r.id, r.booking_id, r.rating, r.comment, r.review_date, d.name AS dest_name, h.name AS hotel_name, t.provider AS transport_provider
                        FROM reviews r
                        LEFT JOIN bookings b ON r.booking_id = b.id
                        LEFT JOIN destinations d ON b.destination_id = d.id
                        LEFT JOIN hotels h ON b.hotel_id = h.id
                        LEFT JOIN transport t ON b.transport_id = t.id
                        WHERE r.user_id = ?
                        ORDER BY r.review_date DESC";

        $stmt_reviews = $conn->prepare($sql_reviews);
        $stmt_reviews->bind_param("i", $user_id);
        $stmt_reviews->execute();
        $result_reviews = $stmt_reviews->get_result();

        if ($result_reviews->num_rows > 0) {
            echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
            echo "<tr><th>Review ID</th><th>Booking ID</th><th>Rating</th><th>Comment</th><th>Related To</th><th>Review Date</th></tr>";
            while ($row = $result_reviews->fetch_assoc()) {
                $related_to = '';
                if ($row['dest_name']) $related_to .= 'Destination: ' . $row['dest_name'];
                if ($row['hotel_name']) $related_to .= ' | Hotel: ' . $row['hotel_name'];
                if ($row['transport_provider']) $related_to .= ' | Transport: ' . $row['transport_provider'];
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['booking_id'] . "</td>";
                echo "<td>" . $row['rating'] . "/5</td>";
                echo "<td>" . $row['comment'] . "</td>";
                echo "<td>" . $related_to . "</td>";
                echo "<td>" . $row['review_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no reviews yet. <a href='#review-form'>Leave one below</a>.</p>";
        }

        $stmt_reviews->close();
        ?>

        <h3 id="review-form">Leave a Review for a Booking</h3>
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
                $stmt->close();
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