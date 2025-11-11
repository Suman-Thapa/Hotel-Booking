<?php
include '../includes/navbar.php';
include '../includes/connection.php';

// Fetch all bookings with payment status
$sql = "SELECT 
            b.booking_id, b.check_in, b.check_out,
            u.user_id, u.name, h.hotel_name, h.hotel_image,
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
    <title>Admin - All Bookings</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0px 2px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background: #8cc78e; color: white; font-size: 15px; }
        tr:hover { background: #f1f1f1; }
        img { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ccc; transition: 0.2s; }
        img:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        a.image-link { display: inline-block; }
        .status-completed { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-failed { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Admin - All Bookings</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Booking ID</th>
            <th>User</th>
            <th>Hotel</th>
            <th>Hotel Image</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Total Amount</th>
            <th>Payment Status</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { 
            // ✅ Correct image path
            $imagePath = "../uploads/hotels/" . $row['hotel_image'];
            $imageExists = (!empty($row['hotel_image']) && file_exists($imagePath));

            // ✅ Fallback image if missing
            if (!$imageExists) {
                $imagePath = "https://via.placeholder.com/200x150?text=No+Image";
            }
        ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= $row['booking_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['hotel_name']) ?></td>
                <td>
                    <a href="<?= $imagePath ?>" target="_blank">
                        <img src="<?= $imagePath ?>" alt="Hotel Image">
                    </a>
                </td>
                <td><?= $row['check_in'] ?></td>
                <td><?= $row['check_out'] ?></td>
                <td>$<?= $row['amount'] ?></td>
                <td class="status-<?= strtolower($row['payment_status']) ?>">
                    <?= ucfirst($row['payment_status']) ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
