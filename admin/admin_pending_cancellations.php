<?php
session_start();
include '../includes/connection.php';
include '../includes/navbar.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    die("Access denied. <a href='login.php'>Login as Admin</a>");
}

// Fetch pending cancellation requests
$query = "SELECT b.booking_id, b.user_id, b.hotel_id, b.rooms_booked, b.check_in, b.check_out, b.status, 
                 h.hotel_name, h.available_rooms, u.name
          FROM bookings b
          JOIN hotels h ON b.hotel_id = h.hotel_id
          JOIN users u ON b.user_id = u.user_id
          WHERE b.status='cancel_requested'
          ORDER BY b.booking_id DESC";

$result = mysqli_query($con, $query);

echo "<h2>Pending Cancellation Requests</h2>";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='request-card'>";
        echo "<b>User:</b> " . $row['name'] . "<br>";
        echo "<b>Hotel:</b> " . $row['hotel_name'] . "<br>";
        echo "<b>Rooms Booked:</b> " . $row['rooms_booked'] . "<br>";
        echo "<b>Check-in:</b> " . $row['check_in'] . "<br>";
        echo "<b>Check-out:</b> " . $row['check_out'] . "<br>";
        echo "<b>Status:</b> " . $row['status'] . "<br>";

        // Approve button
        echo "<form method='POST' action='approve_cancel.php'>
                <input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>
                <button type='submit'>Approve Cancellation</button>
              </form>";
        echo "</div>";
    }
} else {
        echo "<p class='no-requests'>No pending cancellation requests.</p>";
}
?>

<!-- CSS for styling -->
<!-- CSS for styling -->
<style>
    h2 {
        text-align: center;
        color: #72889eff;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
    }

    .request-card {
        border: 1px solid #ccc;
        padding: 15px;
        margin: 10px auto;
        max-width: 600px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        background-color: #fdfdfd;
    }

    .request-card b {
        color: #34495e;
    }

    .request-card button {
        margin-top: 10px;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        background-color: #e74c3c;
        color: white;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    .request-card button:hover {
        background-color: #c0392b;
    }

    .no-requests {
        text-align: center;
        color: #555;
        font-family: Arial, sans-serif;
    }
</style>
