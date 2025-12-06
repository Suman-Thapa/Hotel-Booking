<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

check_login();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("❌ Invalid Access (POST only).");
}

// Sanitize POST data
$user_id   = $_SESSION['user_id'];
$hotel_id  = (int)($_POST['hotel_id'] ?? 0);
$room_id   = (int)($_POST['room_id'] ?? 0);
$rooms     = (int)($_POST['rooms'] ?? 0);
$check_in  = sanitize($con, $_POST['check_in'] ?? '');
$check_out = sanitize($con, $_POST['check_out'] ?? '');

// Validate required data
if (!$hotel_id || !$room_id || !$rooms || !$check_in || !$check_out) {
    die("❌ Missing required data.");
}



// Fetch room info
$res = mysqli_query($con, "SELECT * FROM hotel_room WHERE room_id=$room_id AND hotel_id=$hotel_id");

if (!$res) {
    die("❌ SQL ERROR: " . mysqli_error($con));
}

$room = mysqli_fetch_assoc($res);

if (!$room) {
    die(" Room not found for this hotel.");
}

// Check availability
if ($room['available_rooms'] < $rooms) {
    die(" Only {$room['available_rooms']} room(s) are available.");
}

// Calculate days and total price
$days = (strtotime($check_out) - strtotime($check_in)) / 86400;
if ($days < 1) $days = 1;
$total_price = $rooms * $room['price_per_room'] * $days;


    $sql_booking = "INSERT INTO bookings
        (user_id, hotel_id, room_id, rooms_booked, check_in, check_out, status, booked_at)
        VALUES ($user_id, $hotel_id, $room_id, $rooms, '$check_in', '$check_out', 'booked', NOW())";

    mysqli_query($con, $sql_booking);
    $booking_id = mysqli_insert_id($con);

    // Update room availability
    $new_avail = $room['available_rooms'] - $rooms;
    $sql_update = "UPDATE hotel_room SET available_rooms=$new_avail WHERE room_id=$room_id";
    mysqli_query($con, $sql_update);

    // Insert payment (pending)
    $sql_payment = "INSERT INTO payments
        (booking_id, amount, payment_method, payment_status, user_id)
        VALUES ($booking_id, '$total_price', 'credit_card', 'pending', $user_id)";
    mysqli_query($con, $sql_payment);

    // Commit transaction
    mysqli_commit($con);

    // Redirect to invoice
    header("Location: invoice.php?booking_id=$booking_id");
    exit();

?>
