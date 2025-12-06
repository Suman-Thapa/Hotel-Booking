<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$hotel_admin_id = $_SESSION['user_id'];

// Fetch rooms with hotel info
$query = "SELECT hr.*, h.hotel_name, h.location 
          FROM hotel_room hr
          JOIN hotels h ON hr.hotel_id = h.hotel_id
          WHERE h.hotel_admin_id = $hotel_admin_id
          ORDER BY h.hotel_name, hr.room_number";

$result = mysqli_query($con, $query);

// Show session messages
$msg = "";
if (isset($_SESSION['hotel_msg'])) {
    $msg = $_SESSION['hotel_msg']['text'];
    $msgType = $_SESSION['hotel_msg']['type'];
    unset($_SESSION['hotel_msg']);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../style/adminstyle.css">
<title>Hotel Admin Dashboard</title>
</head>
<body>

<div class="container">
    <h2>Hotel Admin Dashboard</h2>

    <?php if ($msg != ""): ?>
        <div class="msg <?= $msgType ?>"><?= $msg ?></div>
    <?php endif; ?>

    <a href="add_room.php" class="add-btn">+ Add New Room</a>

    <table>
        <thead>
            <tr>
                <th>Hotel</th>
                <th>Room No.</th>
                <th>Type</th>
                <th>Price (₹)</th>
                <th>Total Rooms</th>
                <th>Available</th>
                <th>Room Image</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['hotel_name']) ?></td>
                    <td><?= htmlspecialchars($row['room_number']) ?></td>
                    <td><?= htmlspecialchars($row['room_type']) ?></td>
                    <td>₹<?= $row['price_per_room'] ?></td>
                    <td><?= $row['total_rooms'] ?></td>
                    <td><?= $row['available_rooms'] ?></td>

                    <td>
                        <?php
                        $imgPath = "../uploads/rooms/" . $row['room_image'];
                        if (!empty($row['room_image']) && file_exists($imgPath)) {
                            echo "<img src='$imgPath' style='width:80px;height:60px;object-fit:cover;border-radius:4px;'>";
                        } else {
                            echo "<img src='https://via.placeholder.com/80x60?text=No+Image'>";
                        }
                        ?>
                    </td>

                    <td>
                        <a href="edit_room.php?id=<?= $row['room_id'] ?>" class="action-btn edit-btn">Edit</a>
                        <a href="delete_room.php?id=<?= $row['room_id'] ?>" class="action-btn delete-btn"
                           onclick="return confirm('Are you sure you want to delete this room?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No rooms found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
