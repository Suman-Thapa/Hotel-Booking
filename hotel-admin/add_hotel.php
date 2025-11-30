<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if ($_SESSION['level'] != 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_name = sanitize($con, $_POST['hotel_name']);
    $location = sanitize($con, $_POST['location']);
    $available_rooms = sanitize($con, $_POST['available_rooms']);
    $price_per_room = sanitize($con, $_POST['price_per_room']);
    $hotel_image = "";

    // Handle image upload
    if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
        $uploadDir = "uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $hotel_image = basename($_FILES['hotel_image']['name']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['hotel_image']['tmp_name'], $targetFile);
    }

    $query = "INSERT INTO hotels (hotel_name, location, available_rooms, price_per_room, hotel_image)
              VALUES ('$hotel_name', '$location', '$available_rooms', '$price_per_room', '$hotel_image')";

    if (mysqli_query($con, $query)) {
        $_SESSION['hotel_msg'] = ['type' => 'success', 'text' => 'Hotel added successfully.'];
    } else {
        $_SESSION['hotel_msg'] = ['type' => 'error', 'text' => 'Error: ' . mysqli_error($con)];
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style/formstyle.css">
    <title>Add Hotel</title>
    
</head>
<body>

    <div class="add-hotel-container">
    <div class="addhotel">
    <h2>Add Hotel</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-container">
            <div>
                <label>Hotel Name:</label>
                <input type="text" name="hotel_name" required>
            </div>

            <div>
            <label>Location:</label>
            <input type="text" name="location" required>
            </div>

            <div>
                <label>Available Rooms:</label>
                <input type="number" name="available_rooms" required>
            </div>


            <div>
                <label>Price per Room:</label>
                <input type="number" name="price_per_room" required>
            </div>

            <div>
                <label>Hotel Image:</label>
                <input type="file" name="hotel_image" accept="image/*">
            </div>

            <button type="submit">Add Hotel</button>
        </div>
    </form>
    </div>
    </div>
</body>
</html>
