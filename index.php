<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/navbar.php';
include 'includes/connection.php';
include 'includes/functions.php';

$search = '';
if (isset($_GET['search'])) {
    $search = sanitize($con, $_GET['search']);
}

// Fetch hotels with available rooms
// Base query
$query = "
    SELECT 
        hotel_name,
        h.location,
        hr.*
    FROM hotels h
    JOIN hotel_room hr ON h.hotel_id = hr.hotel_id
    WHERE hr.status = 'Available'
";

// Apply search
if (!empty($search)) {
    $s = mysqli_real_escape_string($con, $search);
    $query .= " AND (h.hotel_name LIKE '%$s%' OR h.location LIKE '%$s%') ";
}




$result = mysqli_query($con, $query);

if (!$result) {
    die("SQL ERROR: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home Page</title>
<link rel="stylesheet" href="style/userindexstyle.css">
</head>
<body>
    
<div class="wrapper">
    <div class="content">

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-overlay">
                <form method="GET" action="" class="search-box">
                    <input type="text" name="search" placeholder="Search hotel or location..."
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>

        <!-- Hotels Listing -->
        <div class="hotels-container">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // rooms image
                    $image = "uploads/rooms/" . $row['room_image'];
                    
                    if (!file_exists($image) || empty($row['room_image'])) {
                        $image = "https://via.placeholder.com/260x180?text=No+Image";
                    }
            ?>
                    <div class="hotel-card">
                        <div class="hotel-image">
                            <img src="<?= $image ?>" alt="<?= htmlspecialchars($row['hotel_name']) ?>">
                        </div>
                        <div class="hotel-info">
                            <h2 class="hotel-title"><?= htmlspecialchars($row['hotel_name']) ?></h2>
                            <p class="hotel-location"><?= htmlspecialchars($row['location']) ?></p>
                            <p class="price-label">Price per night</p>
                            <p class="hotel-price">NPR <?= number_format($row['price_per_room'], 2) ?></p>

                            <form method="POST" action="user/check_avilabilty.php" class="book-form">
                                <input type="hidden" name="room_id" value="<?= $row['room_id'] ?>">
                                <div class="form-row">
                                    <div>
                                        <label>Check-in</label>
                                        <input type="date" name="check_in" required>
                                    </div>
                                    <div>
                                        <label>Check-out</label>
                                        <input type="date" name="check_out" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div style="width:100%;">
                                        <label>Rooms</label>
                                        <select name="rooms">
                                            <?php 
                                            for ($i = 1; $i <= min(5, $row['available_rooms']); $i++) {
                                                echo "<option value='$i'>$i Room(s)</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <button class="check-btn" type="submit">Check Availability</button>
                            </form>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='no-hotels'>No hotels found.</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
