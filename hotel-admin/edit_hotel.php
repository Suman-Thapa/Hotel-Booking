<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if ($_SESSION['level'] != 1) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$result = mysqli_query($con, "SELECT * FROM hotels WHERE hotel_id='$id'");
$hotel = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_name = sanitize($con, $_POST['hotel_name']);
    $location = sanitize($con, $_POST['location']);
    $available_rooms = sanitize($con, $_POST['available_rooms']);
    $price_per_room = sanitize($con, $_POST['price_per_room']);
    $image_sql = "";

    if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
        $uploadDir = "uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $hotel_image = basename($_FILES['hotel_image']['name']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['hotel_image']['tmp_name'], $targetFile);

        $image_sql = ", hotel_image='$hotel_image'";
    }

    $update = "UPDATE hotels 
               SET hotel_name='$hotel_name', location='$location', available_rooms='$available_rooms', price_per_room='$price_per_room' $image_sql 
               WHERE hotel_id='$id'";

    mysqli_query($con, $update);
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="../style/formstyle.css">
    <title>Edit Hotel</title>
    
</head>
<body>
    
    <div class="edit-hotel-container">
    <div class="edithotel">
    <h2>Edit Hotel</h2>
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-container">
        <div>
            <label>Hotel Name:</label>
            <input type="text" name="hotel_name" value="<?= htmlspecialchars($hotel['hotel_name']); ?>" required>
        </div>

        <div>
            <label>Location:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($hotel['location']); ?>" required>
        </div>

        <div>
            <label>Available Rooms:</label>
            <input type="number" name="available_rooms" value="<?= htmlspecialchars($hotel['available_rooms']); ?>" required>
        </div>

        <div>
            <label>Price per Room:</label>
            <input type="number" name="price_per_room" value="<?= htmlspecialchars($hotel['price_per_room']); ?>" required>
        </div>

        <div>
            <label>Hotel Image:</label>
            <input type="file" name="hotel_image" accept="image/*">
        </div>

        <button type="submit">Update Hotel</button>
        </div>
      </div>
      </div>
    </form>


    </body>
    </html>