<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

check_login();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Allow ONLY POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("❌ Invalid Access (POST only).");
}

// Get POST values
$room_id  = (int)$_POST['room_id'];
$check_in  = sanitize($con, $_POST['check_in']);
$check_out = sanitize($con, $_POST['check_out']);
$rooms     = (int)$_POST['rooms'];


if (!$room_id || !$check_in || !$check_out || !$rooms) {
    die("❌ Missing required data.");
}


$query = "
    SELECT hr.*, h.hotel_name, h.location, h.hotel_image
    FROM hotel_room hr
    JOIN hotels h ON hr.hotel_id = h.hotel_id
    WHERE hr.room_id = $room_id
      AND hr.available_rooms >= $rooms
    LIMIT 1
";

$res = mysqli_query($con, $query);

if (!$res) {
    die("❌ SQL ERROR: " . mysqli_error($con));
}

if (mysqli_num_rows($res) == 0) {
    die("❌ Room not found OR not enough available rooms.");
}

$room = mysqli_fetch_assoc($res);

// Extract hotel_id from DB
$hotel_id = $room['hotel_id'];

// Image fix
$image = "../uploads/rooms/" . $room['room_image'];
if (!file_exists($image) || empty($room['room_image'])) {
    $image = "https://via.placeholder.com/260x180?text=No+Image";
}

// Days difference
$days = (strtotime($check_out) - strtotime($check_in)) / 86400;
if ($days < 1) $days = 1;

$total_price = $room['price_per_room'] * $rooms * $days;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Check Availability</title>
<link rel="stylesheet" href="../style/userindexstyle.css">
</head>
<body>

<div class="booking-container">

    <!-- LEFT SIDE -->
    <div class="left-side">
        <img src="<?= $image ?>" class="hotel-img">

        <div class="hotel-details">
            <h2><?= htmlspecialchars($room['hotel_name']) ?></h2>
            <p class="loc"><?= htmlspecialchars($room['location']) ?></p>

            <div class="info">
                <b>Rooms:</b> <?= $rooms ?><br>
                <b>Nights:</b> <?= $days ?><br>
                <b>Price / Room:</b> NPR <?= number_format($room['price_per_room']) ?>
                <h2>About</h2>
                <p><?= htmlspecialchars($room['about_rooms']) ?></p>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right-side">
        <div class="summary-box">
            <h3>Total Price</h3>
            <p class="price">NPR <?= number_format($total_price) ?></p>

            <div class="date-box">
                <p><b>Check-in:</b> <?= $check_in ?></p>
                <p><b>Check-out:</b> <?= $check_out ?></p>
            </div>

            <form method="POST" action="book_process.php">
                <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">
                <input type="hidden" name="room_id" value="<?= $room_id ?>">
                <input type="hidden" name="check_in" value="<?= $check_in ?>">
                <input type="hidden" name="check_out" value="<?= $check_out ?>">
                <input type="hidden" name="rooms" value="<?= $rooms ?>">

                <button class="book-btn"
                    onclick="return confirm('Please confirm your booking before proceeding.')">
                    Proceed To Book
                </button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
