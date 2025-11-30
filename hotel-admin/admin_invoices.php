<?php
include '../includes/navbar.php';
include '../includes/connection.php';

// Fetch all bookings with payment status
$sql = "SELECT 
            b.booking_id, b.check_in, b.check_out,
            u.user_id, u.name, h.hotel_name, h.hotel_image,u.email,
            p.amount, p.payment_status
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN payments p ON b.booking_id = p.booking_id
        ORDER BY b.booking_id DESC";

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style/adminstyle.css">
    <title>Admin - All Bookings</title>
    
</head>

<body>
    <div class="All-Contain">
    <h2>Admin - All Bookings</h2>

    <div class="table-container">
        <table>
            <tr>
                <th>User ID</th>
                <th>Booking ID</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Hotel</th>
                <th>Hotel Image</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
            </tr>

            <?php 
            while ($row = mysqli_fetch_assoc($result)) { 
                $imagePath = "../uploads/hotels/" . $row['hotel_image'];
                $imageExists = (!empty($row['hotel_image']) && file_exists($imagePath));

                if (!$imageExists) {
                    $imagePath = "https://via.placeholder.com/200x150?text=No+Image";
                }
            ?>
                <tr>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= $row['booking_id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= htmlspecialchars($row['hotel_name']) ?></td>
                    <td>
                        <a href="<?= $imagePath ?>" target="_blank">
                            <img src="<?= $imagePath ?>" alt="Hotel Image">
                        </a>
                    </td>
                    <td><?= $row['check_in'] ?></td>
                    <td><?= $row['check_out'] ?></td>
                    <td>Npr. <?= $row['amount'] ?></td>
                    <td class="status-<?= strtolower($row['payment_status']) ?>">
                        <?= ucfirst($row['payment_status']) ?>
                    </td>
                </tr>
            <?php } ?>

        </table>
    </div>
    </div>

</body>
</html>
