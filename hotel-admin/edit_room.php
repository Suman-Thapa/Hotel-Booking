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
$result = mysqli_query($con, "SELECT * FROM hotel_room WHERE room_id='$id'");
$room = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = sanitize($con, $_POST['room_number']);
    $room_type = sanitize($con, $_POST['room_type']);
    $total_rooms = sanitize($con, $_POST['total_rooms']);
    $available_rooms = sanitize($con, $_POST['available_rooms']);
    $price_per_room = sanitize($con, $_POST['price_per_room']);
    $about = sanitize($con,$_POST['description']);
    $image_sql = "";

    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
        $uploadDir = "uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $hotel_image = basename($_FILES['room_image']['name']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['room_image']['tmp_name'], $targetFile);

        $image_sql = ", room_image='$hotel_image'";
    }

    $update = "UPDATE hotel_room 
               SET room_number='$room_number',about_rooms='$about', room_type='$room_type', available_rooms=$available_rooms,total_rooms = $total_rooms, price_per_room=$price_per_room $image_sql 
               WHERE room_id='$id'";

    mysqli_query($con, $update);
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="../style/formstyle.css">
    <title>Edit Room</title>
    
</head>
<body>
    
    <div class="edit-hotel-container">
    <div class="edithotel">
    <h2>Edit Hotel</h2>
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-container">
        <div>
            <label>Room Number:</label>
            <input type="text" name="room_number" value="<?= htmlspecialchars($room['room_number']); ?>" required>
        </div>

        <div>
            <label>Room Type:</label>
            <select name="room_type" >
                <option value="Single">Single Bed</option>
                <option value="Double">Double Bed</option>
            </select>
        </div>

        <div>
            <label>Total Room:</label>
            <input type="number" name="total_rooms" value="<?= htmlspecialchars($room['total_rooms']); ?>" required>

        </div>

        <div>
            <label>Available Rooms:</label>
            <input type="number" name="available_rooms" value="<?= htmlspecialchars($room['available_rooms']); ?>" required>
        </div>

        <div>
            <label>Price per Room:</label>
            <input type="number" name="price_per_room" value="<?= htmlspecialchars($room['price_per_room']); ?>" required>
        </div>

        <div>
            <label>Room Image:</label>
            <input type="file" name="room_image" accept="image/*">
        </div>

         <div>
                <label for="discription">About</label>
                <textarea name="description" id="discription" placeholder="About Room"><?= $room['about_rooms'] ?></textarea>
        </div>

        <button type="submit">Update Room</button>
        </div>
      </div>
      </div>
    </form>


    </body>
    </html>