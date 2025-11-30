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

echo "<div class='All-Contain'> ";
echo "<h2>Pending Cancellation Requests</h2>";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='request-card'>";
        echo "<b>User:</b> " . $row['name'] . "<br>";
        echo "<b>Hotel:</b> " . $row['hotel_name'] . "<br>";
        echo "<b>Rooms Booked:</b> " . $row['rooms_booked'] . "<br>";
        echo "<b>Check-in:</b> " . $row['check_in'] . "<br>";
        echo "<b>Check-out:</b> " . $row['check_out'] . "<br>";
        echo "<b>Status:</b> " . ucfirst($row['status']) . "<br>";

        echo "<form method='POST' action='approve_cancel.php'>
                <input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>
                <button type='submit'>Approve Cancellation</button>
              </form>";
        echo "</div>";
    }
} else {
    echo "<p class='no-requests'>No pending cancellation requests.</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Cancelation</title>
    <link rel="stylesheet" href="../style/adminstyle.css">
</head>
<body>
    
</body>
</html>