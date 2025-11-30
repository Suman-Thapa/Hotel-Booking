<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Invalid Access";
    exit;
}

$hotel_id  = (int)$_POST['hotel_id'];
$check_in  = sanitize($con, $_POST['check_in']);
$check_out = sanitize($con, $_POST['check_out']);
$rooms     = (int)$_POST['rooms'];

$res = mysqli_query($con, "SELECT * FROM hotels WHERE hotel_id='$hotel_id'");
$hotel = mysqli_fetch_assoc($res);

if (!$hotel) {
    echo "Hotel not found!";
    exit;
}

$image = "../uploads/hotels/" . $hotel['hotel_image'];
if (!file_exists($image) || empty($hotel['hotel_image'])) {
    $image = "https://via.placeholder.com/260x180?text=No+Image";
}

$days = (strtotime($check_out) - strtotime($check_in)) / 86400;
if ($days < 1) $days = 1;

$total_price = $hotel['price_per_room'] * $rooms * $days;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Avilability</title>
    <link rel="stylesheet" href="../style/userstyle.css">
</head>
<body>
    
</body>
</html>

<div class="booking-container">

    <!-- LEFT SIDE -->
    <div class="left-side">
        <img src="<?= $image ?>" class="hotel-img">

        <div class="hotel-details">
            <h2><?= $hotel['hotel_name'] ?></h2>
            <p class="loc"><?= $hotel['location'] ?></p>

            <p class="info">
                <b>Rooms:</b> <?= $rooms ?><br>
                <b>Nights:</b> <?= $days ?><br>
                <b>Price / Room:</b> NPR <?= number_format($hotel['price_per_room']) ?>
            </p>
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
                <input type="hidden" name="check_in" value="<?= $check_in ?>">
                <input type="hidden" name="check_out" value="<?= $check_out ?>">
                <input type="hidden" name="rooms" value="<?= $rooms ?>">

                <button class="book-btn">Proceed To Book</button>
            </form>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

