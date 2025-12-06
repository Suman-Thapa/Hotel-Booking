<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Fetch hotel and its room info
$result = mysqli_query($con, "SELECT h.hotel_id, h.hotel_name, h.location, h.about, h.hotel_image, hr.room_id, hr.total_rooms, hr.available_rooms 
                              FROM hotels h 
                              JOIN hotel_room hr ON h.hotel_id = hr.hotel_id
                              WHERE h.hotel_id = $id
                              LIMIT 1");
$hotel = mysqli_fetch_assoc($result);

if (!$hotel) {
    die("Hotel not found!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hotel_name = sanitize($con, $_POST['hotel_name']);
    $location   = sanitize($con, $_POST['location']);
    $about      = sanitize($con, $_POST['description']);
    $totalroom  = (int)$_POST['total_rooms'];
    $availableroom = (int)$_POST['available_rooms'];

    $image_sql = "";
    if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
        $uploadDir = "../uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $image_name = time() . "_" . basename($_FILES['hotel_image']['name']);
        $target_file = $uploadDir . $image_name;
        move_uploaded_file($_FILES['hotel_image']['tmp_name'], $target_file);

        $image_sql = ", hotel_image='$image_name'";
    }

    // Update hotels table
    $updatehotel = "UPDATE hotels SET 
                        hotel_name='$hotel_name',
                        about='$about',
                        location='$location' $image_sql
                    WHERE hotel_id=$id";
    mysqli_query($con, $updatehotel) or die("Hotel Update Failed: " . mysqli_error($con));

    // Update hotel_room table
    $roomid = $hotel['room_id'];
    $updateroom = "UPDATE hotel_room SET total_rooms=$totalroom, available_rooms=$availableroom WHERE room_id=$roomid";
    mysqli_query($con, $updateroom) or die("Room Update Failed: " . mysqli_error($con));

    header("Location: view_hotel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotel</title>
    <link rel="stylesheet" href="../style/formstyle.css">
    
</head>
<body>

<div class="edit-hotel-container">
    <h2>Edit Hotel</h2>
    <form method="POST" enctype="multipart/form-data">

        <label>Hotel Name:</label>
        <input type="text" name="hotel_name" value="<?= htmlspecialchars($hotel['hotel_name']); ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($hotel['location']); ?>" required>

        <label>Total Rooms:</label>
        <input type="number" name="total_rooms" value="<?= htmlspecialchars($hotel['total_rooms']); ?>" min="1" required>

        <label>Available Rooms:</label>
        <input type="number" name="available_rooms" value="<?= htmlspecialchars($hotel['available_rooms']); ?>" min="0" required>

        <label>Hotel Image:</label>
        <?php if (!empty($hotel['hotel_image']) && file_exists("../uploads/hotels/".$hotel['hotel_image'])): ?>
            <img src="../uploads/hotels/<?= $hotel['hotel_image'] ?>" alt="Hotel Image">
        <?php else: ?>
            <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No Image">
        <?php endif; ?>
        <input type="file" name="hotel_image" accept="image/*">

        <label>About Hotel:</label>
        <textarea name="description" rows="5"><?= htmlspecialchars($hotel['about']); ?></textarea>

        <button type="submit">Update Hotel</button>

    </form>
</div>

</body>
</html>
