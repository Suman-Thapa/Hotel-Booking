<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

// Ensure user is logged in
check_login();

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    die("You must <a href='../login/login.php'>login</a> to view the invoice.");
}

// Get booking ID
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($booking_id < 1) {
    die("Invalid Booking ID.");
}

/*
    FIXED JOIN:
    Previously: you joined hotel_room ON hotel_id, which returned WRONG room.
    NOW: hotel_room is joined using room_id from booking.
*/

$query = "
    SELECT 
        b.*, 
        u.name, u.email,
        h.hotel_name, h.location, h.hotel_image,
        hr.room_number, hr.room_type, hr.price_per_room, 
        hr.about_rooms, hr.room_image,
        p.payment_status, p.amount
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN hotel_room hr ON b.room_id = hr.room_id   -- FIXED JOIN
    JOIN payments p ON b.booking_id = p.booking_id
    WHERE b.booking_id = $booking_id
      AND b.user_id = $user_id       -- Prevent viewing another user's invoice
    LIMIT 1
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    die("<p style='text-align:center;'>Invoice not found or you don't have permission to view it.</p>");
}

// Calculate nights
$check_in = new DateTime($row['check_in']);
$check_out = new DateTime($row['check_out']);
$nights = max(1, $check_in->diff($check_out)->days);

// Total price
$total_price = $row['rooms_booked'] * $row['price_per_room'] * $nights;

// Room image
$imagePath = "../uploads/rooms/" . $row['room_image'];
if (!file_exists($imagePath) || empty($row['room_image'])) {
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
<link rel="stylesheet" href="../style/userinvoice.css">
</head>
<body>

<div class="invoice-container">

    <div class="invoice-left">
        <h2>Hotel Booking Invoice</h2>
        <hr>

        <p><b>Invoice No:</b> <?= $row['booking_id']; ?></p>
        <p><b>Date:</b> <?= date("Y-m-d"); ?></p>

        <h3>Customer Details</h3>
        <p>
            <b>Name:</b> <?= htmlspecialchars($row['name']); ?><br>
            <b>Email:</b> <?= htmlspecialchars($row['email']); ?>
        </p>

        <h3>Hotel & Room Details</h3>
        <p>
            <b>Hotel:</b> <?= htmlspecialchars($row['hotel_name']); ?><br>
            <b>Location:</b> <?= htmlspecialchars($row['location']); ?><br>
            <b>Room No:</b> <?= htmlspecialchars($row['room_number']); ?><br>
            <b>Room Type:</b> <?= htmlspecialchars($row['room_type']); ?><br>
            <b>Room Info:</b> <?= htmlspecialchars($row['about_rooms']); ?><br>
            <b>Price per Room:</b> Rs. <?= number_format($row['price_per_room'], 2); ?>
        </p>

        <h3>Booking Details</h3>
        <p>
            <b>Check-in:</b> <?= $row['check_in']; ?><br>
            <b>Check-out:</b> <?= $row['check_out']; ?><br>
            <b>Nights:</b> <?= $nights; ?><br>
            <b>Rooms Booked:</b> <?= $row['rooms_booked']; ?><br>
            <b>Total Price:</b> Rs. <?= number_format($total_price, 2); ?><br>
            <b>Payment Status:</b>
            <span style="color:<?= $row['payment_status']=='paid'?'green':'red'; ?>;">
                <?= ucfirst($row['payment_status']); ?>
            </span>
        </p>

        <?php if ($row['payment_status'] != 'paid'): ?>
        <div class="payment-buttons">
            <a href="../payment/payment_khalti.php?booking_id=<?= $booking_id ?>&amount=<?= $total_price ?>&name=<?= urlencode($row['name']) ?>&email=<?= urlencode($row['email']) ?>">
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
        <img src="<?= $imagePath; ?>" alt="Room Image">
    </div>

</div>

<div class="footer-section">
    <hr>
    <p>Thank you for booking with us!</p>
    <button class="print-btn" onclick="window.print()">🖨️ Print Invoice</button>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
