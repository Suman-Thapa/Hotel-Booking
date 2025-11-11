<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='..login/login.php'>login</a> to view bookings.");
}

$user_id = $_SESSION['user_id'];

$query = "SELECT b.booking_id, b.hotel_id, b.rooms_booked, b.check_in, b.check_out, b.status, h.hotel_name
          FROM bookings b
          JOIN hotels h ON b.hotel_id = h.hotel_id
          WHERE b.user_id='$user_id'
          ORDER BY b.booking_id DESC";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($con));
}
?>

<!-- CSS for styling -->
<style>
    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        text-transform: uppercase;
    }

    .booking-card {
        border: 1px solid #ccc;
        padding: 15px;
        margin: 10px auto;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
    }

    .booking-card b {
        font-size: 18px;
        color: #34495e;
    }

    .booking-card i {
        color: #e74c3c;
    }

    .booking-card button {
        margin-top: 10px;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
        transition: 0.3s;
        font-weight: bold;
    }

    /* Request Cancellation button - green */
    .booking-card .btn-cancel {
        background-color: #27ae60;
    }

    .booking-card .btn-cancel:hover {
        background-color: #2ecc71;
    }

    /* View Invoice button - blue */
    .booking-card .btn-invoice {
        background-color: #2980b9;
    }

    .booking-card .btn-invoice:hover {
        background-color: #3498db;
    }

    /* Make buttons side by side */
    .booking-card form {
        display: inline-block;
        margin-right: 10px;
    }

    .no-bookings {
        text-align: center;
        color: #555;
        font-family: Arial, sans-serif;
    }

    /* Status badges */
    .status-booked {
        color: #27ae60;
        font-weight: bold;
    }

    .status-cancel_requested {
        color: #f39c12;
        font-weight: bold;
    }

    .status-canceled {
        color: #e74c3c;
        font-weight: bold;
    }
</style>

<h2>My Bookings</h2>

<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Format dates
        $check_in = date("d M Y", strtotime($row['check_in']));
        $check_out = date("d M Y", strtotime($row['check_out']));

        echo "<div class='booking-card'>";
        echo "<b>" . htmlspecialchars($row['hotel_name']) . "</b><br>";
        echo "Rooms: " . $row['rooms_booked'] . "<br>";
        echo "Check-in: " . $check_in . "<br>";
        echo "Check-out: " . $check_out . "<br>";
        echo "Status: <b class='status-" . $row['status'] . "'>" . $row['status'] . "</b><br>";

        if ($row['status'] == "booked") {
            echo "<form method='POST' action='cancel_request.php'>
                    <input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>
                    <button type='submit' class='btn-cancel'>Request Cancellation</button>
                  </form>";

            echo "<form method='GET' action='invoice.php'>
                    <input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>
                    <button type='submit' class='btn-invoice'>View Invoice</button>
                  </form>";
        } elseif ($row['status'] == "cancel_requested") {
            echo "<i>Cancellation pending approval...</i>";
        } elseif ($row['status'] == "canceled") {
            echo "<i>Booking canceled</i>";
        }

        echo "</div>";
    }
} else {
    echo "<p class='no-bookings'>You have no bookings yet. Start by <a href='hotels.php'>booking a hotel</a>!</p>";
}
?>
