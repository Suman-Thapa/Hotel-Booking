<?php
session_start();
include '../includes/connection.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='../login/login.php'>login</a> to view the invoice.");
}

if (!isset($_GET['booking_id'])) {
    die("Booking ID not provided!");
}

$booking_id = (int) $_GET['booking_id'];
$user_id = (int) $_SESSION['user_id'];

// Fetch booking + user + hotel + payment
$query = "
    SELECT b.*, u.name, u.email, 
           h.hotel_name, h.location, h.price_per_room, h.hotel_image,
           p.payment_status, p.amount
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN payments p ON b.booking_id = p.booking_id
    WHERE b.booking_id = $booking_id AND b.user_id = $user_id
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

if ($row = mysqli_fetch_assoc($result)) {

    $check_in = new DateTime($row['check_in']);
    $check_out = new DateTime($row['check_out']);
    $nights = $check_in->diff($check_out)->days ?: 1;
    $total_price = $row['rooms_booked'] * $row['price_per_room'] * $nights;

    $imagePath = "../uploads/hotels/" . $row['hotel_image'];
    if (!file_exists($imagePath) || empty($row['hotel_image'])) {
        $imagePath = "https://via.placeholder.com/400x300?text=No+Image";
    }

    $epay_url = "../payment/payment_esewa.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="../style/userstyle.css">
</head>
<body>
    
</body>
</html>
<div class="invoice-container">

    <div class="invoice-left">

        <h2>Hotel Booking Invoice</h2>
        <hr>

        <p><b>Invoice No:</b> <?= $row['booking_id']; ?></p>
        <p><b>Date:</b> <?= date("Y-m-d"); ?></p>

        <h3>Customer Details</h3>
        <p>
            <b>Name:</b> <?= $row['name']; ?><br>
            <b>Email:</b> <?= $row['email']; ?>
        </p>

        <h3>Hotel Details</h3>
        <p>
            <b>Hotel:</b> <?= $row['hotel_name']; ?><br>
            <b>Location:</b> <?= $row['location']; ?>
        </p>

        <h3>Booking Details</h3>
        <p>
            <b>Check-in:</b> <?= $row['check_in']; ?><br>
            <b>Check-out:</b> <?= $row['check_out']; ?><br>
            <b>Nights:</b> <?= $nights; ?><br>
            <b>Rooms:</b> <?= $row['rooms_booked']; ?><br>
            <b>Price Per Room:</b> Rs. <?= $row['price_per_room']; ?><br>
            <b>Total Price:</b> Rs. <?= number_format($total_price, 2); ?><br>

            <b>Payment Status:</b>
            <span style="color:<?= $row['payment_status']=='paid'?'green':'red'; ?>">
                <?= ucfirst($row['payment_status']); ?>
            </span>
        </p>

        <?php if ($row['payment_status'] != 'paid') : ?>
        <div class="payment-buttons">

            <a href="../payment/payment_khalti.php?booking_id=<?= $booking_id ?>&amount=<?= $total_price ?>&name=<?= $row['name'] ?>&email=<?= $row['email'] ?>">
                <button class="btn-pay khalti">Pay With Khalti</button>
            </a>

            <form action="<?= $epay_url ?>" method="POST">
                <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                <input type="hidden" name="total_amount" value="<?= $total_price; ?>">
                <button class="btn-pay esewa" type="submit">Pay with eSewa</button>
            </form>

        </div>
        <?php endif; ?>

    </div>

    <div class="invoice-right">
        <img src="<?= $imagePath; ?>" alt="Hotel Image">
    </div>

</div>

<div class="footer-section">
    <hr>
    <p>Thank you for booking with us!</p>
    <button class="print-btn" onclick="window.print()">🖨️ Print Invoice</button>
</div>

<?php
} else {
    echo "<p style='text-align:center;'>Invoice not found.</p>";
}

include '../includes/footer.php';
?>
