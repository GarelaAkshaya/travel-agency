<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Agency</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome to Our Travel Agency</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="book.php">Book a Trip</a>
            <a href="my_bookings.php">My Bookings</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>

            <?php endif; ?>
        </nav>
    </header>
    <main>
        <h2>Available Destinations</h2>
        <ul>
            <?php
            $sql = "SELECT * FROM destinations";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li><strong>" . $row["name"] . "</strong> - " . $row["description"] . " - $" . $row["price"] . "</li>";
                }
            } else {
                echo "<li>No destinations available.</li>";
            }
            ?>
        </ul>
    </main>
</body>
</html>
<?php $conn->close(); ?>