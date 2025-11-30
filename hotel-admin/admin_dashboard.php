<?php
session_start();
include '../includes/navbar.php';
include '../includes/connection.php';
include '../includes/functions.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch all hotels
$query = "SELECT * FROM hotels ORDER BY hotel_id DESC";
$result = mysqli_query($con, $query);

// Success message from add/edit pages
$msg = "";
if (isset($_SESSION['hotel_msg'])) {
    $msg = $_SESSION['hotel_msg']['text'];
    $msgType = $_SESSION['hotel_msg']['type'];
    unset($_SESSION['hotel_msg']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../style/adminstyle.css">
<title>Admin Dashboard</title>
<style>

</style>
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>

    <?php if ($msg != ""): ?>
        <div class="msg <?php echo $msgType; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <a href="add_hotel.php" class="add-btn">+ Add New Hotel</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hotel Name</th>
                <th>Location</th>
                <th>Available Rooms</th>
                <th>Price (₹)</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['hotel_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['hotel_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo $row['available_rooms']; ?></td>
                    <td>₹<?php echo $row['price_per_room']; ?></td>
                    <td>
                    <?php
                    if (!empty($row['hotel_image']) && file_exists("../uploads/hotels/" . $row['hotel_image'])) {
                        echo "<img src='../uploads/hotels/" . $row['hotel_image'] . "' style='width:80px;height:60px;object-fit:cover;border-radius:4px;'>";
                    } else {
                        echo "<img src='https://via.placeholder.com/80x60?text=No+Image'>";
                    }
                    ?>
                    </td>

                    <td>
                        <a href="edit_hotel.php?id=<?php echo $row['hotel_id']; ?>" class="action-btn edit-btn">Edit</a>
                        <a href="delete_hotel.php?id=<?php echo $row['hotel_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No hotels found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
