<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='../login/login.php'>login</a> to book.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id   = $_SESSION['user_id'];
    $hotel_id  = (int)$_POST['hotel_id'];
    $rooms     = (int)$_POST['rooms'];
    $check_in  = sanitize($con, $_POST['check_in']);
    $check_out = sanitize($con, $_POST['check_out']);

    // Check available rooms
    $res = mysqli_query($con, "SELECT available_rooms, price_per_room 
                               FROM hotels WHERE hotel_id='$hotel_id'");
    $hotel = mysqli_fetch_assoc($res);

    if ($hotel && $hotel['available_rooms'] >= $rooms) {
        // Insert booking
        mysqli_query($con, "INSERT INTO bookings 
            (user_id, hotel_id, rooms_booked, check_in, check_out, status)
            VALUES ('$user_id', '$hotel_id', '$rooms', '$check_in', '$check_out', 'booked')");

        // Get last inserted booking_id
        $booking_id = mysqli_insert_id($con);

        // Decrease available rooms
        $new_avail = $hotel['available_rooms'] - $rooms;
        mysqli_query($con, "UPDATE hotels 
                            SET available_rooms='$new_avail' 
                            WHERE hotel_id='$hotel_id'");

        // Insert into payments table (unpaid by default)
        $amount = $rooms * $hotel['price_per_room'];
        mysqli_query($con, "INSERT INTO payments 
            (booking_id, amount, payment_method, payment_status,user_id) 
            VALUES ('$booking_id', '$amount', 'credit_card', 'pending',$user_id)");

        // Redirect to invoice (shows unpaid invoice until payment is done)
        header("Location: invoice.php?booking_id=$booking_id");
        exit();

    } else {
        echo "Sorry, only " . ($hotel ? $hotel['available_rooms'] : 0) . " rooms are available.";
    }
}
?>
