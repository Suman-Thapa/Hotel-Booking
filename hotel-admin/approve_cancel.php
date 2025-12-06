<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';

// Only hotel admin can access
if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];

    // Get booking details
    $res = mysqli_query($con, "SELECT hotel_id, room_id, rooms_booked, status FROM bookings WHERE booking_id='$booking_id'");
    
    if ($res && mysqli_num_rows($res) > 0) {
        $booking = mysqli_fetch_assoc($res);

        if ($booking['status'] == 'cancel_requested') {
            $room_id = $booking['room_id'];
            $rooms_booked = $booking['rooms_booked'];

            // Update booking status to canceled
            mysqli_query($con, "UPDATE bookings SET status='canceled' WHERE booking_id='$booking_id'");

            // Add rooms back to hotel_room availability
            mysqli_query($con, "UPDATE hotel_room SET available_rooms = available_rooms + $rooms_booked WHERE room_id='$room_id'");


            header('Location: admin_pending_cancellations.php');
            exit();

        } else {
            echo "This booking cannot be canceled.";
        }
    } else {
        echo "Booking not found!";
    }
} else {
    echo "Invalid request.";
}
?>
