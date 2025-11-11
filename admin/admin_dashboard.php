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
<title>Admin Dashboard</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}
.container {
    width: 90%;
    margin: 30px auto;
}
h2 {
    text-align: center;
    color: #2e7d32;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}
table th, table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}
table th {
    background: #2e7d32;
    color: white;
}
img {
    width: 100px;
    height: 70px;
    object-fit: cover;
    border-radius: 6px;
}
.add-btn {
    display: block;
    width: 180px;
    text-align: center;
    padding: 10px;
    background: #2e7d32;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    margin: 10px auto;
}
.add-btn:hover {
    background: #1b5e20;
}
.msg {
    width: 90%;
    margin: 10px auto;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    font-weight: bold;
}
.msg.success { background: #c8e6c9; color: #256029; }
.msg.error { background: #ffcdd2; color: #c62828; }
.action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-weight: bold;
}
.edit-btn { background: #0288d1; }
.delete-btn { background: #c62828; }
.action-btn:hover { opacity: 0.8; }
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
