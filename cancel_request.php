<?php
session_start();
include 'navbar.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> first.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    $user_id    = $_SESSION['user_id'];

    // Check if booking belongs to this user
    $query = "SELECT * FROM bookings WHERE booking_id='$booking_id' AND user_id='$user_id'";
    $res = mysqli_query($con, $query);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);

        if ($row['status'] == "booked") {
            $q2 = "UPDATE bookings SET status='cancel_requested' WHERE booking_id='$booking_id'";
            if (mysqli_query($con, $q2)) {
                echo "Cancellation request sent. <a href='my_bookings.php'>Go back</a>";
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "Booking cannot be canceled (already canceled or pending).";
        }
    } else {
        echo "Booking not found!";
    }
}
?>
