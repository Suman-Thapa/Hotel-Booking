<?php
session_start();
include 'navbar.php';
include 'connection.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    die("Access denied. <a href='login.php'>Login as Admin</a>");
}

// Check if booking_id is provided
if (isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];

    // Get booking details
    $res = mysqli_query($con, "SELECT hotel_id, rooms_booked, status FROM bookings WHERE booking_id='$booking_id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $booking = mysqli_fetch_assoc($res);

        if ($booking['status'] == 'cancel_requested') {
            $hotel_id = $booking['hotel_id'];
            $rooms = $booking['rooms_booked'];

            // Update booking status to canceled
            mysqli_query($con, "UPDATE bookings SET status='canceled' WHERE booking_id='$booking_id'");

            // Add rooms back to hotel availability
            mysqli_query($con, "UPDATE hotels SET available_rooms = available_rooms + $rooms WHERE hotel_id='$hotel_id'");

            echo "Cancellation approved successfully! <a href='admin_pending_cancellations.php'>Go Back</a>";
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
